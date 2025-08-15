<?php echo '<!-- layout-faqs.php loaded -->'; ?>

<section class="faq-hero d-flex flex-column align-items-center justify-content-center" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/faq.svg" id="faq-img" alt="FAQs Image">
    <h2 class="faq-hero-text h1">Frequently Asked Questions</h2>
</section>


<section class="faq-content d-flex flex-column align-items-center justify-content-center pt-4" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
    <div class="accordion" id="accordionExample">
        <div class="accordion-item" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s" >
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Is the Library open on Saturdays?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <p>The Library is open Monday to Friday, from 8:00 a.m. to 5:00 p.m., except Holidays.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Does the Library have access to datasets?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" >
                <div class="accordion-body">
                    <p>The Library has a set of curated datasets from different organizations, like the Asian Development Bank (ADB), Bangko Sentral ng Pilipinas (BSP), Department of Social Welfare and Development (DSWD), Philippine Center for Economic Development (PCED), Philippine Statistics Authority (PSA), Worldbank, United Nations Development Programme (UNDP), and UNESCO. You may email upselibrary.upd@up.edu.ph for access instructions.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    How can I access our subscribed dataset providers?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <p>The Library is subscribed to four dataset providers: CEIC Data, EIKON with Datastream, Orbis Database, and GTAP Database. The access is exclusive to currently enrolled UPSE students, faculty members, and staff. You may email upselibrary.upd@up.edu.ph for the access instructions.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Does the Library have an electronic copy of books I need for my subject?
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <p>The Library is subscribed to four dataset providers: CEIC Data, EIKON with Datastream, Orbis Database, and GTAP Database. The access is exclusive to currently enrolled UPSE students, faculty members, and staff. You may email upselibrary.upd@up.edu.ph for the access instructions.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    How can I access the Library's collection of theses and dissertations??
                </button>
            </h2>
            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <p>The Library's thesis and dissertation collections are available in print and electronic. Refer to this link to see the list of theses and dissertations. To access the electronic copies, email upselibrary.upd@up.edu.ph for the instructions.</p>
                </div>
            </div>
        </div>
    </div>
</section>


