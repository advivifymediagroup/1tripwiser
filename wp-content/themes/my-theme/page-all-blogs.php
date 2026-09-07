<?php
/**
 * Template Name: All Blogs
 *
 * Lists every published blog post in a paginated grid.
 * Mirrors the card layout of the Blog & Affiliates page.
 *
 * @package my-theme
 */
get_header();

$paged   = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$blog_q  = new WP_Query( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
$total_posts = (int) $blog_q->found_posts;
?>

<main class="main-content explore-page ba-all-page">

    <!-- Hero -->
    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( home_url('/blog-affiliates/') ); ?>">Blog</a><span>›</span>
                <span>All Stories</span>
            </nav>
            <span class="explore-hero-kicker">From the Blog</span>
            <h1 class="tw-h1">All <span class="accent-red">Stories</span></h1>
            <p class="explore-hero-sub">Every travel guide, honest review and trip story from the 1TRIPWISER team — in one place.</p>
            <?php if ( $total_posts ) : ?>
            <p class="explore-hero-count"><?php echo esc_html( $total_posts ); ?> <?php echo ( 1 === $total_posts ) ? 'story' : 'stories'; ?></p>
            <?php endif; ?>
        </div>
    </section>

    <div class="container explore-wrap">

        <?php if ( $blog_q->have_posts() ) : ?>
        <div class="ba-grid">
            <?php while ( $blog_q->have_posts() ) : $blog_q->the_post();
                $cats     = get_the_category();
                $cat_name = $cats ? $cats[0]->name : '';
            ?>
            <article class="ba-card">
                <div class="ba-card-img">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a>
                    <?php else : ?>
                        <a href="<?php the_permalink(); ?>" class="ba-card-img-placeholder"><i class="fi-rr-plane" aria-hidden="true"></i></a>
                    <?php endif; ?>
                    <?php if ( $cat_name ) : ?>
                        <span class="ba-card-cat"><?php echo esc_html( $cat_name ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="ba-card-body">
                    <div class="ba-card-meta"><?php echo esc_html( get_the_date() ); ?></div>
                    <h3 class="ba-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="ba-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="ba-card-read-more">Read More →</a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <!-- Pagination -->
        <?php if ( $blog_q->max_num_pages > 1 ) : ?>
        <nav class="tribe-pagination" aria-label="Blog pagination">
            <?php
            echo paginate_links( array(
                'base'      => add_query_arg( 'paged', '%#%' ),
                'format'    => '',
                'current'   => $paged,
                'total'     => $blog_q->max_num_pages,
                'mid_size'  => 1,
                'prev_text' => '← Prev',
                'next_text' => 'Next →',
            ) );
            ?>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <div class="tribe-empty">
            <div class="tribe-empty-icon"><i class="fi-rr-edit-alt" aria-hidden="true"></i></div>
            <h3>No blog posts yet</h3>
            <p>Our writers are crafting fresh travel stories. Check back soon.</p>
            <?php if ( is_user_logged_in() ) : ?>
                <a class="btn-primary" href="<?php echo esc_url( home_url('/submit-blog/') ); ?>"><i class="fi-rr-edit-alt" aria-hidden="true"></i> Write a Post</a>
            <?php else : ?>
                <a class="btn-primary" href="<?php echo esc_url( home_url('/blog-affiliates/') ); ?>">← Back to Blog</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
