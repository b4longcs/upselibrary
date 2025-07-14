<?php
/**
 * Plugin Name: Room Reservation System
 * Description: Room Reservations
 * Version: 1.0
 * Author: Jonathan Tubo
 */

/* ==========================================================================
    CUSTOM POST TYPE
========================================================================== */

add_action('init', function () {
    // Only register if user can manage content
    if (!current_user_can('edit_pages')) {
        return;
    }

    register_post_type('reservation_request', [
        'labels' => [
            'name'               => __('Reservations', 'your-textdomain'),
            'singular_name'      => __('Reservation', 'your-textdomain'),
            'edit_item'          => __('Edit Reservation', 'your-textdomain'),
            'view_item'          => __('View Reservation', 'your-textdomain'),
            'search_items'       => __('Search Reservations', 'your-textdomain'),
            'not_found'          => __('No reservations found.', 'your-textdomain'),
            'not_found_in_trash' => __('No reservations found in Trash.', 'your-textdomain'),
            'all_items'          => __('All Reservations', 'your-textdomain'),
        ],
        'public'              => false,
        'show_ui'             => true,               // Show list in admin
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => ['title'],
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_admin_bar'   => false,
        'has_archive'         => false,
    ]);
});



/* ==========================================================================
   FRONTEND SHORTCODE: RESERVATION FORM MODAL
========================================================================== */
add_shortcode('room_reservation_form', function () {
    ob_start(); ?>
    
    <!-- Room Reservation Modal -->
    <div id="rrs-modal" class="modal-hidden">
        <div class="rrs-modal-content">
            <h2 class="rrs-header my-3"><?php echo esc_html__('Room Reservation', 'your-textdomain'); ?></h2>

            <form id="rrs-reservation-form">
                <!-- User Details -->
                <input class="rrs-input my-1" type="text" name="name" placeholder="Full Name" required>
                <input class="rrs-input my-1" type="text" name="college" placeholder="College" required>
                <input class="rrs-input my-1" type="text" name="course" placeholder="Course" required>
                <input class="rrs-input my-1" type="email" name="email" placeholder="UP Email" required>

                <!-- Room & Time Selectors -->
                <div class="select-container d-flex flex-row justify-content-between align-items-center gap-2 w-100">

                    <!-- Room Dropdown -->
                    <select class="my-1 rrs-dropdown" name="room" required>
                        <option value="" disabled selected hidden><?php echo esc_html__('Select Room', 'your-textdomain'); ?></option>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo esc_attr("Room $i"); ?>">
                                <?php echo esc_html("Room $i"); ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <!-- Time Dropdown -->
                    <select class="my-1 rrs-dropdown" name="time" id="rrs-time-dropdown" required>
                        <option value="" disabled selected hidden><?php echo esc_html__('Select Time', 'your-textdomain'); ?></option>
                        <?php for ($i = 8; $i <= 16; $i++): ?>
                            <?php
                                $time_value      = sprintf('%02d:00', $i);
                                $time_label_start = date('g A', strtotime($time_value));
                                $time_label_end   = date('g A', strtotime(($i + 1) . ":00"));
                                $label = $time_label_start . ' - ' . $time_label_end;
                            ?>
                            <option value="<?php echo esc_attr($time_value); ?>">
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                </div>

                <!-- Date Picker -->
                <input class="rrs-input my-1" type="text" name="date" id="rrs-date-picker" placeholder="Select a date" required>

                <!-- Buttons -->
                <div class="rrs-button-container d-flex justify-content-between align-items-center gap-2 flex-row my-3">
                    <button id="rrs-close-modal" type="button"><?php echo esc_html__('Cancel', 'your-textdomain'); ?></button>
                    <button class="rrs-submit-modal" type="submit"><?php echo esc_html__('Submit', 'your-textdomain'); ?></button>
                </div>

                <!-- AJAX Response -->
                <div id="rrs-response"></div>
            </form>
        </div>
    </div>

    <!-- Success Popup -->
    <div id="rrs-success-popup" class="modal-hidden">
        <div class="rrs-success-content">
            <p id="rrs-success-message"></p>
            <button id="rrs-ok-button"><?php echo esc_html__('OK', 'your-textdomain'); ?></button>
        </div>
    </div>

    <?php return ob_get_clean();
});


