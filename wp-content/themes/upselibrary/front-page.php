<?php get_header();?>
<section class="hero-section">
    <div class="video-overlay"></div>
    <video class="hero-video" autoplay muted loop playsinline preload="none">
        <source src="<?php echo get_template_directory_uri(); ?>/assets/videos/hero-video.mp4" type="video/mp4">
    </video>
    <div class="hero-container">
        <div class="left-hero" data-scrollreveal="enter bottom over 1s and move 30px">
                <div class="left-content-one">
                    <p><span class="left-content-one-normal">Supplying</span></p>
                    <p><span class="left-content-one-highlight">Information</span></p>
                    <p><span class="left-content-one-normal">on Demand</span></p>
                </div>
                <div class="left-content-two">
                    <a href="#from-GetS" class="custom-button-one" >Get Started</a>
                    <a href="#" class="custom-button-two">Contact Us</a>
                </div>
        </div>
        <div class="right-hero" data-scrollreveal="enter bottom over 1s and move 30px after 0.3s">
            <div class="right-hero-container p-2 p-lg-5">
                <div class="carousel position-relative mx-auto overflow-hidden rounded">
                <div class="carousel-images d-flex"></div>

                <div class="carousel-controls position-absolute top-50 start-0 end-0 d-flex justify-content-between translate-middle-y">
                    <button class="prev btn btn-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 33px; height: 33px;">&#10094;
                    </button>

                    <button class="next btn btn-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 33px; height: 33px;">&#10095;
                    </button>
                </div>

                <div class="carousel-pagination-container d-flex justify-content-center mt-3">
                    <div class="carousel-pagination d-flex"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<main class="container">
    <section class="home-tabs" id="from-GetS">
        <div class="tabs" data-scrollreveal="enter bottom over 1s and move 40px after 0.2s">
            <div class="tabs-nav" role="tablist" aria-label="Content sections">
                <div class="tabs-indicator"></div>
                <button class="tab-button" role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1">
                    Tuklas
                </button>
                <button class="tab-button" role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2">
                    ETD-IR
                </button>
                <button class="tab-button" role="tab" aria-selected="false" aria-controls="panel-3" id="tab-3">
                    EBSCO
                </button>
            </div>

            <div class="tab-panel" role="tabpanel" id="panel-1" aria-labelledby="tab-1" aria-hidden="false">
                <form align="center" id="custom_search" class="search-container" action="https://tuklas.up.edu.ph/Search/Results" method="get" target="_blank">
                    <div class="form-group">
                        <input class="form-control" maxlength="60" size="60" name="lookfor" type="text" placeholder="Tuklas: Search anything">
                    </div>
                    <div class="form-group">
                        <button class="search" type="submit">SEARCH</button>
                    </div>
                </form>
            </div>
            
            <div class="tab-panel" role="tabpanel" id="panel-2" aria-labelledby="tab-2" aria-hidden="true">
                <form align="center" id="custom_search" class="search-container" action="https://selib.upd.edu.ph/etdir/search?" method="get" target="_blank">
                    <div class="form-group">
                        <input class="form-control" maxlength="100" size="60" name="query" type="text" placeholder="ETD: Search anything">
                    </div>
                    <div class="form-group">
                        <button class="search" type="submit">SEARCH</button>
                    </div>
                </form>
            </div>
            <div class="tab-panel" role="tabpanel" id="panel-3" aria-labelledby="tab-3" aria-hidden="true">
                <form align="center" id="custom_search" class="search-container" action="https://research.ebsco.com/c/fk6kem/search/results?" method="get" target="_blank">
                    <div class="form-group">
                        <input class="form-control" maxlength="60" size="60" name="q" type="text" placeholder="EBSCO: Search anything">
                    </div>
                    <div class="form-group">
                        <button class="search" type="submit">SEARCH</button>
                    </div>
                </form>
            </div>
        </div>

    </section>
    <section class="animation">
        <h1 class="animated-text" data-scrollreveal="enter bottom over 1s and move 20px after 0.4s"><span class="text-animated">Explore</span>UPSE Library</h1>
    </section>
    <section class="cas">
        <div class="cas-left" data-scrollreveal="enter bottom over 1s and move 30px">
            <p class="cas-category">Current Awareness</p>
            <h2>Keep up-to-date with the library's <span class="highlight-text">latest news</span></h2>
            <span class="line w-10"></span>
            <p class="cas-left-subheader">Stay updated with the latest news, announcements, and advisories from the UP School of Economics Library. Offering valuable resources and insights for every interested in economics.</p>
        </div>
        <div class="cas-right" data-scrollreveal="enter bottom over 1s and move 30px after 0.1s">
            <h4 class="cas-right-text">Recent Updates</h4>
            <div class="custom-recent-posts">
                <?php
                $recent_posts = new WP_Query([
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                ]);

                if ($recent_posts->have_posts()) :
                    while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                        <a href="<?php the_permalink(); ?>" class="cas-post-link">
                            <div class="cas-post-item">
                                <div class="cas-post-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                                <div class="cas-post-content">
                                    <h3 class="cas-post-title"><?php the_title(); ?></h3>
                                    <p class="cas-post-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 17, '...'); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <a class="global-button" href="#">See More</a>
        </div>
    </section>
    <span class="separator d-flex justify-content-center align-items-center" data-scrollreveal="enter bottom over 1s and move 30px after 0.4s">
        <svg width="80px" height="80px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier"> 
                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.31212 9L1 10.5094L4.77355 13.7897L6.28297 15.1018L7.59509 13.5924L9.13456 11.8214L11.3988 13.7897L12.9082 15.1018L14.2203 13.5924L15.7584 11.823L18.0209 13.7897L19.5303 15.1018L20.8424 13.5924L22.8106 11.3283L21.3012 10.0162L19.333 12.2803L15.5594 9L14.2473 10.5094L14.249 10.5109L12.7109 12.2803L8.93736 9L8.05395 10.0163L6.08567 12.2803L2.31212 9Z" fill="#bdbdbd">
                </path> 
            </g>
        </svg>
       
    </span>
    <section class="collection">
        <div class="collection-left" data-scrollreveal="enter bottom over 1s and move 30px">
            <img class="collection-left-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/collection-img.png" alt="collection-image">
        </div>
        <div class="collection-right" data-scrollreveal="enter bottom over 1s and move 30px after 0.3s">
        <p class="collection-category">Collection</p>
            <h2><span class="highlight-text">Explore</span> the vast collection of Economics literature</h2>
            <span class="line w-10"></span>
            <p class="collection-right-subheader">Immense yourself in a wealth of economics knowledge with our collection of literature and databases. Start your journey today and gain a deeper understanding the world of economics.</p>

            <a class="global-button" href="#">
                <span class="dots">About Us</span> 
            </a>

        </div>
    </section>
    <section class="about-us-ext">
        <div class="ext-left" data-scrollreveal="enter bottom over 1s and move 30px">
            <div class="top">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/globe-about.svg" alt="about-globe">
                <h3>Discover our services</h3>
            </div>
            <p>
                We provide a range of services to support your research needs, 
                including research assistance, study spaces, and access to online databases.
            </p>
            <a class="global-button" href="#">
                <span class="dots">View More</span> 
            </a>
        </div>

        <div class="ext-right" data-scrollreveal="enter bottom over 1s and move 30px after 0.3s">
            <div class="top">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/explore-about.svg" alt="about-explore">
                <h3>Explore our resources</h3>
            </div>
            <p>
                Our library offers a variety of resources to expand your knowledge 
                and enhance your learning experience with us.
            </p>
            <a class="global-button" href="#">
                <span class="dots">View More</span> 
            </a>
        </div>
    </section>
    <p class="databases-text">Unleashing the power of knowledge through our <span class="highlight-text">online databases</span></p>
    <section class="online-database">
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/tuklas.webp" alt="tuklas">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.15s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img href="#" src="<?php echo get_template_directory_uri(); ?>/assets/images/openathens.png" alt="openAthens">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.16s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img href="#" src="<?php echo get_template_directory_uri(); ?>/assets/images/eikon.webp" alt="eikon">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.17s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img href="#" src="<?php echo get_template_directory_uri(); ?>/assets/images/gsap.png" alt="gsap">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.18s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img class="ceic" src="<?php echo get_template_directory_uri(); ?>/assets/images/ceic.webp" alt="ceic">
            </a>
        </div>
    </section>
    <section class="new-acquisition" data-scrollreveal="enter bottom over 1s and move 30px">
        <div class="na-text" data-scrollreveal="enter bottom over 1s and move 30px after 0.1s">
            <p class="upper-text" data-scrollreveal="enter bottom over 1s and move 30px after 0.13s">New</p>
            <p class="bottom-text" data-scrollreveal="enter bottom over 1s and move 30px after 0.14s">Acquisition</p>
        </div>
        <div class="na-button" data-scrollreveal="enter bottom over 1s and move 30px after 0.15s">
            <a class="global-button" href="#" data-scrollreveal="enter bottom over 1s and move 30px after 0.16s" >
                <span class="dots">View All</span> 
            </a>
        </div>
    </section>
    <section class="cas" id="join-special" data-scrollreveal="enter bottom over 1s and move 30px">
        <div class="cas-left" id="join-special-content" data-scrollreveal="enter bottom over 1s and move 30px after 0.2s">
            <h2><span class="highlight-text">Join </span>our library community<span class="highlight-text"> today!</span></h2>
            <div class="social-media gap-4 pt-3">
                <!-- Facebook Icon -->
                <a href="" class="social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/>
                    </svg>
                </a>
                <!-- Twitter/X Icon -->
                <a href="" class="social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                </svg>
                </a>
                <!-- Instagram Icon -->
                <a href="" class="social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="collection-left" data-scrollreveal="enter bottom over 1s and move 30px after 0.4s">
            <img class="collection-left-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/groupchat.png" alt="collection-image">
        </div>
    </section>
</main>


<?php get_footer();?>