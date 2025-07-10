<?php
/**
 * Plugin Name: Room Reservation System
 * Description: A simple room reservation system with calendar view, approval workflow, and email notifications.
 * Version: 1.0
 * Author: Jonathan Tubo
 */

/* ==========================================================================
   1. REGISTER CUSTOM POST TYPE
   Purpose: Store each reservation as a custom post.
========================================================================== */
add_action('init', function() {
    register_post_type('reservation_request', [
        'labels' => [
            'name'          => 'Room Reservations',
            'singular_name' => 'Reservation',
            'add_new'       => '', // removes label from UI
            'add_new_item'  => '',
        ],
        'public'            => false,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'menu_icon'         => 'dashicons-calendar-alt',
        'supports'          => ['title', 'custom-fields'],
        'capability_type'   => 'post',
        'map_meta_cap'      => true,
        'show_in_admin_bar' => false, // hides from top admin bar
        'has_archive'       => false,
    ]);

});

/* ==========================================================================
   2. FRONTEND SHORTCODE: RESERVATION FORM + MODAL
   Shortcode: [room_reservation_form]
========================================================================== */
add_shortcode('room_reservation_form', function() {
    ob_start(); ?>
    
    <!-- Trigger Button -->
    <button id="rrs-open-modal">Reserve a Room</button>

    <!-- Modal Form -->
    <div id="rrs-modal">
        <div style="background:#fff; padding:20px; max-width:500px; width:100%; border-radius:8px; position:relative;">
            <button id="rrs-close-modal" style="position:absolute; top:10px; right:10px;">Cancel</button>
            <form id="rrs-reservation-form">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="college" placeholder="College" required>
                <input type="text" name="course" placeholder="Course" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="subject" placeholder="Subject" required>

                <select name="room" required>
                    <option value="">Select Room</option>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="Room <?= $i ?>">Room <?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <input type="date" name="date" required>

                <select name="time" required>
                    <?php for ($i = 8; $i <= 16; $i++): ?>
                        <option value="<?= $i ?>:00">
                            <?= date('g A', strtotime("$i:00")) ?> - <?= date('g A', strtotime(($i+1) . ":00")) ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <button type="submit">Submit</button>
                <div id="rrs-response"></div>
            </form>
        </div>
    </div>

    <?php return ob_get_clean();
});

/* ==========================================================================
   3. FRONTEND ASSETS: SCRIPTS & STYLES
   Purpose: Load jQuery, FullCalendar, and custom CSS/JS.
========================================================================== */
add_action('wp_enqueue_scripts', function() {
    // JS for handling form + calendar
    wp_enqueue_script('rrs-script', plugin_dir_url(__FILE__) . 'rrs-script.js', ['jquery'], null, true);
    wp_localize_script('rrs-script', 'rrs_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('rrs_nonce')
    ]);

    // FullCalendar assets
    wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js', [], null, true);
    wp_enqueue_style('fullcalendar-style', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css');

    // Custom calendar styles
    wp_enqueue_style('rrs-reservation-css', plugin_dir_url(__FILE__) . 'room-reservation-system.css');
});

/* ==========================================================================
   4. HANDLE FORM SUBMISSION (AJAX)
   Action: submit_reservation
========================================================================== */
add_action('wp_ajax_submit_reservation', 'rrs_handle_reservation');
add_action('wp_ajax_nopriv_submit_reservation', 'rrs_handle_reservation');

function rrs_handle_reservation() {
    check_ajax_referer('rrs_nonce', 'nonce');
    $data = array_map('sanitize_text_field', $_POST);
    $email = sanitize_email($data['email']);

    // Check for existing approved reservation
    $existing = get_posts([
        'post_type' => 'reservation_request',
        'meta_query' => [
            ['key' => 'room', 'value' => $data['room']],
            ['key' => 'date', 'value' => $data['date']],
            ['key' => 'time', 'value' => $data['time']],
            ['key' => 'status', 'value' => 'approved']
        ]
    ]);

    if ($existing) {
        wp_send_json_error(['message' => 'That time slot is already taken.']);
    }

    // Save the reservation
    wp_insert_post([
        'post_type'    => 'reservation_request',
        'post_title'   => "{$data['name']} - {$data['room']}",
        'post_status'  => 'publish',
        'meta_input'   => [
            'name'    => $data['name'],
            'college' => $data['college'],
            'course'  => $data['course'],
            'email'   => $email,
            'subject' => $data['subject'],
            'room'    => $data['room'],
            'date'    => $data['date'],
            'time'    => $data['time'],
            'status'  => 'pending'
        ]
    ]);

    wp_send_json_success(['message' => 'Reservation submitted and pending approval.']);
}

/* ==========================================================================
   5. CALENDAR SHORTCODE + FETCH EVENTS
   Shortcode: [room_reservation_calendar]
   AJAX: get_approved_reservations
========================================================================== */
add_shortcode('room_reservation_calendar', function() {
    ob_start(); ?>
    <div>
        <select id="rrs-room-select">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="Room <?= $i ?>">Room <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div id="rrs-calendar"></div>
    <?php return ob_get_clean();
});

