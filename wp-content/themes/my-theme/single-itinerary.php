<?php
/**
 * Single Itinerary — cinematic hero layout.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id = get_the_ID();

    /* ── fields ── */
    $duration  = mytheme_get_travel_field( 'itinerary_duration',      $id ) ?: mytheme_get_travel_field( 'trip_duration', $id );
    $best_time = mytheme_get_travel_field( 'itinerary_best_time',     $id ) ?: mytheme_get_travel_field( 'best_time',     $id );
    $route_sum = mytheme_get_travel_field( 'itinerary_route_summary', $id ) ?: mytheme_get_travel_field( 'route_summary', $id );
    $dest_obj  = mytheme_get_travel_field( 'itinerary_destination',   $id );
    $dest_name = ( $dest_obj && isset( $dest_obj->post_title ) ) ? $dest_obj->post_title : mytheme_get_travel_field( 'destination_name', $id );

    /* ── price (numeric guard) ── */
    $price = '';
    foreach ( array(
        get_post_meta( $id, '_itinerary_budget', true ),
        get_post_meta( $id, '_starting_price',   true ),
        get_post_meta( $id, 'package_amount',    true ),
    ) as $_v ) {
        if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
    }

    /* ── book URL ── */
    $book_url = mytheme_get_travel_field( 'book_url', $id ) ?: mytheme_get_plan_trip_url();

    /* ── region ── */
    $regions = get_the_terms( $id, 'destination_region' );
    $region  = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;
    $thumb   = get_the_post_thumbnail_url( $id, 'large' );
    $archive = get_post_type_archive_link( 'itinerary' );
    ?>

