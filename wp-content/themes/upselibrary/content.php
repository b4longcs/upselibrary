<section class="container">
    <article>
        <header>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p>Posted on <?php the_date(); ?> by <?php the_author(); ?></p>
        </header>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </article>
</section>
