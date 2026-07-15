<?php
/**
 * Group Trips archive — branded grid with region filter.
 *
 * @package my-theme
 */
get_header();

$tw_active_region = ! empty( $_GET['region'] ) ? sanitize_title( wp_unslash( $_GET['region'] ) ) : '';
$tw_region_term   = $tw_active_region ? get_term_by( 'slug', $tw_active_region, 'destination_region' ) : null;

if ( ! function_exists( 'tw_explore_card' ) ) {
    /* Fallback card (same markup as the taxonomy explore template) */
    function tw_explore_card() {
        $thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        ?>
        <article class="explore-card">
            <a class="explore-card-img" href="<?php the_permalink(); ?>">
                <?php if ( $thumb ) : ?><img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy"><?php else : ?><span class="explore-card-img--ph" aria-hidden="true"><i class="fi-rr-users" aria-hidden="true"></i></span><?php endif; ?>
                <span class="explore-card-type">Group Trip</span>
            </a>
            <div class="explore-card-body">
                <h3 class="explore-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="explore-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
                <a class="explore-card-link" href="<?php the_permalink(); ?>">View group trip →</a>
            </div>
        </article>
        <?php
    }
}
?>

<main class="main-content explore-page">

    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <span class="explore-hero-kicker">Travel together</span>
            <h1 class="explore-hero-title">Group <span style="color:#D83550">Trips</span></h1>
            <p class="explore-hero-sub">
                <?php
                echo $tw_region_term
                    ? 'Group departures in ' . esc_html( $tw_region_term->name )
                    : 'Join a like-minded crew on fixed-departure group adventures across India and beyond.';
                ?>
            </p>
        </div>
    </section>

    <div class="container explore-wrap">

        <?php if ( function_exists( 'tw_explore_region_filter_box' ) ) {
            tw_explore_region_filter_box( get_post_type_archive_link( 'group_trip' ) );
        } ?>

        <!-- Women's Trips callout -->
        <?php if ( function_exists( 'tw_womens_trips_page_url' ) ) : ?>
        <div class="gt-womens-callout">
            <span class="gt-womens-callout-icon"><i class="fi-rr-user" aria-hidden="true"></i></span>
            <div class="gt-womens-callout-copy">
                <strong>Looking for Women-Only Group Trips?</strong>
                <span>We have a dedicated section with safe, curated travel experiences exclusively for women.</span>
            </div>
            <a class="gt-womens-callout-btn" href="<?php echo esc_url( tw_womens_trips_page_url() ); ?>">
                Explore Women's Trips →
            </a>
        </div>
        <?php endif; ?>

        <?php if ( have_posts() ) : ?>
            <div class="explore-grid">
                <?php while ( have_posts() ) : the_post(); tw_explore_card(); endwhile; ?>
            </div>
            <div class="tribe-pagination">
                <?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '← Prev', 'next_text' => 'Next →' ) ); ?>
            </div>
        <?php else : ?>
            <div class="tribe-empty">
                <div class="tribe-empty-icon"><i class="fi-rr-users" aria-hidden="true"></i></div>
                <h3>No group trips here yet</h3>
                <p>We're lining up new group departures. Tell us where you want to go and we'll plan one.</p>
                <a class="tribe-btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><i class="fi-rr-plane" aria-hidden="true"></i> Plan a Trip — Free</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
