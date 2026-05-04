<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article class="page-content">
                <h1><?php the_title(); ?></h1>
                <div class="page-meta">
                    <span class="date"><?php echo get_the_date(); ?></span>
                    <span class="author">by <?php the_author(); ?></span>
                </div>
                <div class="content">
                    <?php the_content(); ?>
                </div>
                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Pages:', 'mytheme'),
                    'after' => '</div>',
                ));
                ?>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>