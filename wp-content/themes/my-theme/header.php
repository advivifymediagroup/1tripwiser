<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<!-- ===== PAGE LOADER ===== -->
<div id="tw-page-loader" role="status" aria-label="Loading">
    <div class="tw-loader-logo"><span>1</span>TRIPWISER</div>
    <div class="tw-loader-bar"></div>
</div>

<!-- ===== SITE NAV ===== -->
<header class="tw-nav" id="tw-nav" role="banner">
    <div class="tw-nav-inner">

        <!-- Brand / Logo -->
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
            <a class="tw-cta" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">
                <span aria-hidden="true">✈</span> Plan My Trip
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
                            <?php echo $tw_avatar ?: '<span>👤</span>'; ?>
                        </div>
                        <span class="tw-user-display"><?php echo esc_html($tw_name); ?></span>
                        <span class="tw-user-chevron" aria-hidden="true">▾</span>
                    </button>
                    <div class="tw-user-menu" role="menu">
                        <a href="<?php echo esc_url(home_url('/profile/')); ?>" role="menuitem">👤 My Profile</a>
                        <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>" role="menuitem">✍️ Write a Post</a>
                        <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="tw-logout" role="menuitem">🚪 Log Out</a>
                    </div>
                </div>
            <?php else : ?>
                <a class="tw-auth-link" href="<?php echo esc_url(home_url('/login/')); ?>">Log In</a>
                <a class="tw-auth-btn" href="<?php echo esc_url(home_url('/register/')); ?>">Sign Up</a>
            <?php endif; ?>
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
        <a class="tw-cta tw-cta-mobile" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">✈ Plan My Trip</a>

        <!-- Mobile auth links -->
        <div class="tw-mobile-auth">
        <?php if (is_user_logged_in()) :
            $tw_mob_user = wp_get_current_user();
        ?>
            <a href="<?php echo esc_url(home_url('/profile/')); ?>" class="login">👤 <?php echo esc_html($tw_mob_user->display_name ?: $tw_mob_user->user_login); ?></a>
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
    $plan_url = mytheme_get_plan_trip_url();
    $pkg_url  = esc_url(get_post_type_archive_link('travel_package'));
    $blog_url = esc_url(home_url('/blog-affiliates/'));
    echo '<ul class="tw-menu">';
    echo '<li class="tw-menu-item"><a href="' . esc_url(home_url('/')) . '" class="tw-nav-link">Home</a></li>';
    echo '<li class="tw-menu-item"><a href="' . $pkg_url . '" class="tw-nav-link">Packages</a></li>';
    echo '<li class="tw-menu-item"><a href="' . $blog_url . '" class="tw-nav-link">Blog</a></li>';
    echo '<li class="tw-menu-item"><a href="' . esc_url($plan_url) . '" class="tw-nav-link">Plan a Trip</a></li>';
    echo '</ul>';
}

function tw_default_mobile_nav() {
    $plan_url = mytheme_get_plan_trip_url();
    $pkg_url  = esc_url(get_post_type_archive_link('travel_package'));
    $blog_url = esc_url(home_url('/blog-affiliates/'));
    echo '<ul class="tw-mobile-list">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . $pkg_url . '">Packages</a></li>';
    echo '<li><a href="' . $blog_url . '">Blog</a></li>';
    echo '<li><a href="' . esc_url($plan_url) . '">Plan a Trip</a></li>';
    echo '</ul>';
}
?>

<style>
/* ================================================================
   TW NAV STYLES
   ================================================================ */
.tw-nav {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(13,21,38,0.96);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(6,146,175,0.2);
    font-family: 'Nunito', sans-serif;
}

.tw-nav-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    height: 68px;
    gap: 8px;
}

/* Brand */
.tw-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    text-decoration: none;
    flex-shrink: 0;
}

/* Custom logo (uploaded via Customizer) — sits in place of the SVG ring */
.tw-custom-logo {
    max-height: 42px;
    width: auto;
    display: block;
    object-fit: contain;
    flex-shrink: 0;
}

.tw-logo-ring {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid #FCB415;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(252,180,21,0.06);
    flex-shrink: 0;
}

.tw-compass { width: 28px; height: 28px; }

.tw-brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1;
}

.tw-brand-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.45rem;
    color: #ffffff;
    letter-spacing: 0.04em;
}

.tw-gold { color: #FCB415; }

.tw-brand-tag {
    font-size: 0.6rem;
    color: rgba(255,255,255,0.4);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-top: 2px;
}

/* Desktop links */
.tw-links {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-left: auto;
    flex-wrap: nowrap;
}

.tw-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    gap: 2px;
    flex-shrink: 0;
}

.tw-menu-item { margin: 0; }

.tw-nav-link {
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    font-size: 0.86rem;
    font-weight: 600;
    padding: 6px 11px;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
    display: block;
    white-space: nowrap;
}

.tw-nav-link:hover,
.tw-nav-link.tw-active {
    background: rgba(6,146,175,0.15);
    color: #ffffff;
}

/* CTA button */
.tw-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-left: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #FCB415 0%, #f09a00 100%);
    color: #0D1526 !important;
    font-size: 0.84rem;
    font-weight: 800;
    border-radius: 8px;
    text-decoration: none;
    letter-spacing: 0.02em;
    transition: opacity 0.2s, transform 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.tw-cta:hover { opacity: 0.88; transform: translateY(-1px); }

