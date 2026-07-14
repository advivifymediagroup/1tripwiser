<?php get_header(); ?>

<main class="main-content travel-archive explore-page dest-archive">

    <!-- Hero (matches Packages archive) -->
    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span><span>Destinations</span>
            </nav>
            <span class="explore-hero-kicker">Explore by Place</span>
            <h1 class="explore-hero-title">Dest<span style="color:#D83550">inations</span></h1>
            <p class="explore-hero-sub">Browse destinations, best seasons, ideal trip lengths and package starting prices.</p>
        </div>
    </section>

    <div class="container explore-wrap">

        <?php if ( have_posts() ) : ?>
            <div class="posts-grid">
                <?php while ( have_posts() ) : the_post();
                    $destination = mytheme_get_destination_data();
                    $image_url   = mytheme_get_image_url( $destination['image'], 'large' );
                    if ( ! $image_url && has_post_thumbnail() ) {
                        $image_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                    }
                    $intro = $destination['short_intro'] ? wp_strip_all_tags( $destination['short_intro'] ) : get_the_excerpt();
                    ?>
                    <article class="post-card package-card destination-card">
                        <div class="package-media">
                            <a href="<?php the_permalink(); ?>">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                <?php endif; ?>
                            </a>
                            <span class="package-tag">Destination</span>
                        </div>
                        <div class="package-content">
                            <?php if ( $destination['country'] ) : ?>
                                <span class="package-location"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $destination['country'] ); ?></span>
                            <?php endif; ?>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="package-facts">
                                <?php if ( $destination['best_time'] )      : ?><span><i class="fa-regular fa-sun"></i> <?php echo esc_html( $destination['best_time'] ); ?></span><?php endif; ?>
                                <?php if ( $destination['ideal_duration'] ) : ?><span><i class="fa-regular fa-clock"></i> <?php echo esc_html( $destination['ideal_duration'] ); ?></span><?php endif; ?>
                            </div>
                            <p class="dest-card-excerpt"><?php echo esc_html( $intro ); ?></p>
                            <div class="package-price-row">
                                <div>
                                    <?php if ( $destination['starting_price'] ) : ?>
                                        <small>Packages from</small>
                                        <strong><?php echo esc_html( $destination['starting_price'] ); ?></strong>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="book-now-btn">Explore</a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( 'Previous', 'mytheme' ),
                    'next_text' => __( 'Next', 'mytheme' ),
                ) ); ?>
            </div>
        <?php else : ?>
            <div class="no-posts">
                <h2>No destinations found</h2>
                <p>Add your first destination from the WordPress dashboard.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
