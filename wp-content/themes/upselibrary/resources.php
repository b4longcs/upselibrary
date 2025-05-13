<?php 
/*
Template Name: Resources
*/
get_header(); ?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap my-5">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Resources</p>
                <span class="menu-category-page"></span>
            </div>
         
        </div>
        <div class="global-right-hero">
        </div>
    </section>
    <section class="resources-grid my-5 my-lg-3 my-md-2 my-sm-2">
        <div class="resources-item">
            <div class="resources-grid-wrapper">
                <div class="resources-grid-container">
                    <div class="resources-grid-content">
                        <i class="ri-book-2-fill"></i> 
                        <h3>Print Collection</h3>
                    </div>
                    <p class="rp-content">The UPSE Library’s print collection is comprised of more than 80,000 volumes of books classified as Filipiniana, General Circulation, General Reference, and Reserve…</p>
                </div>
                <a href="/print-collection" class="resources-grid-btn">See more</a>
            </div>
        </div>
        <div class="resources-item">
            <div class="resources-grid-wrapper">
                <div class="resources-grid-container">
                    <div class="resources-grid-content">
                        <i class="ri-tv-2-line"></i>
                        <h3>Online Subscription</h3>
                    </div>
                    <p class="rp-content">The UPSE Library is subscribed to four dataset providers namely, CEIC Data, EIKON with Datastream, Orbis Database, and GTAP Database…</p>
                </div>
                <a href="/online-subscription" class="resources-grid-btn">See more</a>
            </div>
        </div>
        <div class="resources-item">
            <div class="resources-grid-wrapper">
                <div class="resources-grid-container">
                    <div class="resources-grid-content">
                        <i class="ri-database-2-fill"></i>
                        <h3>Databases</h3>
                    </div>
                    <p class="rp-content">The UPSE Library is subscribed to four dataset providers namely, CEIC Data, EIKON with Datastream, and GTAP Database…</p>
                </div>
                <a href="/databases" class="resources-grid-btn">See more</a>
            </div>
        </div>
        <div class="resources-item">
            <div class="resources-grid-wrapper">
                <div class="resources-grid-container">
                    <div class="resources-grid-content">
                        <i class="ri-file-text-fill"></i>
                        <h3>Datasets</h3>
                    </div>
                    <p class="rp-content">To access the curated datasets from various agencies, accomplish the attached Data Use Agreement (DUA) …</p>
                </div>
                <a href="/datasets" class="resources-grid-btn">See more</a>
            </div>
        </div>
        <div class="resources-item">
            <div class="resources-grid-wrapper">
                <div class="resources-grid-container">
                    <div class="resources-grid-content">
                        <i class="ri-archive-fill"></i>
                        <h3>Archives Collection</h3>
                    </div>
                    <p class="rp-content">Work in progress</p>
                </div>
                <a href="/archives-collection" class="resources-grid-btn">See more</a>
            </div>
        </div>
    </section>
    <section class="spacer"></section>
</section>
<?php get_footer(); ?>
