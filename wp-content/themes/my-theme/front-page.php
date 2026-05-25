<?php get_header(); ?>

<?php
/* Hero video / image background from admin settings */
$tw_hero_video = get_option('tw_hero_video_url', '');
$tw_hero_image = get_option('tw_hero_image_url', '');
$tw_yt_id = '';
if ( $tw_hero_video ) {
    if ( preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $tw_hero_video, $m ) ) {
        $tw_yt_id = $m[1];
    }
}
?>

<main class="main-content">

<!-- ═══════════════════════ HERO ═══════════════════════ -->
<section class="tw-hero" id="tw-hero-top">

    <div class="tw-hero-bg" aria-hidden="true">
        <?php if ( $tw_yt_id ) : ?>
        <div class="tw-hero-yt-wrap">
            <iframe class="tw-hero-yt"
                src="https://www.youtube.com/embed/<?php echo esc_attr($tw_yt_id); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo esc_attr($tw_yt_id); ?>&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&playsinline=1"
                frameborder="0" allow="autoplay; encrypted-media" allowfullscreen loading="lazy"></iframe>
        </div>
        <?php elseif ( $tw_hero_video ) : ?>
        <video class="tw-hero-vid" autoplay muted loop playsinline preload="metadata">
            <source src="<?php echo esc_url($tw_hero_video); ?>">
        </video>
        <?php elseif ( $tw_hero_image ) : ?>
        <div class="tw-hero-img" style="background-image:url('<?php echo esc_url($tw_hero_image); ?>')"></div>
        <?php endif; ?>
        <div class="tw-hero-overlay"></div>
    </div>

    <div class="tw-hero-accent tw-hero-accent--1" aria-hidden="true"></div>
    <div class="tw-hero-accent tw-hero-accent--2" aria-hidden="true"></div>

    <div class="container tw-hero-inner">

        <div class="tw-hero-pill">
            <span class="tw-hero-pill-dot"></span>
            India's Most Trusted Travel Community &nbsp;·&nbsp; 300K+ on Instagram
        </div>

        <h1 class="tw-hero-title">
            <span class="tw-hero-title-gold">1</span>TRIP<span class="tw-hero-title-accent">WISER</span>
        </h1>
        <p class="tw-hero-tagline">Wiser Trips · Better Memories</p>
        <p class="tw-hero-desc"><?php bloginfo('description'); ?></p>

        <div class="tw-hero-actions">
            <a href="#featured-packages" class="tw-hero-btn tw-hero-btn--primary">🗺️ Explore Packages</a>
            <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="tw-hero-btn tw-hero-btn--outline">✈️ Plan a Trip — Free</a>
        </div>

        <div class="tw-hero-tags">
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">⛰️ Mountains</a>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">🏖️ Beaches</a>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">🌴 Offbeat</a>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">💍 Honeymoon</a>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">👥 Group Trips</a>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">💰 Budget</a>
            <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-hero-tag">🌍 International</a>
        </div>
    </div>

    <div class="tw-hero-scroll" aria-hidden="true"><div class="tw-hero-scroll-line"></div></div>
</section>

<!-- ═══════════════════════ STATS ═══════════════════════ -->
<section class="tw-stats-bar">
    <div class="tw-stats-inner">
        <div class="tw-stat"><span class="tw-stat-num">300K+</span><span class="tw-stat-label">Instagram Community</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">1000+</span><span class="tw-stat-label">Trips Planned</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">58+</span><span class="tw-stat-label">Destinations</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">4.9 ⭐</span><span class="tw-stat-label">Average Rating</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">₹0</span><span class="tw-stat-label">Planning Fee</span></div>
    </div>
</section>

