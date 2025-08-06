<?php
/**
 * Template Name: Gate Scanner (Full Screen)
 */
 
// Exit if accessed directly
defined('ABSPATH') || exit;

// Optional: check permissions or limit access
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Scanner</title>
    <?php wp_head(); ?>
</head>
<body class="gate-scanner-body">
    <section class="header-text">
        <p>The UPSE Library is open from 8:00 AM to 7:00 PM (Monday to Friday).</p>
    </section>
    <section class="logo-div px-3 pt-4">
        <img class="gate-logo" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="UPSE logo">
    </section>
    <section class="gate-scanner d-flex justify-content-center gap-3" id="gate-scanner">
        <section class="left-div d-flex flex-column align-items-left justify-content-start">
            
            <div class="gate-static-section my-5">
                <h1>Hello, <span> Welcome to UPSE Library!<span></h1>
            </div>
            <div class="scanner-div w-100 d-flex align-items-center flex-row my-1 gap-5 justify-content-evenly">
                
                <div class="input-ctr h-100">
                    <h2 class="input-ctr-text mb-4">Please Scan your ID</h2>
                    <div class="scanner-input-wrapper">
                        <input type="text" id="scanner-input" placeholder="Tap Here" autofocus>
                    </div>

                    <p class=" mt-3">For assistance, please proceed to the Reference Desk.</p>
                </div>
                <!-- <span class="gs-span-line"></span> -->
                <!-- Date & Time -->
                <div class="bento-box datetime-box h-100">
                    <h3 id="current-date"></h3>
                    <div id="current-time">
                        <span id="time-hm"></span><span id="time-s"></span><span id="time-ampm"></span>
                    </div>
                </div>
            </div>

            <div class="gs-notice d-flex justify-content-center align-items-start flex-row mt-5 gap-5">
                <div class="gs-notice-left d-flex justify-content-center align-items-start flex-row p-4 gap-4">
                    <img class="notices-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/notices.png" alt="Important Notice">
                    <div class="notice-txt">
                        <p class="mb-3"><b><i>Important Notice:</b></i></p>
                        <h2 class="my-4">UPSE Library is BACK!</h2>
                        <p class="my-4 pe-3">Starting <b>October 30, 2024,</b> the UPSE Library will be relocated to a temporary location in <b>Room 111, Encarnacion Hall | Monday to Friday from 8:00 AM to 7:00 PM</b></p>
                    </div>
                </div>
                <div class="gs-notice-right d-flex justify-content-center align-items-start flex-row h-100 p-2 gap-4">
                    321
                </div>
                
            </div>

            <!-- MODAL -->
            <div class="scanner-overlay" id="scanner-overlay"></div>
            <div id="scanner-modal" class="hidden">
                <div class="modal-content">
                    <p id="scanner-message">Scanning...</p>

                    <div id="scanner-success-details" class="hidden">
                        <label>Name</label>
                        <input type="text" id="scanner-name" readonly>

                        <label>Course</label>
                        <input type="text" id="scanner-course" readonly>

                        <label>College</label>
                        <input type="text" id="scanner-college" readonly>
                    </div>

                    <!-- Fail Message -->
                    <div id="scanner-fail-message" class="hidden">
                        <p>User not found or unauthorized.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="right-div">
            <div class="right-top">
                <h2 class="mb-4">Latest Updates</h2>
                <!-- Carousel -->
                <div id="gate-carousel" class="carousel-container">
                    
                    <!-- <?php
                    $carousel_images = get_option('gs_carousel_images', []);
                    if (!empty($carousel_images)) {
                        foreach ($carousel_images as $url) {
                            echo '<div class="carousel-slide"><img src="' . esc_url($url) . '" alt="Slide"></div>';
                        }
                    }
                    ?> -->

                    <?php
                    $carousel_images = get_option('gs_carousel_images', []);
                    if (!empty($carousel_images)) {
                        echo '<!-- Found images -->';
                        foreach ($carousel_images as $url) {
                            echo '<div class="carousel-slide"><img src="' . esc_url($url) . '" alt="Slide"></div>';
                        }
                    } else {
                        echo '<!-- No images found in gs_carousel_images -->';
                    }
                    ?>

                </div>
            </div>
        </section>
    </section>
    <?php wp_footer(); ?>
</body>
</html>
