<?php get_header(); ?>

<section class="container">
    <h1>Oops! Page not found.</h1>
    <p>Sorry, but the page you were looking for doesn't exist. You can try searching below or go back to the homepage.</p>

    <?php get_search_form(); ?>

    <a href="<?php echo home_url(); ?>">Return to homepage</a>
</section>

<?php get_footer(); ?>
