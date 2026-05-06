<?php get_header(); ?>

<main class="main-content travel-archive">
    <div class="container">
        <header class="archive-header travel-archive-header">
            <span>Curated trips</span>
            <h1 class="archive-title">Travel Packages</h1>
            <p class="archive-description">Choose a ready-to-book trip, then customize the pace, stays and experiences around your travel style.</p>
        </header>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-card travel-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="travel-meta">
                                <?php foreach (mytheme_travel_detail_items() as $detail) : ?>
                                    <span><?php echo esc_html($detail['value']); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read-more">View Package</a>
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
                <h2>No packages found</h2>
                <p>Add your first package from the WordPress dashboard.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
