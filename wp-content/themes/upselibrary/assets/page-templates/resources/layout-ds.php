<?php echo '<!-- layout-ds.php loaded -->'; ?>
<section class="ds-container mt-5">
    <section class="global-pages-container align-items-center justify-content-between d-flex flex-wrap">
        <div class="global-left-hero">
            <div class="left-hero-header d-flex justify-content-center  flex-column">
                <p class="hero-header-one">Datasets</p>
                <!-- <p class="hero-header-two">Subscription</p> -->
            </div>
            <div class="left-hero-subsubheader my-3 w-75">
                <p class="content-text my-3">To access the curated datasets from various agencies, accomplish the attached <b>Data Use Agreement (DUA)</b> form and send it to <b><u>upselibrary.upd@up.edu.ph</u></b>. The signature of the faculty/thesis adviser is no longer required.</p>
                <a href="https://docs.google.com/document/d/17EJyNvBCI_faK9mXzewZVyL1BgWRZHtm/edit?tab=t.0" class="dua">Download DUA Here!</a>
            </div>
        </div>
        <div class="global-right-hero">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/datasets-img.svg" alt="global-hero-img">
        </div>
    </section>
    
    <section class="global-pages-content my-lg-3 my-md-2 my-sm-2">
        <section class=" mt-5" id="from-GetS">
            <div class="tabs" >
                <div class="tabs-nav" role="tablist" aria-label="Content sections" data-scrollreveal="enter bottom over 1s and move 50px after 0.1s">
                    <div class="tabs-indicator"></div>
                    <button class="tab-button" role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1">
                        Proprietary
                    </button>
                    <button class="tab-button" role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2">
                        Open Data
                    </button>
                </div>
                <div class="tab-panel" role="tabpanel" id="panel-1" aria-labelledby="tab-1" aria-hidden="false" >
                    <div class="gp-datasets">
                        <div class="gp-ds-content-one d-flex flex-row justify-content-center align-items-center">
                            <a href="https://comtradeplus.un.org/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/comtradelogo.png" alt="Comtrade Plus"></a>
                            <div class="content-one-desc d-flex flex-column justify-content-center">
                                <p>The UN Comtrade Database is the world's most comprehensive global trade data platform, aggregating detailed annual and monthly merchandise trade statistics by product and trading partner for nearly 200 countries. Managed by the United Nations Statistics Division, it provides this crucial economic information to governments, academia, and enterprises, offering data extraction via various formats including API developer tools.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-one d-flex flex-row justify-content-center align-items-center">
                            <a href="https://www.ceicdata.com/en" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/ceic.webp" alt="CEIC Data"></a>
                            <div class="content-one-desc d-flex flex-column justify-content-center">
                                <p>CEIC Data, founded in 1992, is a trusted provider of comprehensive and accurate macroeconomic data for both developed and developing markets, serving top-tier clients like investment banks, corporations, and universities worldwide. With local experts in over 18 countries, CEIC curates data from thousands of sources and offers it through an easy-to-use platform with 24/5 expert analyst support.</p>
                            </div>
                        </div>
                        
                        <div class="gp-ds-content-one d-flex flex-row justify-content-center align-items-center">
                            
                            <a href="https://www.gtap.agecon.purdue.edu/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/gtap.png" alt="GTAP Database"></a>
                            <div class="content-one-desc d-flex flex-column justify-content-center">
                                <p>The Global Trade Analysis Project (GTAP), coordinated by the Center for Global Trade Analysis at Purdue University, is a network of over 33,000 individuals dedicated to advancing high-quality quantitative analysis of global economic challenges. To achieve this, GTAP provides the widely recognized "gold standard" GTAP Data Base, cutting-edge models and software, and training courses for multi-region, applied general equilibrium analysis.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-one d-flex flex-row justify-content-center align-items-center">
                            <a href="https://eikon.refinitiv.com" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/eikon.webp" alt="EIKON with Datastream"></a>  
                            <div class="content-one-desc d-flex flex-column justify-content-center">
                                <p>Refinitiv Eikon is an open-technology solution for financial market professionals, providing comprehensive access to industry-leading financial data, news, and analysis from Refinitiv (an LSEG Business). It features exclusive Reuters News, the extensive Refinitiv Datastream database, and proprietary tools like StarMine Quantitative Analytics, all accessible through a high-performance desktop, web, and mobile apps to streamline workflows and inform critical investing, trading, and risk decisions.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-panel" role="tabpanel" id="panel-2" aria-labelledby="tab-2" aria-hidden="true">
                    <div class="gp-datasets-two">
                        <div class="gp-ds-content-two">
                            <a href="http://www.data.gov/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/data-gov.png" alt="Data-gov"></a>
                            <div class="content-two-desc">
                                <p>Data.gov is the U.S. Government's open data site, managed by the GSA Technology Transformation Services, designed to unleash the power of public government data to inform decisions, drive innovation, and ensure transparent governance. Operating under the OPEN Government Data Act, the site centralizes datasets from federal agencies that are required to catalog and publish their public data in machine-readable formats.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="http://www.dhsprogram.com/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/dhs.webp" alt="DHS"></a>
                            <div class="content-two-desc">
                                <p>The DHS Program (Demographic and Health Surveys) provides technical assistance to over 400 surveys in 90+ countries to advance the global understanding of health and population trends in developing nations. Implemented by ICF, the program collects and disseminates accurate, nationally representative data on key topics like fertility, family planning, maternal and child health, and HIV/AIDS, with the ultimate goal of fostering data use for policy and program planning.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://dataverse.harvard.edu/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/harvard-dataverse.png" alt="Harvard dataverse"></a>
                            <div class="content-two-desc">
                                <p>The Harvard Dataverse Repository is a free, open-access repository for researchers worldwide to share, archive, cite, and explore research data, satisfying mandates from funders and journals. Researchers gain increased visibility and academic credit with automatic Digital Object Identifiers (DOIs), and they can organize their data within customizable collections called Dataverses.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="http://www.icpsr.umich.edu/icpsrweb/ICPSR/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icpsr.jpg" alt="ICPSR"></a>
                            <div class="content-two-desc">
                                <p>The ICPSR (Inter-university Consortium for Political and Social Research) is a major international consortium of over 800 academic and research organizations, serving as a leader in social science data access, curation, and analytical training. It archives over 400,000 research files across more than 25 specialized data collections—including fields like education and criminal justice—while also hosting the long-running Summer Program in Quantitative Methods.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="https://data.imf.org/en/datasets/IMF.STA:PIP)" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/imf.png" alt="IMF"></a>
                            <div class="content-two-desc">
                                <p>The International Monetary Fund (IMF) is a global organization of 191 member countries established in 1944 to promote financial stability and monetary cooperation to achieve sustainable growth and prosperity for all. It supports its members by offering policy advice on economic and financial developments, financial assistance through approximately $1 trillion in lending capacity, and capacity development through technical assistance and training.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://www.ipums.org/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/ipums.png" alt="IPUMS"></a>
                            <div class="content-two-desc">
                                <p>IPUMS (originally Integrated Public Use Microdata Series) democratizes access to the world's social and economic data by integrating and harmonizing census and survey data across time and space to enable comparative research. Through innovative technology and extensive funding, this free service provides the world's largest accessible database of census microdata, streamlining complex data for transformative scholarship, teaching, and policy-making.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="https://dataverse.harvard.edu/dataverse/jpal" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/jpal-dataverse.png" alt="JPAL Dataverse"></a>
                            <div class="content-two-desc">
                                <p>The Harvard Dataverse Repository is a free, open-access repository for researchers worldwide to share, archive, cite, and explore research data, satisfying mandates from funders and journals. Researchers gain increased visibility and academic credit with automatic Digital Object Identifiers (DOIs), and they can organize their data within customizable collections called Dataverses.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://ocw.mit.edu/" target="_blank" rel="noopener noreferrer"><img class="img-custom" src="<?php echo get_template_directory_uri(); ?>/assets/images/mit-open.png" alt="mit"></a>
                            <div class="content-two-desc">
                                <p>This website operates under the MIT OpenCourseWare License, combining a privacy policy that protects your personal information and a Creative Commons (CC BY-NC-SA 4.0) license that allows free sharing and adaptation for non-commercial educational use. While OCW respects user privacy by not collecting personally identifiable data unless voluntarily provided, it uses anonymous analytics to improve the service and requires proper attribution when using its materials.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="https://www.deped.gov.ph/paaralang-bukas/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/project-bukas.png" alt="project bukas"></a>
                            <div class="content-two-desc">
                                <p>The Department of Education (DepEd) is committed to promoting transparency, integrity, and accountability in basic education by making its data accessible to the public through "Project Bukas". This initiative aims to empower stakeholders to improve learning outcomes, and feedback provided by users is collected solely for the monitoring and evaluation of the project, adhering to the Data Privacy Act of 2012.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://thinkingmachines.github.io/project-cchain/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cchain.png" alt="cchain"></a>
                            <div class="content-two-desc">
                                <p>Project CCHAIN offers a validated, open-sourced linked dataset containing 20 years (2003-2022) of health, climate, environment, and socioeconomic variables at the barangay (village) level across 12 Philippine cities. This effort, supported by the Lacuna Fund and Wellcome, also includes an open deep-learning model code and output data for downscaled temperature and rainfall.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="https://psada.psa.gov.ph/about" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/psa.png" alt="psa"></a>
                            <div class="content-two-desc">
                                <p>The PSA Data Archive is the systematic repository and central data catalog of the Philippine Statistics Authority (PSA), mandated to collect and disseminate the country's comprehensive economic, social, and demographic statistics under Republic Act No. 10625. Built in partnership with PARIS21, its goal is to document, preserve, and promote the rational use of all PSA datasets, including microdata from censuses and surveys, and to provide free and open information accessible online 24/7.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://www.povertyactionlab.org/admindata" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/jpal-data.png" alt="jpal-lab"></a>
                            <div class="content-two-desc">
                                <p>The Abdul Latif Jameel Poverty Action Lab (J-PAL) is a global research center anchored by over 1,100 affiliated researchers that works to reduce poverty by using randomized impact evaluations to rigorously test and improve social programs. J-PAL translates this scientific evidence into action, informing policy for governments and NGOs worldwide, while also providing extensive education and training to strengthen capacity for evidence-informed decision-making.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two">
                            <a href="http://data.worldbank.org/" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/wb-360.jpg" alt="worldbank-data360"></a>
                            <div class="content-two-desc">
                                <p>Data360 is the World Bank’s comprehensive, integrated open data portal, consolidating over 300 million curated development data points from across the World Bank Group and its partners into a single, user-friendly platform. It aims to drive evidence-based decision-making by offering advanced search, analytics, and custom reporting across key focus areas, with most data available under a Creative Commons Attribution 4.0 International License (CC BY 4.0).</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://www.enterprisesurveys.org/en/enterprisesurveys" target="_blank" rel="noopener noreferrer"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/wb-enterprise.png" alt="worldbank enterprise"></a>
                            <div class="content-two-desc">
                                <p>The World Bank Enterprise Survey (WBES) is a standardized, firm-level survey that collects representative data on a private sector's business environment, covering topics like finance, infrastructure, and competition. Since 2006, the WBES has been a key tool for the World Bank's Enterprise Analysis Unit, also supplying data for the Business Ready indicators.</p>
                            </div>
                        </div>
                        <div class="gp-ds-content-two-custom">
                            <a href="https://microdata.worldbank.org/index.php/home" target="_blank" rel="noopener noreferrer"><img class ="img-custom" src="<?php echo get_template_directory_uri(); ?>/assets/images/wb-microlib.png" alt="worldbank enterprise"></a>
                            <div class="content-two-desc">
                                <p>The Microdata Library offers free access to a comprehensive collection of high-quality microdata from the World Bank and other agencies, focusing on people, institutions, and economies in developing countries. Compliant with international standards, this service supports the Bank's commitment to Open Data, Open Knowledge, and Open Solutions by democratizing access to crucial development research.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <section class="spacer"></section>
</section>
