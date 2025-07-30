<?php
if (!defined('ABSPATH')) exit;

// 🔒 Hide "Add New" from UI
add_action('admin_bar_menu', function($bar) {
    if (sanitize_key($_GET['post_type'] ?? '') === 'reservation_request') {
        $bar->remove_node('new-reservation_request');
    }
}, 999);
// Remove "Add New" from admin menu
add_action('admin_menu', function() {
    $pt = 'edit.php?post_type=reservation_request';
    remove_submenu_page($pt, 'post-new.php?post_type=reservation_request');
    if (!current_user_can('edit_pages')) remove_menu_page($pt);
}, 99);
// Disable manual post creation
add_action('load-post-new.php', function() {
    if (sanitize_key($_GET['post_type'] ?? '') === 'reservation_request') {
        wp_die('You are not allowed to add a reservation manually.');
    }
});
//  📋 Hide "Quick Edit" and "Trash"
add_action('admin_head', function() {
    global $typenow;
    if ($typenow !== 'reservation_request') return;
    echo '<style>.page-title-action,.post-type-reservation_request .subsubsub .create{display:none!important}.row-actions{visibility:hidden}tr:hover .row-actions{visibility:visible}</style>';
});

// 📋 Custom Columns
add_filter('manage_edit-reservation_request_columns', fn($cols) => array_merge($cols, [
    'reservation_datetime' => 'Date & Time',
    'reservation_name'     => 'Name',
    'reservation_email'    => 'Email',
    'reservation_status'   => 'Status',
    'reservation_action'   => 'Action'
]));
// Populate custom columns
add_action('manage_reservation_request_posts_custom_column', function($col, $id) {
    $meta = fn($key) => esc_html(get_post_meta($id, $key, true));
    if ($col === 'reservation_datetime') {
        $dt = strtotime("{$meta('date')} {$meta('time')}");
        echo $dt ? date('M j, Y • g A', $dt) . ' - ' . date('g A', strtotime('+1 hour', $dt)) : '—';
    } elseif (in_array($col, ['reservation_name', 'reservation_email', 'reservation_status'])) {
        echo $meta(str_replace('reservation_', '', $col));
    } elseif ($col === 'reservation_action') {
        $status = $meta('status');
        $room = $meta('room'); $date = $meta('date'); $time = $meta('time');
        global $wpdb;

        $conflict = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $wpdb->posts p
            JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key='room' AND m1.meta_value=%s
            JOIN $wpdb->postmeta m2 ON p.ID = m2.post_id AND m2.meta_key='date' AND m2.meta_value=%s
            JOIN $wpdb->postmeta m3 ON p.ID = m3.post_id AND m3.meta_key='time' AND m3.meta_value=%s
            JOIN $wpdb->postmeta m4 ON p.ID = m4.post_id AND m4.meta_key='status' AND m4.meta_value='approved'
            WHERE p.ID != %d AND p.post_type='reservation_request' AND p.post_status='publish'
        ", $room, $date, $time, $id)) > 0;

        if ($status !== 'approved' && !$conflict) {
            echo '<a class="button" href="' . esc_url(wp_nonce_url(
                admin_url("admin-post.php?action=approve_reservation&post_id=$id"),
                'rrs_approve_' . $id
            )) . '">Approve</a>';
        }
        if ($status !== 'denied') {
            echo ' <a class="button" href="' . esc_url(wp_nonce_url(
                admin_url("admin-post.php?action=deny_reservation&post_id=$id"),
                'rrs_deny_' . $id
            )) . '">Deny</a>';
        }
        if ($status !== 'approved' && $conflict) {
            echo '<span style="color:#c00; margin-left:8px;">Slot Taken</span>';
        }
    }
}, 10, 2);

// 🗑 Hide quick edit, rename Trash
add_filter('post_row_actions', function($actions, $post) {
    if ($post->post_type !== 'reservation_request') return $actions;
    unset($actions['edit'], $actions['inline'], $actions['inline hide-if-no-js']);
    if (isset($actions['trash'])) $actions['trash'] = str_replace('Trash', 'Delete', $actions['trash']);
    return $actions;
}, 10, 2);

