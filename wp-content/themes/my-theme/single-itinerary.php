<?php
/**
 * Single Itinerary — wide editorial layout: one continuous article column
 * with a sticky table of contents alongside it, rather than a stack of
 * separate boxed sections. Shares the .dest-* layout classes with
 * single-destination.php (see style.css → "single-page editorial layout").
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

    /* ── region ── */
    $regions = get_the_terms( $id, 'destination_region' );
    $region  = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;
    $thumb   = get_the_post_thumbnail_url( $id, 'full' );
    $archive = get_post_type_archive_link( 'itinerary' );

    /* Collect the day rows up-front so the contents list can mirror them
       without running the ACF loop twice. */
    $days = array();
    if ( function_exists( 'have_rows' ) && have_rows( 'itinerary_days', $id ) ) {
        while ( have_rows( 'itinerary_days', $id ) ) {
            the_row();
            $days[] = array(
                'title'      => get_sub_field( 'day_title' ),
                'details'    => get_sub_field( 'day_details' ),
                'route'      => get_sub_field( 'day_route' ),
                'stay'       => get_sub_field( 'day_stay' ),
                'meals'      => get_sub_field( 'day_meals' ),
                'transfer'   => get_sub_field( 'day_transfer' ),
                'highlights' => get_sub_field( 'day_highlights' ),
            );
        }
    }

    $excerpt      = get_the_excerpt();
    $body_content = trim( get_the_content() );
    $has_faqs     = function_exists( 'have_rows' ) && have_rows( 'faqs', $id );

    /* Single contents list covering the whole page, in render order. */
    $toc = array();
    if ( $excerpt )   { $toc[] = array( 'id' => 'itin-overview', 'label' => __( 'Overview', 'mytheme' ) ); }
    if ( $route_sum ) { $toc[] = array( 'id' => 'itin-route',    'label' => __( 'Route', 'mytheme' ) ); }
    if ( $days ) {
        $day_children = array();
        foreach ( $days as $d_i => $d ) {
            $day_children[] = array(
                'id'    => 'itin-day-' . ( $d_i + 1 ),
                'label' => $d['title'] ?: sprintf( __( 'Day %d', 'mytheme' ), $d_i + 1 ),
            );
        }
        $toc[] = array( 'id' => 'itin-days', 'label' => __( 'Day Wise Plan', 'mytheme' ), 'children' => $day_children );
    }
    if ( $body_content ) { $toc[] = array( 'id' => 'itin-more', 'label' => __( 'Good to Know', 'mytheme' ) ); }
    if ( $has_faqs )     { $toc[] = array( 'id' => 'itin-faqs', 'label' => __( 'FAQs', 'mytheme' ) ); }
    ?>

