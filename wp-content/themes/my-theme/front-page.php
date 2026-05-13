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

    <section id="featured-posts" class="featured-posts">
        <div class="container">
            <div class="section-heading">
                <span>From the blog</span>
                <h2>Latest Travel Stories</h2>
            </div>
            <div class="posts-grid">
                <?php
                $featured_posts = new WP_Query([
                    "posts_per_page" => 3,
                    "post_status" => "publish",
                ]);
                if ($featured_posts->have_posts()):
                    while ($featured_posts->have_posts()):
                        $featured_posts->the_post(); ?>
                    <article class="post-card featured">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail("medium"); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="post-meta">
                                <span class="date"><?php echo get_the_date(); ?></span>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                        </div>
                    </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                     ?>
                    <div class="no-posts">
                        <p>No posts available yet. Check back soon!</p>
                    </div>
                <?php
                endif;
                ?>
            </div>
            <div class="view-all">
                <a href="<?php echo get_permalink(
                    get_option("page_for_posts")
                ); ?>" class="btn-secondary">View All Posts</a>
            </div>
        </div>
    </section>

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
