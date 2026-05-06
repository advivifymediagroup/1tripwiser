<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article class="single-post travel-single">
                <header class="post-header travel-single-header">
                    <span>Itinerary</span>
                    <h1><?php the_title(); ?></h1>
                    <div class="travel-meta header-meta">
                        <?php foreach (mytheme_travel_detail_items() as $detail) : ?>
                            <span><?php echo esc_html($detail['value']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail travel-hero-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="travel-detail-grid">
                    <?php foreach (mytheme_travel_detail_items() as $detail) : ?>
                        <div class="travel-detail">
                            <span><?php echo esc_html($detail['label']); ?></span>
                            <strong><?php echo esc_html($detail['value']); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <footer class="post-footer travel-cta">
                    <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-primary">Customize This Route</a>
                    <a href="<?php echo esc_url(get_post_type_archive_link('itinerary')); ?>" class="btn-secondary">All Itineraries</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
