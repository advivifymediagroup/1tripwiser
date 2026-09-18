<?php
/**
 * Template Name: App Coming Soon
 * Styles are in style.css (APP — COMING SOON PAGE section)
 *
 * Rebuilt to match the ADVIVIFY Figma design exactly (fonts, colors, SVG
 * icons/illustrations, spacing) — see commit message for the reference.
 * Playfair Display + DM Sans are loaded here only, scoped to this page.
 */
get_header();
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Sans:wght@400;500;600;700&display=swap">

<section class="app2-hero">
    <svg class="app2-constellation" viewBox="0 0 1440 730" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <g stroke="rgba(255,255,255,0.11)" stroke-width="1" fill="none">
            <path d="M248 150 L540 196 L660 120 L748 296 L486 262 L540 196"></path>
            <path d="M486 262 L330 520 L560 585 L790 700 L948 690 L930 668 L992 448 L748 296"></path>
            <path d="M992 448 L1160 520 L1290 300"></path>
            <path d="M120 430 L330 520"></path>
            <path d="M120 430 L248 150"></path>
            <path d="M560 585 L748 296"></path>
            <path d="M790 700 L992 448"></path>
            <path d="M1160 520 L1290 620"></path>
        </g>
        <g stroke="rgba(45,212,191,0.22)" stroke-width="1" fill="none">
            <path d="M248 150 L540 196"></path>
            <path d="M992 448 L1160 520"></path>
        </g>
        <g stroke="rgba(236,11,122,0.22)" stroke-width="1" fill="none">
            <path d="M486 262 L330 520"></path>
            <path d="M930 668 L992 448"></path>
        </g>
        <circle cx="248" cy="150" r="3" fill="#2DD4BF"></circle>
        <circle cx="540" cy="196" r="3.2" fill="#2DD4BF"></circle>
        <circle cx="486" cy="262" r="3.2" fill="#EC0B7A"></circle>
        <circle cx="660" cy="120" r="2.4" fill="rgba(255,255,255,0.55)"></circle>
        <circle cx="748" cy="296" r="3" fill="rgba(255,255,255,0.7)"></circle>
        <circle cx="330" cy="520" r="2.6" fill="rgba(255,255,255,0.5)"></circle>
        <circle cx="560" cy="585" r="2.6" fill="rgba(255,255,255,0.5)"></circle>
        <circle cx="790" cy="700" r="3" fill="rgba(255,255,255,0.7)"></circle>
        <circle cx="992" cy="448" r="3.4" fill="#2DD4BF"></circle>
        <circle cx="930" cy="668" r="3.2" fill="#EC0B7A"></circle>
        <circle cx="948" cy="690" r="3" fill="#E9A23B"></circle>
        <circle cx="1160" cy="520" r="2.6" fill="rgba(255,255,255,0.45)"></circle>
        <circle cx="1290" cy="300" r="2.4" fill="rgba(255,255,255,0.4)"></circle>
        <circle cx="120" cy="430" r="2.4" fill="rgba(255,255,255,0.4)"></circle>
    </svg>

    <div class="app2-hero-inner">

        <div class="app2-hero-copy">
            <span class="app2-kicker"><span class="app2-dot"></span>Launching Soon</span>

            <h1 class="app2-h1">Your next trip<br>fits in your <em>pocket.</em></h1>

            <p class="app2-hero-sub">Plan with Wisey AI, drop Secret Pins, and take 300K+ travellers with you across the globe.</p>

            <div class="app2-hero-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="app2-btn-primary">
                    Explore the Website
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="12" x2="19" y2="12"></line><polyline points="13,6 19,12 13,18"></polyline></svg>
                </a>
                <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="app2-btn-secondary">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#F2EFEA" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.2" cy="6.8" r="1.1" fill="#F2EFEA" stroke="none"></circle></svg>
                    Follow for updates
                </a>
            </div>

            <div class="app2-store-row">
                <span><svg width="13" height="15" viewBox="0 0 16 19" fill="rgba(255,255,255,0.56)" aria-hidden="true"><path d="M12.9 10.1c0-2 1.6-2.9 1.7-3-0.9-1.4-2.4-1.5-2.9-1.6-1.2-0.1-2.4 0.7-3 0.7-0.6 0-1.6-0.7-2.6-0.7-1.3 0-2.6 0.8-3.3 2-1.4 2.4-0.4 6 1 8 0.7 1 1.5 2.1 2.5 2 1 0 1.4-0.6 2.6-0.6 1.2 0 1.5 0.6 2.6 0.6 1.1 0 1.8-1 2.4-2 0.8-1.1 1.1-2.2 1.1-2.3 0 0-2.1-0.8-2.1-3.1zM10.9 3.9c0.5-0.7 0.9-1.6 0.8-2.6-0.8 0-1.8 0.5-2.4 1.2-0.5 0.6-1 1.6-0.8 2.5 0.9 0.1 1.8-0.4 2.4-1.1z"></path></svg>App Store — Coming Soon</span>
                <span class="app2-store-sep"></span>
                <span><svg width="13" height="15" viewBox="0 0 18 20" fill="none" stroke="rgba(255,255,255,0.56)" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="M2 1.6 L14.6 10 L2 18.4 Z"></path></svg>Google Play — Coming Soon</span>
            </div>
        </div>

        <div class="app2-hero-visual">

            <span class="app2-wisecoins">
                <span class="app2-wisecoins-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="#5A3A02" aria-hidden="true"><polygon points="12,2.5 14.9,9 22,9.8 16.7,14.5 18.2,21.5 12,17.9 5.8,21.5 7.3,14.5 2,9.8 9.1,9"></polygon></svg></span>
                1,248 WiseCoins
            </span>

            <div class="app2-phone-col">
                <span class="app2-phone-badge" id="appPhoneBadge"><span class="app2-dot"></span>05 / 08 &middot; TripBook</span>

                <div class="app2-phone">
                    <div class="app2-phone-status">
                        <span>9:41</span>
                        <span class="app2-phone-status-icons">
                            <svg width="12" height="9" viewBox="0 0 14 10" fill="rgba(255,255,255,0.85)" aria-hidden="true"><polygon points="0,10 12,10 12,0"></polygon></svg>
                            <svg width="12" height="9" viewBox="0 0 14 10" fill="none" stroke="rgba(255,255,255,0.85)" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><path d="M1 3.6a9 9 0 0 1 12 0"></path><path d="M3.6 6.2a5.2 5.2 0 0 1 6.8 0"></path><circle cx="7" cy="8.7" r="0.8" fill="rgba(255,255,255,0.85)" stroke="none"></circle></svg>
                            <svg width="16" height="9" viewBox="0 0 18 10" fill="none" aria-hidden="true"><rect x="0.6" y="0.6" width="14" height="8.8" rx="2.4" stroke="rgba(255,255,255,0.55)" stroke-width="1"></rect><rect x="2.2" y="2.2" width="10.4" height="5.6" rx="1.3" fill="rgba(255,255,255,0.85)"></rect><path d="M16.4 3.4v3.2" stroke="rgba(255,255,255,0.55)" stroke-width="1.6" stroke-linecap="round"></path></svg>
                        </span>
                    </div>

                    <div class="app2-phone-screen" id="appPhoneScreen">

                        <!-- 1 — Welcome -->
                        <div class="app2-slide app2-slide--center" data-label="Welcome">
                            <span class="app2-welcome-mark"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m15 9-4 6-2-2 4-6z"></path></svg></span>
                            <div class="app2-welcome-brand"><span>1</span>TripWiser</div>
                            <div class="app2-welcome-tag">Wiser trips &middot; Better memories</div>
                            <div class="app2-welcome-head">Every trip deserves<br>a better story.</div>
                            <div class="app2-cta-btn">Sign In</div>
                        </div>

                        <!-- 2 — Home Feed -->
                        <div class="app2-slide" data-label="Home Feed">
                            <div class="app2-ps-head">
                                <div>
                                    <span class="app2-ps-label">GOOD MORNING</span>
                                    <div class="app2-ps-title">Explorer</div>
                                </div>
                                <span class="app2-ps-coinpill"><span>1,248 WC</span><span class="app2-ps-avatar"></span></span>
                            </div>
                            <div class="app2-stories">
                                <span></span><span></span><span></span><span></span>
                            </div>
                            <div class="app2-heroimg app2-heroimg--sm">
                                <?php get_template_part('template-parts/app-landscape-svg'); ?>
                                <div class="app2-heroimg-fade-t"></div>
                                <div class="app2-heroimg-fade-b"></div>
                                <div class="app2-heroimg-caption">
                                    <span class="app2-ps-kicker">PRIYA.WANDERS</span>
                                    <span class="app2-ps-cardtitle">Fushimi Inari, Kyoto</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3 — Wisey AI -->
                        <div class="app2-slide" data-label="Wisey AI">
                            <div class="app2-ps-head">
                                <div>
                                    <span class="app2-ps-label">WISEY AI</span>
                                    <div class="app2-ps-title">Trip Planner</div>
                                </div>
                            </div>
                            <div class="app2-chat">
                                <div class="app2-chat-bubble app2-chat-bubble--user">Plan me 5 days in Kyoto, mid-range budget</div>
                                <div class="app2-chat-bubble app2-chat-bubble--ai">Here&rsquo;s a draft: Day 1 Fushimi Inari &amp; Gion, Day 2 bamboo grove&hellip;</div>
                            </div>
                            <div class="app2-chat-input">Ask Wisey anything&hellip;</div>
                        </div>

                        <!-- 4 — Secret Pins -->
                        <div class="app2-slide" data-label="Secret Pins">
                            <div class="app2-ps-head">
                                <div>
                                    <span class="app2-ps-label">NEARBY</span>
                                    <div class="app2-ps-title">Secret Pins</div>
                                </div>
                            </div>
                            <div class="app2-map">
                                <span class="app2-map-pin" style="top:32%;left:24%"><svg width="16" height="16" viewBox="0 0 24 24" fill="#EC0B7A"><path d="M12 2.2a7.4 7.4 0 0 0-7.4 7.4c0 5.6 7.4 12.2 7.4 12.2s7.4-6.6 7.4-12.2A7.4 7.4 0 0 0 12 2.2z"></path></svg></span>
                                <span class="app2-map-pin" style="top:52%;left:62%"><svg width="16" height="16" viewBox="0 0 24 24" fill="#2DD4BF"><path d="M12 2.2a7.4 7.4 0 0 0-7.4 7.4c0 5.6 7.4 12.2 7.4 12.2s7.4-6.6 7.4-12.2A7.4 7.4 0 0 0 12 2.2z"></path></svg></span>
                                <span class="app2-map-pin" style="top:18%;left:76%"><svg width="16" height="16" viewBox="0 0 24 24" fill="#EC0B7A"><path d="M12 2.2a7.4 7.4 0 0 0-7.4 7.4c0 5.6 7.4 12.2 7.4 12.2s7.4-6.6 7.4-12.2A7.4 7.4 0 0 0 12 2.2z"></path></svg></span>
                            </div>
                            <div class="app2-pin-row">
                                <span class="app2-pin-thumb app2-pin-thumb--unlock"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4.5" y="10.5" width="15" height="10" rx="2.5"></rect><path d="M8 10.5V7a4 4 0 0 1 7.5-2"></path></svg></span>
                                <span class="app2-pin-copy">
                                    <span class="app2-pin-title">Pin unlocked!</span>
                                    <span class="app2-pin-sub">Within 40m &middot; Hidden waterfall</span>
                                </span>
                            </div>
                        </div>

                        <!-- 5 — TripBook -->
                        <div class="app2-slide app2-slide--active" data-label="TripBook">
                            <div class="app2-ps-head">
                                <div>
                                    <span class="app2-ps-label">Active Journal</span>
                                    <div class="app2-ps-title">TripBook</div>
                                </div>
                                <span class="app2-ps-coinpill"><span>1,248 WC</span><span class="app2-ps-avatar"></span></span>
                            </div>

                            <div class="app2-route-row">
                                <span class="app2-route-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="6" r="3"></circle><path d="M9 18h5a4 4 0 0 0 0-8H10a4 4 0 0 1 0-8h5"></path></svg></span>
                                <span class="app2-route-copy">
                                    <span class="app2-route-title">Zurich &rarr; Zermatt</span>
                                    <span class="app2-route-sub">Alpine Glacier Express &middot; 1,420 km</span>
                                </span>
                                <span class="app2-live-pill">Live</span>
                            </div>

                            <div class="app2-heroimg">
                                <?php get_template_part('template-parts/app-landscape-svg'); ?>
                                <div class="app2-heroimg-fade-t"></div>
                                <div class="app2-heroimg-fade-b"></div>
                                <div class="app2-heroimg-topbar">
                                    <span class="app2-heroimg-tag"><span class="app2-heroimg-tag-dot"></span>Wisey AI &middot; Day 3</span>
                                    <span class="app2-heroimg-save"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#F2EFEA" stroke-width="2" stroke-linejoin="round"><path d="M6 3h12v18l-6-4.6L6 21z"></path></svg></span>
                                </div>
                                <div class="app2-heroimg-caption">
                                    <span class="app2-ps-kicker">Alpine Panorama</span>
                                    <span class="app2-ps-cardtitle">Morning Mist over Lake Como</span>
                                    <span class="app2-heroimg-meta">
                                        <span><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-6.3 7-11.4A7 7 0 0 0 5 10.6C5 15.7 12 22 12 22z"></path><circle cx="12" cy="10.4" r="2.4"></circle></svg>Bellagio Villa Deck</span>
                                        <span class="app2-heroimg-pins">4 Secret Pins &rarr;</span>
                                    </span>
                                </div>
                            </div>

                            <div class="app2-pin-row">
                                <span class="app2-pin-thumb app2-pin-thumb--coffee"></span>
                                <span class="app2-pin-copy">
                                    <span class="app2-pin-title">Caff&egrave; Rossi Espresso Terrace</span>
                                    <span class="app2-pin-sub">Pinned by 14.2K travelers &middot; 0.2 km</span>
                                </span>
                                <span class="app2-pin-avatar">A</span>
                            </div>
                        </div>

                        <!-- 6 — Stories & Trails -->
                        <div class="app2-slide app2-slide--reel" data-label="Stories &amp; Trails">
                            <div class="app2-reel"></div>
                            <div class="app2-reel-segs"><i></i><i></i><i></i><i></i></div>
                            <div class="app2-reel-side">
                                <span><svg width="15" height="15" viewBox="0 0 24 24" fill="#fff"><path d="M12 21s-7-4.5-9.3-9C.8 8.4 2.5 4.5 6.3 3.9c2-.3 3.9.6 5 2.3a1 1 0 0 0 1.6 0c1.1-1.7 3-2.6 5-2.3 3.8.6 5.5 4.5 3.6 8.1C19 16.5 12 21 12 21z"></path></svg>9.2K</span>
                                <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M4 12a8 8 0 1 1 3.2 6.4L4 20l1.3-3.6A7.96 7.96 0 0 1 4 12Z"></path></svg>214</span>
                            </div>
                            <div class="app2-reel-bottom">
                                <span class="app2-ps-avatar"></span>
                                <span>
                                    <span class="app2-route-title">@kenji.explores</span>
                                    <span class="app2-route-sub">Sunrise trek to Kedarkantha &middot; Day 2</span>
                                </span>
                            </div>
                        </div>

                        <!-- 7 — WiseCoins -->
                        <div class="app2-slide" data-label="WiseCoins">
                            <div class="app2-ps-head">
                                <div>
                                    <span class="app2-ps-label">REWARDS</span>
                                    <div class="app2-ps-title">WiseCoins</div>
                                </div>
                            </div>
                            <div class="app2-coinhero">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#F0B429"><polygon points="12,2.5 14.9,9 22,9.8 16.7,14.5 18.2,21.5 12,17.9 5.8,21.5 7.3,14.5 2,9.8 9.1,9"></polygon></svg>
                                <div class="app2-coinhero-num">1,248</div>
                                <div class="app2-coinhero-lbl">Level 3 &middot; Explorer</div>
                            </div>
                            <div class="app2-earn-row"><span>Check-in</span><b>+10</b></div>
                            <div class="app2-earn-row"><span>Cheer a post</span><b>+5</b></div>
                        </div>

                        <!-- 8 — Post Boosting -->
                        <div class="app2-slide" data-label="Post Boosting">
                            <div class="app2-ps-head">
                                <div>
                                    <span class="app2-ps-label">GROW YOUR REACH</span>
                                    <div class="app2-ps-title">Boost Post</div>
                                </div>
                            </div>
                            <div class="app2-heroimg app2-heroimg--sm">
                                <?php get_template_part('template-parts/app-landscape-svg'); ?>
                                <div class="app2-heroimg-fade-t"></div>
                                <div class="app2-heroimg-fade-b"></div>
                                <span class="app2-boost-pill">Boost</span>
                                <div class="app2-heroimg-caption">
                                    <span class="app2-ps-kicker">YOUR POST</span>
                                    <span class="app2-ps-cardtitle">Bali Bliss &mdash; Island Escape</span>
                                </div>
                            </div>
                            <div class="app2-earn-row"><span>Reach</span><b>2.1K &rarr; 9.4K</b></div>
                        </div>

                    </div>

                    <nav class="app2-tabbar">
                        <a href="#" class="app2-tab app2-tab--active"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11.2 12 4.4l8 6.8V20a1 1 0 0 1-1 1h-4v-6h-6v6H5a1 1 0 0 1-1-1z"></path></svg>Home</a>
                        <a href="#" class="app2-tab"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 21V9.5a2.5 2.5 0 0 1 5 0V15"></path><path d="M17 3v11.5a2.5 2.5 0 0 1-5 0V9"></path></svg>Trails</a>
                        <span class="app2-tab-spacer"></span>
                        <a href="#" class="app2-tab"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><line x1="16.2" y1="16.2" x2="21" y2="21"></line></svg>Discover</a>
                        <a href="#" class="app2-tab"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"></circle><path d="M5 20c0-3.6 3.1-5.6 7-5.6s7 2 7 5.6"></path></svg>Profile</a>
                        <a href="#" class="app2-fab" aria-label="Drop a Secret Pin"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-6.3 7-11.4A7 7 0 0 0 5 10.6C5 15.7 12 22 12 22z"></path><circle cx="12" cy="10.4" r="2.5"></circle></svg></a>
                    </nav>

                    <div class="app2-phone-home-indicator"></div>
                </div>
            </div>

        </div>

    </div>

    <div class="app2-rail">
        <a href="https://wa.me/" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" class="app2-rail-btn">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.75)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.6a8.4 8.4 0 0 1-12.4 7.4L3.5 20.5l1.6-5A8.4 8.4 0 1 1 21 11.6z"></path><path d="M8.9 9c0.4 2.4 3 5 5.4 5.4l1.2-1.3 1.9 1-0.6 1.6c-2.9 0.6-7.7-3.8-8.5-7l1.7-0.6 0.9 2z" fill="rgba(255,255,255,0.75)" stroke="none"></path></svg>
        </a>
        <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" aria-label="Follow on Instagram" class="app2-rail-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.75)" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.2" cy="6.8" r="1.1" fill="rgba(255,255,255,0.75)" stroke="none"></circle></svg>
        </a>
        <span class="app2-rail-label">Live Beta</span>
    </div>
