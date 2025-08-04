<?php
/*
Plugin Name: Gate System
Description: RFID/Barcode-based library gate system with user logging and carousel.
Version: 1.0
Author: Jonathan Tubo
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/cpt-users.php';
require_once plugin_dir_path(__FILE__) . 'includes/import-users.php';
require_once plugin_dir_path(__FILE__) . 'includes/export-logs.php';
require_once plugin_dir_path(__FILE__) . 'includes/carousel-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-log.php';


// Enqueue JS/CSS
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('gs-css', plugin_dir_url(__FILE__) . 'assets/css/gs-css.css');
    wp_enqueue_script('gs-js', plugin_dir_url(__FILE__) . 'assets/js/gs-js.js', ['jquery'], null, true);

    if (is_page_template('page-gate-scanner.php')) {
        wp_enqueue_style('gs-frontend-style', plugin_dir_url(__FILE__) . 'assets/css/scanner.css');
        wp_enqueue_script('gs-frontend-script', plugin_dir_url(__FILE__) . 'assets/js/scanner.js', [], null, true);

        wp_localize_script('gs-frontend-script', 'gs_frontend', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('gs_frontend_nonce'),
        ]);
    }
}); 

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table = $wpdb->prefix . 'gate_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        rfid VARCHAR(100),
        name VARCHAR(100),
        time DATETIME DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});


add_action('wp_ajax_gs_save_user_meta', function () {
    define('GS_AJAX_SAVE', true);

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gs_user_meta_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $post_id = intval($_POST['post_id']);
    $fields  = $_POST['fields'] ?? [];

    foreach ($fields as $key => $val) {
        update_post_meta($post_id, $key, sanitize_text_field($val));
    }

    wp_send_json_success();
});

add_action('wp_ajax_gs_scan_user', 'gs_handle_scan');
add_action('wp_ajax_nopriv_gs_scan_user', 'gs_handle_scan');

function gs_handle_scan() {
    check_ajax_referer('gs_frontend_nonce', 'nonce');

    $barcode = sanitize_text_field($_POST['barcode']);
    $args = [
        'post_type'  => 'gs_user',
        'meta_query' => [[
            'key'   => 'barcode',
            'value' => $barcode,
        ]],
        'posts_per_page' => 1
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post = $query->posts[0];

        $name    = get_post_meta($post->ID, 'name', true);
        $college = get_post_meta($post->ID, 'college', true);
        $course  = get_post_meta($post->ID, 'course', true);
        $type    = get_post_meta($post->ID, 'type', true);

        // Log to CSV
        $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $datePH = $dt->format('F d, Y');
        $timePH = $dt->format('g:i A');

        // Log to CSV
        $log = implode(",", [
            '"' . $name . '"',
            '"' . $college . '"',
            '"' . $course . '"',
            '"' . $type . '"',
            '"' . $barcode . '"',
            '"' . $datePH . '"',
            '"' . $timePH . '"'
        ]);


        $file = plugin_dir_path(__FILE__) . 'gate-logs.csv';
        file_put_contents($file, $log . PHP_EOL, FILE_APPEND);

        wp_send_json_success([
            'message' => "Welcome, $name!",
            'name'    => $name,
            'course'  => $course,
            'college' => $college,
        ]);

    } else {
        wp_send_json_error([
            'message' => 'User not found.',
        ]);
    }
}


