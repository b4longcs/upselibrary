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
<div id="progress-bar"></div> 
<header class="header-sticky">
       
       
    <div class="header-text">
        <p>The UPSE Library is open from 8:00 AM to 7:00 PM (Monday to Friday).</p>
    </div>
    <div class="container">
        <nav class="nav-container">
            <div class="logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Logo" /></a>
            </div>
            <div class="hamburger" id="hamburger">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4H21V6H3V4ZM9 11H21V13H9V11ZM3 18H21V20H3V18Z"></path></svg>
            </div>
            <div class="overlay" id="overlay"></div>
            <ul class="nav-links" id="navLinks">
                <li class="close-btn-mobile" id="close-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10.5859 12L2.79297 4.20706L4.20718 2.79285L12.0001 10.5857L19.793 2.79285L21.2072 4.20706L13.4143 12L21.2072 19.7928L19.793 21.2071L12.0001 13.4142L4.20718 21.2071L2.79297 19.7928L10.5859 12Z"></path></svg>
                </li>
                <li>
                <a href="#" class="nav-menu">About</a>
                <ul class="sub-menu">
                    <li><a href="#">Brief History</a></li>
                    <li><a href="#">Vision & Mission</a></li>
                    <li><a href="#">General Policy</a></li>
                    <li><a href="#">Library Guide</a></li>
                    <li><a href="#">Library Staff</a></li>
                    <li><a href="#">Citizen's Charter</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
                </li>
                <li>
                <a href="#" class="nav-menu">Resources</a>
                <ul class="sub-menu">
                    <li><a href="/print-collection">Print Collection</a></li>
                    <li><a href="/online-subscription">Online Subscription</a></li>
                    <li><a href="/databases">Databases</a></li>
                    <li><a href="/datasets">Datasets</a></li>
                    <li><a href="/archives-collection">Archives Collection</a></li>
                </ul>
                </li>
                <li>
                <a href="#" class="nav-menu">Services</a>
                <ul class="sub-menu">
                    <li><a href="#">Circulation Service</a></li>
                    <li><a href="#">Reference Service</a></li>
                    <li><a href="#">Current Awareness Service</a></li>
                    <li><a href="#">Thesis and Dissertation Binding</a></li>
                    <li><a href="#">Interlibrary Loan</a></li>
                    <li><a href="#">Document Delivery Service</a></li>
                </ul>
                </li>
                <li><a href="#" class="nav-menu">Spaces</a></li>
                <li><a href="#" class="nav-menu">Visitor</a></li>
            </ul>
        </nav>
    </div>
</header>




