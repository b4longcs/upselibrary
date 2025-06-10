<?php
/*
Template Name: Maintenance
*/
if (is_page('up-school-of-economics')) {
    $page_title = "UP School of Economics";
    $page_description = "Welcome to the UP School of Economics Library, home to world-class education and research in economics. Learn more about our services and resources.";
    $canonical_url = "https://upselibrary.local/up-school-of-economics";
    $og_image = "https://upselibrary.local/images/up-school-economics.jpg";
    $twitter_image = "https://www.upselibrary.localges/up-school-economics-twitter.jpg";
} else {
    $page_title = get_bloginfo('name'); 
    $page_description = get_bloginfo('description'); 
    $canonical_url = get_permalink();
    $og_image = get_theme_mod('default_og_image', 'https://www.upselibrary.localault-image.jpg'); 
    $twitter_image = get_theme_mod('default_twitter_image', 'https://www.upselibrary.localault-twitter-image.jpg'); 
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($page_title); ?></title>
    <meta name="description" content="<?php echo esc_attr($page_description); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo esc_url($canonical_url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($page_description); ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:url" content="<?php echo esc_url($canonical_url); ?>">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($twitter_image); ?>">
    <?php wp_head(); ?>
</head>
<body>
    <section class="mn-container px-4">
        <div class="mn-logo my-5">
            <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Logo" /></a>
        </div>
        <div class="mt-5">
            <h1 class="mn-text text-center" >The site is currently down for maintenance.</h1>
            <p class="text-center h5 mt-3">Sorry for the inconvenience caused. We're almost done.</p>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/maintenance-img.png" alt="maintenance-img">
    </section>
</body>
</html>