<?php echo '<!-- layout-ds.php loaded -->'; ?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Datasets</p>
                <!-- <p class="hero-header-two">Subscription</p> -->
            </div>
            <div class="left-hero-subsubheader my-3 w-75">
                <p class="content-text my-3">To access the curated datasets from various agencies, accomplish the attached <b>Data Use Agreement (DUA)</b> form and send it to <b><u>upselibrary.upd@up.edu.ph</u></b>. The signature of the faculty/thesis adviser is no longer required.</p>
                <a href="https://docs.google.com/document/d/17EJyNvBCI_faK9mXzewZVyL1BgWRZHtm/edit?tab=t.0" class="dua">Download DUA Here!</a>
            </div>
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/datasets-img.svg" alt="global-hero-img">
        </div>
    </section>
    <section class="global-pages-content my-5 my-lg-3 my-md-2 my-sm-2">
        <div class="gp-databases">
            <a href="https://psada.psa.gov.ph/home"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/psa.png" alt=""></a>
            <a href="https://comtradeplus.un.org/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/comtradelogo.png" alt=""></a>
            <a href="https://www.ceicdata.com/en"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/ceic.webp" alt=""></a>
            <a href="https://www.gtap.agecon.purdue.edu/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/eikon.webp" alt=""></a>
            <a href="https://eikon.refinitiv.com/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/gtap.png" alt=""></a>
        </div>
    </section>
    <section class="spacer"></section>
</section>
