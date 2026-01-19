<?php
/*
Template Name: Visitor
*/
get_header(); ?>
<section class="container">
    <?php custom_breadcrumb(); ?>  
    <div class="global-pages-container">
        <div class="global-left-hero"
            
            <p class="hero-header-one py-3">Visitor</p>
            <!-- <p class="hero-header-two"></p> -->
            <p class="global-hero-subheader py-3">The library resources may be utilized by the following non-members of the University, provided that they adhere to the rules and regulations that regulate their usage:</p>
            
            <div>
                <p class="gh-subsubheader">• Alumni, former faculty members and students honorably discharged from the University</p>
                <p class="gh-subsubheader">• Undergraduate/Graduate students from other schools</p>
                <p class="gh-subsubheader">• Government and private researchers</p>
            </div>
            
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/visitor-header-img.svg" alt="Visitor Hero Image" class="right-hero-img">
        </div>
    </div>
</section>

<section class="container">
    <div class="global-pages-container pt-5">
        <h2 data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">use of the library by non-UP</h2>
        <div class="span-line" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s"></div>
        <div class="global-pages-container">
            <div class="global-left-hero" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s" >
                <div class="global-left-hero-content">
                    <p class="subheader py-3">
                        For alumni, former faculty members and students honorably discharged from the University the following procedures will be observed:
                    </p>
                </div>
            </div>
            <article class="global-right-hero" data-scrollreveal="enter bottom over 1s and move 50px after 0.2s" >
                <p class="subsubheader  py-2"><b class="text-uppercase">• Free use.</b> Alumni, former faculty members and students honorably discharged from the University may be allowed to use the library for five (5) days free of charge within a semester. They are issued special permits by the duly authorized staff of the college/unit library they would like to use. Beyond five days, they will be asked to pay library fees.</p>
                <p class="subsubheader py-2"><b class="text-uppercase">• Fees.</b> Beyond five days the following fees are charged: Php20.00 per day; Php450.00 per year.</p>
                <p class="subsubheader py-2">Upon payment of the fees, they are issued special permits by the duly authorized staff of the college/unit library they would like to use.</p>
                <p class="subsubheader py-2"><b class="text-uppercase">• Letter of Introduction or ID.</b> The above users must present identification cards or letters of introduction from a University personnel or a U.P. Alumni Association ID when applying for a permit to use the library.</p>
            </article>
        </div>
    </div>
</section>

<section class="container">
    <div class="global-pages-container pt-5">
        <div class="span-line" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s"></div>
        <div class="global-pages-container">
            <div class="global-left-hero" data-scrollreveal="enter bottom over 1s and move 50px after 0.15s" >
                <div class="global-left-hero-content">
                    <p class="subheader py-3">
                        For undergraduate/graduate students and researchers the following procedures are observed:
                    </p>
                </div>
            </div>
            <article class="global-right-hero" data-scrollreveal="enter bottom over 1s and move 50px after 0.2s" >
                    <p class="subsubheader py-2"><b class="text-uppercase">• Undergraduate/Graduate students</b> must present a letter from their librarian requesting privilege to use the UP Diliman Library, and their school/university ID.</p>
                    <p class="subsubheader py-2"><b class="text-uppercase">• Private researchers</b> must present a letter of request to use the UP libraries from their offices, and their office IDs.</p>
                    <p class="subsubheader py-2"><b class="text-uppercase">• Government researchers</b> must present a letter from the head of their agency requesting privilege to use the library, and their office ID. They may be allowed to use the library free of charge up to five (5) days per semester. Beyond five days they will be assessed library fees.</p>
                    <p class="subsubheader py-2"><b class="text-uppercase">• Fees</b> Graduate students and private researchers are assessed as follows: Php50.00 per day; Php450.00 per semester; Php350.00 per summer. Government researchers are assessed as follows: Php20.00 per day; Php450.00 per semester; Php300.00 per summer.</p>
            </article>
        </div>
    </div>
</section>
<?php get_footer(); ?>
