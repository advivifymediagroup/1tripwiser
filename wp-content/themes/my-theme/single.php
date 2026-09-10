<?php
/**
 * Single Blog Post — wide editorial layout matching the other single trip
 * templates: sticky table of contents on the left (built from the post's
 * own H2/H3 headings), one continuous article column, and a sticky author +
 * share rail on the right. The hero is untouched from the previous design.
 *
 * @package my-theme
 */
get_header();
?>

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
$sp_prev      = get_previous_post();
$sp_next      = get_next_post();
$sp_show_comments = comments_open() || get_comments_number();

/* Table of contents, built from the post's own H2/H3 headings — blog posts
   don't have structured ACF sections like the trip templates, so the
   contents list (and the anchor ids it links to) come from parsing the
   rendered content instead. H3s nest under whichever H2 came before them. */
$sp_toc = array();
$sp_heading_i = 0;
$sp_current_h2 = null;
$sp_content_html = preg_replace_callback(
    '/<h([23])\b([^>]*)>(.*?)<\/h\1>/is',
    function ($m) use (&$sp_toc, &$sp_heading_i, &$sp_current_h2) {
        $sp_heading_i++;
        $level = (int) $m[1];
        $id    = 'sp-heading-' . $sp_heading_i;
        $label = trim(wp_strip_all_tags($m[3]));

        if ($label) {
            if (2 === $level || null === $sp_current_h2) {
                $sp_toc[] = array('id' => $id, 'label' => $label, 'children' => array());
                $sp_current_h2 = count($sp_toc) - 1;
            } else {
                $sp_toc[$sp_current_h2]['children'][] = array('id' => $id, 'label' => $label);
            }
        }

        return '<h' . $level . $m[2] . ' id="' . esc_attr($id) . '">' . $m[3] . '</h' . $level . '>';
    },
    apply_filters('the_content', get_the_content())
);

if ($sp_show_comments) {
    $sp_toc[] = array('id' => 'sp-comments', 'label' => __('Comments', 'mytheme'));
}
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
            <div class="sp-hero-meta-item"><i class="fi-rr-clock" aria-hidden="true"></i> <?php echo esc_html($sp_read_min); ?> min read</div>
            <?php if ($sp_word_cnt > 0) : ?>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item"><i class="fi-rr-book-open-reader" aria-hidden="true"></i> <?php echo number_format($sp_word_cnt); ?> words</div>
            <?php endif; ?>
        </div>

        <div class="sp-hero-share">
            <span class="sp-hero-share-label"><?php esc_html_e('Share', 'mytheme'); ?></span>
            <a class="sp-share-btn sp-share-twitter" href="https://twitter.com/intent/tweet?url=<?php echo $sp_post_enc; ?>&text=<?php echo $sp_title_enc; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on X', 'mytheme'); ?>">𝕏</a>
            <a class="sp-share-btn sp-share-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $sp_post_enc; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on Facebook', 'mytheme'); ?>">f</a>
            <a class="sp-share-btn sp-share-whatsapp" href="https://wa.me/?text=<?php echo $sp_title_enc; ?>%20<?php echo $sp_post_enc; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on WhatsApp', 'mytheme'); ?>"><i class="fi-rr-comment" aria-hidden="true"></i></a>
            <button class="sp-share-btn sp-share-copy" aria-label="<?php esc_attr_e('Copy link', 'mytheme'); ?>" onclick="navigator.clipboard.writeText('<?php echo esc_js($sp_post_url); ?>').then(function(){this.innerHTML='<i class=\'fi-rr-check-circle\' aria-hidden=\'true\'></i>';}.bind(this))"><i class="fi-rr-link" aria-hidden="true"></i></button>
        </div>
    </div>
</section>

