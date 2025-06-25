<?php
/**
 * Plugin Name: New Acquisition
 * Description: Manage book acquisition dates and images, displayed as accordion on New Acquisition Page.
 * Version: 1.0
 * Author: Jonathan Tubo
 */

if (!defined('ABSPATH')) exit;

// Add admin menu
add_action('admin_menu', function () {
    add_menu_page('New Acquisitions', 'New Acquisitions', 'edit_pages', 'new-acquisition', 'na_admin_page', 'dashicons-cover-image', 25);
});

// Enqueue admin scripts
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_new-acquisition') return;
    wp_enqueue_media();
    wp_enqueue_script('na-admin', plugin_dir_url(__FILE__) . 'nq-custom.js', ['jquery'], null, true);
    wp_enqueue_style('na-style-admin', plugin_dir_url(__FILE__) . 'nq-custom.css');
});

// Handle save
add_action('admin_post_na_save', function () {
    if (!current_user_can('edit_pages')) return;
    check_admin_referer('na_nonce');

    $acquisitions = [];

    if (!empty($_POST['na_entries'])) {
        foreach ($_POST['na_entries'] as $entry) {
            if (empty($entry['date'])) continue;
            $acquisitions[] = [
                'date' => sanitize_text_field($entry['date']),
                'images' => array_map('esc_url_raw', explode(',', $entry['images']))
            ];
        }
    }

    update_option('na_data', $acquisitions);
    wp_redirect(admin_url('admin.php?page=new-acquisition&saved=true'));
    exit;
});

// Render admin page
function na_admin_page() {
    $entries = get_option('na_data', []);
    ?>
    <div class="wrap">
        <div class="na-header">
            <h1>New Acquisitions</h1>
            <div class="na-header-buttons">
                <button type="button" class="button add-na-entry">+ Add Entry</button>
                <input type="submit" class="button button-primary" value="Save Changes">
            </div>
        </div>
        <?php if (isset($_GET['saved'])) echo '<div class="updated"><p>Saved successfully!</p></div>'; ?>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('na_nonce'); ?>
            <input type="hidden" name="action" value="na_save">
            <div id="na-entries">
                <?php foreach ($entries as $i => $entry): ?>
                    <div class="na-entry">
                        <input type="text" name="na_entries[<?php echo $i; ?>][date]" value="<?php echo esc_attr($entry['date']); ?>" placeholder="Acquisition Date" required>
                        <button class="button upload-na">Upload Images</button>
                        <button type="button" class="button-link delete-na-entry" style="color:red;">Delete Entry</button>
                        <input type="hidden" class="na-images" name="na_entries[<?php echo $i; ?>][images]" value="<?php echo esc_attr(implode(',', $entry['images'])); ?>">
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
            
        </form>
    </div>
    <?php
}
// Remove Comments Menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
}, 999); // Run late to ensure it gets removed


// Frontend CSS & JS
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('na-style', plugin_dir_url(__FILE__) . 'nq-custom.css');
    wp_enqueue_script('na-script', plugin_dir_url(__FILE__) . 'nq-custom.js', [], null, true);
});

// Shortcode output
add_shortcode('new_acquisition', function () {
    $entries = get_option('na_data', []);
    if (!$entries) return '';

    ob_start(); ?>
    <div class="na-accordion">
        <?php foreach ($entries as $entry): ?>
            <div class="na-item">
            <button class="na-toggle" aria-expanded="false">
                <?php echo esc_html($entry['date']); ?>
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

    <?php return ob_get_clean();
});
