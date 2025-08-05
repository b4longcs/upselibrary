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
<body>

    <div id="gate-scanner-app">
        <input type="text" id="scanner-input" placeholder="Tap or Scan ID..." autofocus>

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

        <div id="gate-carousel" class="carousel-container">
            <?php
            $carousel_images = get_option('gs_carousel_images', []);
            if (!empty($carousel_images)) {
                foreach ($carousel_images as $url) {
                    echo '<div class="carousel-slide"><img src="' . esc_url($url) . '" alt="Slide"></div>';
                }
            }
            ?>
        </div>
        <!-- Static Custom Sections -->
        <div class="gate-static-section">
            <h2>Welcome to UPSE Library</h2>
            <p>For assistance, please proceed to the counter.</p>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
