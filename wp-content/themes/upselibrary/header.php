<?php
if (is_page('up-school-of-economics')) {
    $page_title = "UP School of Economics";
    $page_description = "Welcome to the UP School of Economics Library, home to world-class education and research in economics. Learn more about our services and resources.";
    $canonical_url = "https://selib.upd.edu.ph/up-school-of-economics";
    $og_image = "https://selib.upd.edu.ph/images/logo.png";
    $twitter_image = "https://www.selib.upd.edu.phges/logo.png";
} else {
    $page_title = get_bloginfo('name'); 
    $page_description = get_bloginfo('description'); 
    $canonical_url = get_permalink();
    $og_image = get_theme_mod('default_og_image', 'https://www.selib.upd.edu.ph/ault-image.jpg'); 
    $twitter_image = get_theme_mod('default_twitter_image', 'https://www.selib.upd.edu.ph/ault-twitter-image.jpg'); 
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
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="site-preloader">
    <div class="loader-inner">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Loading">
    </div>
</div>
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
                    <a href="#" class="nav-menu" aria-haspopup="true">About</a>
                    <ul class="sub-menu">

                        <li><a href="/brief-history" class="mt-3">Brief History</a></li>
                        <li><a href="/vision-and-mission">Vision & Mission</a></li>
                        <li><a href="/general-policy">General Policy</a></li>
                        <li><a href="/library-guide">Library Guide</a></li>
                        <li><a href="/library-staff">Library Staff</a></li>
                        <li><a href="https://drive.google.com/file/d/1cgqN7T1mbFOvwj7B4i5OkXsBByCs8_bl/view">Citizen's Charter</a></li>
                        <li><a href="/faq">FAQ</a></li>
                        <li><a href="/contact-us" class="mb-3">Contact Us</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="nav-menu" aria-haspopup="true">Resources</a>
                    <ul class="sub-menu">
                        <li><a href="/print-collection" class="mt-3">Print Collection</a></li>
                        <li><a href="/online-subscription">Online Subscription</a></li>
                        <li><a href="/databases">Databases</a></li>
                        <li><a href="/datasets">Datasets</a></li>
                        <li><a href="/archives-collection" class="mb-3">Archives Collection</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="nav-menu" aria-haspopup="true">Services</a>
                    <ul class="sub-menu">
                        <li><a href="/circulation-service" class="mt-3">Circulation Service</a></li>
                        <li><a href="/reference-service">Reference Service</a></li>
                        <li><a href="/current-awareness-service">Current Awareness Service</a></li>
                        <li><a href="/thesis-and-dissertation-binding">Thesis and Dissertation Binding</a></li>
                        <li><a href="/interlibrary-loan">Interlibrary Loan</a></li>
                        <li><a href="/document-delivery-service" class="mb-3">Document Delivery Service</a></li>
                    </ul>
                </li>
                <li><a href="/spaces" class="me-2" aria-haspopup="true">Spaces</a></li>
                <li><a href="/visitor" class="me-4" aria-haspopup="true">Visitor</a></li>
            </ul>
        </nav>
    </div>
</header>
<button id="scrollToTopBtn" class="scrollToTopBtn"><i class="ri-arrow-up-line"></i></button>




