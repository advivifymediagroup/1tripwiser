<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article class="single-post">
                <header class="post-header">
                    <h1><?php the_title(); ?></h1>
                    <div class="post-meta">
                        <span class="date"><?php echo get_the_date(); ?></span>
                        <span class="author">by <?php the_author(); ?></span>
                        <span class="categories"><?php the_category(', '); ?></span>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <footer class="post-footer">
                    <div class="tags">
                        <?php the_tags('<span class="tag-label">Tags:</span> ', ', ', ''); ?>
                    </div>
                </footer>
            </article>

            <nav class="post-navigation">
                <div class="nav-previous"><?php previous_post_link('%link', '« Previous Post'); ?></div>
                <div class="nav-next"><?php next_post_link('%link', 'Next Post »'); ?></div>
            </nav>

            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>