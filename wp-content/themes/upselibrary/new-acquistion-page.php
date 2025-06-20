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
        <p>New economics books have arrived at the UPSE Library, featuring up-to-date research and key discussions shaping the discipline. </p>
    </section>
    <?php the_content(); ?>
</section>
<div class="spacer"></div>
<?php get_footer(); ?>
