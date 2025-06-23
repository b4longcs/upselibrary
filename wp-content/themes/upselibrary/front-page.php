<?php get_header();?>
<section class="fp-hero-section">
    <div class="video-overlay"></div>
    <video class="fp-hero-video" autoplay muted loop playsinline preload="none">
        <source src="<?php echo get_template_directory_uri(); ?>/assets/videos/hero-video.mp4" type="video/mp4">
    </video>
    <div class="fp-hero-container" >
        <div class="fp-left-hero" data-scrollreveal="enter bottom over 1s and move 30px">
                <div class="left-content-one my-4">
                    <p><span class="left-content-one-normal">Supplying</span></p>
                    <p><span class="left-content-one-highlight">Information</span></p>
                    <p><span class="left-content-one-normal">on Demand</span></p>
                </div>
                <div class="fp-left-content-two mt-5">
                    <a href="#from-GetS" class="custom-button-one me-2" >Get Started</a>
                    <a href="#" class="custom-button-two me-2">Contact Us</a>
                </div>
        </div>
        <div class="fp-right-hero" data-scrollreveal="enter bottom over 1s and move 30px after 0.2s">
            <div class="right-hero-container m-3">
                <div class="carousel position-relative mx-auto overflow-hidden rounded">
                <div class="carousel-images d-flex"></div>

                <div class="carousel-controls position-absolute top-50 start-0 end-0 d-flex justify-content-between translate-middle-y">
                    <button class="prev btn btn-light lh-1 rounded-circle d-flex align-items-center justify-content-center"><i class="ri-arrow-left-s-line"></i>
                    </button>

                    <button class="next btn btn-light lh-1 rounded-circle d-flex align-items-center justify-content-center"><i class="ri-arrow-right-s-line"></i>
                </div>

                <div class="carousel-pagination-container d-flex justify-content-center mt-3">
                    <div class="carousel-pagination d-flex"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="animation m-lg-5 m-md-4 m-sm-4" id="from-GetS">
    <h1 class="animated-text"  data-scrollreveal="enter bottom over 1s and move 20px after 0.2s"><span class="text-animated">Explore</span>UPSE Library</h1>
