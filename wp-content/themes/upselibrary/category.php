<?php get_header(); ?>

<?php
$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

$args = [
    'cat'            => get_queried_object_id(),
    'posts_per_page' => 12, // Always fetch 12
    'paged'          => $paged,
];

$query = new WP_Query($args);
?>

<section class="container category-archive">
    <h1 class="archive-title"><?php single_cat_title(); ?></h1>

    <?php if ($query->have_posts()) : ?>
        <div class="category-posts-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article <?php post_class('category-post-card'); ?>>
                    <a href="<?php the_permalink(); ?>" class="category-post-thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/placeholder.png'); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                    </a>
                    <div class="category-post-content">
                        <h2 class="category-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="category-post-excerpt">
                            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15, '...')); ?>
                        </p>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            <?php
            echo paginate_links([
                'total'     => $query->max_num_pages,
                'current'   => $paged,
                'mid_size'  => 1,
                'end_size'  => 1,
                'prev_text' => __('<i class="ri-arrow-left-long-line"></i>'),
                'next_text' => __('<i class="ri-arrow-right-long-line"></i>'),
                'type'      => 'list',
            ]);
            ?>
        </div>
    <?php else : ?>
        <p>No posts found in this category.</p>
    <?php endif; ?>
</section>

<section class="fp-search d-flex justify-content-center align-items-center flex-column mt-5">
    <p class="fp-search-p">Looking for something specific?</p>
    <div class="error-form">
        <form role="search" method="get" class="custom-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" class="custom-search-input" placeholder="Search anything..." required />
            <button type="submit" class="custom-search-button">Search</button>
        </form>
    </div>
</section>

<section class="spacer"></section>

<?php
wp_reset_postdata();
get_footer();
?>
