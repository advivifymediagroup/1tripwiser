<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
    <!-- Critical overflow prevention — inline so no cache/plugin can block it (v3) -->
    <style id="tw-overflow-fix">
      /* Layer 1 — root: html hidden works on iOS Safari; body clip skips scroll-container */
      html{overflow-x:hidden!important}
      body{overflow-x:clip!important;max-width:100%!important}
      /* Layer 2 — main content area */
      .main-content{overflow-x:hidden!important}

      /* Hero: containment so nothing bleeds past its bounds */
      .tw-hero{overflow:hidden!important;contain:paint!important;transform:translateZ(0)!important;isolation:isolate!important;clip-path:inset(0)!important}
      .tw-hero-bg{overflow:hidden!important}
      .tw-hero-yt-wrap{overflow:hidden!important}
      /* Mask YouTube title card (top) and player controls (bottom) on desktop —
         showinfo=0 was deprecated by YouTube, so we cover the UI areas visually. */
      .tw-hero-yt-wrap::before,.tw-hero-yt-wrap::after{content:''!important;position:absolute!important;left:0!important;right:0!important;z-index:5!important;pointer-events:none!important}
      .tw-hero-yt-wrap::before{top:0!important;height:12%!important;background:linear-gradient(to bottom,rgba(13,21,38,0.9) 0%,transparent 100%)!important}
      .tw-hero-yt-wrap::after{bottom:0!important;height:12%!important;background:linear-gradient(to top,rgba(13,21,38,0.9) 0%,transparent 100%)!important}
      /* On mobile: hide all video sources entirely + accent blobs */
      @media(max-width:768px){
        .tw-hero-accent{display:none!important}
        .tw-hero-yt-wrap,.tw-hero-vid,.tw-hero-img{display:none!important}
        .tw-hero-mobile-img{display:block!important;position:absolute!important;inset:0!important;background-size:cover!important;background-position:center!important}
      }

      /* Travel tabs — scroll inside, never push page wider */
      .travel-tabs{overflow:hidden!important;max-width:100vw!important;width:100%!important}
      .travel-tabs-inner{overflow-x:auto!important;-webkit-overflow-scrolling:touch!important;scrollbar-width:none!important;width:100%!important;box-sizing:border-box!important}
      .travel-tabs-inner::-webkit-scrollbar{display:none!important}
      .travel-tabs a{white-space:nowrap!important;flex:0 0 auto!important}
      /* On mobile only — switch to flex-start so users can swipe to see overflowing tabs.
         Desktop keeps the original justify-content:center for the polished look. */
      @media(max-width:768px){
        .travel-tabs-inner{justify-content:flex-start!important}
      }

      /* Mobile menu & nav */
      .tw-mobile-menu{max-width:100vw!important;overflow:hidden!important;width:100%!important}
      /* Only constrain .tw-nav itself — NOT .tw-nav-inner.
         .tw-nav-inner has its own max-width:1280px that creates breathing room on wide screens. */
      .tw-nav{max-width:100%!important;box-sizing:border-box!important}

      /* Posts grid — column width can never exceed 100% */
      .posts-grid{grid-template-columns:repeat(auto-fit,minmax(min(300px,100%),1fr))!important}

      /* Instagram feed — clip SBI plugin overflow horizontally */
      .instagram-feed-section{overflow-x:hidden!important}
      .instagram-feed-wrap{overflow-x:hidden!important}

      /* Footer */
      .tw-footer,.tw-footer-grid,.tw-f-bottom{max-width:100%!important;box-sizing:border-box!important}

      /* All media elements */
      img,video,iframe,embed,object{max-width:100%!important}
    </style>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<!-- ===== SITE SEARCH OVERLAY ===== -->
<div class="tw-search-overlay" id="tw-search-overlay" aria-hidden="true">
    <button type="button" class="tw-search-close" id="tw-search-close" aria-label="Close search">✕</button>
    <div class="tw-search-box">
        <form class="tw-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" class="tw-search-input" name="s" id="tw-search-input"
                   placeholder="Search trips, itineraries, group trips, blogs, the Tribe…"
                   autocomplete="off" value="<?php echo esc_attr(get_search_query()); ?>">
            <button type="submit" class="tw-search-submit">Search</button>
        </form>
        <p class="tw-search-hint">Try <span>Bali</span>, <span>Ladakh</span>, <span>Honeymoon</span> or <span>Oktoberfest</span></p>
    </div>
</div>

<!-- ===== PAGE LOADER ===== -->
<div id="tw-page-loader" role="status" aria-label="Loading">
    <div class="tw-loader-logo"><span>1</span>TRIPWISER</div>
    <div class="tw-loader-bar"></div>
</div>

<!-- ===== SITE WRAP — overflow-x clip so nothing bleeds past viewport ===== -->
<div id="tw-site-wrap">

