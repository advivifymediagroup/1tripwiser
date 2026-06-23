<?php
/**
 * Template Name: Blog & Affiliates
 *
 * Displays blog posts with inline affiliate links and an affiliate partner banner.
 * Styles are in style.css
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
        <div class="ba-section-row">
            <h2 class="ba-section-head">Latest Guides</h2>
            <div class="ba-section-row-actions">
                <a href="<?php echo esc_url( home_url('/blogs/') ); ?>" class="ba-view-all-btn">
                    View All Blogs →
                </a>
                <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url('/submit-blog/') ); ?>" class="ba-write-btn">
                    ✍️ Write a Post
                </a>
                <?php endif; ?>
            </div>
        </div>

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