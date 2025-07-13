<?php
/**
 * Plugin Name: Room Reservation System
 * Description: A simple room reservation system with calendar view, approval workflow, and email notifications.
 * Version: 1.0
 * Author: Jonathan Tubo
 */

/* ==========================================================================
   1. REGISTER CUSTOM POST TYPE
   --------------------------------------------------------------------------
   Purpose:
   - Store each room reservation request as a custom post.
   - Display it in the admin UI but hide all manual creation/edit actions.
========================================================================== */
add_action('init', function() {
    register_post_type('reservation_request', [
        'labels' => [
            'name'          => 'Room Reservations',
            'singular_name' => 'Reservation',
            'add_new'       => '', // Hides "Add New" from UI
            'add_new_item'  => '',
        ],
        'public'            => false,
        'show_ui'           => current_user_can('edit_pages'),
        'show_in_menu'      => current_user_can('edit_pages'),
        'menu_icon'         => 'dashicons-calendar-alt',
        'supports'          => ['title', 'custom-fields'],
        'capability_type'   => 'post',
        'map_meta_cap'      => true,
        'show_in_admin_bar' => false,
        'has_archive'       => false,
    ]);
});

/* ==========================================================================
   2. FRONTEND SHORTCODE: RESERVATION FORM MODAL
   --------------------------------------------------------------------------
   Shortcode: [room_reservation_form]
   Purpose:
   - Display a modal form for users to submit room reservation requests.
========================================================================== */
add_shortcode('room_reservation_form', function() {
    ob_start(); ?>
    
    <!-- Modal container triggered by button with ID #rrs-open-modal -->
    <div id="rrs-modal" class="modal-hidden">
        <div class="rrs-modal-content">
            <h2 class="rrs-header my-3">Room Reservation</h2>
            <form id="rrs-reservation-form">
                <!-- Input fields for personal and booking info -->
                <input class="rrs-input my-1" type="text" name="name" placeholder="Full Name" required>
                <input class="rrs-input my-1" type="text" name="college" placeholder="College" required>
                <input class="rrs-input my-1" type="text" name="course" placeholder="Course" required>
                <input class="rrs-input my-1" type="email" name="email" placeholder="UP Email" required>

                <!-- Room & Time selectors -->
                <div class="select-container d-flex flex-row justify-content-between align-items-center gap-2 w-100">
                    <select class="my-1 rrs-dropdown" name="room" required>
                        <option value="" disabled selected hidden>Select Room</option>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option class="my-2" value="Room <?= $i ?>">Room <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <select class="my-1 rrs-dropdown" name="time" required>
                        <option value="" disabled selected hidden>Select Time</option>
                        <?php for ($i = 8; $i <= 16; $i++): ?>
                            <option class="my-2" value="<?= $i ?>:00">
                                <?= date('g A', strtotime("$i:00")) ?> - <?= date('g A', strtotime(($i+1) . ":00")) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Date picker -->
                <input class="rrs-input my-1" type="text" name="date" id="rrs-date-picker" placeholder="Select a date" required>

                <!-- Form actions -->
                <div class="rrs-button-container d-flex justify-content-between align-items-center gap-2 flex-row my-3">
                    <button id="rrs-close-modal" type="button">Cancel</button>
                    <button class="rrs-submit-modal" type="submit">Submit</button>
                </div>

                <div id="rrs-response"></div>
            </form>
        </div>
    </div>

    <!-- Confirmation popup -->
    <div id="rrs-success-popup" class="modal-hidden">
        <div class="rrs-success-content">
            <p id="rrs-success-message"></p>
            <button id="rrs-ok-button">OK</button>
        </div>
    </div>
    <?php return ob_get_clean();
});

/* ==========================================================================
   3. FRONTEND ASSETS: SCRIPTS & STYLES
   --------------------------------------------------------------------------
   Purpose:
   - Enqueue necessary libraries and plugin assets.
========================================================================== */
add_action('wp_enqueue_scripts', function() {
    if (!is_page('room-reservation')) return; 

    wp_enqueue_script('rrs-script', plugin_dir_url(__FILE__) . 'rrs-script.js', ['jquery'], null, true);
    wp_localize_script('rrs-script', 'rrs_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('rrs_nonce')
    ]);

    wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js', [], null, true);
    wp_enqueue_style('fullcalendar-style', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css');

    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);

    wp_enqueue_style('rrs-reservation-css', plugin_dir_url(__FILE__) . 'room-reservation-system.css');
});

/* ==========================================================================
   4. HANDLE FORM SUBMISSION (AJAX)
   --------------------------------------------------------------------------
   AJAX Action: submit_reservation
   Purpose:
   - Validate input, check for conflicts, save as a custom post, return success/error.
========================================================================== */
add_action('wp_ajax_submit_reservation', 'rrs_handle_reservation');
add_action('wp_ajax_nopriv_submit_reservation', 'rrs_handle_reservation');

