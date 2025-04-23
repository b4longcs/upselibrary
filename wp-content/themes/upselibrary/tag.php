<?php get_header(); ?>

<section class="container">
    <h1><?php single_tag_title(); ?></h1>

    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p>No posts found for this tag.</p>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
