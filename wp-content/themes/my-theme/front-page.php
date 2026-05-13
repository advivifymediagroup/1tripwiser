<?php get_header(); ?>

<main class="main-content">
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">

            <div class="pill">
                <p>India's Most Trusted Travel Community · 300K+ on Instagram</p>
            </div>
                
                <h1>1TRIPWISER
</h1>
                <span class="hero-kicker">Wiser trips. Better memories.</span>
                <p><?php bloginfo("description"); ?></p>
                <div class="hero-actions">
                    <a href="#featured-packages" class="btn-primary">Explore Packages</a>
                    <a href="<?php echo esc_url(
                        mytheme_get_plan_trip_url()
                    ); ?>" class="btn-secondary hero-secondary">Plan a Trip</a>
                </div>
            </div>
           <div class="tag-pills">
                <span class="tag-pill">⛰️Mountains</span>
                <span class="tag-pill">🏖️Beaches</span>
                <span class="tag-pill">🌴Offbeat</span>
                <span class="tag-pill">💍Honeymoon</span>
                <span class="tag-pill">👥Group Trips</span>
                <span class="tag-pill">💰Budget</span>
                <span class="tag-pill">🌍International</span>
             
           </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stat">
            <h3>300K+</h3>
            <p>Instagram Community</p>
        </div>
        <div class="stat">
            <h3>1000+</h3>
            <p>Trips Planned</p>
        </div>
        <div class="stat">
            <h3>58+</h3>
            <p>Destinations</p>
        </div>
        <div class="stat">
            <h3>4.9⭐</h3>
            <p>Average Rating</p>
        </div>

        <div class="stat">
            <h3>₹0</h3>
            <p>Planning Fee</p>
        </div>

        


    </section>

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
                $package_args = [
                    "post_type" => "travel_package",
                    "posts_per_page" => 3,
                    "post_status" => "publish",
                ];
                $package_meta_query = mytheme_build_travel_filter_meta_query("travel_package", $package_filter);
                if (!empty($package_meta_query)) {
                    $package_args["meta_query"] = $package_meta_query;
                }
                $packages = new WP_Query($package_args);
                if ($packages->have_posts()):
                    while ($packages->have_posts()):
                        $packages->the_post();
                        mytheme_package_card();
                        ?>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                     ?>
                    <div class="no-posts">
                        <p>No travel packages found with the selected filters.</p>
                    </div>
                <?php
                endif;
                ?>
            </div>
            <div class="view-all">
                <a href="<?php echo esc_url(
                    get_post_type_archive_link("travel_package")
                ); ?>" class="btn-secondary">View All Packages</a>
            </div>
        </div>
    </section>

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
                $itinerary_args = [
                    "post_type" => "itinerary",
                    "posts_per_page" => 3,
                    "post_status" => "publish",
                ];
                $itinerary_meta_query = mytheme_build_travel_filter_meta_query("itinerary", $itinerary_filter);
                if (!empty($itinerary_meta_query)) {
                    $itinerary_args["meta_query"] = $itinerary_meta_query;
                }
                $itineraries = new WP_Query($itinerary_args);
                if ($itineraries->have_posts()):
                    while ($itineraries->have_posts()):

                        $itineraries->the_post();
                        $duration = mytheme_get_travel_field("itinerary_duration");
                        $best_time = mytheme_get_travel_field("itinerary_best_time");
                        $route_summary = mytheme_get_travel_field("itinerary_route_summary");
                        ?>
                    <article class="post-card travel-card">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail("medium"); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="travel-meta">
                                <?php if (
                                    $duration
                                ): ?><span><?php echo esc_html(
    $duration
); ?></span><?php endif; ?>
                                <?php if (
                                    $best_time
                                ): ?><span><?php echo esc_html(
    $best_time
); ?></span><?php endif; ?>
                            </div>
                          <div class="post-excerpt">
    <?php
    $summary = $route_summary
        ? wp_strip_all_tags($route_summary)
        : get_the_excerpt();

    echo wp_trim_words($summary, 28, '...');
    ?>
    
    <a href="<?php the_permalink(); ?>" class="inline-read-more">
        Read More
    </a>
