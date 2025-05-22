<?php
/*
Template Name: Custom Page Template
*/
get_header();
?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <?php
    $groups = [
        'about' => [
            'brief-history' => 'bf',
            'vision-and-mission' => 'vm',
            'general-policy' => 'gp',
            'library-guides' => 'lg',
            'library-staff' => 'ls',
            'faq' => 'faq',
            'contact-us' => 'cu',
        ],
        'resources' => [
            'print-collection' => 'pc',
            'online-subscription' => 'os',
            'databases' => 'db',
            'datasets' => 'ds',
            'archives-collection' => 'ac',
        ],
        'services' => [
            'circulation-service' => 'cs',
            'reference-service' => 'rs',
            'thesis-and-dissertation-binding' => 'tdb',
            'interlibrary-loan' => 'ill',
            'document-delivery-service' => 'dds',
        ]
    ];

    $page_templates = [];
    foreach ($groups as $folder => $pages) {
        foreach ($pages as $slug => $file) {
            $page_templates[$slug] = [$folder, $file];
        }
    }

    $current_slug = get_post_field('post_name', get_post());

    if (isset($page_templates[$current_slug])) {
        [$folder, $file] = $page_templates[$current_slug];
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