</section>

<section class="app2-features">
    <span class="app2-features-kicker">&bull; What&rsquo;s inside &bull;</span>
    <h2 class="app2-h2">Everything your trip needs.</h2>
    <p class="app2-features-sub">One app. Every part of the journey.</p>

    <div class="app2-feature-wrap">
        <div class="app2-feature-line"></div>
        <div class="app2-feature-grid">
            <div class="app2-feature">
                <span class="app2-feature-num">01</span>
                <span class="app2-feature-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M11 3.2 12.7 8l4.8 1.7-4.8 1.7L11 16.2 9.3 11.4 4.5 9.7l4.8-1.7z"></path><path d="M18 14.2l0.8 2.2 2.2 0.8-2.2 0.8-0.8 2.2-0.8-2.2-2.2-0.8 2.2-0.8z"></path></svg></span>
                <span class="app2-feature-title">Wisey &mdash; AI TripPlanner</span>
                <p>Build day-by-day itineraries with your AI travel companion.</p>
            </div>
            <div class="app2-feature">
                <span class="app2-feature-num">02</span>
                <span class="app2-feature-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.8C10.4 5.3 8.2 4.6 5.2 4.6H3v13.2h2.2c3 0 5.2 0.7 6.8 2.2"></path><path d="M12 6.8c1.6-1.5 3.8-2.2 6.8-2.2H21v13.2h-2.2c-3 0-5.2 0.7-6.8 2.2"></path><path d="M12 6.8V20"></path></svg></span>
                <span class="app2-feature-title">TripBook</span>
                <p>Automatically turn your journey into a visual travelogue.</p>
            </div>
            <div class="app2-feature">
                <span class="app2-feature-num">03</span>
                <span class="app2-feature-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="#EC0B7A"><path d="M12 2.2a7.4 7.4 0 0 0-7.4 7.4c0 5.6 7.4 12.2 7.4 12.2s7.4-6.6 7.4-12.2A7.4 7.4 0 0 0 12 2.2zm0 10.1a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4z"></path></svg></span>
                <span class="app2-feature-title">Secret Pins</span>
                <p>Leave geo-locked memories for travellers to discover.</p>
            </div>
            <div class="app2-feature">
                <span class="app2-feature-num">04</span>
                <span class="app2-feature-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="#EC0B7A"><path d="M12 2.4A9.6 9.6 0 1 0 21.6 12 9.6 9.6 0 0 0 12 2.4zm-1.6 13.8V7.8l6 4.2z"></path></svg></span>
                <span class="app2-feature-title">Stories &amp; Trails</span>
                <p>Share your journey through stories and short travel clips.</p>
            </div>
            <div class="app2-feature">
                <span class="app2-feature-num">05</span>
                <span class="app2-feature-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="#EC0B7A"><ellipse cx="12" cy="5.6" rx="8.2" ry="3.2"></ellipse><path d="M3.8 9.4v2.2c0 1.8 3.7 3.2 8.2 3.2s8.2-1.4 8.2-3.2V9.4c0 1.8-3.7 3.2-8.2 3.2S3.8 11.2 3.8 9.4z"></path><path d="M3.8 15v2.2c0 1.8 3.7 3.2 8.2 3.2s8.2-1.4 8.2-3.2V15c0 1.8-3.7 3.2-8.2 3.2S3.8 16.8 3.8 15z"></path></svg></span>
                <span class="app2-feature-title">WiseCoins</span>
                <p>Earn coins through check-ins, cheers and posts.</p>
            </div>
            <div class="app2-feature">
                <span class="app2-feature-num">06</span>
                <span class="app2-feature-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#EC0B7A" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><polyline points="3.5,17.5 9.5,10.5 13.5,14 20.5,6"></polyline><polyline points="15.2,6 20.5,6 20.5,11.3"></polyline></svg></span>
                <span class="app2-feature-title">Post Boosting</span>
                <p>Give your best travel content more reach.</p>
            </div>
        </div>
    </div>
