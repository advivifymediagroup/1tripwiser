<?php get_header(); ?>

<?php
/* Hero video / image background from admin settings */
$tw_hero_video        = get_option('tw_hero_video_url', '');
$tw_hero_image        = get_option('tw_hero_image_url', '');
$tw_hero_mobile_image = get_option('tw_hero_mobile_image_url', '');

/* Hero floating images — small tilted photos that float around the video */
$tw_hero_float_images = array(
    'tr' => get_option( 'tw_hero_float_img_tr', '' ),
    'ml' => get_option( 'tw_hero_float_img_ml', '' ),
    'br' => get_option( 'tw_hero_float_img_br', '' ),
);
$tw_yt_id = '';
if ( $tw_hero_video ) {
    if ( preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $tw_hero_video, $m ) ) {
        $tw_yt_id = $m[1];
    }
}
?>

<main class="main-content">

<!-- ═══════════════════════ HERO — copy left, video in a right-aligned container ═══════════════════════ -->
<section class="tw-hero" id="tw-hero-top">

    <div class="tw-hero-split">

        <div class="tw-hero-copy">

            <div class="tw-hero-pill">
              
                India's Most Trusted Travel Community <span class="tw-hero-pill-dot"></span> 300K+ on Instagram
            </div>

            <h1 class="tw-h1">
                Trips designed around <span class="tw-hero-title-accent">you</span>.<br>
                <span class="tw-hero-title-quiet">Not around a package.</span>
            </h1>

            <form class="tw-hero-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="tw-hero-search-field">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" name="s" class="tw-hero-search-input" placeholder="Where do you want to go? Try Bali, Ladakh, Honeymoon&hellip;" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
                </div>
                <button type="submit" class="btn-primary btn-sm">Search</button>
            </form>

            <div class="tw-hero-actions">
                <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-primary">Plan a Trip &mdash; Free</a>
                <a href="#featured-packages" class="btn-secondary">Explore Packages</a>
            </div>
        </div>

        <!-- Right-aligned media container — video plays here, not as a full-bleed section background -->
        <div class="tw-hero-media">
            <div class="tw-hero-media-frame">
                <?php if ( $tw_yt_id ) : ?>
                <div class="tw-hero-yt-wrap">
                    <iframe class="tw-hero-yt"
                        src="https://www.youtube.com/embed/<?php echo esc_attr($tw_yt_id); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo esc_attr($tw_yt_id); ?>&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&playsinline=1&disablekb=1&fs=0&cc_load_policy=0&color=white"
                        frameborder="0" allow="autoplay; encrypted-media" loading="lazy" title=""></iframe>
                </div>
                <?php elseif ( $tw_hero_video ) : ?>
                <video class="tw-hero-vid" autoplay muted loop playsinline preload="metadata">
                    <source src="<?php echo esc_url($tw_hero_video); ?>">
                </video>
                <?php elseif ( $tw_hero_image ) : ?>
                <div class="tw-hero-img" style="background-image:url('<?php echo esc_url($tw_hero_image); ?>')"></div>
                <?php endif; ?>

                <?php /* Mobile-only image — replaces the video on phones via CSS @media (max-width:768px) */ ?>
                <?php if ( $tw_hero_mobile_image ) : ?>
                <div class="tw-hero-mobile-img" style="background-image:url('<?php echo esc_url($tw_hero_mobile_image); ?>')"></div>
                <?php endif; ?>
            </div>

            <?php /* Floating images — admin-editable, positioned around the video frame */ ?>
            <?php if ( $tw_hero_float_images['tr'] ) : ?>
            <div class="tw-hero-float tw-hero-float--tr" aria-hidden="true">
                <img src="<?php echo esc_url( $tw_hero_float_images['tr'] ); ?>" alt="" loading="lazy">
            </div>
            <?php endif; ?>
            <?php if ( $tw_hero_float_images['ml'] ) : ?>
            <div class="tw-hero-float tw-hero-float--ml" aria-hidden="true">
                <img src="<?php echo esc_url( $tw_hero_float_images['ml'] ); ?>" alt="" loading="lazy">
            </div>
            <?php endif; ?>
            <?php if ( $tw_hero_float_images['br'] ) : ?>
            <div class="tw-hero-float tw-hero-float--br" aria-hidden="true">
                <img src="<?php echo esc_url( $tw_hero_float_images['br'] ); ?>" alt="" loading="lazy">
            </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="tw-hero-scroll" aria-hidden="true"><div class="tw-hero-scroll-line"></div></div>
