<?php
/**
 * Destination Region archive — Tripoto-style destination landing page.
 *
 * Shows all packages, group trips, events and itineraries tagged with
 * this region/country/state in one consolidated, filterable grid.
 *
 * @package my-theme
 */
get_header();

$term    = get_queried_object();
$parent  = ( $term && $term->parent ) ? get_term( $term->parent, 'destination_region' ) : null;
$icon    = function_exists( 'tw_region_icon' ) ? tw_region_icon( $term->term_id ) : '<i class="fi-rr-location-crosshairs" aria-hidden="true"></i>';
$kids    = get_terms( array( 'taxonomy' => 'destination_region', 'parent' => $term->term_id, 'hide_empty' => false ) );

/* ACF term meta */
$hero_img = function_exists( 'get_field' ) ? get_field( 'region_hero_image', $term ) : '';
$tagline  = function_exists( 'get_field' ) ? get_field( 'region_tagline',    $term ) : '';
$overview = function_exists( 'get_field' ) ? get_field( 'region_overview',   $term ) : '';
if ( ! $overview ) { $overview = $term->description; }

/* Count posts by type in this term (include children) */
function tw_count_type_in_term( $post_type, $term ) {
    $q = new WP_Query( array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => false,
        'tax_query'      => array( array(
            'taxonomy'         => 'destination_region',
            'field'            => 'term_id',
            'terms'            => $term->term_id,
            'include_children' => true,
        ) ),
    ) );
    return (int) $q->found_posts;
}
$counts = array(
    'travel_package' => tw_count_type_in_term( 'travel_package', $term ),
    'group_trip'     => tw_count_type_in_term( 'group_trip',     $term ),
    'tw_event'       => tw_count_type_in_term( 'tw_event',       $term ),
    'itinerary'      => tw_count_type_in_term( 'itinerary',      $term ),
);
$total = array_sum( $counts );
?>

