<?php
/**
 * Plugin Name: Room Reservation System
 * Description: Room Reservations
 * Version: 1.0
 * Author: Jonathan Tubo
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Plugin constants
if (!defined('RRS_PLUGIN_URL')) {
    define('RRS_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('RRS_PLUGIN_PATH')) {
    define('RRS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

// Include required files
require_once RRS_PLUGIN_PATH . 'includes/ajax-handlers.php';
require_once RRS_PLUGIN_PATH . 'includes/shortcodes.php';
require_once RRS_PLUGIN_PATH . 'includes/assets.php';
require_once RRS_PLUGIN_PATH . 'admin/utils.php';
require_once RRS_PLUGIN_PATH . 'admin/ui.php';
require_once RRS_PLUGIN_PATH . 'admin/widget.php';

/* ==========================================================================
   Custom Post Type: Reservation Request
========================================================================== */
add_action('init', function () {
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
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => ['title'],
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_admin_bar'   => false,
        'has_archive'         => false,
    ]);
});


function rrs_get_smtp_email() {

    // Get WP Mail SMTP plugin settings
    $smtp_options = get_option('wp_mail_smtp');

    // Check if SMTP from_email exists
    if (!empty($smtp_options['mail']['from_email']) && is_email($smtp_options['mail']['from_email'])) {
        return sanitize_email($smtp_options['mail']['from_email']);
    }

    // If SMTP email is not found, return false
    return false;
}
/* ==========================================================================
   Email Notifications
========================================================================== */

function rrs_send_status_email($post_id, $status) {
    if (get_post_type($post_id) !== 'reservation_request') return;
    if (!in_array($status, ['pending', 'approved', 'denied'], true)) return;

    $get_meta = fn($key) => sanitize_text_field(get_post_meta($post_id, $key, true));

    $email = sanitize_email($get_meta('email'));
    if (!$email || !is_email($email)) {
        error_log("RRS Email Error: Invalid user email for post ID $post_id.");
        return;
    }

    $name  = esc_html($get_meta('name'));
    $room  = esc_html($get_meta('room'));
    $date  = $get_meta('date');
    $time  = $get_meta('time');

    $start = strtotime("$date $time");
    if (!$start) {
        error_log("RRS Email Error: Invalid date/time for post ID $post_id.");
        return;
    }

    $date_fmt = date('F j, Y', $start);
    $time_fmt = date('g:i A', $start) . ' - ' . date('g:i A', strtotime('+1 hour', $start));
    $headers  = ['Content-Type: text/html; charset=UTF-8'];

    // Pending email (user)
    if ($status === 'pending') {
        $subject = "Room Reservation Request Received";
        $msg = "
        <html><body style='font-family:sans-serif'>
            <p>Dear <strong>$name</strong>,</p>
            <p>Your reservation request for Discussion<strong>$room</strong> on 
            <strong>$date_fmt</strong> at <strong>$time_fmt</strong> 
            has been successfully submitted and is currently 
            <strong>PENDING APPROVAL</strong>.</p>
            <p>You will receive another email once your request has been reviewed.</p>
            <p>Thank you for using the UPSE Library Room Reservation System.</p>

            <p><br>Regards,<br></p>

            <p><b>UPSE Library</b><br>
            <i>Room Reservation System</i></br></p>
            <p><b>FB/X:</b> @UPSELibrary<br>
            <b>Email:</b> upselibrary.upd@edu.ph<br>
            <b>Website:</b> selib.upd.edu.ph</p>
        </body></html>";

        wp_mail($email, $subject, $msg, $headers);
        return;
    }

    // Approved / Denied email (user)
    $subject = "Room Reservation " . ucfirst($status);
    $msg = "
    <html><body style='font-family:sans-serif'>
        <p>Dear <strong>$name</strong>,</p>
        <p>This is to formally inform you that your reservation for Discussion <strong>$room</strong> on 
        <strong>$date_fmt</strong> at <strong>$time_fmt</strong> has been 
        <strong>" . strtoupper($status) . "</strong>.</p>
        <p><br><strong>To ensure the proper use of the facility, please be guided by the following policies:</strong><br></p>

        <p>• Each reservation is limited to one (1) hour only(maximum of three (3) hours). Requests exceeding the one (1) hour standard reservation may be sent to <b>upselibrary.upd@up.edu.ph</b> and shall be subject to review and approval.<br>
        • Users are advised to check the reservation calendar prior to requesting a two (2) to three (3) hour booking to ensure that the desired time slot has not already been allocated to other users.<br>
        • Kindly arrive on time or at least ten (10) minutes before your scheduled reservation. Please note that the reservation is valid only within the approved time slot.<br>         
        • Present a valid UP ID to the Reference Librarian for verification upon arrival.<br>
        • Observe proper decorum and maintain minimal noise at all times to maintain a conducive environment for all library users.<br>        
        • Handle all furniture, equipment, and facilities with care. The reserving party shall be held accountable for any damage or loss incurred during the reservation period.<br>
        • Ensure that the room is left clean, orderly, and properly arranged after use.<br>
        • Food is strictly prohibited inside the room. Beverages are allowed only if contained in spill-proof containers.</p>

        <p>Should you need to modify or cancel your reservation, please do so in advance through the UP Library Reservation System. </p>

        <p>Thank you for your cooperation.</p>

        <p><br>Regards,<br></p>
        <p><b>UPSE Library</b><br>
        <i>Room Reservation System</i></br></p>
        <p><b>FB/X:</b> @UPSELibrary<br>
        <b>Email:</b> upselibrary.upd@edu.ph<br>
        <b>Website:</b> selib.upd.edu.ph</p>
    </body></html>";

    if (!wp_mail($email, $subject, $msg, $headers)) {
        error_log("RRS Email Error: Failed to send to user <$email> for post ID $post_id.");
    }

    // Admin notification (approved only)
    if ($status === 'approved') {

        $system_email = rrs_get_smtp_email();

        if ($system_email) {

            $admin_subject = "Reservation Approved: $name - $room";

            if (!wp_mail($system_email, $admin_subject, $msg, $headers)) {
                error_log("RRS Email Error: Failed to send SMTP notification for post ID $post_id.");
            }

        } else {
            error_log("RRS Email Error: WP Mail SMTP email not detected.");
        }
    }
}

// Handle reservation status changes
add_action('save_post_reservation_request', 'rrs_handle_status_change', 10, 3);
function rrs_handle_status_change($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    if (wp_is_post_autosave($post_id)) return;

    $new_status = sanitize_text_field(get_post_meta($post_id, 'reservation_status', true));
    if (!$new_status) return;

    $old_status = get_post_meta($post_id, '_rrs_last_email_status', true);
    if ($new_status !== $old_status) {
        rrs_send_status_email($post_id, $new_status);
        update_post_meta($post_id, '_rrs_last_email_status', $new_status);
    }
}
