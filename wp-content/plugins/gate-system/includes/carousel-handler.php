<?php

// 🖼️ Add 'Carousel' submenu under gs_user CPT
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=gs_user',
        'Carousel',
        'Carousel',
        'manage_options',
        'gs-carousel',
        'gs_carousel_page'
    );
});

// 📦 Enqueue media uploader and custom admin CSS for carousel page
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'gs_user_page_gs-carousel') {
        wp_enqueue_media();
        wp_enqueue_style('gs-admin-css', plugin_dir_url(__FILE__) . '../assets/css/gs-css.css');
    }
});

// 🎠 Carousel admin page with image uploader and preview
function gs_carousel_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Unauthorized access.', 'gate-system'));
    }

    $images = get_option('gs_carousel_images', ['', '', '', '', '']);

    if (
        isset($_POST['save_carousel'], $_POST['carousel_image'], $_POST['_wpnonce']) &&
        wp_verify_nonce($_POST['_wpnonce'], 'gs_save_carousel')
    ) {
        $raw = array_slice($_POST['carousel_image'], 0, 5);
        $filtered = array_map('esc_url_raw', $raw);
        update_option('gs_carousel_images', $filtered);
        $images = $filtered;

        echo '<div class="notice notice-success"><p>Carousel images saved!</p></div>';
    }

    ?>
    <div class="wrap">
        <h2>Carousel Images</h2>
        <form method="post" id="gs-carousel-form">
            <?php wp_nonce_field('gs_save_carousel'); ?>
            <div class="gs-carousel-grid">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <div class="gs-carousel-slot" data-index="<?= esc_attr($i) ?>">
                        <input type="hidden" name="carousel_image[]" id="carousel_image_<?= esc_attr($i) ?>" value="<?= esc_url($images[$i] ?? '') ?>">
                        <div class="gs-preview-container" id="preview_<?= esc_attr($i) ?>">
                            <?php if (!empty($images[$i])): ?>
                                <img src="<?= esc_url($images[$i]) ?>" class="gs-carousel-image">
                                <span class="gs-remove-image" data-index="<?= esc_attr($i) ?>">&times;</span>
                            <?php else: ?>
                                <div class="gs-placeholder">No image</div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button gs-select-image" data-index="<?= esc_attr($i) ?>">Select Image</button>
                    </div>
                <?php endfor; ?>
            </div>
            <p class="carousel-submit">
                <button type="submit" name="save_carousel" class="button button-primary">Save Carousel</button>
            </p>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.gs-select-image');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const index = button.dataset.index;
                const frame = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });
                frame.on('select', () => {
                    const attachment = frame.state().get('selection').first().toJSON();
                    const input = document.getElementById('carousel_image_' + index);
                    const preview = document.getElementById('preview_' + index);
                    if (input) input.value = attachment.url;
                    if (preview) {
                        preview.innerHTML = `
                            <img src="${attachment.url}" class="gs-carousel-image">
                            <span class="gs-remove-image" data-index="${index}">&times;</span>`;
                    }
                });
                frame.open();
            });
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('gs-remove-image')) {
                const index = e.target.dataset.index;
                const input = document.getElementById('carousel_image_' + index);
                const preview = document.getElementById('preview_' + index);
                if (input) input.value = '';
                if (preview) preview.innerHTML = '<div class="gs-placeholder">No image</div>';
            }
        });
    });
    </script>
    <?php
}
