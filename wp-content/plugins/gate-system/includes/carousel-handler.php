<?php

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=gs_user', 'Carousel', 'Carousel', 'manage_options', 'gs-carousel', 'gs_carousel_page');
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'gs_user_page_gs-carousel') {
        wp_enqueue_media();
        wp_enqueue_style('gs-admin-css', plugin_dir_url(__FILE__) . '../assets/css/gs-css.css');
    }
});



function gs_carousel_page() {
    $images = get_option('gs_carousel_images', ['', '', '', '', '']);

    if (isset($_POST['save_carousel']) && is_array($_POST['carousel_image'])) {
        $filtered = array_map('esc_url_raw', $_POST['carousel_image']);
        update_option('gs_carousel_images', array_slice($filtered, 0, 5));
        echo '<div class="notice notice-success"><p>Carousel images saved!</p></div>';
        $images = $filtered;
    }

    ?>
    <div class="wrap">
        <h2>Carousel Images</h2>
        <form method="post" id="gs-carousel-form">
            <div class="gs-carousel-grid">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <div class="gs-carousel-slot" data-index="<?= $i ?>">
                        <input type="hidden" name="carousel_image[]" id="carousel_image_<?= $i ?>" value="<?= esc_attr($images[$i] ?? '') ?>">
                        <div class="gs-preview-container" id="preview_<?= $i ?>">
                            <?php if (!empty($images[$i])): ?>
                                <img src="<?= esc_url($images[$i]) ?>" class="gs-carousel-image">
                                <span class="gs-remove-image" data-index="<?= $i ?>">&times;</span>
                            <?php else: ?>
                                <div class="gs-placeholder">No image</div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button gs-select-image" data-index="<?= $i ?>">Select Image</button>
                    </div>
                <?php endfor; ?>
            </div>

            <p class="carousel-submit"><button type="submit" name="save_carousel" class="button button-primary">Save Carousel</button></p>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectButtons = document.querySelectorAll('.gs-select-image');

        selectButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const index = this.dataset.index;

                const frame = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });

                frame.on('select', function () {
                    const attachment = frame.state().get('selection').first().toJSON();
                    const input = document.getElementById('carousel_image_' + index);
                    const preview = document.getElementById('preview_' + index);

                    if (input) input.value = attachment.url;
                    if (preview) {
                        preview.innerHTML =
                            `<img src="${attachment.url}" class="gs-carousel-image">
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