<style>
/* ── Hero ── */
.tw-hero { position:relative; min-height:100svh; display:flex; align-items:center; overflow:hidden; background:#0d1526; }
.tw-hero-bg { position:absolute; inset:0; z-index:0; }
.tw-hero-yt-wrap { position:absolute; inset:-10%; pointer-events:none; }
.tw-hero-yt { width:100%; height:100%; object-fit:cover; }
.tw-hero-vid { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
.tw-hero-img { position:absolute; inset:0; background-size:cover; background-position:center; transform:scale(1.04); transition:transform 12s ease; }
.tw-hero:hover .tw-hero-img { transform:scale(1.0); }
.tw-hero-overlay { position:absolute; inset:0; background: linear-gradient(to bottom,rgba(13,21,38,0.6) 0%,rgba(13,21,38,0.25) 40%,rgba(13,21,38,0.8) 100%), linear-gradient(to right,rgba(13,21,38,0.75) 0%,transparent 65%); }
.tw-hero-accent { position:absolute; border-radius:50%; pointer-events:none; z-index:1; animation:tw-blob-float 8s ease-in-out infinite; }
.tw-hero-accent--1 { width:520px; height:520px; background:radial-gradient(circle,rgba(6,146,175,0.18) 0%,transparent 70%); top:-120px; right:-80px; }
.tw-hero-accent--2 { width:380px; height:380px; background:radial-gradient(circle,rgba(252,180,21,0.12) 0%,transparent 70%); bottom:80px; left:-60px; animation-delay:-4s; }
@keyframes tw-blob-float { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-28px) scale(1.04)} }
.tw-hero-inner { position:relative; z-index:2; padding:120px 20px 80px; max-width:860px; }
.tw-hero-pill { display:inline-flex; align-items:center; gap:10px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.18); backdrop-filter:blur(8px); color:rgba(255,255,255,0.9); font-size:0.78rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; padding:8px 18px; border-radius:999px; margin-bottom:28px; animation:tw-hero-fadein 0.9s ease both; }
.tw-hero-pill-dot { width:7px; height:7px; background:#25D366; border-radius:50%; flex-shrink:0; box-shadow:0 0 0 3px rgba(37,211,102,0.3); animation:tw-pulse 2s ease infinite; }
@keyframes tw-pulse { 0%,100%{box-shadow:0 0 0 3px rgba(37,211,102,0.3)} 50%{box-shadow:0 0 0 7px rgba(37,211,102,0.1)} }
.tw-hero-title { font-family:'Bebas Neue',sans-serif; font-size:clamp(3.8rem,10vw,8rem); color:#fff; letter-spacing:0.04em; line-height:0.95; margin:0 0 16px; animation:tw-hero-fadein 1s ease 0.15s both; }
.tw-hero-title-gold { color:#FCB415; }
.tw-hero-title-accent { color:#0692AF; }
.tw-hero-tagline { font-size:1.05rem; font-weight:700; color:rgba(255,255,255,0.65); letter-spacing:0.2em; text-transform:uppercase; margin-bottom:16px; animation:tw-hero-fadein 1s ease 0.28s both; }
.tw-hero-desc { font-size:1.08rem; color:rgba(255,255,255,0.72); line-height:1.7; max-width:580px; margin-bottom:36px; animation:tw-hero-fadein 1s ease 0.4s both; }
.tw-hero-actions { display:flex; gap:14px; flex-wrap:wrap; margin-bottom:48px; animation:tw-hero-fadein 1s ease 0.52s both; }
.tw-hero-btn { display:inline-flex; align-items:center; gap:8px; padding:14px 28px; border-radius:10px; font-size:0.95rem; font-weight:800; text-decoration:none; transition:transform 0.2s,box-shadow 0.2s,background 0.2s; position:relative; overflow:hidden; }
.tw-hero-btn:hover { transform:translateY(-3px); }
.tw-hero-btn--primary { background:linear-gradient(135deg,#FCB415 0%,#f09a00 100%); color:#0d1526; box-shadow:0 8px 28px rgba(252,180,21,0.35); }
.tw-hero-btn--primary:hover { box-shadow:0 12px 36px rgba(252,180,21,0.45); color:#0d1526; }
.tw-hero-btn--outline { background:rgba(255,255,255,0.08); border:1.5px solid rgba(255,255,255,0.3); color:#fff; backdrop-filter:blur(6px); }
.tw-hero-btn--outline:hover { background:rgba(255,255,255,0.16); border-color:rgba(255,255,255,0.5); color:#fff; }
.tw-hero-tags { display:flex; gap:10px; flex-wrap:wrap; animation:tw-hero-fadein 1s ease 0.65s both; }
.tw-hero-tag { display:inline-block; background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(4px); color:rgba(255,255,255,0.8); font-size:0.8rem; font-weight:700; padding:7px 16px; border-radius:999px; text-decoration:none; transition:background 0.2s,border-color 0.2s,color 0.2s; }
.tw-hero-tag:hover { background:rgba(252,180,21,0.15); border-color:rgba(252,180,21,0.4); color:#FCB415; }
.tw-hero-scroll { position:absolute; bottom:32px; left:50%; transform:translateX(-50%); z-index:2; }
.tw-hero-scroll-line { width:1.5px; height:52px; background:linear-gradient(to bottom,rgba(255,255,255,0.6),transparent); animation:tw-scroll-bounce 1.8s ease-in-out infinite; }
@keyframes tw-scroll-bounce { 0%,100%{transform:scaleY(1) translateY(0);opacity:0.8} 50%{transform:scaleY(0.6) translateY(8px);opacity:0.3} }
@keyframes tw-hero-fadein { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }
@media(max-width:600px){.tw-hero-inner{padding:100px 20px 60px} .tw-hero-actions{flex-direction:column} .tw-hero-btn{justify-content:center;text-align:center}}

/* ── Stats bar ── */
.tw-stats-bar { background:#0d1526; border-bottom:1px solid rgba(6,146,175,0.2); }
.tw-stats-inner { max-width:1200px; margin:0 auto; padding:0 20px; display:flex; align-items:stretch; justify-content:center; flex-wrap:wrap; }
.tw-stat { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:28px 36px; gap:6px; flex:1 1 140px; transition:background 0.2s; }
.tw-stat:hover { background:rgba(255,255,255,0.04); }
.tw-stat-num { font-family:'Bebas Neue',sans-serif; font-size:2.1rem; letter-spacing:0.04em; line-height:1; background:linear-gradient(135deg,#FCB415,#0692AF); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.tw-stat-label { font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.45); }
.tw-stat-divider { width:1px; background:rgba(255,255,255,0.08); align-self:stretch; margin:12px 0; }
@media(max-width:600px){.tw-stat-divider{display:none} .tw-stat{padding:20px 16px;flex:1 1 90px} .tw-stat-num{font-size:1.6rem}}
</style>

    <!-- ═══════════ PACKAGES ═══════════ -->
    <section id="featured-packages" class="featured-posts travel-section">
        <div class="container">
            <div class="section-heading">
                <span>Curated trips</span>
                <h2>Popular Travel Packages</h2>
            </div>
            <?php mytheme_travel_filter_box('travel_package', 'package_filter', home_url('/'), '#featured-packages'); ?>
            <div class="posts-grid">
                <?php
                $package_filter = mytheme_get_active_travel_filter("package_filter", "travel_package");
                $package_args = ["post_type" => "travel_package", "posts_per_page" => 3, "post_status" => "publish"];
                $package_meta_query = mytheme_build_travel_filter_meta_query("travel_package", $package_filter);
                if (!empty($package_meta_query)) { $package_args["meta_query"] = $package_meta_query; }
                $packages = new WP_Query($package_args);
                if ($packages->have_posts()):
                    while ($packages->have_posts()): $packages->the_post(); mytheme_package_card(); endwhile;
                    wp_reset_postdata();
                else: ?>
                    <?php mytheme_render_package_empty_state(
                        home_url('/#featured-packages'),
                        __('No packages match this homepage filter right now. Reset the filters or ask us to plan a custom trip for you.', 'mytheme')
                    ); ?>
                <?php endif; ?>
            </div>
            <div class="view-all">
                <a href="<?php echo esc_url(get_post_type_archive_link("travel_package")); ?>" class="btn-secondary">View All Packages</a>
            </div>
        </div>
    </section>

    <!-- ═══════════ ITINERARIES ═══════════ -->
    <section id="upcoming-trips" class="featured-posts itinerary-section">
        <div class="container">
            <div class="section-heading">
                <span>CURATED BY OUR EXPERTS</span>
                <h2>UPCOMING TRIPS</h2>
            </div>
            <?php mytheme_travel_filter_box('itinerary', 'itinerary_filter', home_url('/'), '#upcoming-trips'); ?>
            <div class="posts-grid">
                <?php
                $itinerary_filter = mytheme_get_active_travel_filter("itinerary_filter", "itinerary");
                $itinerary_args = ["post_type" => "itinerary", "posts_per_page" => 3, "post_status" => "publish"];
                $itinerary_meta_query = mytheme_build_travel_filter_meta_query("itinerary", $itinerary_filter);
                if (!empty($itinerary_meta_query)) { $itinerary_args["meta_query"] = $itinerary_meta_query; }
                $itineraries = new WP_Query($itinerary_args);
                if ($itineraries->have_posts()):
                    while ($itineraries->have_posts()):
                        $itineraries->the_post();
                        $duration      = mytheme_get_travel_field("itinerary_duration");
                        $best_time     = mytheme_get_travel_field("itinerary_best_time");
                        $route_summary = mytheme_get_travel_field("itinerary_route_summary"); ?>
                    <article class="post-card travel-card">
                        <?php if (has_post_thumbnail()): ?>
                        <div class="post-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail("medium"); ?></a></div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="travel-meta">
                                <?php if ($duration): ?><span><?php echo esc_html($duration); ?></span><?php endif; ?>
                                <?php if ($best_time): ?><span><?php echo esc_html($best_time); ?></span><?php endif; ?>
                            </div>
                            <div class="post-excerpt">
                                <?php $summary = $route_summary ? wp_strip_all_tags($route_summary) : get_the_excerpt(); echo wp_trim_words($summary, 28, '...'); ?>
                                <a href="<?php the_permalink(); ?>" class="inline-read-more">Read More</a>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Open Itinerary</a>
                        </div>
                    </article>
                    <?php endwhile; wp_reset_postdata();
                else: ?>
                    <?php mytheme_render_itinerary_empty_state(
                        home_url('/#upcoming-trips'),
                        __('No itineraries match this homepage filter right now. Reset the filters or ask us to create a custom route for you.', 'mytheme')
                    ); ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════ BLOG ═══════════ -->
    <section id="featured-posts" class="tw-blog-section">
        <div class="container">
            <div class="tw-blog-header">
                <div>
                    <div class="tw-blog-kicker">✈ From the Blog</div>
                    <h2 class="tw-blog-title">Latest Travel Stories</h2>
                </div>
                <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-blog-viewall">All Stories <span aria-hidden="true">→</span></a>
            </div>
            <?php
            $tw_blog_q = new WP_Query(['posts_per_page'=>3,'post_status'=>'publish','ignore_sticky_posts'=>true]);
            if ( $tw_blog_q->have_posts() ) : $tw_blog_posts = $tw_blog_q->posts; wp_reset_postdata(); ?>
            <div class="tw-blog-grid">
                <?php foreach ( $tw_blog_posts as $tw_idx => $tw_p ) :
                    $tw_id       = $tw_p->ID;
                    $tw_url      = get_permalink($tw_id);
                    $tw_title    = get_the_title($tw_id);
                    $tw_date     = get_the_date('M j, Y', $tw_id);
                    $tw_author   = get_the_author_meta('display_name', $tw_p->post_author);
                    $tw_avatar   = get_avatar($tw_p->post_author, 28, '', '', ['class'=>'']);
                    $tw_cats     = get_the_category($tw_id);
                    $tw_cat_name = $tw_cats ? esc_html($tw_cats[0]->name) : 'Travel';
                    $tw_words    = str_word_count(strip_tags($tw_p->post_content));
                    $tw_read     = max(1, round($tw_words / 200)) . ' min read';
                    $tw_excerpt  = wp_trim_words(get_the_excerpt($tw_id) ?: wp_strip_all_tags($tw_p->post_content), 22, '…');
                    $tw_thumb    = get_the_post_thumbnail_url($tw_id, $tw_idx === 0 ? 'large' : 'medium_large');
                    $tw_is_main  = ($tw_idx === 0); ?>
                <article class="tw-blog-card <?php echo $tw_is_main ? 'tw-blog-card--main' : 'tw-blog-card--side'; ?>">
                    <a class="tw-blog-card-img-wrap" href="<?php echo esc_url($tw_url); ?>" tabindex="-1" aria-hidden="true">
                        <?php if ($tw_thumb) : ?>
                        <img src="<?php echo esc_url($tw_thumb); ?>" alt="" loading="<?php echo $tw_idx === 0 ? 'eager' : 'lazy'; ?>" class="tw-blog-card-img">
                        <?php else : ?>
                        <div class="tw-blog-card-img tw-blog-card-img--placeholder"></div>
                        <?php endif; ?>
                        <div class="tw-blog-card-overlay"></div>
                        <span class="tw-blog-card-cat"><?php echo $tw_cat_name; ?></span>
                    </a>
                    <div class="tw-blog-card-body">
                        <h3 class="tw-blog-card-title"><a href="<?php echo esc_url($tw_url); ?>"><?php echo esc_html($tw_title); ?></a></h3>
                        <?php if ($tw_is_main) : ?><p class="tw-blog-card-excerpt"><?php echo esc_html($tw_excerpt); ?></p><?php endif; ?>
                        <div class="tw-blog-card-meta">
                            <div class="tw-blog-card-author"><div class="tw-blog-card-avatar"><?php echo $tw_avatar; ?></div><span><?php echo esc_html($tw_author); ?></span></div>
                            <div class="tw-blog-card-info"><span class="tw-blog-card-date"><?php echo esc_html($tw_date); ?></span><span class="tw-blog-card-dot" aria-hidden="true">·</span><span class="tw-blog-card-read"><?php echo esc_html($tw_read); ?></span></div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
            <div class="tw-blog-empty"><p>No posts yet — <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>">be the first to write one!</a></p></div>
            <?php endif; ?>
        </div>
    </section>

    <style>
    .tw-blog-section{padding:80px 0 88px;background:linear-gradient(180deg,#0d1526 0%,#0a1e30 100%);position:relative;overflow:hidden}
    .tw-blog-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 10% 60%,rgba(6,146,175,0.12) 0%,transparent 55%),radial-gradient(ellipse at 90% 20%,rgba(252,180,21,0.07) 0%,transparent 50%);pointer-events:none}
    .tw-blog-section .container{position:relative;z-index:1}
    .tw-blog-header{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:40px;flex-wrap:wrap}
    .tw-blog-kicker{font-size:0.78rem;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;color:#FCB415;margin-bottom:8px}
    .tw-blog-title{font-family:'Bebas Neue',sans-serif;font-size:clamp(2rem,4vw,3rem);color:#fff;letter-spacing:0.04em;line-height:1;margin:0}
    .tw-blog-viewall{display:inline-flex;align-items:center;gap:6px;font-size:0.88rem;font-weight:800;color:#FCB415;text-decoration:none;border:1.5px solid rgba(252,180,21,0.4);padding:9px 20px;border-radius:999px;transition:background 0.2s,color 0.2s,border-color 0.2s;white-space:nowrap;flex-shrink:0}
    .tw-blog-viewall:hover{background:#FCB415;color:#0d1526;border-color:#FCB415}
    .tw-blog-grid{display:grid;grid-template-columns:1.45fr 1fr;grid-template-rows:auto auto;gap:20px}
    .tw-blog-card--main{grid-row:1/3}
    .tw-blog-card{position:relative;border-radius:18px;overflow:hidden;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;flex-direction:column;transition:transform 0.3s ease,box-shadow 0.3s ease,border-color 0.3s ease}
    .tw-blog-card:hover{transform:translateY(-5px);box-shadow:0 24px 64px rgba(0,0,0,0.45);border-color:rgba(252,180,21,0.35)}
    .tw-blog-card-img-wrap{display:block;position:relative;overflow:hidden;flex-shrink:0}
    .tw-blog-card--main .tw-blog-card-img-wrap{height:280px}
    .tw-blog-card--side .tw-blog-card-img-wrap{height:170px}
    .tw-blog-card-img{width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.55s ease}
    .tw-blog-card:hover .tw-blog-card-img{transform:scale(1.06)}
    .tw-blog-card-img--placeholder{background:linear-gradient(135deg,#0a1e30,#0d2d44)}
    .tw-blog-card-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(13,21,38,0.72) 0%,rgba(13,21,38,0.1) 55%,transparent 100%);transition:opacity 0.3s}
    .tw-blog-card:hover .tw-blog-card-overlay{opacity:0.85}
    .tw-blog-card-cat{position:absolute;top:14px;left:14px;background:rgba(252,180,21,0.92);color:#0d1526;font-size:0.7rem;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;padding:4px 11px;border-radius:999px}
    .tw-blog-card-body{padding:20px 22px 22px;display:flex;flex-direction:column;gap:10px;flex:1}
    .tw-blog-card-title{font-family:'Nunito',sans-serif;font-weight:800;line-height:1.35;margin:0}
    .tw-blog-card--main .tw-blog-card-title{font-size:1.22rem}
    .tw-blog-card--side .tw-blog-card-title{font-size:0.98rem}
    .tw-blog-card-title a{color:#fff;text-decoration:none;transition:color 0.2s}
    .tw-blog-card-title a:hover{color:#FCB415}
    .tw-blog-card-excerpt{font-size:0.88rem;color:rgba(255,255,255,0.62);line-height:1.65;margin:0}
    .tw-blog-card-meta{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-top:auto;padding-top:12px;border-top:1px solid rgba(255,255,255,0.07)}
    .tw-blog-card-author{display:flex;align-items:center;gap:8px;font-size:0.8rem;font-weight:700;color:rgba(255,255,255,0.72)}
    .tw-blog-card-avatar{width:28px;height:28px;border-radius:50%;overflow:hidden;border:2px solid rgba(252,180,21,0.5);flex-shrink:0;background:rgba(252,180,21,0.1);display:flex;align-items:center;justify-content:center}
    .tw-blog-card-avatar img{width:100%;height:100%;object-fit:cover;display:block}
    .tw-blog-card-info{display:flex;align-items:center;gap:5px;font-size:0.76rem;color:rgba(255,255,255,0.45);font-weight:600;flex-shrink:0}
    .tw-blog-card-dot{opacity:0.4}
    .tw-blog-empty{text-align:center;padding:48px 24px;color:rgba(255,255,255,0.5)}
    .tw-blog-empty a{color:#FCB415;font-weight:800;text-decoration:none}
    @media(max-width:768px){.tw-blog-grid{grid-template-columns:1fr;grid-template-rows:auto} .tw-blog-card--main{grid-row:auto} .tw-blog-card--main .tw-blog-card-img-wrap{height:220px} .tw-blog-card--side .tw-blog-card-img-wrap{height:150px} .tw-blog-section{padding:56px 0 64px}}
    </style>

    <!-- ═══════════ VISA SERVICES ═══════════ -->
    <section class="visa-services-section">
        <div class="container">
            <div class="section-heading visa-services-heading">
                <span class="section-subtitle">Travel made simple</span>
                <h2 class="section-title">Visa <span class="highlight">Services</span></h2>
            </div>
            <div class="visa-services-grid">
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('visa-assistance')); ?>">
                    <span class="visa-service-icon">📄</span>
                    <h3>Visa Assistance</h3>
                    <p>Complete guidance for Schengen, UK, US, and Asia visas. We handle documentation, interviews, and follow-ups.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('passport-services')); ?>">
                    <span class="visa-service-icon">🛂</span>
                    <h3>Passport Services</h3>
                    <p>New passport, renewal, or emergency services. Fast-track assistance for urgent travel plans.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('travel-insurance')); ?>">
                    <span class="visa-service-icon">🌍</span>
                    <h3>Travel Insurance</h3>
                    <p>Protect your trip with medical, cancellation, baggage, and emergency coverage for domestic and international travel.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════ COMMUNITY ═══════════ -->
    <section class="tw-community-section">
        <div class="container">
            <div class="tw-community-inner">
                <div class="tw-community-copy">
                    <span class="tw-community-kicker">Join Our Community</span>
                    <h2 class="tw-community-title">1TRIPWISER <span>TRIBE</span></h2>
                    <p>Connect, share, and grow with 300K+ travel enthusiasts. Ask questions, share tips, and get inspired by real travelers.</p>
                    <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="tw-community-btn">Join Free →</a>
                </div>
                <div class="tw-community-stats">
                    <div class="tw-cstat"><strong>12.5K</strong><span>Active Members</span></div>
                    <div class="tw-cstat"><strong>48K+</strong><span>Discussions</span></div>
                    <div class="tw-cstat"><strong>1M+</strong><span>Trip Photos</span></div>
                    <div class="tw-cstat"><strong>500+</strong><span>Weekly Posts</span></div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .tw-community-section{background:linear-gradient(135deg,#0d1526 0%,#0a1e30 100%);padding:80px 0;position:relative;overflow:hidden}
    .tw-community-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%,rgba(252,180,21,0.08) 0%,transparent 60%),radial-gradient(ellipse at 20% 80%,rgba(6,146,175,0.1) 0%,transparent 55%);pointer-events:none}
    .tw-community-inner{position:relative;z-index:1;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
    .tw-community-kicker{font-size:0.78rem;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;color:#0692AF;display:block;margin-bottom:12px}
    .tw-community-title{font-family:'Bebas Neue',sans-serif;font-size:clamp(2.4rem,5vw,3.6rem);color:#fff;letter-spacing:0.04em;line-height:1;margin:0 0 20px}
    .tw-community-title span{color:#FCB415}
    .tw-community-copy p{color:rgba(255,255,255,0.65);font-size:1rem;line-height:1.75;margin-bottom:28px;max-width:480px}
    .tw-community-btn{display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#FCB415,#f09a00);color:#0d1526;font-weight:800;font-size:0.95rem;padding:12px 26px;border-radius:8px;text-decoration:none;transition:opacity 0.2s,transform 0.2s}
    .tw-community-btn:hover{opacity:0.88;transform:translateY(-2px);color:#0d1526}
    .tw-community-stats{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .tw-cstat{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09);border-radius:16px;padding:28px 24px;text-align:center;transition:border-color 0.2s,background 0.2s}
    .tw-cstat:hover{background:rgba(255,255,255,0.08);border-color:rgba(252,180,21,0.25)}
    .tw-cstat strong{display:block;font-family:'Bebas Neue',sans-serif;font-size:2.2rem;letter-spacing:0.04em;background:linear-gradient(135deg,#FCB415,#0692AF);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:6px}
    .tw-cstat span{font-size:0.75rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.45)}
    @media(max-width:768px){.tw-community-inner{grid-template-columns:1fr;gap:40px} .tw-community-title{font-size:2.4rem}}
    @media(max-width:480px){.tw-community-stats{grid-template-columns:1fr 1fr}}
    </style>

    <!-- ═══════════ INSTAGRAM ═══════════ -->
    <section class="instagram-feed-section">
        <div class="container">
            <div class="section-heading instagram-feed-heading">
                <span>Follow the journey</span>
            </div>
            <div class="instagram-feed-wrap">
                <?php echo do_shortcode('[instagram-feed feed=3]'); ?>
            </div>
        </div>
    </section>

    <!-- ═══════════ FREE ITINERARY CTA ═══════════ -->
    <section class="tw-cta-section">
        <div class="container">
            <div class="tw-cta-inner">
                <div class="tw-cta-copy">
                    <span class="tw-cta-kicker">100% Free · No Hidden Charges</span>
                    <h2 class="tw-cta-title">GET A <span>FREE</span> ITINERARY</h2>
                    <p>Tell us your dream destination — we'll craft a personalised trip plan.</p>
                </div>
                <a class="free-itinerary-link" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">✈ Plan My Trip</a>
            </div>
        </div>
    </section>

    <style>
    .tw-cta-section{background:linear-gradient(135deg,rgba(6,146,175,0.12),rgba(48,108,53,0.08));border-top:1px solid rgba(6,146,175,0.15);border-bottom:1px solid rgba(6,146,175,0.15);padding:72px 0}
    .tw-cta-inner{display:flex;gap:48px;align-items:center;flex-wrap:wrap;justify-content:space-between}
    .tw-cta-kicker{font-size:0.72rem;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;color:#0692AF;display:block;margin-bottom:10px}
    .tw-cta-title{font-family:'Bebas Neue',sans-serif;font-size:clamp(2rem,4vw,3rem);color:#0d1526;letter-spacing:0.04em;line-height:1;margin:0 0 10px}
    .tw-cta-title span{color:#FCB415}
    .tw-cta-copy p{color:#6b7a8f;font-size:1rem;line-height:1.65;margin:0}
    .tw-cta-form{display:flex;gap:0;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(13,21,38,0.12);overflow:hidden;min-width:340px;flex-shrink:0;border:1.5px solid rgba(6,146,175,0.2)}
    .tw-cta-form input{flex:1;padding:16px 20px;border:none;font-family:'Nunito',sans-serif;font-size:0.96rem;color:#0d1526;background:transparent;outline:none}
    .tw-cta-form input::placeholder{color:#b0bac9}
    .tw-cta-form button{padding:16px 24px;background:linear-gradient(135deg,#FCB415,#f09a00);border:none;color:#0d1526;font-family:'Nunito',sans-serif;font-size:0.9rem;font-weight:800;cursor:pointer;white-space:nowrap;transition:opacity 0.2s}
    .tw-cta-form button:hover{opacity:0.88}
    @media(max-width:700px){.tw-cta-inner{flex-direction:column;gap:28px} .tw-cta-form{min-width:0;width:100%} .tw-cta-copy{text-align:center} .tw-cta-copy p{max-width:100%}}
    </style>

</main>

<?php get_footer(); ?>