<main class="main-content explore-page dest-single">

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
            <h1 class="tw-h1"><?php the_title(); ?></h1>
            <?php if ( $dest_name || $region ) : ?>
            <p class="explore-hero-sub"><?php echo esc_html( $dest_name ?: '' ); ?><?php if ( $region && $dest_name ) { echo ' · '; } ?><?php if ( $region ) { echo esc_html( $region->name ); } ?></p>
            <?php endif; ?>
            <div class="ev-hero-facts">
                <span class="ev-hero-fact ev-hero-fact--kicker"><?php esc_html_e( 'Ready-made Itinerary', 'mytheme' ); ?></span>
                <?php if ( $duration )  : ?><span class="ev-hero-fact"><i class="fa-regular fa-clock ev-fact-icon"></i><?php echo esc_html( $duration ); ?></span><?php endif; ?>
                <?php if ( $best_time ) : ?><span class="ev-hero-fact"><i class="fa-solid fa-sun ev-fact-icon"></i><?php echo esc_html( $best_time ); ?></span><?php endif; ?>
                <?php if ( $region )    : ?><span class="ev-hero-fact"><i class="fa-solid fa-location-dot ev-fact-icon"></i><a href="<?php echo esc_url( get_term_link( $region ) ); ?>"><?php echo esc_html( $region->name ); ?></a></span><?php endif; ?>
                <?php if ( $price )     : ?><span class="ev-hero-fact ev-hero-fact--price"><i class="fa-solid fa-tag ev-fact-icon"></i><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹' . $price ); ?></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container dest-single-wrap">
        <div class="dest-single-layout">

            <!-- Sticky contents rail -->
            <aside class="dest-rail">
                <?php if ( $toc ) : ?>
                <nav class="dest-toc" aria-label="<?php esc_attr_e( 'On this page', 'mytheme' ); ?>">
                    <div class="dest-toc-title"><?php esc_html_e( 'Table of Contents', 'mytheme' ); ?></div>
                    <ol class="dest-toc-list">
                        <?php foreach ( $toc as $t_i => $t ) : ?>
                        <li>
                            <a href="#<?php echo esc_attr( $t['id'] ); ?>" data-dest-toc="<?php echo esc_attr( $t['id'] ); ?>">
                                <span class="dest-toc-num"><?php echo esc_html( $t_i + 1 ); ?></span>
                                <span class="dest-toc-label"><?php echo esc_html( $t['label'] ); ?></span>
                            </a>
                            <?php if ( ! empty( $t['children'] ) ) : ?>
                            <ol class="dest-toc-sublist">
                                <?php foreach ( $t['children'] as $c_i => $c ) : ?>
                                <li>
                                    <a href="#<?php echo esc_attr( $c['id'] ); ?>" data-dest-toc="<?php echo esc_attr( $c['id'] ); ?>">
                                        <span class="dest-toc-subnum"><?php echo esc_html( ( $t_i + 1 ) . '.' . ( $c_i + 1 ) ); ?></span>
                                        <span class="dest-toc-label"><?php echo esc_html( $c['label'] ); ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ol>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>

            </aside>

            <!-- One continuous article — no per-section boxes -->
            <article class="dest-article ev-content-styled">

                <?php if ( $excerpt ) : ?>
                <section id="itin-overview" class="dest-article-section">
                    <h2><?php esc_html_e( 'Overview', 'mytheme' ); ?></h2>
                    <div class="dest-lede"><p><?php echo esc_html( $excerpt ); ?></p></div>
                </section>
                <?php endif; ?>

                <?php if ( $route_sum ) : ?>
                <section id="itin-route" class="dest-article-section">
                    <h2><?php esc_html_e( 'Route', 'mytheme' ); ?></h2>
                    <?php echo wp_kses_post( wpautop( $route_sum ) ); ?>
                </section>
                <?php endif; ?>

                <?php if ( $days ) : ?>
                <section id="itin-days" class="dest-article-section">
                    <h2><?php esc_html_e( 'Day Wise Plan', 'mytheme' ); ?></h2>
                    <div class="itinerary-days itinerary-timeline dest-itin-days">
                        <?php foreach ( $days as $d_i => $d ) : $day_number = $d_i + 1; ?>
                        <details class="itinerary-day" id="itin-day-<?php echo esc_attr( $day_number ); ?>" <?php echo $day_number === 1 ? 'open' : ''; ?>>
                            <summary>
                                <span class="itinerary-day-marker"><?php echo esc_html( $day_number ); ?></span>
                                <span class="itinerary-day-summary">
                                    <strong><?php echo esc_html( $d['title'] ?: sprintf( __('Day %d','mytheme'), $day_number ) ); ?></strong>
                                    <?php
                                    $preview_text = wp_trim_words( wp_strip_all_tags( $d['details'] ), 18, '...' );
                                    if ( $d['route'] ) : ?><em><?php echo esc_html( $d['route'] ); ?></em>
                                    <?php elseif ( $preview_text ) : ?><em><?php echo esc_html( $preview_text ); ?></em><?php endif; ?>
                                </span>
                                <span class="itinerary-day-toggle" aria-hidden="true"></span>
                            </summary>
                            <div class="itinerary-day-panel">
                                <?php if ( $d['stay'] || $d['meals'] || $d['transfer'] || $d['highlights'] ) : ?>
                                <div class="itinerary-day-chips">
                                    <?php if ( $d['stay'] )       : ?><span><i class="fa-solid fa-bed"></i><?php echo esc_html( $d['stay'] ); ?></span><?php endif; ?>
                                    <?php if ( $d['meals'] )      : ?><span><i class="fa-solid fa-utensils"></i><?php echo esc_html( $d['meals'] ); ?></span><?php endif; ?>
                                    <?php if ( $d['transfer'] )   : ?><span><i class="fa-solid fa-route"></i><?php echo esc_html( $d['transfer'] ); ?></span><?php endif; ?>
                                    <?php if ( $d['highlights'] ) : ?><span><i class="fa-solid fa-star"></i><?php echo esc_html( $d['highlights'] ); ?></span><?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="itinerary-day-copy"><?php echo wp_kses_post( wpautop( $d['details'] ) ); ?></div>
                            </div>
                        </details>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ( $body_content ) : ?>
                <section id="itin-more" class="dest-article-section">
                    <h2><?php esc_html_e( 'Good to Know', 'mytheme' ); ?></h2>
                    <?php the_content(); ?>
                </section>
                <?php endif; ?>

                <!-- Enquiry form -->
                <?php
                get_template_part( 'template-parts/enquiry-form', null, array(
                    'type'    => 'itinerary',
                    'ref_id'  => $id,
                    'anchor'  => 'itinerary-enquiry',
                    'kicker'  => __( 'Interested in this itinerary?', 'mytheme' ),
                    'heading' => sprintf( __( 'Enquire about %s', 'mytheme' ), get_the_title() ),
                    'intro'   => __( 'Share your details and our travel expert will help with dates, pricing and booking.', 'mytheme' ),
                ) );
                ?>

                <?php if ( $has_faqs ) : ?>
                <section id="itin-faqs" class="dest-article-section dest-article-faqs">
                    <?php mytheme_render_faq_section( $id, __( 'Itinerary FAQs', 'mytheme' ) ); ?>
                </section>
                <?php endif; ?>

                <footer class="dest-article-cta">
                    <?php mytheme_render_trip_pdf_button( $id, __( 'Download PDF', 'mytheme' ) ); ?>
                    <a href="<?php echo esc_url( $archive ); ?>" class="btn-secondary"><?php esc_html_e( 'All Itineraries', 'mytheme' ); ?></a>
                </footer>

                <a class="tribe-back-link" href="<?php echo esc_url( $archive ); ?>">← <?php esc_html_e( 'All Itineraries', 'mytheme' ); ?></a>
            </article>

            <!-- Sticky booking rail: price + at-a-glance facts + CTAs -->
            <aside class="dest-booking">
                <div class="dest-rail-card">
                    <?php if ( $price ) : ?>
                    <div class="dest-rail-price">
                        <span><?php esc_html_e( 'Starts from', 'mytheme' ); ?></span>
                        <strong><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount( $price ) : '₹' . $price ); ?></strong>
                        <small><?php esc_html_e( 'per person', 'mytheme' ); ?></small>
                    </div>
                    <?php endif; ?>
                    <?php
                    $rail_rows = array_filter( array(
                        __( 'Duration', 'mytheme' )    => $duration,
                        __( 'Best Time', 'mytheme' )   => $best_time,
                        __( 'Destination', 'mytheme' ) => $dest_name,
                    ) );
                    if ( $rail_rows ) : ?>
                    <dl class="dest-rail-facts">
                        <?php foreach ( $rail_rows as $label => $val ) : ?>
                        <div class="dest-rail-fact">
                            <dt><?php echo esc_html( $label ); ?></dt>
                            <dd><?php echo esc_html( $val ); ?></dd>
                        </div>
                        <?php endforeach; ?>
                    </dl>
                    <?php endif; ?>
                    <a class="btn-primary btn-block" href="#itinerary-enquiry"><?php esc_html_e( 'Enquire', 'mytheme' ); ?></a>
                    <a class="btn-secondary btn-block" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><?php esc_html_e( 'Plan a Trip', 'mytheme' ); ?></a>
                </div>
                <?php mytheme_render_enquiry_popup_card(); ?>
            </aside>

        </div>
    </div>