</div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Open Itinerary</a>
                        </div>
                    </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                     ?>
                    <div class="no-posts">
                        <p>No itineraries found with the selected filters.</p>
                    </div>
                <?php
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         LATEST TRAVEL STORIES — Magazine Layout
    ═══════════════════════════════════════════ -->
    <section id="featured-posts" class="tw-blog-section">
        <div class="container">

            <!-- Header row -->
            <div class="tw-blog-header">
                <div>
                    <div class="tw-blog-kicker">✈ From the Blog</div>
                    <h2 class="tw-blog-title">Latest Travel Stories</h2>
                </div>
                <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="tw-blog-viewall">
                    All Stories <span aria-hidden="true">→</span>
                </a>
            </div>

            <?php
            $tw_blog_q = new WP_Query([
                'posts_per_page' => 3,
                'post_status'    => 'publish',
                'ignore_sticky_posts' => true,
            ]);

            if ( $tw_blog_q->have_posts() ) :
                $tw_blog_posts = $tw_blog_q->posts;
                wp_reset_postdata();
            ?>

            <div class="tw-blog-grid">

                <?php
                /* ── Card renderer ── */
                foreach ( $tw_blog_posts as $tw_idx => $tw_p ) :
                    $tw_id       = $tw_p->ID;
                    $tw_url      = get_permalink( $tw_id );
                    $tw_title    = get_the_title( $tw_id );
                    $tw_date     = get_the_date( 'M j, Y', $tw_id );
                    $tw_author   = get_the_author_meta( 'display_name', $tw_p->post_author );
                    $tw_avatar   = get_avatar( $tw_p->post_author, 28, '', '', ['class' => ''] );
                    $tw_cats     = get_the_category( $tw_id );
                    $tw_cat_name = $tw_cats ? esc_html( $tw_cats[0]->name ) : 'Travel';
                    $tw_words    = str_word_count( strip_tags( $tw_p->post_content ) );
                    $tw_read     = max( 1, round( $tw_words / 200 ) ) . ' min read';
                    $tw_excerpt  = wp_trim_words( get_the_excerpt( $tw_id ) ?: wp_strip_all_tags( $tw_p->post_content ), 22, '…' );
                    $tw_thumb    = get_the_post_thumbnail_url( $tw_id, $tw_idx === 0 ? 'large' : 'medium_large' );
                    $tw_is_main  = ( $tw_idx === 0 );
                ?>

                <article class="tw-blog-card <?php echo $tw_is_main ? 'tw-blog-card--main' : 'tw-blog-card--side'; ?>">
                    <a class="tw-blog-card-img-wrap" href="<?php echo esc_url( $tw_url ); ?>" tabindex="-1" aria-hidden="true">
                        <?php if ( $tw_thumb ) : ?>
                        <img src="<?php echo esc_url( $tw_thumb ); ?>" alt="" loading="<?php echo $tw_idx === 0 ? 'eager' : 'lazy'; ?>" class="tw-blog-card-img">
                        <?php else : ?>
                        <div class="tw-blog-card-img tw-blog-card-img--placeholder"></div>
                        <?php endif; ?>
                        <div class="tw-blog-card-overlay"></div>
                        <span class="tw-blog-card-cat"><?php echo $tw_cat_name; ?></span>
                    </a>

                    <div class="tw-blog-card-body">
                        <h3 class="tw-blog-card-title">
                            <a href="<?php echo esc_url( $tw_url ); ?>"><?php echo esc_html( $tw_title ); ?></a>
                        </h3>
                        <?php if ( $tw_is_main ) : ?>
                        <p class="tw-blog-card-excerpt"><?php echo esc_html( $tw_excerpt ); ?></p>
                        <?php endif; ?>
                        <div class="tw-blog-card-meta">
                            <div class="tw-blog-card-author">
                                <div class="tw-blog-card-avatar"><?php echo $tw_avatar; ?></div>
                                <span><?php echo esc_html( $tw_author ); ?></span>
                            </div>
                            <div class="tw-blog-card-info">
                                <span class="tw-blog-card-date"><?php echo esc_html( $tw_date ); ?></span>
                                <span class="tw-blog-card-dot" aria-hidden="true">·</span>
                                <span class="tw-blog-card-read"><?php echo esc_html( $tw_read ); ?></span>
                            </div>
                        </div>
                    </div>
                </article>

                <?php endforeach; ?>

            </div><!-- /.tw-blog-grid -->

            <?php else : ?>
            <div class="tw-blog-empty">
                <p>No posts yet — <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>">be the first to write one!</a></p>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <style>
    /* ============================================================
       HOMEPAGE BLOG SECTION — Magazine Layout
    ============================================================ */
    .tw-blog-section {
        padding: 80px 0 88px;
        background: linear-gradient(180deg, #0d1526 0%, #0a1e30 100%);
        position: relative;
        overflow: hidden;
    }
    .tw-blog-section::before {
        content: '';
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse at 10% 60%, rgba(6,146,175,0.12) 0%, transparent 55%),
            radial-gradient(ellipse at 90% 20%, rgba(252,180,21,0.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .tw-blog-section .container { position: relative; z-index: 1; }

    /* Header row */
    .tw-blog-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }
    .tw-blog-kicker {
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #FCB415;
        margin-bottom: 8px;
    }
    .tw-blog-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(2rem, 4vw, 3rem);
        color: #ffffff;
        letter-spacing: 0.04em;
        line-height: 1;
        margin: 0;
    }
    .tw-blog-viewall {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.88rem;
        font-weight: 800;
        color: #FCB415;
        text-decoration: none;
        border: 1.5px solid rgba(252,180,21,0.4);
        padding: 9px 20px;
        border-radius: 999px;
        transition: background 0.2s, color 0.2s, border-color 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tw-blog-viewall:hover {
        background: #FCB415;
        color: #0d1526;
        border-color: #FCB415;
    }

    /* Grid: main card left (tall), two side cards right (stacked) */
    .tw-blog-grid {
        display: grid;
        grid-template-columns: 1.45fr 1fr;
        grid-template-rows: auto auto;
        gap: 20px;
    }
    .tw-blog-card--main {
        grid-row: 1 / 3;
    }

    /* Base card */
    .tw-blog-card {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }
    .tw-blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 24px 64px rgba(0,0,0,0.45);
        border-color: rgba(252,180,21,0.35);
    }

    /* Image wrap */
    .tw-blog-card-img-wrap {
        display: block;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }
    .tw-blog-card--main .tw-blog-card-img-wrap  { height: 280px; }
    .tw-blog-card--side  .tw-blog-card-img-wrap  { height: 170px; }

    .tw-blog-card-img {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
        transition: transform 0.55s ease;
    }
    .tw-blog-card:hover .tw-blog-card-img { transform: scale(1.06); }

    .tw-blog-card-img--placeholder {
        background: linear-gradient(135deg, #0a1e30, #0d2d44);
    }

    /* Gradient overlay on image */
    .tw-blog-card-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(13,21,38,0.72) 0%, rgba(13,21,38,0.1) 55%, transparent 100%);
        transition: opacity 0.3s;
    }
    .tw-blog-card:hover .tw-blog-card-overlay { opacity: 0.85; }

    /* Category pill on image */
    .tw-blog-card-cat {
        position: absolute;
        top: 14px; left: 14px;
        background: rgba(252,180,21,0.92);
        color: #0d1526;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 4px 11px;
        border-radius: 999px;
        backdrop-filter: blur(4px);
    }

    /* Card body */
    .tw-blog-card-body {
        padding: 20px 22px 22px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .tw-blog-card-title {
        font-family: 'Nunito', sans-serif;
        font-weight: 800;
        line-height: 1.35;
        margin: 0;
    }
    .tw-blog-card--main  .tw-blog-card-title { font-size: 1.22rem; }
    .tw-blog-card--side  .tw-blog-card-title { font-size: 0.98rem; }

    .tw-blog-card-title a {
        color: #ffffff;
        text-decoration: none;
        transition: color 0.2s;
    }
    .tw-blog-card-title a:hover { color: #FCB415; }

    .tw-blog-card-excerpt {
        font-size: 0.88rem;
        color: rgba(255,255,255,0.62);
        line-height: 1.65;
        margin: 0;
    }

    /* Meta row */
    .tw-blog-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,0.07);
    }

    .tw-blog-card-author {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        color: rgba(255,255,255,0.72);
    }
    .tw-blog-card-avatar {
        width: 28px; height: 28px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid rgba(252,180,21,0.5);
        flex-shrink: 0;
        background: rgba(252,180,21,0.1);
        display: flex; align-items: center; justify-content: center;
    }
    .tw-blog-card-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .tw-blog-card-info {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        color: rgba(255,255,255,0.45);
        font-weight: 600;
        flex-shrink: 0;
    }
    .tw-blog-card-dot { opacity: 0.4; }

    /* Empty state */
    .tw-blog-empty {
        text-align: center;
        padding: 48px 24px;
        color: rgba(255,255,255,0.5);
    }
    .tw-blog-empty a { color: #FCB415; font-weight: 800; text-decoration: none; }

    /* Responsive */
    @media (max-width: 768px) {
        .tw-blog-grid {
            grid-template-columns: 1fr;
            grid-template-rows: auto;
        }
        .tw-blog-card--main { grid-row: auto; }
        .tw-blog-card--main .tw-blog-card-img-wrap { height: 220px; }
        .tw-blog-card--side  .tw-blog-card-img-wrap { height: 150px; }
        .tw-blog-section { padding: 56px 0 64px; }
    }
    </style>

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

    <section class="community-section-container">
        <div class="container">
            <div class="community-section">
                <div class="community-heading">
                    <span>Join Our Community</span>
                    <h2>1TRIPWISER <span class="highlight">
                         TRIBE
                    </span>
                    </h2>
                    
                    <p>Connect, share, and grow with 300K+ travel enthusiasts. Ask questions, share tips, and get inspired by real travelers.</p>
                </div>
                <div class="community-stats">
                    <div class="community-stat">
                        <strong>12.5K</strong>
                        <span>active members</span>
                    </div>
                    <div class="community-stat">
                        <strong>48K+</strong>
                        <span>discussions</span>
                    </div>
                    <div class="community-stat">
                        <strong>1M+</strong>
                        <span>Trip Photos</span>
                    </div>
                    <div class="community-stat">
                        <strong>500+</strong>
                        <span>Weekly Posts</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="free-itinerary-section">
        <div class="container">
            <div class="free-itinerary-content">
                <div class="free-itinerary-copy">
                    <h2>GET A <span class="highlight">
                        FREE
                    </span>
                     ITINERARY</h2>
                    <p>Tell us your dream destination — we'll craft a personalised trip plan in 24 hours. No charges, ever.</p>
                </div>
                <form class="free-itinerary-form" action="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" method="get">
                    <label class="screen-reader-text" for="free-itinerary-email">Email address</label>
                    <input id="free-itinerary-email" type="email" name="email" placeholder="your@email.com" required>
                    <button type="submit">Plan My Trip</button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
