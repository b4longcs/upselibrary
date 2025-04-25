<?php 
/*
Template Name: Print Collection
*/
get_header(); ?>

<section class="container">
    <div id="breadcrumb-container"></div>
    <section class="pages-container my-5 my-lg-3 my-md-2 my-sm-2 d-flex justify-content-between w-100">
        <div class="left-hero">
            <div class="left-hero-header d-flex justify-content-center align-items-center flex-column">
                <p class="hero-header-one">Print</p>
                <p class="hero-header-two">Collection</p>
            </div>
            <!-- <div class="left-hero-subsubheader"></div> -->
        </div>
        <div class="right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/printcollection-img.svg" alt="global-hero-img">
        </div>
    </section>
    <section class="global-pages-container my-5 my-lg-3 my-md-2 my-sm-2 d-flex justify-content-between">
        <div>
            <span class="span-line"></span>
            <div class="global-pages-content d-flex flex-row justify-content-center align-items-start">
                <div>
                    <p class="content-header w-75">General Reference Books</p>
                </div>
                <div>
                    <p class="content-text">General reference books and materials (encyclopedias, dictionaries, atlases, etc.) shall be for room use only. General reference books may be issued for classroom use upon the request of a faculty member, but these shall be returned within the day.</p>
                </div>
            </div>
        </div>
        <div>
            <span class="span-line"></span>
            <div class="global-pages-content d-flex flex-row justify-content-center align-items-start">
                <div>
                    <p class="content-header">Circulation Books</p>
                </div>
                <div>
                    <p class="content-text">Books for home use are usually loaned for two weeks.</p>
                </div>
            </div>
        </div>
        <div>
            <span class="span-line"></span>
            <div class="global-pages-content d-flex flex-row justify-content-center align-items-start">
                <div>
                    <p class="content-header w-75">
                    Course Reserve Books</p>
                </div>
                <div>
                    <p class="content-text">Course reserve books are the primary reference literature used in the courses offered by the School. This section employs a closed shelving system.</p>
                    <article class="content-text-added ml-3 my-2">
                        <p class="content-header">Restrictions on Course Reserve Books</p>
                        <p class="content-text">• The items can only be borrowed for room use or for overnight use.</p>
                        It is not possible to extend the borrowing period for reserve books. Users are required to wait until 4:00 PM before they can borrow an item for a second time.
                    </article>
                </div>
            </div>
        </div>
        <div>
            <span class="span-line"></span>
            <div class="global-pages-content d-flex flex-row justify-content-center align-items-start">
                <div>
                    <p class="content-header w-75">Filipiniana Books</p>
                </div>
                <div>
                    <p class="content-text">Filipiniana books encompass literature that pertains to the Philippines, regardless of whether the author is of native or international origin. These books can be borrowed for home use for one (1) week.</p>
                   
                </div>
            </div>
        </div>
        <div>
            <span class="span-line"></span>
            <div class="global-pages-content d-flex flex-row justify-content-center align-items-start">
                <div>
                    <p class="content-header w-75">Thesis, Dissertation, and Periodicals</p>
                </div>
                <div>
                    <p class="content-text">These materials are for room use only. However, faculty and officials of the University may borrow a periodical, other than the latest issue for a period of not more than one (1) week.</p>
                   
                </div>
            </div>
        </div>
        
    </section>
</section>
<?php get_footer(); ?>
