<?php
/**
 * Template Name: LUXE
 *
 * Dedicated landing page for LUXE — luxury / concierge-style trips
 * (yachts, charter jets, Michelin experiences) for premium clientele.
 *
 * @package my-theme
 */
get_header();

$trips_query = new WP_Query( array(
    'post_type'      => array( 'group_trip', 'travel_package', 'tw_event' ),
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => 'package_trip_type',  'value' => 'Luxe Trip', 'compare' => '=' ),
        array( 'key' => '_package_trip_type', 'value' => 'Luxe Trip', 'compare' => '=' ),
    ),
) );
$has_trips = $trips_query->have_posts();
?>

<main class="main-content tw-luxe-page">

    <!-- HERO -->
    <section class="tw-luxe-hero">
        <div class="tw-luxe-hero-overlay" aria-hidden="true"></div>
        <div class="container tw-luxe-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <span>LUXE</span>
            </nav>
            <span class="tw-luxe-hero-kicker">By Invitation &middot; Curated for Connoisseurs</span>
            <h1 class="tw-luxe-hero-title">LUXE</h1>
            <p class="tw-luxe-hero-sub">Private yachts. Charter jets. Michelin-starred experiences. Quiet villas in coordinates we don't print. Travel as it should be — orchestrated for you, by us.</p>

            <div class="tw-luxe-hero-stats">
                <div class="tw-luxe-stat"><strong>24/7</strong><span>Personal concierge</span></div>
                <div class="tw-luxe-stat-div"></div>
                <div class="tw-luxe-stat"><strong>1:1</strong><span>Curation</span></div>
                <div class="tw-luxe-stat-div"></div>
                <div class="tw-luxe-stat"><strong>Tier-1</strong><span>Partner hotels &amp; villas</span></div>
                <div class="tw-luxe-stat-div"></div>
                <div class="tw-luxe-stat"><strong>NDA</strong><span>Discretion guaranteed</span></div>
            </div>

            <div class="tw-luxe-hero-cta">
                <a href="#luxe-enquiry" class="tw-luxe-hero-btn">Request a Curation</a>
            </div>
        </div>
    </section>

    <div class="container tw-luxe-wrap">

        <!-- WHY LUXE -->
        <div class="tw-luxe-why">
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-crown tw-luxe-why-icon"></i>
                <h3>Hand-crafted</h3>
                <p>Every itinerary is built around you. No templates. No package deals. Just a designer working in your time zone.</p>
            </div>
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-ship tw-luxe-why-icon"></i>
                <h3>Private Access</h3>
                <p>Private yachts in the Mediterranean. Charter jets between islands. After-hours access to museums and cellars.</p>
            </div>
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-utensils tw-luxe-why-icon"></i>
                <h3>Michelin &amp; Beyond</h3>
                <p>Tables at restaurants without phone numbers. Chefs sent to your villa. Tastings with vintners on the estate.</p>
            </div>
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-shield-halved tw-luxe-why-icon"></i>
                <h3>Discreet</h3>
                <p>Optional NDA on every engagement. No social posts. No press. What happens on your trip stays there.</p>
            </div>
        </div>

        <!-- TRIPS GRID -->
        <div class="tw-luxe-section-head">
            <h2>Curated Experiences</h2>
            <p><?php echo $has_trips ? $trips_query->found_posts . ' invitations available' : 'New experiences added every season'; ?></p>
        </div>

        <?php if ( $has_trips ) : ?>
        <div class="tw-luxe-trips-grid">
            <?php while ( $trips_query->have_posts() ) : $trips_query->the_post();
                $id    = get_the_ID();
                $thumb = get_the_post_thumbnail_url( $id, 'large' );
                $price = '';
                foreach ( array( 'package_amount', 'starting_price', '_starting_price', '_package_amount' ) as $_k ) {
                    $_v = get_post_meta( $id, $_k, true );
                    if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
                }
                $nights = (int) get_post_meta( $id, 'total_nights', true ) ?: (int) mytheme_get_travel_field('total_nights', $id);
                $days   = (int) get_post_meta( $id, 'total_days', true )   ?: (int) mytheme_get_travel_field('total_days', $id);
                $dur    = $nights ? $nights . ' Nights ' . ( $days ?: $nights + 1 ) . ' Days' : mytheme_get_travel_field('trip_duration', $id);
                $loc    = get_post_meta( $id, 'package_location', true ) ?: mytheme_get_travel_field('destination_name', $id);
                $regions = get_the_terms( $id, 'destination_region' );
                $region  = ( $regions && ! is_wp_error($regions) ) ? $regions[0]->name : '';
            ?>
            <article class="tw-luxe-trip-card">
                <a class="tw-luxe-trip-img" href="<?php the_permalink(); ?>">
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
                    <?php else : ?>
                        <i class="fa-solid fa-crown tw-luxe-trip-ph"></i>
                    <?php endif; ?>
                    <span class="tw-luxe-trip-badge">LUXE</span>
                </a>
                <div class="tw-luxe-trip-body">
                    <div class="tw-luxe-trip-meta">
                        <?php if ( $region || $loc ) : ?><span><?php echo esc_html( $loc ?: $region ); ?></span><?php endif; ?>
                        <?php if ( $dur )    : ?><span><?php echo esc_html( $dur ); ?></span><?php endif; ?>
                    </div>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20, '…' ) ); ?></p>
                    <div class="tw-luxe-trip-foot">
                        <?php if ( $price ) : ?>
                        <div>
                            <small>FROM</small>
                            <strong class="tw-luxe-trip-price"><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹'.$price ); ?></strong>
                        </div>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="tw-luxe-trip-cta">Enquire <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php else : ?>
        <div class="tw-luxe-empty">
            <i class="fa-solid fa-crown"></i>
            <h3>New experiences are being curated</h3>
            <p>Our concierge team is hand-picking the next round of LUXE experiences. Request a private curation in the meantime.</p>
            <a class="tw-luxe-hero-btn" href="#luxe-enquiry">Request a Curation</a>
        </div>
        <?php endif; ?>

        <!-- ENQUIRY FORM -->
        <section class="tw-luxe-enquiry" id="luxe-enquiry">
            <div class="tw-luxe-enquiry-copy">
                <span class="tw-luxe-kicker">Private Curation</span>
                <h2>Tell us where, and we'll do the rest</h2>
                <p>Our concierge team responds within 4 hours. All enquiries treated in confidence.</p>
            </div>
            <form class="tw-luxe-enquiry-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
                <input type="hidden" name="action" value="mytheme_package_inquiry">
                <input type="hidden" name="package_id" value="0">
                <?php wp_nonce_field( 'mytheme_package_inquiry', 'mytheme_package_inquiry_nonce' ); ?>
                <div class="tw-luxe-form-grid">
                    <label><span>Name *</span><input type="text" name="name" required></label>
                    <label><span>Phone *</span><input type="tel" name="phone" required></label>
                    <label><span>Email</span><input type="email" name="email"></label>
                    <label><span>Preferred Travel Window</span><input type="text" name="date" placeholder="e.g. October 2026"></label>
                    <label><span>Party Size</span><input type="number" name="adults" min="1" value="2"></label>
                    <label><span>Budget Range</span>
                        <select name="budget">
                            <option value="">Select tier</option>
                            <option>₹5L – ₹15L</option>
                            <option>₹15L – ₹50L</option>
                            <option>₹50L+</option>
                            <option>By NDA</option>
                        </select>
                    </label>
                    <label class="tw-luxe-form-full"><span>Tell us about your trip</span>
                        <textarea name="message" rows="4" placeholder="Destinations, dates, occasions, dietary needs, mobility, anything else…"></textarea>
                    </label>
                </div>
                <button type="submit" class="tw-luxe-submit">Send Request</button>
            </form>
        </section>

    </div>
</main>

<?php get_footer(); ?>
