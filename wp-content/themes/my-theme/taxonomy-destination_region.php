<?php
/**
 * Explore archive — region (destination_region) and events (event_festival).
 * Shared by taxonomy-event_festival.php. Lists all trips tagged with the term.
 *
 * @package my-theme
 */
get_header();

$tw_term   = get_queried_object();
$tw_is_event = ( $tw_term instanceof WP_Term && $tw_term->taxonomy === 'event_festival' );
$tw_parent = ( $tw_term && $tw_term->parent ) ? get_term( $tw_term->parent, $tw_term->taxonomy ) : null;
$tw_kids   = $tw_term ? get_terms( array( 'taxonomy' => $tw_term->taxonomy, 'parent' => $tw_term->term_id, 'hide_empty' => false ) ) : array();
$tw_icon   = $tw_is_event ? tw_event_icon( $tw_term->term_id ) : tw_region_icon( $tw_term->term_id );
$tw_count  = $tw_term ? (int) $tw_term->count : 0;

/* Unified trip card for mixed post types */
if ( ! function_exists( 'tw_explore_card' ) ) :
function tw_explore_card() {
    $type_labels = array(
        'travel_package' => 'Package',
        'itinerary'      => 'Itinerary',
        'destination'    => 'Destination',
        'post'           => 'Story',
    );
    $pt    = get_post_type();
    $label = isset( $type_labels[ $pt ] ) ? $type_labels[ $pt ] : 'Trip';
    $thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
    ?>
    <article class="explore-card">
        <a class="explore-card-img" href="<?php the_permalink(); ?>">
            <?php if ( $thumb ) : ?>
                <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
            <?php else : ?>
                <span class="explore-card-img--ph" aria-hidden="true">🧭</span>
            <?php endif; ?>
            <span class="explore-card-type"><?php echo esc_html( $label ); ?></span>
        </a>
        <div class="explore-card-body">
            <h3 class="explore-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p class="explore-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
            <a class="explore-card-link" href="<?php the_permalink(); ?>">View <?php echo esc_html( strtolower( $label ) ); ?> →</a>
        </div>
    </article>
    <?php
}
endif;
?>

<main class="main-content explore-page">

    <!-- Hero -->
    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
                <span>›</span>
                <?php if ( $tw_parent ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $tw_parent ) ); ?>"><?php echo esc_html( $tw_parent->name ); ?></a>
                    <span>›</span>
                <?php endif; ?>
                <span><?php echo esc_html( $tw_term->name ); ?></span>
            </nav>
            <span class="explore-hero-kicker"><?php echo $tw_is_event ? 'Event &amp; Festival' : 'Explore Destination'; ?></span>
            <h1 class="explore-hero-title"><?php echo esc_html( trim( $tw_icon . ' ' . $tw_term->name ) ); ?></h1>
            <?php if ( ! empty( $tw_term->description ) ) : ?>
                <p class="explore-hero-sub"><?php echo esc_html( $tw_term->description ); ?></p>
            <?php endif; ?>
            <p class="explore-hero-count"><?php echo esc_html( $tw_count ); ?> <?php echo ( 1 === $tw_count ) ? 'trip' : 'trips'; ?> available</p>
        </div>
    </section>

    <div class="container explore-wrap">

        <!-- Sub-region / child chips (e.g. India → zones, Asia → destinations) -->
        <?php if ( $tw_kids && ! is_wp_error( $tw_kids ) ) : ?>
            <nav class="explore-chips" aria-label="Sub-regions">
                <?php foreach ( $tw_kids as $kid ) : ?>
                    <a class="explore-chip" href="<?php echo esc_url( get_term_link( $kid ) ); ?>"><?php echo esc_html( $kid->name ); ?></a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <!-- Trips grid -->
        <?php if ( have_posts() ) : ?>
            <div class="explore-grid">
                <?php while ( have_posts() ) : the_post(); tw_explore_card(); endwhile; ?>
            </div>
            <div class="tribe-pagination">
                <?php the_posts_pagination( array(
                    'mid_size'  => 1,
                    'prev_text' => '← Prev',
                    'next_text' => 'Next →',
                ) ); ?>
            </div>
        <?php else : ?>
            <div class="tribe-empty">
                <div class="tribe-empty-icon"><?php echo esc_html( $tw_icon ?: '🧭' ); ?></div>
                <h3>No trips here yet</h3>
                <p>We're curating trips for <?php echo esc_html( $tw_term->name ); ?>. Tell us your dream plan and we'll build it.</p>
                <a class="tribe-btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">✈️ Plan a Trip — Free</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
