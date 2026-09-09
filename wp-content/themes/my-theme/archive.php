<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <header class="archive-header">
            <?php
            the_archive_title('<h1 class="tw-h1">', '</h1>');
            the_archive_description('<div class="archive-description">', '</div>');
            ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="post-meta">
                                <span class="date"><?php echo get_the_date(); ?></span>
                                <span class="author">by <?php the_author(); ?></span>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn-primary btn-sm">Read More</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('« Previous', 'mytheme'),
                    'next_text' => __('Next »', 'mytheme'),
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="no-posts">
                <h2>No posts found</h2>
                <p>Sorry, no posts matched your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>