/* ==========================================================================
   FRONTEND ASSETS: SCRIPTS & STYLES
========================================================================== */
add_action('wp_enqueue_scripts', function () {
    if (!is_page('room-reservation')) return;

    $dir = plugin_dir_url(__FILE__);
    $path = plugin_dir_path(__FILE__);

    // Local plugin assets
    wp_enqueue_script('rrs-script', $dir . 'rrs-script.js', ['jquery'], filemtime($path . 'rrs-script.js'), true);
    wp_localize_script('rrs-script', 'rrs_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('rrs_nonce')
    ]);
    wp_enqueue_style('rrs-reservation-css', $dir . 'room-reservation-system.css', [], filemtime($path . 'room-reservation-system.css'));

    // External libraries
    wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js', [], '6.1.9', true);
    wp_enqueue_style('fullcalendar-style', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css', [], '6.1.9');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css');
});

/* ==========================================================================
   HANDLE FORM SUBMISSION (AJAX)
========================================================================== */
add_action('wp_ajax_submit_reservation', 'rrs_handle_reservation');
add_action('wp_ajax_nopriv_submit_reservation', 'rrs_handle_reservation');

function rrs_handle_reservation() {
    check_ajax_referer('rrs_nonce', 'nonce');

    $data  = array_map('sanitize_text_field', $_POST);
    $email = sanitize_email($data['email']);

    // Check for conflicting approved reservation
    $conflict = get_posts([
        'post_type'  => 'reservation_request',
        'fields'     => 'ids',
        'meta_query' => [
            ['key' => 'room',   'value' => $data['room']],
            ['key' => 'date',   'value' => $data['date']],
            ['key' => 'time',   'value' => $data['time']],
            ['key' => 'status', 'value' => 'approved']
        ]
    ]);

    if ($conflict) {
        wp_send_json_error(['message' => 'That time slot is already taken.']);
    }

    // Insert reservation post
    wp_insert_post([
        'post_type'   => 'reservation_request',
        'post_title'  => "{$data['name']} - {$data['room']}",
        'post_status' => 'publish',
        'meta_input'  => [
            'name'    => $data['name'],
            'college' => $data['college'],
            'course'  => $data['course'],
            'email'   => $email,
            'room'    => $data['room'],
            'date'    => $data['date'],
            'time'    => $data['time'],
            'status'  => 'pending'
        ]
    ]);

    // Invalidate cache for this room
    delete_transient('rrs_events_' . md5($data['room']));

    wp_send_json_success(['message' => 'Reservation submitted and pending approval.']);
}

/* ==========================================================================
   CALENDAR SHORTCODE + FETCH EVENTS
========================================================================== */
add_shortcode('room_reservation_calendar', function () {
    ob_start(); ?>
    <div class="d-flex flex-row align-items-center gap-3 mb-3">
        <select id="rrs-room-select">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?php echo esc_attr("Room $i"); ?>"><?php echo esc_html("Room $i"); ?></option>
            <?php endfor; ?>
        </select>
        <button id="rrs-open-modal"><?php echo esc_html__('Reserve a Room', 'your-textdomain'); ?></button>
    </div>
    <div id="rrs-calendar"></div>
    <?php return ob_get_clean();
});

add_action('wp_ajax_get_approved_reservations', 'rrs_get_approved_reservations');
add_action('wp_ajax_nopriv_get_approved_reservations', 'rrs_get_approved_reservations');

/**
 * AJAX handler: Return approved reservations for the selected room (cached for 5 mins)
 */
function rrs_get_approved_reservations() {
    check_ajax_referer('rrs_nonce', 'nonce');

    $room      = sanitize_text_field($_GET['room'] ?? '');
    $cache_key = 'rrs_events_' . md5($room);

    if (!$room) {
        wp_send_json_error(['message' => 'Invalid room selected.']);
    }

    // Try to get from cache
    $events = get_transient($cache_key);
    if ($events === false) {
        $posts = get_posts([
            'post_type'      => 'reservation_request',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [
                ['key' => 'room',   'value' => $room],
                ['key' => 'status', 'value' => 'approved'],
            ]
        ]);

        $events = array_map(function ($post) {
            $date = get_post_meta($post->ID, 'date', true);
            $time = get_post_meta($post->ID, 'time', true);
            $name = get_post_meta($post->ID, 'name', true);

            if (!$date || !$time) return null;

            $start = date('Y-m-d\TH:i:s', strtotime("$date $time"));
            $end   = date('Y-m-d\TH:i:s', strtotime("$date $time +1 hour"));

            return [
                'title' => "Reserved by $name",
                'start' => $start,
                'end'   => $end,
            ];
        }, $posts);

        $events = array_filter($events); // remove nulls
        set_transient($cache_key, $events, 5 * MINUTE_IN_SECONDS);
    }

    wp_send_json_success($events);
}

