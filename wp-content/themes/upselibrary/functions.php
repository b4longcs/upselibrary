<?php

// Theme support and navigation setup
function my_custom_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'upselibrary'),
        'mobile' => __('Mobile Menu', 'upselibrary')
    ));
}
add_action('after_setup_theme', 'my_custom_theme_setup');

// Enqueue scripts and styles
function enqueue_theme_assets() {
    $template_uri = get_template_directory_uri();

    // 🔹 Register styles
    wp_register_style('remixicon', 'https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css', array(), '4.6.0', 'all');
    wp_register_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap', array(), null, 'all');
    wp_register_style('tex-gyre', 'https://fonts.cdnfonts.com/css/tex-gyre-adventor', array(), null, 'all');
    wp_register_style('bootstrap', $template_uri . '/assets/css/bootstrap.min.css', array(), '4.5.0', 'all');
    wp_register_style('custom-header-css', $template_uri . '/assets/css/header.css', array(), null, 'all');
    wp_register_style('custom-pages-css', $template_uri . '/assets/css/custom-pages.css', array(), null, 'all');
    wp_register_style('custom-upselibrary', $template_uri . '/assets/css/custom-upselibrary.css', array(), null, 'all');
    wp_register_style('carouselcss', $template_uri . '/assets/css/carousel.css', array(), null, 'all');
    wp_register_style('front-page', $template_uri . '/assets/css/front-page.css', array(), null, 'all');

    // 🔹 Register scripts
    wp_register_script('bootstrap', $template_uri . '/assets/js/bootstrap.min.js', array('jquery'), '4.5.0', true);
    wp_register_script('nav-script', $template_uri . '/assets/js/navigation.js', array('jquery'), '1.0', true);
    wp_register_script('mainjs', $template_uri . '/assets/js/main.js', array(), null, true);
    wp_register_script('carouseljs', $template_uri . '/assets/js/carousel.js', array(), null, true);

    // ✅ Enqueue global styles
    wp_enqueue_style('remixicon');
    wp_enqueue_style('google-fonts');
    wp_enqueue_style('tex-gyre');
    wp_enqueue_style('bootstrap');
    wp_enqueue_style('custom-header-css');
    wp_enqueue_style('custom-pages-css');
    wp_enqueue_style('custom-upselibrary');

    // ✅ Enqueue global scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap');
    wp_enqueue_script('nav-script');
    wp_enqueue_script('mainjs');

    // ✅ Load front-page styles and scripts globally
    wp_enqueue_style('carouselcss');
    wp_enqueue_style('front-page');
    wp_enqueue_script('carouseljs');
}

add_action('wp_enqueue_scripts', 'enqueue_theme_assets');

// Customizer settings
function custom_image_sizes() {
    add_image_size('sidebar-thumbnail', 280, 140, true); // Custom size for sidebar
}
add_action('after_setup_theme', 'custom_image_sizes');

// Breadcrumbs function
function custom_breadcrumb() {
    echo '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

    echo '<li class="breadcrumb-item"><a href="' . home_url() . '">Home</a></li>';

    if (is_single()) {
        $category = get_the_category();
        if ($category) {
            $category = $category[0];
            echo '<li class="breadcrumb-item"><a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></li>';
        }
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    } elseif (is_page()) {
        $parent_id = wp_get_post_parent_id(get_the_ID());
        if ($parent_id) {
            $parent = get_post($parent_id);
            echo '<li class="breadcrumb-item"><a href="' . get_permalink($parent->ID) . '">' . $parent->post_title . '</a></li>';
        }
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    } elseif (is_category()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . single_cat_title('', false) . '</li>';
    }

    echo '</ol></nav>';
}

?>
