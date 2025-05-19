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
        // About
        'brief-history'         => ['about', 'bf'],
        'vision-and-mission'    => ['about', 'vm'],
        'general-policy'        => ['about', 'gp'],
        'library-guides'        => ['about', 'lg'],
        'library-staff'         => ['about', 'ls'],
        'faq'                   => ['about', 'faq'],
        'contact-us'            => ['about', 'cu'],

        // Resources
        'print-collection'      => ['resources', 'pc'],
        'online-subscription'   => ['resources', 'os'],
        'databases'             => ['resources', 'db'],
        'datasets'              => ['resources', 'ds'],
        'archives-collection'   => ['resources', 'ac'],

        // Services
        'circulation-service'       => ['services', 'cs'],
        'reference-service'         => ['services', 'rs'],
        'current-awareness-service' => ['services', 'csa'],
        'tbd'                       => ['services', 'tbd'],
        'interlibrary-loan'         => ['services', 'ill'],
        'document-delivery-service' => ['services', 'dds']
    ];

    $current_slug = get_post_field('post_name', get_post());

    if (array_key_exists($current_slug, $page_templates)) {
        list($folder, $file) = $page_templates[$current_slug];
        $template_path = locate_template("assets/page-templates/{$folder}/layout-{$file}.php");

        if ($template_path) {
            include $template_path;
        } else {
            get_template_part('assets/page-templates/layout', 'default');
        }
    } else {
        get_template_part('assets/page-templates/layout', 'default');
    }
    ?>
</section>
<section class="spacer"></section>
<?php get_footer(); ?>
