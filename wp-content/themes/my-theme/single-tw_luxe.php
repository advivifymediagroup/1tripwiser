<?php
/**
 * Single LUXE Journey — dark, photography-led, magazine-style template.
 *
 * Deliberately different from every other CPT template: no orange "Book Now"
 * pill, no white card stack. Black canvas, gold accents, italic serif headlines,
 * hairline dividers and a single "Begin a Private Enquiry" CTA.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id = get_the_ID();

    /* ── fields ── */
    $location  = get_post_meta( $id, 'package_location', true );
    $departure = get_post_meta( $id, 'event_date', true );
    $grp_size  = get_post_meta( $id, 'group_size', true );
    $trip_type = get_post_meta( $id, 'package_trip_type', true );
    $tag       = get_post_meta( $id, 'package_tag', true );
    $overview  = get_post_meta( $id, 'package_overview', true );
    $route     = get_post_meta( $id, 'route_summary', true );
    $best_time = get_post_meta( $id, 'best_time', true );

    $nights = (int) get_post_meta( $id, 'total_nights', true );
    $days   = (int) get_post_meta( $id, 'total_days', true );
    $dur    = $nights ? $nights . ' Nights ' . ( $days ?: $nights + 1 ) . ' Days' : '';

    /* price */
    $price = '';
    foreach ( array( 'package_amount', '_package_amount', '_starting_price', 'starting_price' ) as $_k ) {
        $_v = get_post_meta( $id, $_k, true );
        if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
    }

    $thumb       = get_the_post_thumbnail_url( $id, 'full' );
    $archive_url = function_exists( 'tw_luxe_page_url' ) ? tw_luxe_page_url() : home_url( '/luxe/' );
    ?>

