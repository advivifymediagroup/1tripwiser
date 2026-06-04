<?php
/**
 * Single Travel Package — cinematic hero layout.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id      = get_the_ID();
    $package = mytheme_get_package_data( $id );

    /* ── price (numeric guard) ── */
    $price = '';
    foreach ( array(
        get_post_meta( $id, 'package_amount',  true ),
        get_post_meta( $id, '_package_amount', true ),
        get_post_meta( $id, '_starting_price', true ),
    ) as $_v ) {
        if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
    }

    /* ── fields ── */
    $location  = $package['location'];
    $dur       = $package['duration'];
    $trip_type = $package['trip_type'];
    $tag       = $package['tag'];
    $emi       = $package['emi'];
    $overview  = $package['overview'];
    $book_url  = $package['book_url'] ?: mytheme_get_plan_trip_url();
    $route     = get_post_meta( $id, 'route_summary', true ) ?: mytheme_get_travel_field( 'route_summary', $id );
    $best_time = get_post_meta( $id, 'best_time', true )     ?: mytheme_get_travel_field( 'best_time', $id );
    $grp_size  = get_post_meta( $id, 'group_size', true )    ?: mytheme_get_travel_field( 'group_size', $id );

    /* ── region ── */
    $regions = get_the_terms( $id, 'destination_region' );
    $region  = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;
    $thumb   = get_the_post_thumbnail_url( $id, 'large' );
    $archive = get_post_type_archive_link( 'travel_package' );
    ?>

