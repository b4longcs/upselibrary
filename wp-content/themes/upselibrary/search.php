<?php get_header(); ?>

<section class="container">
    <h1>Search Results for: <?php echo get_search_query(); ?></h1>
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <div><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p>No results found.</p>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
