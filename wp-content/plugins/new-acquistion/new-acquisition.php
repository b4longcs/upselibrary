<?php
/**
 * Plugin Name: New Acquisition
 * Description: Manage book acquisition dates and images, displayed as an accordion on the New Acquisition Page.
 * Version: 1.0
 * Author: Jonathan Tubo
 */

if (!defined('ABSPATH')) exit; // Prevent direct access

/** ------------------------------------------
 * Admin Menu & Assets
 * ------------------------------------------ */

// Add custom admin menu
add_action('admin_menu', function () {
    add_menu_page(
        'New Acquisitions',           // Page title
        'New Acquisitions',           // Menu title
        'edit_pages',                 // Capability
        'new-acquisition',            // Menu slug
        'na_admin_page',              // Callback function
        'dashicons-buddicons-activity', // Icon
        25                            // Position
    );
});

// Enqueue admin scripts and styles
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_new-acquisition') return;
    wp_enqueue_media();
    wp_enqueue_script('na-admin', plugin_dir_url(__FILE__) . 'nq-custom.js', ['jquery'], null, true);
    wp_enqueue_style('na-style-admin', plugin_dir_url(__FILE__) . 'nq-custom.css');
});

/** ------------------------------------------
 * Admin Data Handling
 * ------------------------------------------ */

// Save form data
add_action('admin_post_na_save', function () {
    if (!current_user_can('edit_pages')) return;
    check_admin_referer('na_nonce');

    $acquisitions = [];

    if (!empty($_POST['na_entries'])) {
        foreach ($_POST['na_entries'] as $entry) {
            if (empty($entry['date'])) continue;

            $acquisitions[] = [
                'date'     => sanitize_text_field($entry['date']),
                'images'   => array_map('esc_url_raw', explode(',', $entry['images'])),
                'archived' => !empty($entry['archived'])
            ];
        }
    }

    update_option('na_data', $acquisitions);
    wp_redirect(admin_url('admin.php?page=new-acquisition&saved=true'));
    exit;
});

/** ------------------------------------------
 * Admin Page Rendering
 * ------------------------------------------ */

function na_admin_page() {
    $entries = get_option('na_data', []);
    ?>
    <div class="wrap">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('na_nonce'); ?>
            <input type="hidden" name="action" value="na_save">

            <div class="na-header">
                <?php if (isset($_GET['saved'])): ?>
                    <div class="notice notice-success is-dismissible na-save-notice">
                        <p><?php _e('Saved successfully!', 'new-acquisition'); ?></p>
                    </div>
                <?php endif; ?>
                <h1>New Acquisitions</h1>
            </div>

            <div class="na-header-buttons">
                <button type="button" class="button add-na-entry">+ Add Entry</button>
                <input type="submit" class="button button-primary" value="Save Changes">
            </div>

            <?php
            // Render active and archived entry sections
            na_render_entries_section($entries, false, 'Active Entries', 'na-active-entries');
            na_render_entries_section($entries, true, 'Archived Entries', 'na-archived-entries', 'opacity: 0.85;');
            ?>
        </form>
    </div>
    <?php
}

// Helper to render entries
function na_render_entries_section($entries, $archived = false, $header = '', $container_id = '', $style = '') {
    ?>
    <h2 class="admin-na-header"><?php echo esc_html($header); ?></h2>
    <div id="<?php echo esc_attr($container_id); ?>" style="<?php echo esc_attr($style); ?>">
        <?php foreach ($entries as $i => $entry): ?>
            <?php if (!empty($entry['archived']) !== $archived) continue; ?>
            <div class="na-entry">
                <div class="na-archive-toggle">
                    <input type="checkbox"
                           name="na_entries[<?php echo $i; ?>][archived]"
                           class="na-archive-checkbox"
                           <?php checked(!empty($entry['archived'])); ?>
                           style="display:none;">
                    <?php $is_archived = !empty($entry['archived']); ?>
                    <button type="button"
                            class="archive-btn <?php echo $is_archived ? 'restore-btn' : 'archive-only-btn'; ?>"
                            data-archived="<?php echo $is_archived ? '1' : '0'; ?>">
                        <?php echo $is_archived ? 'Restore' : 'Archive'; ?>
                    </button>
                </div>

                <input type="date"
                       name="na_entries[<?php echo $i; ?>][date]"
                       value="<?php echo esc_attr($entry['date']); ?>"
                       required>

                <button type="button" class="upload-na button">Upload Images</button>
                <button type="button" class="button-link delete-na-entry" style="color:red;">Delete Entry</button>

                <input type="hidden"
                       class="na-images"
                       name="na_entries[<?php echo $i; ?>][images]"
                       value="<?php echo esc_attr(implode(',', $entry['images'])); ?>">

                <div class="na-preview">
                    <?php foreach ($entry['images'] as $url): ?>
                        <div class="na-thumb" style="background-image: url('<?php echo esc_url($url); ?>')">
                            <span class="na-remove">&times;</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/** ------------------------------------------
 * UI/UX Cleanup
 * ------------------------------------------ */

// Remove default Comments menu (optional)
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
}, 999);

/** ------------------------------------------
 * Frontend Assets
 * ------------------------------------------ */

// Enqueue plugin styles and scripts for frontend
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('na-style', plugin_dir_url(__FILE__) . 'nq-custom.css');
    wp_enqueue_script('na-script', plugin_dir_url(__FILE__) . 'nq-custom.js', [], null, true);
});

/** ------------------------------------------
 * Shortcode for Frontend Display
 * ------------------------------------------ */

add_shortcode('new_acquisition', function () {
    $entries = get_option('na_data', []);
    if (!$entries) return '';

    // Separate entries
    $active_entries = array_filter($entries, fn($e) => empty($e['archived']));
    $archived_entries = array_filter($entries, fn($e) => !empty($e['archived']));

    // Group archived entries by year
    $archive_by_year = [];
    foreach ($archived_entries as $e) {
        $year = date('Y', strtotime($e['date']));
        $archive_by_year[$year][] = $e;
    }

    ob_start(); ?>

    <!-- Active Entries Accordion -->
    <div class="na-accordion">
        <?php foreach ($active_entries as $entry): ?>
            <div class="na-item">
                <button class="na-toggle" aria-expanded="false">
                    <?php
                    $date_obj = DateTime::createFromFormat('Y-m-d', $entry['date']);
                    echo esc_html($date_obj ? $date_obj->format('F j, Y') : $entry['date']);
                    ?>
                    <i class="ri-arrow-down-s-line na-icon"></i>
                </button>
                <div class="na-content">
                    <div class="na-grid">
                        <?php foreach ($entry['images'] as $img): ?>
                            <div class="na-img" style="background-image: url('<?php echo esc_url($img); ?>')"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Archived Summary -->
    <?php if (!empty($archive_by_year)): ?>
        <div class="na-archive-summary">
            <h3>Archived Acquisitions</h3>
            <ul>
                <?php foreach ($archive_by_year as $year => $items): ?>
                    <li><?php echo esc_html($year); ?> (<?php echo count($items); ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php return ob_get_clean();
});