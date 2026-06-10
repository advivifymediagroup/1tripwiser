<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $destination = mytheme_get_destination_data();
            $image_url = mytheme_get_image_url($destination['image'], 'large');
            ?>
            <article class="single-post travel-single">
                <div class="travel-header-image-section">
                <header class="post-header travel-single-header">
                    <span>Destination</span>
                    <h1><?php the_title(); ?></h1>
                    <div class="travel-meta header-meta">
                        <?php if ($destination['country']) : ?><span><?php echo esc_html($destination['country']); ?></span><?php endif; ?>
                        <?php if ($destination['best_time']) : ?><span><?php echo esc_html($destination['best_time']); ?></span><?php endif; ?>
                        <?php if ($destination['ideal_duration']) : ?><span><?php echo esc_html($destination['ideal_duration']); ?></span><?php endif; ?>
                    </div>
                </header>

                <?php if ($image_url || has_post_thumbnail()) : ?>
                    <div class="post-thumbnail travel-hero-image">
                        <?php if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                </div>

                <div class="travel-detail-grid">
                    <?php if ($destination['country']) : ?><div class="travel-detail"><span>Country / Region</span><strong><?php echo esc_html($destination['country']); ?></strong></div><?php endif; ?>
                    <?php if ($destination['best_time']) : ?><div class="travel-detail"><span>Best Time</span><strong><?php echo esc_html($destination['best_time']); ?></strong></div><?php endif; ?>
                    <?php if ($destination['ideal_duration']) : ?><div class="travel-detail"><span>Ideal Duration</span><strong><?php echo esc_html($destination['ideal_duration']); ?></strong></div><?php endif; ?>
                    <?php if ($destination['starting_price']) : ?><div class="travel-detail"><span>Starting Price</span><strong><?php echo esc_html($destination['starting_price']); ?></strong></div><?php endif; ?>
                </div>

                <?php if ($destination['short_intro']) : ?>
                    <div class="post-content destination-intro">
                        <?php echo wp_kses_post(wpautop($destination['short_intro'])); ?>
                    </div>
                <?php endif; ?>

                <div class="post-content">
                    <?php
                    if ($destination['overview']) {
                        echo wp_kses_post($destination['overview']);
                    }
                    ?>
                </div>

                <?php mytheme_render_destination_guide(get_the_ID()); ?>

                <?php mytheme_render_faq_section(get_the_ID(), 'Destination FAQs'); ?>

                <footer class="post-footer travel-cta">
                    <a href="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>" class="btn-primary">View Packages</a>
                    <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-secondary">Plan a Trip</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