</main>

<?php if ( $toc ) : ?>
<script>
/* Highlight the contents entry for whichever section is currently in view. */
(function () {
    var links = document.querySelectorAll('[data-dest-toc]');
    if (!links.length) { return; }

    var targets = [];
    links.forEach(function (link) {
        var el = document.getElementById(link.getAttribute('data-dest-toc'));
        if (el) { targets.push({ link: link, el: el }); }
    });
    if (!targets.length) { return; }

    function sync() {
        /* Measure against the viewport — offsetTop is relative to the nearest
           positioned ancestor, which is wrong inside this grid layout. */
        var current = targets[0];
        targets.forEach(function (t) {
            if (t.el.getBoundingClientRect().top <= 140) { current = t; }
        });
        targets.forEach(function (t) {
            t.link.classList.toggle('is-current', t === current);
        });
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
        if (ticking) { return; }
        ticking = true;
        window.requestAnimationFrame(function () { sync(); ticking = false; });
    }, { passive: true });
    sync();

    /* Clicking a day in the contents should open that accordion panel. */
    document.querySelectorAll('[data-dest-toc^="itin-day-"]').forEach(function (link) {
        link.addEventListener('click', function () {
            var panel = document.getElementById(link.getAttribute('data-dest-toc'));
            if (panel && panel.tagName === 'DETAILS') { panel.open = true; }
        });
    });
}());
</script>
<?php endif; ?>

<?php
endwhile;
get_footer();
