<?php get_header(); ?>

<div class="archive-header">
    <h1>
        <?php
        if (is_category()) :
            single_cat_title(); // Displays the title of the category
        elseif (is_tag()) :
            single_tag_title(); // Displays the title of the tag
        elseif (is_author()) :
            the_author(); // Displays the author's name
        elseif (is_year()) :
            echo get_the_date('Y'); // Displays the year
        elseif (is_month()) :
            echo get_the_date('F Y'); // Displays the month and year
        elseif (is_day()) :
            echo get_the_date('F j, Y'); // Displays the specific day
        else :
            _e('Archives'); // Default title for general archives
        endif;
        ?>
    </h1>

    <?php
    // Optional: Display the archive description if available
    if (is_category() || is_tag()) :
        echo category_description();
    endif;
    ?>
</div>

<div class="archive-posts">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article class="post">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="post-meta">
                    <span>Posted on <?php the_time('F j, Y'); ?></span>
                </div>
                <div class="post-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            </article>
        <?php endwhile; ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php the_posts_pagination(); ?>
        </div>

    <?php else : ?>
        <p>No posts found for this archive.</p>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
