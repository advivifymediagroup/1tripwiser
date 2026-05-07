<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $duration = mytheme_get_travel_field('itinerary_duration');
            $best_time = mytheme_get_travel_field('itinerary_best_time');
            $route_summary = mytheme_get_travel_field('itinerary_route_summary');
            $destination = mytheme_get_travel_field('itinerary_destination');
            ?>
            <article class="single-post travel-single">
                <header class="post-header travel-single-header">
                    <span>Itinerary</span>
                    <h1><?php the_title(); ?></h1>
                    <div class="travel-meta header-meta">
                        <?php if ($destination && isset($destination->post_title)) : ?><span><?php echo esc_html($destination->post_title); ?></span><?php endif; ?>
                        <?php if ($duration) : ?><span><?php echo esc_html($duration); ?></span><?php endif; ?>
                        <?php if ($best_time) : ?><span><?php echo esc_html($best_time); ?></span><?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail travel-hero-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="travel-detail-grid">
                    <?php if ($destination && isset($destination->post_title)) : ?><div class="travel-detail"><span>Destination</span><strong><?php echo esc_html($destination->post_title); ?></strong></div><?php endif; ?>
                    <?php if ($duration) : ?><div class="travel-detail"><span>Duration</span><strong><?php echo esc_html($duration); ?></strong></div><?php endif; ?>
                    <?php if ($best_time) : ?><div class="travel-detail"><span>Best Time</span><strong><?php echo esc_html($best_time); ?></strong></div><?php endif; ?>
                </div>

                <?php if ($route_summary) : ?>
                    <div class="post-content itinerary-summary">
                        <?php echo wp_kses_post(wpautop($route_summary)); ?>
                    </div>
                <?php endif; ?>

                <?php if (function_exists('have_rows') && have_rows('itinerary_days')) : ?>
                    <section class="itinerary-days">
                        <h2>Day Wise Plan</h2>
                        <?php while (have_rows('itinerary_days')) : the_row(); ?>
                            <article class="itinerary-day">
                                <h3><?php echo esc_html(get_sub_field('day_title')); ?></h3>
                                <div><?php echo wp_kses_post(wpautop(get_sub_field('day_details'))); ?></div>
                            </article>
                        <?php endwhile; ?>
                    </section>
                <?php endif; ?>

                <footer class="post-footer travel-cta">
                    <!-- <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-primary">Customize This Route</a> -->
                    <a href="<?php echo esc_url(get_post_type_archive_link('itinerary')); ?>" class="btn-secondary">All Itineraries</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
