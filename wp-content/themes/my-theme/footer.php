<style>
/* ================================================================
   TW FOOTER STYLES
   ================================================================ */
.tw-footer {
    background: #07101c;
    color: rgba(255,255,255,0.7);
    font-family: 'Nunito', sans-serif;
    border-top: 1px solid rgba(6,146,175,0.1);
}

.tw-footer-grid {
    max-width: 1200px;
    margin: 0 auto;
    padding: 52px 32px 32px;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 40px;
}

/* Col 1 — Brand */
.tw-f-brand-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.6rem;
    color: #ffffff;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
}

.tw-f-brand-tag {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.35);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 14px;
}

.tw-f-desc {
    font-size: 0.84rem;
    line-height: 1.7;
    color: rgba(255,255,255,0.5);
    max-width: 280px;
    margin-bottom: 20px;
}

/* Social squares */
.tw-f-social {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.tw-f-social a {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: rgba(6,146,175,0.1);
    border: 1px solid rgba(6,146,175,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    text-decoration: none;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
}

.tw-f-social a:hover {
    background: rgba(6,146,175,0.25);
    border-color: rgba(6,146,175,0.5);
    color: #ffffff;
}

/* Col headings */
.tw-f-col-head {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.3);
    margin-bottom: 18px;
}

/* Footer links */
.tw-f-links {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.tw-f-links a {
    color: rgba(255,255,255,0.55);
    text-decoration: none;
    font-size: 0.88rem;
    transition: color 0.2s;
}

.tw-f-links a:hover { color: #FCB415; }

/* Affiliate "Book" column */
.tw-aff-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
}

.tw-aff-label {
    color: rgba(255,255,255,0.55);
    font-size: 0.87rem;
}

.tw-aff-btn {
    font-size: 0.72rem;
    font-weight: 700;
    color: #FCB415;
    border: 1px solid rgba(252,180,21,0.35);
    border-radius: 4px;
    padding: 3px 9px;
    text-decoration: none;
    white-space: nowrap;
    transition: background 0.2s, color 0.2s;
    flex-shrink: 0;
}

.tw-aff-btn:hover {
    background: #FCB415;
    color: #0d1526;
}

/* Bottom bar */
.tw-f-bottom {
    background: #07101c;
    border-top: 1px solid rgba(255,255,255,0.04);
    padding: 18px 32px;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.tw-f-bottom p {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.28);
    margin: 0;
}

.tw-f-bottom-links {
    display: flex;
    gap: 16px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.tw-f-bottom-links a {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.3);
    text-decoration: none;
    transition: color 0.2s;
}

.tw-f-bottom-links a:hover { color: #FCB415; }

/* Responsive */
@media (max-width: 900px) {
    .tw-footer-grid {
        grid-template-columns: 1fr 1fr;
        gap: 28px;
    }
}

@media (max-width: 560px) {
    .tw-footer-grid {
        grid-template-columns: 1fr;
        padding: 36px 20px 24px;
    }
    .tw-f-bottom {
        flex-direction: column;
        align-items: flex-start;
        padding: 16px 20px;
    }
}
</style>

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

    <div style="border-top:1px solid rgba(255,255,255,0.04)">
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
