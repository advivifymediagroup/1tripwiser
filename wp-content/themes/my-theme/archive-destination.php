<?php get_header(); ?>

<main class="main-content travel-archive">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>
        <header class="archive-header travel-archive-header">
            <span>Explore by place</span>
            <h1 class="archive-title">Destinations</h1>
            <p class="archive-description">Browse destinations, best seasons, ideal trip lengths and package starting prices.</p>
        </header>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $destination = mytheme_get_destination_data();
                    $image_url = mytheme_get_image_url($destination['image'], 'medium');
                    ?>
                    <article class="post-card destination-card">
                        <div class="package-media">
                            <a href="<?php the_permalink(); ?>">
                                <?php if ($image_url) : ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
                                <?php elseif (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="package-content">
                            <?php if ($destination['country']) : ?>
                                <span class="package-location"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($destination['country']); ?></span>
                            <?php endif; ?>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="package-facts">
                                <?php if ($destination['best_time']) : ?><span><?php echo esc_html($destination['best_time']); ?></span><?php endif; ?>
                                <?php if ($destination['ideal_duration']) : ?><span><?php echo esc_html($destination['ideal_duration']); ?></span><?php endif; ?>
                            </div>
                            <div class="post-excerpt">
                                <?php echo $destination['short_intro'] ? wp_kses_post(wpautop($destination['short_intro'])) : get_the_excerpt(); ?>
                            </div>
                            <div class="package-price-row">
                                <?php if ($destination['starting_price']) : ?>
                                    <div><small>Packages from</small><strong><?php echo esc_html($destination['starting_price']); ?></strong></div>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="book-now-btn">Explore</a>
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
                <h2>No destinations found</h2>
                <p>Add your first destination from the WordPress dashboard.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
