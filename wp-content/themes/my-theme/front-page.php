<?php get_header(); ?>

<?php
/* Hero video / image background from admin settings */
$tw_hero_video        = get_option('tw_hero_video_url', '');
$tw_hero_image        = get_option('tw_hero_image_url', '');
$tw_hero_mobile_image = get_option('tw_hero_mobile_image_url', '');
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
                src="https://www.youtube.com/embed/<?php echo esc_attr($tw_yt_id); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo esc_attr($tw_yt_id); ?>&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&playsinline=1&disablekb=1&fs=0&cc_load_policy=0&color=white"
                frameborder="0" allow="autoplay; encrypted-media" loading="lazy" title=""></iframe>
            <div class="tw-hero-yt-shield" aria-hidden="true"></div>
        </div>
        <?php elseif ( $tw_hero_video ) : ?>
        <video class="tw-hero-vid" autoplay muted loop playsinline preload="metadata">
            <source src="<?php echo esc_url($tw_hero_video); ?>">
        </video>
        <?php elseif ( $tw_hero_image ) : ?>
        <div class="tw-hero-img" style="background-image:url('<?php echo esc_url($tw_hero_image); ?>')"></div>
        <?php endif; ?>

        <?php /* Mobile-only background image — overrides the video on phones via CSS @media (max-width:768px) */ ?>
        <?php if ( $tw_hero_mobile_image ) : ?>
        <div class="tw-hero-mobile-img" style="background-image:url('<?php echo esc_url($tw_hero_mobile_image); ?>')"></div>
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

    <!-- ═══════════ PACKAGES ═══════════ -->
    <section id="featured-packages" class="featured-posts travel-section">
        <div class="container">
            <div class="section-heading" data-reveal="up">
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
            <div class="section-heading" data-reveal="up">
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
            <div class="tw-blog-header" data-reveal="up">
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

    <!-- ═══════════ VISA SERVICES ═══════════ -->
    <section class="visa-services-section">
        <div class="container">
            <div class="section-heading visa-services-heading" data-reveal="up">
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
                <div class="tw-community-copy" data-reveal="left">
                    <span class="tw-community-kicker">Join Our Community</span>
                    <h2 class="tw-community-title">1TRIPWISER <span>TRIBE</span></h2>
                    <p>Connect, share, and grow with 300K+ travel enthusiasts. Ask questions, share tips, and get inspired by real travelers.</p>
                    <a href="<?php echo esc_url( post_type_exists('forum_topic') ? get_post_type_archive_link('forum_topic') : mytheme_get_plan_trip_url() ); ?>" class="tw-community-btn">Enter the Tribe →</a>
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

    <!-- ═══════════ INSTAGRAM ═══════════ -->
    <section class="instagram-feed-section">
        <div class="container">
            <div class="instagram-feed-heading">
                <span class="tw-ig-kicker">Follow the journey</span>
                <h2 class="tw-ig-title">Our Instagram</h2>
                <a class="tw-ig-handle" href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener noreferrer">📸 @1tripwiser</a>
            </div>
            <div class="instagram-feed-wrap">
                <?php echo do_shortcode('[instagram-feed feed=1]'); ?>
            </div>
        </div>
    </section>

    <!-- ═══════════ FREE ITINERARY CTA ═══════════ -->
    <section class="tw-cta-section">
        <div class="container">
            <div class="tw-cta-inner" data-reveal="up">
                <div class="tw-cta-copy">
                    <span class="tw-cta-kicker">100% Free · No Hidden Charges</span>
                    <h2 class="tw-cta-title">GET A <span>FREE</span> ITINERARY</h2>
                    <p>Tell us your dream destination — we'll craft a personalised trip plan.</p>
                </div>
                <a class="free-itinerary-link" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">✈ Plan My Trip</a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
