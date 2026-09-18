<?php
/**
 * Template Name: App Coming Soon
 * Styles are in style.css (APP — COMING SOON PAGE section)
 */
get_header();
?>

<section class="app-hero">
    <div class="app-hero-inner">

        <div class="app-hero-copy">
            <span class="app-kicker"><span class="dot" aria-hidden="true"></span> Launching Soon</span>
            <h1 class="app-hero-title">Your next trip<br>fits in your <span class="accent">pocket.</span></h1>
            <p class="app-hero-sub">
                Plan with Wisey AI, drop Secret Pins, and take 300K+ travellers with you across the globe.
            </p>

            <div class="app-hero-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary">Explore the Website <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="btn-secondary">
                    <i class="fa-brands fa-instagram" aria-hidden="true"></i> Follow for updates
                </a>
            </div>

            <div class="app-store-badges">
                <span class="app-store-badge"><i class="fa-brands fa-apple" aria-hidden="true"></i> App Store — Coming Soon</span>
                <span class="app-store-sep" aria-hidden="true">&middot;</span>
                <span class="app-store-badge"><i class="fa-brands fa-google-play" aria-hidden="true"></i> Google Play — Coming Soon</span>
            </div>
        </div>

        <div class="app-hero-visual">
            <span class="app-wisecoins-badge"><i class="fa-solid fa-star" aria-hidden="true"></i> 1,248 WiseCoins</span>

            <div class="app-phone-frame">
                <span class="app-phone-badge" id="appPhoneBadge">05 / 08 &middot; TRIPBOOK</span>

                <div class="app-phone">
                    <div class="app-phone-notch"></div>
                    <div class="app-phone-status">
                        <span>9:41</span>
                        <span class="app-phone-status-icons"><i class="fa-solid fa-signal" aria-hidden="true"></i><i class="fa-solid fa-wifi" aria-hidden="true"></i><i class="fa-solid fa-battery-full" aria-hidden="true"></i></span>
                    </div>

                    <div class="app-phone-screen" id="appPhoneScreen">

                        <!-- 1 — Welcome -->
                        <div class="app-phone-slide" data-label="Welcome">
                            <div class="app-ps-welcome">
                                <span class="app-ps-welcome-mark"><i class="fa-solid fa-compass" aria-hidden="true"></i></span>
                                <div class="app-ps-welcome-brand"><span>1</span>TRIPWISER</div>
                                <div class="app-ps-welcome-tag">Wiser trips &middot; Better memories</div>
                                <div class="app-ps-welcome-head">Every trip deserves<br>a better story.</div>
                            </div>
                            <div class="app-ps-cta-btn">Sign In <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></div>
                            <div class="app-ps-welcome-alt">or continue with Google</div>
                        </div>

                        <!-- 2 — Home Feed -->
                        <div class="app-phone-slide" data-label="Home Feed">
                            <div class="app-ps-head">
                                <div>
                                    <span class="app-ps-label">GOOD MORNING</span>
                                    <div class="app-ps-title">Explorer</div>
                                </div>
                                <div class="app-ps-head-right">
                                    <span class="app-ps-coin">1,248 WC</span>
                                    <span class="app-ps-avatar" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="app-ps-stories">
                                <span class="app-ps-story"></span><span class="app-ps-story"></span>
                                <span class="app-ps-story"></span><span class="app-ps-story"></span>
                            </div>
                            <div class="app-ps-photo app-ps-photo--feed">
                                <span class="app-ps-photo-bookmark"><i class="fa-regular fa-heart" aria-hidden="true"></i></span>
                                <div class="app-ps-photo-caption">
                                    <span class="app-ps-photo-kicker">PRIYA.WANDERS</span>
                                    <div class="app-ps-photo-title">Fushimi Inari, Kyoto</div>
                                </div>
                            </div>
                            <div class="app-ps-photo-meta">
                                <span><i class="fa-solid fa-heart" aria-hidden="true"></i> 482 likes</span>
                                <span class="app-ps-photo-pins">36 comments</span>
                            </div>
                        </div>

                        <!-- 3 — Wisey AI TripPlanner -->
                        <div class="app-phone-slide" data-label="Wisey AI">
                            <div class="app-ps-head">
                                <div>
                                    <span class="app-ps-label">WISEY AI</span>
                                    <div class="app-ps-title">Trip Planner</div>
                                </div>
                                <div class="app-ps-head-right">
                                    <span class="app-ps-coin"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
                                </div>
                            </div>
                            <div class="app-chat">
                                <div class="app-chat-bubble app-chat-bubble--user">Plan me 5 days in Kyoto, mid-range budget</div>
                                <div class="app-chat-bubble app-chat-bubble--ai">Here&rsquo;s a draft: Day 1 Fushimi Inari &amp; Gion, Day 2 Arashiyama bamboo grove&hellip; <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></div>
                            </div>
                            <div class="app-chat-input"><span>Ask Wisey anything&hellip;</span><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></div>
                        </div>

                        <!-- 4 — Secret Pins -->
                        <div class="app-phone-slide" data-label="Secret Pins">
                            <div class="app-ps-head">
                                <div>
                                    <span class="app-ps-label">NEARBY</span>
                                    <div class="app-ps-title">Secret Pins</div>
                                </div>
                            </div>
                            <div class="app-ps-map">
                                <span class="app-ps-map-pin app-ps-map-pin--a"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                                <span class="app-ps-map-pin app-ps-map-pin--b"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                                <span class="app-ps-map-pin app-ps-map-pin--c"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                            </div>
                            <div class="app-ps-pin-card">
                                <span class="app-ps-pin-thumb"><i class="fa-solid fa-unlock" aria-hidden="true"></i></span>
                                <div class="app-ps-pin-copy">
                                    <div class="app-ps-pin-title">Pin unlocked!</div>
                                    <div class="app-ps-pin-sub">You&rsquo;re within 40m &middot; Hidden waterfall</div>
                                </div>
                                <span class="app-ps-pin-badge"><i class="fa-solid fa-lock-open" aria-hidden="true"></i></span>
                            </div>
                        </div>

                        <!-- 5 — TripBook -->
                        <div class="app-phone-slide active" data-label="TripBook">
                            <div class="app-ps-head">
                                <div>
                                    <span class="app-ps-label">ACTIVE JOURNAL</span>
                                    <div class="app-ps-title">TripBook</div>
                                </div>
                                <div class="app-ps-head-right">
                                    <span class="app-ps-coin">1,248 WC</span>
                                    <span class="app-ps-avatar" aria-hidden="true"></span>
                                </div>
                            </div>

                            <div class="app-ps-route">
                                <span class="app-ps-route-icon"><i class="fa-solid fa-route" aria-hidden="true"></i></span>
                                <div class="app-ps-route-copy">
                                    <div class="app-ps-route-title">Zurich &rarr; Zermatt</div>
                                    <div class="app-ps-route-sub">Alpine Glacier Express &middot; 1,420 km</div>
                                </div>
                                <span class="app-ps-live">Live</span>
                            </div>

                            <div class="app-ps-photo">
                                <span class="app-ps-photo-tag">Wisey AI &middot; Day 3</span>
                                <span class="app-ps-photo-bookmark"><i class="fa-regular fa-bookmark" aria-hidden="true"></i></span>
                                <div class="app-ps-photo-caption">
                                    <span class="app-ps-photo-kicker">ALPINE PANORAMA</span>
                                    <div class="app-ps-photo-title">Morning Mist over Lake Como</div>
                                </div>
                            </div>
                            <div class="app-ps-photo-meta">
                                <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Bellagio Villa Deck</span>
                                <span class="app-ps-photo-pins">4 Secret Pins <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                            </div>

                            <div class="app-ps-pin-card">
                                <span class="app-ps-pin-thumb"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></span>
                                <div class="app-ps-pin-copy">
                                    <div class="app-ps-pin-title">Caff&egrave; Rossi Espresso Terrace</div>
                                    <div class="app-ps-pin-sub">Pinned by 14.2K travellers &middot; 0.2 km</div>
                                </div>
                                <span class="app-ps-pin-badge"><i class="fa-solid fa-bell" aria-hidden="true"></i></span>
                            </div>
                        </div>

                        <!-- 6 — Stories & Trails -->
                        <div class="app-phone-slide app-phone-slide--reel" data-label="Stories &amp; Trails">
                            <div class="app-reel"></div>
                            <div class="app-reel-segs"><i></i><i></i><i></i><i></i></div>
                            <div class="app-reel-side">
                                <span><i class="fa-solid fa-heart" aria-hidden="true"></i>9.2K</span>
                                <span><i class="fa-solid fa-comment" aria-hidden="true"></i>214</span>
                                <span><i class="fa-solid fa-share" aria-hidden="true"></i></span>
                            </div>
                            <div class="app-reel-bottom">
                                <span class="app-ps-avatar" aria-hidden="true"></span>
                                <div>
                                    <div class="app-ps-route-title">@kenji.explores</div>
                                    <div class="app-ps-route-sub">Sunrise trek to Kedarkantha &middot; Day 2</div>
                                </div>
                            </div>
                        </div>

                        <!-- 7 — WiseCoins -->
                        <div class="app-phone-slide" data-label="WiseCoins">
                            <div class="app-ps-head">
                                <div>
                                    <span class="app-ps-label">REWARDS</span>
                                    <div class="app-ps-title">WiseCoins</div>
                                </div>
                            </div>
                            <div class="app-coin-hero">
                                <i class="fa-solid fa-star" aria-hidden="true"></i>
                                <div class="app-coin-hero-num">1,248</div>
                                <div class="app-coin-hero-lbl">Level 3 &middot; Explorer</div>
                            </div>
                            <div class="app-earn-row"><span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Check-in</span><b>+10</b></div>
                            <div class="app-earn-row"><span><i class="fa-solid fa-comment" aria-hidden="true"></i> Cheer a post</span><b>+5</b></div>
                        </div>

                        <!-- 8 — Post Boosting -->
                        <div class="app-phone-slide" data-label="Post Boosting">
                            <div class="app-ps-head">
                                <div>
                                    <span class="app-ps-label">GROW YOUR REACH</span>
                                    <div class="app-ps-title">Boost Post</div>
                                </div>
                            </div>
                            <div class="app-ps-photo app-ps-photo--feed">
                                <span class="app-boost-pill"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Boost</span>
                                <div class="app-ps-photo-caption">
                                    <span class="app-ps-photo-kicker">YOUR POST</span>
                                    <div class="app-ps-photo-title">Bali Bliss &mdash; Island Escape</div>
                                </div>
                            </div>
                            <div class="app-earn-row"><span><i class="fa-solid fa-eye" aria-hidden="true"></i> Reach</span><b>2.1K &rarr; 9.4K</b></div>
                        </div>

                    </div>

                    <div class="app-phone-tabbar">
                        <span class="app-tab active"><i class="fa-solid fa-house" aria-hidden="true"></i>Home</span>
                        <span class="app-tab"><i class="fa-solid fa-shoe-prints" aria-hidden="true"></i>Trails</span>
                        <span class="app-tab-fab"><i class="fa-solid fa-plus" aria-hidden="true"></i></span>
                        <span class="app-tab"><i class="fa-solid fa-compass" aria-hidden="true"></i>Discover</span>
                        <span class="app-tab"><i class="fa-solid fa-user" aria-hidden="true"></i>Profile</span>
                    </div>
                    <span class="app-phone-home-indicator" aria-hidden="true"></span>
                </div>

                <span class="app-live-beta">LIVE BETA</span>
            </div>
        </div>

    </div>
