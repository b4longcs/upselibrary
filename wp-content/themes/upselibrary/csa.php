
<?php 
/*
Template Name: CSA
*/
get_header(); ?>
<section class="container">
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Current</p>
                <p class="hero-header-one">Awareness</p>
                <p class="hero-header-two">Service</p>
            </div>
            
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/databases-img.svg" alt="global-hero-img">
        </div>
    </section>
    <h1 class="my-5" id="post-list-title">Latest Updates</h1>
    <section class="post-filter-container">
        <div class="post-grid-top-container d-flex  align-items-center flex-wrap gap-2">
            
            <div class="post-grid-filter d-flex justify-content-center align-items-center ">
                <p class="post-grid-filter">Filter : </p>
                <select id="category-filter" class="category-filter">
                    <option value="all">All</option>
                    <?php 
                    $categories = get_categories();
                    foreach ($categories as $category) {
                        echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <!-- Search Input -->
            <input type="text" id="search-input" class="search-input" placeholder="Search posts...">
        </div>

        <div class="span-line my-4"></div>

        <!-- Posts Grid -->
        <div id="posts-grid" class="posts-grid" aria-live="polite">
            <!-- Posts will be dynamically loaded here -->
        </div>

        <!-- Pagination -->
        <div id="pagination" class="pagination" aria-label="Pagination">
            <!-- Pagination buttons will be dynamically generated -->
        </div>
    </section>
</section>
<section class="spacer"></section>
<?php get_footer(); ?>
