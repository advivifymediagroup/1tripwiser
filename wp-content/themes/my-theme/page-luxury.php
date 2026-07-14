<?php
/**
 * Template Name: LUXURY
 *
 * Sprint template — pure-black full-viewport hero with a single billboard
 * headline anchored to the bottom, plus the full LUXE landing content below
 * (why cards, journey collection, enquiry form) reusing the /luxe/ page's
 * already-black/gold-styled sections. Each headline character is its own
 * <tspan> so tw-luxury.js can scrub a fall/fade/squish transform per-letter,
 * driven directly by scroll position (see tw-luxury.js for the math).
 * Intended to replace the existing /luxe/ landing once content is dialled in.
 *
 * @package my-theme
 */
get_header();

$tw_luxury_headline   = 'LUXE';
$tw_luxury_chars      = preg_split( '//u', $tw_luxury_headline, -1, PREG_SPLIT_NO_EMPTY );
// Gold-accent the final word — with no space in "LUXE" this golds the whole word.
$tw_luxury_last_space = mb_strrpos( $tw_luxury_headline, ' ' );
$tw_luxury_gold_start = $tw_luxury_last_space !== false ? $tw_luxury_last_space + 1 : 0;

$tw_luxury_trips_query = new WP_Query( array(
    'post_type'      => 'tw_luxe',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
$tw_luxury_has_trips = $tw_luxury_trips_query->have_posts();
?>

<main class="tw-luxury-page">

    <?php /* Scrolly wrapper: the video stage is sticky and keeps scrubbing until
             this wrapper — hero, why cards AND the journeys grid — has fully
             scrolled past. Content layers scroll over the pinned video. */ ?>
    <div class="tw-luxury-scrolly">

        <div class="tw-luxury-stage" aria-hidden="true">
            <!-- Scroll-scrubbed background video — driven entirely by tw-luxury.js, not autoplaying on its own timeline. -->
            <video class="tw-luxury-video" muted playsinline preload="auto">
                <source src="<?php echo esc_url( get_template_directory_uri() . '/assets/video/tw-luxury-hero.mp4' ); ?>" type="video/mp4">
            </video>
            <div class="tw-luxury-stage-shade"></div>
        </div>

        <div class="tw-luxury-layers">

    <section class="tw-luxury-hero" aria-labelledby="tw-luxury-title">

        <!-- Hairline corner frame — echoes the gold hairline borders used across /luxe/ -->
        <div class="tw-luxury-frame" aria-hidden="true">
            <span class="tw-luxury-corner tw-luxury-corner--tl"></span>
            <span class="tw-luxury-corner tw-luxury-corner--tr"></span>
        </div>

        <div class="tw-luxury-kicker-wrap" aria-hidden="true">
            <span class="tw-luxury-kicker">By Invitation</span>
        </div>

        <!-- Mid-hero copy — fills the space between the kicker and the headline -->
        <div class="tw-luxury-hero-copy">
            <p class="tw-luxury-hero-line">A quieter way to travel. Rare journeys, composed for one party at a time &mdash; and never repeated.</p>
            <p class="tw-luxury-hero-sub">Crewed Yachts &middot; Charter Rail &middot; Private Islands &middot; Utter Discretion</p>
        </div>

        <div class="tw-luxury-hero-content">
            <span class="tw-luxury-divider" aria-hidden="true"></span>
            <svg class="tw-luxury-headline"
                 viewBox="0 0 1000 150"
                 preserveAspectRatio="xMidYMax meet"
                 xmlns="http://www.w3.org/2000/svg"
                 role="img"
                 aria-labelledby="tw-luxury-title">
                <title id="tw-luxury-title"><?php echo esc_html( $tw_luxury_headline ); ?></title>
                <text x="500" y="132"
                      text-anchor="middle"
                      fill="#ffffff"
                      font-family="'Michroma','Bebas Neue','Impact','Arial Narrow',sans-serif"
                      font-size="120"
                      font-weight="400"
                      textLength="480"
                      lengthAdjust="spacing"
                      xml:space="preserve"
                      aria-hidden="true"><?php
                    foreach ( $tw_luxury_chars as $tw_i => $tw_ch ) {
                        $tw_gold = $tw_i >= $tw_luxury_gold_start;
                        printf(
                            '<tspan class="tw-luxury-char%s" data-i="%d">%s</tspan>',
                            $tw_gold ? ' tw-luxury-char--gold' : '',
                            (int) $tw_i,
                            esc_html( $tw_ch )
                        );
                    }
                ?></text>
            </svg>
        </div>
    </section>

    <!-- MIDDLE TEXT SECTION — floats over the still-scrubbing video -->
    <section class="tw-luxury-mid">
        <div class="container">
        <div class="tw-luxe-why">
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-feather tw-luxe-why-icon"></i>
                <h3>Hand-Crafted</h3>
                <p>Each journey is composed for one party. No templates, no group catalogues — only a designer working alongside you.</p>
            </div>
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-anchor tw-luxe-why-icon"></i>
                <h3>Private Access</h3>
                <p>Crewed yachts in the Mediterranean. Light jets between coordinates. After-hours visits to museums, cellars and ateliers.</p>
            </div>
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-wine-glass tw-luxe-why-icon"></i>
                <h3>The Finer Table</h3>
                <p>Reservations at restaurants without phone numbers. Chefs to your villa. Private tastings with vintners on the estate.</p>
            </div>
            <div class="tw-luxe-why-card">
                <i class="fa-solid fa-shield-halved tw-luxe-why-icon"></i>
                <h3>Quiet, Always</h3>
                <p>Optional NDAs on every engagement. No social posts. No press. What happens on your journey remains with you.</p>
            </div>
        </div>
        </div>
    </section>

        </div><!-- /.tw-luxury-layers -->
    </div><!-- /.tw-luxury-scrolly — video stage unpins here, once the card reveals are done -->

    <!-- JOURNEYS — on solid black, after the video sequence ends -->
    <section class="tw-luxury-collection">
        <div class="container">

        <div class="tw-luxe-section-head">
            <p>The Collection</p>
            <h2>Hand-picked Journeys</h2>
        </div>

        <?php if ( $tw_luxury_has_trips ) : ?>
        <div class="tw-luxe-trips-grid">
            <?php while ( $tw_luxury_trips_query->have_posts() ) : $tw_luxury_trips_query->the_post();
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
                            <small>Invitation From</small>
                            <strong class="tw-luxe-trip-price"><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹'.$price ); ?></strong>
                        </div>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="tw-luxe-trip-cta">Begin <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php else : ?>
        <div class="tw-luxe-empty">
            <i class="fa-solid fa-feather"></i>
            <h3>The next collection is being composed</h3>
            <p>Our private travel team is shaping the next set of journeys. In the meantime, share where you'd like to be — and we'll design something just for you.</p>
            <a class="tw-luxe-hero-btn" href="#luxury-enquiry">Begin Your Journey</a>
        </div>
        <?php endif; ?>

        </div>
    </section>

    <div class="container tw-luxe-wrap">

        <!-- ENQUIRY FORM -->
        <section class="tw-luxe-enquiry" id="luxury-enquiry">
            <div class="tw-luxe-enquiry-copy">
                <span class="tw-luxe-kicker">Private Enquiry</span>
                <h2>Tell us where, and we'll do the rest</h2>
                <p>Our private travel team responds within four hours. All enquiries held in confidence.</p>
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