<!-- ===== SITE NAV ===== -->
<header class="tw-nav" id="tw-nav" role="banner">
    <div class="tw-nav-inner">

        <!-- Brand / Logo -->
        <a class="tw-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?> Home">
            <?php if ( has_custom_logo() ) :
                $tw_logo_id = get_theme_mod('custom_logo');
                echo wp_get_attachment_image( $tw_logo_id, 'full', false, array(
                    'class'   => 'tw-custom-logo',
                    'loading' => 'eager',
                    'alt'     => '',
                ) );
            else : ?>
            <div class="tw-logo-ring">
                <svg class="tw-compass" viewBox="0 0 36 36" aria-hidden="true" focusable="false">
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#306C35" stroke-width="2"/>
                    <circle cx="18" cy="10" r="7" fill="#D5374F" opacity="0.9"/>
                    <circle cx="18" cy="26" r="7" fill="#0692AF" opacity="0.9"/>
                    <polygon points="18,3 15,15 18,13 21,15" fill="#FCB415"/>
                    <polygon points="18,33 15,21 18,23 21,21" fill="#FCB415" opacity="0.7"/>
                    <line x1="2" y1="18" x2="34" y2="18" stroke="#306C35" stroke-width="1.5"/>
                </svg>
            </div>
            <?php endif; ?>
            <div class="tw-brand-text">
                <div class="tw-brand-name"><span class="tw-gold">1</span>TRIPWISER</div>
                <div class="tw-brand-tag">Wiser Trips · Better Memories</div>
            </div>
        </a>

        <!-- Desktop Nav Links -->
        <nav class="tw-links" aria-label="Primary navigation">
            <?php
            $walker_args = array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'tw-menu',
                'fallback_cb'    => 'tw_default_nav',
            );
            if (class_exists('TW_Nav_Walker')) {
                $walker_args['walker'] = new TW_Nav_Walker();
            }
            wp_nav_menu($walker_args);
            ?>
            <button type="button" class="tw-search-toggle" id="tw-search-open" aria-label="Search the site">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
            <a class="tw-cta" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">
                <i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Plan My Trip
            </a>

            <!-- User account nav -->
            <div class="tw-user-nav">
            <?php if (is_user_logged_in()) :
                $tw_user   = wp_get_current_user();
                $tw_name   = $tw_user->display_name ?: $tw_user->user_login;
                $tw_avatar = get_avatar($tw_user->ID, 28, '', '', array('class' => ''));
            ?>
                <div class="tw-user-dropdown">
                    <button class="tw-user-trigger" aria-expanded="false" aria-haspopup="true">
                        <div class="tw-user-avatar-wrap">
                            <?php echo $tw_avatar ?: '<i class="fa-regular fa-user"></i>'; ?>
                        </div>
                        <span class="tw-user-display"><?php echo esc_html($tw_name); ?></span>
                        <i class="fa-solid fa-chevron-down tw-user-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="tw-user-menu" role="menu">
                        <a href="<?php echo esc_url(home_url('/profile/')); ?>" role="menuitem"><i class="fa-regular fa-user"></i> My Profile</a>
                        <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>" role="menuitem"><i class="fa-regular fa-pen-to-square"></i> Write a Post</a>
                        <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="tw-logout" role="menuitem"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
                    </div>
                </div>
            <?php else : ?>
                <a class="tw-auth-link" href="<?php echo esc_url(home_url('/login/')); ?>">Log In</a>
                <a class="tw-auth-btn" href="<?php echo esc_url(home_url('/register/')); ?>">Sign Up</a>
            <?php endif; ?>
            </div>
            <div class="instagram-follow tw-f-social">
                <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </nav>

        <!-- Mobile Toggle -->
        <button class="tw-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="tw-mobile-menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile drawer -->
    <div class="tw-mobile-menu" id="tw-mobile-menu" aria-hidden="true">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'tw-mobile-list',
            'fallback_cb'    => 'tw_default_mobile_nav',
        ));
        ?>
        <a class="tw-cta tw-cta-mobile" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
        <button type="button" class="tw-cta tw-cta-mobile" id="tw-search-open-mobile" style="width:100%;background:rgba(255,255,255,0.08);color:#fff;border:1px solid rgba(255,255,255,0.15);margin-top:10px;"><i class="fa-solid fa-magnifying-glass"></i> Search the site</button>

        <!-- Mobile auth links -->
        <div class="tw-mobile-auth">
        <?php if (is_user_logged_in()) :
            $tw_mob_user = wp_get_current_user();
        ?>
            <a href="<?php echo esc_url(home_url('/profile/')); ?>" class="login"><i class="fa-regular fa-user"></i> <?php echo esc_html($tw_mob_user->display_name ?: $tw_mob_user->user_login); ?></a>
            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="signup">Log Out</a>
        <?php else : ?>
            <a href="<?php echo esc_url(home_url('/login/')); ?>" class="login">Log In</a>
            <a href="<?php echo esc_url(home_url('/register/')); ?>" class="signup">Sign Up Free</a>
        <?php endif; ?>
        </div>
    </div>
