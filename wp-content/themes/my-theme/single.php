<?php get_header(); ?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
/* ================================================================
   SINGLE BLOG POST — Professional layout
   ================================================================ */
:root {
    --sp-bg:     #f5f8fa;
    --sp-card:   #ffffff;
    --sp-text:   #1a2535;
    --sp-muted:  #6b7a8f;
    --sp-border: #e0e8f0;
    --sp-head:   #0D1526;
    --sp-shadow: 0 6px 24px rgba(0,0,0,0.07);
    --gold:  #FCB415;
    --blue:  #0692AF;
    --green: #306C35;
}

body { background-color: var(--sp-bg) !important; font-family: 'Nunito', sans-serif; color: var(--sp-text); }

/* ── Hero ─────────────────────────────────────────────────── */
.sp-hero {
    position: relative;
    min-height: 440px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: var(--sp-head);
}

.sp-hero-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0.4;
    transition: opacity 0.3s;
}

.sp-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(13,21,38,0.96) 0%, rgba(13,21,38,0.4) 55%, rgba(13,21,38,0.15) 100%);
}

.sp-hero-content {
    position: relative;
    z-index: 2;
    max-width: 880px;
    margin: 0 auto;
    padding: 0 24px 56px;
    width: 100%;
}

/* Breadcrumb */
.sp-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.77rem;
    color: rgba(255,255,255,0.5);
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.sp-breadcrumb a { color: rgba(255,255,255,0.55); text-decoration: none; transition: color 0.2s; }
.sp-breadcrumb a:hover { color: var(--gold); }
.sp-breadcrumb .sep { color: rgba(255,255,255,0.3); }

.sp-post-cat {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    background: rgba(6,146,175,0.85);
    color: #fff;
    padding: 3px 13px;
    border-radius: 999px;
    text-transform: uppercase;
    margin-bottom: 16px;
}

.sp-hero-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.2rem, 5vw, 3.4rem);
    color: #ffffff;
    letter-spacing: 0.03em;
    line-height: 1.08;
    margin-bottom: 22px;
}

.sp-hero-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 0.83rem;
    color: rgba(255,255,255,0.6);
}
.sp-hero-meta-item { display: flex; align-items: center; gap: 6px; }
.sp-hero-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    border: 2px solid rgba(252,180,21,0.55);
    overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    background: rgba(252,180,21,0.12);
    font-size: 1rem;
    flex-shrink: 0;
}
.sp-hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
.sp-hero-divider { color: rgba(255,255,255,0.2); }

/* ── Article body ─────────────────────────────────────────── */
.sp-wrap {
    max-width: 880px;
    margin: 0 auto;
    padding: 48px 24px 88px;
}

.sp-content {
    background: var(--sp-card);
    border-radius: 18px;
    border: 1px solid var(--sp-border);
    padding: 52px 56px;
    box-shadow: var(--sp-shadow);
    margin-bottom: 36px;
    font-size: 1.06rem;
    line-height: 1.88;
    color: var(--sp-text);
}

.sp-content h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    color: var(--sp-head);
    letter-spacing: 0.03em;
    margin: 2em 0 0.6em;
    padding-top: 0.4em;
    border-top: 2px solid var(--sp-border);
}
.sp-content h3 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.55rem;
    color: var(--sp-head);
    letter-spacing: 0.03em;
    margin: 1.6em 0 0.5em;
}
.sp-content h4 { font-size: 1.1rem; font-weight: 800; color: var(--sp-head); margin: 1.4em 0 0.4em; }
.sp-content p { margin-bottom: 1.5em; }
.sp-content a { color: var(--blue); font-weight: 700; transition: color 0.2s; }
.sp-content a:hover { color: var(--green); }
.sp-content img { max-width: 100%; border-radius: 12px; display: block; margin: 1.8em auto; box-shadow: var(--sp-shadow); }
.sp-content blockquote {
    border-left: 4px solid var(--gold);
    padding: 14px 22px;
    background: rgba(252,180,21,0.05);
    margin: 1.8em 0;
    border-radius: 0 10px 10px 0;
    font-style: italic;
    color: var(--sp-muted);
    font-size: 1rem;
}
.sp-content ul, .sp-content ol { padding-left: 1.6em; margin-bottom: 1.5em; }
.sp-content li { margin-bottom: 0.55em; }
.sp-content pre {
    background: #0d1526;
    color: #e2e8f0;
    border-radius: 10px;
    padding: 20px 24px;
    overflow-x: auto;
    font-size: 0.88rem;
    margin: 1.5em 0;
}
.sp-content code { background: rgba(6,146,175,0.08); color: var(--blue); padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
.sp-content pre code { background: transparent; color: inherit; padding: 0; }

/* Tags */
.sp-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 36px;
    padding-top: 28px;
    border-top: 1px solid var(--sp-border);
}
.sp-tags-label { font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--sp-muted); line-height: 2.1; }
.sp-tag {
    padding: 5px 14px;
    background: rgba(6,146,175,0.07);
    border: 1px solid rgba(6,146,175,0.18);
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--blue);
    text-decoration: none;
    transition: background 0.2s, border-color 0.2s;
}
.sp-tag:hover { background: rgba(6,146,175,0.16); border-color: rgba(6,146,175,0.4); }

