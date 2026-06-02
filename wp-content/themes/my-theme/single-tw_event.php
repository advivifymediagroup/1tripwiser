<?php
/**
 * Single Event & Festival (tw_event) — a special bookable package.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id      = get_the_ID();
    $date    = mytheme_get_travel_field( 'event_date', $id );
    // Duration: ACF group stores total_nights + total_days (same as packages); old meta fallback = trip_duration
    $nights  = (int) mytheme_get_travel_field( 'total_nights', $id );
    $days    = (int) mytheme_get_travel_field( 'total_days', $id );
    $dur     = mytheme_get_travel_field( 'trip_duration', $id );
    if ( ! $dur && $nights ) {
        $dur = $nights . ' Nights ' . ( $days ? $days : $nights + 1 ) . ' Days';
    }
    // Price: try every meta path ACF or the seeder could have written; only accept numeric values
    // so we never display raw ACF field-key strings (e.g. "field_twe_amount").
    $price = '';
    foreach ( array(
        get_post_meta( $id, 'package_amount',   true ),  // ACF-saved (no underscore)
        get_post_meta( $id, 'starting_price',   true ),  // ACF-saved (no underscore)
        get_post_meta( $id, '_starting_price',  true ),  // seeder _meta fallback
        get_post_meta( $id, '_package_amount',  true ),  // seeder _meta fallback
    ) as $_v ) {
        if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
    }
    // Book URL: ACF group uses package_book_url (same as packages); old meta fallback = book_url
    $book    = mytheme_get_travel_field( 'package_book_url', $id ) ?: mytheme_get_travel_field( 'book_url', $id );
    $book_url = $book ? $book : mytheme_get_plan_trip_url();
    $regions = get_the_terms( $id, 'destination_region' );
    $region  = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;
    $thumb   = get_the_post_thumbnail_url( $id, 'large' );
    ?>

<main class="main-content explore-page ev-single">

    <section class="explore-hero ev-hero-cinematic<?php echo $thumb ? ' has-hero-img' : ''; ?>"
             <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( tw_events_page_url() ); ?>">Events &amp; Festivals</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <span class="explore-hero-kicker">Event &amp; Festival</span>
            <h1 class="explore-hero-title"><?php the_title(); ?></h1>
            <?php if ( $date || $region ) : ?>
            <p class="explore-hero-sub">
                <?php if ( $region ) { echo esc_html( $region->name ); } ?>
                <?php if ( $date ) { echo $region ? ' · ' : ''; echo esc_html( $date ); } ?>
            </p>
            <?php endif; ?>
            <!-- Quick facts in hero -->
            <div class="ev-hero-facts">
                <?php if ( $dur ) : ?><span class="ev-hero-fact"><span class="ev-fact-icon">⏱️</span><?php echo esc_html( $dur ); ?></span><?php endif; ?>
                <?php if ( $date ) : ?><span class="ev-hero-fact"><span class="ev-fact-icon">📅</span><?php echo esc_html( $date ); ?></span><?php endif; ?>
                <?php if ( $region ) : ?><span class="ev-hero-fact"><span class="ev-fact-icon">📍</span><a href="<?php echo esc_url( get_term_link( $region ) ); ?>"><?php echo esc_html( $region->name ); ?></a></span><?php endif; ?>
                <?php if ( $price ) : ?><span class="ev-hero-fact ev-hero-fact--price"><span class="ev-fact-icon">💰</span><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹' . $price ); ?> <small>/ person</small></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container ev-single-wrap">
        <article class="ev-single-card">
            <!-- Featured image moved to hero bg — no standalone media block -->

            <div class="ev-single-body">
                <div class="ev-single-content"><?php the_content(); ?></div>
            </div>

            <!-- Sticky booking bar -->
            <aside class="ev-single-book">
                <?php if ( $price ) : ?>
                <div class="ev-single-price">
                    <span>Starts from</span>
                    <strong><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : $price ); ?></strong>
                    <small>per person</small>
                </div>
                <?php endif; ?>
                <a class="ev-single-btn" href="<?php echo esc_url( $book_url ); ?>">Book Now</a>
                <a class="ev-single-btn ev-single-btn--ghost" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">Customise this trip</a>
            </aside>
        </article>

        <a class="tribe-back-link" href="<?php echo esc_url( tw_events_page_url() ); ?>">← All events &amp; festivals</a>
    </div>
</main>

<?php
endwhile;
get_footer();
