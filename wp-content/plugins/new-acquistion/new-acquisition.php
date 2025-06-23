<?php
/**
 * Plugin Name: New Acquisition
 * Description: Adds a backend interface to manage book acquisition dates and images, displayed as accordion via shortcode.
 * Version: 1.1
 * Author: Jonathan Tubo
 */

if (!defined('ABSPATH')) {
    exit;
}

// Admin menu registration
add_action('admin_menu', function () {
    add_menu_page(
        __('New Acquisitions', 'new-acquisition'),
        __('New Acquisitions', 'new-acquisition'),
        'edit_pages',
        'new-acquisition',
        'na_admin_page',
        'dashicons-archive',
        26
    );
});

// Admin scripts and styles enqueue
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_new-acquisition') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'na-admin-js',
        plugin_dir_url(__FILE__) . 'nq-custom.js',
        ['jquery'],
        filemtime(plugin_dir_path(__FILE__) . 'nq-custom.js'),
        true
    );
    wp_enqueue_style(
        'na-admin-css',
        plugin_dir_url(__FILE__) . 'nq-custom.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'nq-custom.css')
    );
});

// Save handler
add_action('admin_post_na_save', function () {
    if (!current_user_can('edit_pages')) {
        wp_die(__('Insufficient permissions.', 'new-acquisition'));
    }

    check_admin_referer('na_nonce');

    $acquisitions = [];

    if (!empty($_POST['na_entries']) && is_array($_POST['na_entries'])) {
        foreach ($_POST['na_entries'] as $entry) {
            if (empty($entry['date'])) {
                continue;
            }
            $date = sanitize_text_field($entry['date']);
            $images = [];

            if (!empty($entry['images'])) {
                $raw_images = explode(',', $entry['images']);
                foreach ($raw_images as $img) {
                    $img_url = esc_url_raw(trim($img));
                    if ($img_url) {
                        $images[] = $img_url;
                    }
                }
            }

            $acquisitions[] = [
                'date'   => $date,
                'images' => $images,
            ];
        }
    }

    update_option('na_data', $acquisitions);

    wp_safe_redirect(add_query_arg('saved', 'true', admin_url('admin.php?page=new-acquisition')));
    exit;
});

// Admin page display
function na_admin_page() {
    $entries = get_option('na_data', []);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('New Acquisitions', 'new-acquisition'); ?></h1>
        <?php if (!empty($_GET['saved'])): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Saved successfully!', 'new-acquisition'); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('na_nonce'); ?>
            <input type="hidden" name="action" value="na_save">

            <div id="na-entries">
                <?php foreach ($entries as $i => $entry): ?>
                    <div class="na-entry">
                        <input
                            type="text"
                            name="na_entries[<?php echo $i; ?>][date]"
                            value="<?php echo esc_attr($entry['date']); ?>"
                            placeholder="<?php esc_attr_e('Acquisition Date', 'new-acquisition'); ?>"
                            required
                        />
                        <button class="button upload-na" type="button"><?php esc_html_e('Upload Images', 'new-acquisition'); ?></button>
                        <input
                            type="hidden"
                            class="na-images"
                            name="na_entries[<?php echo $i; ?>][images]"
                            value="<?php echo esc_attr(implode(',', $entry['images'])); ?>"
                        />
                        <div class="na-preview">
                            <?php foreach ($entry['images'] as $url): ?>
                                <div class="na-thumb" style="background-image: url('<?php echo esc_url($url); ?>')"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="button add-na-entry">+ <?php esc_html_e('Add Entry', 'new-acquisition'); ?></button>
            <p>
                <input type="submit" class="button button-primary" value="<?php esc_attr_e('Save', 'new-acquisition'); ?>">
            </p>
        </form>
    </div>
    <?php
}

// Frontend enqueue
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'na-style',
        plugin_dir_url(__FILE__) . 'nq-custom.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'nq-custom.css')
    );
    wp_enqueue_script(
        'na-script',
        plugin_dir_url(__FILE__) . 'nq-custom.js',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'nq-custom.js'),
        true
    );
});

// Shortcode to display acquisitions accordion
add_shortcode('new_acquisition', function () {
    $entries = get_option('na_data', []);

    if (empty($entries)) {
        return '';
    }

    ob_start(); ?>
    <div class="na-accordion">
        <?php foreach ($entries as $entry): ?>
            <div class="na-item">
                <button class="na-toggle" aria-expanded="false">
                    <?php echo esc_html($entry['date']); ?>
                    <i class="ri-arrow-down-s-line na-icon"></i>
                </button>
                <div class="na-content" hidden>
                    <div class="na-grid">
                        <?php foreach ($entry['images'] as $img): ?>
                            <div class="na-img" style="background-image: url('<?php echo esc_url($img); ?>')"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php

    return ob_get_clean();
});
