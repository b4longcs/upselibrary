<?php
/**
 * Template Name: Gate Scanner
 */
// 
defined('ABSPATH') || exit;

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
    <section class="gs-header-text d-flex justify-content-center align-items-center pt-2">
        <p>The UPSE Library is open from 8:00 AM to 7:00 PM (Monday to Friday).</p>
    </section>
    <section class="logo-div px-3 pt-3 d-flex justify-content-left align-items-center flex-row">
        <img class="gate-logo" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="UPSE logo">
        <span class="gs-span-line"></span>
        <h5>GATE SYSTEM</h5>
    </section>
    <section class="gate-scanner d-flex justify-content-center gap-3" id="gate-scanner">
        <section class="left-div d-flex flex-column align-items-left justify-content-start">
            <div class="gate-static-section my-5">
                <h1>Hello, <span> Welcome to UPSE Library!<span></h1>
            </div>
            <div class="left-div-container d-flex flex-column align-items-left justify-content-start p-3">
                <div class="scanner-div w-100 d-flex align-items-center flex-row my-1 gap-3 justify-content-evenly">
                    
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

                <div class="gs-notice d-flex justify-content-center align-items-start flex-row mt-2 gap-3">
                    <div class="gs-notice-left d-flex justify-content-center align-items-start flex-row h-100 p-4 gap-4">
                        <img class="notices-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/notices.png" alt="Important Notice">
                        <div class="notice-txt">
                            <p class="notice-red-text mb-4"><span class="gs-span-line-two"></span>IMPORTANT NOTICE:</p>
                            <h2 class="mt-2 mb-1">UPSE LIBRARY IS BACK!</h2>
                            <p class="notice-subtext my-3 pe-3">Starting <b>October 30, 2024,</b> the UPSE Library will be relocated to a temporary location in <b>Room 111, Encarnacion Hall | Monday to Friday from 8:00 AM to 7:00 PM</b></p>
                        </div>
                    </div>
                    <div class="gs-notice-right d-flex justify-content-center align-items-center flex-column h-100 pt-4">
                        <h5>Help us to serve you better!</h5>
                        <img class="survey-qr w-75 h-75" src="<?php echo get_template_directory_uri(); ?>/assets/images/qr-code.png" alt="Survey QR Code">
                    </div>
                    
                </div>

                <!-- MODAL -->
                <div class="scanner-overlay" id="scanner-overlay"></div>
                <div id="scanner-modal" class="hidden">
                    <div class="modal-content">
                        <section id="scanner-success-details" class="hidden">
                            <h2 class="success-text">Welcome to UPSE Library</h2>
                            <div class="success-container d-flex justify-content-center align-items-center flex-row">
                                <div class="success-left-div d-flex justify-content-center align-items-center">
                                    <img class="success-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/success-img.svg" alt="Success Image">
                                </div>
                                <div class="success-right-div">
                                    <div class="success-labels d-flex justify-content-center align-items-center flex-row gap-4">
                                        <label>Name:</label>
                                        <input class="success-label-text" type="text" id="scanner-name" readonly>
                                    </div>
                                    <div class="success-labels d-flex justify-content-center align-items-center flex-row gap-4">
                                        <label>Course:</label>
                                        <input class="success-label-text" type="text" id="scanner-course" readonly>
                                    </div>
                                    <div class="success-labels d-flex justify-content-center align-items-center flex-row gap-4">
                                        <label>College:</label>
                                        <input class="success-label-text" type="text" id="scanner-college" readonly>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <!-- Fail Message -->
                        <div id="scanner-fail-message" class="hidden">
                            <p>ACCESS DENIED!</p>
                            <p>User not found or unauthorized.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="right-div d-flex flex-column align-items-center justify-content-start">
            <div class="right-top">
                <h2 class="mb-4"><span class="gs-span-line-three"></span>Latest Updates</h2>
                <!-- Carousel -->
                <div id="gate-carousel" class="carousel-container">
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
            <div class="seal">
                <img class="seal-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/seal.png" alt="DPO/DPS Seal">
            </div>
        </section>
    </section>
    <section class="bottom-div d-flex flex-row align-items-center justify-content-center gap-5">
        <div class="bottom-div-one d-flex flex-row align-items-center justify-content-center links">
            <a href="#" class="gs-social-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/>
                </svg>
                
            </a>
            <a href="#" class="gs-social-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                </svg>
                
            </a>
            <p class="gs-social-text">@UPSELibrary</p>
        </div>
        <div class="bottom-div-two d-flex flex-row align-items-center justify-content-center links gap-1">
            <a href="#" class="gs-social-icon">
                <img class="web-svg" src="<?php echo get_template_directory_uri(); ?>/assets/images/web.svg" alt="Website">
            </a>
            <p class="gs-social-text">selib.upd.edu.ph</p>
        </div>
    </section>
    <?php wp_footer(); ?>
</body>
</html>