</section>
<main class="container">
    <section class="home-tabs">
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
    <section class="cas">
        <div class="cas-left p-3" data-scrollreveal="enter bottom over 1s and move 30px">
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
            <a class="global-button" href="/current-awareness-service">See More</a>
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
            <img class="collection-left-img" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/collection-img.png" alt="collection-image">
        </div>
        <div class="collection-right" data-scrollreveal="enter bottom over 1s and move 30px after 0.3s">
        <p class="collection-category">Collection</p>
            <h2><span class="highlight-text">Explore</span> the vast collection of Economics literature</h2>
            <span class="line w-10"></span>
            <p class="collection-right-subheader">Immense yourself in a wealth of economics knowledge with our collection of literature and databases. Start your journey today and gain a deeper understanding the world of economics.</p>

            <a class="global-button" href="/about">
                <span class="dots">About Us</span> 
            </a>

        </div>
    </section>
    <section class="about-us-ext">
        <div class="ext-left" data-scrollreveal="enter bottom over 1s and move 30px">
            <div class="top">
                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/globe-about.svg" alt="about-globe">
                <h3>Discover our services</h3>
            </div>
            <p>
                We provide a range of services to support your research needs, 
                including research assistance, study spaces, and access to online databases.
            </p>
            <a class="global-button" href="/services">
                <span class="dots">View More</span> 
            </a>
        </div>

        <div class="ext-right" data-scrollreveal="enter bottom over 1s and move 30px after 0.3s">
            <div class="top">
                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/explore-about.svg" alt="about-explore">
                <h3>Explore our resources</h3>
            </div>
            <p>
                Our library offers a variety of resources to expand your knowledge 
                and enhance your learning experience with us.
            </p>
            <a class="global-button" href="/resources">
                <span class="dots">View More</span> 
            </a>
        </div>
    </section>
    <p class="databases-text">Unleashing the power of knowledge through our <span class="highlight-text">online databases</span></p>
    <section class="online-database">
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/tuklas.webp" alt="tuklas">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.15s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img loading="lazy" href="#" src="<?php echo get_template_directory_uri(); ?>/assets/images/openathens.png" alt="openAthens">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.16s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img loading="lazy" href="#" src="<?php echo get_template_directory_uri(); ?>/assets/images/eikon.webp" alt="eikon">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.17s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img loading="lazy" href="#" src="<?php echo get_template_directory_uri(); ?>/assets/images/gtap.png" alt="gtap">
            </a>
        </div>
        <div class="grid-item" data-scrollreveal="enter bottom over 0.8s and move 20px after 0.18s">
            <a href="#" target="_blank" rel="noopener noreferrer">
                <img loading="lazy" class="ceic" src="<?php echo get_template_directory_uri(); ?>/assets/images/ceic.webp" alt="ceic">
            </a>
        </div>
    </section>
    <section class="new-acquisition" data-scrollreveal="enter bottom over 1s and move 30px">
        <div class="na-text" data-scrollreveal="enter bottom over 1s and move 30px after 0.1s">
            <p class="upper-text" data-scrollreveal="enter bottom over 1s and move 30px after 0.13s">New</p>
            <p class="bottom-text" data-scrollreveal="enter bottom over 1s and move 30px after 0.14s">Acquisition</p>
        </div>
        <div class="na-button" data-scrollreveal="enter bottom over 1s and move 30px after 0.15s">
            <a class="global-button" href="/new-acquisition" data-scrollreveal="enter bottom over 1s and move 30px after 0.16s" >
                <span class="dots">View All</span> 
            </a>
        </div>
    </section>
    <section class="cas" id="join-special" data-scrollreveal="enter bottom over 1s and move 30px">
        <div class="cas-left" id="join-special-content" data-scrollreveal="enter bottom over 1s and move 30px after 0.2s">
            <h2><span class="highlight-text">Join </span>our library community<span class="highlight-text"> today!</span></h2>
            <div class="fp-social-media pt-4">
                <!-- Facebook Icon -->
                <a href="https://www.facebook.com/upselib" class="fp-social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/>
                    </svg>
                    
                </a>
                <p class="pe-4">/upselib</p>
                <!-- Twitter/X Icon -->
                <a href="https://x.com/upse_lib" class="fp-social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                    </svg>
                    
                </a>
                <p class="pe-4">@upse_lib</p>
                <!-- Instagram Icon -->
                <a href="https://www.instagram.com/upse_lib/" class="fp-social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M13.0281 2.00073C14.1535 2.00259 14.7238 2.00855 15.2166 2.02322L15.4107 2.02956C15.6349 2.03753 15.8561 2.04753 16.1228 2.06003C17.1869 2.1092 17.9128 2.27753 18.5503 2.52503C19.2094 2.7792 19.7661 3.12253 20.3219 3.67837C20.8769 4.2342 21.2203 4.79253 21.4753 5.45003C21.7219 6.0867 21.8903 6.81337 21.9403 7.87753C21.9522 8.1442 21.9618 8.3654 21.9697 8.58964L21.976 8.78373C21.9906 9.27647 21.9973 9.84686 21.9994 10.9723L22.0002 11.7179C22.0003 11.809 22.0003 11.903 22.0003 12L22.0002 12.2821L21.9996 13.0278C21.9977 14.1532 21.9918 14.7236 21.9771 15.2163L21.9707 15.4104C21.9628 15.6347 21.9528 15.8559 21.9403 16.1225C21.8911 17.1867 21.7219 17.9125 21.4753 18.55C21.2211 19.2092 20.8769 19.7659 20.3219 20.3217C19.7661 20.8767 19.2069 21.22 18.5503 21.475C17.9128 21.7217 17.1869 21.89 16.1228 21.94C15.8561 21.9519 15.6349 21.9616 15.4107 21.9694L15.2166 21.9757C14.7238 21.9904 14.1535 21.997 13.0281 21.9992L12.2824 22C12.1913 22 12.0973 22 12.0003 22L11.7182 22L10.9725 21.9993C9.8471 21.9975 9.27672 21.9915 8.78397 21.9768L8.58989 21.9705C8.36564 21.9625 8.14444 21.9525 7.87778 21.94C6.81361 21.8909 6.08861 21.7217 5.45028 21.475C4.79194 21.2209 4.23444 20.8767 3.67861 20.3217C3.12278 19.7659 2.78028 19.2067 2.52528 18.55C2.27778 17.9125 2.11028 17.1867 2.06028 16.1225C2.0484 15.8559 2.03871 15.6347 2.03086 15.4104L2.02457 15.2163C2.00994 14.7236 2.00327 14.1532 2.00111 13.0278L2.00098 10.9723C2.00284 9.84686 2.00879 9.27647 2.02346 8.78373L2.02981 8.58964C2.03778 8.3654 2.04778 8.1442 2.06028 7.87753C2.10944 6.81253 2.27778 6.08753 2.52528 5.45003C2.77944 4.7917 3.12278 4.2342 3.67861 3.67837C4.23444 3.12253 4.79278 2.78003 5.45028 2.52503C6.08778 2.27753 6.81278 2.11003 7.87778 2.06003C8.14444 2.04816 8.36564 2.03847 8.58989 2.03062L8.78397 2.02433C9.27672 2.00969 9.8471 2.00302 10.9725 2.00086L13.0281 2.00073ZM12.0003 7.00003C9.23738 7.00003 7.00028 9.23956 7.00028 12C7.00028 14.7629 9.23981 17 12.0003 17C14.7632 17 17.0003 14.7605 17.0003 12C17.0003 9.23713 14.7607 7.00003 12.0003 7.00003ZM12.0003 9.00003C13.6572 9.00003 15.0003 10.3427 15.0003 12C15.0003 13.6569 13.6576 15 12.0003 15C10.3434 15 9.00028 13.6574 9.00028 12C9.00028 10.3431 10.3429 9.00003 12.0003 9.00003ZM17.2503 5.50003C16.561 5.50003 16.0003 6.05994 16.0003 6.74918C16.0003 7.43843 16.5602 7.9992 17.2503 7.9992C17.9395 7.9992 18.5003 7.4393 18.5003 6.74918C18.5003 6.05994 17.9386 5.49917 17.2503 5.50003Z"></path></svg>
                    
                </a>
                <p class="pe-4">@upse_lib</p>
            </div>
        </div>
        <div class="collection-left" data-scrollreveal="enter bottom over 1s and move 30px after 0.4s">
            <img class="collection-left-img" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/groupchat.png" alt="collection-image">
        </div>
    </section>
    <section class="fp-search d-flex justify-content-center align-items-center flex-column py-5">
        <p class="fp-search-p">Looking for something specific?</p>
        <div class="error-form">
            <form role="search" method="get" class="custom-search-form" action="<?php echo home_url('/'); ?>">
                <input type="search" name="s" class="custom-search-input" placeholder="Search anything..." required />
                <button type="submit" class="custom-search-button">Search</button>
            </form>
        </div>
    </section>
</main>


<?php get_footer();?>