</section>

<section class="app-features">
    <div class="app-features-inner">
        <span class="app-kicker app-kicker--light"><span class="dot" aria-hidden="true"></span> What's Inside <span class="dot" aria-hidden="true"></span></span>
        <h2 class="tw-h2 tw-h2--lg app-section-title">Everything your trip needs.</h2>
        <p class="app-section-sub">One app. Every part of the journey.</p>

        <div class="app-feature-grid">
            <div class="app-feature-card">
                <span class="app-feature-num">01</span>
                <div class="app-feature-icon"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></div>
                <h3>Wisey &mdash;AI TripPlanner</h3>
                <p>Build day-by-day itineraries with your AI travel companion.</p>
            </div>
            <div class="app-feature-card">
                <span class="app-feature-num">02</span>
                <div class="app-feature-icon"><i class="fa-solid fa-book-open" aria-hidden="true"></i></div>
                <h3>TripBook</h3>
                <p>Automatically turn your journey into a visual travelogue.</p>
            </div>
            <div class="app-feature-card">
                <span class="app-feature-num">03</span>
                <div class="app-feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></div>
                <h3>Secret Pins</h3>
                <p>Leave geo-locked memories for travellers to discover.</p>
            </div>
            <div class="app-feature-card">
                <span class="app-feature-num">04</span>
                <div class="app-feature-icon"><i class="fa-solid fa-play" aria-hidden="true"></i></div>
                <h3>Stories &amp; Trails</h3>
                <p>Share your journey through stories and short travel clips.</p>
            </div>
            <div class="app-feature-card">
                <span class="app-feature-num">05</span>
                <div class="app-feature-icon"><i class="fa-solid fa-coins" aria-hidden="true"></i></div>
                <h3>WiseCoins</h3>
                <p>Earn coins through check-ins, cheers and posts.</p>
            </div>
            <div class="app-feature-card">
                <span class="app-feature-num">06</span>
                <div class="app-feature-icon"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i></div>
                <h3>Post Boosting</h3>
                <p>Give your best travel content more reach.</p>
            </div>
        </div>
    </div>
