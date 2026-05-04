<?php get_header(); ?>

<main class="main-content">
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to <?php bloginfo("name"); ?></h1>
                <p><?php bloginfo("description"); ?></p>
                <a href="#featured-posts" class="btn-primary">Explore Destinations</a>
            </div>
        </div>
    </section>

    <section id="featured-posts" class="featured-posts">
        <div class="container">
            <h2>Latest Travel Stories</h2>
            <div class="posts-grid">
                <?php
                $featured_posts = new WP_Query([
                    "posts_per_page" => 3,
                    "post_status" => "publish",
                ]);
                if ($featured_posts->have_posts()):
                    while ($featured_posts->have_posts()):
                        $featured_posts->the_post(); ?>
                    <article class="post-card featured">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail("medium"); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="post-meta">
                                <span class="date"><?php echo get_the_date(); ?></span>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                        </div>
                    </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                     ?>
                    <div class="no-posts">
                        <p>No posts available yet. Check back soon!</p>
                    </div>
                <?php
                endif;
                ?>
            </div>
            <div class="view-all">
                <a href="<?php echo get_permalink(
                    get_option("page_for_posts")
                ); ?>" class="btn-secondary">View All Posts</a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
