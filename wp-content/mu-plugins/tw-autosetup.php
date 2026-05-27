<?php
/**
 * Plugin Name: 1TripWiser Auto-Setup
 * Description: Locks the active theme to "my-theme" via pre_option filters
 *              (more reliable than update_option + redirect) and auto-configures
 *              the static front page. Loaded automatically by WordPress.
 */

/* ============================================================================
   THEME LOCK
   Forces 'my-theme' to be the active theme on EVERY request by intercepting
   the get_option('stylesheet') and get_option('template') calls. No DB writes,
   no redirects, no first-request flash of the default WordPress theme.
   ============================================================================ */
add_filter( 'pre_option_stylesheet', 'tw_force_active_theme' );
add_filter( 'pre_option_template',   'tw_force_active_theme' );

function tw_force_active_theme( $pre ) {
    // Cache the directory check so we only hit the filesystem once per request
    static $theme_exists = null;
    if ( $theme_exists === null ) {
        $theme_exists = is_dir( WP_CONTENT_DIR . '/themes/my-theme' );
    }
    return $theme_exists ? 'my-theme' : $pre;
}

/* ============================================================================
   FRONT PAGE LOCK
   Ensures Settings → Reading is configured to use the "home" page as the
   static front page. Runs on init so the home page (created by the theme's
   tw_maybe_create_pages function) is available.
   ============================================================================ */
add_action( 'init', 'tw_autosetup_front_page', 999 );

function tw_autosetup_front_page() {
    // Fast path — already configured correctly
    if ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) > 0 ) {
        return;
    }

    $home = get_page_by_path( 'home' );
    if ( ! $home ) {
        return; // theme's tw_maybe_create_pages will create it on next request
    }

    update_option( 'show_on_front',  'page' );
    update_option( 'page_on_front',  $home->ID );
}
