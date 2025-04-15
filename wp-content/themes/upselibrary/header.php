<?php
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
<header id="main-header">
    <div class="header-text d-flex justify-content-center align-center pb-3 pt-3 text-white">
        <p>The UPSE Library is open from 8:00 AM to 7:00 PM (Monday to Friday).</p>
    </div>
    <nav class="container pt-3 pb-3">
        <div class="nav-container d-flex justify-content-between align-content-around">
            <div class="logo">
                <a href="<?php echo home_url(); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Logo">
                </a>
            </div>  
            <div class="nav-menu">
                <input type="checkbox" id="sidebar-active">
                <label for="sidebar-active" class="open-btn">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/menu.svg" alt="Logo">
                </label>
                <label id="overlay" for="sidebar-active"></label>
                <div class="links-container"> 
                    <label for="sidebar-active" class="close-btn">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/close.svg" alt="Logo">
                    </label>
                    <a class="gen-btn" href="<?php echo home_url(); ?>">
                        <label class="menu-label">home</label>
                    </a> 
                    <a class="gen-btn" href="#">
                        <label class="menu-label" data-menu="about" aria-expanded="false">about
                            <img class="menu-arrow" src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow-up.svg" alt="toggle arrow">
                        </label>
                        <div class="sub-menu" data-submenu="about">
                            <ul>
                                <li><p>test</p></li>
                                <li><p>test</p></li>
                                <li><p>test</p></li>
                            </ul>
                        </div>
                    </a>
                    <a class="gen-btn" href="#">
                        <label class="menu-label" data-menu="services" aria-expanded="false">services
                            <img class="menu-arrow" src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow-up.svg" alt="toggle arrow">
                        </label>
                        <div class="sub-menu" data-submenu="services">
                            <ul>
                                <li><p>test</p></li>
                                <li><p>test</p></li>
                                <li><p>test</p></li>
                            </ul>
                        </div>
                    </a>
                    <a class="gen-btn" href="#">
                        <label class="menu-label" data-menu="resources" aria-expanded="false">resources
                            <img class="menu-arrow" src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow-up.svg" alt="toggle arrow">
                        </label>
                        <div class="sub-menu" data-submenu="resources">
                            <ul>
                                <li><p>test</p></li>
                                <li><p>test</p></li>
                                <li><p>test</p></li>
                            </ul>
                        </div>
                    </a>
                    <a class="gen-btn" href="#">
                        <label class="menu-label" data-menu="spaces">spaces</label>
                    </a>
                    <a class="gen-btn" href="#">
                        <label class="menu-label" data-menu="visitor">visitor</label>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <style>
        @media(max-width:1024px){.links-container{flex-direction:column;align-items:flex-end;position:fixed;top:0;right:-100%;z-index:10;width:380px;background-image:url('<?php echo get_template_directory_uri(); ?>/assets/images/bg-bg.jpg');background-position:right bottom;background-size:cover;background-repeat:no-repeat;box-shadow:-5px 0 5px rgba(0,0,0,0.05);transition:0.45s ease-out;}}
    </style>
</header>




