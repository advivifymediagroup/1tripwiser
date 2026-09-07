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
$hero_title    = get_option('tw_blog_title',       'Blogs + Affiliates');
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
        'icon'       => get_post_meta($aid, '_taff_icon',        true) ?: 'link',
        'cta'        => get_post_meta($aid, '_taff_cta',         true) ?: 'Book',
    );
}
wp_reset_postdata();

// Fallback: if no CPT entries yet, use the old global options so nothing breaks
if (empty($affiliates)) {
    $affiliates = array(
        'booking'    => array('name'=>'Booking.com',  'desc'=>'Hotels & Stays',       'url'=>get_option('tw_aff_booking_url',    'https://www.booking.com'),    'category'=>'Hotels',             'commission'=>'4–6%',    'icon'=>'hotel', 'cta'=>'Book'),
        'skyscanner' => array('name'=>'Skyscanner',   'desc'=>'Cheap flights',         'url'=>get_option('tw_aff_skyscanner_url', 'https://www.skyscanner.com'),  'category'=>'Flights',            'commission'=>'Varies',  'icon'=>'plane', 'cta'=>'Compare'),
        'viator'     => array('name'=>'Viator',        'desc'=>'Tours & Activities',    'url'=>get_option('tw_aff_viator_url',     'https://www.viator.com'),      'category'=>'Tours & Activities', 'commission'=>'8–12%',   'icon'=>'ticket-alt', 'cta'=>'Explore'),
        'safetywing' => array('name'=>'SafetyWing',   'desc'=>'Travel Insurance',      'url'=>get_option('tw_aff_safetywing_url', 'https://www.safetywing.com'),  'category'=>'Travel Insurance',   'commission'=>'10%',     'icon'=>'shield', 'cta'=>'Insure'),
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
        <h1 class="tw-h1 ba-hero-title"><?php
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
                <span class="ba-aff-chip-icon"><?php tw_render_aff_icon($aff['icon']); ?></span>
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
        // Recalculate cached category post counts so they reflect reality.
        // (Counts can drift after WXR imports, bulk operations, etc.)
        $all_cat_ids = get_terms( array(
            'taxonomy'   => 'category',
            'fields'     => 'ids',
            'hide_empty' => false,
        ) );
        if ( ! is_wp_error( $all_cat_ids ) && ! empty( $all_cat_ids ) ) {
            wp_update_term_count_now( $all_cat_ids, 'category' );
        }

        $categories = get_categories( array(
            'hide_empty' => false,    // show ALL categories
            'number'     => 0,        // 0 = no limit
            'orderby'    => 'name',
            'order'      => 'ASC',
            'exclude'    => array( 1 ), // hide default "Uncategorized" (term_id 1)
        ) );
        $total_published = (int) wp_count_posts( 'post' )->publish;
        if ( $categories ) :
        ?>
        <div class="ba-filters">
            <button class="ba-filter-pill active" data-cat="all">
                All Posts <span class="ba-filter-count"><?php echo $total_published; ?></span>
            </button>
            <?php foreach ( $categories as $cat ) :
                $is_empty = ( (int) $cat->count === 0 );
                $cls      = 'ba-filter-pill' . ( $is_empty ? ' ba-filter-pill--empty' : '' );
            ?>
            <button class="<?php echo esc_attr( $cls ); ?>" data-cat="<?php echo esc_attr( $cat->slug ); ?>">
                <?php echo esc_html( $cat->name ); ?>
                <span class="ba-filter-count"><?php echo (int) $cat->count; ?></span>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- FEATURED POSTS — one per category (only the active one is visible) -->
        <?php
        /*
         * Build a featured post for "all" + one per non-empty category.
         * "all" = manually-flagged _is_featured post, falling back to latest.
         * Per-cat = latest post in that category.
         */
        $featured_per_cat = array();

        // "All Posts" featured slot
        $all_q = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_key'       => '_is_featured',
            'meta_value'     => '1',
        ) );
        if ( ! $all_q->have_posts() ) {
            $all_q = new WP_Query( array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
            ) );
        }
        if ( $all_q->have_posts() ) {
            $all_q->the_post();
            $featured_per_cat['all'] = get_post();
        }
        wp_reset_postdata();

        // One per category (skip categories with zero posts)
        foreach ( $categories as $cat ) {
            if ( (int) $cat->count === 0 ) { continue; }
            $cq = new WP_Query( array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'cat'            => $cat->term_id,
            ) );
            if ( $cq->have_posts() ) {
                $cq->the_post();
                $featured_per_cat[ $cat->slug ] = get_post();
            }
            wp_reset_postdata();
        }

        /* Render each featured block; only "all" is visible initially */
        foreach ( $featured_per_cat as $cat_slug => $featured_post ) :
            $GLOBALS['post'] = $featured_post;
            setup_postdata( $GLOBALS['post'] );
            $f_cats      = get_the_category( $featured_post->ID );
            $f_cat_slugs = $f_cats ? implode( ' ', wp_list_pluck( $f_cats, 'slug' ) ) : '';
            $f_visible   = ( $cat_slug === 'all' );
        ?>
        <div class="ba-featured"
             data-featured-for="<?php echo esc_attr( $cat_slug ); ?>"
             data-cats="<?php echo esc_attr( $f_cat_slugs ); ?>"
             data-post-id="<?php echo (int) $featured_post->ID; ?>"
             <?php if ( ! $f_visible ) : ?>class="hidden"<?php endif; ?>>
            <div class="ba-featured-img">
                <?php if ( has_post_thumbnail( $featured_post->ID ) ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $featured_post->ID ) ); ?>"><?php echo get_the_post_thumbnail( $featured_post->ID, 'large' ); ?></a>
                <?php else : ?>
                    <a href="<?php echo esc_url( get_permalink( $featured_post->ID ) ); ?>" class="ba-featured-img-placeholder"><i class="fi-rr-plane" aria-hidden="true"></i></a>
                <?php endif; ?>
                <span class="ba-featured-badge"><i class="fi-rr-star" aria-hidden="true"></i> Featured</span>
            </div>
            <div class="ba-featured-body">
                <div>
                    <div class="ba-featured-meta">
                        <?php echo esc_html( get_the_date( '', $featured_post->ID ) ); ?> &nbsp;·&nbsp;
                        <?php
                        $cat_links = array();
                        foreach ( (array) $f_cats as $fc ) {
                            $cat_links[] = '<a href="' . esc_url( get_category_link( $fc->term_id ) ) . '">' . esc_html( $fc->name ) . '</a>';
                        }
                        echo implode( ', ', $cat_links );
                        ?>
                    </div>
                    <h2 class="ba-featured-title"><a href="<?php echo esc_url( get_permalink( $featured_post->ID ) ); ?>"><?php echo esc_html( get_the_title( $featured_post->ID ) ); ?></a></h2>
                    <p class="ba-featured-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $featured_post->ID ), 28, '…' ) ); ?></p>
                </div>
                <div>
                    <a href="<?php echo esc_url( get_permalink( $featured_post->ID ) ); ?>" class="btn-primary">Read Full Guide →</a>
                </div>
            </div>
        </div>
        <?php
            wp_reset_postdata();
        endforeach;

        /* IDs of all featured posts — exclude them from the grid so we never duplicate */
        $featured_ids = array();
        foreach ( $featured_per_cat as $fp ) {
            if ( $fp ) { $featured_ids[] = (int) $fp->ID; }
        }
        $featured_ids = array_unique( $featured_ids );
        $post_id = isset( $featured_per_cat['all'] ) ? $featured_per_cat['all']->ID : 0; // legacy var kept for downstream code
        ?>

        <!-- BLOG GRID -->
        <div class="ba-section-row">
            <h2 class="tw-h2 ba-section-head">Latest Guides</h2>
            <div class="ba-section-row-actions">
                <a href="<?php echo esc_url( home_url('/blogs/') ); ?>" class="btn-secondary btn-sm">
                    View All Blogs →
                </a>
                <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url('/submit-blog/') ); ?>" class="btn-primary btn-sm">
                    <i class="fi-rr-edit-alt" aria-hidden="true"></i> Write a Post
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Load ALL published posts (minus the featured set) so the JS filter
        // can show category-specific posts without an AJAX round-trip.
        $grid_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'post__not_in'   => ! empty( $featured_ids ) ? $featured_ids : array( 0 ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
        ?>

        <?php if ($grid_query->have_posts()) : ?>
        <div class="ba-grid">
            <?php while ($grid_query->have_posts()) : $grid_query->the_post();
                $pid       = get_the_ID();
                $cats      = get_the_category();
                $cat_name  = $cats ? $cats[0]->name : '';
                $cat_slugs = $cats ? implode( ' ', wp_list_pluck( $cats, 'slug' ) ) : '';
            ?>
            <article class="ba-card" data-cats="<?php echo esc_attr( $cat_slugs ); ?>" data-post-id="<?php echo (int) $pid; ?>">
                <div class="ba-card-img">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                    <?php else : ?>
                        <a href="<?php the_permalink(); ?>" class="ba-card-img-placeholder"><i class="fi-rr-plane" aria-hidden="true"></i></a>
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

        <!-- Empty state for filter results — hidden until JS toggles it -->
        <div class="ba-no-filter-results hidden">
            <p><i class="fi-rr-inbox" aria-hidden="true"></i> No posts in this category yet. <button type="button" class="ba-reset-filter">Show all posts</button></p>
        </div>
        <?php else : ?>
        <div class="ba-no-posts">
            <p><i class="fi-rr-memo" aria-hidden="true"></i> No blog posts yet. <a href="<?php echo esc_url(admin_url('post-new.php')); ?>">Create your first post</a> in the WordPress admin.</p>
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
                        <td><span class="ba-partner-logo"><?php tw_render_aff_icon($aff['icon']); ?> <?php echo esc_html($aff['name']); ?></span></td>
                        <td><?php echo esc_html($aff['category']); ?></td>
                        <td><span class="ba-commission-rate"><?php echo esc_html($aff['commission'] ?: '—'); ?></span></td>
                        <td><?php echo esc_html($aff['desc'] ?: '—'); ?></td>
                        <td><a href="<?php echo esc_url($aff['url']); ?>" target="_blank" rel="noopener sponsored" class="btn-primary btn-sm"><?php echo esc_html($aff['cta']); ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <div class="ba-no-posts">
            <p>No affiliate partners added yet.<br>
            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=tw_affiliate')); ?>">Add your first affiliate link →</a></p>
        </div>
        <?php endif; ?>

    </div><!-- /.ba-page -->
</main>

<script>
// Category filter — swap featured block per category and show/hide cards
(function () {
    var pills      = document.querySelectorAll('.ba-filter-pill');
    var cards      = document.querySelectorAll('.ba-card[data-cats]');
    var featureds  = document.querySelectorAll('.ba-featured[data-featured-for]');
    var empty      = document.querySelector('.ba-no-filter-results');
    if (!pills.length) { return; }

    function matchesCat(el, cat) {
        if (cat === 'all') { return true; }
        var raw = (el.getAttribute('data-cats') || '').trim();
        if (!raw) { return false; }
        return (' ' + raw + ' ').indexOf(' ' + cat + ' ') !== -1;
    }

    function applyFilter(cat) {
        var visibleCount = 0;
        var activeFeaturedId = null;

        // Show only the featured block targeted at this category
        featureds.forEach(function (f) {
            var match = f.getAttribute('data-featured-for') === cat;
            f.style.display = match ? '' : 'none';
            if (match) {
                visibleCount++;
                activeFeaturedId = f.getAttribute('data-post-id');
            }
        });

        // Grid cards — hide non-matching, and de-dupe the active featured post
        cards.forEach(function (card) {
            var m = matchesCat(card, cat);
            var isFeaturedDup = activeFeaturedId && card.getAttribute('data-post-id') === activeFeaturedId;
            card.style.display = (m && !isFeaturedDup) ? '' : 'none';
            if (m && !isFeaturedDup) { visibleCount++; }
        });

        if (empty) {
            empty.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            pills.forEach(function (p) { p.classList.remove('active'); });
            pill.classList.add('active');
            applyFilter(pill.getAttribute('data-cat'));
        });
    });

    // "Show all posts" reset link inside the empty state
    var resetBtn = document.querySelector('.ba-reset-filter');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            var allPill = document.querySelector('.ba-filter-pill[data-cat="all"]');
            if (allPill) { allPill.click(); }
        });
    }

    // Init on load
    applyFilter('all');
})();
</script>

<?php get_footer(); ?>