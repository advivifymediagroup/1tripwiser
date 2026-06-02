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
    'travel_package' => array( '🧳 Packages', 'Package' ),
    'itinerary'      => array( '🧭 Itineraries', 'Itinerary' ),
    'group_trip'     => array( '👥 Group Trips', 'Group Trip' ),
    'destination'    => array( '📍 Destinations', 'Destination' ),
    'post'           => array( '✍️ Blog & Stories', 'Story' ),
    'forum_topic'    => array( '💬 Tribe Discussions', 'Tribe' ),
);

if ( ! function_exists( 'tw_search_card' ) ) :
function tw_search_card( $post, $type_label ) {
    $thumb = get_the_post_thumbnail_url( $post->ID, 'medium' );
    ?>
    <article class="explore-card">
        <a class="explore-card-img" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
            <?php if ( $thumb ) : ?><img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy"><?php else : ?><span class="explore-card-img--ph" aria-hidden="true">🔎</span><?php endif; ?>
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

<main class="main-content explore-page">

    <header class="tw-results-head">
        <h1>Results for "<span><?php echo esc_html( $tw_q ); ?></span>"</h1>
        <p><?php echo esc_html( $tw_found ); ?> <?php echo ( 1 === $tw_found ) ? 'result' : 'results'; ?> across the site</p>
    </header>

    <div class="container explore-wrap">
        <?php if ( ! empty( $tw_groups ) ) : ?>
            <?php foreach ( $tw_meta as $pt => $info ) :
                if ( empty( $tw_groups[ $pt ] ) ) { continue; }
                $items = $tw_groups[ $pt ]; ?>
                <section class="tw-results-group">
                    <h2 class="tw-results-group-title">
                        <?php echo esc_html( $info[0] ); ?>
                        <span class="tw-results-badge"><?php echo esc_html( count( $items ) ); ?></span>
                    </h2>
                    <div class="explore-grid">
                        <?php foreach ( $items as $item ) { tw_search_card( $item, $info[1] ); } ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="tribe-empty" style="margin-top:36px;">
                <div class="tribe-empty-icon">🔎</div>
                <h3>No results for "<?php echo esc_html( $tw_q ); ?>"</h3>
                <p>Try a destination (Bali, Ladakh), a trip style (Honeymoon, Group), or an event (Oktoberfest).</p>
                <a class="tribe-btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">✈️ Plan a Trip — Free</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
