<?php
/**
 * Plugin Name: Room Reservation System
 * Description: Room Reservations
 * Version: 1.0
 * Author: Jonathan Tubo
 */

if (!defined('RRS_PLUGIN_URL')) {
    define('RRS_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('RRS_PLUGIN_PATH')) {
    define('RRS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

require_once RRS_PLUGIN_PATH . 'includes/ajax-handlers.php';
require_once RRS_PLUGIN_PATH . 'includes/shortcodes.php';
require_once RRS_PLUGIN_PATH . 'includes/assets.php'; 
require_once RRS_PLUGIN_PATH . 'admin/utils.php';
require_once RRS_PLUGIN_PATH . 'admin/ui.php';
require_once RRS_PLUGIN_PATH . 'admin/widget.php';

/* ==========================================================================
    CUSTOM POST TYPE
========================================================================== */
// Register custom post type for reservation requests
add_action('init', function () {
    // Only register if user can manage content
    if (!current_user_can('edit_pages')) {
        return;
    }

    register_post_type('reservation_request', [
        'labels' => [
            'name'               => __('Reservations'),
            'singular_name'      => __('Reservation'),
            'edit_item'          => __('Edit Reservation'),
            'view_item'          => __('View Reservation'),
            'search_items'       => __('Search Reservations'),
            'not_found'          => __('No reservations found'),
            'not_found_in_trash' => __('No reservations found in Trash.'),
            'all_items'          => __('All Reservations'),
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
    EMAIL NOTIFICATIONS (Under testing)
========================================================================== */
// Send approval/denial email to user and admin
function rrs_send_approval_email($post_id, $status) {
    if (!in_array($status, ['approved', 'denied'], true)) return;

    $meta = fn($k) => sanitize_text_field(get_post_meta($post_id, $k, true));
    $email = sanitize_email($meta('email'));
    if (!$email || !is_email($email)) {
        error_log("RRS Email Error: Invalid user email for post ID $post_id.");
        return;
    }

    $name = $meta('name');
    $room = $meta('room');
    $date = $meta('date');
    $time = $meta('time');

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
    $subject = "Room Reservation " . ucfirst($status);
    
    // Send emails
    $user_sent  = wp_mail($email, $subject, $msg, $headers);
    $admin_sent = wp_mail(get_option('admin_email'), "Reservation $status: $name - $room", $msg, $headers);

    // Logging
    if (!$user_sent)  error_log("RRS Email Error: Failed to send to user <$email> for post ID $post_id.");
    if (!$admin_sent) error_log("RRS Email Error: Failed to send to admin for post ID $post_id.");
}



