<?php get_header(); ?>

<section class="tags-hero">
    <div class="container">
        <p class="tags-hero-text">Tags:</p>
        <h1 class="tags-hero-search mt-4">
            <span class="tag-name"><?php echo single_tag_title("", false); ?></span>
        </h1>
        <span class="tag-post-count">
            <?php $tag = get_queried_object(); 
                $post_count = $tag->count;
                echo '(' . $post_count . ' posts)';
            ?>
        </span>
    </div>
</section>
<section class="container">
    <span class="span-line"></span> 
    <?php if ( have_posts() ) : ?>
        <div class="tags-wrapper">
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="tags-container">
                    <div class="tags-thumbnail">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                    </div>
                    <div class="tags-content">
                        <h2 class="tags-title mb-4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="tags-excerpt">
                            <?php 
                            $excerpt = get_the_excerpt();
                            echo (strlen($excerpt) > 120) ? substr($excerpt, 0, 120) . '...' : $excerpt;
                            ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <p>No posts found for this tag.</p>
    <?php endif; ?>
</section>
<section class="fp-search d-flex justify-content-center align-items-center flex-column py-5 my-5">
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
