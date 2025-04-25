<?php get_header(); ?>

<section class="container">
    <div class="error-container">
        <div class="hero-error">
            <a href="<?php echo home_url(); ?>" class="home-link">Return to homepage</a>
            <p class="error-text">Oops! Page not found.</p>
            <img class="error-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/404-error.svg">
            <p class="error-text">Sorry, the page you're looking for doesn't exist. Try searching or go back to the homepage.</p>
            <div class="error-form">
                <form role="search" method="get" class="custom-search-form" action="<?php echo home_url('/'); ?>">
                    <input type="search" name="s" class="custom-search-input" placeholder="Search anything..." required />
                    <button type="submit" class="custom-search-button">Search</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
