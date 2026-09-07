<?php
/**
 * Generic page template. A handful of plain informational pages (Visa
 * Assistance, Passport Services, Travel Insurance — all linked from the
 * homepage's "Trip Services" cards) get the site's editorial hero + styled
 * content treatment instead of the bare title-and-paragraphs layout, since
 * they only ever contain free-text block-editor content with no page
 * template of their own.
 */
get_header();

$tw_service_page_slugs = array( 'visa-assistance', 'passport-services', 'travel-insurance' );
?>

<?php while ( have_posts() ) : the_post();
    $tw_is_service_page = in_array( get_post_field( 'post_name' ), $tw_service_page_slugs, true );
    if ( $tw_is_service_page ) :
        $tw_archive = home_url( '/' );
        ?>

<main class="main-content explore-page tw-service-page">

    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mytheme' ); ?>">
                <a href="<?php echo esc_url( $tw_archive ); ?>">Home</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <span class="explore-hero-kicker"><?php esc_html_e( 'Travel Made Simple', 'mytheme' ); ?></span>
            <h1 class="tw-h1"><?php the_title(); ?></h1>
        </div>
    </section>

    <div class="container tw-service-wrap">
        <article class="tw-service-content ev-content-styled">
            <?php the_content(); ?>
        </article>

        <div class="tw-service-cta">
            <h2><?php esc_html_e( 'Still have questions?', 'mytheme' ); ?></h2>
            <p><?php esc_html_e( "Our travel experts are ready to help — get in touch and we'll take it from there.", 'mytheme' ); ?></p>
            <a href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>" class="btn-primary"><?php esc_html_e( 'Plan My Trip', 'mytheme' ); ?></a>
        </div>
    </div>

</main>

    <?php else : ?>

<main class="main-content">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>
        <article class="page-content">
            <h1 class="tw-h1"><?php the_title(); ?></h1>
            <!-- <div class="page-meta">
                <span class="date"><?php echo get_the_date(); ?></span>
                <span class="author">by <?php the_author(); ?></span>
            </div> -->
            <div class="content">
                <?php the_content(); ?>
            </div>
            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">' . __('Pages:', 'mytheme'),
                'after' => '</div>',
            ));
            ?>
            <?php mytheme_render_faq_section(get_the_ID()); ?>
        </article>
    </div>
</main>

    <?php endif; ?>
<?php endwhile; ?>

<?php get_footer(); ?>