</header>

<?php
function tw_default_nav() {
    $dest_url  = esc_url(get_post_type_archive_link('destination'));
    $pkg_url   = esc_url(get_post_type_archive_link('travel_package'));
    $blog_url  = esc_url(home_url('/blog-affiliates/'));
    $tribe_url = post_type_exists('forum_topic') ? esc_url(get_post_type_archive_link('forum_topic')) : '';
    echo '<ul class="tw-menu">';
    echo '<li class="tw-menu-item"><a href="' . $dest_url . '" class="tw-nav-link">Destinations</a></li>';
    echo '<li class="tw-menu-item"><a href="' . $pkg_url . '" class="tw-nav-link">Packages</a></li>';
    echo '<li class="tw-menu-item"><a href="' . $blog_url . '" class="tw-nav-link">Blog</a></li>';
    if ( $tribe_url ) { echo '<li class="tw-menu-item"><a href="' . $tribe_url . '" class="tw-nav-link">Tribe</a></li>'; }
    echo '</ul>';
}

function tw_default_mobile_nav() {
    $dest_url  = esc_url(get_post_type_archive_link('destination'));
    $pkg_url   = esc_url(get_post_type_archive_link('travel_package'));
    $blog_url  = esc_url(home_url('/blog-affiliates/'));
    $tribe_url = post_type_exists('forum_topic') ? esc_url(get_post_type_archive_link('forum_topic')) : '';
    echo '<ul class="tw-mobile-list">';
    echo '<li><a href="' . $dest_url . '">Destinations</a></li>';
    echo '<li><a href="' . $pkg_url . '">Packages</a></li>';
    echo '<li><a href="' . $blog_url . '">Blog</a></li>';
    if ( $tribe_url ) { echo '<li><a href="' . $tribe_url . '">Tribe</a></li>'; }
    echo '</ul>';
}
?>

