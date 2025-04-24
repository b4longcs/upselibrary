<?php get_header(); ?>
<section class="container">
    <main>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article>
                <h1><?php the_title(); ?></h1>
                <p><?php the_content(); ?></p>
            </article>
        <?php endwhile; endif; ?>
    </main>
</section>


<?php get_footer(); ?>
