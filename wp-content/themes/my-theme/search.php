<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <header class="search-header">
            <h1>Search Results</h1>
            <p>You searched for: "<strong><?php echo get_search_query(); ?></strong>"</p>
        </header>

        <?php if (have_posts()) : ?>
            <div class="search-results">
                <p>Found <?php echo $wp_query->found_posts; ?> result(s)</p>
                <div class="posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="post-content">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <div class="post-meta">
                                    <span class="date"><?php echo get_the_date(); ?></span>
                                    <span class="author">by <?php the_author(); ?></span>
                                    <span class="post-type"><?php echo get_post_type(); ?></span>
                                </div>
                                <div class="post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
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
            <div class="no-results">
                <h2>No results found</h2>
                <p>Sorry, no posts matched your search criteria. Please try again with different keywords.</p>
                <div class="search-form-no-results">
                    <?php get_search_form(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>