<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <h3><?php bloginfo("name"); ?></h3>
                <div class="footer-section sub-title">
                    <p class="footer-section sub-title-text">
                        WISER TRIPS · BETTER MEMORIES
                    </p>
                </div>
                <p class="footer-section description">
                India's most trusted travel community — curating unforgettable group trips, honest guides and custom packages.
                </p>
                <div class="social-links">
                    <a href="https://www.facebook.com/1tripwiser/" title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/1tripwiser" title="Twitter" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/1tripwiser/" title="Instagram" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/1tripwiser" title="YouTube" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="https://www.pinterest.com/1tripwiser/" title="Pinterest" target="_blank"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>

             <div class="footer-section links">
                <h3>EXPLORE</h3>
                <ul>
                    
                    <li><a href="#">India Trips</a></li>
                    <li><a href="#">International</a></li>
                    <li><a href="#">Honeymoon</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link("travel_package")); ?>">Group Tours</a></li>
                </ul>
            </div>

             <div class="footer-section links">
                <h3>COMPANY</h3>
                <ul>
                    <li><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li><a href="<?php echo get_permalink(
                        get_option("page_for_posts")
                    ); ?>">Blog</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link("travel_package")); ?>">Packages</a></li>
                    <li><a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">Plan a Trip</a></li>
                </ul>
            </div>

            

           <div class="footer-section links">
                <h3>COMMUNITY</h3>
                <ul>
                    <li><a href="">1tripwiserTribe</a></li>
                    <li><a href="https://www.instagram.com/1tripwiser/" target="_blank">Instagram</a></li>
                    <li><a href="https://www.youtube.com/1tripwiser" target="_blank">YouTube</a></li>
                    <li><a href="https://www.facebook.com/1tripwiser/" target="_blank">Facebook</a></li>
                    
                </ul>
            </div>

            <!-- <div class="footer-section recent-posts">
                <h3>Recent Posts</h3>
                <ul>
                    <?php
                    $recent_posts = wp_get_recent_posts([
                        "numberposts" => 4,
                        "post_status" => "publish",
                    ]);
                    foreach ($recent_posts as $post) {
                        echo '<li><a href="' .
                            get_permalink($post["ID"]) .
                            '">' .
                            $post["post_title"] .
                            "</a></li>";
                    }
                    wp_reset_postdata();
                    ?>
                </ul>
            </div> -->
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; <?php echo date("Y"); ?> <?php bloginfo(
     "name"
 ); ?>. All rights reserved. | Designed for travelers, by travelers.</p>
            </div>
            <div class="footer-menu">
                <?php wp_nav_menu([
                    "theme_location" => "footer",
                    "container" => false,
                    "menu_class" => "footer-nav",
                    "fallback_cb" => false,
                ]); ?>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
