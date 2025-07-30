<?php
if (!defined('ABSPATH')) exit;

// Register AJAX actions
add_action('wp_ajax_submit_reservation', 'rrs_handle_reservation');
add_action('wp_ajax_nopriv_submit_reservation', 'rrs_handle_reservation');
add_action('wp_ajax_get_approved_reservations', 'rrs_get_approved_reservations');
add_action('wp_ajax_nopriv_get_approved_reservations', 'rrs_get_approved_reservations');

// Handle reservation form submission
function rrs_handle_reservation() {
    check_ajax_referer('rrs_nonce', 'nonce');
    $data = array_map('sanitize_text_field', $_POST);
    $email = sanitize_email($data['email'] ?? '');

    if (empty($data['name']) || empty($data['room']) || empty($data['date']) || empty($data['time'])) {
        wp_send_json_error(['message' => 'Sorry, Room already taken.']);
    }

    $conflict = get_posts([
        'post_type'  => 'reservation_request',
        'fields'     => 'ids',
        'meta_query' => [
            ['key' => 'room', 'value' => $data['room']],
            ['key' => 'date', 'value' => $data['date']],
            ['key' => 'time', 'value' => $data['time']],
            ['key' => 'status', 'value' => 'approved'],
        ]
    ]);

    if ($conflict) {
        wp_send_json_error(['message' => 'That time slot is already taken.']);
    }

    wp_insert_post([
        'post_type'   => 'reservation_request',
        'post_title'  => "{$data['name']} - {$data['room']}",
        'post_status' => 'publish',
        'meta_input'  => [
            'name'    => $data['name'],
            'college' => $data['college'] ?? '',
            'course'  => $data['course'] ?? '',
            'email'   => $email,
            'room'    => $data['room'],
            'date'    => $data['date'],
            'time'    => $data['time'],
            'status'  => 'pending'
        ]
    ]);

    delete_transient('rrs_events_' . md5($data['room']));
    wp_send_json_success(['message' => 'Reservation submitted and pending approval.']);
}

// Return approved reservations for calendar
function rrs_get_approved_reservations() {
    check_ajax_referer('rrs_nonce', 'nonce');
    $room = sanitize_text_field($_GET['room'] ?? '');

    if (!$room) wp_send_json_error(['message' => 'Invalid room selected.']);

    $cache_key = 'rrs_events_' . md5($room);
    $events = get_transient($cache_key);

    if ($events === false) {
        $posts = get_posts([
            'post_type' => 'reservation_request',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                ['key' => 'room', 'value' => $room],
                ['key' => 'status', 'value' => 'approved']
            ]
        ]);

        $events = array_values(array_filter(array_map(function ($post) {
            $date = get_post_meta($post->ID, 'date', true);
            $time = get_post_meta($post->ID, 'time', true);
            $name = get_post_meta($post->ID, 'name', true);
            if (!$date || !$time) return null;

            $start = date('Y-m-d\TH:i:s', strtotime("$date $time"));
            $end   = date('Y-m-d\TH:i:s', strtotime("$date $time +1 hour"));

            return ['title' => "Reserved by $name", 'start' => $start, 'end' => $end];
        }, $posts)));

        set_transient($cache_key, $events, 5 * MINUTE_IN_SECONDS);
    }

    wp_send_json_success($events);
}
