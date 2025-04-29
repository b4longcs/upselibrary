<?php 
/*
Template Name: InterLoan
*/
get_header(); ?>
<section class="container">
    <?php custom_breadcrumb(); ?>
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap mb-5">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">InterLibrary</p>
                <p class="hero-header-two">Loan</p>
            </div>
            <div class="left-hero-subsubheader my-3 w-75">
               
                <p class="content-text-custom bg-custom p-4">The UPSE Library offers ILL to users who want to borrow books from partner libraries. Email <b>upselibrary.upd@up.edu.ph</b> to avail.</p>
            </div>
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/interlibrary-img.svg" alt="global-hero-img">
        </div>
    </section>
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap-reverse mt-5">
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/referral-img.svg" alt="global-hero-img">
        </div>
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Referral</p>
                <!-- <p class="hero-header-two">Loan</p> -->
            </div>
            <div class="left-hero-subsubheader my-3 w-75">
                <p class="content-text-custom bg-custom p-4">If ILL cannot be used to borrow the needed material/s from a partner library, the librarian can provide a referral letter to the user so s/he can personally visit the partner library and photocopy the material/s needed.</p>  
            </div>
        </div>
    </section>
    
</section>
<?php get_footer(); ?>