// 💾 Invalidate cache on delete
function rrs_clear_cache_on_delete($id) {
    if (get_post_type($id) === 'reservation_request') {
        $room = get_post_meta($id, 'room', true);
        if ($room) delete_transient('rrs_events_' . md5($room));
    }
}
// Clear cache on post delete or trash
add_action('before_delete_post', 'rrs_clear_cache_on_delete');
add_action('wp_trash_post', 'rrs_clear_cache_on_delete');

// 📤 CSV Export Notice + Handler
add_action('admin_notices', function() {
    $screen = get_current_screen();
    if ($screen->post_type === 'reservation_request' && current_user_can('administrator')) {
        $url = admin_url('admin-post.php?action=export_approved_reservations_csv');
        echo '<div class="notice notice-info is-dismissible"><p><a class="button button-primary" href="' . esc_url($url) . '">Export Report</a></p></div>';
    }
});
// Export approved reservations as CSV
add_action('admin_post_export_approved_reservations_csv', function() {
    if (!current_user_can('administrator')) wp_die('Access denied.');

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="room-reservation-report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'College', 'Course', 'Room', 'Date', 'Time Slot']);

    foreach (get_posts([
        'post_type' => 'reservation_request',
        'post_status' => 'publish',
        'meta_query' => [['key' => 'status', 'value' => 'approved']]
    ]) as $post) {
        $date = get_post_meta($post->ID, 'date', true);
        $time = get_post_meta($post->ID, 'time', true);
        $start = strtotime("$date $time");
        $end = strtotime('+1 hour', $start);
        fputcsv($output, [
            get_post_meta($post->ID, 'name', true),
            get_post_meta($post->ID, 'email', true),
            get_post_meta($post->ID, 'college', true),
            get_post_meta($post->ID, 'course', true),
            get_post_meta($post->ID, 'room', true),
            date('F j, Y', $start),
            date('g:i A', $start) . ' - ' . date('g:i A', $end)
        ]);
    }
    fclose($output);
    exit;
});

// ✅ Approve/Deny Handlers
add_action('admin_post_approve_reservation', function() {
    if (!current_user_can('edit_others_posts')) wp_die('Permission denied');
    $id = intval($_GET['post_id'] ?? 0);
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'rrs_approve_' . $id)) wp_die('Security check failed');

    $room = get_post_meta($id, 'room', true);
    $date = get_post_meta($id, 'date', true);
    $time = get_post_meta($id, 'time', true);

    $conflict = get_posts([
        'post_type' => 'reservation_request', 'post_status' => 'publish', 'exclude' => [$id],
        'meta_query' => [
            ['key' => 'room', 'value' => $room],
            ['key' => 'date', 'value' => $date],
            ['key' => 'time', 'value' => $time],
            ['key' => 'status', 'value' => 'approved']
        ]
    ]);
    if ($conflict) {
        wp_redirect(add_query_arg('rrs_error', 'slot_taken', admin_url('edit.php?post_type=reservation_request')));
        exit;
    }

    update_post_meta($id, 'status', 'approved');
    rrs_send_approval_email($id, 'approved');
    delete_transient('rrs_events_' . md5($room));

    foreach (get_posts([
        'post_type' => 'reservation_request', 'exclude' => [$id],
        'meta_query' => [
            ['key' => 'room', 'value' => $room],
            ['key' => 'date', 'value' => $date],
            ['key' => 'time', 'value' => $time],
            ['key' => 'status', 'value' => 'pending']
        ]
    ]) as $req) {
        update_post_meta($req->ID, 'status', 'denied');
        rrs_send_approval_email($req->ID, 'denied');
    }

    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});
// Deny reservation handler
add_action('admin_post_deny_reservation', function() {
    if (!current_user_can('edit_others_posts')) wp_die('Permission denied');
    $id = intval($_GET['post_id'] ?? 0);
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'rrs_deny_' . $id)) wp_die('Security check failed');

    update_post_meta($id, 'status', 'denied');
    rrs_send_approval_email($id, 'denied');

    $room = get_post_meta($id, 'room', true);
    if ($room) delete_transient('rrs_events_' . md5($room));

    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});
