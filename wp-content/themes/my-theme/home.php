<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <header class="blog-header">
            <h1>Travel Blog</h1>
            <p>Discover amazing destinations and travel tips</p>
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
                                <span class="categories"><?php the_category(', '); ?></span>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
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