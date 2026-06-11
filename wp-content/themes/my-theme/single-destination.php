<?php
/**
 * Single Destination — cinematic hero layout matching all other trip types.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id          = get_the_ID();
    $destination = mytheme_get_destination_data( $id );

    /* Hero image — try ACF, then WP featured thumbnail, always at FULL size */
    $hero_img = '';
    if ( ! empty( $destination['image'] ) ) {
        $hero_img = mytheme_get_image_url( $destination['image'], 'full' );
    }
    if ( ! $hero_img && has_post_thumbnail( $id ) ) {
        $hero_img = get_the_post_thumbnail_url( $id, 'full' );
    }

    $archive = get_post_type_archive_link( 'destination' );
    ?>

<main class="main-content explore-page ev-single">

    <!-- CINEMATIC HERO -->
    <section class="explore-hero ev-hero-cinematic<?php echo $hero_img ? ' has-hero-img' : ''; ?>"
             <?php if ( $hero_img ) : ?>style="background-image:url('<?php echo esc_url( $hero_img ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( $archive ); ?>">Destinations</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <span class="explore-hero-kicker">Destination</span>
            <h1 class="explore-hero-title"><?php the_title(); ?></h1>
            <?php if ( $destination['country'] ) : ?>
            <p class="explore-hero-sub"><?php echo esc_html( $destination['country'] ); ?></p>
            <?php endif; ?>
            <div class="ev-hero-facts">
                <?php if ( $destination['country'] )        : ?><span class="ev-hero-fact"><span class="ev-fact-icon">📍</span><?php echo esc_html( $destination['country'] ); ?></span><?php endif; ?>
                <?php if ( $destination['best_time'] )      : ?><span class="ev-hero-fact"><span class="ev-fact-icon">🌤️</span><?php echo esc_html( $destination['best_time'] ); ?></span><?php endif; ?>
                <?php if ( $destination['ideal_duration'] ) : ?><span class="ev-hero-fact"><span class="ev-fact-icon">⏱️</span><?php echo esc_html( $destination['ideal_duration'] ); ?></span><?php endif; ?>
                <?php if ( $destination['starting_price'] ) : ?><span class="ev-hero-fact ev-hero-fact--price"><span class="ev-fact-icon">💰</span><?php echo esc_html( $destination['starting_price'] ); ?> <small>onwards</small></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container ev-single-wrap">
        <article class="ev-single-card">

            <div class="ev-single-body">

                <!-- 1. Short intro -->
                <?php if ( $destination['short_intro'] ) : ?>
                <div class="ev-single-content ev-content-styled ev-overview-card">
                    <?php echo wp_kses_post( wpautop( $destination['short_intro'] ) ); ?>
                </div>
                <?php endif; ?>

                <!-- 2. Destination details panel -->
                <?php
                $details = array_filter( array(
                    array( '📍', 'Country / Region', $destination['country'] ),
                    array( '🌤️', 'Best Time',         $destination['best_time'] ),
                    array( '⏱️', 'Ideal Duration',    $destination['ideal_duration'] ),
                    array( '💰', 'Starting Price',    $destination['starting_price'] ),
                ), function( $d ) { return ! empty( $d[2] ); } );
                if ( $details ) : ?>
                <div class="ev-trip-details">
                    <h3 class="ev-trip-details-title">Destination Details</h3>
                    <div class="ev-trip-details-grid">
                        <?php foreach ( $details as $d ) : ?>
                        <div class="ev-trip-detail-item">
                            <span class="ev-trip-detail-icon"><?php echo $d[0]; ?></span>
                            <div>
                                <span class="ev-trip-detail-label"><?php echo esc_html( $d[1] ); ?></span>
                                <span class="ev-trip-detail-val"><?php echo esc_html( $d[2] ); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 3. Overview content -->
                <?php if ( $destination['overview'] ) : ?>
                <div class="ev-single-content ev-content-styled ev-overview-card" style="margin-top:24px;">
                    <?php echo wp_kses_post( $destination['overview'] ); ?>
                </div>
                <?php endif; ?>

                <!-- 4. Destination guide + FAQs -->
                <?php mytheme_render_destination_guide( $id ); ?>
                <?php mytheme_render_faq_section( $id, 'Destination FAQs' ); ?>

                <footer class="post-footer travel-cta" style="margin-top:28px;">
                    <a href="<?php echo esc_url( get_post_type_archive_link('travel_package') ); ?>" class="btn-primary">View Packages</a>
                    <a href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>" class="btn-secondary">Plan a Trip</a>
                </footer>

            </div>

            <!-- Sticky sidebar -->
            <aside class="ev-single-book">
                <?php if ( $destination['starting_price'] ) : ?>
                <div class="ev-single-price">
                    <span>Packages from</span>
                    <strong><?php echo esc_html( $destination['starting_price'] ); ?></strong>
                    <small>per person</small>
                </div>
                <?php endif; ?>
                <?php
                $sidebar_rows = array_filter( array(
                    'Country'        => $destination['country'],
                    'Best Time'      => $destination['best_time'],
                    'Ideal Duration' => $destination['ideal_duration'],
                ) );
                if ( $sidebar_rows ) : ?>
                <div class="ev-single-detail-list">
                    <?php foreach ( $sidebar_rows as $label => $val ) : ?>
                    <div class="ev-single-detail-row">
                        <span class="ev-detail-label"><?php echo esc_html( $label ); ?></span>
                        <span class="ev-detail-val"><?php echo esc_html( $val ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a class="ev-single-btn" href="<?php echo esc_url( get_post_type_archive_link('travel_package') ); ?>">View Packages</a>
                <a class="ev-single-btn ev-single-btn--ghost" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">Plan a Trip</a>
            </aside>

        </article>
        <a class="tribe-back-link" href="<?php echo esc_url( $archive ); ?>">← All Destinations</a>
    </div>

</main>

<?php
endwhile;
get_footer();
