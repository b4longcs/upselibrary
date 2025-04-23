<?php get_header(); ?>
<div class="container">
    <div class="single-main">
        <div class="single-container me-5">
            <main>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <article>
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        <p class="post-meta mt-5 mb-2">
                            <span class="post-date">Published: <?php echo get_the_date(); ?></span>
                            | <span class="post-time"><?php echo get_the_time(); ?></span>
                        </p>
                        <?php
                            $tags = get_the_tags();
                            if ($tags) :
                        ?>
                            <div class="post-tags mb-4">
                                <span class="tag-label">Tags:</span>
                                <?php foreach ($tags as $tag) : ?>
                                    <span class="tag-badge">
                                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>">
                                            <?php echo esc_html($tag->name); ?>
                                        </a>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                    <div class="post-navigation">
                        <div class="nav-previous">
                            <?php previous_post_link('%link', '<i class="fa-solid fa-chevron-left"></i> Previous Post'); ?>
                            

                        </div>
                        <div class="nav-next">
                            <?php next_post_link('%link', 'Next Post <i class="fa-solid fa-chevron-right"></i>'); ?>
                        </div>
                    </div>


                         
                <?php endwhile; endif; ?>
            </main>
        </div>
        
        <div class="sidebar mt-5 mb-5 p-3">
            <div class="categories">
                <h5 class="category mt-3 mb-2">Categories</h5>
                <?php
                $categories = get_categories();
                foreach ( $categories as $category ) {
                    if ( $category->slug === 'uncategorized' ) {
                        continue;
                    }
                    echo '<div class="category-item">';
                    echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="category-link">';
                    echo esc_html( $category->name );
                    echo '</a>';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="totalpost">
                <h5 class="totalpost mt-4 mb-2">Total posts</h5>
                <div class="yearly-post-count">
                    <?php
                    global $wpdb;

                    $results = $wpdb->get_results("
                        SELECT YEAR(post_date) AS year, COUNT(*) AS total_posts
                        FROM $wpdb->posts
                        WHERE post_type = 'post' 
                        AND post_status = 'publish'
                        GROUP BY YEAR(post_date)
                        ORDER BY year DESC
                    ");

                    if ( $results ) {
                        echo '<ul>';
                        foreach ( $results as $row ) {
                            echo '<li><strong>' . esc_html($row->year) . '</strong>: ' . esc_html($row->total_posts) . ' posts</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No posts found.</p>';
                    }
                    ?>
                </div>
            </div>
            
        </div>

    </div>
</div>
<span class="span-line mt-5"></span>
<section class="more-related mb-5">
    <section class="more-news-container d-flex">
        <span class="more-spaces"></span>
        <h2 class="related-header-text">Want more of this?</h2>
    </section>
    <div class="related-posts">
        <?php
        $tags = wp_get_post_tags(get_the_ID());
        if ($tags) :
            $tag_ids = array();
            foreach ($tags as $tag) {
                $tag_ids[] = $tag->term_id;
            }

            // Query related posts
            $related_query = new WP_Query(array(
                'tag__in' => $tag_ids,
                'posts_per_page' => 10,
                'post__not_in' => array(get_the_ID()), 
                'ignore_sticky_posts' => 1
            ));

            if ($related_query->have_posts()) :
                echo '<div class="related-posts-grid">';
                while ($related_query->have_posts()) : $related_query->the_post();
        ?>
                    <div class="related-post-item">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="related-post-thumbnail">
                                <?php the_post_thumbnail('full', ['srcset' => wp_get_attachment_image_srcset(get_post_thumbnail_id())]); ?>
                            </div>
                        <?php endif; ?>

                        <div class="related-post-content">
                            <a href="<?php the_permalink(); ?>" class="related-post-item-link">
                                <h4 class="related-post-title"><?php the_title(); ?></h4>
                                <p class="related-post-excerpt">
                                    <?php echo substr(strip_tags(get_the_excerpt()), 0, 70) . '...'; ?>
                                </p>
                            </a>

                            <button class="see-more-btn">See More</button>
                        </div>
                    </div>
        <?php
                endwhile;
                echo '</div>';
                wp_reset_postdata();
            else :
                echo '<p>No related post.</p>';
            endif;
        else :
            echo '<p>No related post.</p>';
        endif;
        ?>

    </div> 
</section>

<section class="more-news">
    
    <section class="single-most">
        <div><span class="most-header">Most Recent Updates</span></div>
        <section class="most-grid">
            <?php
            // Define the arguments for the query
            $args = array(
                'posts_per_page' => 8, // Limit to 5 posts (adjust as needed)
                'post_status' => 'publish', // Only published posts
            );

            // Create a new instance of WP_Query
            $query = new WP_Query($args);

            // Start the loop to display posts
            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
            ?>
            
            <section class="most-item">
                <div class="most-thumbnail">
                    <?php
                    // Get the post thumbnail (featured image)
                    if (has_post_thumbnail()) {
                        // Make the image clickable and link to the post
                        ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>

                <div class="most-content">
                    <div>
                        <h2 class="most-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="most-excerpt">
                            <?php echo mb_strimwidth(get_the_excerpt(), 0, 70, '...'); ?>
                        </p>
                    </div>
                    <div class="most-meta-wrapper">
                        <div class="most-meta-info">
                            <?php
                            $categories = get_the_category();
                            if ($categories) {
                                echo '<span class="most-meta-category">';
                                echo esc_html($categories[0]->name);
                                echo '</span>';
                            }

                            $word_count = str_word_count(strip_tags(get_the_content()));
                            $reading_time = ceil($word_count / 200);
                            echo '<span class="most-meta-reading-time">' . $reading_time . ' min read</span>';
                            ?>
                        </div>
                        
                    </div>
                </div>
            </section>

            <?php
                endwhile;
                wp_reset_postdata(); // Reset the query
            else :
                echo '<p>No posts found.</p>';
            endif;
            ?>
        </section>
        
    </section>
      
</section>

<?php get_footer(); ?>
