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
            <div class="posts-grid">
                <?php
                $packages = new WP_Query([
                    "post_type" => "travel_package",
                    "posts_per_page" => 3,
                    "post_status" => "publish",
                ]);
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
                        <p>Add Travel Packages in WordPress admin to show them here.</p>
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

    <section class="featured-posts itinerary-section">
        <div class="container">
            <div class="section-heading">
                <span>CURATED BY OUR EXPERTS</span>
                <h2>UPCOMING TRIPS</h2>
            </div>
            <div class="posts-grid">
                <?php
                $itineraries = new WP_Query([
                    "post_type" => "itinerary",
                    "posts_per_page" => 3,
                    "post_status" => "publish",
                ]);
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
                                <?php echo $route_summary ? wp_kses_post(wpautop($route_summary)) : get_the_excerpt(); ?>
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
                        <p>Add Itineraries in WordPress admin to show them here.</p>
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
