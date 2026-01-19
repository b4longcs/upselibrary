<?php echo '<!-- layout-db.php loaded -->'; ?>
<section class="db-container mt-5">
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap mt-5">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Databases</p>
                <!-- <p class="hero-header-two">Subscription</p> -->
            </div>
            <div class="left-hero-subsubheader my-3 w-75">
                <p class="content-text my-3">The UPSE Library is subscribed to four dataset providers namely, <b>CEIC Data, EIKON with Datastream, and GTAP Database.</b> Access is exclusive to currently-enrolled UPSE students, faculty members, and staff. </p>
                <p class="content-text-custom bg-custom p-4">
                    <a class="note">NOTE:</a> You may open these electronic periodicals using <b>OpenAthens</b>. For further assistance, please email <a href="mailto:upselibrary.upd@up.edu.ph"><u class="email">upselibrary.upd@up.edu.ph</u></a>
                </p>
            </div>
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/databases-img.svg" alt="global-hero-img">
        </div>
    </section>
    <section class="global-pages-content my-5 my-lg-3 my-md-2 my-sm-2">
        <div class="gp-databases" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <a href="https://www.ceicdata.com/en"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/ceic.webp" alt="CEIC Data"></a>
            <a href="https://www.gtap.agecon.purdue.edu/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/eikon.webp" alt="EIKON with Datastream"></a>
            <a href="https://eikon.refinitiv.com/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/gtap.png" alt="GTAP Database"></a>
        </div>
    </section>
    <section class="spacer"></section>
</section>