<main class="main-content explore-page ev-single">

    <!-- CINEMATIC HERO -->
    <section class="explore-hero ev-hero-cinematic<?php echo $thumb ? ' has-hero-img' : ''; ?>"
             <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( $archive ); ?>">Itineraries</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <span class="explore-hero-kicker">Ready-made Itinerary</span>
            <h1 class="explore-hero-title"><?php the_title(); ?></h1>
            <?php if ( $dest_name || $region ) : ?>
            <p class="explore-hero-sub"><?php echo esc_html( $dest_name ?: '' ); ?><?php if ( $region && $dest_name ) { echo ' · '; } ?><?php if ( $region ) { echo esc_html( $region->name ); } ?></p>
            <?php endif; ?>
            <div class="ev-hero-facts">
                <?php if ( $duration )  : ?><span class="ev-hero-fact"><span class="ev-fact-icon">⏱️</span><?php echo esc_html( $duration ); ?></span><?php endif; ?>
                <?php if ( $best_time ) : ?><span class="ev-hero-fact"><span class="ev-fact-icon">🌤️</span><?php echo esc_html( $best_time ); ?></span><?php endif; ?>
                <?php if ( $region )    : ?><span class="ev-hero-fact"><span class="ev-fact-icon">📍</span><a href="<?php echo esc_url( get_term_link( $region ) ); ?>"><?php echo esc_html( $region->name ); ?></a></span><?php endif; ?>
                <?php if ( $price )     : ?><span class="ev-hero-fact ev-hero-fact--price"><span class="ev-fact-icon">💰</span><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹' . $price ); ?></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container ev-single-wrap">
        <article class="ev-single-card">

            <div class="ev-single-body">

                <!-- 1. Description first (excerpt / post content intro) -->
                <?php if ( get_the_excerpt() ) : ?>
                <div class="ev-single-content ev-content-styled ev-itin-intro">
                    <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                </div>
                <?php endif; ?>

                <!-- 2. Trip details -->
                <?php
                $details = array_filter( array(
                    array( '🗺️', 'Route',       $route_sum ),
                    array( '🌤️', 'Best Time',    $best_time ),
                    array( '⏱️', 'Duration',     $duration ),
                    array( '📍', 'Destination',  $dest_name ),
                ), function( $d ) { return ! empty( $d[2] ); } );
                if ( $details ) : ?>
                <div class="ev-trip-details">
                    <h3 class="ev-trip-details-title">Itinerary Details</h3>
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

                <!-- 3. Day-wise plan (accordion) -->
                <?php if ( function_exists('have_rows') && have_rows('itinerary_days') ) : ?>
                <section class="itinerary-days itinerary-timeline" style="margin-top:28px;">
                    <div class="itinerary-days-heading">
                        <span>Route timeline</span>
                        <h2>Day Wise Plan</h2>
                    </div>
                    <?php $day_number = 1; ?>
                    <?php while ( have_rows('itinerary_days') ) : the_row();
                        $day_title      = get_sub_field('day_title');
                        $day_details    = get_sub_field('day_details');
                        $day_route      = get_sub_field('day_route');
                        $day_stay       = get_sub_field('day_stay');
                        $day_meals      = get_sub_field('day_meals');
                        $day_transfer   = get_sub_field('day_transfer');
                        $day_highlights = get_sub_field('day_highlights');
                        $preview_text   = wp_trim_words( wp_strip_all_tags( $day_details ), 18, '...' );
                    ?>
                    <details class="itinerary-day" <?php echo $day_number === 1 ? 'open' : ''; ?>>
                        <summary>
                            <span class="itinerary-day-marker"><?php echo esc_html( $day_number ); ?></span>
                            <span class="itinerary-day-summary">
                                <strong><?php echo esc_html( $day_title ?: sprintf( __('Day %d','mytheme'), $day_number ) ); ?></strong>
                                <?php if ( $day_route ) : ?><em><?php echo esc_html( $day_route ); ?></em><?php elseif ( $preview_text ) : ?><em><?php echo esc_html( $preview_text ); ?></em><?php endif; ?>
                            </span>
                            <span class="itinerary-day-toggle" aria-hidden="true"></span>
                        </summary>
                        <div class="itinerary-day-panel">
                            <?php if ( $day_stay || $day_meals || $day_transfer || $day_highlights ) : ?>
                            <div class="itinerary-day-chips">
                                <?php if ( $day_stay )       : ?><span><i class="fa-solid fa-bed"></i><?php echo esc_html( $day_stay ); ?></span><?php endif; ?>
                                <?php if ( $day_meals )      : ?><span><i class="fa-solid fa-utensils"></i><?php echo esc_html( $day_meals ); ?></span><?php endif; ?>
                                <?php if ( $day_transfer )   : ?><span><i class="fa-solid fa-route"></i><?php echo esc_html( $day_transfer ); ?></span><?php endif; ?>
                                <?php if ( $day_highlights ) : ?><span><i class="fa-solid fa-star"></i><?php echo esc_html( $day_highlights ); ?></span><?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="itinerary-day-copy ev-content-styled"><?php echo wp_kses_post( wpautop( $day_details ) ); ?></div>
                        </div>
                    </details>
                    <?php $day_number++; endwhile; ?>
                </section>
                <?php endif; ?>

                <!-- 4. Post editor content (if any additional content) -->
                <?php if ( get_the_content() ) : ?>
                <div class="ev-single-content ev-content-styled" style="margin-top:24px;">
                    <?php the_content(); ?>
                </div>
                <?php endif; ?>

                <?php mytheme_render_faq_section( $id, 'Itinerary FAQs' ); ?>

                <footer class="post-footer travel-cta" style="margin-top:28px;">
                    <?php mytheme_render_trip_pdf_button( $id, 'Download PDF' ); ?>
                    <a href="<?php echo esc_url( $archive ); ?>" class="btn-secondary">All Itineraries</a>
                </footer>

            </div><!-- /.ev-single-body -->

            <!-- Booking sidebar -->
            <aside class="ev-single-book">
                <?php if ( $price ) : ?>
                <div class="ev-single-price">
                    <span>Starts from</span>
                    <strong><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹' . $price ); ?></strong>
                    <small>per person</small>
                </div>
                <?php endif; ?>
                <?php
                $sidebar_rows = array_filter( array(
                    'Duration'    => $duration,
                    'Best Time'   => $best_time,
                    'Destination' => $dest_name,
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
                <a class="ev-single-btn" href="<?php echo esc_url( $book_url ); ?>">Book Now</a>
                <a class="ev-single-btn ev-single-btn--ghost" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">Customise Route</a>
            </aside>

        </article>
        <a class="tribe-back-link" href="<?php echo esc_url( $archive ); ?>">← All Itineraries</a>
    </div>

</main>

<?php
endwhile;
get_footer();