/* ==========================================================================
   ADMIN INTERFACE CUSTOMIZATIONS
========================================================================== */
// Hide "Add New" (Top Bar, Submenu, Page)
add_action('admin_bar_menu', function($bar) {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'reservation_request') {
        $bar->remove_node('new-reservation_request');
    }
}, 999);

add_action('admin_menu', function() {
    remove_submenu_page('edit.php?post_type=reservation_request', 'post-new.php?post_type=reservation_request');
    if (!current_user_can('edit_pages')) {
        remove_menu_page('edit.php?post_type=reservation_request');
    }
}, 99);

add_action('load-post-new.php', function() {
    if ($_GET['post_type'] === 'reservation_request') {
        wp_die('You are not allowed to add a reservation manually.');
    }
});

// 🎨 Admin CSS to hide "Add New" button & style row actions
add_action('admin_head', function() {
    global $typenow;
    if ($typenow !== 'reservation_request') return;

    echo '<style>
        .page-title-action,
        .post-type-reservation_request .subsubsub .create { display: none !important; }
        .wp-list-table .row-actions { visibility: hidden; }
        .wp-list-table tr:hover .row-actions { visibility: visible; }
    </style>';
});

// Custom admin columns
add_filter('manage_edit-reservation_request_columns', function($cols) {
    return array_merge($cols, [
        'reservation_datetime' => 'Date & Time',
        'reservation_name'     => 'Name',
        'reservation_email'    => 'Email',
        'reservation_status'   => 'Status',
        'reservation_action'   => 'Action'
    ]);
});

add_action('manage_reservation_request_posts_custom_column', function($col, $post_id) {
    $meta = fn($key) => esc_html(get_post_meta($post_id, $key, true));
    if ($col === 'reservation_datetime') {
        $dt = strtotime("{$meta('date')} {$meta('time')}");
        echo $dt ? date('M j, Y • g A', $dt) . ' - ' . date('g A', strtotime('+1 hour', $dt)) : '—';
    } elseif ($col === 'reservation_name') echo $meta('name');
    elseif ($col === 'reservation_email') echo $meta('email');
    elseif ($col === 'reservation_status') echo $meta('status');
    elseif ($col === 'reservation_action') {
        $status = get_post_meta($post_id, 'status', true);
        $room   = get_post_meta($post_id, 'room', true);
        $date   = get_post_meta($post_id, 'date', true);
        $time   = get_post_meta($post_id, 'time', true);

        global $wpdb;
        $is_conflict = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $wpdb->posts p
            JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key = 'room' AND m1.meta_value = %s
            JOIN $wpdb->postmeta m2 ON p.ID = m2.post_id AND m2.meta_key = 'date' AND m2.meta_value = %s
            JOIN $wpdb->postmeta m3 ON p.ID = m3.post_id AND m3.meta_key = 'time' AND m3.meta_value = %s
            JOIN $wpdb->postmeta m4 ON p.ID = m4.post_id AND m4.meta_key = 'status' AND m4.meta_value = 'approved'
            WHERE p.ID != %d AND p.post_type = 'reservation_request' AND p.post_status = 'publish'
        ", $room, $date, $time, $post_id)) > 0;

        if ($status !== 'approved' && !$is_conflict) {
            echo '<a class="button" href="' . esc_url(wp_nonce_url(
                admin_url("admin-post.php?action=approve_reservation&post_id=$post_id"),
                'rrs_approve_' . $post_id
            )) . '">Approve</a>';
        }

        if ($status !== 'denied') {
            echo ' <a class="button" href="' . esc_url(wp_nonce_url(
                admin_url("admin-post.php?action=deny_reservation&post_id=$post_id"),
                'rrs_deny_' . $post_id
            )) . '">Deny</a>';
        }

        if ($status !== 'approved' && $is_conflict) {
            echo '<span style="color:#c00; margin-left:8px;">Slot Taken</span>';
        }
    }
}, 10, 2);

