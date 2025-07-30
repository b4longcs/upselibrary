<?php
// Frontend assets for "room-reservation" page
add_action('wp_enqueue_scripts', function () {
    if (!is_page('room-reservation') || !defined('RRS_PLUGIN_URL') || !defined('RRS_PLUGIN_PATH')) return;

    $js = RRS_PLUGIN_PATH . 'assets/js/rrs-script.js';
    $css = RRS_PLUGIN_PATH . 'assets/css/room-reservation-system.css';

    if (file_exists($js)) {
        wp_enqueue_script('rrs-script', esc_url(RRS_PLUGIN_URL . 'assets/js/rrs-script.js'), ['jquery'], filemtime($js), true);
        wp_localize_script('rrs-script', 'rrs_ajax', [
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'nonce'    => wp_create_nonce('rrs_nonce')
        ]);
    }

    if (file_exists($css)) {
        wp_enqueue_style('rrs-css', esc_url(RRS_PLUGIN_URL . 'assets/css/room-reservation-system.css'), [], filemtime($css));
    }

    wp_enqueue_script('fullcalendar', esc_url('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'), [], '6.1.9', true);
    wp_enqueue_style('fullcalendar-style', esc_url('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css'));
    wp_enqueue_script('flatpickr-js', esc_url('https://cdn.jsdelivr.net/npm/flatpickr'), [], null, true);
    wp_enqueue_style('flatpickr-css', esc_url('https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css'));
});

// Admin assets for reservation list
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'edit.php' || sanitize_key($_GET['post_type'] ?? '') !== 'reservation_request') return;

    wp_enqueue_style('flatpickr-css', esc_url('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'));
    wp_enqueue_script('flatpickr-js', esc_url('https://cdn.jsdelivr.net/npm/flatpickr'), [], null, true);
    wp_add_inline_script('flatpickr-js', "document.addEventListener('DOMContentLoaded',()=>flatpickr('#rrs-date-filter',{dateFormat:'Y-m-d',allowInput:true}));");

    if (defined('RRS_PLUGIN_URL')) {
        wp_enqueue_style('rrs-admin-style', esc_url(RRS_PLUGIN_URL . 'assets/css/rrs-admin.css'));
    }
});
