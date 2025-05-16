<?php get_header(); ?>
<section class="category-header">
    <div class="category-content">
        <h1><?php single_cat_title(); ?></h1>
        <p><?php echo category_description(); ?></p>
    </div>
    
</section>
<section class="container">
    <div class="category-posts py-5">
        <span class="span-line my-3"></span> 

        <div class="category-wrapper">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="category-container">
                        <div class="category-thumbnail">
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                        </div>
                        <div class="category-content">
                            <h2 class="category-title mb-3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="category-excerpt">
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
                <p>No posts found in this category.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<section class="fp-search d-flex justify-content-center align-items-center flex-column">
    <p class="fp-search-p">Looking for something specific?</p>
    <div class="error-form">
        <form role="search" method="get" class="custom-search-form" action="<?php echo home_url('/'); ?>">
            <input type="search" name="s" class="custom-search-input" placeholder="Search anything..." required />
            <button type="submit" class="custom-search-button">Search</button>
        </form>
    </div>
</section>
<section class="spacer"></section>
<?php get_footer(); ?>
