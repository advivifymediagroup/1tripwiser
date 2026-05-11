<?php
/**
 * Template Name: Blog & Affiliates
 *
 * Displays blog posts with inline affiliate links and an affiliate partner banner.
 */

get_header();

// Pull editable content from WP Options (set in Admin → Trip Inquiries → Page Settings)
$hero_kicker   = get_option('tw_blog_kicker',      'TRAVEL GUIDES & AFFILIATE PICKS');
$hero_title    = get_option('tw_blog_title',       'BLOGS + AFFILIATES');
$hero_subtitle = get_option('tw_blog_subtitle',    'Honest travel guides. Trusted tools. Every link we share is something we actually use and believe in.');
$aff_heading   = get_option('tw_blog_aff_heading', 'Our Trusted Travel Partners');
$aff_text      = get_option('tw_blog_aff_text',    "We partner with travel platforms we personally trust. When you book through our links, you support our free content at no extra cost to you.");

// Load affiliates from the tw_affiliate CPT (ordered by _taff_order meta)
$aff_query = new WP_Query(array(
    'post_type'      => 'tw_affiliate',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => '_taff_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
));

$affiliates = array(); // indexed by post ID for easy lookup
while ($aff_query->have_posts()) {
    $aff_query->the_post();
    $aid = get_the_ID();
    $affiliates[$aid] = array(
        'id'         => $aid,
        'name'       => get_the_title(),
        'desc'       => get_the_excerpt(),
        'url'        => get_post_meta($aid, '_taff_url',        true),
        'category'   => get_post_meta($aid, '_taff_category',   true),
        'commission' => get_post_meta($aid, '_taff_commission',  true),
        'icon'       => get_post_meta($aid, '_taff_icon',        true) ?: '🔗',
        'cta'        => get_post_meta($aid, '_taff_cta',         true) ?: 'Book',
    );
}
wp_reset_postdata();

// Fallback: if no CPT entries yet, use the old global options so nothing breaks
if (empty($affiliates)) {
    $affiliates = array(
        'booking'    => array('name'=>'Booking.com',  'desc'=>'Hotels & Stays',       'url'=>get_option('tw_aff_booking_url',    'https://www.booking.com'),    'category'=>'Hotels',             'commission'=>'4–6%',    'icon'=>'🏨', 'cta'=>'Book'),
        'skyscanner' => array('name'=>'Skyscanner',   'desc'=>'Cheap flights',         'url'=>get_option('tw_aff_skyscanner_url', 'https://www.skyscanner.com'),  'category'=>'Flights',            'commission'=>'Varies',  'icon'=>'✈️', 'cta'=>'Compare'),
        'viator'     => array('name'=>'Viator',        'desc'=>'Tours & Activities',    'url'=>get_option('tw_aff_viator_url',     'https://www.viator.com'),      'category'=>'Tours & Activities', 'commission'=>'8–12%',   'icon'=>'🎟️', 'cta'=>'Explore'),
        'safetywing' => array('name'=>'SafetyWing',   'desc'=>'Travel Insurance',      'url'=>get_option('tw_aff_safetywing_url', 'https://www.safetywing.com'),  'category'=>'Travel Insurance',   'commission'=>'10%',     'icon'=>'🛡️', 'cta'=>'Insure'),
    );
}

// For backward-compat in per-post affiliate lookups, pick first aff by category
function tw_aff_by_cat($affiliates, $cat) {
    foreach ($affiliates as $a) {
        if (isset($a['category']) && stripos($a['category'], $cat) !== false && !empty($a['url'])) {
            return $a;
        }
    }
    return null;
}
$g_hotel_aff   = tw_aff_by_cat($affiliates, 'Hotel');
$g_flight_aff  = tw_aff_by_cat($affiliates, 'Flight');
$g_tours_aff   = tw_aff_by_cat($affiliates, 'Tour');
$g_insure_aff  = tw_aff_by_cat($affiliates, 'Insurance');
?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
/* ================================================================
   BLOG + AFFILIATES PAGE — full dark/light adaptive styles
   ================================================================ */
:root {
    --ba-bg:        #f5f8fa;
    --ba-card:      #ffffff;
    --ba-raised:    #f0f4f8;
    --ba-text:      #1a2535;
    --ba-muted:     #6b7a8f;
    --ba-label:     #374151;
    --ba-border:    #e0e8f0;
    --ba-head:      #0D1526;
    --ba-shadow:    0 6px 24px rgba(0,0,0,0.07);
    --gold:         #FCB415;
    --blue:         #0692AF;
    --green:        #306C35;
}