add_action('wp_ajax_get_approved_reservations', 'rrs_get_approved_reservations');
add_action('wp_ajax_nopriv_get_approved_reservations', 'rrs_get_approved_reservations');

function rrs_get_approved_reservations() {
    check_ajax_referer('rrs_nonce', 'nonce');
    $room = sanitize_text_field($_GET['room']);

    $posts = get_posts([
        'post_type'      => 'reservation_request',
        'posts_per_page' => -1,
        'meta_query'     => [
            ['key' => 'room', 'value' => $room],
            ['key' => 'status', 'value' => 'approved']
        ]
    ]);

    $events = [];
    foreach ($posts as $post) {
        $date = get_post_meta($post->ID, 'date', true);
        $time = get_post_meta($post->ID, 'time', true);
        $name = get_post_meta($post->ID, 'name', true);

        $start = date('Y-m-d\TH:i:s', strtotime("$date $time"));
        $end   = date('Y-m-d\TH:i:s', strtotime("$date $time +1 hour"));

        $events[] = [
            'title' => "Reserved by $name",
            'start' => $start,
            'end'   => $end,
        ];
    }

    wp_send_json_success($events);
}

/* ==========================================================================
   6. ADMIN COLUMNS + APPROVE/DENY HANDLERS
========================================================================== */
// ✅ 1. Remove "Add New" from admin toolbar (top black bar)
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'reservation_request') {
        $wp_admin_bar->remove_node('new-reservation_request');
    }
}, 999);

// ✅ 2. Hide the "Add New" button beside the page title
add_action('admin_head', function() {
    global $typenow;
    if ($typenow === 'reservation_request') {
        echo '<style>.page-title-action { display: none; }</style>';
    }
});

// ✅ 3. Remove "Add New" from the sidebar under "Room Reservations"
add_action('admin_menu', function() {
    remove_submenu_page('edit.php?post_type=reservation_request', 'post-new.php?post_type=reservation_request');
});

// ✅ 4. Block direct access to post-new.php?post_type=reservation_request
add_action('load-post-new.php', function() {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'reservation_request') {
        wp_die('You are not allowed to add a reservation manually.');
    }
});


// Add custom columns
add_filter('manage_edit-reservation_request_columns', function($columns) {
    $columns['reservation_name']   = 'Name';
    $columns['reservation_email']  = 'Email';
    $columns['reservation_status'] = 'Status';
    $columns['reservation_action'] = 'Action';
    return $columns;
});

//Disable Quick Edit, Edit, Trash Links
add_filter('post_row_actions', function($actions, $post) {
    if ($post->post_type === 'reservation_request') {
        unset($actions['edit']);
        unset($actions['inline hide-if-no-js']); // Quick Edit
        unset($actions['trash']);
        unset($actions['view']);
    }
    return $actions;
}, 10, 2);


// Render column content
add_action('manage_reservation_request_posts_custom_column', function($column, $post_id) {
    if ($column === 'reservation_name') {
        echo esc_html(get_post_meta($post_id, 'name', true));
    }

    if ($column === 'reservation_email') {
        echo esc_html(get_post_meta($post_id, 'email', true));
    }

    if ($column === 'reservation_status') {
        echo esc_html(get_post_meta($post_id, 'status', true));
    }

    if ($column === 'reservation_action') {
        $status = get_post_meta($post_id, 'status', true);
        if ($status !== 'approved') {
            echo '<a href="' . admin_url("admin-post.php?action=approve_reservation&post_id=$post_id") . '" class="button">Approve</a> ';
        }
        if ($status !== 'denied') {
            echo '<a href="' . admin_url("admin-post.php?action=deny_reservation&post_id=$post_id") . '" class="button">Deny</a>';
        }
    }
}, 10, 2);


// Approve action
add_action('admin_post_approve_reservation', function() {
    if (!current_user_can('edit_posts')) wp_die('Permission denied');
    $post_id = intval($_GET['post_id']);
    update_post_meta($post_id, 'status', 'approved');
    rrs_send_approval_email($post_id, 'approved');
    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});

// Deny action
add_action('admin_post_deny_reservation', function() {
    if (!current_user_can('edit_posts')) wp_die('Permission denied');
    $post_id = intval($_GET['post_id']);
    update_post_meta($post_id, 'status', 'denied');
    rrs_send_approval_email($post_id, 'denied');
    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});

/* ==========================================================================
   7. EMAIL NOTIFICATIONS
   Purpose: Notify both user and admin about reservation status
========================================================================== */
function rrs_send_approval_email($post_id, $status) {
    $email = get_post_meta($post_id, 'email', true);
    $name  = get_post_meta($post_id, 'name', true);
    $room  = get_post_meta($post_id, 'room', true);
    $date  = get_post_meta($post_id, 'date', true);
    $time  = get_post_meta($post_id, 'time', true);

    $subject = "Reservation $status";
    $message = "Dear $name,\n\nYour reservation for $room on $date at $time has been $status.\n\nThank you.";

    // Send to user
    wp_mail($email, $subject, $message);

    // Notify admin
    wp_mail(get_option('admin_email'), "Reservation $status: $name - $room", $message);
}
