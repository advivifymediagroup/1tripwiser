<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