body { background-color: var(--ba-bg) !important; color: var(--ba-text); font-family: 'Nunito', sans-serif; }

.ba-page { max-width: 1200px; margin: 0 auto; padding: 0 24px 80px; }

/* ── Hero ─────────────────────────────────────────────────────── */
.ba-hero {
    padding: 64px 24px 52px;
    text-align: center;
    position: relative;
}

.ba-hero-kicker {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    color: var(--blue);
    background: rgba(6,146,175,0.1);
    border: 1px solid rgba(6,146,175,0.22);
    border-radius: 999px;
    padding: 4px 16px;
    margin-bottom: 18px;
}

.ba-hero-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.4rem, 6vw, 4rem);
    color: var(--ba-head);
    letter-spacing: 0.04em;
    line-height: 1.05;
    margin-bottom: 16px;
}

.ba-hero-title span { color: var(--gold); }

.ba-hero-sub {
    font-size: 1rem;
    color: var(--ba-muted);
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.7;
}

/* ── Affiliate Banner ─────────────────────────────────────────── */
.ba-aff-banner {
    background: linear-gradient(135deg, rgba(6,146,175,0.1) 0%, rgba(48,108,53,0.08) 100%);
    border: 1px solid rgba(6,146,175,0.2);
    border-radius: 16px;
    padding: 28px 32px;
    margin: 0 24px 40px;
    max-width: 1152px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    align-items: flex-start;
    gap: 28px;
    flex-wrap: wrap;
}

.ba-aff-text-col { flex: 1 1 260px; }

.ba-aff-heading {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.35rem;
    color: var(--ba-head);
    letter-spacing: 0.04em;
    margin-bottom: 8px;
}

.ba-aff-desc { font-size: 0.87rem; color: var(--ba-muted); line-height: 1.65; }

.ba-aff-chips { display: flex; gap: 10px; flex-wrap: wrap; flex: 1 1 320px; align-items: center; }

.ba-aff-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: var(--ba-card);
    border: 1px solid var(--ba-border);
    border-radius: 10px;
    text-decoration: none;
    color: var(--ba-text);
    font-size: 0.85rem;
    font-weight: 700;
    transition: border-color 0.2s, transform 0.15s;
}

.ba-aff-chip:hover { border-color: var(--gold); transform: translateY(-2px); }
.ba-aff-chip-icon { font-size: 1.1rem; }
.ba-aff-chip-label { color: var(--ba-muted); font-size: 0.72rem; font-weight: 600; display: block; }

/* ── Filter Pills ─────────────────────────────────────────────── */
.ba-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding: 0 0 28px;
    border-bottom: 1px solid var(--ba-border);
    margin-bottom: 36px;
}

.ba-filter-pill {
    padding: 6px 16px;
    border-radius: 999px;
    border: 1px solid var(--ba-border);
    background: transparent;
    color: var(--ba-muted);
    font-size: 0.83rem;
    font-weight: 700;
    cursor: pointer;
    font-family: 'Nunito', sans-serif;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
}

.ba-filter-pill.active,
.ba-filter-pill:hover {
    background: var(--gold);
    border-color: var(--gold);
    color: #0D1526;
}

/* ── Featured Post (big card) ─────────────────────────────────── */
.ba-featured {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    background: var(--ba-card);
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--ba-border);
    box-shadow: var(--ba-shadow);
    margin-bottom: 48px;
}

.ba-featured-img {
    position: relative;
    min-height: 340px;
    overflow: hidden;
    background: var(--ba-raised);
}

.ba-featured-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.ba-featured-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: var(--gold);
    color: #0D1526;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    padding: 4px 12px;
    border-radius: 999px;
}

.ba-featured-body {
    padding: 36px 32px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.ba-featured-meta {
    font-size: 0.78rem;
    color: var(--ba-muted);
    margin-bottom: 10px;
}

.ba-featured-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    color: var(--ba-head);
    letter-spacing: 0.03em;
    line-height: 1.15;
    margin-bottom: 12px;
}

.ba-featured-title a { color: inherit; text-decoration: none; transition: color 0.2s; }
.ba-featured-title a:hover { color: var(--blue); }

