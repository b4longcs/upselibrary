<?php 
/*
Template Name: New Acquisition Page
Description: A template for displaying new acquisitions in a library.   
*/
get_header(); ?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <section class="na-page justify-content-center align-items-center d-flex flex-column mb-5">
        
        <h1>New Acquisitions</h1>
        
    </section>
    <?php the_content(); ?>
</section>
<div class="spacer"></div>
<?php get_footer(); ?>
