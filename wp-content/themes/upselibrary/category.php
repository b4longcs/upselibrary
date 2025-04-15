<?php get_header(); ?>

<div class="category-header">
    <h1><?php single_cat_title(); ?></h1>
    <p><?php echo category_description(); ?></p>
</div>

<div class="category-posts">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article class="post">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
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
        <p>No posts found in this category.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
