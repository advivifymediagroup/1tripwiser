<?php get_header(); ?>

<main class="main-content plan-trip-page">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article class="page-content plan-trip-content">
                <span class="hero-kicker">Custom travel planning</span>
                <h1><?php the_title(); ?></h1>
                <div class="content">
                    <?php the_content(); ?>
                </div>

                <div class="plan-trip-grid">
                    <div>
                        <h2>Tell us what kind of trip you want</h2>
                        <!-- <p>Add a contact form plugin shortcode here, or place your phone, WhatsApp and email details in this page content from WordPress admin.</p> -->
                    </div>
                    <div class="travel-detail-grid compact">
                        <div class="travel-detail">
                            <span>Step 1</span>
                            <strong>Share dates and budget</strong>
                        </div>
                        <div class="travel-detail">
                            <span>Step 2</span>
                            <strong>Pick destinations</strong>
                        </div>
                        <div class="travel-detail">
                            <span>Step 3</span>
                            <strong>Get a custom plan</strong>
                        </div>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
