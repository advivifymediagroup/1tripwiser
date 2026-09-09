<?php get_header(); ?>

<main class="main-content travel-archive explore-page">

    <!-- Hero (dark band — matches Blog + Affiliates) -->
    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span><span>Packages</span>
            </nav>
            <span class="explore-hero-kicker">Wiser Packages</span>
            <h1 class="tw-h1">Travel <span class="accent-red">Packages</span></h1>
            <p class="explore-hero-sub">Choose a ready-to-book trip, then customize the pace, stays and experiences around your travel style.</p>
        </div>
    </section>

    <div class="container explore-wrap">
        <?php mytheme_render_package_compare_table(); ?>

        <?php
        if ( function_exists('tw_explore_unified_filter_box') ) {
            tw_explore_unified_filter_box('travel_package', get_post_type_archive_link('travel_package'));
        } else {
            mytheme_travel_filter_box('travel_package', 'package_filter', get_post_type_archive_link('travel_package'));
        }
        ?>

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