//  Remove inline/quick edit, rename "Trash" to "Delete"
add_filter('post_row_actions', function($actions, $post) {
    if ($post->post_type !== 'reservation_request') return $actions;
    unset($actions['edit'], $actions['inline'], $actions['inline hide-if-no-js']);
    if (isset($actions['trash'])) {
        $actions['trash'] = str_replace('Trash', 'Delete', $actions['trash']);
    }
    return $actions;
}, 10, 2);

// 🧹 Invalidate cache on deletion
add_action('before_delete_post', 'rrs_clear_cache_on_delete');
add_action('wp_trash_post',    'rrs_clear_cache_on_delete');
function rrs_clear_cache_on_delete($post_id) {
    if (get_post_type($post_id) === 'reservation_request') {
        $room = get_post_meta($post_id, 'room', true);
        if ($room) delete_transient('rrs_events_' . md5($room));
    }
}

//  CSV Export Button
add_action('admin_notices', function() {
    if (get_current_screen()->post_type === 'reservation_request' && current_user_can('administrator')) {
        $url = admin_url('admin-post.php?action=export_approved_reservations_csv');
        echo '<div class="notice notice-info is-dismissible"><p><a class="button button-primary" href="' . esc_url($url) . '">Export Report</a></p></div>';
    }
});

add_action('admin_post_export_approved_reservations_csv', function() {
    if (!current_user_can('administrator')) wp_die('Access denied.');

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="room-reservation-report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'College', 'Course', 'Room', 'Date', 'Time Slot']);

    $posts = get_posts([
        'post_type'   => 'reservation_request',
        'post_status' => 'publish',
        'meta_query'  => [['key' => 'status', 'value' => 'approved']]
    ]);

    foreach ($posts as $post) {
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

//  Approve/Deny Handlers
add_action('admin_post_approve_reservation', function() {
    if (!current_user_can('edit_others_posts')) wp_die('Permission denied');
    $post_id = intval($_GET['post_id']);
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'rrs_approve_' . $post_id)) wp_die('Security check failed');

    $room = get_post_meta($post_id, 'room', true);
    $date = get_post_meta($post_id, 'date', true);
    $time = get_post_meta($post_id, 'time', true);

    $conflict = get_posts([
        'post_type'   => 'reservation_request',
        'post_status' => 'publish',
        'exclude'     => [$post_id],
        'meta_query'  => [
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

    update_post_meta($post_id, 'status', 'approved');
    rrs_send_approval_email($post_id, 'approved');
    delete_transient('rrs_events_' . md5($room));

    // Deny other requests for same slot
    $others = get_posts([
        'post_type' => 'reservation_request',
        'exclude'   => [$post_id],
        'meta_query' => [
            ['key' => 'room', 'value' => $room],
            ['key' => 'date', 'value' => $date],
            ['key' => 'time', 'value' => $time],
            ['key' => 'status', 'value' => 'pending']
        ]
    ]);
    foreach ($others as $req) {
        update_post_meta($req->ID, 'status', 'denied');
        rrs_send_approval_email($req->ID, 'denied');
    }

    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});

add_action('admin_post_deny_reservation', function() {
    if (!current_user_can('edit_others_posts')) wp_die('Permission denied');
    $post_id = intval($_GET['post_id']);
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'rrs_deny_' . $post_id)) wp_die('Security check failed');

    update_post_meta($post_id, 'status', 'denied');
    rrs_send_approval_email($post_id, 'denied');

    $room = get_post_meta($post_id, 'room', true);
    if ($room) delete_transient('rrs_events_' . md5($room));

    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});


/* ==========================================================================
    EMAIL NOTIFICATIONS
========================================================================== */
function rrs_send_approval_email($post_id, $status) {
    if (!in_array($status, ['approved', 'denied'], true)) return;

    $meta = fn($k) => sanitize_text_field(get_post_meta($post_id, $k, true));
    $email = sanitize_email($meta('email'));
    if (!$email || !is_email($email)) return;

    $name = $meta('name'); $room = $meta('room'); $date = $meta('date'); $time = $meta('time');
    $start = strtotime("$date $time");
    $date_fmt = date('F j, Y', $start);
    $time_fmt = date('g:i A', $start) . ' - ' . date('g:i A', strtotime('+1 hour', $start));
    
    $msg = "
        <html><body style='font-family:sans-serif'>
            <p>Dear <strong>$name</strong>,</p>
            <p>Your reservation for <strong>$room</strong> on <strong>$date_fmt</strong> at <strong>$time_fmt</strong> has been <strong>" . strtoupper($status) . "</strong>.</p>
            <p>Thank you,<br>UP Library Reservation System</p>
        </body></html>";

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($email, "Room Reservation " . ucfirst($status), $msg, $headers);
    wp_mail(get_option('admin_email'), "Reservation $status: $name - $room", $msg, $headers);
}


/* ==========================================================================
   ADMIN FILTERS & STYLING
========================================================================== */

// Filters: Room & Date
add_action('restrict_manage_posts', function () {
    global $typenow;
    if ($typenow !== 'reservation_request') return;

    $selected_room = $_GET['room_filter'] ?? '';
    echo '<select name="room_filter"><option value="">All Rooms</option>';
    for ($i = 1; $i <= 6; $i++) {
        $room = "Room $i";
        printf('<option value="%s"%s>%s</option>',
            esc_attr($room),
            selected($selected_room, $room, false),
            esc_html($room)
        );
    }
    echo '</select>';

    printf('<input type="text" name="date_filter" id="rrs-date-filter" placeholder="Filter by Date" value="%s" style="width:130px; margin-left:10px;" autocomplete="off">',
        esc_attr($_GET['date_filter'] ?? '')
    );
});

// Apply meta query filters (room/date/time)
add_filter('parse_query', function ($query) {
    if (is_admin() && $query->get('post_type') === 'reservation_request') {
        $meta_query = [];

        foreach (['room', 'time', 'date'] as $key) {
            $param = "{$key}_filter";
            if (!empty($_GET[$param])) {
                $meta_query[] = [
                    'key'   => $key,
                    'value' => sanitize_text_field($_GET[$param])
                ];
            }
        }

        if ($meta_query) {
            $existing = $query->get('meta_query') ?: [];
            $query->set('meta_query', array_merge($existing, $meta_query));
        }
    }
});

// Load date picker and admin CSS
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'edit.php' && ($_GET['post_type'] ?? '') === 'reservation_request') {
        wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
        wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
        wp_add_inline_script('flatpickr-js', "document.addEventListener('DOMContentLoaded',function(){flatpickr('#rrs-date-filter',{dateFormat:'Y-m-d',allowInput:true});});");
        wp_enqueue_style('rrs-admin-style', plugin_dir_url(__FILE__) . 'rrs-admin.css');
    }
});