</section>

<section class="app2-cta">
    <span class="app2-cta-kicker">The journey starts here</span>
    <h2 class="app2-cta-h2">Be there on day one.</h2>
    <p class="app2-cta-sub">Follow along on Instagram &mdash; we&rsquo;ll announce the launch there first.</p>

    <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="app2-cta-btn">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.9"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.2" cy="6.8" r="1.2" fill="#ffffff" stroke="none"></circle></svg>
        Follow @1tripwiser
    </a>

    <div class="app2-cta-badges">
        <span><svg width="13" height="15" viewBox="0 0 16 19" fill="rgba(255,255,255,0.78)"><path d="M12.9 10.1c0-2 1.6-2.9 1.7-3-0.9-1.4-2.4-1.5-2.9-1.6-1.2-0.1-2.4 0.7-3 0.7-0.6 0-1.6-0.7-2.6-0.7-1.3 0-2.6 0.8-3.3 2-1.4 2.4-0.4 6 1 8 0.7 1 1.5 2.1 2.5 2 1 0 1.4-0.6 2.6-0.6 1.2 0 1.5 0.6 2.6 0.6 1.1 0 1.8-1 2.4-2 0.8-1.1 1.1-2.2 1.1-2.3 0 0-2.1-0.8-2.1-3.1zM10.9 3.9c0.5-0.7 0.9-1.6 0.8-2.6-0.8 0-1.8 0.5-2.4 1.2-0.5 0.6-1 1.6-0.8 2.5 0.9 0.1 1.8-0.4 2.4-1.1z"></path></svg>App Store — Coming Soon</span>
        <span><svg width="13" height="15" viewBox="0 0 18 20" fill="none" stroke="rgba(255,255,255,0.78)" stroke-width="1.7" stroke-linejoin="round"><path d="M2 1.6 L14.6 10 L2 18.4 Z"></path></svg>Google Play — Coming Soon</span>
    </div>

    <p class="app2-cta-note">Join 300K+ travellers already travelling with TripWiser.</p>
</section>

<script>
(function () {
    var screen = document.getElementById('appPhoneScreen');
    var badge  = document.getElementById('appPhoneBadge');
    if (!screen || !badge) { return; }

    var slides = Array.prototype.slice.call(screen.querySelectorAll('.app2-slide'));
    if (slides.length < 2) { return; }

    var current = slides.findIndex(function (s) { return s.classList.contains('app2-slide--active'); });
    if (current === -1) { current = 0; slides[0].classList.add('app2-slide--active'); }

    function paint() {
        var n = String(current + 1).padStart(2, '0');
        badge.lastChild.textContent = n + ' / ' + String(slides.length).padStart(2, '0') + ' · ' + slides[current].dataset.label;
    }
    paint();

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

    setInterval(function () {
        slides[current].classList.remove('app2-slide--active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('app2-slide--active');
        paint();
    }, 3600);
})();
</script>

<?php get_footer(); ?>
