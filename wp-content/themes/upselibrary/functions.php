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
    // Styles (unchanged)
    wp_enqueue_style('remixicon', 'https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css', array(), '4.6.0', 'all');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap', array(), null, 'all');
    wp_enqueue_style('tex-gyre', 'https://fonts.cdnfonts.com/css/tex-gyre-adventor', array(), null, 'all');
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '4.5.0', 'all');
    wp_enqueue_style('carouselcss', get_template_directory_uri() . '/assets/css/carousel.css', array(), null, 'all');
    wp_enqueue_style('front-page', get_template_directory_uri() . '/assets/css/front-page.css', array(), null, 'all');
    wp_enqueue_style('custom-header-css', get_template_directory_uri() . '/assets/css/header.css', array(), null, 'all');
    wp_enqueue_style('custom-upselibrary', get_template_directory_uri() . '/assets/css/custom-upselibrary.css', array(), null, 'all');
    wp_enqueue_style('custom-pages-css', get_template_directory_uri() . '/assets/css/custom-pages.css', array(), null, 'all');  // Added custom-pages-css

    // Scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '4.5.0', true);
    wp_enqueue_script('nav-script', get_template_directory_uri() . '/assets/js/navigation.js', array('jquery'), '1.0', true);
    wp_enqueue_script('mainjs', get_template_directory_uri() . '/assets/js/main.js', array(), null, true);  // main.js now includes the tabs code

    // Other front-page-specific scripts
    if ( is_front_page() ) {
        wp_enqueue_script('carouseljs', get_template_directory_uri() . '/assets/js/carousel.js', array(), null, true);  // Added carouseljs
        wp_enqueue_script('text-animated-js', get_template_directory_uri() . '/assets/js/text-animated.js', array(), null, true);
    }

    // Conditionally load main.js for custom templates if needed
    $custom_templates = array('spaces.php', 'analytics.php', 'tools.php');
    if ( in_array(get_page_template_slug(), $custom_templates) ) {
        wp_enqueue_script('mainjs', get_template_directory_uri() . '/assets/js/main.js', array(), null, true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');

// Customizer settings
function custom_image_sizes() {
    add_image_size('sidebar-thumbnail', 280, 140, true); // Custom size for sidebar
}
add_action('after_setup_theme', 'custom_image_sizes');


//* breadcrumb */
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
