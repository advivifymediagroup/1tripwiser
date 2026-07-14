<?php get_header(); ?>

<main class="main-content travel-archive explore-page">

    <!-- Hero (dark band — matches Blog + Affiliates) -->
    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span><span>Itineraries</span>
            </nav>
            <span class="explore-hero-kicker">Ready-made Routes</span>
            <h1 class="explore-hero-title">Itin<span style="color:#D83550">eraries</span></h1>
            <p class="explore-hero-sub">Browse day-wise routes, practical travel notes and inspiration for your next journey.</p>
        </div>
    </section>

    <div class="container explore-wrap">
        <?php
        if ( function_exists('tw_explore_unified_filter_box') ) {
            tw_explore_unified_filter_box('itinerary', get_post_type_archive_link('itinerary'));
        } else {
            mytheme_travel_filter_box('itinerary', 'itinerary_filter', get_post_type_archive_link('itinerary'));
        }
        ?>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-card travel-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="travel-meta">
                                <?php $duration = mytheme_get_travel_field('itinerary_duration'); ?>
                                <?php $best_time = mytheme_get_travel_field('itinerary_best_time'); ?>
                                <?php if ($duration) : ?><span><?php echo esc_html($duration); ?></span><?php endif; ?>
                                <?php if ($best_time) : ?><span><?php echo esc_html($best_time); ?></span><?php endif; ?>
                            </div>
                            <div class="post-excerpt">
                                <?php
                                $route_summary = mytheme_get_travel_field('itinerary_route_summary');
                                echo $route_summary ? wp_kses_post(wpautop($route_summary)) : get_the_excerpt();
                                ?>
                            </div>
                            <div class="itin-actions">
                                <a href="<?php the_permalink(); ?>" class="read-more">Open Itinerary</a>
                                <a href="<?php echo esc_url( mytheme_get_travel_field('book_url') ?: get_permalink() ); ?>" class="book-now-gold">Book Now</a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('Previous', 'mytheme'),
                    'next_text' => __('Next', 'mytheme'),
                )); ?>
            </div>
        <?php else : ?>
            <div class="no-posts">
                <h2>No itineraries found</h2>
                <p>Add your first itinerary from the WordPress dashboard.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
