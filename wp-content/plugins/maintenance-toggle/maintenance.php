<?php
    $page_title = 'Site Under Maintenance';
    $page_description = 'Our site is currently undergoing maintenance. Please check back soon.';
    $canonical_url = home_url();
    $og_image = plugin_dir_url(__FILE__) . 'assets/images/og-image.jpg';
    $twitter_image = $og_image;
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
            <h1 class="mn-text text-center" >Our site is currently undergoing maintenance. </h1>
            <p class="text-center h5 mt-3">For the latest announcements and updates, please visit our Facebook page.</p>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/maintenance-img.png" alt="maintenance-img">
    </section>
</body>
</html>