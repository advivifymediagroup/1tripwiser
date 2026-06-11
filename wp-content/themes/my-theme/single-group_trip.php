<?php
/**
 * Single Group Trip — package-style layout matching Events & Festivals.
 *
 * Reads fields from the "Group Trip Details" ACF group:
 *   package_amount, total_nights, total_days, package_location,
 *   event_date (departure), group_size, package_book_url, package_overview,
 *   package_trip_type, package_tag, package_emi
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id = get_the_ID();

    /* ── price ── */
    $price = '';
    foreach ( array(
        get_post_meta( $id, 'package_amount',  true ),
        get_post_meta( $id, '_package_amount', true ),
        get_post_meta( $id, '_starting_price', true ),
        get_post_meta( $id, 'starting_price',  true ),
    ) as $_v ) {
        if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
    }

    /* ── duration ── */
    $nights = (int) ( get_post_meta( $id, 'total_nights', true ) ?: mytheme_get_travel_field( 'total_nights', $id ) );
    $days   = (int) ( get_post_meta( $id, 'total_days',   true ) ?: mytheme_get_travel_field( 'total_days',   $id ) );
    $dur    = mytheme_get_travel_field( 'trip_duration', $id );
    if ( ! $dur && $nights ) {
        $dur = $nights . ' Nights ' . ( $days ?: $nights + 1 ) . ' Days';
    }

    /* ── other fields ── */
    $location  = get_post_meta( $id, 'package_location', true )  ?: mytheme_get_travel_field( 'destination_name', $id );
    $departure = get_post_meta( $id, 'event_date', true )         ?: mytheme_get_travel_field( 'event_date', $id );
    $grp_size  = get_post_meta( $id, 'group_size', true )         ?: mytheme_get_travel_field( 'group_size', $id );
    $trip_type = get_post_meta( $id, 'package_trip_type', true )  ?: mytheme_get_travel_field( 'package_trip_type', $id );
    $tag       = get_post_meta( $id, 'package_tag', true );
    $emi       = get_post_meta( $id, 'package_emi', true );
    $overview  = get_post_meta( $id, 'package_overview', true );
    $route     = get_post_meta( $id, 'route_summary', true )      ?: mytheme_get_travel_field( 'route_summary', $id );
    $best_time = get_post_meta( $id, 'best_time', true )          ?: mytheme_get_travel_field( 'best_time', $id );

    /* ── book URL ── */
    $book     = get_post_meta( $id, 'package_book_url', true ) ?: mytheme_get_travel_field( 'book_url', $id );
    $book_url = $book ?: mytheme_get_plan_trip_url();

    /* ── region / taxonomy ── */
    $regions = get_the_terms( $id, 'destination_region' );
    $region  = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;
    $thumb   = get_the_post_thumbnail_url( $id, 'full' );

    /* archive URL */
    $archive_url = get_post_type_archive_link( 'group_trip' ) ?: home_url( '/group-trips/' );
    ?>