// Tab UI for Room and Time filters
add_filter('views_edit-reservation_request', function ($views) {
    $room_selected = $_GET['room_filter'] ?? '';
    $time_selected = $_GET['time_filter'] ?? '';
    $base = admin_url('edit.php?post_type=reservation_request');

    echo '<div class="room-tabs" style="margin:10px 0; display:flex; gap:10px;">';
    echo '<a class="room-tab' . ($room_selected === '' ? ' active' : '') . '" href="' . esc_url($base) . '">All Rooms</a>';
    for ($i = 1; $i <= 6; $i++) {
        $room = "Room $i";
        $url = add_query_arg('room_filter', urlencode($room), $base);
        echo '<a class="room-tab' . ($room_selected === $room ? ' active' : '') . '" href="' . esc_url($url) . '">' . esc_html($room) . '</a>';
    }
    echo '</div>';

    if ($room_selected !== '') {
        echo '<div class="time-tabs" style="margin-bottom:10px; display:flex; gap:8px;">';
        echo '<a class="time-tab' . ($time_selected === '' ? ' active' : '') . '" href="' . esc_url(add_query_arg(['room_filter' => $room_selected], $base)) . '">All Times</a>';
        for ($i = 8; $i <= 16; $i++) {
            $time = "$i:00";
            $label = date('g A', strtotime($time)) . ' - ' . date('g A', strtotime(($i + 1) . ":00"));
            $url = add_query_arg(['room_filter' => $room_selected, 'time_filter' => $time], $base);
            echo '<a class="time-tab' . ($time_selected === $time ? ' active' : '') . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
        }
        echo '</div>';
    }

    return $views;
});

// Add class based on time (e.g., time-09, time-14)
add_filter('post_class', function ($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'reservation_request') {
        $time = get_post_meta($post_id, 'time', true);
        if ($time) {
            $hour = str_pad((int) explode(':', $time)[0], 2, '0', STR_PAD_LEFT);
            $classes[] = 'time-' . $hour;
        }
    }
    return $classes;
}, 10, 3);
