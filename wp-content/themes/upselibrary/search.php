<?php get_header(); ?>

<section class="searchpage-results container">
    <div class="searchpage-hero">
        <h1 class="searchpage-title">Search Results for <span class="search-query">"<?php echo esc_html(get_search_query()); ?>"</span>
        </h1>

        <p class="searchpage-count">
            <?php
            $total_results = $wp_query->found_posts;
            echo esc_html($total_results) . ' result' . ($total_results > 1 ? 's' : '') . ' found.';
            ?>
        </p>
    </div>
    <div class="span-line"></div>
    <?php
    if (have_posts()) :
        $results_by_type = [];

        while (have_posts()) : the_post();
            $post_type = get_post_type();
            $results_by_type[$post_type][] = [
                'ID' => get_the_ID(),
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'excerpt' => mb_strimwidth(strip_tags(get_the_excerpt()), 0, 350, '...'),
                'has_thumbnail' => has_post_thumbnail(),
                'thumbnail' => get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'searchpage-thumb-img']),
            ];
        endwhile;

        foreach ($results_by_type as $type => $posts) :
            $type_obj = get_post_type_object($type);
            ?>
            <div class="searchpage-group my-5">
                <h2 class="searchpage-header"><?php echo esc_html($type_obj->labels->name); ?></h2>
                
                <div class="searchpage-items">
                    <?php foreach ($posts as $post) : ?>
                        <div class="searchpage-item searchpage-fade-in">
                            <div class="searchpage-item-content">
                                <?php if ($type === 'post' && $post['has_thumbnail']) : ?>
                                    <div class="searchpage-thumbnail">
                                        <a href="<?php echo esc_url($post['permalink']); ?>">
                                            <?php echo $post['thumbnail']; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="searchpage-item-content">
                                <div class="searchpage-item-tags">
                                    <?php if ($type === 'post') :
                                        $post_tags = get_the_tags($post['ID']);
                                        if ($post_tags) : ?>
                                            <div class="searchpage-item-tags">
                                                <span class="searchpage-item-tags-label"></span>
                                                <?php foreach ($post_tags as $tag) : ?>
                                                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>">
                                                        <span class="searchpage-tag-badge"><?php echo esc_html($tag->name); ?></span>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif;
                                    endif; ?>
                                </div>    
                                <a href="<?php echo esc_url($post['permalink']); ?>" class="searchpage-item-title">
                                    <?php echo esc_html($post['title']); ?>
                                </a>
                                <p class="searchpage-item-excerpt"><?php echo esc_html($post['excerpt']); ?></p>          
                            </div>
                            
                        </div>
                        
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach;

        the_posts_navigation();

    else : ?>
    <?php endif; ?>
</section>
<section class="spacer"></section>
<span class="span-line"></span>
<section class="fp-search d-flex justify-content-center align-items-center flex-column py-5 my-5">
        <p class="fp-search-p">Looking for something specific?</p>
        <div class="error-form">
            <form role="search" method="get" class="custom-search-form" action="<?php echo home_url('/'); ?>">
                <input type="search" name="s" class="custom-search-input" placeholder="Search anything..." required />
                <button type="submit" class="custom-search-button">Search</button>
            </form>
        </div>
    </section>


<?php get_footer(); ?>
