<?php
/**
 * Plugin Name: 1TripWiser Auto-Setup
 * Description: Ensures the correct theme is active and the front page is
 *              configured on every environment (local, staging, production).
 *              Runs as a Must-Use plugin so WordPress loads it automatically
 *              without manual activation — perfect for CI/CD deployments.
 */

// Fire as early as possible in the WordPress boot sequence
add_action( 'muplugins_loaded', 'tw_autosetup_theme', 1 );
add_action( 'init',             'tw_autosetup_front_page', 999 );

/**
 * Switch to our custom theme if it isn't already active.
 * Redirects the current request so the next page load uses the correct theme.
 */
function tw_autosetup_theme() {
    $target = 'my-theme';

    if ( get_option( 'stylesheet' ) === $target && get_option( 'template' ) === $target ) {
        return; // Already correct — fast path (DB options are cached in memory)
    }

    // Safety check: only switch if the theme actually exists and has no errors
    $theme = wp_get_theme( $target );
    if ( ! $theme->exists() || $theme->errors() ) {
        return;
    }

    // Update the active theme in the database
    update_option( 'stylesheet', $target );
    update_option( 'template',   $target );

    // Redirect so THIS request also benefits from the theme switch
    if ( ! headers_sent() ) {
        $redirect_to = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/';
        header( 'Location: ' . esc_url_raw( $redirect_to ), true, 302 );
        exit;
    }
}

/**
 * Ensure Settings → Reading points to the "home" page as the static front page.
 * Idempotent — only writes to DB when something is misconfigured.
 */
function tw_autosetup_front_page() {
    // Quick cache-hit check before any DB queries
    if ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) > 0 ) {
        return;
    }

    $home_page = get_page_by_path( 'home' );
    if ( ! $home_page ) {
        return; // Home page not created yet — functions.php will create it
    }

    update_option( 'show_on_front',  'page' );
    update_option( 'page_on_front',  $home_page->ID );
}
