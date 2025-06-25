<?php
/**
 * Plugin Name: Maintenance Mode Toggle
 * Description: Toggle a custom maintenance mode from Settings > General.
 * Version: 1.0
 * Author: Jonathan Tubo
 */

// Add toggle option to Settings > General
add_action('admin_init', function() {
    register_setting('general', 'custom_maintenance_mode', [
        'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);

    add_settings_field(
        'custom_maintenance_mode',
        'Maintenance Mode',
        function() {
            $value = get_option('custom_maintenance_mode');
            echo '<input type="checkbox" name="custom_maintenance_mode" value="1" ' . checked(1, $value, false) . '> Enable maintenance mode';
        },
        'general'
    );
});

// Serve maintenance.php for non-admins 
add_action('template_redirect', function() {
    if (
        get_option('custom_maintenance_mode') &&
        !current_user_can('manage_options') &&
        is_front_page()
    ) {
        include plugin_dir_path(__FILE__) . 'maintenance.php';
        exit;
    }
});
