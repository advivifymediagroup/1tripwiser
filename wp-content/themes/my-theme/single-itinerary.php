<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>
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
                    <section class="itinerary-days itinerary-timeline">
                        <div class="itinerary-days-heading">
                            <span>Route timeline</span>
                            <h2>Day Wise Plan</h2>
                        </div>
                        <?php $day_number = 1; ?>
                        <?php while (have_rows('itinerary_days')) : the_row(); ?>
                            <?php
                            $day_title = get_sub_field('day_title');
                            $day_details = get_sub_field('day_details');
                            $day_route = get_sub_field('day_route');
                            $day_stay = get_sub_field('day_stay');
                            $day_meals = get_sub_field('day_meals');
                            $day_transfer = get_sub_field('day_transfer');
                            $day_highlights = get_sub_field('day_highlights');
                            $preview_text = wp_trim_words(wp_strip_all_tags($day_details), 18, '...');
                            ?>
                            <details class="itinerary-day" <?php echo $day_number === 1 ? 'open' : ''; ?>>
                                <summary>
                                    <span class="itinerary-day-marker"><?php echo esc_html($day_number); ?></span>
                                    <span class="itinerary-day-summary">
                                        <strong><?php echo esc_html($day_title ? $day_title : sprintf(__('Day %d', 'mytheme'), $day_number)); ?></strong>
                                        <?php if ($day_route) : ?><em><?php echo esc_html($day_route); ?></em><?php endif; ?>
                                        <?php if (!$day_route && $preview_text) : ?><em><?php echo esc_html($preview_text); ?></em><?php endif; ?>
                                    </span>
                                    <span class="itinerary-day-toggle" aria-hidden="true"></span>
                                </summary>
                                <div class="itinerary-day-panel">
                                    <?php if ($day_stay || $day_meals || $day_transfer || $day_highlights) : ?>
                                        <div class="itinerary-day-chips">
                                            <?php if ($day_stay) : ?><span><i class="fa-solid fa-bed"></i><?php echo esc_html($day_stay); ?></span><?php endif; ?>
                                            <?php if ($day_meals) : ?><span><i class="fa-solid fa-utensils"></i><?php echo esc_html($day_meals); ?></span><?php endif; ?>
                                            <?php if ($day_transfer) : ?><span><i class="fa-solid fa-route"></i><?php echo esc_html($day_transfer); ?></span><?php endif; ?>
                                            <?php if ($day_highlights) : ?><span><i class="fa-solid fa-star"></i><?php echo esc_html($day_highlights); ?></span><?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="itinerary-day-copy">
                                        <?php echo wp_kses_post(wpautop($day_details)); ?>
                                    </div>
                                </div>
                            </details>
                            <?php $day_number++; ?>
                        <?php endwhile; ?>
                    </section>
                <?php endif; ?>

                <?php mytheme_render_faq_section(get_the_ID(), 'Itinerary FAQs'); ?>

                <footer class="post-footer travel-cta">
                    <!-- <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-primary">Customize This Route</a> -->
                    <?php mytheme_render_trip_pdf_button(get_the_ID(), 'Download PDF'); ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('itinerary')); ?>" class="btn-secondary">All Itineraries</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