</section>

<section class="app-final-cta">
    <span class="app-kicker app-kicker--light">The Journey Starts Here</span>
    <h2>Be there on day one.</h2>
    <p>Follow along on Instagram &mdash; we'll announce the launch there first.</p>
    <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="app-final-cta-btn">
        <i class="fa-brands fa-instagram" aria-hidden="true"></i> Follow @1tripwiser
    </a>
    <div class="app-store-badges app-store-badges--outline">
        <span class="app-store-badge"><i class="fa-brands fa-apple" aria-hidden="true"></i> App Store — Coming Soon</span>
        <span class="app-store-badge"><i class="fa-brands fa-google-play" aria-hidden="true"></i> Google Play — Coming Soon</span>
    </div>
    <p class="app-final-cta-note">Join 300K+ travellers already travelling with TripWiser.</p>
</section>

<script>
(function () {
    var screen = document.getElementById('appPhoneScreen');
    var badge  = document.getElementById('appPhoneBadge');
    if (!screen || !badge) { return; }

    var slides = Array.prototype.slice.call(screen.querySelectorAll('.app-phone-slide'));
    if (slides.length < 2) { return; }

    var current = slides.findIndex(function (s) { return s.classList.contains('active'); });
    if (current === -1) { current = 0; slides[0].classList.add('active'); }

    function paint() {
        var n = String(current + 1).padStart(2, '0');
        badge.textContent = n + ' / ' + String(slides.length).padStart(2, '0') + ' · ' + slides[current].dataset.label.toUpperCase();
    }
    paint();

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

    setInterval(function () {
        slides[current].classList.remove('active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('active');
        paint();
    }, 3600);
})();
</script>

<?php get_footer(); ?>
