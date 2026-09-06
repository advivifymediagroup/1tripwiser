<?php
/**
 * Universal search results — groups results by content type so a query like
 * "Bali" shows packages, itineraries, group trips, destinations, blogs and
 * Tribe topics together. Query is expanded in includes/explore.php.
 *
 * @package my-theme
 */
get_header();

$tw_q     = get_search_query();
$tw_found = (int) $GLOBALS['wp_query']->found_posts;

/* Bucket the results by post type */
$tw_groups = array();
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        $tw_groups[ get_post_type() ][] = get_post();
    }
    wp_reset_postdata();
}

/* Display order + labels/icons for each content type */
$tw_meta = array(
    'travel_package' => array( 'icon' => 'suitcase-alt',         'label' => 'Packages',          'type' => 'Package' ),
    'itinerary'      => array( 'icon' => 'location-crosshairs',  'label' => 'Itineraries',        'type' => 'Itinerary' ),
    'group_trip'     => array( 'icon' => 'users',                'label' => 'Group Trips',        'type' => 'Group Trip' ),
    'corporate_trip' => array( 'icon' => 'briefcase',            'label' => 'Corporate Trips',    'type' => 'Corporate Trip' ),
    'destination'    => array( 'icon' => 'marker',                'label' => 'Destinations',       'type' => 'Destination' ),
    'post'           => array( 'icon' => 'edit-alt',             'label' => 'Blog & Stories',      'type' => 'Story' ),
    'forum_topic'    => array( 'icon' => 'comment',              'label' => 'Tribe Discussions',   'type' => 'Tribe' ),
);

if ( ! function_exists( 'tw_search_card' ) ) :
function tw_search_card( $post, $type_label ) {
    $thumb = get_the_post_thumbnail_url( $post->ID, 'large' );
    ?>
    <article class="explore-card">
        <a class="explore-card-img" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
            <?php if ( $thumb ) : ?><img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy"><?php else : ?><span class="explore-card-img--ph" aria-hidden="true"><i class="fi-rr-search" aria-hidden="true"></i></span><?php endif; ?>
            <span class="explore-card-type"><?php echo esc_html( $type_label ); ?></span>
        </a>
        <div class="explore-card-body">
            <h3 class="explore-card-title"><a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><?php echo esc_html( get_the_title( $post->ID ) ); ?></a></h3>
            <p class="explore-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post->ID ), 18, '…' ) ); ?></p>
            <a class="explore-card-link" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">View →</a>
        </div>
    </article>
    <?php
}
endif;
?>

<main class="main-content explore-page tw-search-page">

    <header class="tw-results-head">
        <h1>Results for "<span><?php echo esc_html( $tw_q ); ?></span>"</h1>
        <p><?php echo esc_html( $tw_found ); ?> <?php echo ( 1 === $tw_found ) ? 'result' : 'results'; ?> across the site</p>
    </header>

    <div class="container explore-wrap tw-search-layout">

        <?php if ( ! empty( $tw_groups ) ) : ?>

            <!-- Floating category nav -->
            <aside class="tw-search-nav" id="tw-search-nav">
                <div class="tw-search-nav-head">Jump to</div>
                <ul class="tw-search-nav-list">
                    <?php foreach ( $tw_meta as $pt => $info ) :
                        if ( empty( $tw_groups[ $pt ] ) ) { continue; }
                        $count   = count( $tw_groups[ $pt ] );
                        $section = 'tw-cat-' . sanitize_html_class( $pt );
                    ?>
                    <li>
                        <a href="#<?php echo esc_attr( $section ); ?>" data-section="<?php echo esc_attr( $section ); ?>">
                            <span class="tw-search-nav-icon"><i class="fi-rr-<?php echo esc_attr( $info['icon'] ); ?>" aria-hidden="true"></i></span>
                            <span class="tw-search-nav-label"><?php echo esc_html( $info['label'] ); ?></span>
                            <span class="tw-search-nav-badge"><?php echo (int) $count; ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="#" class="tw-search-nav-top">↑ Back to top</a>
            </aside>

            <!-- Results column -->
            <div class="tw-search-results">
                <?php foreach ( $tw_meta as $pt => $info ) :
                    if ( empty( $tw_groups[ $pt ] ) ) { continue; }
                    $items   = $tw_groups[ $pt ];
                    $section = 'tw-cat-' . sanitize_html_class( $pt ); ?>
                    <section class="tw-results-group" id="<?php echo esc_attr( $section ); ?>" data-section="<?php echo esc_attr( $section ); ?>">
                        <h2 class="tw-results-group-title">
                            <i class="fi-rr-<?php echo esc_attr( $info['icon'] ); ?>" aria-hidden="true"></i> <?php echo esc_html( $info['label'] ); ?>
                            <span class="tw-results-badge"><?php echo esc_html( count( $items ) ); ?></span>
                        </h2>
                        <div class="explore-grid">
                            <?php foreach ( $items as $item ) { tw_search_card( $item, $info['type'] ); } ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

        <?php else : ?>
            <div class="tribe-empty" style="margin-top:36px;">
                <div class="tribe-empty-icon"><i class="fi-rr-search" aria-hidden="true"></i></div>
                <h3>No results for "<?php echo esc_html( $tw_q ); ?>"</h3>
                <p>Try a destination (Bali, Ladakh), a trip style (Honeymoon, Group), or an event (Oktoberfest).</p>
                <a class="tribe-btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><i class="fi-rr-plane" aria-hidden="true"></i> Plan a Trip — Free</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
/* Smooth scroll + active highlight for the floating nav */
(function () {
    var nav   = document.getElementById('tw-search-nav');
    if (!nav) { return; }
    var links = nav.querySelectorAll('a[data-section]');
    var headerOffset = 110;  /* sticky header height */

    /* Smooth scroll on click */
    links.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var id = link.getAttribute('data-section');
            var target = document.getElementById(id);
            if (!target) { return; }
            e.preventDefault();
            var y = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });
            history.replaceState(null, '', '#' + id);
        });
    });

    /* Back to top */
    var topBtn = nav.querySelector('.tw-search-nav-top');
    if (topBtn) {
        topBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* IntersectionObserver — auto-highlight active section as user scrolls */
    if ('IntersectionObserver' in window) {
        var sections = document.querySelectorAll('.tw-results-group[data-section]');
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var id = entry.target.getAttribute('data-section');
                    links.forEach(function (l) {
                        l.classList.toggle('active', l.getAttribute('data-section') === id);
                    });
                }
            });
        }, { rootMargin: '-120px 0px -55% 0px', threshold: 0 });
        sections.forEach(function (s) { io.observe(s); });
    }

    /* If page loaded with a hash, smooth-scroll to it after render */
    if (window.location.hash) {
        var target = document.getElementById(window.location.hash.slice(1));
        if (target) {
            setTimeout(function () {
                var y = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
            }, 100);
        }
    }
}());
</script>

<?php get_footer(); ?>
