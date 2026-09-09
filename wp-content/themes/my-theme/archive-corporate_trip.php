<?php
/**
 * Corporate Trips archive.
 *
 * @package my-theme
 */
get_header();

$tw_active_region = ! empty( $_GET['region'] ) ? sanitize_title( wp_unslash( $_GET['region'] ) ) : '';
$tw_region_term   = $tw_active_region ? get_term_by( 'slug', $tw_active_region, 'destination_region' ) : null;
?>

<main class="main-content explore-page">

    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>&rsaquo;</span><span>Corporate Trips</span>
            </nav>
            <span class="explore-hero-kicker">Teams travel better</span>
            <h1 class="tw-h1">Corporate <span class="accent-red">Trips</span></h1>
            <p class="explore-hero-sub">
                <?php
                echo $tw_region_term
                    ? 'Curated corporate offsites, incentive trips and leadership retreats in ' . esc_html( $tw_region_term->name ) . '.'
                    : 'Offsites, incentive journeys, annual meets and leadership retreats planned end-to-end for modern teams.';
                ?>
            </p>
        </div>
    </section>

    <div class="container explore-wrap">

        <?php if ( function_exists( 'tw_explore_region_filter_box' ) ) {
            tw_explore_region_filter_box( get_post_type_archive_link( 'corporate_trip' ) );
        } ?>

        <?php if ( have_posts() ) : ?>
            <div class="explore-grid">
                <?php while ( have_posts() ) : the_post();
                    $thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                    ?>
                    <article class="explore-card">
                        <a class="explore-card-img" href="<?php the_permalink(); ?>">
                            <?php if ( $thumb ) : ?>
                                <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
                            <?php else : ?>
                                <span class="explore-card-img--ph" aria-hidden="true"><i class="fi-rr-briefcase" aria-hidden="true"></i></span>
                            <?php endif; ?>
                            <span class="explore-card-type">Corporate Trip</span>
                        </a>
                        <div class="explore-card-body">
                            <h3 class="explore-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="explore-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?></p>
                            <a class="explore-card-link" href="<?php the_permalink(); ?>">View corporate trip &rarr;</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="tribe-pagination">
                <?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '&larr; Prev', 'next_text' => 'Next &rarr;' ) ); ?>
            </div>
        <?php else : ?>
            <div class="tribe-empty">
                <div class="tribe-empty-icon"><i class="fi-rr-briefcase" aria-hidden="true"></i></div>
                <h3>No corporate trips here yet</h3>
                <p>Share your team size, destination and goals. We will build the right offsite plan for you.</p>
                <a class="btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><i class="fi-rr-plane" aria-hidden="true"></i> Plan a Corporate Trip</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