/* ── Author card ──────────────────────────────────────────── */
.sp-author {
    display: flex;
    align-items: flex-start;
    gap: 22px;
    background: var(--sp-card);
    border: 1px solid var(--sp-border);
    border-radius: 16px;
    padding: 28px 32px;
    box-shadow: var(--sp-shadow);
    margin-bottom: 36px;
}
.sp-author-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    border: 2px solid var(--gold);
    overflow: hidden;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(252,180,21,0.1);
    font-size: 1.6rem;
}
.sp-author-avatar img { width: 100%; height: 100%; object-fit: cover; }
.sp-author-label { font-size: 0.72rem; font-weight: 800; letter-spacing: 0.1em; color: var(--blue); text-transform: uppercase; margin-bottom: 4px; }
.sp-author-name { font-size: 1.08rem; font-weight: 800; color: var(--sp-head); margin-bottom: 6px; }
.sp-author-bio { font-size: 0.87rem; color: var(--sp-muted); line-height: 1.65; margin: 0; }

/* ── Post navigation ──────────────────────────────────────── */
.sp-nav {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 44px;
}
.sp-nav-link {
    padding: 20px 24px;
    background: var(--sp-card);
    border: 1px solid var(--sp-border);
    border-radius: 14px;
    text-decoration: none;
    transition: border-color 0.2s, transform 0.18s, box-shadow 0.2s;
    display: block;
}
.sp-nav-link:hover { border-color: var(--gold); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.1); }
.sp-nav-link.sp-nav-prev { text-align: left; }
.sp-nav-link.sp-nav-next { text-align: right; }
.sp-nav-dir {
    font-size: 0.72rem; font-weight: 800;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--sp-muted); margin-bottom: 5px;
}
.sp-nav-title { font-size: 0.9rem; font-weight: 800; color: var(--sp-head); line-height: 1.4; }

