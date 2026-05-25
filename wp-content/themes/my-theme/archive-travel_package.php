<?php get_header(); ?>

<main class="main-content travel-archive">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>
        <header class="archive-header travel-archive-header">
            <span>Curated trips</span>
            <h1 class="archive-title">Travel Packages</h1>
            <p class="archive-description">Choose a ready-to-book trip, then customize the pace, stays and experiences around your travel style.</p>
        </header>

        <?php mytheme_travel_filter_box('travel_package', 'package_filter', get_post_type_archive_link('travel_package')); ?>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php mytheme_package_card(); ?>
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
            <?php mytheme_render_package_empty_state(get_post_type_archive_link('travel_package')); ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