</section>

<!-- ═══════════════════════ STATS ═══════════════════════ -->
<section class="tw-stats-bar">
    <div class="tw-stats-inner">
        <div class="tw-stat"><span class="tw-stat-num">4.9<i class="fa-solid fa-star tw-stat-star" aria-hidden="true"></i></span><span class="tw-stat-label">Average Rating</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">1000+</span><span class="tw-stat-label">Trips Planned</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">58+</span><span class="tw-stat-label">Destinations</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">300K+</span><span class="tw-stat-label">Instagram Community</span></div>
        <div class="tw-stat-divider" aria-hidden="true"></div>
        <div class="tw-stat"><span class="tw-stat-num">₹0</span><span class="tw-stat-label">Planning Fee</span></div>
    </div>
</section>

    <!-- ═══════════ PACKAGES ═══════════ -->
    <section id="featured-packages" class="featured-posts travel-section">
        <div class="container">
            <div class="section-heading" data-reveal="up">
                <span>Wiser Packages</span>
                <h2 class="tw-h2 tw-h2--lg">Popular Travel <span class="tw-h2-accent">Packages</span></h2>
            </div>
            <?php mytheme_travel_filter_box('travel_package', 'package_filter', home_url('/'), '#featured-packages'); ?>
            <div class="posts-grid tw-ajax-grid" id="tw-cards-packages">
                <?php
                $package_filter = mytheme_get_active_travel_filter("package_filter", "travel_package");
                $package_args = ["post_type" => "travel_package", "posts_per_page" => 3, "post_status" => "publish"];
                $package_args = array_merge($package_args, mytheme_build_travel_filter_query_args("travel_package", $package_filter));
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

    <!-- ═══════════ DESTINATIONS MARQUEE ═══════════ -->
    <?php
    $tw_marquee_destinations = array(
        'Ladakh', 'Bali', 'Rajasthan', 'Kerala', 'Vietnam', 'Bhutan',
        'Maldives', 'Iceland', 'Japan', 'Switzerland', 'Thailand', 'Dubai',
    );
    ?>
    <section class="tw-marquee-section" aria-label="Popular destinations">
        <div class="tw-marquee-track" aria-hidden="true">
            <?php for ( $tw_mq_r = 0; $tw_mq_r < 2; $tw_mq_r++ ) : ?>
                <?php foreach ( $tw_marquee_destinations as $tw_mq_dest ) : ?>
                    <span class="tw-marquee-item"><?php echo esc_html( $tw_mq_dest ); ?></span>
                    <span class="tw-marquee-dot">&bull;</span>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </section>

    <!-- ═══════════ ITINERARIES ═══════════ -->
    <section id="upcoming-trips" class="featured-posts itinerary-section">
        <div class="container">
            <div class="section-heading" data-reveal="up">
                <span>CURATED BY OUR EXPERTS</span>
                <h2 class="tw-h2 tw-h2--lg">Exquisite <span class="tw-h2-accent">Itineraries</span></h2>
            </div>
            <div class="posts-grid tw-ajax-grid" id="tw-cards-itineraries">
                <?php
                $itinerary_filter = mytheme_get_active_travel_filter("itinerary_filter", "itinerary");
                $itinerary_args = ["post_type" => "itinerary", "posts_per_page" => 3, "post_status" => "publish"];
                $itinerary_args = array_merge($itinerary_args, mytheme_build_travel_filter_query_args("itinerary", $itinerary_filter));
                $itineraries = new WP_Query($itinerary_args);
                if ($itineraries->have_posts()):
                    while ($itineraries->have_posts()): $itineraries->the_post(); tw_homepage_itinerary_card(); endwhile;
                    wp_reset_postdata();
                else: ?>
                    <?php mytheme_render_itinerary_empty_state(
                        home_url('/#upcoming-trips'),
                        __('No itineraries match this homepage filter right now. Reset the filters or ask us to create a custom route for you.', 'mytheme')
                    ); ?>
                <?php endif; ?>
            </div>
            <div class="view-all">
                <a href="<?php echo esc_url(get_post_type_archive_link("itinerary")); ?>" class="btn-secondary">View All Itineraries</a>
            </div>
        </div>
    </section>

    <!-- ═══════════ LUXE ═══════════ -->
    <?php if ( function_exists( 'tw_luxe_showcase' ) ) { tw_luxe_showcase( 3 ); } ?>

    <!-- ═══════════ WOMEN'S GROUP TRIPS ═══════════ -->
    <?php if ( function_exists( 'tw_womens_trips_showcase' ) ) { tw_womens_trips_showcase( 3 ); } ?>

    <!-- ═══════════ VISA SERVICES ═══════════ -->
    <section class="visa-services-section">
        <div class="container">
            <div class="section-heading visa-services-heading" data-reveal="up">
                <span class="section-subtitle">Travel made simple</span>
                <h2 class="tw-h2 tw-h2--lg section-title">Trip <span class="tw-h2-accent">Services</span></h2>
            </div>
            <div class="visa-services-grid">
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('visa-assistance')); ?>">
                    <span class="visa-service-icon"><i class="fi-rr-document-signed" aria-hidden="true"></i></span>
                    <h3>Visa Assistance</h3>
                    <p>Complete guidance for Schengen, UK, US, and Asia visas. We handle documentation, interviews, and follow-ups.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('passport-services')); ?>">
                    <span class="visa-service-icon"><i class="fi-rr-passport" aria-hidden="true"></i></span>
                    <h3>Passport Services</h3>
                    <p>New passport, renewal, or emergency services. Fast-track assistance for urgent travel plans.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('travel-insurance')); ?>">
                    <span class="visa-service-icon"><i class="fi-rr-globe" aria-hidden="true"></i></span>
                    <h3>Travel Insurance</h3>
                    <p>Protect your trip with medical, cancellation, baggage, and emergency coverage for domestic and international travel.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
                <a class="visa-service-card" href="<?php echo esc_url(mytheme_get_page_url_by_path('travel-agency-registration')); ?>">
                    <span class="visa-service-icon"><i class="fi-rr-briefcase" aria-hidden="true"></i></span>
                    <h3>Travel Agency Registration</h3>
                    <p>Run a travel agency? Partner with us for quality leads, a verified listing, and marketing support.</p>
                    <span class="visa-service-link">Learn More -></span>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════ BLOG ═══════════ -->
    <section id="featured-posts" class="tw-blog-section">
        <div class="container">
            <div class="tw-blog-header" data-reveal="up">
                <div class="tw-blog-header-cn">
                    <div class="tw-blog-kicker">From the Blog</div>
                    <h2 class="tw-h2 tw-h2--lg tw-blog-title">Latest Travel <span class="tw-h2-accent">Stories</span></h2>
                </div>
                <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-blog-viewall">All Stories <span aria-hidden="true">→</span></a>
            </div>
            <?php
            $tw_blog_q = new WP_Query(['posts_per_page'=>8,'post_status'=>'publish','ignore_sticky_posts'=>true]);
            if ( $tw_blog_q->have_posts() ) : $tw_blog_posts = $tw_blog_q->posts; wp_reset_postdata(); ?>
            <div class="tw-blog-carousel">
                <button type="button" class="tw-blog-carousel-btn tw-blog-carousel-btn--prev" aria-label="<?php esc_attr_e('Previous', 'mytheme'); ?>">&#8249;</button>
                <div class="tw-blog-carousel-track" id="tw-blog-carousel-track">
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
                        $tw_excerpt  = wp_trim_words(get_the_excerpt($tw_id) ?: wp_strip_all_tags($tw_p->post_content), 14, '…');
                        $tw_thumb    = get_the_post_thumbnail_url($tw_id, 'medium_large'); ?>
                    <article class="tw-blog-card">
                        <a class="tw-blog-card-img-wrap" href="<?php echo esc_url($tw_url); ?>" tabindex="-1" aria-hidden="true">
                            <?php if ($tw_thumb) : ?>
                            <img src="<?php echo esc_url($tw_thumb); ?>" alt="" loading="<?php echo $tw_idx < 2 ? 'eager' : 'lazy'; ?>" class="tw-blog-card-img">
                            <?php else : ?>
                            <div class="tw-blog-card-img tw-blog-card-img--placeholder"></div>
                            <?php endif; ?>
                            <div class="tw-blog-card-overlay"></div>
                            <span class="tw-blog-card-cat"><?php echo $tw_cat_name; ?></span>
                        </a>
                        <div class="tw-blog-card-body">
                            <h3 class="tw-blog-card-title"><a href="<?php echo esc_url($tw_url); ?>"><?php echo esc_html($tw_title); ?></a></h3>
                            <p class="tw-blog-card-excerpt"><?php echo esc_html($tw_excerpt); ?></p>
                            <div class="tw-blog-card-meta">
                                <div class="tw-blog-card-author"><div class="tw-blog-card-avatar"><?php echo $tw_avatar; ?></div><span><?php echo esc_html($tw_author); ?></span></div>
                                <div class="tw-blog-card-info"><span class="tw-blog-card-date"><?php echo esc_html($tw_date); ?></span><span class="tw-blog-card-dot" aria-hidden="true">·</span><span class="tw-blog-card-read"><?php echo esc_html($tw_read); ?></span></div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="tw-blog-carousel-btn tw-blog-carousel-btn--next" aria-label="<?php esc_attr_e('Next', 'mytheme'); ?>">&#8250;</button>
            </div>
            <script>
            (function () {
                var track = document.getElementById('tw-blog-carousel-track');
                if (!track) return;
                var prev = track.parentElement.querySelector('.tw-blog-carousel-btn--prev');
                var next = track.parentElement.querySelector('.tw-blog-carousel-btn--next');
                function step() {
                    var card = track.querySelector('.tw-blog-card');
                    return card ? card.getBoundingClientRect().width + 24 : 300;
                }
                prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
                next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
            })();
            </script>
            <?php else : ?>
            <div class="tw-blog-empty"><p>No posts yet — <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>">be the first to write one!</a></p></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════════ EVENTS & FESTIVALS SHOWCASE ═══════════ -->
    <?php if ( function_exists( 'tw_explore_events_showcase' ) ) { tw_explore_events_showcase(); } ?>

    <!-- ═══════════ COMMUNITY ═══════════ -->
    <section class="tw-community-section">
        <div class="container">
            <div class="tw-community-inner">
                <div class="tw-community-copy" data-reveal="left">
                    <span class="tw-community-kicker">Join Our Community</span>
                    <h2 class="tw-h2 tw-h2--lg tw-community-title"><span class="tw-community-title-num">1</span>TripWiser <span class="tw-h2-accent">Tribe</span></h2>
                    <p>Connect, share, and grow with 300K+ travel enthusiasts. Ask questions, share tips, and get inspired by real travelers.</p>
                    <a href="<?php echo esc_url( post_type_exists('forum_topic') ? get_post_type_archive_link('forum_topic') : mytheme_get_plan_trip_url() ); ?>" class="btn-primary">Enter the Tribe</a>
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
            <div class="instagram-feed-heading" data-reveal="up">
                <span class="tw-ig-kicker">Follow the journey</span>
                <h2 class="tw-h2 tw-h2--lg">Our <span class="tw-h2-accent">Instagram</span></h2>
                <a class="tw-ig-handle" href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener noreferrer"> @1tripwiser</a>
            </div>
            <div class="instagram-feed-wrap">
                <?php echo do_shortcode('[instagram-feed feed=1]'); ?>
            </div>
        </div>
    </section>

    <!-- ═══════════ TESTIMONIALS ═══════════ -->
    <?php if ( function_exists( 'tw_homepage_testimonials_section' ) ) { tw_homepage_testimonials_section(); } ?>

    <!-- ═══════════ GET A FREE ITINERARY ═══════════ -->
    <?php
    $tw_cta_wa_num = get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    $tw_cta_wa_url = $tw_cta_wa_num
        ? 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $tw_cta_wa_num ) . '?text=' . rawurlencode( "Hi! I'd like a free itinerary." )
        : '';
    ?>
    <section class="tw-cta-section">
        <div class="container tw-cta-inner" data-reveal="up">
            <span class="tw-cta-kicker">100% Free &middot; No Hidden Charges</span>
            <h2 class="tw-cta-title">Get a <em>free</em> itinerary &mdash; ready in <span>24 hours</span>.</h2>
            <p class="tw-cta-desc">Tell us your dream destination, dates and rough budget. A real travel planner will hand-craft your itinerary and hop on a call.</p>
            <div class="tw-cta-actions">
                <a class="free-itinerary-link" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Plan My Trip</a>
                <?php if ( $tw_cta_wa_url ) : ?>
                <a class="tw-cta-whatsapp" href="<?php echo esc_url( $tw_cta_wa_url ); ?>" target="_blank" rel="noopener noreferrer">Or chat on WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
