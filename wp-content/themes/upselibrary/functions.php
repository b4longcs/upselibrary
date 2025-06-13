<?php

// ====================================
// Theme Setup and Navigation
// ====================================
function my_custom_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('Primary Menu', 'upselibrary'),
        'mobile'  => __('Mobile Menu', 'upselibrary')
    ]);
}
add_action('after_setup_theme', 'my_custom_theme_setup');


// ====================================
// Enqueue Scripts and Styles
// ====================================
function enqueue_theme_assets() {
    enqueue_styles();
    enqueue_scripts();
    enqueue_conditional_scripts();
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');

function display_home_popup_html() {
    if (!is_front_page()) return;
    get_template_part('template-parts/popup');
}
add_action('wp_body_open', 'display_home_popup_html');


function enqueue_styles() {
    wp_enqueue_style('wp-block-library');
    wp_enqueue_style('remixicon', 'https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css', [], '4.6.0');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap');
    wp_enqueue_style('tex-gyre', 'https://fonts.cdnfonts.com/css/tex-gyre-adventor');
    
    $css_files = [
        'bootstrap'           => '/assets/css/bootstrap.min.css',
        'carouselcss'         => '/assets/css/carousel.css',
        'front-page'          => '/assets/css/front-page.css',
        'custom-header-css'   => '/assets/css/header.css',
        'custom-upselibrary'  => '/assets/css/custom-upselibrary.css',
        'custom-pages-css'    => '/assets/css/custom-pages.css',
        'popup-style'         => '/assets/css/popup.css',

    ];
    foreach ($css_files as $handle => $path) {
        wp_enqueue_style($handle, get_template_directory_uri() . $path);
    }
}

// Enqueue Scripts
function enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', ['jquery'], '4.5.0', true);
    wp_enqueue_script('nav-script', get_template_directory_uri() . '/assets/js/navigation.js', ['jquery'], '1.0', true);

    // Main JS Script (Avoid Duplicate Enqueue)
    wp_register_script('mainjs', get_template_directory_uri() . '/assets/js/main.js', [], null, true);
    wp_localize_script('mainjs', 'tags_ajax_obj', [
        'ajaxurl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tags_ajax_nonce'),
        'category' => get_queried_object()->slug ?? '',
    ]);
    wp_enqueue_script('mainjs');
}

// Conditional Script Enqueue based on Page Type
function enqueue_conditional_scripts() {
    if (is_front_page()) {
        wp_enqueue_script('carouseljs', get_template_directory_uri() . '/assets/js/carousel.js', [], null, true);
        wp_enqueue_script('text-animated-js', get_template_directory_uri() . '/assets/js/text-animated.js', [], null, true);
    }

    if (is_page_template('csa.php')) {
        wp_enqueue_script('filter-js', get_template_directory_uri() . '/assets/js/filter.js', ['jquery'], null, true);
        wp_localize_script('filter-js', 'ajaxurl', admin_url('admin-ajax.php'));
    }

    $custom_templates = ['spaces.php', 'analytics.php', 'tools.php'];
    if (in_array(get_page_template_slug(), $custom_templates)) {
        wp_enqueue_script('mainjs');
    }

    if (is_front_page()) {
        wp_enqueue_script('popup-js', get_template_directory_uri() . '/assets/js/popup.js', [], null, true);
    }

}


// ====================================
// Breadcrumbs Function
// ====================================
function custom_breadcrumb() {
    echo '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    echo '<li class="breadcrumb-item"><a href="' . home_url() . '">Home</a></li>';

    if (is_single()) {
        if ($category = get_the_category()) {
            echo '<li class="breadcrumb-item"><a href="' . get_category_link($category[0]->term_id) . '">' . esc_html($category[0]->name) . '</a></li>';
        }
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    } elseif (is_page()) {
        if ($parent_id = wp_get_post_parent_id(get_the_ID())) {
            $parent = get_post($parent_id);
            echo '<li class="breadcrumb-item"><a href="' . get_permalink($parent->ID) . '">' . esc_html($parent->post_title) . '</a></li>';
        }
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    } elseif (is_category()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . single_cat_title('', false) . '</li>';
    }

    echo '</ol></nav>';
}


// ====================================
// Filter Posts for CSA Page
// ====================================
function filter_posts() {
    $category        = sanitize_text_field($_POST['category']);
    $search          = sanitize_text_field($_POST['search']);
    $page            = intval($_POST['page']);
    $posts_per_page  = intval($_POST['posts_per_page']);

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
        's'              => $search,
    ];

    if ($category !== 'all') {
        $args['category_name'] = $category;
    }

    $query = new WP_Query($args);
    $posts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $excerpt = wp_trim_words(get_the_excerpt(), 20, '...');
            $posts[] = [
                'title'     => get_the_title(),
                'excerpt'   => $excerpt,
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'link'      => get_permalink()
            ];
        }
    }

    wp_send_json([
        'posts'       => $posts,
        'total_pages' => $query->max_num_pages
    ]);
}
add_action('wp_ajax_filter_posts', 'filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts');


// ====================================
// Load Tags via Ajax
// ====================================
function load_tags_ajax() {
    check_ajax_referer('tags_ajax_nonce', 'security');

    $paged           = intval($_POST['page'] ?? 1);
    $posts_per_page  = intval($_POST['posts_per_page'] ?? 10);
    $category_slug   = sanitize_text_field($_POST['category'] ?? '');

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    ];

    if (!empty($category_slug)) {
        $args['category_name'] = $category_slug;
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <article class="tags-container fade-in">
                <div class="tags-thumbnail">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                </div>
                <div class="tags-content">
                    <h2 class="tags-title mb-3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="tags-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        echo (strlen($excerpt) > 120) ? substr($excerpt, 0, 120) . '...' : $excerpt;
                        ?>
                    </div>
                </div>
            </article>
        <?php endwhile;
    else :
        echo '<p>No posts found in this category.</p>';
    endif;

    wp_reset_postdata();
    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_load_tags_ajax', 'load_tags_ajax');
add_action('wp_ajax_nopriv_load_tags_ajax', 'load_tags_ajax');


// ====================================
// Scroll-to-Section on Paginated Click
// ====================================
function custom_scroll_to_archive_script() {
    if (is_category() || is_tag()) {
        $hash_id = is_category() ? 'category-posts' : 'tag-posts';
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hash = window.location.hash;
            const targetId = '<?= esc_js($hash_id); ?>';

            if (hash === '#' + targetId) {
                const section = document.getElementById(targetId);
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            }

            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href') + '#<?= esc_js($hash_id); ?>';
                    window.location.href = href;
                });
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_scroll_to_archive_script');