/* ── Share bar ────────────────────────────────────────────── */
.sp-share {
    background: var(--sp-card);
    border: 1px solid var(--sp-border);
    border-radius: 14px;
    padding: 20px 28px;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 36px;
    box-shadow: var(--sp-shadow);
}
.sp-share-label { font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--sp-muted); }
.sp-share-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px;
    border-radius: 8px;
    font-size: 0.82rem; font-weight: 800;
    text-decoration: none;
    transition: opacity 0.2s, transform 0.15s;
    color: #fff;
}
.sp-share-btn:hover { opacity: 0.87; transform: translateY(-1px); }
.sp-share-twitter  { background: #1DA1F2; }
.sp-share-facebook { background: #1877F2; }
.sp-share-whatsapp { background: #25D366; }
.sp-share-copy {
    background: var(--sp-bg);
    border: 1px solid var(--sp-border);
    color: var(--sp-text) !important;
    cursor: pointer;
    font-family: 'Nunito', sans-serif;
}
.sp-share-copy:hover { border-color: var(--blue); }

/* ── Comments ─────────────────────────────────────────────── */
.sp-comments {
    background: var(--sp-card);
    border: 1px solid var(--sp-border);
    border-radius: 18px;
    padding: 40px 48px;
    box-shadow: var(--sp-shadow);
}

.sp-comments .comments-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.6rem; color: var(--sp-head);
    letter-spacing: 0.04em; margin-bottom: 28px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--sp-border);
}

.sp-comments .comment-list { list-style: none; padding: 0; margin: 0 0 32px; }
.sp-comments .comment-list .comment {
    padding: 20px 0;
    border-bottom: 1px solid var(--sp-border);
}
.sp-comments .comment-list .comment:last-child { border-bottom: none; }
.sp-comments .comment-author.vcard b { font-weight: 800; color: var(--sp-head); font-size: 0.95rem; }
.sp-comments .comment-metadata { font-size: 0.77rem; color: var(--sp-muted); margin: 4px 0 10px; }
.sp-comments .comment-metadata a { color: var(--sp-muted); text-decoration: none; }
.sp-comments .comment-body p { font-size: 0.92rem; line-height: 1.7; color: var(--sp-text); margin: 0; }
.sp-comments .reply a { font-size: 0.78rem; font-weight: 700; color: var(--blue); text-decoration: none; margin-top: 8px; display: inline-block; }

/* Comment form */
.sp-comments #respond h3 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.4rem; color: var(--sp-head);
    letter-spacing: 0.04em; margin-bottom: 20px;
}
.sp-comments .comment-form p { margin-bottom: 0; }
.sp-comments .comment-form label {
    display: block;
    font-size: 0.8rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--sp-muted); margin-bottom: 6px;
}
.sp-comments .comment-form input[type="text"],
.sp-comments .comment-form input[type="email"],
.sp-comments .comment-form input[type="url"],
.sp-comments .comment-form textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--sp-border);
    border-radius: 9px;
    font-family: 'Nunito', sans-serif;
    font-size: 0.93rem;
    color: var(--sp-text);
    background: var(--sp-bg);
    transition: border-color 0.2s, background 0.2s;
    box-sizing: border-box;
    margin-bottom: 16px;
}
.sp-comments .comment-form input:focus,
.sp-comments .comment-form textarea:focus {
    outline: none;
    border-color: var(--blue);
    background: #fff;
}
.sp-comments .comment-form .form-submit { margin: 0; }
.sp-comments .comment-form input[type="submit"] {
    padding: 12px 32px;
    background: linear-gradient(135deg, #FCB415 0%, #f09a00 100%);
    color: #0D1526;
    font-family: 'Nunito', sans-serif;
    font-size: 0.92rem; font-weight: 800;
    border: none; border-radius: 9px;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
    letter-spacing: 0.02em;
}
.sp-comments .comment-form input[type="submit"]:hover { opacity: 0.88; transform: translateY(-1px); }
.sp-comments .logged-in-as { font-size: 0.84rem; color: var(--sp-muted); margin-bottom: 14px !important; }
.sp-comments .logged-in-as a { color: var(--blue); font-weight: 700; text-decoration: none; }
.sp-comments .comment-notes { font-size: 0.82rem; color: var(--sp-muted); margin-bottom: 14px !important; }
.sp-comments .comment-form-comment { margin-bottom: 0; }

/* No-hero fallback (no featured image) */
.sp-hero.sp-hero-no-img { min-height: 300px; }

/* Responsive */
@media (max-width: 820px) {
    .sp-content { padding: 28px 22px; font-size: 0.98rem; }
    .sp-nav { grid-template-columns: 1fr; }
    .sp-author { flex-direction: column; gap: 14px; padding: 22px 20px; }
    .sp-comments { padding: 28px 20px; }
    .sp-share { gap: 10px; }
}
</style>

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
            <a href="<?php echo esc_url(home_url('/')); ?>">🏠 Home</a>
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
                    <?php echo get_avatar(get_the_author_meta('email'), 34, '', '', array('extra_attr' => 'loading="lazy"')) ?: '✍️'; ?>
                </div>
                <span><?php the_author(); ?></span>
            </div>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item">📅 <?php echo esc_html(get_the_date()); ?></div>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item">⏱ <?php echo esc_html($sp_read_min); ?> min read</div>
            <?php if ($sp_word_cnt > 0) : ?>
            <span class="sp-hero-divider">·</span>
            <div class="sp-hero-meta-item">📖 <?php echo number_format($sp_word_cnt); ?> words</div>
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
            💬 WhatsApp
        </a>
        <button class="sp-share-btn sp-share-copy" onclick="navigator.clipboard.writeText('<?php echo esc_js($sp_post_url); ?>').then(function(){this.textContent='✅ Copied!';}.bind(this))">
            🔗 Copy Link
        </button>
    </div>

    <!-- Author card -->
    <div class="sp-author">
        <div class="sp-author-avatar">
            <?php $sp_av = get_avatar($sp_author_id, 64, '', '', array('extra_attr' => 'loading="lazy"'));
            echo $sp_av ?: '✍️'; ?>
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