<main class="main-content explore-page ev-single">

    <!-- CINEMATIC HERO -->
    <section class="explore-hero ev-hero-cinematic<?php echo $thumb ? ' has-hero-img' : ''; ?>"
             <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( $archive ); ?>">Packages</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <span class="explore-hero-kicker">
                Travel Package<?php if ( $tag ) : ?>&nbsp;·&nbsp;<span style="color:#FCB415;"><?php echo esc_html( ucfirst( $tag ) ); ?></span><?php endif; ?>
            </span>
            <h1 class="explore-hero-title"><?php the_title(); ?></h1>
            <?php if ( $location ) : ?>
            <p class="explore-hero-sub"><?php echo esc_html( $location ); ?><?php if ( $region ) { echo ' · ' . esc_html( $region->name ); } ?></p>
            <?php endif; ?>
            <div class="ev-hero-facts">
                <?php if ( $dur )       : ?><span class="ev-hero-fact"><span class="ev-fact-icon">⏱️</span><?php echo esc_html( $dur ); ?></span><?php endif; ?>
                <?php if ( $trip_type ) : ?><span class="ev-hero-fact"><span class="ev-fact-icon">👥</span><?php echo esc_html( $trip_type ); ?></span><?php endif; ?>
                <?php if ( $grp_size )  : ?><span class="ev-hero-fact"><span class="ev-fact-icon">🧑‍🤝‍🧑</span><?php echo esc_html( $grp_size ); ?></span><?php endif; ?>
                <?php if ( $region )    : ?><span class="ev-hero-fact"><span class="ev-fact-icon">📍</span><a href="<?php echo esc_url( get_term_link( $region ) ); ?>"><?php echo esc_html( $region->name ); ?></a></span><?php endif; ?>
                <?php if ( $price )     : ?><span class="ev-hero-fact ev-hero-fact--price"><span class="ev-fact-icon">💰</span><?php echo esc_html( mytheme_format_rupee_amount( $price ) ); ?> <small>/ person</small></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container ev-single-wrap">
        <article class="ev-single-card">

            <div class="ev-single-body">

                <!-- 1. Description / overview first -->
                <?php if ( $overview ) : ?>
                <div class="ev-single-content ev-content-styled">
                    <?php echo wp_kses_post( $overview ); ?>
                </div>
                <?php endif; ?>

                <!-- 2. Trip details panel -->
                <?php
                $details = array_filter( array(
                    array( '🗺️', 'Route',       $route ),
                    array( '🌤️', 'Best Time',    $best_time ),
                    array( '👥', 'Group Size',   $grp_size ),
                    array( '🏷️', 'Trip Type',    $trip_type ),
                    array( '📍', 'Destination',  $location ),
                ), function( $d ) { return ! empty( $d[2] ); } );
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

                <!-- 3. Post editor content (inclusions, day-by-day, etc.) -->
                <?php if ( get_the_content() ) : ?>
                <div class="ev-single-content ev-content-styled" style="margin-top:24px;">
                    <?php the_content(); ?>
                </div>
                <?php endif; ?>

                <!-- 4. Enquiry form -->
                <section class="tw-package-enquiry" id="package-enquiry" style="margin-top:32px;">
                    <div class="tw-package-enquiry-copy">
                        <span>Interested in this package?</span>
                        <h2>Get a callback for <?php the_title(); ?></h2>
                        <p>Share your details and our travel expert will help with dates, pricing, inclusions and customisation.</p>
                    </div>
                    <?php if ( isset($_GET['package_enquiry']) && $_GET['package_enquiry'] === 'success' ) : ?>
                        <div class="tw-form-notice success">Thanks. Your enquiry has been received.</div>
                    <?php elseif ( isset($_GET['package_enquiry']) && $_GET['package_enquiry'] === 'error' ) : ?>
                        <div class="tw-form-notice error">Please fill your name and phone number.</div>
                    <?php endif; ?>
                    <form class="tw-package-enquiry-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
                        <input type="hidden" name="action"     value="mytheme_package_inquiry">
                        <input type="hidden" name="package_id" value="<?php echo esc_attr( $id ); ?>">
                        <?php wp_nonce_field('mytheme_package_inquiry','mytheme_package_inquiry_nonce'); ?>
                        <div class="tw-form-grid">
                            <label><span>Name *</span><input type="text"   name="name"   required></label>
                            <label><span>Phone *</span><input type="tel"   name="phone"  required></label>
                            <label><span>Email</span><input  type="email"  name="email"></label>
                            <label><span>Preferred Travel Date</span><input type="date" name="date"></label>
                            <label><span>Adults</span><input type="number" name="adults" min="1" value="1"></label>
                            <label><span>Budget</span>
                                <select name="budget">
                                    <option value="">Select budget range</option>
                                    <option>Budget - Under Rs. 25,000</option>
                                    <option>Mid-range - Rs. 25K–Rs. 60K</option>
                                    <option>Premium - Rs. 60K–Rs. 1.5L</option>
                                    <option>Luxury - Above Rs. 1.5L</option>
                                </select>
                            </label>
                            <label class="tw-form-full"><span>Message</span>
                                <textarea name="message" rows="4" placeholder="Tell us your travel dates, group size, or custom requests."></textarea>
                            </label>
                        </div>
                        <button type="submit">Send Enquiry</button>
                    </form>
                </section>

                <?php mytheme_render_faq_section( $id, 'Package FAQs' ); ?>

            </div><!-- /.ev-single-body -->

            <!-- Booking sidebar -->
            <aside class="ev-single-book">
                <?php if ( $price ) : ?>
                <div class="ev-single-price">
                    <span>Starts from</span>
                    <strong><?php echo esc_html( mytheme_format_rupee_amount( $price ) ); ?></strong>
                    <?php if ( $emi ) : ?><small><?php echo esc_html( $emi ); ?>/mo</small><?php endif; ?>
                    <small>per person</small>
                </div>
                <?php endif; ?>
                <?php
                $sidebar_rows = array_filter( array(
                    'Duration'    => $dur,
                    'Destination' => $location,
                    'Trip Type'   => $trip_type,
                    'Group Size'  => $grp_size,
                    'Best Time'   => $best_time,
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
                <a class="ev-single-btn ev-single-btn--ghost" href="#package-enquiry">Enquire Now</a>
            </aside>

        </article>
        <a class="tribe-back-link" href="<?php echo esc_url( $archive ); ?>">← All Packages</a>
    </div>

</main>

<?php
endwhile;
get_footer();