<main class="main-content tw-luxe-page tw-luxe-single">

    <!-- HERO -->
    <section class="tw-luxe-single-hero<?php echo $thumb ? ' has-img' : ''; ?>"
             <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
        <div class="tw-luxe-single-hero-overlay" aria-hidden="true"></div>
        <div class="container tw-luxe-single-hero-inner">
            <nav class="tw-luxe-single-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a>
                <span>·</span>
                <a href="<?php echo esc_url( $archive_url ); ?>">LUXE</a>
                <span>·</span>
                <span><?php echo esc_html( wp_trim_words( get_the_title(), 6, '' ) ); ?></span>
            </nav>

            <?php if ( $trip_type ) : ?>
            <span class="tw-luxe-single-kicker"><?php echo esc_html( strtoupper( $trip_type ) ); ?></span>
            <?php endif; ?>

            <h1 class="tw-luxe-single-title"><?php the_title(); ?></h1>

            <?php if ( $location ) : ?>
            <p class="tw-luxe-single-location"><?php echo esc_html( $location ); ?></p>
            <?php endif; ?>

            <div class="tw-luxe-single-meta">
                <?php if ( $dur ) : ?>
                <div class="tw-luxe-single-meta-item">
                    <span class="lbl">Duration</span>
                    <span class="val"><?php echo esc_html( $dur ); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $grp_size ) : ?>
                <div class="tw-luxe-single-meta-item">
                    <span class="lbl">Party</span>
                    <span class="val"><?php echo esc_html( $grp_size ); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $departure ) : ?>
                <div class="tw-luxe-single-meta-item">
                    <span class="lbl">Departure</span>
                    <span class="val"><?php echo esc_html( $departure ); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $best_time ) : ?>
                <div class="tw-luxe-single-meta-item">
                    <span class="lbl">Best Time</span>
                    <span class="val"><?php echo esc_html( $best_time ); ?></span>
                </div>
                <?php endif; ?>
                <div class="tw-luxe-single-meta-item">
                    <span class="lbl">Invitation From</span>
                    <span class="val">
                        <?php if ( $price ) {
                            echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹'.$price );
                        } else {
                            echo 'By Private Enquiry';
                        } ?>
                    </span>
                </div>
            </div>

            <a class="tw-luxe-single-hero-cta" href="#luxe-single-enquiry">Begin a Private Enquiry</a>
        </div>
    </section>

    <!-- BODY -->
    <div class="container tw-luxe-single-wrap">

        <!-- Overview / lede -->
        <?php if ( $overview ) : ?>
        <section class="tw-luxe-single-overview">
            <?php echo wp_kses_post( wpautop( $overview ) ); ?>
        </section>
        <?php endif; ?>

        <!-- Editor content (the journey notes) -->
        <?php if ( get_the_content() ) : ?>
        <section class="tw-luxe-single-content">
            <?php the_content(); ?>
        </section>
        <?php endif; ?>

        <!-- Route timeline (if filled) -->
        <?php if ( $route ) : ?>
        <section class="tw-luxe-single-route">
            <p class="tw-luxe-single-section-label">The Route</p>
            <h2 class="tw-luxe-single-section-title"><?php echo esc_html( $route ); ?></h2>
        </section>
        <?php endif; ?>

        <!-- Signature highlights -->
        <?php $highlights = get_post_meta( $id, 'luxe_highlights', true ); ?>
        <?php if ( $highlights ) : ?>
        <section class="tw-luxe-single-list-block">
            <p class="tw-luxe-single-section-label">Signature</p>
            <h2 class="tw-luxe-single-section-title">Highlights</h2>
            <ul class="tw-luxe-single-list">
                <?php foreach ( preg_split( '/\r\n|\r|\n|<br ?\/?>/', $highlights ) as $line ) :
                    $line = trim( wp_strip_all_tags( $line ) );
                    if ( $line === '' ) continue;
                ?>
                <li><i class="fa-solid fa-diamond"></i><span><?php echo esc_html( $line ); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <!-- Inclusions -->
        <?php $inclusions = get_post_meta( $id, 'luxe_inclusions', true ); ?>
        <?php if ( $inclusions ) : ?>
        <section class="tw-luxe-single-list-block">
            <p class="tw-luxe-single-section-label">Privately Curated</p>
            <h2 class="tw-luxe-single-section-title">What's Included</h2>
            <ul class="tw-luxe-single-list">
                <?php foreach ( preg_split( '/\r\n|\r|\n|<br ?\/?>/', $inclusions ) as $line ) :
                    $line = trim( wp_strip_all_tags( $line ) );
                    if ( $line === '' ) continue;
                ?>
                <li><i class="fa-solid fa-check"></i><span><?php echo esc_html( $line ); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <!-- Day-wise plan from ACF repeater (if used) -->
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
        <section class="tw-luxe-single-days">
            <p class="tw-luxe-single-section-label">The Itinerary</p>
            <h2 class="tw-luxe-single-section-title">Day by Day</h2>
            <?php $day_number = 1; ?>
            <?php while ( have_rows( $tw_days_field ) ) : the_row();
                $day_title   = get_sub_field( 'day_title' );
                $day_details = get_sub_field( 'day_details' );
                $day_route   = get_sub_field( 'day_route' );
            ?>
            <article class="tw-luxe-day">
                <span class="tw-luxe-day-num"><?php echo esc_html( sprintf( '%02d', $day_number ) ); ?></span>
                <div class="tw-luxe-day-copy">
                    <h3><?php echo esc_html( $day_title ?: sprintf( __('Day %d','mytheme'), $day_number ) ); ?></h3>
                    <?php if ( $day_route ) : ?><p class="tw-luxe-day-route"><?php echo esc_html( $day_route ); ?></p><?php endif; ?>
                    <?php if ( $day_details ) : ?><div class="tw-luxe-day-detail"><?php echo wp_kses_post( wpautop( $day_details ) ); ?></div><?php endif; ?>
                </div>
            </article>
            <?php $day_number++; endwhile; ?>
        </section>
        <?php endif; ?>

        <!-- Enquiry -->
        <section class="tw-luxe-single-enquiry" id="luxe-single-enquiry">
            <div class="tw-luxe-single-enquiry-copy">
                <p class="tw-luxe-single-section-label">Private Enquiry</p>
                <h2 class="tw-luxe-single-section-title">Begin your journey</h2>
                <p class="tw-luxe-single-enquiry-sub">Share where you'd like to go and when. We'll respond within four hours — privately, by phone or email.</p>
            </div>
            <form class="tw-luxe-enquiry-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
                <input type="hidden" name="action" value="mytheme_package_inquiry">
                <input type="hidden" name="package_id" value="<?php echo (int) $id; ?>">
                <?php wp_nonce_field( 'mytheme_package_inquiry', 'mytheme_package_inquiry_nonce' ); ?>
                <div class="tw-luxe-form-grid">
                    <label><span>Name</span><input type="text" name="name" required></label>
                    <label><span>Phone</span><input type="tel" name="phone" required></label>
                    <label><span>Email</span><input type="email" name="email"></label>
                    <label><span>Preferred Travel Window</span><input type="text" name="date" placeholder="e.g. October 2026"></label>
                    <label class="tw-luxe-form-full"><span>Tell us about your trip</span><textarea name="message" rows="4" placeholder="Dates, party size, dietary needs, occasions, special requests…"></textarea></label>
                </div>
                <button type="submit" class="tw-luxe-submit">Send Enquiry</button>
            </form>
        </section>

        <a class="tw-luxe-single-back" href="<?php echo esc_url( $archive_url ); ?>">&larr; All LUXE Journeys</a>

    </div>
</main>

<?php
endwhile;
get_footer();