<script>
(function() {
    var toggle = document.querySelector('.tw-toggle');
    var drawer = document.getElementById('tw-mobile-menu');
    if (toggle && drawer) {
        toggle.addEventListener('click', function() {
            var open = drawer.classList.toggle('open');
            toggle.classList.toggle('open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        });
    }

    /* Explore subheader mega-menu — tap-to-open on touch/mobile.
       Desktop uses CSS :hover; here we add click toggling for touch devices
       and so the caret works without a mouse. */
    var subItems = document.querySelectorAll('.tw-sub-item.has-mega');
    if (subItems.length) {
        subItems.forEach(function (item) {
            var link = item.querySelector('.tw-sub-link');
            if (!link) return;
            link.addEventListener('click', function (e) {
                // Only intercept on small screens / touch; let desktop links navigate
                if (window.matchMedia('(max-width: 900px)').matches) {
                    var alreadyOpen = item.classList.contains('is-open');
                    subItems.forEach(function (i) { i.classList.remove('is-open'); });
                    if (!alreadyOpen) { e.preventDefault(); item.classList.add('is-open'); }
                }
            });
        });
        // Close when tapping outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.tw-sub-item')) {
                subItems.forEach(function (i) { i.classList.remove('is-open'); });
            }
        });
    }

    /* Site search overlay */
    var sOverlay = document.getElementById('tw-search-overlay');
    var sInput   = document.getElementById('tw-search-input');
    function openSearch() {
        if (!sOverlay) return;
        sOverlay.classList.add('open');
        sOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (sInput) setTimeout(function () { sInput.focus(); }, 60);
    }
    function closeSearch() {
        if (!sOverlay) return;
        sOverlay.classList.remove('open');
        sOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    ['tw-search-open', 'tw-search-open-mobile'].forEach(function (id) {
        var b = document.getElementById(id);
        if (b) b.addEventListener('click', openSearch);
    });
    var sClose = document.getElementById('tw-search-close');
    if (sClose) sClose.addEventListener('click', closeSearch);
    if (sOverlay) {
        sOverlay.addEventListener('click', function (e) { if (e.target === sOverlay) closeSearch(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sOverlay.classList.contains('open')) closeSearch();
        });
    }
})();

/* ============================================================
   Force-style SBI Instagram feed buttons via inline JS.
   The SBI plugin uses extremely high-specificity CSS that wins
   even against our !important rules. Inline style attributes
   always win the cascade, so this is the bulletproof fix. */
(function () {
    var BTN_BASE = [
        'display:inline-flex',
        'align-items:center',
        'justify-content:center',
        'gap:10px',
        'height:48px',
        'padding:0 30px',
        'border:none',
        'border-radius:999px',
        'font-family:Nunito,sans-serif',
        'font-size:0.88rem',
        'font-weight:800',
        'letter-spacing:0.06em',
        'text-transform:uppercase',
        'text-decoration:none',
        'line-height:1',
        'margin:0',
        'box-sizing:border-box',
        'cursor:pointer',
        'float:none',
        'clear:none',
        'position:static',
        'width:auto',
        'max-width:max-content',
        'min-width:0',
        'flex:0 0 auto',
        'transition:transform 0.2s ease,box-shadow 0.2s ease,filter 0.2s ease'
    ].join(';');

    var LOAD_STYLE = BTN_BASE + ';' + [
        'background:linear-gradient(135deg,#FCB415 0%,#f09a00 100%)',
        'background-color:#FCB415',
        'color:#0d1526',
        'box-shadow:0 4px 14px rgba(252,180,21,0.32)'
    ].join(';');

    var FOLLOW_STYLE = BTN_BASE + ';' + [
        'background:linear-gradient(135deg,#0692af 0%,#056d83 100%)',
        'background-color:#0692af',
        'color:#ffffff',
        'box-shadow:0 4px 14px rgba(6,146,175,0.32)'
    ].join(';');

    var WRAP_STYLE = [
        'background:transparent',
        'background-color:transparent',
        'background-image:none',
        'box-shadow:none',
        'border:none',
        'border-radius:0',
        'padding:0',
        'margin:36px 0 0',
        'display:flex',
        'justify-content:center',
        'align-items:center',
        'flex-wrap:wrap',
        'gap:14px',
        'width:100%',
        'max-width:100%',
        'box-sizing:border-box',
        'text-align:center',
        'float:none',
        'clear:both'
    ].join(';');

    function styleSBI() {
        var wrap = document.querySelector('.instagram-feed-wrap');
        if (!wrap) return;

        // Wrapper (container holding both buttons)
        var loadWrap = wrap.querySelector('#sbi_load, .sbi_load, .sb-load-wrap');
        if (loadWrap) loadWrap.setAttribute('style', WRAP_STYLE);

        // Load More button — find anchor/button with load-related class or id
        var loadBtn = wrap.querySelector(
            'a#sbi_load_btn, button#sbi_load_btn, a.sbi_load_btn, button.sbi_load_btn, ' +
            'a.sb-loadmore_btn, button.sb-loadmore_btn, ' +
            '#sbi_load > a:first-child, #sbi_load > button:first-child'
        );
        if (loadBtn && loadBtn.tagName !== 'DIV') {
            loadBtn.setAttribute('style', LOAD_STYLE);
        }

        // Follow on Instagram button — find anchor with follow-related class or id
        var followBtn = wrap.querySelector(
            'a#sbi_follow_btn, a.sbi_follow_btn, a.sb-followBtn-link, ' +
            '#sbi_follow_btn > a, #sb_instagram_follow > a, ' +
            '#sbi_load a[href*="instagram.com"]'
        );
        if (followBtn && followBtn.tagName !== 'DIV') {
            followBtn.setAttribute('style', FOLLOW_STYLE);
        }

        // If #sbi_follow_btn is a wrapper div, strip its styles
        var followWrap = wrap.querySelector('div#sbi_follow_btn, div.sb-follow-btn');
        if (followWrap) {
            followWrap.setAttribute('style',
                'background:transparent;background-color:transparent;background-image:none;' +
                'box-shadow:none;border:none;padding:0;margin:0;float:none;' +
                'display:inline-flex;width:auto;max-width:max-content;'
            );
        }
    }

    // Run at multiple points — SBI loads its feed asynchronously
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', styleSBI);
    } else {
        styleSBI();
    }
    [400, 1200, 2500, 5000].forEach(function (delay) {
        setTimeout(styleSBI, delay);
    });

    // Re-apply whenever SBI re-renders (e.g. after Load More click adds posts)
    if (window.MutationObserver) {
        var target = document.querySelector('.instagram-feed-wrap');
        if (target) {
            var obs = new MutationObserver(function () { styleSBI(); });
            obs.observe(target, { childList: true, subtree: true });
        } else {
            // wrap not yet in DOM — observe body until it appears
            var bodyObs = new MutationObserver(function () {
                var t = document.querySelector('.instagram-feed-wrap');
                if (t) {
                    bodyObs.disconnect();
                    var o = new MutationObserver(function () { styleSBI(); });
                    o.observe(t, { childList: true, subtree: true });
                    styleSBI();
                }
            });
            bodyObs.observe(document.body, { childList: true, subtree: true });
        }
    }
})();
</script>

<!-- ===== SUB-HEADER TABS ===== -->
<?php if (function_exists('mytheme_travel_tabs')) { mytheme_travel_tabs(); } ?>
