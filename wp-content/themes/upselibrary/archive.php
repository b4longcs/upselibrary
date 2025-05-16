<?php get_header(); ?>

<section class="taxonomy-header">
    <div class="taxonomy-content">
        <h1>
            <?php 
            if (is_category()) {
                single_cat_title();
            } elseif (is_tag()) {
                single_tag_title();
            }
            ?>
        </h1>
        <p>
            <?php 
            if (is_category()) {
                echo category_description();
            } elseif (is_tag()) {
                echo tag_description();
            }
            ?>
        </p>
    </div>
</section>

<section class="container">
    <?php 
    // Define dynamic ID and hash anchor based on taxonomy type
    $taxonomy_id = is_category() ? 'category-posts' : (is_tag() ? 'tag-posts' : 'archive-posts');
    ?>
    <div id="<?php echo $taxonomy_id; ?>" class="taxonomy-posts py-5">
        <span class="span-line my-3"></span> 

        <div class="tags-wrapper">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="tags-container">
                        <div class="tags-thumbnail">
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                        </div>
                        <div class="tags-content">
                            <h2 class="tags-title mb-3">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="tags-excerpt">
                                <?php 
                                $excerpt = get_the_excerpt();
                                echo (strlen($excerpt) > 120) ? substr($excerpt, 0, 120) . '...' : $excerpt;
                                ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
                <div class="pagination">
                    <?php the_posts_pagination(); ?>
                </div>
            <?php else : ?>
                <p>No posts found in this archive.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
