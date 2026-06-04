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
    $location  = get_post_meta( $id, 'package_location', true ) ?: mytheme_get_travel_field( 'destination_name', $id );
    $departure = get_post_meta( $id, 'event_date', true )        ?: mytheme_get_travel_field( 'event_date', $id );
    $grp_size  = get_post_meta( $id, 'group_size', true )        ?: mytheme_get_travel_field( 'group_size', $id );
    $trip_type = get_post_meta( $id, 'package_trip_type', true ) ?: mytheme_get_travel_field( 'trip_type', $id );
    $tag       = get_post_meta( $id, 'package_tag', true );
    $emi       = get_post_meta( $id, 'package_emi', true );
    $overview  = get_post_meta( $id, 'package_overview', true );

    /* ── book URL ── */
    $book     = get_post_meta( $id, 'package_book_url', true ) ?: mytheme_get_travel_field( 'book_url', $id );
    $book_url = $book ?: mytheme_get_plan_trip_url();

    /* ── region / taxonomy ── */
    $regions = get_the_terms( $id, 'destination_region' );
    $region  = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;
    $thumb   = get_the_post_thumbnail_url( $id, 'large' );

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
                <div class="ev-single-content">
                    <?php
                    /* Show WYSIWYG overview from ACF if available, else fall back to post content */
                    if ( $overview ) {
                        echo wp_kses_post( wpautop( $overview ) );
                    }
                    the_content();
                    ?>
                </div>
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

                <?php if ( $dur || $departure || $grp_size ) : ?>
                <div class="ev-single-detail-list">
                    <?php if ( $dur ) : ?>
                    <div class="ev-single-detail-row">
                        <span class="ev-detail-label">Duration</span>
                        <span class="ev-detail-val"><?php echo esc_html( $dur ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $departure ) : ?>
                    <div class="ev-single-detail-row">
                        <span class="ev-detail-label">Departure</span>
                        <span class="ev-detail-val"><?php echo esc_html( $departure ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $grp_size ) : ?>
                    <div class="ev-single-detail-row">
                        <span class="ev-detail-label">Group Size</span>
                        <span class="ev-detail-val"><?php echo esc_html( $grp_size ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $location ) : ?>
                    <div class="ev-single-detail-row">
                        <span class="ev-detail-label">Destination</span>
                        <span class="ev-detail-val"><?php echo esc_html( $location ); ?></span>
                    </div>
                    <?php endif; ?>
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