<main class="main-content dest-single">
    <div class="container dest-single-wrap">
        <div class="dest-single-layout">

            <!-- Sticky contents rail -->
            <aside class="dest-rail">
                <?php if ($sp_toc) : ?>
                <nav class="dest-toc" aria-label="<?php esc_attr_e('On this page', 'mytheme'); ?>">
                    <div class="dest-toc-title"><?php esc_html_e('Table of Contents', 'mytheme'); ?></div>
                    <ol class="dest-toc-list">
                        <?php foreach ($sp_toc as $t_i => $t) : ?>
                        <li>
                            <a href="#<?php echo esc_attr($t['id']); ?>" data-dest-toc="<?php echo esc_attr($t['id']); ?>">
                                <span class="dest-toc-num"><?php echo esc_html($t_i + 1); ?></span>
                                <span class="dest-toc-label"><?php echo esc_html($t['label']); ?></span>
                            </a>
                            <?php if (!empty($t['children'])) : ?>
                            <ol class="dest-toc-sublist">
                                <?php foreach ($t['children'] as $c_i => $c) : ?>
                                <li>
                                    <a href="#<?php echo esc_attr($c['id']); ?>" data-dest-toc="<?php echo esc_attr($c['id']); ?>">
                                        <span class="dest-toc-subnum"><?php echo esc_html(($t_i + 1) . '.' . ($c_i + 1)); ?></span>
                                        <span class="dest-toc-label"><?php echo esc_html($c['label']); ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ol>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </aside>

            <!-- One continuous article -->
            <article class="dest-article ev-content-styled sp-content">
                <?php echo $sp_content_html; ?>

                <?php if ($sp_tags) : ?>
                <div class="sp-tags dest-article-section">
                    <span class="sp-tags-label">Tags:&nbsp;</span>
                    <?php foreach ($sp_tags as $tag) : ?>
                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="sp-tag">#<?php echo esc_html($tag->name); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($sp_prev || $sp_next) : ?>
                <nav class="sp-nav dest-article-section" aria-label="Post navigation">
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

                <?php if ($sp_show_comments) : ?>
                <div id="sp-comments" class="sp-comments dest-article-section">
                    <?php comments_template(); ?>
                </div>
                <?php endif; ?>
            </article>

            <!-- Sticky rail: author + share -->
            <aside class="dest-booking">
                <div class="dest-rail-card sp-author-rail">
                    <div class="sp-author-label"><?php esc_html_e('Written by', 'mytheme'); ?></div>
                    <div class="sp-author-rail-who">
                        <div class="sp-author-avatar">
                            <?php $sp_av = get_avatar($sp_author_id, 56, '', '', array('extra_attr' => 'loading="lazy"'));
                            echo $sp_av ?: '<i class="fi-rr-edit-alt" aria-hidden="true"></i>'; ?>
                        </div>
                        <div class="sp-author-name"><?php the_author(); ?></div>
                    </div>
                    <p class="sp-author-bio">
                        <?php echo esc_html($sp_bio ?: 'Travel writer & explorer sharing honest guides and tips from the road. Every recommendation is tried and tested.'); ?>
                    </p>
                </div>
                <?php mytheme_render_enquiry_popup_card(); ?>
            </aside>

        </div>
    </div>
</main>

<script>
/* A missing/deleted media file leaves an empty gap the size of the image
   (or the browser's broken-image icon) right in the middle of the article.
   Collapse it instead — hide the image (and its figure wrapper, if any) the
   moment it fails to load. */
(function () {
    document.querySelectorAll('.sp-content img').forEach(function (img) {
        function hide() { (img.closest('figure, p') || img).style.display = 'none'; }
        img.addEventListener('error', hide);
        /* Images fetched before this script ran may have already failed —
           the error event only fires once, at the moment of failure. */
        if (img.complete && img.naturalWidth === 0) { hide(); }
    });
}());
</script>

<?php if ($sp_toc) : ?>
<script>
/* Highlight the contents entry for whichever section is currently in view. */
(function () {
    var links = document.querySelectorAll('[data-dest-toc]');
    if (!links.length) { return; }

    var targets = [];
    links.forEach(function (link) {
        var el = document.getElementById(link.getAttribute('data-dest-toc'));
        if (el) { targets.push({ link: link, el: el }); }
    });
    if (!targets.length) { return; }

    function sync() {
        var current = targets[0];
        targets.forEach(function (t) {
            if (t.el.getBoundingClientRect().top <= 140) { current = t; }
        });
        targets.forEach(function (t) {
            t.link.classList.toggle('is-current', t === current);
        });
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
        if (ticking) { return; }
        ticking = true;
        window.requestAnimationFrame(function () { sync(); ticking = false; });
    }, { passive: true });
    sync();
}());
</script>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
