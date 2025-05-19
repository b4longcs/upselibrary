<?php echo '<!-- layout-os.php loaded -->'; ?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Online</p>
                <p class="hero-header-two">Subscription</p>
            </div>
            <div class="left-hero-subsubheader my-3 w-75">
                <p class="content-text">The UPSE Library is subscribed to four dataset providers namely, CEIC Data, EIKON with Datastream, Orbis Database, and GTAP Database. Access is exclusive to currently-enrolled UPSE students, faculty members, and staff.</p>
                <p class="content-text-custom bg-custom p-4">
                    <a class="note">NOTE:</a> You may open these electronic periodicals using <b>OpenAthens</b>. For further assistance, please email <a href="mailto:upselibrary.upd@up.edu.ph"><u class="email">upselibrary.upd@up.edu.ph</u></a>
                </p>
            </div>
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/onlinesubscription-img.svg" alt="global-hero-img">
        </div>
    </section>
    <section class="global-pages-content my-5 my-lg-3 my-md-2 my-sm-2">
        <section class="home-tabs" id="from-GetS">
            <div class="tabs" data-scrollreveal="enter bottom over 1s and move 40px after 0.2s">
                <div class="tabs-nav" role="tablist" aria-label="Content sections">
                    <div class="tabs-indicator"></div>
                    <button class="tab-button" role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1">
                        eJournals
                    </button>
                    <button class="tab-button" role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2">
                        ePeriodicals
                    </button>
                    <button class="tab-button" role="tab" aria-selected="false" aria-controls="panel-3" id="tab-3">
                        eBooks
                    </button>
                </div>

                <div class="tab-panel" role="tabpanel" id="panel-1" aria-labelledby="tab-1" aria-hidden="false">
                    <div class="online-sub-content">
                        <section class="bento-layout">
                            <div class="bento-grid">
                                <div class="div1">
                                    <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/taylor.png" alt="taylor"></a>
                                </div>
                                <div class="div2">
                                    <a href="#"><img src=""></a>
                                </div>
                                <div class="div3">
                                    <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/clarivate.png" alt=""></a>
                                </div>
                                <div class="div4">
                                    <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/sciencedirect.svg" alt=""></a>
                                </div>
                                <div class="div5">
                                    <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/wiley.png" alt=""></a>
                                </div>
                                <div class="div6">
                                    <a href="https://selib.upd.edu.ph/resources/online-subscription"></a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                
                <div class="tab-panel" role="tabpanel" id="panel-2" aria-labelledby="tab-2" aria-hidden="true">
                    <div class="online-sub-content">
                    <section class="bento-layout">
                            
                        </section>
                    </div>
                </div>
                <div class="tab-panel" role="tabpanel" id="panel-3" aria-labelledby="tab-3" aria-hidden="true">
                    <div class="online-sub-content">
                        
                    </div>
                </div>
            </div>

        </section>

    </section>
    <section class="spacer"></section>
</section>



