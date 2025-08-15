
<?php 
/*
Template Name: CSA
*/
get_header(); ?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap">
        <div class="global-left-hero" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Current</p>
                <p class="hero-header-one">Awareness</p>
                <p class="hero-header-two">Service</p>
            </div>
            
        </div>
        <div class="global-right-hero" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/databases-img.svg" alt="Current Awareness Service Image">
        </div>
    </section>
    <h1 class="my-5" id="post-list-title" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">Latest Updates</h1>
    <section class="post-filter-container">
        <div class="post-grid-top-container d-flex  align-items-center flex-wrap gap-2" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            
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

        <div class="span-line my-4" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s"></div>

        <!-- Posts Grid -->
        <div id="posts-grid" class="posts-grid" aria-live="polite" data-scrollreveal="enter bottom over 1s and move 50px after 0.15s">
            <!-- Posts will be dynamically loaded here -->
        </div>

        <!-- Pagination -->
        <div id="pagination" class="pagination" aria-label="Pagination" data-scrollreveal="enter bottom over 1s and move 50px after 0.18s">
            <!-- Pagination buttons will be dynamically generated -->
        </div>
    </section>
</section>
<section class="spacer"></section>
<?php get_footer(); ?>