.ba-featured-excerpt { font-size: 0.92rem; color: var(--ba-muted); line-height: 1.7; margin-bottom: 20px; }

/* Affiliate link rows inside featured */
.ba-aff-rows { border-top: 1px solid var(--ba-border); padding-top: 18px; }

.ba-aff-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--ba-border);
    font-size: 0.84rem;
}

.ba-aff-row:last-child { border-bottom: none; }

.ba-aff-row-name { color: var(--ba-text); font-weight: 700; }

.ba-aff-row-sub { color: var(--ba-muted); font-size: 0.76rem; }

.ba-aff-row-link {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--blue);
    border: 1px solid rgba(6,146,175,0.35);
    border-radius: 5px;
    padding: 4px 12px;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    white-space: nowrap;
}

.ba-aff-row-link:hover { background: var(--blue); color: #fff; }

.ba-read-more {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--blue);
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none;
    margin-top: 16px;
}

.ba-read-more:hover { color: var(--gold); }

/* ── Blog Grid ────────────────────────────────────────────────── */
.ba-section-head {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.5rem;
    color: var(--ba-head);
    letter-spacing: 0.04em;
    margin-bottom: 20px;
}

.ba-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 56px;
}

.ba-card {
    background: var(--ba-card);
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--ba-border);
    box-shadow: var(--ba-shadow);
    display: flex;
    flex-direction: column;
    transition: transform 0.25s, box-shadow 0.25s;
}

.ba-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0,0,0,0.13); }

.ba-card-img {
    height: 190px;
    overflow: hidden;
    background: var(--ba-raised);
    position: relative;
}

.ba-card-img img { width: 100%; height: 100%; object-fit: cover; display: block; }

.ba-card-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--ba-raised), rgba(6,146,175,0.08));
    color: var(--ba-muted);
    font-size: 2rem;
}

.ba-card-cat {
    position: absolute;
    bottom: 10px;
    left: 10px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    background: rgba(6,146,175,0.85);
    color: #fff;
    padding: 3px 10px;
    border-radius: 999px;
}

.ba-card-body { padding: 18px 20px 20px; flex: 1; display: flex; flex-direction: column; }

.ba-card-meta { font-size: 0.76rem; color: var(--ba-muted); margin-bottom: 8px; }

.ba-card-title { font-size: 1rem; font-weight: 800; color: var(--ba-head); line-height: 1.4; margin-bottom: 8px; }

.ba-card-title a { color: inherit; text-decoration: none; transition: color 0.2s; }
.ba-card-title a:hover { color: var(--blue); }

.ba-card-excerpt { font-size: 0.85rem; color: var(--ba-muted); line-height: 1.6; margin-bottom: 14px; flex: 1; }

/* Inline affiliate row in card */
.ba-card-aff {
    border-top: 1px solid var(--ba-border);
    padding-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.ba-card-aff-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
}

.ba-card-aff-name { color: var(--ba-text); font-weight: 700; }

.ba-card-aff-link {
    color: var(--blue);
    font-size: 0.74rem;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid rgba(6,146,175,0.3);
    border-radius: 4px;
    padding: 2px 9px;
    transition: background 0.2s, color 0.2s;
}

