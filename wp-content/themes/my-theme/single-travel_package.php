<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $package = mytheme_get_package_data();
            $package_image = mytheme_get_image_url($package['image'], 'large');
            $book_url = $package['book_url'] ? $package['book_url'] : mytheme_get_plan_trip_url();
            ?>
            <article class="single-post travel-single">
                <header class="post-header travel-single-header">
                    <span><?php echo $package['tag'] ? esc_html($package['tag']) : 'Travel package'; ?></span>
                    <h1><?php the_title(); ?></h1>
                    <div class="travel-meta header-meta">
                        <?php if ($package['location']) : ?><span><?php echo esc_html($package['location']); ?></span><?php endif; ?>
                        <?php if ($package['duration']) : ?><span><?php echo esc_html($package['duration']); ?></span><?php endif; ?>
                        <?php if ($package['trip_type']) : ?><span><?php echo esc_html($package['trip_type']); ?></span><?php endif; ?>
                    </div>
                </header>

                <?php if ($package_image || has_post_thumbnail()) : ?>
                    <div class="post-thumbnail travel-hero-image">
                        <?php if ($package_image) : ?>
                            <img src="<?php echo esc_url($package_image); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="travel-detail-grid">
                    <?php if ($package['location']) : ?><div class="travel-detail"><span>Location</span><strong><?php echo esc_html($package['location']); ?></strong></div><?php endif; ?>
                    <?php if ($package['duration']) : ?><div class="travel-detail"><span>Duration</span><strong><?php echo esc_html($package['duration']); ?></strong></div><?php endif; ?>
                    <?php if ($package['trip_type']) : ?><div class="travel-detail"><span>Trip Type</span><strong><?php echo esc_html($package['trip_type']); ?></strong></div><?php endif; ?>
                    <?php if ($package['amount']) : ?><div class="travel-detail"><span>Amount</span><strong><?php echo esc_html($package['amount']); ?></strong></div><?php endif; ?>
                    <?php if ($package['emi']) : ?><div class="travel-detail"><span>EMI Option</span><strong><?php echo esc_html($package['emi']); ?></strong></div><?php endif; ?>
                </div>

                <div class="post-content">
                    <?php
                    if ($package['overview']) {
                        echo wp_kses_post($package['overview']);
                    }
                    ?>
                </div>

                <footer class="post-footer travel-cta">
                    <a href="<?php echo esc_url($book_url); ?>" class="btn-primary">Book Now</a>
                    <!-- <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-secondary">Customize Trip</a> -->
                    <a href="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>" class="btn-secondary">All Packages</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