<main class="main-content dest-page">

    <!-- ═══ CINEMATIC HERO ═══ -->
    <section class="dest-hero<?php echo $hero_img ? ' has-hero-img' : ''; ?>">
        <?php if ( $hero_img ) : ?>
        <div class="dest-hero-bg" id="dest-parallax-bg"
             style="background-image:url('<?php echo esc_url( $hero_img ); ?>')"></div>
        <?php endif; ?>
        <div class="dest-hero-overlay" aria-hidden="true"></div>
        <div class="container dest-hero-inner">
            <nav class="dest-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <?php if ( $parent && ! is_wp_error( $parent ) ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $parent ) ); ?>"><?php echo esc_html( $parent->name ); ?></a><span>›</span>
                <?php endif; ?>
                <span><?php echo esc_html( $term->name ); ?></span>
            </nav>

            <h1 class="dest-hero-title">
                <?php if ( $icon ) : ?><span class="dest-hero-icon" aria-hidden="true"><?php echo wp_kses_post( $icon ); ?></span><?php endif; ?>
                <?php echo esc_html( $term->name ); ?>
            </h1>

            <?php if ( $tagline ) : ?>
            <p class="dest-hero-tagline"><?php echo esc_html( $tagline ); ?></p>
            <?php endif; ?>

            <div class="dest-hero-stats">
                <div class="dest-stat"><strong><?php echo $total; ?></strong><span>Trips</span></div>
                <?php if ( $counts['travel_package'] ) : ?><div class="dest-stat-div"></div><div class="dest-stat"><strong><?php echo $counts['travel_package']; ?></strong><span>Packages</span></div><?php endif; ?>
                <?php if ( $counts['group_trip'] ) : ?><div class="dest-stat-div"></div><div class="dest-stat"><strong><?php echo $counts['group_trip']; ?></strong><span>Group Trips</span></div><?php endif; ?>
                <?php if ( $counts['tw_event'] ) : ?><div class="dest-stat-div"></div><div class="dest-stat"><strong><?php echo $counts['tw_event']; ?></strong><span>Events</span></div><?php endif; ?>
                <?php if ( $counts['itinerary'] ) : ?><div class="dest-stat-div"></div><div class="dest-stat"><strong><?php echo $counts['itinerary']; ?></strong><span>Itineraries</span></div><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container dest-wrap">

        <!-- Overview paragraph -->
        <?php if ( $overview ) : ?>
        <div class="dest-overview"><?php echo wp_kses_post( wpautop( $overview ) ); ?></div>
        <?php endif; ?>

        <!-- Sub-region chips -->
        <?php if ( $kids && ! is_wp_error( $kids ) ) : ?>
        <nav class="dest-sub-chips" aria-label="Sub-regions">
            <?php foreach ( $kids as $kid ) : ?>
                <a class="dest-sub-chip" href="<?php echo esc_url( get_term_link( $kid ) ); ?>">
                    <?php echo esc_html( $kid->name ); ?>
                    <span class="dest-sub-chip-count"><?php echo (int) $kid->count; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <!-- Tab filters -->
        <div class="dest-tabs" role="tablist">
            <button class="dest-tab active" data-filter="all" role="tab">All <span><?php echo $total; ?></span></button>
            <?php if ( $counts['travel_package'] ) : ?>
            <button class="dest-tab" data-filter="travel_package" role="tab">Packages <span><?php echo $counts['travel_package']; ?></span></button>
            <?php endif; ?>
            <?php if ( $counts['group_trip'] ) : ?>
            <button class="dest-tab" data-filter="group_trip" role="tab">Group Trips <span><?php echo $counts['group_trip']; ?></span></button>
            <?php endif; ?>
            <?php if ( $counts['itinerary'] ) : ?>
            <button class="dest-tab" data-filter="itinerary" role="tab">Itineraries <span><?php echo $counts['itinerary']; ?></span></button>
            <?php endif; ?>
            <?php if ( $counts['tw_event'] ) : ?>
            <button class="dest-tab" data-filter="tw_event" role="tab">Events <span><?php echo $counts['tw_event']; ?></span></button>
            <?php endif; ?>
        </div>

        <!-- Trip cards grid -->
        <?php if ( have_posts() ) : ?>
        <div class="dest-grid" id="dest-trips-grid">
            <?php while ( have_posts() ) : the_post();
                $pid      = get_the_ID();
                $pt       = get_post_type();
                $thumb    = get_the_post_thumbnail_url( $pid, 'medium_large' ) ?: get_post_meta( $pid, 'package_image_url', true );
                if ( ! $thumb ) {
                    $acf_img = function_exists('get_field') ? get_field('package_image', $pid) : null;
                    if ( is_array( $acf_img ) && isset( $acf_img['url'] ) ) { $thumb = $acf_img['url']; }
                    elseif ( is_string( $acf_img ) && $acf_img ) { $thumb = $acf_img; }
                }

                /* price */
                $price = '';
                foreach ( array( 'package_amount', '_starting_price', '_package_amount' ) as $_k ) {
                    $_v = get_post_meta( $pid, $_k, true );
                    if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
                }

                /* duration */
                $nights = (int) get_post_meta( $pid, 'total_nights', true );
                $days   = (int) get_post_meta( $pid, 'total_days', true );
                $dur    = $nights ? $nights . 'N / ' . ( $days ?: $nights + 1 ) . 'D' : '';
                if ( ! $dur ) {
                    $dur = get_post_meta( $pid, '_trip_duration', true )
                        ?: ( function_exists('mytheme_get_travel_field') ? mytheme_get_travel_field('itinerary_duration', $pid) : '' );
                }

                /* type badge */
                $badges = array(
                    'travel_package' => array( 'Package',    'var(--tw-red)', '#0d1526' ),
                    'group_trip'     => array( 'Group Trip',  '#1B93B0', '#fff'    ),
                    'tw_event'       => array( 'Event',       'var(--tw-red)', '#fff'    ),
                    'itinerary'      => array( 'Itinerary',  '#10b981', '#fff'    ),
                );
                $badge     = isset( $badges[$pt] ) ? $badges[$pt] : array( '<i class="fi-rr-plane" aria-hidden="true"></i> Trip', '#6b7a8f', '#fff' );
                $book_url  = get_post_meta( $pid, 'package_book_url', true ) ?: get_permalink( $pid );
                $tag_label = get_post_meta( $pid, 'package_tag', true );
            ?>
            <article class="dest-card" data-type="<?php echo esc_attr( $pt ); ?>">
                <a class="dest-card-img" href="<?php the_permalink(); ?>">
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
                    <?php else : ?>
                        <span class="dest-card-ph"><i class="fi-rr-location-crosshairs" aria-hidden="true"></i></span>
                    <?php endif; ?>
                    <span class="dest-card-badge"
                          style="background:<?php echo esc_attr($badge[1]); ?>;color:<?php echo esc_attr($badge[2]); ?>;">
                        <?php echo $badge[0]; ?>
                    </span>
                    <?php if ( $tag_label ) : ?>
                    <span class="dest-card-tag"><?php echo esc_html( ucfirst( $tag_label ) ); ?></span>
                    <?php endif; ?>
                </a>
                <div class="dest-card-body">
                    <h3 class="dest-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <div class="dest-card-meta">
                        <?php if ( $dur ) : ?><span class="dest-card-dur"><i class="fi-rr-clock" aria-hidden="true"></i> <?php echo esc_html( $dur ); ?></span><?php endif; ?>
                    </div>
                    <p class="dest-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16, '…' ) ); ?></p>
                    <div class="dest-card-foot">
                        <?php if ( $price ) : ?>
                        <div class="dest-card-price">
                            <span>Starts from</span>
                            <strong><?php echo esc_html( function_exists('mytheme_format_rupee_amount') ? mytheme_format_rupee_amount($price) : '₹'.$price ); ?></strong>
                        </div>
                        <?php endif; ?>
                        <a class="dest-card-btn" href="<?php echo esc_url( $book_url ); ?>">Book Now</a>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <div class="tribe-pagination">
            <?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '← Prev', 'next_text' => 'Next →' ) ); ?>
        </div>

        <?php else : ?>
        <div class="tribe-empty">
            <div class="tribe-empty-icon"><?php echo wp_kses_post( $icon ); ?></div>
            <h3>No trips for <?php echo esc_html( $term->name ); ?> yet</h3>
            <p>We're curating amazing trips here. Tell us where you want to go and we'll build a custom itinerary.</p>
            <a class="tribe-btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">Plan a Trip — Free</a>
        </div>
        <?php endif; ?>

    </div><!-- /.dest-wrap -->
</main>

<script>
(function () {
    /* ── Tab filter ── */
    var tabs  = document.querySelectorAll('.dest-tab');
    var cards = document.querySelectorAll('.dest-card');
    if (tabs.length && cards.length) {
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');
                var filter = tab.getAttribute('data-filter');
                cards.forEach(function (card) {
                    card.style.display = (filter === 'all' || card.getAttribute('data-type') === filter) ? '' : 'none';
                });
            });
        });
    }

    /* ── Parallax: translate the bg-div instead of background-position ── */
    var bg = document.getElementById('dest-parallax-bg');
    if (!bg) { return; }
    var hero = bg.parentElement;
    var heroH = hero ? hero.offsetHeight : 600;
    function onScroll() {
        var s = window.pageYOffset || document.documentElement.scrollTop;
        if (s > heroH * 1.8) { return; }
        bg.style.transform = 'translateY(' + Math.round(s * 0.38) + 'px)';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}());
</script>

<?php get_footer(); ?>