.ba-card-aff-link:hover { background: var(--blue); color: #fff; }

/* ── Commission Table ─────────────────────────────────────────── */
.ba-table-wrap {
    background: var(--ba-card);
    border: 1px solid var(--ba-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--ba-shadow);
}

.ba-table-head {
    padding: 24px 28px 16px;
    border-bottom: 1px solid var(--ba-border);
}

.ba-table-head h2 { font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; color: var(--ba-head); letter-spacing: 0.04em; margin-bottom: 6px; }

.ba-table-head p { font-size: 0.85rem; color: var(--ba-muted); margin: 0; }

table.ba-table { width: 100%; border-collapse: collapse; }

table.ba-table th {
    text-align: left;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: var(--ba-muted);
    text-transform: uppercase;
    padding: 12px 20px;
    background: var(--ba-raised);
    border-bottom: 1px solid var(--ba-border);
}

table.ba-table td {
    padding: 14px 20px;
    font-size: 0.88rem;
    color: var(--ba-text);
    border-bottom: 1px solid var(--ba-border);
    vertical-align: middle;
}

table.ba-table tr:last-child td { border-bottom: none; }

table.ba-table tr:hover td { background: rgba(6,146,175,0.04); }

.ba-partner-logo { font-weight: 800; color: var(--ba-head); }

.ba-commission-rate { color: var(--green); font-weight: 800; }

.ba-table-book-btn {
    display: inline-block;
    padding: 5px 14px;
    background: linear-gradient(135deg, #FCB415 0%, #f09a00 100%);
    color: #0D1526;
    font-size: 0.78rem;
    font-weight: 800;
    border-radius: 6px;
    text-decoration: none;
    transition: opacity 0.2s;
}

.ba-table-book-btn:hover { opacity: 0.85; }

/* Read more link on cards */
.ba-card-read-more {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--blue);
    font-size: 0.83rem;
    font-weight: 800;
    text-decoration: none;
    margin-top: auto;
    padding-top: 12px;
    transition: color 0.2s;
}
.ba-card-read-more:hover { color: var(--gold); }

/* No posts message */
.ba-no-posts {
    text-align: center;
    padding: 60px 20px;
    color: var(--ba-muted);
    font-size: 0.95rem;
}

/* Responsive */
@media (max-width: 768px) {
    .ba-featured { grid-template-columns: 1fr; }
    .ba-featured-img { min-height: 220px; }
    .ba-featured-body { padding: 24px 20px; }
    .ba-aff-banner { flex-direction: column; gap: 18px; padding: 20px; }
    table.ba-table th, table.ba-table td { padding: 10px 14px; }
}
</style>

<main class="main-content">

    <!-- HERO -->
    <section class="ba-hero">
        <div class="ba-hero-kicker"><?php echo esc_html($hero_kicker); ?></div>
        <h1 class="ba-hero-title"><?php
            $parts = explode('+', $hero_title, 2);
            if (count($parts) === 2) {
                echo esc_html(trim($parts[0])) . ' + <span>' . esc_html(trim($parts[1])) . '</span>';
            } else {
                echo esc_html($hero_title);
            }
        ?></h1>
        <p class="ba-hero-sub"><?php echo esc_html($hero_subtitle); ?></p>
    </section>

    <!-- AFFILIATE BANNER -->
    <div class="ba-aff-banner">
        <div class="ba-aff-text-col">
            <div class="ba-aff-heading"><?php echo esc_html($aff_heading); ?></div>
            <p class="ba-aff-desc"><?php echo wp_kses_post($aff_text); ?></p>
        </div>
        <div class="ba-aff-chips">
            <?php foreach ($affiliates as $aff) :
                if (empty($aff['url'])) continue; ?>
            <a class="ba-aff-chip" href="<?php echo esc_url($aff['url']); ?>" target="_blank" rel="noopener sponsored">
                <span class="ba-aff-chip-icon"><?php echo esc_html($aff['icon']); ?></span>
                <span>
                    <strong><?php echo esc_html($aff['name']); ?></strong>
                    <span class="ba-aff-chip-label"><?php echo esc_html($aff['category'] ?: $aff['desc']); ?></span>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="ba-page">

        <!-- FILTER PILLS (category-based) -->
        <?php
        $categories = get_categories(array('hide_empty' => true, 'number' => 10));
        if ($categories) :
        ?>
        <div class="ba-filters">
            <button class="ba-filter-pill active" data-cat="all">All Posts</button>
            <?php foreach ($categories as $cat) : ?>
            <button class="ba-filter-pill" data-cat="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- FEATURED POST -->
        <?php
        $featured_query = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_key'       => '_is_featured',
            'meta_value'     => '1',
        ));

        // Fallback: just get latest post if no featured one
        if (!$featured_query->have_posts()) {
            $featured_query = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
            ));
        }

        if ($featured_query->have_posts()) :
            $featured_query->the_post();
            $post_id = get_the_ID();
        ?>
        <div class="ba-featured">
            <div class="ba-featured-img">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>" style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--ba-muted);font-size:3rem;">✈️</a>
                <?php endif; ?>
                <span class="ba-featured-badge">⭐ Featured</span>
            </div>
            <div class="ba-featured-body">
                <div>
                    <div class="ba-featured-meta">
                        <?php echo esc_html(get_the_date()); ?> &nbsp;·&nbsp;
                        <?php the_category(', '); ?>
                    </div>
                    <h2 class="ba-featured-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="ba-featured-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 28, '…'); ?></p>
                </div>
                <div>
                    <a href="<?php the_permalink(); ?>" class="ba-read-more">Read Full Guide →</a>
                </div>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        endif; // end featured
        ?>

        <!-- BLOG GRID -->
        <h2 class="ba-section-head">Latest Guides</h2>

        <?php
        // Get posts excluding the featured one
        $featured_id = isset($post_id) ? $post_id : 0;
        $grid_query  = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'post__not_in'   => $featured_id ? array($featured_id) : array(),
        ));
        ?>

        <?php if ($grid_query->have_posts()) : ?>
        <div class="ba-grid">
            <?php while ($grid_query->have_posts()) : $grid_query->the_post();
                $pid      = get_the_ID();
                $cats     = get_the_category();
                $cat_name = $cats ? $cats[0]->name : '';
            ?>
            <article class="ba-card">
                <div class="ba-card-img">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large'); ?></a>
                    <?php else : ?>
                        <a href="<?php the_permalink(); ?>" class="ba-card-img-placeholder">✈️</a>
                    <?php endif; ?>
                    <?php if ($cat_name) : ?>
                        <span class="ba-card-cat"><?php echo esc_html($cat_name); ?></span>
                    <?php endif; ?>
                </div>
                <div class="ba-card-body">
                    <div class="ba-card-meta"><?php echo esc_html(get_the_date()); ?></div>
                    <h3 class="ba-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="ba-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 22, '…'); ?></p>
                    <a href="<?php the_permalink(); ?>" class="ba-card-read-more">Read More →</a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php else : ?>
        <div class="ba-no-posts">
            <p>📝 No blog posts yet. <a href="<?php echo esc_url(admin_url('post-new.php')); ?>">Create your first post</a> in the WordPress admin.</p>
        </div>
        <?php endif; ?>

        <!-- AFFILIATE COMMISSION TABLE — dynamic from tw_affiliate CPT -->
        <?php if (!empty($affiliates)) : ?>
        <div class="ba-table-wrap">
            <div class="ba-table-head">
                <h2>Affiliate Partners &amp; Commission Disclosure</h2>
                <p>We believe in full transparency. Here are the partners we work with and how we earn when you book through our links — at no extra cost to you.</p>
            </div>
            <table class="ba-table">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>Category</th>
                        <th>Typical Commission</th>
                        <th>What You Get</th>
                        <th>Link</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($affiliates as $aff) :
                        if (empty($aff['url'])) continue; ?>
                    <tr>
                        <td><span class="ba-partner-logo"><?php echo esc_html($aff['icon'] . ' ' . $aff['name']); ?></span></td>
                        <td><?php echo esc_html($aff['category']); ?></td>
                        <td><span class="ba-commission-rate"><?php echo esc_html($aff['commission'] ?: '—'); ?></span></td>
                        <td><?php echo esc_html($aff['desc'] ?: '—'); ?></td>
                        <td><a href="<?php echo esc_url($aff['url']); ?>" target="_blank" rel="noopener sponsored" class="ba-table-book-btn"><?php echo esc_html($aff['cta']); ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <div class="ba-no-posts" style="border:1px solid var(--ba-border);border-radius:14px;background:var(--ba-card)">
            <p>No affiliate partners added yet.<br>
            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=tw_affiliate')); ?>" style="color:var(--blue);font-weight:700">Add your first affiliate link →</a></p>
        </div>
        <?php endif; ?>

    </div><!-- /.ba-page -->
</main>

<script>
// Simple category filter (front-end show/hide)
(function () {
    var pills = document.querySelectorAll('.ba-filter-pill');
    var cards = document.querySelectorAll('.ba-card');

    if (!pills.length || !cards.length) return;

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            pills.forEach(function (p) { p.classList.remove('active'); });
            pill.classList.add('active');

            var cat = pill.getAttribute('data-cat');
            cards.forEach(function (card) {
                if (cat === 'all') {
                    card.parentElement.style.display = '';
                } else {
                    var cardCat = card.querySelector('.ba-card-cat');
                    var match   = cardCat && cardCat.textContent.toLowerCase().replace(/\s+/g, '-') === cat;
                    card.parentElement.style.display = match ? '' : 'none';
                }
            });
        });
    });
})();
</script>

<?php get_footer(); ?>