/* User nav */
.tw-user-nav {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-left: 6px;
    flex-shrink: 0;
}

.tw-auth-link {
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    font-size: 0.84rem;
    font-weight: 700;
    padding: 6px 11px;
    border-radius: 7px;
    transition: background 0.2s, color 0.2s;
    white-space: nowrap;
}
.tw-auth-link:hover { background: rgba(255,255,255,0.08); color: #fff; }

.tw-auth-btn {
    display: inline-flex;
    align-items: center;
    padding: 7px 14px;
    border: 1.5px solid rgba(252,180,21,0.6);
    border-radius: 8px;
    color: #FCB415;
    font-size: 0.84rem;
    font-weight: 800;
    text-decoration: none;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
    white-space: nowrap;
}
.tw-auth-btn:hover { background: #FCB415; color: #0d1526; border-color: #FCB415; }

.tw-user-dropdown { position: relative; }

.tw-user-trigger {
    display: flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 999px;
    padding: 5px 11px 5px 5px;
    cursor: pointer;
    color: #fff;
    font-family: 'Nunito', sans-serif;
    font-size: 0.84rem;
    font-weight: 700;
    transition: background 0.2s, border-color 0.2s;
    white-space: nowrap;
}
.tw-user-trigger:hover { background: rgba(255,255,255,0.12); border-color: rgba(252,180,21,0.4); }

.tw-user-avatar-wrap {
    width: 28px; height: 28px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid rgba(252,180,21,0.6);
    display: flex; align-items: center; justify-content: center;
    background: rgba(252,180,21,0.1);
    font-size: 0.85rem;
    flex-shrink: 0;
}
.tw-user-avatar-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }

.tw-user-display {
    max-width: 90px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.tw-user-chevron { font-size: 0.55rem; opacity: 0.6; transition: transform 0.2s; }
.tw-user-trigger[aria-expanded="true"] .tw-user-chevron { transform: rotate(180deg); }

.tw-user-menu {
    display: none;
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: rgba(13,21,38,0.98);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px;
    min-width: 200px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.45);
    overflow: hidden;
    z-index: 9000;
    animation: tw-dropdown-in 0.2s ease;
}
.tw-user-menu.open { display: block; }

@keyframes tw-dropdown-in {
    from { opacity: 0; transform: translateY(-6px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.tw-user-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 18px;
    color: rgba(255,255,255,0.78);
    text-decoration: none;
    font-size: 0.87rem;
    font-weight: 700;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    transition: background 0.15s, color 0.15s;
}
.tw-user-menu a:last-child { border-bottom: none; }
.tw-user-menu a:hover { background: rgba(255,255,255,0.07); color: #fff; }
.tw-user-menu a.tw-logout:hover { background: rgba(213,55,79,0.15); color: #ff6b81; }

/* Mobile toggle */
.tw-toggle {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    margin-left: auto;
    flex-shrink: 0;
}

.tw-toggle span {
    display: block;
    width: 22px;
    height: 2px;
    background: rgba(255,255,255,0.85);
    border-radius: 2px;
    transition: transform 0.25s, opacity 0.25s;
}

.tw-toggle.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
.tw-toggle.open span:nth-child(2) { opacity: 0; }
.tw-toggle.open span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

/* Mobile drawer */
.tw-mobile-menu {
    display: none;
    padding: 12px 24px 20px;
    border-top: 1px solid rgba(255,255,255,0.06);
}

.tw-mobile-menu.open { display: block; }

.tw-mobile-list {
    list-style: none;
    margin: 0 0 14px;
    padding: 0;
}

.tw-mobile-list li { border-bottom: 1px solid rgba(255,255,255,0.05); }

.tw-mobile-list a {
    display: block;
    color: rgba(255,255,255,0.82);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    padding: 11px 4px;
    transition: color 0.2s;
}
.tw-mobile-list a:hover { color: #FCB415; }

.tw-cta-mobile {
    display: block;
    text-align: center;
    margin: 0;
    width: 100%;
}

/* Mobile auth buttons */
.tw-mobile-auth {
    display: flex;
    gap: 10px;
    padding: 14px 4px 0;
    border-top: 1px solid rgba(255,255,255,0.06);
    margin-top: 8px;
}
.tw-mobile-auth a {
    flex: 1;
    text-align: center;
    padding: 10px;
    border-radius: 9px;
    font-weight: 800;
    font-size: 0.88rem;
    text-decoration: none;
    color: inherit;
}
.tw-mobile-auth .login  { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.8); }
.tw-mobile-auth .signup { background: linear-gradient(135deg,#FCB415,#f09a00); color: #0d1526; }

/* Responsive breakpoint */
@media (max-width: 900px) {
    .tw-links   { display: none; }
    .tw-toggle  { display: flex; }
    .tw-user-nav { display: none; }
}

/* Nav shrink on scroll (JS adds this class) */
.tw-nav-scrolled .tw-nav-inner { height: 56px; }
.tw-nav-scrolled { box-shadow: 0 4px 28px rgba(0,0,0,0.35); }
</style>

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
})();
</script>

<!-- ===== SUB-HEADER TABS ===== -->
<?php if (function_exists('mytheme_travel_tabs')) { mytheme_travel_tabs(); } ?>