function rrs_handle_reservation() {
    check_ajax_referer('rrs_nonce', 'nonce');
    $data = array_map('sanitize_text_field', $_POST);
    $email = sanitize_email($data['email']);

    // Check for conflicting approved reservation
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

    // Save new reservation request
    wp_insert_post([
        'post_type'    => 'reservation_request',
        'post_title'   => "{$data['name']} - {$data['room']}",
        'post_status'  => 'publish',
        'meta_input'   => [
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

    wp_send_json_success(['message' => 'Reservation submitted and pending approval.']);
}

/* ==========================================================================
   5. CALENDAR SHORTCODE + FETCH EVENTS
   --------------------------------------------------------------------------
   Shortcode: [room_reservation_calendar]
   AJAX: get_approved_reservations
   Purpose:
   - Display a calendar of approved reservations for a selected room.
========================================================================== */
add_shortcode('room_reservation_calendar', function() {
    ob_start(); ?>
    <div class="d-flex flex-row align-items-center gap-3 mb-3">
        <select id="rrs-room-select">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="Room <?= $i ?>">Room <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <button id="rrs-open-modal">Reserve a Room</button>
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
   6. ADMIN INTERFACE CUSTOMIZATIONS
   --------------------------------------------------------------------------
   Purpose:
   - Prevent manual reservation entry via admin.
   - Add custom columns, filters, actions.
========================================================================== */
// Prevent manual entry via admin
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'reservation_request') {
        $wp_admin_bar->remove_node('new-reservation_request');
    }
}, 999);

add_action('admin_head', function() {
    global $typenow;
    if ($typenow === 'reservation_request') {
        echo '<style>.page-title-action { display: none; }</style>';
    }
});

add_action('admin_menu', function() {
    // Remove "Add New" submenu
    remove_submenu_page('edit.php?post_type=reservation_request', 'post-new.php?post_type=reservation_request');

    // hide the "Room Reservations" menu 
    if (!current_user_can('edit_pages')) {
        remove_menu_page('edit.php?post_type=reservation_request');
    }
}, 99);


add_action('load-post-new.php', function() {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'reservation_request') {
        wp_die('You are not allowed to add a reservation manually.');
    }
});

// Add columns to the admin table
add_filter('manage_edit-reservation_request_columns', function($columns) {
    $columns['reservation_datetime'] = 'Date & Time';
    $columns['reservation_name']     = 'Name';
    $columns['reservation_email']    = 'Email';
    $columns['reservation_status']   = 'Status';
    $columns['reservation_action']   = 'Action';
    return $columns;
});

// Remove quick actions from row
add_filter('post_row_actions', function($actions, $post) {
    if ($post->post_type === 'reservation_request') {
        unset($actions['edit'], $actions['inline hide-if-no-js'], $actions['trash'], $actions['view']);
    }
    return $actions;
}, 10, 2);

// Populate custom columns
add_action('manage_reservation_request_posts_custom_column', function($column, $post_id) {
    if ($column === 'reservation_datetime') {
        $date = get_post_meta($post_id, 'date', true);
        $time = get_post_meta($post_id, 'time', true);
        if ($date && $time) {
            $datetime = strtotime("$date $time");
            echo esc_html(date('M j, Y • g A', $datetime) . ' - ' . date('g A', strtotime('+1 hour', $datetime)));
        } else {
            echo '—';
        }
    }

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

// Handle approve/deny actions
add_action('admin_post_approve_reservation', function() {
    if (!current_user_can('edit_posts')) wp_die('Permission denied');
    $post_id = intval($_GET['post_id']);

    // Get the room, date, and time of this reservation
    $room = get_post_meta($post_id, 'room', true);
    $date = get_post_meta($post_id, 'date', true);
    $time = get_post_meta($post_id, 'time', true);

    // Check if another reservation is already approved for this slot
    $existing_approved = get_posts([
        'post_type'      => 'reservation_request',
        'posts_per_page' => 1,
        'meta_query'     => [
            ['key' => 'room', 'value' => $room],
            ['key' => 'date', 'value' => $date],
            ['key' => 'time', 'value' => $time],
            ['key' => 'status', 'value' => 'approved'],
            ['key' => '_wpnonce', 'compare' => 'NOT EXISTS'], // avoid conflict with nonce meta if any
        ],
        'exclude' => [$post_id], 
    ]);

    if ($existing_approved) {
        // Redirect back with error message
        $redirect_url = add_query_arg('rrs_error', 'slot_taken', admin_url('edit.php?post_type=reservation_request'));
        wp_redirect($redirect_url);
        exit;
    }

    // Approve this reservation
    update_post_meta($post_id, 'status', 'approved');
    rrs_send_approval_email($post_id, 'approved');

    // Optional: Deny all other pending requests for this slot
    $other_requests = get_posts([
        'post_type'      => 'reservation_request',
        'posts_per_page' => -1,
        'meta_query'     => [
            ['key' => 'room', 'value' => $room],
            ['key' => 'date', 'value' => $date],
            ['key' => 'time', 'value' => $time],
            ['key' => 'status', 'value' => 'pending'],
        ],
        'exclude' => [$post_id],
    ]);
    foreach ($other_requests as $request) {
        update_post_meta($request->ID, 'status', 'denied');
        rrs_send_approval_email($request->ID, 'denied');
    }

    wp_redirect(admin_url('edit.php?post_type=reservation_request'));
    exit;
});


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
   --------------------------------------------------------------------------
   Purpose:
   - Notify users of their reservation status and send copy to admin.
========================================================================== */
function rrs_send_approval_email($post_id, $status) {
    $email = get_post_meta($post_id, 'email', true);
    $name  = get_post_meta($post_id, 'name', true);
    $room  = get_post_meta($post_id, 'room', true);
    $date  = get_post_meta($post_id, 'date', true);
    $time  = get_post_meta($post_id, 'time', true);

    $subject = "Room Reservation $status";
    $message = "Dear $name,\n\nYour reservation for $room on $date at $time has been $status.\n\nThank you.";

    wp_mail($email, $subject, $message); // User
    wp_mail(get_option('admin_email'), "Reservation $status: $name - $room", $message); // Admin
}

/* ==========================================================================
   8. ADMIN FILTERS & STYLING
   --------------------------------------------------------------------------
   Purpose:
   - Provide filtering by room via dropdown and tabbed UI.
========================================================================== */
add_action('restrict_manage_posts', function() {
    global $typenow;
    if ($typenow !== 'reservation_request') return;

    $selected = $_GET['room_filter'] ?? '';
    ?>
    <select name="room_filter">
        <option value="">All Rooms</option>
        <?php for ($i = 1; $i <= 6; $i++):
            $room = "Room $i"; ?>
            <option value="<?= esc_attr($room) ?>" <?= selected($selected, $room) ?>><?= esc_html($room) ?></option>
        <?php endfor; ?>
    </select>
    <?php
});

add_filter('parse_query', function($query) {
    global $pagenow;
    if ($pagenow === 'edit.php' && $query->get('post_type') === 'reservation_request') {
        $meta_query = [];

        if (!empty($_GET['room_filter'])) {
            $meta_query[] = [
                'key'   => 'room',
                'value' => sanitize_text_field($_GET['room_filter']),
            ];
        }

        if (!empty($_GET['time_filter'])) {
            $meta_query[] = [
                'key'   => 'time',
                'value' => sanitize_text_field($_GET['time_filter']),
            ];
        }

        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
});


add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'reservation_request') {
        wp_enqueue_style('rrs-admin-style', plugin_dir_url(__FILE__) . 'rrs-admin.css');
    }
});

add_filter('views_edit-reservation_request', function($views) {
    $room_selected = $_GET['room_filter'] ?? '';
    $time_selected = $_GET['time_filter'] ?? '';
    $base_url = admin_url('edit.php?post_type=reservation_request');

    echo '<div class="room-tabs" style="margin: 10px 0; display: flex; gap: 10px;">';
    echo '<a class="room-tab' . ($room_selected === '' ? ' active' : '') . '" href="' . esc_url($base_url) . '">All Rooms</a>';
    for ($i = 1; $i <= 6; $i++) {
        $room = "Room $i";
        $url = add_query_arg('room_filter', urlencode($room), $base_url);
        $active = ($room_selected === $room) ? ' active' : '';
        echo '<a class="room-tab' . $active . '" href="' . esc_url($url) . '">' . esc_html($room) . '</a>';
    }
    echo '</div>';

    if ($room_selected !== '') {
        echo '<div class="time-tabs" style="margin-bottom: 10px; display: flex; gap: 8px;">';
        echo '<a class="time-tab' . ($time_selected === '' ? ' active' : '') . '" href="' . esc_url(add_query_arg(['room_filter' => $room_selected], $base_url)) . '">All Times</a>';
        for ($i = 8; $i <= 16; $i++) {
            $time = "$i:00";
            $next_time = ($i + 1) . ":00";
            $label = date('g A', strtotime($time)) . ' - ' . date('g A', strtotime($next_time));
            $url = add_query_arg(['room_filter' => $room_selected, 'time_filter' => $time], $base_url);
            $active = ($time_selected === $time) ? ' active' : '';
            echo '<a class="time-tab' . $active . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
        }
        echo '</div>';
    }

    return $views;
});


add_filter('post_class', function($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'reservation_request') {
        $time = get_post_meta($post_id, 'time', true);
        if ($time) {
            $hour = (int) explode(':', $time)[0];
            $classes[] = 'time-' . str_pad($hour, 2, '0', STR_PAD_LEFT);
        }
    }
    return $classes;
}, 10, 3);
