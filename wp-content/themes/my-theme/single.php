<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<?php
$sp_post_id   = get_the_ID();
$sp_cats      = get_the_category();
$sp_cat_name  = $sp_cats ? $sp_cats[0]->name : '';
$sp_tags      = get_the_tags();
$sp_author_id = get_the_author_meta('ID');
$sp_bio       = get_the_author_meta('description');
$sp_word_cnt  = str_word_count(wp_strip_all_tags(get_the_content()));
$sp_read_min  = max(1, round($sp_word_cnt / 200));
$sp_has_img   = has_post_thumbnail();
$sp_img_url   = $sp_has_img ? get_the_post_thumbnail_url(null, 'full') : '';
$sp_post_url  = get_permalink();
$sp_post_enc  = rawurlencode($sp_post_url);
$sp_title_enc = rawurlencode(get_the_title());
?>

<!-- ═══ HERO ═══ -->
<section class="sp-hero<?php echo $sp_has_img ? '' : ' sp-hero-no-img'; ?>">
    <?php if ($sp_img_url) : ?>
    <div class="sp-hero-bg" style="background-image:url(<?php echo esc_url($sp_img_url); ?>);"></div>
    <?php endif; ?>
    <div class="sp-hero-overlay"></div>

    <div class="sp-hero-content">
        <!-- Breadcrumb -->
        <nav class="sp-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>"><i class="fi-rr-home" aria-hidden="true"></i> Home</a>
            <span class="sep">›</span>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>">Blog</a>
            <span class="sep">›</span>
            <span><?php echo esc_html(wp_trim_words(get_the_title(), 7, '…')); ?></span>
        </nav>

        <?php if ($sp_cat_name) : ?>
        <div class="sp-post-cat"><?php echo esc_html($sp_cat_name); ?></div>
        <?php endif; ?>

        <h1 class="sp-hero-title"><?php the_title(); ?></h1>

        <div class="sp-hero-meta">
            <div class="sp-hero-meta-item">
                <div class="sp-hero-avatar">
                    <?php echo get_avatar(get_the_author_meta('email'), 34, '', '', array('extra_attr' => 'loading="lazy"')) ?: '<i class="fi-rr-edit-alt" aria-hidden="true"></i>'; ?>
                </div>
                <span><?php the_author(); ?></span>
            </div>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item"><i class="fi-rr-calendar" aria-hidden="true"></i> <?php echo esc_html(get_the_date()); ?></div>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item">⏱ <?php echo esc_html($sp_read_min); ?> min read</div>
            <?php if ($sp_word_cnt > 0) : ?>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item"><i class="fi-rr-book-open-reader" aria-hidden="true"></i> <?php echo number_format($sp_word_cnt); ?> words</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ═══ ARTICLE BODY ═══ -->
<div class="sp-wrap">

    <!-- Content card -->
    <div class="sp-content">
        <?php the_content(); ?>

        <?php if ($sp_tags) : ?>
        <div class="sp-tags">
            <span class="sp-tags-label">Tags:&nbsp;</span>
            <?php foreach ($sp_tags as $tag) : ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="sp-tag">#<?php echo esc_html($tag->name); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Share bar -->
    <div class="sp-share">
        <span class="sp-share-label">Share this post:</span>
        <a class="sp-share-btn sp-share-twitter"
           href="https://twitter.com/intent/tweet?url=<?php echo $sp_post_enc; ?>&text=<?php echo $sp_title_enc; ?>"
           target="_blank" rel="noopener noreferrer">
            𝕏 Twitter
        </a>
        <a class="sp-share-btn sp-share-facebook"
           href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $sp_post_enc; ?>"
           target="_blank" rel="noopener noreferrer">
            f Facebook
        </a>
        <a class="sp-share-btn sp-share-whatsapp"
           href="https://wa.me/?text=<?php echo $sp_title_enc; ?>%20<?php echo $sp_post_enc; ?>"
           target="_blank" rel="noopener noreferrer">
            <i class="fi-rr-comment" aria-hidden="true"></i> WhatsApp
        </a>
        <button class="sp-share-btn sp-share-copy" onclick="navigator.clipboard.writeText('<?php echo esc_js($sp_post_url); ?>').then(function(){this.innerHTML='<i class=\'fi-rr-check-circle\' aria-hidden=\'true\'></i> Copied!';}.bind(this))">
            <i class="fi-rr-link" aria-hidden="true"></i> Copy Link
        </button>
    </div>

    <!-- Author card -->
    <div class="sp-author">
        <div class="sp-author-avatar">
            <?php $sp_av = get_avatar($sp_author_id, 64, '', '', array('extra_attr' => 'loading="lazy"'));
            echo $sp_av ?: '<i class="fi-rr-edit-alt" aria-hidden="true"></i>'; ?>
        </div>
        <div>
            <div class="sp-author-label">Written by</div>
            <div class="sp-author-name"><?php the_author(); ?></div>
            <p class="sp-author-bio">
                <?php echo esc_html($sp_bio ?: 'Travel writer &amp; explorer sharing honest guides and tips from the road. Every recommendation is tried and tested.'); ?>
            </p>
        </div>
    </div>

    <!-- Post navigation -->
    <?php
    $sp_prev = get_previous_post();
    $sp_next = get_next_post();
    if ($sp_prev || $sp_next) :
    ?>
    <nav class="sp-nav" aria-label="Post navigation">
        <?php if ($sp_prev) : ?>
        <a href="<?php echo esc_url(get_permalink($sp_prev)); ?>" class="sp-nav-link sp-nav-prev">
            <div class="sp-nav-dir">← Previous post</div>
            <div class="sp-nav-title"><?php echo esc_html(get_the_title($sp_prev)); ?></div>
        </a>
        <?php else : ?>
        <div></div>
        <?php endif; ?>
        <?php if ($sp_next) : ?>
        <a href="<?php echo esc_url(get_permalink($sp_next)); ?>" class="sp-nav-link sp-nav-next">
            <div class="sp-nav-dir">Next post →</div>
            <div class="sp-nav-title"><?php echo esc_html(get_the_title($sp_next)); ?></div>
        </a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <!-- Comments -->
    <?php if (comments_open() || get_comments_number()) : ?>
    <div class="sp-comments">
        <?php comments_template(); ?>
    </div>
    <?php endif; ?>

</div><!-- /.sp-wrap -->

<?php endwhile; ?>

<?php get_footer(); ?>
