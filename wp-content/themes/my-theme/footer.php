<?php /* Footer styles are in style.css */ ?>

<footer class="tw-footer" role="contentinfo">
    <div class="tw-footer-grid">

        <!-- Col 1: Brand + Social -->
        <div>
            <div class="tw-f-brand-name"><span class="tw-gold">1</span>TRIPWISER</div>
            <div class="tw-f-brand-tag">Wiser Trips · Better Memories</div>
            <p class="tw-f-desc">India's most trusted travel community — curating unforgettable group trips, honest guides and custom packages.</p>
            <div class="tw-f-social">
                <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://www.youtube.com/1tripwiser"   target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                <a href="https://www.facebook.com/1tripwiser/" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/1tripwiser"       target="_blank" rel="noopener" aria-label="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                <a href="https://www.pinterest.com/1tripwiser/" target="_blank" rel="noopener" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
            </div>
        </div>

        <!-- Col 2: Explore -->
        <div>
            <div class="tw-f-col-head">Explore</div>
            <ul class="tw-f-links">
                <li><a href="#">India Trips</a></li>
                <li><a href="#">International</a></li>
                <li><a href="#">Honeymoon</a></li>
                <li><a href="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>">Group Tours</a></li>
                <li><a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>">Blog &amp; Guides</a></li>
            </ul>
        </div>

        <!-- Col 3: Company -->
        <div>
            <div class="tw-f-col-head">Company</div>
            <ul class="tw-f-links">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li><a href="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>">Packages</a></li>
                <li><a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>">Blog</a></li>
                <li><a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">Plan a Trip</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>

        <!-- Col 4: Book (Affiliates) -->
        <div>
            <div class="tw-f-col-head">Book</div>
            <?php
            $aff_links = array(
                'Booking.com'  => get_option('tw_aff_booking_url',    'https://www.booking.com'),
                'Skyscanner'   => get_option('tw_aff_skyscanner_url', 'https://www.skyscanner.com'),
                'Viator'       => get_option('tw_aff_viator_url',     'https://www.viator.com'),
                'SafetyWing'   => get_option('tw_aff_safetywing_url', 'https://www.safetywing.com'),
            );
            $aff_desc = array(
                'Booking.com' => 'Hotels',
                'Skyscanner'  => 'Flights',
                'Viator'      => 'Experiences',
                'SafetyWing'  => 'Insurance',
            );
            foreach ($aff_links as $name => $url) :
                if ($url) :
            ?>
            <div class="tw-aff-item">
                <span class="tw-aff-label"><?php echo esc_html($aff_desc[$name]); ?> — <?php echo esc_html($name); ?></span>
                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener sponsored" class="tw-aff-btn">Book</a>
            </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>

    </div><!-- /.tw-footer-grid -->

    <div class="tw-f-bottom-wrap">
        <div class="tw-f-bottom">
            <p>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?>. All rights reserved. Designed for travellers, by travellers.</p>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'tw-f-bottom-links',
                'fallback_cb'    => false,
                'depth'          => 1,
            ));
            ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