<main class="main-content explore-page ev-single">

    <!-- CINEMATIC HERO — featured image as full-bleed background -->
    <section class="explore-hero ev-hero-cinematic<?php echo $thumb ? ' has-hero-img' : ''; ?>"
             <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">

            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( $archive_url ); ?>">Group Trips</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>

            <span class="explore-hero-kicker">
                <?php echo esc_html( $trip_type ?: 'Group Trip' ); ?>
                <?php if ( $tag ) : ?> &nbsp;·&nbsp; <span style="color:#FCB415;"><?php echo esc_html( ucfirst( $tag ) ); ?></span><?php endif; ?>
            </span>

            <h1 class="explore-hero-title"><?php the_title(); ?></h1>

            <?php if ( $location || $region ) : ?>
            <p class="explore-hero-sub">
                <?php echo esc_html( $location ?: ( $region ? $region->name : '' ) ); ?>
                <?php if ( $region && $location ) : ?> · <?php echo esc_html( $region->name ); ?><?php endif; ?>
            </p>
            <?php endif; ?>

            <!-- Quick-fact pills in hero -->
            <div class="ev-hero-facts">
                <?php if ( $dur ) : ?>
                    <span class="ev-hero-fact"><span class="ev-fact-icon">⏱️</span><?php echo esc_html( $dur ); ?></span>
                <?php endif; ?>
                <?php if ( $departure ) : ?>
                    <span class="ev-hero-fact"><span class="ev-fact-icon">📅</span><?php echo esc_html( $departure ); ?></span>
                <?php endif; ?>
                <?php if ( $grp_size ) : ?>
                    <span class="ev-hero-fact"><span class="ev-fact-icon">👥</span><?php echo esc_html( $grp_size ); ?></span>
                <?php endif; ?>
                <?php if ( $region ) : ?>
                    <span class="ev-hero-fact"><span class="ev-fact-icon">📍</span>
                        <a href="<?php echo esc_url( get_term_link( $region ) ); ?>"><?php echo esc_html( $region->name ); ?></a>
                    </span>
                <?php endif; ?>
                <?php if ( $price ) : ?>
                    <span class="ev-hero-fact ev-hero-fact--price">
                        <span class="ev-fact-icon">💰</span>
                        <?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹' . $price ); ?>
                        <small>/ person</small>
                    </span>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- MAIN CONTENT + BOOKING SIDEBAR -->
    <div class="container ev-single-wrap">
        <article class="ev-single-card">

            <div class="ev-single-body">

                <!-- Trip details panel — shown FIRST -->
                <?php
                $details = array(
                    array( '🗺️', 'Route',          $route ),
                    array( '🌤️', 'Best Time',       $best_time ),
                    array( '👥', 'Group Size',      $grp_size ),
                    array( '🏷️', 'Trip Type',       $trip_type ),
                    array( '📅', 'Departure',       $departure ),
                    array( '📍', 'Destination',     $location ),
                );
                $details = array_filter( $details, function( $d ) { return ! empty( $d[2] ); } );
                if ( $details ) : ?>
                <div class="ev-trip-details">
                    <h3 class="ev-trip-details-title">Trip Details</h3>
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

                <!-- Overview / itinerary content -->
                <?php if ( $overview || get_the_content() ) : ?>
                <div class="ev-single-content ev-content-styled ev-overview-card">
                    <?php if ( $overview ) { echo wp_kses_post( wpautop( $overview ) ); } ?>
                    <?php the_content(); ?>
                </div>
                <?php endif; ?>

                <!-- Day-wise plan (from ACF repeater — try multiple field names) -->
                <?php
                $tw_days_field = '';
                if ( function_exists('get_field') ) {
                    foreach ( array( 'itinerary_days', 'package_days', 'day_wise_plan', 'days', 'trip_days' ) as $_fn ) {
                        $_v = get_field( $_fn );
                        if ( is_array( $_v ) && ! empty( $_v ) ) { $tw_days_field = $_fn; break; }
                    }
                }
                ?>
                <?php if ( $tw_days_field ) : ?>
                <section class="itinerary-days itinerary-timeline" style="margin-top:28px;">
                    <div class="itinerary-days-heading">
                        <span>Day-by-day</span>
                        <h2>Itinerary</h2>
                    </div>
                    <?php $day_number = 1; ?>
                    <?php while ( have_rows( $tw_days_field ) ) : the_row();
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

            </div>

            <!-- Sticky booking panel -->
            <aside class="ev-single-book">
                <?php if ( $price ) : ?>
                <div class="ev-single-price">
                    <span>Price per person</span>
                    <strong><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : $price ); ?></strong>
                    <?php if ( $emi ) : ?><small><?php echo esc_html( $emi ); ?>/mo</small><?php endif; ?>
                </div>
                <?php endif; ?>

                <?php
                $sidebar_rows = array_filter( array(
                    'Duration'    => $dur,
                    'Departure'   => $departure,
                    'Group Size'  => $grp_size,
                    'Destination' => $location,
                    'Trip Type'   => $trip_type,
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
                <a class="ev-single-btn ev-single-btn--ghost" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">Customise this trip</a>
            </aside>

        </article>

        <a class="tribe-back-link" href="<?php echo esc_url( $archive_url ); ?>">← All group trips</a>
    </div>

</main>

<?php
endwhile;
get_footer();
