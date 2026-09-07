<?php
/**
 * Template Name: Women's Group Trips
 *
 * Dedicated landing page for Women-Only group trips.
 * Queries group_trip posts where package_trip_type = 'Women Trip'.
 *
 * @package my-theme
 */
get_header();

$trips_query = new WP_Query( array(
    'post_type'      => 'group_trip',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => 'package_trip_type', 'value' => 'Women Trip', 'compare' => '=' ),
        array( 'key' => '_package_trip_type','value' => 'Women Trip', 'compare' => '=' ),
    ),
) );

$has_trips = $trips_query->have_posts();
?>

<main class="main-content explore-page tw-womens-page">

    <!-- HERO -->
    <section class="explore-hero tw-womens-hero">
        <div class="tw-womens-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <span>Women's Group Trips</span>
            </nav>
            <span class="tw-womens-kicker"><i class="fi-rr-sparkles" aria-hidden="true"></i> Travel Together · Grow Together</span>
            <h1 class="tw-h1">Women's <span class="accent-red">Group Trips</span></h1>
            <p class="explore-hero-sub">Safe, curated and empowering travel experiences designed exclusively for women. Join your tribe, explore the world.</p>
        </div>
    </section>

    <div class="container explore-wrap">

        <!-- WHY SECTION -->
        <div class="tw-womens-why">
            <div class="tw-womens-why-card">
                <span class="tw-womens-why-icon"><i class="fi-rr-shield" aria-hidden="true"></i></span>
                <h3>Safety First</h3>
                <p>Every trip is designed with women's safety at the core — verified accommodations, trusted operators and 24/7 support.</p>
            </div>
            <div class="tw-womens-why-card">
                <span class="tw-womens-why-icon"><i class="fi-rr-user" aria-hidden="true"></i></span>
                <h3>Women-Led</h3>
                <p>Our female trip leaders bring local expertise and a personal touch that makes every journey feel like travelling with a friend.</p>
            </div>
            <div class="tw-womens-why-card">
                <span class="tw-womens-why-icon"><i class="fi-rr-flower" aria-hidden="true"></i></span>
                <h3>Curated Experiences</h3>
                <p>From wellness retreats to adventure hikes — every itinerary is handcrafted with experiences that resonate with women travellers.</p>
            </div>
            <div class="tw-womens-why-card">
                <span class="tw-womens-why-icon"><i class="fi-rr-comment" aria-hidden="true"></i></span>
                <h3>Build Your Tribe</h3>
                <p>Meet incredible women, form lifelong bonds and join the 1TRIPWISER Tribe community of 10K+ women adventurers.</p>
            </div>
        </div>

        <!-- TRIPS GRID -->
        <div class="tw-womens-section-head">
            <h2 class="tw-h2">Upcoming Women's Trips</h2>
            <p><?php echo $has_trips ? $trips_query->found_posts . ' curated trips available' : 'Check back soon — new trips added regularly'; ?></p>
        </div>

        <?php if ( $has_trips ) : ?>
        <div class="tw-womens-trips-grid">
            <?php while ( $trips_query->have_posts() ) : $trips_query->the_post();
                $id    = get_the_ID();
                $thumb = get_the_post_thumbnail_url( $id, 'large' );
                $price_raw = '';
                foreach ( array( 'package_amount', 'starting_price', '_starting_price', '_package_amount' ) as $_k ) {
                    $_v = get_post_meta( $id, $_k, true );
                    if ( is_numeric( $_v ) && $_v > 0 ) { $price_raw = $_v; break; }
                }
                $nights  = (int) get_post_meta( $id, 'total_nights', true ) ?: (int) mytheme_get_travel_field('total_nights', $id);
                $days    = (int) get_post_meta( $id, 'total_days', true )   ?: (int) mytheme_get_travel_field('total_days', $id);
                $dur     = $nights ? $nights . ' Nights ' . ( $days ?: $nights + 1 ) . ' Days' : mytheme_get_travel_field('trip_duration', $id);
                $loc     = get_post_meta( $id, 'package_location', true ) ?: mytheme_get_travel_field('destination_name', $id);
                $book    = get_permalink( $id ) . '#group-enquiry';
                $regions = get_the_terms( $id, 'destination_region' );
                $region  = ( $regions && ! is_wp_error($regions) ) ? $regions[0]->name : '';
            ?>
            <article class="tw-womens-trip-card">
                <a class="tw-womens-trip-img" href="<?php the_permalink(); ?>">
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
                    <?php else : ?>
                        <span class="tw-womens-trip-placeholder"><i class="fi-rr-flower" aria-hidden="true"></i></span>
                    <?php endif; ?>
                    <span class="tw-womens-trip-badge">Women Only</span>
                </a>
                <div class="tw-womens-trip-body">
                    <div class="tw-womens-trip-meta">
                        <?php if ( $region ) : ?><span><i class="fi-rr-marker" aria-hidden="true"></i> <?php echo esc_html( $region ); ?></span><?php endif; ?>
                        <?php if ( $dur )    : ?><span><i class="fi-rr-clock" aria-hidden="true"></i> <?php echo esc_html( $dur ); ?></span><?php endif; ?>
                    </div>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php echo wp_trim_words( get_the_excerpt(), 20, '…' ); ?></p>
                    <div class="tw-womens-trip-foot">
                        <div>
                            <?php if ( $price_raw ) : ?>
                            <strong class="tw-womens-trip-price"><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount($price_raw) : '₹'.$price_raw ); ?></strong>
                            <?php endif; ?>
                            <?php if ( $emi ) : ?><small><?php echo esc_html( $emi ); ?>/mo</small><?php endif; ?>
                        </div>
                        <a href="<?php echo esc_url( $book ); ?>" class="btn-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php else : ?>
        <div class="tribe-empty">
            <div class="tribe-empty-icon"><i class="fi-rr-flower" aria-hidden="true"></i></div>
            <h3>Women's trips coming soon</h3>
            <p>We're curating the most empowering travel experiences. In the meantime, plan a custom women's trip with us!</p>
            <a class="btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><i class="fi-rr-plane" aria-hidden="true"></i> Plan a Custom Trip — Free</a>
        </div>
        <?php endif; ?>

        <!-- TESTIMONIALS -->
        <?php
        $testimonials = get_option( 'tw_womens_testimonials', array() );
        if ( empty( $testimonials ) ) {
            $testimonials = array(
                array( 'quote' => 'Best decision I ever made for solo travel — felt safe, empowered and came back a changed person.', 'name' => 'Priya S.', 'location' => 'Mumbai' ),
                array( 'quote' => 'Felt safe the entire trip. The female trip leader was amazing and the group was so supportive.', 'name' => 'Ananya R.', 'location' => 'Delhi' ),
                array( 'quote' => 'Met my absolute best friends on a 1TRIPWISER women\'s trip. We\'ve already booked the next one!', 'name' => 'Riya K.', 'location' => 'Bangalore' ),
            );
        }
        if ( ! empty( $testimonials ) ) : ?>
        <div class="tw-womens-testimonials">
            <div class="tw-womens-testi-head">
                <span class="tw-womens-kicker"><i class="fi-rr-comment" aria-hidden="true"></i> What our travellers say</span>
                <h2 class="tw-womens-testi-title">Stories from <span>Our Tribe</span></h2>
            </div>
            <div class="tw-womens-testi-grid">
                <?php foreach ( $testimonials as $t ) :
                    $initials = strtoupper( substr( $t['name'] ?? 'W', 0, 1 ) );
                ?>
                <div class="tw-womens-testi-card">
                    <div class="tw-womens-testi-stars">
                        <span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span>
                    </div>
                    <span class="tw-womens-testi-quote-mark">"</span>
                    <p class="tw-womens-testi-text"><?php echo esc_html( $t['quote'] ); ?>"</p>
                    <div class="tw-womens-testi-author">
                        <div class="tw-womens-testi-avatar"><?php echo esc_html( $initials ); ?></div>
                        <div>
                            <?php if ( ! empty( $t['name'] ) ) : ?>
                            <strong><?php echo esc_html( $t['name'] ); ?></strong>
                            <?php endif; ?>
                            <?php if ( ! empty( $t['location'] ) ) : ?>
                            <span><?php echo esc_html( $t['location'] ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.container -->
</main>

<?php get_footer(); ?>
