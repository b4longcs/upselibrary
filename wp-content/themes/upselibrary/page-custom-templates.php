<?php
/*
Template Name: Custom Page Template
*/
get_header();
?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <?php
    $page_templates = [
        'brief-history'   => 'bf',
        'vision-and-mission'  => 'vm',
        'general-policy'  => 'gp',
        'library-guides'  => 'lg',
        'library-staff'   => 'ls',
        'faq'             => 'faq',
        'contact-us'      => 'cu'
    ];

    $current_slug = get_post_field('post_name', get_post());

    if (array_key_exists($current_slug, $page_templates)) {
        get_template_part('assets/page-templates/about/layout', $page_templates[$current_slug]);
    } else {
        get_template_part('assets/page-templates/layout', 'default');
    }
    ?>

</section>
<div class="spacer"></div>
<?php get_footer(); ?>
