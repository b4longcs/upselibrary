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

<section class="container">
    <h1>Search Results for: <?php echo esc_html( get_search_query() ); ?></h1>

    <?php if ( have_posts() ) : ?>
        <div class="results-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="search-card">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="date"><?php echo get_the_date(); ?></p>
                    <div class="excerpt"><?php the_excerpt(); ?></div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p class="no-results">Sorry, no results matched your search.</p>
    <?php endif; ?>
</section>
