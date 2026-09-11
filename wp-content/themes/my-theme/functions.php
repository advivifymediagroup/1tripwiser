<?php

/* ── Tribe community forum (custom post type, replies, likes, leaderboard) ── */
require_once get_template_directory() . '/includes/forum.php';

/* ── Explore system: region tree + events taxonomy, subheader, unified filters ── */
require_once get_template_directory() . '/includes/explore.php';

/* ── Demo content seeder (Tools → Demo Content) for leadership walkthroughs ── */
require_once get_template_directory() . '/includes/demo-seed.php';

function load_css(){
    wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), false, 'all');
    wp_enqueue_style('bootstrap');
}

add_action('wp_enqueue_scripts', 'load_css');


function load_js(){
    wp_enqueue_script('jquery');
    wp_register_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), false, true);
    wp_enqueue_script('bootstrap');
}

add_action('wp_enqueue_scripts', 'load_js');

function mytheme_enqueue_styles() {
    /* Use filemtime() as the version string so the browser cache busts EVERY time style.css is modified. 
    Without this, WordPress falls back to the WP core version (e.g. ?ver=7.0) which never changes, so updated CSS stays cached. */
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_ver  = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0';
    // Google Fonts as a real <link> (not a CSS @import inside style.css) so it
    // loads in parallel instead of after style.css finishes downloading.
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,300;1,9..144,400;1,9..144,600;1,9..144,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Saira:wght@400;500;600&family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500;1,600&family=Bebas+Neue&display=swap', array(), null);
    wp_enqueue_style('main-style', get_stylesheet_uri(), array(), $style_ver);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css');
    wp_enqueue_style('flaticon-uicons', 'https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css');
    // Animations CSS (global) — same filemtime cache-busting
    // $anim_path = get_template_directory() . '/assets/css/tw-animations.css';
    // $anim_ver  = file_exists( $anim_path ) ? filemtime( $anim_path ) : '1.1';
    // wp_enqueue_style('tw-animations', get_template_directory_uri() . '/assets/css/tw-animations.css', array(), $anim_ver);
    
    
    
    wp_enqueue_script('jquery');
    // Custom JS placeholder (kept for legacy localize_script hook)
    if ( file_exists( get_template_directory() . '/assets/js/custom.js' ) ) {
        $custom_js_path = get_template_directory() . '/assets/js/custom.js';
        $custom_js_ver  = filemtime($custom_js_path);
        wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), $custom_js_ver, true);
    } else {
        // Register a dummy handle so localize_script still works
        wp_register_script('custom-js', '', array('jquery'), '1.0', true);
        wp_enqueue_script('custom-js');
    }
    // Global animations (canvas bubbles, scroll reveal, tilt, ripple)
    // filemtime()-based version so the browser/CDN cache busts every time this file changes.
    $tw_anim_path = get_template_directory() . '/assets/js/tw-animations.js';
    $tw_anim_ver  = file_exists( $tw_anim_path ) ? filemtime( $tw_anim_path ) : '1.1';
    wp_enqueue_script('tw-animations', get_template_directory_uri() . '/assets/js/tw-animations.js', array(), $tw_anim_ver, true);
    // Content image carousel — auto-activates for 2+ images in any post content area
    wp_enqueue_script('tw-carousel', get_template_directory_uri() . '/assets/js/tw-carousel.js', array(), '1.0', true);
    // AJAX section filter
    wp_enqueue_script('tw-filters', get_template_directory_uri() . '/assets/js/tw-filters.js', array(), '1.0', true);
    wp_localize_script('tw-filters', 'tw_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tw_filter_nonce'),
    ));
}

add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');

/* LUXURY sprint template — scroll-scrubbed headline animation, loaded only there. */
function tw_luxury_enqueue_scripts() {
    if ( ! is_page_template( 'page-luxury.php' ) ) { return; }
    $path = get_template_directory() . '/assets/js/tw-luxury.js';
    $ver  = file_exists( $path ) ? filemtime( $path ) : '1.0';
    wp_enqueue_script( 'tw-luxury', get_template_directory_uri() . '/assets/js/tw-luxury.js', array(), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'tw_luxury_enqueue_scripts' );

/* Performance: drop two requests nothing on the site depends on.
   - Emoji detection JS/CSS: no theme template uses the emoji picker/fallback.
   - jquery-migrate: only needed for deprecated jQuery APIs (.live(), .die(), etc.),
     none of which appear anywhere in this theme's JS. */
function mytheme_disable_unused_assets() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'mytheme_disable_unused_assets' );

function mytheme_dequeue_jquery_migrate( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
    }
}
add_action( 'wp_default_scripts', 'mytheme_dequeue_jquery_migrate' );

// ============================================================
// Hide WordPress admin bar on the frontend (keeps it in wp-admin)
// ============================================================
add_filter('show_admin_bar', '__return_false');

// ============================================================
// Enable user registration (required for Register page)
// ============================================================
add_action('init', function () {
    if ( ! get_option('users_can_register') ) {
        update_option('users_can_register', 1);
    }
}, 5);

// ============================================================
// AJAX: Custom Login
// ============================================================
function tw_ajax_login() {
    if ( ! isset( $_POST['tw_login_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tw_login_nonce'] ) ), 'tw_login_nonce' ) ) {
        wp_send_json_error( 'Security check failed.' );
    }

    $creds = array(
        'user_login'    => sanitize_text_field( wp_unslash( $_POST['tw_username'] ?? '' ) ),
        'user_password' => wp_unslash( $_POST['tw_password'] ?? '' ),
        'remember'      => ! empty( $_POST['tw_remember'] ),
    );

    if ( empty( $creds['user_login'] ) || empty( $creds['user_password'] ) ) {
        wp_send_json_error( 'Please enter your username and password.' );
    }

    $user = wp_signon( $creds, is_ssl() );
    if ( is_wp_error( $user ) ) {
        wp_send_json_error( 'Incorrect username or password.' );
    }

    $redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url('/');
    wp_send_json_success( array( 'redirect' => $redirect ) );
}
add_action( 'wp_ajax_nopriv_tw_ajax_login', 'tw_ajax_login' );
add_action( 'wp_ajax_tw_ajax_login',        'tw_ajax_login' ); // allow logged-in reload

// ============================================================
// AJAX: Custom Register
// ============================================================
function tw_ajax_register() {
    if ( ! isset( $_POST['tw_register_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tw_register_nonce'] ) ), 'tw_register_nonce' ) ) {
        wp_send_json_error( 'Security check failed.' );
    }

    if ( ! get_option('users_can_register') ) {
        wp_send_json_error( 'Registrations are currently closed.' );
    }

    $username  = sanitize_user( wp_unslash( $_POST['tw_reg_username'] ?? '' ) );
    $email     = sanitize_email( wp_unslash( $_POST['tw_reg_email']   ?? '' ) );
    $password  = wp_unslash( $_POST['tw_reg_password'] ?? '' );
    $fname     = sanitize_text_field( wp_unslash( $_POST['tw_first_name'] ?? '' ) );
    $lname     = sanitize_text_field( wp_unslash( $_POST['tw_last_name']  ?? '' ) );

    // Validate
    if ( empty( $username ) || strlen( $username ) < 3 ) {
        wp_send_json_error( 'Username must be at least 3 characters.' );
    }
    if ( ! preg_match('/^[a-zA-Z0-9_\-]+$/', $username) ) {
        wp_send_json_error( 'Username contains invalid characters.' );
    }
    if ( username_exists( $username ) ) {
        wp_send_json_error( 'That username is already taken. Please choose another.' );
    }
    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( 'Please enter a valid email address.' );
    }
    if ( email_exists( $email ) ) {
        wp_send_json_error( 'That email is already registered. Try logging in instead.' );
    }
    if ( strlen( $password ) < 8 ) {
        wp_send_json_error( 'Password must be at least 8 characters.' );
    }

    // Create user
    $user_id = wp_create_user( $username, $password, $email );
    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( $user_id->get_error_message() );
    }

    // Update display name & bio
    wp_update_user( array(
        'ID'           => $user_id,
        'first_name'   => $fname,
        'last_name'    => $lname,
        'display_name' => trim( $fname . ' ' . $lname ) ?: $username,
        'role'         => 'author', // can publish their own posts
    ) );

    // Auto-login
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, false, is_ssl() );

    // Welcome email
    wp_new_user_notification( $user_id, null, 'user' );

    wp_send_json_success( array( 'redirect' => home_url('/') ) );
}
add_action( 'wp_ajax_nopriv_tw_ajax_register', 'tw_ajax_register' );

// Register travel content types and taxonomies
function mytheme_register_travel_content() {
    register_taxonomy('destination_region', array('post', 'destination', 'itinerary', 'travel_package', 'group_trip', 'corporate_trip', 'tw_event'), array(
        'labels' => array(
            'name' => __('Destination Regions', 'mytheme'),
            'singular_name' => __('Destination Region', 'mytheme'),
            'search_items' => __('Search Destination Regions', 'mytheme'),
            'all_items' => __('All Destination Regions', 'mytheme'),
            'edit_item' => __('Edit Destination Region', 'mytheme'),
            'update_item' => __('Update Destination Region', 'mytheme'),
            'add_new_item' => __('Add New Destination Region', 'mytheme'),
            'new_item_name' => __('New Destination Region Name', 'mytheme'),
            'menu_name' => __('Destination Regions', 'mytheme'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'destination-region'),
    ));

    register_taxonomy('trip_style', array('post', 'destination', 'itinerary', 'travel_package'), array(
        'labels' => array(
            'name' => __('Trip Styles', 'mytheme'),
            'singular_name' => __('Trip Style', 'mytheme'),
            'menu_name' => __('Trip Styles', 'mytheme'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'trip-style'),
    ));

    register_post_type('destination', array(
        'labels' => array(
            'name' => __('Destinations', 'mytheme'),
            'singular_name' => __('Destination', 'mytheme'),
            'add_new_item' => __('Add New Destination', 'mytheme'),
            'edit_item' => __('Edit Destination', 'mytheme'),
            'new_item' => __('New Destination', 'mytheme'),
            'view_item' => __('View Destination', 'mytheme'),
            'search_items' => __('Search Destinations', 'mytheme'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-site-alt3',
        'rewrite' => array('slug' => 'destinations'),
        'show_in_rest' => true,
        'supports' => array('title', 'excerpt', 'thumbnail', 'author', 'revisions'),
        'taxonomies' => array('destination_region', 'trip_style'),
    ));

    register_post_type('itinerary', array(
        'labels' => array(
            'name' => __('Itineraries', 'mytheme'),
            'singular_name' => __('Itinerary', 'mytheme'),
            'add_new_item' => __('Add New Itinerary', 'mytheme'),
            'edit_item' => __('Edit Itinerary', 'mytheme'),
            'new_item' => __('New Itinerary', 'mytheme'),
            'view_item' => __('View Itinerary', 'mytheme'),
            'search_items' => __('Search Itineraries', 'mytheme'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-location-alt',
        'rewrite' => array('slug' => 'itineraries'),
        'show_in_rest' => true,
        'supports' => array('title', 'excerpt', 'thumbnail', 'author', 'revisions'),
        'taxonomies' => array('destination_region', 'trip_style'),
    ));

    register_post_type('travel_package', array(
        'labels' => array(
            'name' => __('Travel Packages', 'mytheme'),
            'singular_name' => __('Travel Package', 'mytheme'),
            'add_new_item' => __('Add New Package', 'mytheme'),
            'edit_item' => __('Edit Package', 'mytheme'),
            'new_item' => __('New Package', 'mytheme'),
            'view_item' => __('View Package', 'mytheme'),
            'search_items' => __('Search Packages', 'mytheme'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-palmtree',
        'rewrite' => array('slug' => 'packages'),
        'show_in_rest' => true,
        'supports' => array('title', 'excerpt', 'thumbnail', 'author', 'revisions'),
        'taxonomies' => array('destination_region', 'trip_style'),
    ));
}
add_action('init', 'mytheme_register_travel_content');

function mytheme_use_classic_editor_for_travel_content($use_block_editor, $post_type) {
    if (in_array($post_type, array('destination', 'itinerary', 'travel_package', 'corporate_trip'), true)) {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'mytheme_use_classic_editor_for_travel_content', 10, 2);

// function mytheme_register_acf_travel_fields() {
//     if (!function_exists('acf_add_local_field_group')) {
//         return;
//     }

//     acf_add_local_field_group(array(
//         'key' => 'group_mytheme_package_details',
//         'title' => 'Package Details',
//         'fields' => array(
//             array(
//                 'key' => 'field_mytheme_package_image',
//                 'label' => 'Package Image',
//                 'name' => 'package_image',
//                 'type' => 'image',
//                 'return_format' => 'array',
//                 'preview_size' => 'medium',
//                 'library' => 'all',
//             ),
//             array(
//                 'key' => 'field_mytheme_package_location',
//                 'label' => 'Location',
//                 'name' => 'package_location',
//                 'type' => 'text',
//             ),
//             array(
//                 'key' => 'field_mytheme_package_tag',
//                 'label' => 'Package Tag',
//                 'name' => 'package_tag',
//                 'type' => 'select',
//                 'choices' => array(
//                     'bestseller' => 'Bestseller',
//                     'trending' => 'Trending',
//                     'new' => 'New',
//                     'limited' => 'Limited Seats',
//                     'popular' => 'Popular',
//                 ),
//                 'allow_null' => 1,
//                 'ui' => 1,
//             ),
//             array(
//                 'key' => 'field_mytheme_package_total_nights',
//                 'label' => 'Total Nights',
//                 'name' => 'total_nights',
//                 'type' => 'number',
//                 'min' => 0,
//             ),
//             array(
//                 'key' => 'field_mytheme_package_total_days',
//                 'label' => 'Total Days',
//                 'name' => 'total_days',
//                 'type' => 'number',
//                 'min' => 1,
//             ),
//             array(
//                 'key' => 'field_mytheme_package_trip_type',
//                 'label' => 'Group or Single',
//                 'name' => 'package_trip_type',
//                 'type' => 'select',
//                 'choices' => array(
//                     'Group Trip' => 'Group Trip',
//                     'Single Traveller' => 'Single Traveller',
//                     'Private Trip' => 'Private Trip',
//                     'Family Trip' => 'Family Trip',
//                     'Honeymoon' => 'Honeymoon',
//                 ),
//                 'allow_null' => 1,
//                 'ui' => 1,
//             ),
//             array(
//                 'key' => 'field_mytheme_package_amount',
//                 'label' => 'Amount',
//                 'name' => 'package_amount',
//                 'type' => 'number',
//                 'prepend' => '₹',
//                 'min' => 0,
//             ),
//             array(
//                 'key' => 'field_mytheme_package_emi',
//                 'label' => 'EMI Option',
//                 'name' => 'package_emi',
//                 'type' => 'text',
//                 'instructions' => 'Example: EMI from ₹2,499/month',
//             ),
//             array(
//                 'key' => 'field_mytheme_package_book_url',
//                 'label' => 'Book Now Button URL',
//                 'name' => 'package_book_url',
//                 'type' => 'url',
//             ),
//             array(
//                 'key' => 'field_mytheme_package_overview',
//                 'label' => 'Package Overview',
//                 'name' => 'package_overview',
//                 'type' => 'wysiwyg',
//                 'tabs' => 'all',
//                 'toolbar' => 'basic',
//                 'media_upload' => 0,
//             ),
//             array(
//                 'key' => 'field_mytheme_package_destination',
//                 'label' => 'Linked Destination',
//                 'name' => 'linked_destination',
//                 'type' => 'post_object',
//                 'post_type' => array('destination'),
//                 'return_format' => 'object',
//                 'ui' => 1,
//             ),
//         ),
//         'location' => array(
//             array(
//                 array(
//                     'param' => 'post_type',
//                     'operator' => '==',
//                     'value' => 'travel_package',
//                 ),
//             ),
//         ),
//         'position' => 'acf_after_title',
//         'style' => 'default',
//         'show_in_rest' => 1,
//     ));

//     acf_add_local_field_group(array(
//         'key' => 'group_mytheme_itinerary_details',
//         'title' => 'Itinerary Details',
//         'fields' => array(
//             array(
//                 'key' => 'field_mytheme_itinerary_destination',
//                 'label' => 'Linked Destination',
//                 'name' => 'itinerary_destination',
//                 'type' => 'post_object',
//                 'post_type' => array('destination'),
//                 'return_format' => 'object',
//                 'ui' => 1,
//             ),
//             array(
//                 'key' => 'field_mytheme_itinerary_duration',
//                 'label' => 'Duration',
//                 'name' => 'itinerary_duration',
//                 'type' => 'text',
//                 'instructions' => 'Example: 5 nights / 6 days',
//             ),
//             array(
//                 'key' => 'field_mytheme_itinerary_best_time',
//                 'label' => 'Best Time To Visit',
//                 'name' => 'itinerary_best_time',
//                 'type' => 'text',
//             ),
//             array(
//                 'key' => 'field_mytheme_itinerary_route_summary',
//                 'label' => 'Route Summary',
//                 'name' => 'itinerary_route_summary',
//                 'type' => 'textarea',
//                 'rows' => 3,
//             ),
//             array(
//                 'key' => 'field_mytheme_itinerary_days',
//                 'label' => 'Day Wise Plan',
//                 'name' => 'itinerary_days',
//                 'type' => 'repeater',
//                 'layout' => 'block',
//                 'button_label' => 'Add Day',
//                 'sub_fields' => array(
//                     array(
//                         'key' => 'field_mytheme_itinerary_day_title',
//                         'label' => 'Day Title',
//                         'name' => 'day_title',
//                         'type' => 'text',
//                     ),
//                     array(
//                         'key' => 'field_mytheme_itinerary_day_details',
//                         'label' => 'Day Details',
//                         'name' => 'day_details',
//                         'type' => 'textarea',
//                         'rows' => 3,
//                     ),
//                 ),
//             ),
//         ),
//         'location' => array(
//             array(
//                 array(
//                     'param' => 'post_type',
//                     'operator' => '==',
//                     'value' => 'itinerary',
//                 ),
//             ),
//         ),
//         'position' => 'acf_after_title',
//         'show_in_rest' => 1,
//     ));

//     acf_add_local_field_group(array(
//         'key' => 'group_mytheme_destination_details',
//         'title' => 'Destination Details',
//         'fields' => array(
//             array(
//                 'key' => 'field_mytheme_destination_image',
//                 'label' => 'Destination Image',
//                 'name' => 'destination_image',
//                 'type' => 'image',
//                 'return_format' => 'array',
//                 'preview_size' => 'medium',
//             ),
//             array(
//                 'key' => 'field_mytheme_destination_country',
//                 'label' => 'Country / Region',
//                 'name' => 'destination_country',
//                 'type' => 'text',
//             ),
//             array(
//                 'key' => 'field_mytheme_destination_best_time',
//                 'label' => 'Best Time To Visit',
//                 'name' => 'destination_best_time',
//                 'type' => 'text',
//             ),
//             array(
//                 'key' => 'field_mytheme_destination_ideal_duration',
//                 'label' => 'Ideal Duration',
//                 'name' => 'destination_ideal_duration',
//                 'type' => 'text',
//             ),
//             array(
//                 'key' => 'field_mytheme_destination_starting_price',
//                 'label' => 'Starting Price',
//                 'name' => 'destination_starting_price',
//                 'type' => 'number',
//                 'prepend' => '₹',
//                 'min' => 0,
//             ),
//             array(
//                 'key' => 'field_mytheme_destination_short_intro',
//                 'label' => 'Short Intro',
//                 'name' => 'destination_short_intro',
//                 'type' => 'textarea',
//                 'rows' => 3,
//             ),
//             array(
//                 'key' => 'field_mytheme_destination_overview',
//                 'label' => 'Destination Overview',
//                 'name' => 'destination_overview',
//                 'type' => 'wysiwyg',
//                 'tabs' => 'all',
//                 'toolbar' => 'basic',
//                 'media_upload' => 0,
//             ),
//         ),
//         'location' => array(
//             array(
//                 array(
//                     'param' => 'post_type',
//                     'operator' => '==',
//                     'value' => 'destination',
//                 ),
//             ),
//         ),
//         'position' => 'acf_after_title',
//         'show_in_rest' => 1,
//     ));
// }
// add_action('acf/init', 'mytheme_register_acf_travel_fields');

function mytheme_travel_meta_fields() {
    return array(
        'destination_name' => __('Destination', 'mytheme'),
        'trip_duration' => __('Duration', 'mytheme'),
        'starting_price' => __('Starting Price', 'mytheme'),
        'best_time' => __('Best Time To Visit', 'mytheme'),
        'group_size' => __('Group Size', 'mytheme'),
        'route_summary' => __('Route Summary (one line, e.g. Delhi → Manali → Kasol)', 'mytheme'),
        'event_date' => __('Event Date (Events & Festivals — e.g. 29 May 2026)', 'mytheme'),
        'book_url'   => __('Booking URL (used by the "Book Now" button; blank = trip page)', 'mytheme'),
    );
}

function mytheme_add_travel_meta_boxes() {
    // When ACF is active, the ACF field group (mytheme_register_acf_travel_fields)
    // provides the editor for all trip types. Only fall back to this simple meta
    // box when ACF is not present.
    if (function_exists('acf_add_local_field_group')) {
        return;
    }

    add_meta_box(
        'mytheme_travel_details',
        __('Travel Details', 'mytheme'),
        'mytheme_render_travel_meta_box',
        array('itinerary', 'travel_package', 'tw_event', 'group_trip', 'corporate_trip'),
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mytheme_add_travel_meta_boxes');

function mytheme_render_travel_meta_box($post) {
    wp_nonce_field('mytheme_save_travel_meta', 'mytheme_travel_meta_nonce');

    foreach (mytheme_travel_meta_fields() as $key => $label) {
        $value = get_post_meta($post->ID, '_' . $key, true);
        echo '<p><label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label>';
        echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width:100%;margin-top:6px;" /></p>';
    }
}

function mytheme_save_travel_meta($post_id) {
    if (!isset($_POST['mytheme_travel_meta_nonce']) || !wp_verify_nonce($_POST['mytheme_travel_meta_nonce'], 'mytheme_save_travel_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    foreach (mytheme_travel_meta_fields() as $key => $label) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, '_' . $key, sanitize_text_field(wp_unslash($_POST[$key])));
        }
    }
}
add_action('save_post_itinerary', 'mytheme_save_travel_meta');
add_action('save_post_travel_package', 'mytheme_save_travel_meta');
add_action('save_post_corporate_trip', 'mytheme_save_travel_meta');

function mytheme_get_plan_trip_url() {
    $page = get_page_by_path('plan-a-trip');
    return $page ? get_permalink($page) : home_url('/plan-a-trip/');
}

/** Render an affiliate "icon" value as a Flaticon UIcon (slug like "hotel"),
 *  falling back to printing the raw value verbatim for any legacy emoji
 *  an admin may have already saved in the icon meta field. */
function tw_render_aff_icon($icon) {
    $icon = trim((string) $icon);
    if ($icon !== '' && preg_match('/^[a-z0-9-]+$/', $icon)) {
        echo '<i class="fi-rr-' . esc_attr($icon) . '" aria-hidden="true"></i>';
    } else {
        echo esc_html($icon);
    }
}

function mytheme_get_page_url_by_path($path) {
    $page = get_page_by_path($path);
    return $page ? get_permalink($page) : home_url('/' . trim($path, '/') . '/');
}

function mytheme_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    $items = array(
        array(
            'label' => __('Home', 'mytheme'),
            'url' => home_url('/'),
        ),
    );

    if (is_singular('travel_package')) {
        $items[] = array('label' => __('Packages', 'mytheme'), 'url' => get_post_type_archive_link('travel_package'));
        $items[] = array('label' => get_the_title(), 'url' => '');
    } elseif (is_singular('itinerary')) {
        $items[] = array('label' => __('Itineraries', 'mytheme'), 'url' => get_post_type_archive_link('itinerary'));
        $items[] = array('label' => get_the_title(), 'url' => '');
    } elseif (is_singular('destination')) {
        $items[] = array('label' => __('Destinations', 'mytheme'), 'url' => get_post_type_archive_link('destination'));
        $items[] = array('label' => get_the_title(), 'url' => '');
    } elseif (is_singular('post')) {
        $blog_url = get_permalink(get_option('page_for_posts'));
        $items[] = array('label' => __('Blog', 'mytheme'), 'url' => $blog_url ? $blog_url : home_url('/blog/'));
        $items[] = array('label' => get_the_title(), 'url' => '');
    } elseif (is_page()) {
        $ancestors = array_reverse(get_post_ancestors(get_the_ID()));
        foreach ($ancestors as $ancestor_id) {
            $items[] = array('label' => get_the_title($ancestor_id), 'url' => get_permalink($ancestor_id));
        }
        $items[] = array('label' => get_the_title(), 'url' => '');
    } elseif (is_post_type_archive('travel_package')) {
        $items[] = array('label' => __('Packages', 'mytheme'), 'url' => '');
    } elseif (is_post_type_archive('itinerary')) {
        $items[] = array('label' => __('Itineraries', 'mytheme'), 'url' => '');
    } elseif (is_post_type_archive('destination')) {
        $items[] = array('label' => __('Destinations', 'mytheme'), 'url' => '');
    } elseif (is_archive()) {
        $items[] = array('label' => get_the_archive_title(), 'url' => '');
    } elseif (is_search()) {
        $items[] = array('label' => sprintf(__('Search: %s', 'mytheme'), get_search_query()), 'url' => '');
    }

    if (count($items) < 2) {
        return;
    }

    echo '<nav class="tw-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'mytheme') . '">';
    foreach ($items as $index => $item) {
        if ($index > 0) {
            echo '<span class="tw-breadcrumb-separator" aria-hidden="true">/</span>';
        }

        if (!empty($item['url']) && $index < count($items) - 1) {
            echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>';
        } else {
            echo '<span aria-current="page">' . esc_html($item['label']) . '</span>';
        }
    }
    echo '</nav>';
}

function mytheme_travel_tabs() {
    // Subheader is now the Explore mega-menu (India / International / Events & Festivals).
    if ( function_exists( 'tw_explore_subheader' ) ) {
        tw_explore_subheader();
    }
}

function mytheme_travel_detail_items($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $details = array();

    foreach (mytheme_travel_meta_fields() as $key => $label) {
        $value = get_post_meta($post_id, '_' . $key, true);
        if ($value) {
            $details[] = array(
                'label' => $label,
                'value' => $value,
            );
        }
    }

    return $details;
}

function mytheme_get_travel_field($name, $post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();

    if (function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    return get_post_meta($post_id, '_' . $name, true);
}

function mytheme_format_rupee_amount($amount) {
    if ($amount === '' || $amount === null) {
        return '';
    }

    if (is_numeric($amount)) {
        return '₹' . number_format_i18n((float) $amount);
    }

    return $amount;
}

function mytheme_get_package_data($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $nights = mytheme_get_travel_field('total_nights', $post_id);
    $days = mytheme_get_travel_field('total_days', $post_id);
    $duration = '';

    if ($nights !== '' && $days !== '') {
        $duration = sprintf(__('%s nights / %s days', 'mytheme'), $nights, $days);
    } elseif ($days !== '') {
        $duration = sprintf(__('%s days', 'mytheme'), $days);
    } elseif ($nights !== '') {
        $duration = sprintf(__('%s nights', 'mytheme'), $nights);
    } else {
        $duration = get_post_meta($post_id, '_trip_duration', true);
    }

    return array(
        'image' => mytheme_get_travel_field('package_image', $post_id),
        'location' => mytheme_get_travel_field('package_location', $post_id),
        'tag' => mytheme_get_travel_field('package_tag', $post_id),
        'duration' => $duration,
        'trip_type' => mytheme_get_travel_field('package_trip_type', $post_id),
        'amount' => mytheme_format_rupee_amount(mytheme_get_travel_field('package_amount', $post_id)),
        'emi' => mytheme_get_travel_field('package_emi', $post_id),
        'book_url' => mytheme_get_travel_field('package_book_url', $post_id),
        'overview' => mytheme_get_travel_field('package_overview', $post_id),
    );
}

function mytheme_get_image_url($image, $size = 'medium') {
    if (is_array($image)) {
        if (isset($image['sizes'][$size])) {
            return $image['sizes'][$size];
        }

        if (isset($image['url'])) {
            return $image['url'];
        }
    }

    if (is_numeric($image)) {
        return wp_get_attachment_image_url((int) $image, $size);
    }

    if (is_string($image)) {
        return $image;
    }

    return '';
}

function mytheme_get_trip_pdf_url($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();

    return add_query_arg('trip_pdf', '1', get_permalink($post_id));
}

function mytheme_render_trip_pdf_button($post_id = null, $label = '') {
    $post_id = $post_id ? $post_id : get_the_ID();
    $label = $label ? $label : __('Download PDF', 'mytheme');

    if (!in_array(get_post_type($post_id), array('itinerary', 'travel_package'), true)) {
        return;
    }
    ?>
    <a href="<?php echo esc_url(mytheme_get_trip_pdf_url($post_id)); ?>" class="btn-secondary tw-pdf-btn" target="_blank" rel="noopener">
        <?php echo esc_html($label); ?>
    </a>
    <?php
}

function mytheme_get_package_compare_ids() {
    if (empty($_GET['compare_packages'])) {
        return array();
    }

    $raw_ids = explode(',', sanitize_text_field(wp_unslash($_GET['compare_packages'])));
    $ids = array();

    foreach ($raw_ids as $raw_id) {
        $id = absint($raw_id);

        if ($id && get_post_type($id) === 'travel_package' && get_post_status($id) === 'publish') {
            $ids[] = $id;
        }
    }

    return array_slice(array_values(array_unique($ids)), 0, 3);
}

function mytheme_format_compare_value($value) {
    if (is_array($value)) {
        $value = implode(', ', array_filter(array_map('wp_strip_all_tags', $value)));
    }

    $value = trim(wp_strip_all_tags((string) $value));

    return $value !== '' ? $value : __('Not specified', 'mytheme');
}

function mytheme_render_package_compare_table($package_ids = array()) {
    $package_ids = $package_ids ? $package_ids : mytheme_get_package_compare_ids();
    $package_ids = array_slice(array_values(array_unique(array_map('absint', $package_ids))), 0, 3);
    $package_count = count($package_ids);

    if ($package_count < 2) {
        return;
    }

    $rows = array(
        'location' => __('Location', 'mytheme'),
        'duration' => __('Duration', 'mytheme'),
        'trip_type' => __('Trip Type', 'mytheme'),
        'amount' => __('Price', 'mytheme'),
        'emi' => __('EMI', 'mytheme'),
    );
    ?>
    <section class="package-compare-section" id="package-comparison">
        <div class="package-compare-heading">
            <span><?php esc_html_e('Package comparison', 'mytheme'); ?></span>
            <h2><?php esc_html_e('Compare Selected Trips', 'mytheme'); ?></h2>
            <p><?php esc_html_e('Review location, duration, trip type and pricing side by side before choosing your trip.', 'mytheme'); ?></p>
        </div>

        <div class="package-compare-table-wrap">
            <table class="package-compare-table package-compare-table--count-<?php echo esc_attr($package_count); ?>">
                <colgroup>
                    <col class="package-compare-detail-col">
                    <?php foreach ($package_ids as $package_id) : ?>
                        <col class="package-compare-package-col">
                    <?php endforeach; ?>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php esc_html_e('Details', 'mytheme'); ?></th>
                        <?php foreach ($package_ids as $package_id) : ?>
                            <?php
                            $data = mytheme_get_package_data($package_id);
                            $image_url = mytheme_get_image_url($data['image'], 'large');
                            if (!$image_url && has_post_thumbnail($package_id)) {
                                $image_url = get_the_post_thumbnail_url($package_id, 'large');
                            }
                            ?>
                            <th>
                                <a class="package-compare-title" href="<?php echo esc_url(get_permalink($package_id)); ?>">
                                    <?php if ($image_url) : ?>
                                        <img src="<?php echo esc_url($image_url); ?>" alt="">
                                    <?php endif; ?>
                                    <strong><?php echo esc_html(get_the_title($package_id)); ?></strong>
                                </a>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $key => $label) : ?>
                        <tr>
                            <th><?php echo esc_html($label); ?></th>
                            <?php foreach ($package_ids as $package_id) : ?>
                                <?php $data = mytheme_get_package_data($package_id); ?>
                                <td><?php echo esc_html(mytheme_format_compare_value($data[$key] ?? '')); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th><?php esc_html_e('Action', 'mytheme'); ?></th>
                        <?php foreach ($package_ids as $package_id) : ?>
                            <td>
                                <a class="btn-primary btn-sm" href="<?php echo esc_url(get_permalink($package_id)); ?>">
                                    <?php esc_html_e('View Package', 'mytheme'); ?>
                                </a>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <?php
}

function mytheme_render_package_compare_tray() { 
    if (is_admin()) {
        return;
    }
    ?>
    <div class="package-compare-tray" data-package-compare-tray hidden>
        <div>
            <strong><?php esc_html_e('Compare packages', 'mytheme'); ?></strong>
            <span data-package-compare-count><?php esc_html_e('Select 2-3 packages', 'mytheme'); ?></span>
        </div>
        <div class="package-compare-tray-actions">
            <button type="button" data-package-compare-clear><?php esc_html_e('Clear', 'mytheme'); ?></button>
            <button type="button" data-package-compare-open><?php esc_html_e('Compare', 'mytheme'); ?></button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'mytheme_render_package_compare_tray');

function mytheme_render_trip_pdf_fact($label, $value) {
    if ($value === '' || $value === null) {
        return;
    }
    ?>
    <div class="tw-pdf-fact">
        <span><?php echo esc_html($label); ?></span>
        <strong><?php echo esc_html($value); ?></strong>
    </div>
    <?php
}

function mytheme_render_trip_pdf_document($post_id) {
    $post = get_post($post_id);

    if (!$post || !in_array($post->post_type, array('itinerary', 'travel_package'), true)) {
        return;
    }

    $is_package = $post->post_type === 'travel_package';
    $title = get_the_title($post_id);
    $print_url = mytheme_get_trip_pdf_url($post_id);
    $share_text = rawurlencode(sprintf('%s - %s', $title, $print_url));
    $custom_logo_id = get_theme_mod('custom_logo');
    $pdf_logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';
    if (!$pdf_logo_url) {
        $pdf_logo_url = get_template_directory_uri() . '/assets/images/logo-dark.png';
    }
    $image_url = '';
    $facts = array();
    $main_content = '';

    if ($is_package) {
        $package = mytheme_get_package_data($post_id);
        $image_url = mytheme_get_image_url($package['image'], 'large');
        if (!$image_url && has_post_thumbnail($post_id)) {
            $image_url = get_the_post_thumbnail_url($post_id, 'large');
        }

        $facts = array(
            __('Location', 'mytheme') => $package['location'],
            __('Duration', 'mytheme') => $package['duration'],
            __('Trip Type', 'mytheme') => $package['trip_type'],
            __('Amount', 'mytheme') => $package['amount'],
            __('EMI Option', 'mytheme') => $package['emi'],
        );
        $main_content = $package['overview'];
    } else {
        $destination = mytheme_get_travel_field('itinerary_destination', $post_id);
        $duration = mytheme_get_travel_field('itinerary_duration', $post_id);
        $best_time = mytheme_get_travel_field('itinerary_best_time', $post_id);
        $route_summary = mytheme_get_travel_field('itinerary_route_summary', $post_id);
        $image_url = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'large') : '';

        $facts = array(
            __('Destination', 'mytheme') => ($destination && isset($destination->post_title)) ? $destination->post_title : '',
            __('Duration', 'mytheme') => $duration,
            __('Best Time', 'mytheme') => $best_time,
        );
        $main_content = $route_summary ? wpautop($route_summary) : apply_filters('the_content', $post->post_content);
    }

    nocache_headers();
    status_header(200);
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html(sprintf(__('%s PDF', 'mytheme'), $title)); ?></title>
        <?php wp_site_icon(); ?>
        <style>
            :root {
                --tw-blue: #1B93B0;
                --tw-gold: #D83550;
                --tw-navy: #0d1526;
                --tw-muted: #526070;
                --tw-line: #dfe7ef;
            }
            * { box-sizing: border-box; }
            body {
                background: #eef3f6;
                color: #1a2535;
                font-family: Arial, sans-serif;
                line-height: 1.62;
                margin: 0;
            }
            .tw-pdf-toolbar {
                align-items: center;
                background: #0d1526;
                color: #fff;
                display: flex;
                gap: 10px;
                justify-content: center;
                padding: 14px;
                position: sticky;
                top: 0;
                z-index: 5;
            }
            .tw-pdf-toolbar a,
            .tw-pdf-toolbar button {
                background: #D83550;
                border: 0;
                border-radius: 999px;
                color: #0d1526;
                cursor: pointer;
                font: inherit;
                font-weight: 800;
                padding: 10px 18px;
                text-decoration: none;
            }
            .tw-pdf-toolbar a.secondary {
                background: transparent;
                border: 1px solid rgba(255,255,255,0.35);
                color: #fff;
            }
            .tw-pdf-page {
                background: #fff;
                box-shadow: 0 20px 70px rgba(13,21,38,0.12);
                margin: 28px auto;
                max-width: 860px;
                min-height: 1120px;
                padding: 46px;
            }
            .tw-pdf-brand {
                align-items: center;
                border-bottom: 2px solid var(--tw-line);
                display: flex;
                justify-content: space-between;
                margin-bottom: 26px;
                padding-bottom: 16px;
            }
            .tw-pdf-logo {
                display: block;
                height: auto;
                max-height: 58px;
                max-width: 220px;
                object-fit: contain;
                width: auto;
            }
            .tw-pdf-brand strong {
                color: var(--tw-navy);
                display: block;
                font-size: 1.25rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .tw-pdf-brand span {
                color: var(--tw-muted);
                font-size: 0.86rem;
            }
            .tw-pdf-kicker {
                color: var(--tw-blue);
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                margin-bottom: 8px;
                text-transform: uppercase;
            }
            h1 {
                color: var(--tw-navy);
                font-size: 2.2rem;
                line-height: 1.08;
                margin: 0 0 18px;
            }
            h2 {
                border-bottom: 1px solid var(--tw-line);
                color: var(--tw-navy);
                font-size: 1.2rem;
                margin: 32px 0 14px;
                padding-bottom: 8px;
            }
            h3 {
                color: var(--tw-navy);
                font-size: 1rem;
                margin: 0 0 6px;
            }
            .tw-pdf-hero {
                border-radius: 12px;
                display: block;
                height: 260px;
                margin: 22px 0;
                object-fit: cover;
                width: 100%;
            }
            .tw-pdf-facts {
                display: grid;
                gap: 10px;
                grid-template-columns: repeat(3, 1fr);
                margin: 22px 0;
            }
            .tw-pdf-fact {
                border: 1px solid var(--tw-line);
                border-radius: 8px;
                padding: 12px;
            }
            .tw-pdf-fact span {
                color: var(--tw-muted);
                display: block;
                font-size: 0.78rem;
                font-weight: 700;
                margin-bottom: 4px;
                text-transform: uppercase;
            }
            .tw-pdf-fact strong {
                color: var(--tw-navy);
                font-size: 0.98rem;
            }
            .tw-pdf-content,
            .tw-pdf-day,
            .tw-pdf-faq {
                color: #2d3b4d;
                font-size: 0.96rem;
            }
            .tw-pdf-content p,
            .tw-pdf-content ul,
            .tw-pdf-content ol {
                margin-bottom: 12px;
            }
            .tw-pdf-day,
            .tw-pdf-faq {
                border-left: 3px solid var(--tw-blue);
                margin-bottom: 14px;
                padding: 2px 0 2px 16px;
            }
            .tw-pdf-footer {
                border-top: 2px solid var(--tw-line);
                color: var(--tw-muted);
                display: flex;
                font-size: 0.86rem;
                justify-content: space-between;
                margin-top: 36px;
                padding-top: 14px;
            }
            @page { margin: 14mm; size: A4; }
            @media print {
                body { background: #fff; }
                .tw-pdf-toolbar { display: none; }
                .tw-pdf-page {
                    box-shadow: none;
                    margin: 0;
                    max-width: none;
                    min-height: 0;
                    padding: 0;
                }
                .tw-pdf-hero { height: 210px; }
                .tw-pdf-day,
                .tw-pdf-faq,
                .tw-pdf-fact { break-inside: avoid; }
            }
            @media (max-width: 760px) {
                .tw-pdf-page { margin: 0; padding: 24px; }
                .tw-pdf-toolbar { flex-wrap: wrap; position: static; }
                .tw-pdf-facts { grid-template-columns: 1fr; }
                .tw-pdf-brand { align-items: flex-start; flex-direction: column; gap: 8px; }
                h1 { font-size: 1.7rem; }
            }
        </style>
    </head>
    <body>
        <div class="tw-pdf-toolbar">
            <button type="button" onclick="window.print()"><?php esc_html_e('Download / Print PDF', 'mytheme'); ?></button>
            <a href="<?php echo esc_url('https://wa.me/?text=' . $share_text); ?>" target="_blank" rel="noopener"><?php esc_html_e('Share on WhatsApp', 'mytheme'); ?></a>
            <a class="secondary" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php esc_html_e('Back to trip', 'mytheme'); ?></a>
        </div>

        <main class="tw-pdf-page">
            <header class="tw-pdf-brand">
                <div>
                    <img class="tw-pdf-logo" src="<?php echo esc_url($pdf_logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <span><?php bloginfo('description'); ?></span>
                </div>
                <span><?php echo esc_html(home_url('/')); ?></span>
            </header>

            <div class="tw-pdf-kicker"><?php echo esc_html($is_package ? __('Travel Package', 'mytheme') : __('Itinerary', 'mytheme')); ?></div>
            <h1><?php echo esc_html($title); ?></h1>

            <?php if ($image_url) : ?>
                <img class="tw-pdf-hero" src="<?php echo esc_url($image_url); ?>" alt="">
            <?php endif; ?>

            <section class="tw-pdf-facts">
                <?php foreach ($facts as $label => $value) : ?>
                    <?php mytheme_render_trip_pdf_fact($label, $value); ?>
                <?php endforeach; ?>
            </section>

            <?php if ($main_content) : ?>
                <section>
                    <h2><?php echo esc_html($is_package ? __('Package Overview', 'mytheme') : __('Route Summary', 'mytheme')); ?></h2>
                    <div class="tw-pdf-content">
                        <?php echo wp_kses_post($main_content); ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!$is_package && function_exists('have_rows') && have_rows('itinerary_days', $post_id)) : ?>
                <section>
                    <h2><?php esc_html_e('Day Wise Plan', 'mytheme'); ?></h2>
                    <?php while (have_rows('itinerary_days', $post_id)) : the_row(); ?>
                        <article class="tw-pdf-day">
                            <h3><?php echo esc_html(get_sub_field('day_title')); ?></h3>
                            <div><?php echo wp_kses_post(wpautop(get_sub_field('day_details'))); ?></div>
                        </article>
                    <?php endwhile; ?>
                </section>
            <?php endif; ?>

            <?php if (function_exists('have_rows') && have_rows('faqs', $post_id)) : ?>
                <section>
                    <h2><?php esc_html_e('FAQs', 'mytheme'); ?></h2>
                    <?php while (have_rows('faqs', $post_id)) : the_row(); ?>
                        <?php
                        $question = get_sub_field('faq_question');
                        $answer = get_sub_field('faq_answer');
                        ?>
                        <article class="tw-pdf-faq">
                            <h3><?php echo esc_html($question); ?></h3>
                            <div><?php echo wp_kses_post(wpautop($answer)); ?></div>
                        </article>
                    <?php endwhile; ?>
                </section>
            <?php endif; ?>

            <footer class="tw-pdf-footer">
                <span><?php esc_html_e('Planned by', 'mytheme'); ?> <?php bloginfo('name'); ?></span>
                <span><?php echo esc_html(get_permalink($post_id)); ?></span>
            </footer>
        </main>
    </body>
    </html>
    <?php
}

function mytheme_maybe_render_trip_pdf() {
    if (!is_singular(array('itinerary', 'travel_package')) || !isset($_GET['trip_pdf'])) {
        return;
    }

    mytheme_render_trip_pdf_document(get_queried_object_id());
    exit;
}
add_action('template_redirect', 'mytheme_maybe_render_trip_pdf');

function mytheme_package_card($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $data = mytheme_get_package_data($post_id);
    $image_url = mytheme_get_image_url($data['image'], 'large');
    // Fallback: if ACF image field is empty, use the featured thumbnail URL
    if ( ! $image_url && has_post_thumbnail( $post_id ) ) {
        $image_url = get_the_post_thumbnail_url( $post_id, 'large' );
    }
    $book_url = get_permalink($post_id) . '#package-enquiry';
    ?>
    <article class="post-card package-card" data-package-card-id="<?php echo esc_attr($post_id); ?>">
        <div class="package-media">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" loading="lazy">
                <?php endif; ?>
            </a>
            <?php if ($data['tag']) : ?>
                <span class="package-tag"><?php echo esc_html($data['tag']); ?></span>
            <?php endif; ?>
            <label class="package-compare-check">
                <input type="checkbox" data-package-compare-id="<?php echo esc_attr($post_id); ?>" data-package-compare-title="<?php echo esc_attr(get_the_title($post_id)); ?>">
                <span><?php esc_html_e('Compare', 'mytheme'); ?></span>
            </label>
        </div>
        <div class="package-content">
            <?php if ($data['location']) : ?>
                <span class="package-location"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($data['location']); ?></span>
            <?php endif; ?>
            <h3><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a></h3>
            <div class="package-facts">
                <?php if ($data['duration']) : ?><span><i class="fa-regular fa-clock"></i> <?php echo esc_html($data['duration']); ?></span><?php endif; ?>
                <?php if ($data['trip_type']) : ?><span><i class="fa-solid fa-user-group"></i> <?php echo esc_html($data['trip_type']); ?></span><?php endif; ?>
            </div>
            <div class="package-price-row">
                <div>
                    <?php if ($data['amount']) : ?><strong><?php echo esc_html($data['amount']); ?></strong><?php endif; ?>
                </div>
                <a href="<?php echo esc_url($book_url); ?>" class="btn-primary btn-sm">Book Now</a>
            </div>
        </div>
    </article>
    <?php
}

function mytheme_get_destination_data($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();

    return array(
        'image' => mytheme_get_travel_field('destination_image', $post_id),
        'country' => mytheme_get_travel_field('destination_country', $post_id),
        'best_time' => mytheme_get_travel_field('destination_best_time', $post_id),
        'ideal_duration' => mytheme_get_travel_field('destination_ideal_duration', $post_id),
        'starting_price' => mytheme_format_rupee_amount(mytheme_get_travel_field('destination_starting_price', $post_id)),
        'short_intro' => mytheme_get_travel_field('destination_short_intro', $post_id),
        'overview' => mytheme_get_travel_field('destination_overview', $post_id),
    );
}

/**
 * Icon shown next to each destination guide section heading, keyed by the
 * section's ACF field name (see mytheme_get_destination_guide_sections()).
 */
function mytheme_destination_guide_icon( $field ) {
    $icons = array(
        'destination_best_time'     => 'fa-solid fa-sun',
        'destination_things_to_do'  => 'fa-solid fa-compass',
        'destination_food'          => 'fa-solid fa-utensils',
        'destination_visa_info'     => 'fa-solid fa-passport',
        'destination_budget'        => 'fa-solid fa-wallet',
        'destination_how_to_reach'  => 'fa-solid fa-plane',
        'destination_travel_tips'   => 'fa-solid fa-lightbulb',
    );
    return isset( $icons[ $field ] ) ? $icons[ $field ] : 'fa-solid fa-circle-info';
}

/**
 * Destination guide content is free-text entered in the CMS, often as one
 * fact per line (with or without a leading "*"/"-" bullet) rather than real
 * paragraphs. Rendering it through a bare wpautop() just strings those
 * lines into one run-on paragraph with literal asterisks, which is the
 * "plain" look this is fixing. Detect the shape of the content instead:
 *   - "Label: value" lines (e.g. Budget tiers)      -> a small stat grid
 *   - bulleted or many short lines (e.g. Things to Do, Food, Travel Tips)
 *                                                    -> a real <ul>
 *   - otherwise (prose, e.g. Visa Info, How to Reach) -> normal wpautop()
 */
function mytheme_format_guide_content( $text ) {
    $text = trim( (string) $text );
    if ( '' === $text ) {
        return '';
    }

    $lines = preg_split( '/\r\n|\r|\n/', $text );
    $lines = array_values( array_filter( array_map( 'trim', $lines ), function ( $line ) {
        return $line !== '';
    } ) );

    if ( count( $lines ) < 2 ) {
        return wp_kses_post( wpautop( $text ) );
    }

    $stripped = array_map( function ( $line ) {
        return preg_replace( '/^[\*\-•]\s*/', '', $line );
    }, $lines );

    $is_stat_list = true;
    foreach ( $stripped as $line ) {
        if ( ! preg_match( '/^[^:]{2,40}:\s*\S.*/', $line ) ) {
            $is_stat_list = false;
            break;
        }
    }
    if ( $is_stat_list ) {
        $html = '<div class="dest-guide-stats">';
        foreach ( $stripped as $line ) {
            $parts = explode( ':', $line, 2 );
            $html .= '<div class="dest-guide-stat"><span class="dest-guide-stat-k">' . esc_html( trim( $parts[0] ) ) . '</span><span class="dest-guide-stat-v">' . esc_html( trim( $parts[1] ) ) . '</span></div>';
        }
        $html .= '</div>';
        return $html;
    }

    $had_markers = ( $stripped !== $lines );
    $avg_len     = array_sum( array_map( 'strlen', $stripped ) ) / count( $stripped );
    if ( $had_markers || ( count( $stripped ) >= 3 && $avg_len < 70 ) ) {
        $html = '<ul class="dest-guide-list">';
        foreach ( $stripped as $line ) {
            $html .= '<li>' . wp_kses_post( $line ) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    return wp_kses_post( wpautop( $text ) );
}

function mytheme_get_destination_guide_sections($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();

    return array(
        array(
            'label' => __('Best Time to Visit', 'mytheme'),
            'field' => 'destination_best_time',
            'content' => mytheme_get_travel_field('destination_best_time', $post_id),
        ),
        array(
            'label' => __('Things to Do', 'mytheme'),
            'field' => 'destination_things_to_do',
            'content' => mytheme_get_travel_field('destination_things_to_do', $post_id),
        ),
        array(
            'label' => __('Food', 'mytheme'),
            'field' => 'destination_food',
            'content' => mytheme_get_travel_field('destination_food', $post_id),
        ),
        array(
            'label' => __('Visa Info', 'mytheme'),
            'field' => 'destination_visa_info',
            'content' => mytheme_get_travel_field('destination_visa_info', $post_id),
        ),
        array(
            'label' => __('Budget', 'mytheme'),
            'field' => 'destination_budget',
            'content' => mytheme_get_travel_field('destination_budget', $post_id),
        ),
        array(
            'label' => __('How to Reach', 'mytheme'),
            'field' => 'destination_how_to_reach',
            'content' => mytheme_get_travel_field('destination_how_to_reach', $post_id),
        ),
        array(
            'label' => __('Travel Tips', 'mytheme'),
            'field' => 'destination_travel_tips',
            'content' => mytheme_get_travel_field('destination_travel_tips', $post_id),
        ),
    );
}

function mytheme_render_destination_guide($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $sections = array_values(array_filter(mytheme_get_destination_guide_sections($post_id), function ($section) {
        return !empty($section['content']);
    }));

    if (empty($sections)) {
        return;
    }
    ?>
    <section class="destination-guide-section">
        <div class="destination-guide-heading">
            <span><?php esc_html_e('Travel Guide', 'mytheme'); ?></span>
            <h2><?php esc_html_e('Plan this destination better', 'mytheme'); ?></h2>
        </div>
        <div class="destination-guide-layout">
            <nav class="destination-guide-toc" aria-label="<?php esc_attr_e('Destination guide sections', 'mytheme'); ?>">
                <ol>
                    <?php foreach ($sections as $index => $section) : ?>
                        <?php $section_id = 'destination-guide-' . sanitize_html_class($section['field']) . '-' . ($index + 1); ?>
                        <li>
                            <a href="#<?php echo esc_attr($section_id); ?>">
                                <span><?php echo esc_html($index + 1); ?></span>
                                <?php echo esc_html($section['label']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <div class="destination-guide-content-list">
                <?php foreach ($sections as $index => $section) : ?>
                    <?php $section_id = 'destination-guide-' . sanitize_html_class($section['field']) . '-' . ($index + 1); ?>
                    <article class="destination-guide-card" id="<?php echo esc_attr($section_id); ?>">
                        <h3><?php echo esc_html($section['label']); ?></h3>
                        <div class="destination-guide-content">
                            <?php echo wp_kses_post(wpautop($section['content'])); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}

function mytheme_render_faq_section($post_id = null, $heading = 'Frequently Asked Questions') {
    $post_id = $post_id ? $post_id : get_the_ID();

    if (!function_exists('have_rows') || !have_rows('faqs', $post_id)) {
        return;
    }
    ?>
    <section class="tw-faq-section">
        <h2><?php echo esc_html($heading); ?></h2>
        <div class="tw-faq-list">
            <?php while (have_rows('faqs', $post_id)) : the_row(); ?>
                <?php
                $question = get_sub_field('faq_question');
                $answer = get_sub_field('faq_answer');
                if (!$question || !$answer) {
                    continue;
                }
                ?>
                <details class="tw-faq-item">
                    <summary>
                        <span class="tw-faq-question"><?php echo esc_html($question); ?></span>
                        <i class="fi-rr-angle-small-right tw-faq-toggle" aria-hidden="true"></i>
                    </summary>
                    <div class="tw-faq-answer">
                        <?php echo wp_kses_post(wpautop($answer)); ?>
                    </div>
                </details>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
}

// ============================================================
// ENQUIRIES — package / itinerary / destination "Book Now" and
// "Enquire" forms all save here, in a dedicated table separate
// from the trip_inquiry CPT used by the Plan A Trip flow.
// ============================================================
function mytheme_enquiries_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'tw_enquiries';
}

function mytheme_maybe_create_enquiries_table() {
    $installed_version = get_option('tw_enquiries_table_version');
    if ($installed_version === '1.0') {
        return;
    }
    global $wpdb;
    $table_name      = mytheme_enquiries_table_name();
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        enquiry_type VARCHAR(20) NOT NULL DEFAULT 'package',
        ref_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
        ref_title VARCHAR(255) NOT NULL DEFAULT '',
        ref_url VARCHAR(500) NOT NULL DEFAULT '',
        name VARCHAR(150) NOT NULL DEFAULT '',
        phone VARCHAR(40) NOT NULL DEFAULT '',
        email VARCHAR(150) NOT NULL DEFAULT '',
        travel_date VARCHAR(40) NOT NULL DEFAULT '',
        adults SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        budget VARCHAR(100) NOT NULL DEFAULT '',
        message TEXT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'new',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY enquiry_type (enquiry_type),
        KEY ref_id (ref_id)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('tw_enquiries_table_version', '1.0');
}
add_action('init', 'mytheme_maybe_create_enquiries_table');

/**
 * Insert an enquiry row and email 1tripwiser@gmail.com. Shared by the
 * package, itinerary and destination "Book Now" / "Enquire" forms.
 */
function mytheme_save_enquiry($enquiry_type, $ref_id, $ref_title, $ref_url, $fields) {
    global $wpdb;

    $data = wp_parse_args($fields, array(
        'name'        => '',
        'phone'       => '',
        'email'       => '',
        'travel_date' => '',
        'adults'      => 1,
        'budget'      => '',
        'message'     => '',
    ));

    $wpdb->insert(
        mytheme_enquiries_table_name(),
        array(
            'enquiry_type' => $enquiry_type,
            'ref_id'       => $ref_id,
            'ref_title'    => $ref_title,
            'ref_url'      => $ref_url,
            'name'         => $data['name'],
            'phone'        => $data['phone'],
            'email'        => $data['email'],
            'travel_date'  => $data['travel_date'],
            'adults'       => max(1, (int) $data['adults']),
            'budget'       => $data['budget'],
            'message'      => $data['message'],
            'status'       => 'new',
            'created_at'   => current_time('mysql'),
        ),
        array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
    );
    $enquiry_id = $wpdb->insert_id;

    $type_labels = array(
        'package'     => 'Package',
        'itinerary'   => 'Itinerary',
        'destination' => 'Destination',
        'group_trip'  => 'Group Trip',
        'corporate_trip' => 'Corporate Trip',
        'event'       => 'Event',
        'popup'       => 'Website Popup',
    );
    $type_label = isset($type_labels[$enquiry_type]) ? $type_labels[$enquiry_type] : ucfirst($enquiry_type);

    $subject = sprintf('New %s Enquiry — %s', $type_label, $ref_title ?: $data['name']);
    $body_lines = array(
        "A new {$type_label} enquiry was submitted on 1TripWiser.",
        '',
        'Name: ' . $data['name'],
        'Phone: ' . $data['phone'],
        'Email: ' . $data['email'],
        $ref_title ? ($type_label . ': ' . $ref_title) : '',
        $ref_url ? 'Link: ' . $ref_url : '',
        $data['travel_date'] ? 'Preferred Travel Date: ' . $data['travel_date'] : '',
        'Adults: ' . max(1, (int) $data['adults']),
        $data['budget'] ? 'Budget: ' . $data['budget'] : '',
        $data['message'] ? "Message:\n" . $data['message'] : '',
    );
    $body = implode("\n", array_filter($body_lines, function ($line) { return $line !== ''; }));

    wp_mail('1tripwiser@gmail.com', $subject, $body, array(
        $data['email'] ? ('Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>') : '',
    ));

    return $enquiry_id;
}

function mytheme_handle_enquiry_submission() {
    $nonce_ok = isset($_POST['mytheme_enquiry_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mytheme_enquiry_nonce'])), 'mytheme_enquiry')
        || isset($_POST['mytheme_package_inquiry_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mytheme_package_inquiry_nonce'])), 'mytheme_package_inquiry');
    if (!$nonce_ok) {
        wp_die(esc_html__('Security check failed.', 'mytheme'));
    }

    $enquiry_type = isset($_POST['enquiry_type']) ? sanitize_key(wp_unslash($_POST['enquiry_type'])) : 'package';
    if (!in_array($enquiry_type, array('package', 'itinerary', 'destination', 'group_trip', 'corporate_trip', 'event'), true)) {
        $enquiry_type = 'package';
    }
    $ref_id = isset($_POST['package_id']) ? absint($_POST['package_id']) : (isset($_POST['ref_id']) ? absint($_POST['ref_id']) : 0);
    $name        = isset($_POST['name'])    ? sanitize_text_field(wp_unslash($_POST['name']))    : '';
    $phone       = isset($_POST['phone'])   ? sanitize_text_field(wp_unslash($_POST['phone']))   : '';
    $email       = isset($_POST['email'])   ? sanitize_email(wp_unslash($_POST['email']))        : '';
    $travel_date = isset($_POST['date'])    ? sanitize_text_field(wp_unslash($_POST['date']))    : '';
    $adults      = isset($_POST['adults'])  ? max(1, absint($_POST['adults']))                   : 1;
    $budget      = isset($_POST['budget'])  ? sanitize_text_field(wp_unslash($_POST['budget']))  : '';
    $message     = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $redirect    = isset($_POST['_wp_http_referer']) ? esc_url_raw(wp_unslash($_POST['_wp_http_referer'])) : home_url('/');
    $anchor      = isset($_POST['enquiry_anchor']) ? sanitize_key(wp_unslash($_POST['enquiry_anchor'])) : '';

    // A ref_id of 0 is a generic "interest" enquiry not tied to a specific
    // post (e.g. the LUXE collection page when no packages are published
    // yet) — allowed as long as one is submitted with a real name/phone.
    $post_type_map = array('package' => 'travel_package', 'itinerary' => 'itinerary', 'destination' => 'destination', 'group_trip' => 'group_trip', 'corporate_trip' => 'corporate_trip', 'event' => 'tw_event');
    $expected_post_type = $post_type_map[$enquiry_type];
    $valid_ref = !$ref_id || in_array(get_post_type($ref_id), array($expected_post_type, 'tw_luxe'), true);

    if (!$valid_ref || empty($name) || empty($phone)) {
        $redirect = add_query_arg('enquiry', 'error', $redirect);
        wp_safe_redirect($anchor ? $redirect . '#' . $anchor : $redirect);
        exit;
    }

    $ref_title = $ref_id ? get_the_title($ref_id) : '';
    $ref_url   = $ref_id ? get_permalink($ref_id) : '';

    mytheme_save_enquiry($enquiry_type, $ref_id, $ref_title, $ref_url, array(
        'name'        => $name,
        'phone'       => $phone,
        'email'       => $email,
        'travel_date' => $travel_date,
        'adults'      => $adults,
        'budget'      => $budget,
        'message'     => $message,
    ));

    $redirect = add_query_arg('enquiry', 'success', $redirect);
    wp_safe_redirect($anchor ? $redirect . '#' . $anchor : $redirect);
    exit;
}
add_action('admin_post_mytheme_enquiry', 'mytheme_handle_enquiry_submission');
add_action('admin_post_nopriv_mytheme_enquiry', 'mytheme_handle_enquiry_submission');
// Legacy action name — the package enquiry form on package/LUXE pages still posts here.
add_action('admin_post_mytheme_package_inquiry', 'mytheme_handle_enquiry_submission');
add_action('admin_post_nopriv_mytheme_package_inquiry', 'mytheme_handle_enquiry_submission');

// ============================================================
// SITE-WIDE LEAD POPUP + NEWSLETTER — a popup shown a few seconds
// after landing on any page. Step 1 (name/phone/email) reuses the
// enquiries table above; step 2 (email only) saves to its own
// newsletter table, kept separate as requested.
// ============================================================
function mytheme_newsletter_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'tw_newsletter';
}

function mytheme_maybe_create_newsletter_table() {
    $installed_version = get_option('tw_newsletter_table_version');
    if ($installed_version === '1.0') {
        return;
    }
    global $wpdb;
    $table_name      = mytheme_newsletter_table_name();
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        email VARCHAR(150) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('tw_newsletter_table_version', '1.0');
}
add_action('init', 'mytheme_maybe_create_newsletter_table');

function mytheme_save_newsletter_signup($email) {
    global $wpdb;
    $wpdb->insert(
        mytheme_newsletter_table_name(),
        array(
            'email'      => $email,
            'created_at' => current_time('mysql'),
        ),
        array('%s', '%s')
    );
    // A duplicate email hits the UNIQUE KEY and $wpdb->insert() simply
    // returns false — already-subscribed is treated the same as success.
    return true;
}

function mytheme_handle_lead_popup_submit() {
    check_ajax_referer('tw_lead_popup', 'nonce');

    $name  = isset($_POST['name'])  ? sanitize_text_field(wp_unslash($_POST['name']))  : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email']))      : '';

    if (empty($name) || empty($phone)) {
        wp_send_json_error(array('message' => __('Please fill your name and phone number.', 'mytheme')));
    }

    mytheme_save_enquiry('popup', 0, '', wp_get_referer() ?: home_url('/'), array(
        'name'  => $name,
        'phone' => $phone,
        'email' => $email,
    ));

    wp_send_json_success(array('message' => __('Thanks! Our team will reach out shortly.', 'mytheme')));
}
add_action('wp_ajax_mytheme_lead_popup_submit', 'mytheme_handle_lead_popup_submit');
add_action('wp_ajax_nopriv_mytheme_lead_popup_submit', 'mytheme_handle_lead_popup_submit');

function mytheme_handle_newsletter_signup() {
    check_ajax_referer('tw_lead_popup', 'nonce');

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array('message' => __('Please enter a valid email address.', 'mytheme')));
    }

    mytheme_save_newsletter_signup($email);
    wp_send_json_success(array('message' => __('Subscribed! Watch your inbox for travel deals.', 'mytheme')));
}
add_action('wp_ajax_mytheme_newsletter_signup', 'mytheme_handle_newsletter_signup');
add_action('wp_ajax_nopriv_mytheme_newsletter_signup', 'mytheme_handle_newsletter_signup');

/**
 * Reusable right-rail "Enquire Now" card — opens the site-wide lead popup
 * (tw_lead_popup_widget(), below) instead of embedding its own form, so
 * every page that doesn't already have a dedicated contextual enquiry form
 * still gets a CTA. Wrap in a <div class="dest-booking"> (or reuse an
 * existing one) to get the sticky-rail treatment already used on blog posts.
 */
function mytheme_render_enquiry_popup_card( $text = '' ) {
    $text = $text ?: __( 'Planning your next trip? Talk to our travel experts and get a personalized itinerary.', 'mytheme' );
    ?>
    <div class="dest-rail-card sp-blog-enquiry-rail">
        <p><?php echo esc_html( $text ); ?></p>
        <button type="button" class="btn-primary btn-block" onclick="window.twOpenLeadPopup && window.twOpenLeadPopup()"><?php esc_html_e( 'Enquire Now', 'mytheme' ); ?></button>
    </div>
    <?php
}

/**
 * Site-wide popup — name/phone/email, then an email-only newsletter step.
 * Auto-opens once per session a few seconds after landing; can also be
 * opened on demand via window.twOpenLeadPopup() (used by the blog
 * sidebar's "Enquire Now" card).
 */
function tw_lead_popup_widget() {
    $nonce = wp_create_nonce('tw_lead_popup');
    ?>
    <div class="tw-lead-popup-overlay" id="tw-lead-popup-overlay" hidden>
        <div class="tw-lead-popup" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Plan your trip', 'mytheme'); ?>">
            <button type="button" class="tw-lead-popup-close" id="tw-lead-popup-close" aria-label="<?php esc_attr_e('Close', 'mytheme'); ?>">&times;</button>

            <div class="tw-lead-popup-step" id="tw-lead-popup-step-contact">
                <h3><?php esc_html_e('Planning a trip?', 'mytheme'); ?></h3>
                <p><?php esc_html_e('Share your details and our travel expert will get in touch.', 'mytheme'); ?></p>
                <div class="tw-form-notice error" id="tw-lead-popup-contact-error" hidden></div>
                <form class="tw-form-grid" id="tw-lead-popup-contact-form">
                    <label><span><?php esc_html_e('Name *', 'mytheme'); ?></span><input type="text" name="name" required></label>
                    <label><span><?php esc_html_e('Phone *', 'mytheme'); ?></span><input type="tel" name="phone" required></label>
                    <label class="tw-form-full"><span><?php esc_html_e('Email', 'mytheme'); ?></span><input type="email" name="email"></label>
                    <button type="submit" class="btn-primary tw-form-full"><?php esc_html_e('Send Enquiry', 'mytheme'); ?></button>
                </form>
            </div>

            <div class="tw-lead-popup-step" id="tw-lead-popup-step-newsletter" hidden>
                <h3><?php esc_html_e('Before you go…', 'mytheme'); ?></h3>
                <p><?php esc_html_e('Get travel deals and updates from 1TripWiser in your inbox.', 'mytheme'); ?></p>
                <div class="tw-form-notice error" id="tw-lead-popup-newsletter-error" hidden></div>
                <form class="tw-form-grid" id="tw-lead-popup-newsletter-form">
                    <label class="tw-form-full"><span><?php esc_html_e('Email', 'mytheme'); ?></span><input type="email" name="email" required></label>
                    <button type="submit" class="btn-primary tw-form-full"><?php esc_html_e('Subscribe', 'mytheme'); ?></button>
                </form>
                <button type="button" class="tw-lead-popup-skip" id="tw-lead-popup-skip"><?php esc_html_e('No thanks', 'mytheme'); ?></button>
            </div>
        </div>
    </div>
    <script>
    (function () {
        var overlay   = document.getElementById('tw-lead-popup-overlay');
        var closeBtn  = document.getElementById('tw-lead-popup-close');
        var stepC     = document.getElementById('tw-lead-popup-step-contact');
        var stepN     = document.getElementById('tw-lead-popup-step-newsletter');
        var formC     = document.getElementById('tw-lead-popup-contact-form');
        var formN     = document.getElementById('tw-lead-popup-newsletter-form');
        var errC      = document.getElementById('tw-lead-popup-contact-error');
        var errN      = document.getElementById('tw-lead-popup-newsletter-error');
        var skipBtn   = document.getElementById('tw-lead-popup-skip');
        var nonce     = '<?php echo esc_js($nonce); ?>';
        var ajaxUrl   = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        if (!overlay) return;

        function openPopup() {
            stepC.hidden = false;
            stepN.hidden = true;
            overlay.hidden = false;
            document.body.style.overflow = 'hidden';
        }
        function closePopup() {
            overlay.hidden = true;
            document.body.style.overflow = '';
        }
        function showNewsletterStep() {
            stepC.hidden = true;
            stepN.hidden = false;
        }

        window.twOpenLeadPopup = openPopup;

        closeBtn.addEventListener('click', showNewsletterStep);
        skipBtn.addEventListener('click', closePopup);
        overlay.addEventListener('click', function (e) { if (e.target === overlay) closePopup(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !overlay.hidden) closePopup();
        });

        formC.addEventListener('submit', function (e) {
            e.preventDefault();
            errC.hidden = true;
            var data = new FormData(formC);
            data.append('action', 'mytheme_lead_popup_submit');
            data.append('nonce', nonce);
            fetch(ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        showNewsletterStep();
                    } else {
                        errC.textContent = (res.data && res.data.message) || '<?php echo esc_js(__('Something went wrong. Please try again.', 'mytheme')); ?>';
                        errC.hidden = false;
                    }
                })
                .catch(function () {
                    errC.textContent = '<?php echo esc_js(__('Something went wrong. Please try again.', 'mytheme')); ?>';
                    errC.hidden = false;
                });
        });

        formN.addEventListener('submit', function (e) {
            e.preventDefault();
            errN.hidden = true;
            var data = new FormData(formN);
            data.append('action', 'mytheme_newsletter_signup');
            data.append('nonce', nonce);
            fetch(ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        closePopup();
                    } else {
                        errN.textContent = (res.data && res.data.message) || '<?php echo esc_js(__('Something went wrong. Please try again.', 'mytheme')); ?>';
                        errN.hidden = false;
                    }
                })
                .catch(function () {
                    errN.textContent = '<?php echo esc_js(__('Something went wrong. Please try again.', 'mytheme')); ?>';
                    errN.hidden = false;
                });
        });

        // Auto-open once per session, 6s after landing.
        if (!sessionStorage.getItem('tw_lead_popup_shown')) {
            setTimeout(function () {
                openPopup();
                sessionStorage.setItem('tw_lead_popup_shown', '1');
            }, 6000);
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'tw_lead_popup_widget');

// Every wp_mail() the theme sends (enquiries, Plan A Trip, blog
// submissions, agency registration) was showing up as "WordPress".
add_filter('wp_mail_from_name', function () {
    return '1tripwiser Enquiry - Plan a Trip';
});

function mytheme_travel_filter_options($post_type) {
    if ($post_type === 'travel_package') {
        return array(
            'all' => __('All', 'mytheme'),
            'india' => __('India', 'mytheme'),
            'international' => __('International', 'mytheme'),
            'asia' => __('Asia', 'mytheme'),
            'europe' => __('Europe', 'mytheme'),
            'africa' => __('Africa', 'mytheme'),
            'north-america' => __('North America', 'mytheme'),
            'south-america' => __('South America', 'mytheme'),
            'oceania' => __('Oceania', 'mytheme'),
            'budget-under-30k' => __('Budget < 30K', 'mytheme'),
            'bestseller' => __('Bestseller', 'mytheme'),
            'trending' => __('Trending', 'mytheme'),
            'new' => __('New', 'mytheme'),
        );
    }

    if ($post_type === 'itinerary') {
        return array(
            'all' => __('All', 'mytheme'),
            'india' => __('India', 'mytheme'),
            'asia' => __('Asia', 'mytheme'),
            'europe' => __('Europe', 'mytheme'),
            'africa' => __('Africa', 'mytheme'),
            'north-america' => __('North America', 'mytheme'),
            'south-america' => __('South America', 'mytheme'),
            'oceania' => __('Oceania', 'mytheme'),
            'international' => __('International', 'mytheme'),
            'asia' => __('Asia', 'mytheme'),
            'europe' => __('Europe', 'mytheme'),
            'africa' => __('Africa', 'mytheme'),
            'north-america' => __('North America', 'mytheme'),
            'south-america' => __('South America', 'mytheme'),
            'oceania' => __('Oceania', 'mytheme'),
            'budget-under-30k' => __('Budget < 30K', 'mytheme'),
        );
    }

    return array(
        'all' => __('All', 'mytheme'),
        'india' => __('India', 'mytheme'),
        'international' => __('International', 'mytheme'),
    );
}

function mytheme_package_region_options() {
    return array(
        'india' => __('India', 'mytheme'),
        'international' => __('International', 'mytheme'),
        'asia' => __('Asia', 'mytheme'),
        'europe' => __('Europe', 'mytheme'),
    );
}

function mytheme_itinerary_continent_options() {
    return array(
        'asia' => __('Asia', 'mytheme'),
        'europe' => __('Europe', 'mytheme'),
        'africa' => __('Africa', 'mytheme'),
        'north-america' => __('North America', 'mytheme'),
        'south-america' => __('South America', 'mytheme'),
        'oceania' => __('Oceania', 'mytheme'),
    );
}

function mytheme_package_tag_options() {
    return array(
        'bestseller' => __('Bestseller', 'mytheme'),
        'trending' => __('Trending', 'mytheme'),
        'new' => __('New', 'mytheme'),
        'limited' => __('Limited Seats', 'mytheme'),
        'popular' => __('Popular', 'mytheme'),
    );
}

function mytheme_package_trip_type_options() {
    return array(
        'Group Trip' => __('Group Trip', 'mytheme'),
        'Single Traveller' => __('Single Traveller', 'mytheme'),
        'Private Trip' => __('Private Trip', 'mytheme'),
        'Family Trip' => __('Family Trip', 'mytheme'),
        'Honeymoon' => __('Honeymoon', 'mytheme'),
    );
}

function mytheme_package_month_options() {
    return array(
        'january' => __('January', 'mytheme'),
        'february' => __('February', 'mytheme'),
        'march' => __('March', 'mytheme'),
        'april' => __('April', 'mytheme'),
        'may' => __('May', 'mytheme'),
        'june' => __('June', 'mytheme'),
        'july' => __('July', 'mytheme'),
        'august' => __('August', 'mytheme'),
        'september' => __('September', 'mytheme'),
        'october' => __('October', 'mytheme'),
        'november' => __('November', 'mytheme'),
        'december' => __('December', 'mytheme'),
    );
}

/* ─────────────────────────────────────────────────────────────────────────
 * HOMEPAGE ITINERARY CARD — shared by initial render + AJAX refresh
 * ───────────────────────────────────────────────────────────────────────── */
function tw_homepage_itinerary_card() {
    $duration      = mytheme_get_travel_field('itinerary_duration');
    $best_time     = mytheme_get_travel_field('itinerary_best_time');
    $route_summary = mytheme_get_travel_field('itinerary_route_summary');
    // Also try the unified keys written by tw_demo_full_meta()
    if ( ! $duration )      $duration      = mytheme_get_travel_field('trip_duration');
    if ( ! $best_time )     $best_time     = mytheme_get_travel_field('best_time');
    if ( ! $route_summary ) $route_summary = mytheme_get_travel_field('route_summary');
    $thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
    ?>
    <article class="post-card travel-card tw-itin-row">
        <a class="tw-itin-media" href="<?php the_permalink(); ?>">
            <?php if ( $thumb ) : ?>
                <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
            <?php else : ?>
                <i class="fa-solid fa-route tw-itin-media-ph" aria-hidden="true"></i>
            <?php endif; ?>
        </a>
        <div class="post-content tw-itin-body">
            <h3 class="tw-itin-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="travel-meta tw-itin-meta">
                <?php if ( $duration )  : ?><span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( $duration ); ?></span><?php endif; ?>
                <?php if ( $best_time ) : ?><span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( $best_time ); ?></span><?php endif; ?>
            </div>
            <?php if ( $route_summary ) : ?>
            <p class="tw-itin-route"><?php echo esc_html( wp_strip_all_tags( $route_summary ) ); ?></p>
            <?php endif; ?>
            <div class="itin-actions tw-itin-actions">
                <a href="<?php the_permalink(); ?>" class="btn-secondary btn-sm">Open Itinerary</a>
                <a href="<?php echo esc_url( get_permalink() . '#itinerary-enquiry' ); ?>" class="btn-primary btn-sm">Enquire</a>
            </div>
        </div>
    </article>
    <?php
}

/* ─────────────────────────────────────────────────────────────────────────
 * AJAX FILTER — returns card HTML for the packages / itineraries grid
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'wp_ajax_tw_filter_section',        'tw_ajax_filter_section' );
add_action( 'wp_ajax_nopriv_tw_filter_section', 'tw_ajax_filter_section' );

function tw_ajax_filter_section() {
    check_ajax_referer( 'tw_filter_nonce', 'nonce' );

    $section = sanitize_key( $_POST['section'] ?? '' );
    $filter  = sanitize_key( $_POST['filter']  ?? 'all' );

    ob_start();

    if ( $section === 'packages' ) {
        $args  = array( 'post_type' => 'travel_package', 'posts_per_page' => 3, 'post_status' => 'publish' );
        $args  = array_merge( $args, mytheme_build_travel_filter_query_args( 'travel_package', $filter ) );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) { $query->the_post(); mytheme_package_card(); }
            wp_reset_postdata();
        } else {
            mytheme_render_package_empty_state( home_url( '/#featured-packages' ) );
        }

    } elseif ( $section === 'itineraries' ) {
        $args  = array( 'post_type' => 'itinerary', 'posts_per_page' => 3, 'post_status' => 'publish' );
        $args  = array_merge( $args, mytheme_build_travel_filter_query_args( 'itinerary', $filter ) );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) { $query->the_post(); tw_homepage_itinerary_card(); }
            wp_reset_postdata();
        } else {
            mytheme_render_itinerary_empty_state( home_url( '/#upcoming-trips' ) );
        }
    }

    wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

/**
 * Returns WP_Query args (meta_query or tax_query) for a given filter value.
 * Region filters use the destination_region taxonomy (reliable for both ACF and
 * demo posts). Budget / tag filters use meta_query with OR across all known keys.
 */
function mytheme_build_travel_filter_query_args( $post_type, $filter ) {
    if ( $filter === 'all' ) { return array(); }

    // Region → taxonomy query (works for seeded demo + real posts tagged via region selector)
    $region_terms = array(
        'india'         => 'India',
        'international' => 'International',
        'asia'          => 'Asia',
        'europe'        => 'Europe',
        'africa'        => 'Africa',
        'north-america' => 'North America',
        'south-america' => 'South America',
        'oceania'       => 'Oceania',
    );
    if ( isset( $region_terms[ $filter ] ) ) {
        return array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'destination_region',
                    'field'    => 'name',
                    'terms'    => $region_terms[ $filter ],
                    'operator' => 'IN',
                ),
            ),
        );
    }

    // Budget < 30K — try all price meta keys
    if ( $filter === 'budget-under-30k' ) {
        return array(
            'meta_query' => array(
                'relation' => 'OR',
                array( 'key' => 'package_amount',   'value' => 30000, 'type' => 'NUMERIC', 'compare' => '<=' ),
                array( 'key' => '_starting_price',  'value' => 30000, 'type' => 'NUMERIC', 'compare' => '<=' ),
                array( 'key' => '_itinerary_budget','value' => 30000, 'type' => 'NUMERIC', 'compare' => '<=' ),
            ),
        );
    }

    // Tag filters (packages)
    if ( in_array( $filter, array( 'bestseller', 'trending', 'new', 'limited', 'popular' ), true ) ) {
        return array(
            'meta_query' => array(
                'relation' => 'OR',
                array( 'key' => 'package_tag',  'value' => $filter, 'compare' => '=' ),
                array( 'key' => '_package_tag', 'value' => $filter, 'compare' => '=' ),
            ),
        );
    }

    return array();
}

function mytheme_get_active_travel_filter($param, $post_type) {
    $options = mytheme_travel_filter_options($post_type);
    $filter = isset($_GET[$param]) ? sanitize_key(wp_unslash($_GET[$param])) : 'all';

    return array_key_exists($filter, $options) ? $filter : 'all';
}

function mytheme_build_travel_filter_meta_query($post_type, $filter) {
    if ($filter === 'all') {
        return array();
    }

    if ($post_type === 'travel_package') {
        if (in_array($filter, array('india', 'international', 'asia', 'europe'), true)) {
            return array(
                array(
                    'key' => 'package_region',
                    'value' => '"' . $filter . '"',
                    'compare' => 'LIKE',
                ),
            );
        }

        if ($filter === 'budget-under-30k') {
            return array(
                array(
                    'key' => 'package_amount',
                    'value' => 30000,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                ),
            );
        }

        if (in_array($filter, array('bestseller', 'trending', 'new'), true)) {
            return array(
                array(
                    'key' => 'package_tag',
                    'value' => $filter,
                    'compare' => '=',
                ),
            );
        }
    }

    if ($post_type === 'itinerary') {
        if (in_array($filter, array('india', 'international'), true)) {
            return array(
                array(
                    'key' => 'itinerary_region',
                    'value' => $filter,
                    'compare' => '=',
                ),
            );
        }

        if (array_key_exists($filter, mytheme_itinerary_continent_options())) {
            return array(
                'relation' => 'OR',
                array(
                    'key' => 'itinerary_continent',
                    'value' => $filter,
                    'compare' => '=',
                ),
                array(
                    'key' => 'itinerary_continent',
                    'value' => '"' . $filter . '"',
                    'compare' => 'LIKE',
                ),
            );
        }

        if ($filter === 'budget-under-30k') {
            return array(
                array(
                    'key' => 'itinerary_budget',
                    'value' => 30000,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                ),
            );
        }
    }

    return array();
}

function mytheme_travel_filter_box($post_type, $param, $base_url, $anchor = '') {
    $active = mytheme_get_active_travel_filter($param, $post_type);

    // Single consolidated pill list — no destination/type grouping, no continents.
    $pills = array(
        'all'              => 'All',
        'india'            => 'India',
        'international'    => 'International',
        'bestseller'       => 'Bestseller',
        'trending'         => 'Trending',
        'budget-under-30k' => 'Budget < 30K',
    );

    $build_url = function( $value ) use ( $param, $base_url, $anchor ) {
        $url = $value === 'all'
            ? remove_query_arg( $param, $base_url )
            : add_query_arg( $param, $value, $base_url );
        return esc_url( remove_query_arg( 'paged', $url ) . $anchor );
    };

    $section_map = array( 'package_filter' => 'packages', 'itinerary_filter' => 'itineraries' );
    $section     = isset( $section_map[ $param ] ) ? $section_map[ $param ] : $param;
    ?>
    <div class="tw-filter-bar"
         data-section="<?php echo esc_attr( $section ); ?>"
         data-param="<?php echo esc_attr( $param ); ?>"
         aria-label="<?php esc_attr_e('Travel filters','mytheme'); ?>">

        <div class="tw-filter-row">
            <div class="tw-filter-pills" role="list">
                <?php foreach ( $pills as $val => $label ) :
                    $is_active = ( $active === $val );
                    $extra_cls = $val === 'budget-under-30k' ? ' tw-filter-pill--budget' : ''; ?>
                <a class="tw-filter-pill<?php echo $is_active ? ' active' : ''; echo $extra_cls; ?>"
                   href="<?php echo $build_url( $val ); ?>" role="listitem">
                    <?php echo esc_html( $label ); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <?php
}

function mytheme_render_package_empty_state($reset_url = '', $message = '') {
    $archive_url = get_post_type_archive_link('travel_package');
    $reset_url = $reset_url ? $reset_url : $archive_url;
    $message = $message ? $message : __('Try a wider filter, explore popular routes, or tell us your dream trip and we will plan it for you.', 'mytheme');

    $suggestions = array(
        array(
            'label' => __('All Packages', 'mytheme'),
            'url' => $reset_url,
        ),
        array(
            'label' => __('India Trips', 'mytheme'),
            'url' => add_query_arg('package_filter', 'india', $archive_url),
        ),
        array(
            'label' => __('International Trips', 'mytheme'),
            'url' => add_query_arg('package_filter', 'international', $archive_url),
        ),
        array(
            'label' => __('Budget < 30K', 'mytheme'),
            'url' => add_query_arg('package_filter', 'budget-under-30k', $archive_url),
        ),
    );
    ?>
    <div class="no-posts travel-empty-state">
        <span><?php esc_html_e('No matching packages', 'mytheme'); ?></span>
        <h2><?php esc_html_e('Let us find a better route for you', 'mytheme'); ?></h2>
        <p><?php echo esc_html($message); ?></p>
        <div class="travel-empty-actions">
            <a class="btn-primary" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>"><?php esc_html_e('Plan a Trip', 'mytheme'); ?></a>
            <a class="btn-secondary" href="<?php echo esc_url($reset_url); ?>"><?php esc_html_e('Reset Filters', 'mytheme'); ?></a>
        </div>
        <div class="travel-empty-suggestions" aria-label="<?php esc_attr_e('Recommended package filters', 'mytheme'); ?>">
            <?php foreach ($suggestions as $suggestion) : ?>
                <a href="<?php echo esc_url($suggestion['url']); ?>"><?php echo esc_html($suggestion['label']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function mytheme_render_itinerary_empty_state($reset_url = '', $message = '') {
    $archive_url = get_post_type_archive_link('itinerary');
    $reset_url = $reset_url ? $reset_url : $archive_url;
    $message = $message ? $message : __('Try a wider filter, browse popular itineraries, or tell us your dream route and we will plan it for you.', 'mytheme');

    $suggestions = array(
        array(
            'label' => __('All Itineraries', 'mytheme'),
            'url' => $reset_url,
        ),
        array(
            'label' => __('India Routes', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'india', $archive_url),
        ),
        array(
            'label' => __('International Routes', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'international', $archive_url),
        ),
                array(
            'label' => __('Asia Routes', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'asia', $archive_url),
        ),
        array(
            'label' => __('Europe Routes', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'europe', $archive_url),
        ),
        array(
            'label' => __('Asia Routes', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'asia', $archive_url),
        ),
        array(
            'label' => __('Europe Routes', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'europe', $archive_url),
        ),
        array(
            'label' => __('Budget < 30K', 'mytheme'),
            'url' => add_query_arg('itinerary_filter', 'budget-under-30k', $archive_url),
        ),
    );
    ?>
    <div class="no-posts travel-empty-state">
        <span><?php esc_html_e('No matching itineraries', 'mytheme'); ?></span>
        <h2><?php esc_html_e('Let us shape a route around you', 'mytheme'); ?></h2>
        <p><?php echo esc_html($message); ?></p>
        <div class="travel-empty-actions">
            <a class="btn-primary" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>"><?php esc_html_e('Plan a Trip', 'mytheme'); ?></a>
            <a class="btn-secondary" href="<?php echo esc_url($reset_url); ?>"><?php esc_html_e('Reset Filters', 'mytheme'); ?></a>
        </div>
        <div class="travel-empty-suggestions" aria-label="<?php esc_attr_e('Recommended itinerary filters', 'mytheme'); ?>">
            <?php foreach ($suggestions as $suggestion) : ?>
                <a href="<?php echo esc_url($suggestion['url']); ?>"><?php echo esc_html($suggestion['label']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function mytheme_apply_travel_archive_filters($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_post_type_archive('travel_package')) {
        $filter = mytheme_get_active_travel_filter('package_filter', 'travel_package');
        $meta_query = mytheme_build_travel_filter_meta_query('travel_package', $filter);

        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }

    if ($query->is_post_type_archive('itinerary')) {
        $filter = mytheme_get_active_travel_filter('itinerary_filter', 'itinerary');
        $meta_query = mytheme_build_travel_filter_meta_query('itinerary', $filter);

        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }

    /* Destination region taxonomy — query ALL travel CPTs together */
    if ( $query->is_tax( 'destination_region' ) ) {
        $query->set( 'post_type', array( 'travel_package', 'group_trip', 'corporate_trip', 'tw_event', 'itinerary' ) );
        $query->set( 'posts_per_page', 12 );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }

    /* Group Trips archive — exclude Women's Trips (they have their own page) */
    if ( $query->is_post_type_archive( 'group_trip' ) ) {
        $query->set( 'meta_query', array(
            'relation' => 'OR',
            array( 'key' => 'package_trip_type', 'compare' => 'NOT EXISTS' ),
            array( 'key' => 'package_trip_type', 'value' => 'Women Trip', 'compare' => '!=' ),
        ) );
    }
}
add_action('pre_get_posts', 'mytheme_apply_travel_archive_filters');

// ── Nav Walker: adds tw-nav-link class to every <a> in the primary menu ──
if (!class_exists('TW_Nav_Walker')) {
    class TW_Nav_Walker extends Walker_Nav_Menu {
        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            $classes  = empty($item->classes) ? array() : (array) $item->classes;
            $active   = in_array('current-menu-item', $classes) ? ' tw-active' : '';
            $href     = !empty($item->url) ? $item->url : '#';
            $atts     = array(
                'href'   => esc_url($href),
                'target' => !empty($item->target) ? $item->target : '',
                'rel'    => !empty($item->xfn) ? $item->xfn : '',
                'class'  => 'tw-nav-link' . $active,
                'title'  => !empty($item->attr_title) ? $item->attr_title : '',
            );
            $attributes = '';
            foreach ($atts as $attr => $value) {
                if ($value !== '') {
                    $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
                }
            }
            $output .= '<li class="tw-menu-item">';
            $output .= '<a' . $attributes . '>' . esc_html($item->title) . '</a>';
        }
        public function end_el(&$output, $item, $depth = 0, $args = null) {
            $output .= '</li>';
        }
    }
}

// Register navigation menus
function mytheme_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme'),
        'footer' => __('Footer Menu', 'mytheme'),
    ));
}
add_action('init', 'mytheme_register_menus');

// Add theme support
function mytheme_theme_support() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo', array(
        'height'      => 96,   // max display height (px) — doubled for retina
        'width'       => 400,
        'flex-height' => true, // allow shorter logos
        'flex-width'  => true,
        'header-text' => array('tw-brand-name', 'tw-brand-tag'),
    ));
    add_theme_support('html5', array('search-form'));
}
add_action('after_setup_theme', 'mytheme_theme_support');

// ============================================================
// AFFILIATE LINKS — Custom Post Type
// ============================================================
function mytheme_register_affiliate_cpt() {
    register_post_type('tw_affiliate', array(
        'labels' => array(
            'name'               => __('Affiliate Links', 'mytheme'),
            'singular_name'      => __('Affiliate Link', 'mytheme'),
            'add_new'            => __('Add New Affiliate', 'mytheme'),
            'add_new_item'       => __('Add New Affiliate Link', 'mytheme'),
            'edit_item'          => __('Edit Affiliate Link', 'mytheme'),
            'new_item'           => __('New Affiliate Link', 'mytheme'),
            'view_item'          => __('View Affiliate Link', 'mytheme'),
            'search_items'       => __('Search Affiliates', 'mytheme'),
            'not_found'          => __('No affiliates found', 'mytheme'),
            'all_items'          => __('All Affiliates', 'mytheme'),
            'menu_name'          => __('Affiliates', 'mytheme'),
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => false, // we add it under our custom menu manually
        'show_in_rest' => false,
        'supports'     => array('title', 'excerpt', 'thumbnail'),
        'menu_icon'    => 'dashicons-admin-links',
    ));
}
add_action('init', 'mytheme_register_affiliate_cpt');

// ── Affiliate meta box ────────────────────────────────────────
function mytheme_add_affiliate_cpt_meta_box() {
    add_meta_box(
        'tw_affiliate_details',
        __('Affiliate Details', 'mytheme'),
        'mytheme_render_affiliate_cpt_meta_box',
        'tw_affiliate',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mytheme_add_affiliate_cpt_meta_box');

function mytheme_render_affiliate_cpt_meta_box($post) {
    wp_nonce_field('mytheme_save_affiliate_cpt', 'mytheme_affiliate_cpt_nonce');

    $url        = get_post_meta($post->ID, '_taff_url',        true);
    $category   = get_post_meta($post->ID, '_taff_category',   true);
    $commission = get_post_meta($post->ID, '_taff_commission',  true);
    $icon       = get_post_meta($post->ID, '_taff_icon',        true);
    $cta        = get_post_meta($post->ID, '_taff_cta',         true);
    $order      = get_post_meta($post->ID, '_taff_order',       true);

    $categories = array('Hotels', 'Flights', 'Tours & Activities', 'Travel Insurance', 'Car Rental', 'Other');
    ?>
    <table class="form-table" style="margin-top:0">
        <tr>
            <th style="width:160px"><label for="taff_icon"><?php esc_html_e('Icon (Flaticon slug)', 'mytheme'); ?></label></th>
            <td>
                <input type="text" id="taff_icon" name="taff_icon" value="<?php echo esc_attr($icon ?: 'link'); ?>" style="width:120px;text-align:center" />
                <p class="description">e.g. hotel, plane, ticket-alt, shield, car — any slug from <a href="https://www.flaticon.com/uicons" target="_blank" rel="noopener">Flaticon UIcons (regular-rounded)</a></p>
            </td>
        </tr>
        <tr>
            <th><label for="taff_url"><?php esc_html_e('Affiliate URL', 'mytheme'); ?></label></th>
            <td>
                <input type="url" id="taff_url" name="taff_url" value="<?php echo esc_attr($url); ?>" style="width:100%" placeholder="https://your-affiliate-link.com" />
                <p class="description">Full affiliate link including tracking parameters</p>
            </td>
        </tr>
        <tr>
            <th><label for="taff_category"><?php esc_html_e('Category', 'mytheme'); ?></label></th>
            <td>
                <select id="taff_category" name="taff_category" style="width:220px">
                    <option value=""><?php esc_html_e('— Select category —', 'mytheme'); ?></option>
                    <?php foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat); ?>" <?php selected($category, $cat); ?>><?php echo esc_html($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="taff_commission"><?php esc_html_e('Commission Rate', 'mytheme'); ?></label></th>
            <td>
                <input type="text" id="taff_commission" name="taff_commission" value="<?php echo esc_attr($commission); ?>" style="width:140px" placeholder="e.g. 4–6%" />
                <p class="description">Shown on the affiliate table for transparency</p>
            </td>
        </tr>
        <tr>
            <th><label for="taff_cta"><?php esc_html_e('Button Label', 'mytheme'); ?></label></th>
            <td>
                <input type="text" id="taff_cta" name="taff_cta" value="<?php echo esc_attr($cta ?: 'Book'); ?>" style="width:160px" placeholder="Book, Compare, Explore…" />
            </td>
        </tr>
        <tr>
            <th><label for="taff_order"><?php esc_html_e('Display Order', 'mytheme'); ?></label></th>
            <td>
                <input type="number" id="taff_order" name="taff_order" value="<?php echo esc_attr($order ?: 10); ?>" style="width:80px" min="0" step="1" />
                <p class="description">Lower number = shown first. 1=Booking.com, 2=Skyscanner, etc.</p>
            </td>
        </tr>
    </table>
    <?php
}

function mytheme_save_affiliate_cpt_meta($post_id) {
    if (!isset($_POST['mytheme_affiliate_cpt_nonce']) ||
        !wp_verify_nonce($_POST['mytheme_affiliate_cpt_nonce'], 'mytheme_save_affiliate_cpt')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_fields = array('taff_icon', 'taff_category', 'taff_commission', 'taff_cta');
    foreach ($text_fields as $f) {
        if (isset($_POST[$f])) {
            update_post_meta($post_id, '_' . $f, sanitize_text_field(wp_unslash($_POST[$f])));
        }
    }
    if (isset($_POST['taff_url'])) {
        update_post_meta($post_id, '_taff_url', esc_url_raw(wp_unslash($_POST['taff_url'])));
    }
    if (isset($_POST['taff_order'])) {
        update_post_meta($post_id, '_taff_order', absint($_POST['taff_order']));
    }
}
add_action('save_post_tw_affiliate', 'mytheme_save_affiliate_cpt_meta');

// ============================================================
// ADMIN MENUS — three separate top-level menus
// ============================================================
function mytheme_register_admin_menus() {

    // ── 1. BLOG POSTS ─────────────────────────────────────
    add_menu_page(
        __('Blog Posts', 'mytheme'),
        __('Blog Posts', 'mytheme'),
        'edit_posts',
        'tw-blog-posts',
        'mytheme_blog_posts_redirect',
        'dashicons-edit-page',
        26
    );
    add_submenu_page(
        'tw-blog-posts',
        __('All Blog Posts', 'mytheme'),
        __('All Posts', 'mytheme'),
        'edit_posts',
        'tw-blog-posts',
        'mytheme_blog_posts_redirect'
    );
    add_submenu_page(
        'tw-blog-posts',
        __('Add New Blog Post', 'mytheme'),
        __('Add New Post', 'mytheme'),
        'edit_posts',
        'post-new.php',
        ''
    );

    // ── 2. AFFILIATE LINKS ────────────────────────────────
    add_menu_page(
        __('Affiliate Links', 'mytheme'),
        __('Affiliate Links', 'mytheme'),
        'manage_options',
        'tw-affiliates',
        'mytheme_affiliates_redirect',
        'dashicons-admin-links',
        27
    );
    add_submenu_page(
        'tw-affiliates',
        __('All Affiliates', 'mytheme'),
        __('All Affiliates', 'mytheme'),
        'manage_options',
        'tw-affiliates',
        'mytheme_affiliates_redirect'
    );
    add_submenu_page(
        'tw-affiliates',
        __('Add New Affiliate', 'mytheme'),
        __('Add New Affiliate', 'mytheme'),
        'manage_options',
        'post-new.php?post_type=tw_affiliate',
        ''
    );

    // ── 3. TRIP INQUIRIES ─────────────────────────────────
    add_menu_page(
        __('Trip Inquiries', 'mytheme'),
        __('Trip Inquiries', 'mytheme'),
        'manage_options',
        'trip-inquiries',
        'mytheme_render_inquiries_page',
        'dashicons-email-alt',
        28
    );
    add_submenu_page(
        'trip-inquiries',
        __('All Inquiries', 'mytheme'),
        __('All Inquiries', 'mytheme'),
        'manage_options',
        'trip-inquiries',
        'mytheme_render_inquiries_page'
    );

    // ── 3b. ENQUIRIES (package / itinerary / destination "Book Now") ──
    add_menu_page(
        __('Enquiries', 'mytheme'),
        __('Enquiries', 'mytheme'),
        'manage_options',
        'tw-enquiries',
        'mytheme_render_enquiries_page',
        'dashicons-clipboard',
        28.5
    );
    add_submenu_page(
        'tw-enquiries',
        __('Newsletter', 'mytheme'),
        __('Newsletter', 'mytheme'),
        'manage_options',
        'tw-newsletter',
        'mytheme_render_newsletter_page'
    );

    // ── 4. 1TRIPWISER SETTINGS HUB ────────────────────────
    add_menu_page(
        __('1TripWiser Settings', 'mytheme'),
        __('1TripWiser', 'mytheme'),
        'manage_options',
        'tw-settings',
        'tw_render_overview_page',
        'dashicons-admin-settings',
        29
    );
    add_submenu_page(
        'tw-settings',
        __('Overview', 'mytheme'),
        __('<i class="fi-rr-chart-histogram" aria-hidden="true"></i> Overview', 'mytheme'),
        'manage_options',
        'tw-settings',
        'tw_render_overview_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Homepage Hero Banner', 'mytheme'),
        __('<i class="fi-rr-clapperboard" aria-hidden="true"></i> Hero Banner', 'mytheme'),
        'manage_options',
        'tw-hero-settings',
        'tw_render_hero_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('LUXURY Page', 'mytheme'),
        __('<i class="fi-rr-gem" aria-hidden="true"></i> LUXURY Page', 'mytheme'),
        'manage_options',
        'tw-luxury-hero-settings',
        'tw_render_luxury_hero_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Plan A Trip Page', 'mytheme'),
        __('<i class="fi-rr-plane" aria-hidden="true"></i> Plan A Trip Page', 'mytheme'),
        'manage_options',
        'tw-plan-trip-settings',
        'tw_render_plan_trip_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Blog & Affiliates Page', 'mytheme'),
        __('<i class="fi-rr-note" aria-hidden="true"></i> Blog & Affiliates Page', 'mytheme'),
        'manage_options',
        'tw-blog-settings',
        'tw_render_blog_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('WhatsApp Chat Widget', 'mytheme'),
        __('<i class="fi-rr-comment" aria-hidden="true"></i> WhatsApp Widget', 'mytheme'),
        'manage_options',
        'tw-wa-widget-settings',
        'tw_render_wa_widget_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('WhatsApp Cloud API', 'mytheme'),
        __('<i class="fi-rr-brain-circuit" aria-hidden="true"></i> WhatsApp API', 'mytheme'),
        'manage_options',
        'tw-wa-api-settings',
        'tw_render_wa_api_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Testimonials', 'mytheme'),
        __('<i class="fi-rr-comment" aria-hidden="true"></i> Testimonials', 'mytheme'),
        'manage_options',
        'tw-testimonials-settings',
        'tw_render_testimonials_settings_page'
    );
}
add_action('admin_menu', 'mytheme_register_admin_menus');

/* ─────────────────────────────────────────────────────────
 * UNIFIED TESTIMONIALS ADMIN PAGE
 * Manages: Homepage testimonials + Women's Trips testimonials
 * ───────────────────────────────────────────────────────── */
add_action( 'admin_init', function () {
    register_setting( 'tw_testimonials_group', 'tw_homepage_testimonials',  array( 'sanitize_callback' => 'tw_sanitize_testimonials' ) );
    register_setting( 'tw_testimonials_group', 'tw_womens_testimonials',    array( 'sanitize_callback' => 'tw_sanitize_testimonials' ) );
} );

function tw_sanitize_testimonials( $input ) {
    if ( ! is_array( $input ) ) { return array(); }
    $clean = array();
    foreach ( $input as $item ) {
        $q = sanitize_textarea_field( $item['quote'] ?? '' );
        if ( $q === '' ) { continue; }
        $clean[] = array(
            'quote'    => $q,
            'name'     => sanitize_text_field( $item['name']     ?? '' ),
            'location' => sanitize_text_field( $item['location'] ?? '' ),
            'trip'     => sanitize_text_field( $item['trip']     ?? '' ),
        );
    }
    return $clean;
}

/** Homepage testimonials showcase — rendered via front-page.php */
function tw_homepage_testimonials_section() {
    $testimonials = get_option( 'tw_homepage_testimonials', array() );
    if ( empty( $testimonials ) ) {
        $testimonials = array(
            array( 'quote' => 'Planning a trip has never been this easy. The team handled everything — hotels, permits, itinerary. We just showed up and enjoyed!', 'name' => 'Aditya M.', 'location' => 'Pune', 'trip' => 'Ladakh Road Trip' ),
            array( 'quote' => 'Absolutely loved the Kerala backwaters trip. The houseboat stay was magical and the pricing was way better than what I found elsewhere.', 'name' => 'Sneha R.', 'location' => 'Hyderabad', 'trip' => 'Kerala Backwaters' ),
            array( 'quote' => 'Joined a Spiti Valley group trip as a solo traveller and came back with 11 new best friends. The itinerary was perfectly paced — not rushed at all.', 'name' => 'Rahul K.', 'location' => 'Bengaluru', 'trip' => 'Spiti Valley Group Expedition' ),
        );
    }
    if ( empty( $testimonials ) ) { return; }
    ?>
    <section class="tw-testimonials-section">
        <div class="container tw-testimonials-inner">
            <div class="tw-testimonials-head" data-reveal="up">
                <span class="tw-testimonials-kicker"> Real travellers. Real stories.</span>
                <h2 class="tw-h2 tw-h2--lg tw-testimonials-title">What <span class="tw-h2-accent">Travellers Say</span></h2>
            </div>
            <div class="tw-testimonials-grid">
                <?php foreach ( $testimonials as $t ) :
                    $initials = strtoupper( substr( $t['name'] ?? 'T', 0, 1 ) );
                ?>
                <div class="tw-testi-card" data-reveal="scale">
                    <div class="tw-testi-card-stars">
                        <span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span><span><i class="fi-rr-star" aria-hidden="true"></i></span>
                    </div>
                    <span class="tw-testi-card-quote-mark">"</span>
                    <p class="tw-testi-card-text"><?php echo esc_html( $t['quote'] ); ?>"</p>
                    <div class="tw-testi-card-author">
                        <div class="tw-testi-card-avatar"><?php echo esc_html( $initials ); ?></div>
                        <div>
                            <?php if ( ! empty( $t['name'] ) ) : ?><strong><?php echo esc_html( $t['name'] ); ?></strong><?php endif; ?>
                            <?php if ( ! empty( $t['location'] ) ) : ?><span><?php echo esc_html( $t['location'] ); ?></span><?php endif; ?>
                            <?php if ( ! empty( $t['trip'] ) ) : ?><span class="tw-testi-card-trip"><i class="fi-rr-plane" aria-hidden="true"></i> <?php echo esc_html( $t['trip'] ); ?></span><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}

function tw_render_testimonials_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    wp_enqueue_script( 'jquery' );
    tw_settings_page_header( 'Testimonials', '<i class="fi-rr-comment" aria-hidden="true"></i>', 'Manage testimonials shown on the Homepage and the Women\'s Group Trips page.' );

    $hp_defaults = array(
        array( 'quote' => 'Planning a trip has never been this easy. The team handled everything — hotels, permits, itinerary. We just showed up and enjoyed!', 'name' => 'Aditya M.', 'location' => 'Pune', 'trip' => 'Ladakh Road Trip' ),
        array( 'quote' => 'Absolutely loved the Kerala backwaters trip. The houseboat stay was magical and the pricing was way better than what I found elsewhere.', 'name' => 'Sneha R.', 'location' => 'Hyderabad', 'trip' => 'Kerala Backwaters' ),
        array( 'quote' => 'Joined a Spiti Valley group trip as a solo traveller and came back with 11 new best friends. Perfectly paced itinerary — not rushed at all.', 'name' => 'Rahul K.', 'location' => 'Bengaluru', 'trip' => 'Spiti Valley Expedition' ),
    );
    $wo_defaults = array(
        array( 'quote' => 'Best decision I ever made for solo travel — felt safe, empowered and came back a changed person.', 'name' => 'Priya S.', 'location' => 'Mumbai', 'trip' => "Women's Kerala Trip" ),
        array( 'quote' => 'Felt safe the entire trip. The female trip leader was amazing and the group was so supportive.', 'name' => 'Ananya R.', 'location' => 'Delhi', 'trip' => 'Kasol Trek' ),
        array( 'quote' => 'Met my absolute best friends on a 1TRIPWISER women\'s trip. We\'ve already booked the next one!', 'name' => 'Riya K.', 'location' => 'Bangalore', 'trip' => "Bali Women's Getaway" ),
    );

    $hp_testi = get_option( 'tw_homepage_testimonials', array() );
    $wo_testi = get_option( 'tw_womens_testimonials',  array() );
    if ( empty( $hp_testi ) ) { $hp_testi = $hp_defaults; }
    if ( empty( $wo_testi ) ) { $wo_testi = $wo_defaults; }

    // Shared row render helper (outputs HTML directly)
    $render_rows = function( $list, $option_key, $testi_array, $has_trip = true ) {
        foreach ( $testi_array as $i => $t ) : ?>
        <div class="tw-testi-row" style="background:#f9fafb;border:1px solid #e8edf5;border-radius:12px;padding:16px 20px;position:relative;margin-bottom:12px;">
            <button type="button" class="button tw-tr-remove" style="position:absolute;top:12px;right:12px;color:#c0392b;border-color:#c0392b;" onclick="this.closest('.tw-testi-row').remove()"><i class="fi-rr-cross-small" aria-hidden="true"></i> Remove</button>
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr <?php echo $has_trip ? '1fr' : ''; ?>;gap:10px;margin-right:90px;">
                <div style="grid-column:1/-1;">
                    <label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Quote *</label>
                    <textarea name="<?php echo esc_attr($option_key); ?>[<?php echo $i; ?>][quote]" rows="2"
                        style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:8px 10px;font-size:0.88rem;resize:vertical;"
                        placeholder="What the traveller said…"><?php echo esc_textarea( $t['quote'] ?? '' ); ?></textarea>
                </div>
                <div>
                    <label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Name</label>
                    <input type="text" name="<?php echo esc_attr($option_key); ?>[<?php echo $i; ?>][name]"
                        value="<?php echo esc_attr( $t['name'] ?? '' ); ?>"
                        style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:7px 10px;font-size:0.88rem;" placeholder="Priya S.">
                </div>
                <div>
                    <label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Location</label>
                    <input type="text" name="<?php echo esc_attr($option_key); ?>[<?php echo $i; ?>][location]"
                        value="<?php echo esc_attr( $t['location'] ?? '' ); ?>"
                        style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:7px 10px;font-size:0.88rem;" placeholder="Mumbai">
                </div>
                <?php if ( $has_trip ) : ?>
                <div>
                    <label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Trip Name</label>
                    <input type="text" name="<?php echo esc_attr($option_key); ?>[<?php echo $i; ?>][trip]"
                        value="<?php echo esc_attr( $t['trip'] ?? '' ); ?>"
                        style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:7px 10px;font-size:0.88rem;" placeholder="Ladakh Trip">
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach;
    };
    ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'tw_testimonials_group' ); ?>

        <!-- HOMEPAGE TESTIMONIALS -->
        <div class="tw-admin-card" style="margin-bottom:28px;">
            <div class="tw-admin-card-head">
                <div>
                    <h2><i class="fi-rr-home" aria-hidden="true"></i> Homepage Testimonials</h2>
                    <p>Shown in the "What Our Travellers Say" section on the homepage. Up to 6. Include a Trip Name to show the <i class="fi-rr-plane" aria-hidden="true"></i> trip badge.</p>
                </div>
            </div>
            <div class="tw-admin-card-body">
                <div id="tw-hp-list">
                    <?php $render_rows( 'tw-hp-list', 'tw_homepage_testimonials', $hp_testi, true ); ?>
                </div>
                <button type="button" class="button button-secondary tw-tr-add" data-list="tw-hp-list" data-key="tw_homepage_testimonials" data-trip="1" style="margin-top:4px;">+ Add Testimonial</button>
            </div>
        </div>

        <!-- WOMEN'S TESTIMONIALS -->
        <div class="tw-admin-card" style="margin-bottom:28px;">
            <div class="tw-admin-card-head">
                <div>
                    <h2><i class="fi-rr-user" aria-hidden="true"></i> Women's Trips Testimonials</h2>
                    <p>Shown on the Women's Group Trips landing page. Up to 6.</p>
                </div>
            </div>
            <div class="tw-admin-card-body">
                <div id="tw-wo-list">
                    <?php $render_rows( 'tw-wo-list', 'tw_womens_testimonials', $wo_testi, true ); ?>
                </div>
                <button type="button" class="button button-secondary tw-tr-add" data-list="tw-wo-list" data-key="tw_womens_testimonials" data-trip="1" style="margin-top:4px;">+ Add Testimonial</button>
            </div>
        </div>

        <?php submit_button( 'Save All Testimonials', 'primary button-hero', 'submit', true ); ?>
    </form>

    <script>
    (function(){
        var counts = { 'tw-hp-list': <?php echo count($hp_testi); ?>, 'tw-wo-list': <?php echo count($wo_testi); ?> };

        document.querySelectorAll('.tw-tr-add').forEach(function(btn){
            btn.addEventListener('click', function(){
                var listId  = btn.getAttribute('data-list');
                var optKey  = btn.getAttribute('data-key');
                var hasTrip = btn.getAttribute('data-trip') === '1';
                var list    = document.getElementById(listId);
                if ( list.querySelectorAll('.tw-testi-row').length >= 6 ) { alert('Maximum 6 testimonials.'); return; }
                var i = counts[listId]++;
                var tripField = hasTrip
                    ? '<div><label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Trip Name</label>'
                    + '<input type="text" name="' + optKey + '[' + i + '][trip]" style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:7px 10px;font-size:0.88rem;" placeholder="e.g. Ladakh Trip"></div>'
                    : '';
                var row = document.createElement('div');
                row.className = 'tw-testi-row';
                row.style.cssText = 'background:#f9fafb;border:1px solid #e8edf5;border-radius:12px;padding:16px 20px;position:relative;margin-bottom:12px;';
                row.innerHTML = '<button type="button" class="button tw-tr-remove" style="position:absolute;top:12px;right:12px;color:#c0392b;border-color:#c0392b;" onclick="this.closest(\'.tw-testi-row\').remove()"><i class="fi-rr-cross-small" aria-hidden="true"></i> Remove</button>'
                    + '<div style="display:grid;grid-template-columns:2fr 1fr 1fr' + (hasTrip?' 1fr':'') + ';gap:10px;margin-right:90px;">'
                    + '<div style="grid-column:1/-1;"><label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Quote *</label>'
                    + '<textarea name="' + optKey + '[' + i + '][quote]" rows="2" style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:8px 10px;font-size:0.88rem;resize:vertical;" placeholder="What the traveller said…"></textarea></div>'
                    + '<div><label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Name</label>'
                    + '<input type="text" name="' + optKey + '[' + i + '][name]" style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:7px 10px;font-size:0.88rem;" placeholder="Priya S."></div>'
                    + '<div><label style="font-weight:700;display:block;margin-bottom:4px;font-size:0.82rem;">Location</label>'
                    + '<input type="text" name="' + optKey + '[' + i + '][location]" style="width:100%;border-radius:8px;border:1px solid #dde5ef;padding:7px 10px;font-size:0.88rem;" placeholder="Mumbai"></div>'
                    + tripField + '</div>';
                list.appendChild(row);
            });
        });
    })();
    </script>
    <?php
    tw_settings_page_footer();
}

// Redirect tw-blog-posts and tw-affiliates menu slugs BEFORE any output is sent
function mytheme_admin_early_redirects() {
    if (!is_admin() || !current_user_can('edit_posts')) return;
    $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
    if ($page === 'tw-blog-posts') {
        wp_safe_redirect(admin_url('edit.php'));
        exit;
    }
    if ($page === 'tw-affiliates') {
        wp_safe_redirect(admin_url('edit.php?post_type=tw_affiliate'));
        exit;
    }
}
add_action('admin_init', 'mytheme_admin_early_redirects');

// Stub callbacks (admin_init redirect fires first, these are never actually rendered)
function mytheme_blog_posts_redirect() { wp_safe_redirect(admin_url('edit.php')); exit; }
function mytheme_affiliates_redirect()  { wp_safe_redirect(admin_url('edit.php?post_type=tw_affiliate')); exit; }

// ── Highlight correct top-level menu when editing posts / affiliates ──────────
function mytheme_highlight_admin_menu($parent_file) {
    global $current_screen;
    if (!$current_screen) return $parent_file;

    if ($current_screen->post_type === 'post') {
        return 'tw-blog-posts';
    }
    if ($current_screen->post_type === 'tw_affiliate') {
        return 'tw-affiliates';
    }
    return $parent_file;
}
add_filter('parent_file', 'mytheme_highlight_admin_menu');

function mytheme_highlight_admin_submenu($submenu_file) {
    global $current_screen, $pagenow;
    if (!$current_screen) return $submenu_file;

    if ($current_screen->post_type === 'post') {
        return ($pagenow === 'post-new.php') ? 'post-new.php' : 'tw-blog-posts';
    }
    if ($current_screen->post_type === 'tw_affiliate') {
        return ($pagenow === 'post-new.php') ? 'post-new.php?post_type=tw_affiliate' : 'tw-affiliates';
    }
    return $submenu_file;
}
add_filter('submenu_file', 'mytheme_highlight_admin_submenu');

// Default menu fallback
function default_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">Blog</a></li>';
    echo '<li><a href="' . esc_url(home_url('/destinations/')) . '">Destinations</a></li>';
    echo '<li><a href="' . esc_url(get_post_type_archive_link('travel_package')) . '">Packages</a></li>';
    echo '<li><a href="' . esc_url(mytheme_get_plan_trip_url()) . '" class="cta-btn">Plan My Trip</a></li>';
    echo '<li><a href="#">About Us</a></li>';
    echo '</ul>';
}

// Custom search form
function mytheme_search_form($form) {
    $form = '<form role="search" method="get" class="search-form" action="' . home_url('/') . '">
        <label>
            <span class="screen-reader-text">' . _x('Search for:', 'label') . '</span>
            <input type="search" class="search-field" placeholder="' . esc_attr_x('Search destinations...', 'placeholder') . '" value="' . get_search_query() . '" name="s" />
        </label>
        <input type="submit" class="search-submit" value="' . esc_attr_x('Search', 'submit button') . '" />
    </form>';
    return $form;
}
add_filter('get_search_form', 'mytheme_search_form');

// ============================================================
// TRIP INQUIRY CUSTOM POST TYPE (stores Plan A Trip form submissions)
// ============================================================
function mytheme_register_trip_inquiry_cpt() {
    register_post_type('trip_inquiry', array(
        'labels' => array(
            'name'          => __('Trip Inquiries', 'mytheme'),
            'singular_name' => __('Trip Inquiry', 'mytheme'),
            'all_items'     => __('All Inquiries', 'mytheme'),
            'view_item'     => __('View Inquiry', 'mytheme'),
            'search_items'  => __('Search Inquiries', 'mytheme'),
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => false,
        'supports'     => array('title'),
        'menu_icon'    => 'dashicons-email-alt',
    ));
}
add_action('init', 'mytheme_register_trip_inquiry_cpt');

// (Admin menus are now registered in mytheme_register_admin_menus below)

// ============================================================
// TRIP INQUIRIES — EXPORT HANDLER (runs in admin_init, before headers)
// ============================================================
function mytheme_export_trip_inquiries() {
    if ( ! is_admin() || ! current_user_can('manage_options') ) return;
    if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'trip-inquiries' ) return;
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'export' ) return;

    // Verify nonce
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tw_export_inquiries' ) ) {
        wp_die( 'Security check failed.' );
    }

    $format = isset( $_GET['format'] ) ? sanitize_key( $_GET['format'] ) : 'csv';

    // Fetch ALL inquiries (no pagination)
    $all = get_posts( array(
        'post_type'      => 'trip_inquiry',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $headers = array( '#', 'Name', 'Phone', 'Email', 'Destination', 'Travel Date', 'Duration', 'Budget', 'Adults', 'Children', 'Trip Type', 'Submitted' );
    $rows    = array();
    $i       = 1;
    foreach ( $all as $p ) {
        $rows[] = array(
            $i++,
            get_post_meta( $p->ID, '_ti_name',        true ),
            get_post_meta( $p->ID, '_ti_phone',       true ),
            get_post_meta( $p->ID, '_ti_email',       true ),
            get_post_meta( $p->ID, '_ti_destination', true ),
            get_post_meta( $p->ID, '_ti_date',        true ),
            get_post_meta( $p->ID, '_ti_duration',    true ),
            get_post_meta( $p->ID, '_ti_budget',      true ),
            get_post_meta( $p->ID, '_ti_adults',      true ),
            get_post_meta( $p->ID, '_ti_children',    true ),
            get_post_meta( $p->ID, '_ti_trip_type',   true ),
            get_the_date( 'd M Y, g:i a', $p ),
        );
    }

    $filename = '1tripwiser-inquiries-' . date( 'Y-m-d' );

    /* ── CSV ── */
    if ( $format === 'csv' ) {
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '.csv"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        $out = fopen( 'php://output', 'w' );
        fprintf( $out, chr(0xEF) . chr(0xBB) . chr(0xBF) ); // UTF-8 BOM for Excel
        fputcsv( $out, $headers );
        foreach ( $rows as $row ) {
            fputcsv( $out, $row );
        }
        fclose( $out );
        exit;
    }

    /* ── XLSX ── */
    if ( $format === 'xlsx' ) {
        $xlsx = tw_build_xlsx( $headers, $rows );
        header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '.xlsx"' );
        header( 'Content-Length: ' . strlen( $xlsx ) );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        echo $xlsx;
        exit;
    }
}
add_action( 'admin_init', 'mytheme_export_trip_inquiries' );

/* ── Minimal XLSX builder using ZipArchive (no external library needed) ── */
function tw_build_xlsx( array $headers, array $rows ) {
    // Collect all unique strings for the shared strings table
    $strings     = array();
    $string_map  = array();

    $get_sid = function ( $val ) use ( &$strings, &$string_map ) {
        $s = (string) $val;
        if ( ! isset( $string_map[ $s ] ) ) {
            $string_map[ $s ] = count( $strings );
            $strings[]        = $s;
        }
        return $string_map[ $s ];
    };

    // Build sheet rows XML
    $sheet_rows = '';
    $row_num    = 1;

    // Header row (bold via style index 1)
    $sheet_rows .= '<row r="' . $row_num . '">';
    $col = 0;
    foreach ( $headers as $h ) {
        $cell_ref = tw_xlsx_col( $col ) . $row_num;
        $sid      = $get_sid( $h );
        $sheet_rows .= '<c r="' . $cell_ref . '" t="s" s="1"><v>' . $sid . '</v></c>';
        $col++;
    }
    $sheet_rows .= '</row>';
    $row_num++;

    // Data rows
    foreach ( $rows as $row ) {
        $sheet_rows .= '<row r="' . $row_num . '">';
        $col = 0;
        foreach ( $row as $val ) {
            $cell_ref = tw_xlsx_col( $col ) . $row_num;
            if ( is_numeric( $val ) && $col !== 0 ) {
                // numeric cell (not the # column which we want as text)
                $sheet_rows .= '<c r="' . $cell_ref . '"><v>' . esc_attr( $val ) . '</v></c>';
            } else {
                $sid = $get_sid( $val );
                $sheet_rows .= '<c r="' . $cell_ref . '" t="s"><v>' . $sid . '</v></c>';
            }
            $col++;
        }
        $sheet_rows .= '</row>';
        $row_num++;
    }

    // Last column letter for autoFilter
    $last_col = tw_xlsx_col( count( $headers ) - 1 ) . '1';

    // Shared strings XML
    $ss_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count( $strings ) . '" uniqueCount="' . count( $strings ) . '">';
    foreach ( $strings as $s ) {
        $ss_xml .= '<si><t xml:space="preserve">' . xmlspecialchars( $s ) . '</t></si>';
    }
    $ss_xml .= '</sst>';

    // Styles XML — style 0: normal, style 1: bold header with teal fill
    $styles_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><color rgb="FF0D1526"/><name val="Calibri"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF0692AF"/></patternFill></fill>
  </fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>
  </cellXfs>
</styleSheet>';

    // Sheet XML
    $sheet_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetViews><sheetView workbookViewId="0"><selection activeCell="A1"/></sheetView></sheetViews>'
        . '<autoFilter ref="A1:' . $last_col . '"/>'
        . '<sheetData>' . $sheet_rows . '</sheetData>'
        . '</worksheet>';

    // Workbook XML
    $wb_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Trip Inquiries" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';

    // Relationships
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    $wb_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
        . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';

    $content_types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml"  ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>';

    // Build ZIP in memory
    $tmp = tempnam( sys_get_temp_dir(), 'xlsx_' );
    $zip = new ZipArchive();
    $zip->open( $tmp, ZipArchive::OVERWRITE );
    $zip->addFromString( '[Content_Types].xml',           $content_types );
    $zip->addFromString( '_rels/.rels',                   $rels );
    $zip->addFromString( 'xl/workbook.xml',               $wb_xml );
    $zip->addFromString( 'xl/_rels/workbook.xml.rels',    $wb_rels );
    $zip->addFromString( 'xl/worksheets/sheet1.xml',      $sheet_xml );
    $zip->addFromString( 'xl/sharedStrings.xml',          $ss_xml );
    $zip->addFromString( 'xl/styles.xml',                 $styles_xml );
    $zip->close();

    $data = file_get_contents( $tmp );
    unlink( $tmp );
    return $data;
}

function tw_xlsx_col( $idx ) {
    $col = '';
    for ( $i = $idx; $i >= 0; $i = intval( $i / 26 ) - 1 ) {
        $col = chr( 65 + ( $i % 26 ) ) . $col;
    }
    return $col;
}

function xmlspecialchars( $s ) {
    return str_replace( array( '&', '<', '>', '"', "'" ), array( '&amp;', '&lt;', '&gt;', '&quot;', '&apos;' ), $s );
}

// ============================================================
// TRIP INQUIRIES LIST PAGE (Admin view)
// ============================================================
function mytheme_render_inquiries_page() {
    $paged    = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $query    = new WP_Query(array(
        'post_type'      => 'trip_inquiry',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    ?>
    <?php
    $export_base  = admin_url( 'admin.php?page=trip-inquiries&action=export' );
    $export_nonce = wp_create_nonce( 'tw_export_inquiries' );
    $csv_url      = esc_url( add_query_arg( array( 'format' => 'csv',  '_wpnonce' => $export_nonce ), $export_base ) );
    $xlsx_url     = esc_url( add_query_arg( array( 'format' => 'xlsx', '_wpnonce' => $export_nonce ), $export_base ) );
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <?php esc_html_e('Trip Inquiries', 'mytheme'); ?>
            <span style="font-size:14px;font-weight:normal;color:#666;">
                <?php echo esc_html($query->found_posts); ?> total
            </span>
            <span style="margin-left:auto;display:flex;gap:8px;">
                <a href="<?php echo $csv_url; ?>"
                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#2271b1;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:600;line-height:1.4">
                    <i class="fi-rr-arrow-down" aria-hidden="true"></i> Export CSV
                </a>
                <a href="<?php echo $xlsx_url; ?>"
                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#1d7a3a;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:600;line-height:1.4">
                    <i class="fi-rr-arrow-down" aria-hidden="true"></i> Export Excel
                </a>
            </span>
        </h1>
        <table class="wp-list-table widefat fixed striped" style="margin-top:12px">
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th><?php esc_html_e('Name', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Phone', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Email', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Package', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Destination', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Travel Date', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Duration', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Budget', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Adults', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Children', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Trip Type', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Submitted', 'mytheme'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($query->have_posts()) :
                $i = ($paged - 1) * $per_page + 1;
                while ($query->have_posts()) : $query->the_post();
                    $id = get_the_ID(); ?>
                    <tr>
                        <td><?php echo esc_html($i++); ?></td>
                        <td><strong><?php echo esc_html(get_post_meta($id, '_ti_name', true)); ?></strong></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_phone', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_email', true)); ?></td>
                        <td>
                            <?php
                            $package_title = get_post_meta($id, '_ti_package_title', true);
                            $package_url = get_post_meta($id, '_ti_package_url', true);
                            if ($package_title && $package_url) {
                                echo '<a href="' . esc_url($package_url) . '" target="_blank" rel="noopener">' . esc_html($package_title) . '</a>';
                            } elseif ($package_title) {
                                echo esc_html($package_title);
                            } else {
                                echo '&mdash;';
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_destination', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_date', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_duration', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_budget', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_adults', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_children', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_trip_type', true)); ?></td>
                        <td><?php echo esc_html(get_the_date('d M Y, g:i a')); ?></td>
                    </tr>
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <tr><td colspan="13" style="text-align:center;padding:24px;color:#666;"><?php esc_html_e('No inquiries yet. Form submissions will appear here.', 'mytheme'); ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php
        $total_pages = $query->max_num_pages;
        if ($total_pages > 1) {
            echo '<div style="margin-top:16px">';
            echo paginate_links(array(
                'base'    => add_query_arg('paged', '%#%'),
                'format'  => '',
                'current' => $paged,
                'total'   => $total_pages,
            ));
            echo '</div>';
        }
        ?>
    </div>
    <?php
}

// ============================================================
// ADMIN: Enquiries list (package / itinerary / destination)
// ============================================================
function mytheme_render_enquiries_page() {
    global $wpdb;
    $table = mytheme_enquiries_table_name();

    $type_filter = isset($_GET['enquiry_type']) ? sanitize_key($_GET['enquiry_type']) : '';
    $paged       = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page    = 20;
    $offset      = ($paged - 1) * $per_page;

    $where = '';
    $args  = array();
    if (in_array($type_filter, array('package', 'itinerary', 'destination', 'group_trip', 'corporate_trip', 'event', 'popup'), true)) {
        $where = 'WHERE enquiry_type = %s';
        $args[] = $type_filter;
    }

    $total_sql = "SELECT COUNT(*) FROM $table $where";
    $total = $args ? $wpdb->get_var($wpdb->prepare($total_sql, $args)) : $wpdb->get_var($total_sql);

    $rows_sql = "SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $rows_args = array_merge($args, array($per_page, $offset));
    $rows = $wpdb->get_results($wpdb->prepare($rows_sql, $rows_args));

    $type_labels = array('package' => 'Package', 'itinerary' => 'Itinerary', 'destination' => 'Destination', 'group_trip' => 'Group Trip', 'corporate_trip' => 'Corporate Trip', 'event' => 'Event', 'popup' => 'Website Popup');
    $base_url = admin_url('admin.php?page=tw-enquiries');
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <?php esc_html_e('Enquiries', 'mytheme'); ?>
            <span style="font-size:14px;font-weight:normal;color:#666;"><?php echo esc_html($total); ?> total</span>
        </h1>
        <ul class="subsubsub">
            <li><a href="<?php echo esc_url($base_url); ?>" class="<?php echo $type_filter === '' ? 'current' : ''; ?>">All</a> |</li>
            <?php $i = 0; foreach ($type_labels as $key => $label) : $i++; ?>
            <li><a href="<?php echo esc_url(add_query_arg('enquiry_type', $key, $base_url)); ?>" class="<?php echo $type_filter === $key ? 'current' : ''; ?>"><?php echo esc_html($label); ?></a><?php echo $i < count($type_labels) ? ' |' : ''; ?></li>
            <?php endforeach; ?>
        </ul>
        <table class="wp-list-table widefat fixed striped" style="margin-top:12px">
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th><?php esc_html_e('Type', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Name', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Phone', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Email', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Reference', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Travel Date', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Adults', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Budget', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Message', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Submitted', 'mytheme'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($rows) :
                $i = $offset + 1;
                foreach ($rows as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($i++); ?></td>
                        <td><?php echo esc_html(isset($type_labels[$row->enquiry_type]) ? $type_labels[$row->enquiry_type] : $row->enquiry_type); ?></td>
                        <td><strong><?php echo esc_html($row->name); ?></strong></td>
                        <td><?php echo esc_html($row->phone); ?></td>
                        <td><?php echo esc_html($row->email); ?></td>
                        <td>
                            <?php if ($row->ref_title && $row->ref_url) : ?>
                                <a href="<?php echo esc_url($row->ref_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($row->ref_title); ?></a>
                            <?php elseif ($row->ref_title) : ?>
                                <?php echo esc_html($row->ref_title); ?>
                            <?php else : ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($row->travel_date); ?></td>
                        <td><?php echo esc_html($row->adults); ?></td>
                        <td><?php echo esc_html($row->budget); ?></td>
                        <td><?php echo esc_html(wp_trim_words($row->message, 12)); ?></td>
                        <td><?php echo esc_html(mysql2date('d M Y, g:i a', $row->created_at)); ?></td>
                    </tr>
                <?php endforeach;
            else : ?>
                <tr><td colspan="11" style="text-align:center;padding:24px;color:#666;"><?php esc_html_e('No enquiries yet. "Book Now" / "Enquire" submissions will appear here.', 'mytheme'); ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php
        $total_pages = $per_page ? ceil($total / $per_page) : 1;
        if ($total_pages > 1) {
            echo '<div style="margin-top:16px">';
            echo paginate_links(array(
                'base'    => add_query_arg('paged', '%#%'),
                'format'  => '',
                'current' => $paged,
                'total'   => $total_pages,
            ));
            echo '</div>';
        }
        ?>
    </div>
    <?php
}

// ============================================================
// ADMIN: Newsletter subscribers (kept in its own table/page, separate
// from Enquiries, per the request that this data stay separate)
// ============================================================
function mytheme_render_newsletter_page() {
    global $wpdb;
    $table = mytheme_newsletter_table_name();

    $paged    = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $offset   = ($paged - 1) * $per_page;

    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $rows  = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset));
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <?php esc_html_e('Newsletter Subscribers', 'mytheme'); ?>
            <span style="font-size:14px;font-weight:normal;color:#666;"><?php echo esc_html($total); ?> total</span>
        </h1>
        <table class="wp-list-table widefat fixed striped" style="margin-top:12px">
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th><?php esc_html_e('Email', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Subscribed', 'mytheme'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($rows) :
                $i = $offset + 1;
                foreach ($rows as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($i++); ?></td>
                        <td><strong><?php echo esc_html($row->email); ?></strong></td>
                        <td><?php echo esc_html(mysql2date('d M Y, g:i a', $row->created_at)); ?></td>
                    </tr>
                <?php endforeach;
            else : ?>
                <tr><td colspan="3" style="text-align:center;padding:24px;color:#666;"><?php esc_html_e('No subscribers yet.', 'mytheme'); ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php
        $total_pages = $per_page ? ceil($total / $per_page) : 1;
        if ($total_pages > 1) {
            echo '<div style="margin-top:16px">';
            echo paginate_links(array(
                'base'    => add_query_arg('paged', '%#%'),
                'format'  => '',
                'current' => $paged,
                'total'   => $total_pages,
            ));
            echo '</div>';
        }
        ?>
    </div>
    <?php
}

// ============================================================
// SETTINGS: Register options for Plan A Trip + Blog pages + Hero + WhatsApp
// ============================================================
function mytheme_register_page_settings() {
    // Plan A Trip — one group per <form> on the page. WordPress's options.php
    // blanks out every option registered under a group that wasn't present in
    // whichever single form got submitted, so options split across multiple
    // forms must never share a group (see mytheme_register_page_settings()
    // usage below — every group here corresponds to exactly one <form>).
    foreach (array('tw_pat_kicker', 'tw_pat_title', 'tw_pat_subtitle') as $key) {
        register_setting('tw_pat_hero_group', $key, array('sanitize_callback' => 'sanitize_text_field'));
    }
    register_setting('tw_pat_whatsapp_group', 'tw_pat_whatsapp', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_pat_sheet_group', 'tw_pat_sheet_webhook_url', array('sanitize_callback' => 'esc_url_raw'));

    // Blog & Affiliates — two forms, two groups.
    register_setting('tw_blog_hero_group', 'tw_blog_kicker',   array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_blog_hero_group', 'tw_blog_title',    array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_blog_hero_group', 'tw_blog_subtitle', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_blog_aff_group', 'tw_blog_aff_heading', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_blog_aff_group', 'tw_blog_aff_text',    array('sanitize_callback' => 'wp_kses_post'));

    // Hero background — single form, one group is fine.
    register_setting('tripwiser_hero_settings', 'tw_hero_video_url',        array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_hero_image_url',        array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_hero_mobile_image_url', array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_instagram_feed_id', array('sanitize_callback' => 'absint'));
    // Hero floating images (top-right, middle-left, bottom-right of the video)
    register_setting('tripwiser_hero_settings', 'tw_hero_float_img_tr', array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_hero_float_img_ml', array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_hero_float_img_br', array('sanitize_callback' => 'esc_url_raw'));
    // LUXURY page scroll-scrubbed hero video — single form.
    register_setting('tripwiser_luxury_settings', 'tw_luxury_hero_video_url', array('sanitize_callback' => 'esc_url_raw'));

    // WhatsApp widget (its own page, one form) + Cloud API (a separate page
    // with two forms of its own) — three forms total, three groups.
    register_setting('tw_wa_widget_group', 'tw_wa_widget_number',   array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_wa_widget_group', 'tw_wa_widget_message',  array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_wa_widget_group', 'tw_wa_widget_greeting', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_wa_api_creds_group', 'tw_wa_api_token', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_wa_api_creds_group', 'tw_wa_phone_id',  array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_wa_api_template_group', 'tw_wa_template_name', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tw_wa_api_template_group', 'tw_wa_template_lang', array('sanitize_callback' => 'sanitize_text_field'));
}
add_action('admin_init', 'mytheme_register_page_settings');

// ============================================================
// WHATSAPP FLOATING CHAT WIDGET (front-end footer injection)
// ============================================================
function tw_whatsapp_widget() {
    if ( is_admin() ) return;
    $wa_num     = get_option('tw_wa_widget_number',  get_option('tw_pat_whatsapp', '') );
    $wa_msg     = get_option('tw_wa_widget_message',  'Hi! I have a question about a trip.');
    $wa_greet   = get_option('tw_wa_widget_greeting', 'Hi there! How can we help you plan your perfect trip?');
    if ( ! $wa_num ) return;
    $wa_url = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $wa_num) . '?text=' . rawurlencode($wa_msg);
    ?>
    <div id="tw-wa-widget" aria-label="Chat with us on WhatsApp">
        <!-- Popup bubble -->
        <div class="tw-wa-popup" id="tw-wa-popup" role="dialog" aria-label="WhatsApp chat" hidden>
            <div class="tw-wa-popup-head">
                <div class="tw-wa-popup-avatar" aria-hidden="true">
                    <svg viewBox="0 0 40 40" width="40" height="40"><circle cx="20" cy="20" r="20" fill="#25D366"/><path fill="#fff" d="M20 10a10 10 0 0 0-8.66 15l-1.3 3.87 3.98-1.27A10 10 0 1 0 20 10zm0 18.18a8.18 8.18 0 1 1 0-16.36 8.18 8.18 0 0 1 0 16.36z"/><path fill="#fff" d="M25.55 22.66c-.28-.14-1.65-.81-1.9-.9-.26-.1-.45-.14-.64.14-.19.27-.73.9-.9 1.09-.16.18-.33.2-.61.07-.28-.14-1.18-.44-2.25-1.4-.83-.74-1.39-1.65-1.55-1.93-.17-.28-.02-.43.12-.57.13-.12.28-.32.42-.48.14-.16.18-.27.28-.45.09-.18.05-.34-.02-.48-.07-.14-.64-1.54-.88-2.1-.23-.55-.47-.47-.64-.48H17c-.18 0-.46.07-.7.34-.24.27-.93.91-.93 2.22s.95 2.58 1.08 2.76c.14.18 1.87 2.85 4.53 3.99 1.73.75 2.41.81 3.28.68.53-.08 1.65-.67 1.88-1.32.23-.65.23-1.2.16-1.32-.06-.11-.24-.18-.52-.32z"/></svg>
                </div>
                <div class="tw-wa-popup-info">
                    <strong>1TripWiser</strong>
                    <span>Typically replies instantly</span>
                </div>
                <button class="tw-wa-popup-close" id="tw-wa-close" aria-label="Close chat"><i class="fi-rr-cross-small" aria-hidden="true"></i></button>
            </div>
            <div class="tw-wa-popup-body">
                <div class="tw-wa-bubble"><?php echo esc_html($wa_greet); ?></div>
            </div>
            <a href="<?php echo esc_url($wa_url); ?>" class="tw-wa-popup-cta" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                Start Chat on WhatsApp
            </a>
        </div>

        <!-- Trigger button -->
        <button class="tw-wa-btn" id="tw-wa-btn" aria-expanded="false" aria-controls="tw-wa-popup" title="Chat on WhatsApp">
            <span class="tw-wa-btn-icon tw-wa-btn-open" aria-hidden="true">
                <svg viewBox="0 0 32 32" width="28" height="28" fill="currentColor"><path d="M16 2a14 14 0 0 0-12.12 20.93L2 30l7.26-1.85A14 14 0 1 0 16 2zm0 26a12 12 0 0 1-6.18-1.71l-.44-.26-4.58 1.17 1.19-4.47-.29-.46A12 12 0 1 1 16 28z"/><path d="M21.49 18.55c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15s-.78.97-.95 1.17-.35.22-.65.07a8.16 8.16 0 0 1-2.4-1.48 9.03 9.03 0 0 1-1.66-2.07c-.17-.3 0-.46.13-.61l.43-.5c.14-.17.18-.3.28-.5s.05-.37-.02-.52-.68-1.64-.93-2.24c-.24-.59-.49-.51-.68-.52l-.57-.01c-.2 0-.52.07-.8.37S10 12.27 10 13.76s1.08 2.9 1.23 3.1 2.12 3.24 5.14 4.54c.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43s.25-1.3.18-1.43-.28-.2-.57-.35z"/></svg>
            </span>
            <span class="tw-wa-btn-icon tw-wa-btn-close" aria-hidden="true" style="display:none"><i class="fi-rr-cross-small" aria-hidden="true"></i></span>
            <span class="tw-wa-badge" aria-label="1 new message">1</span>
        </button>
    </div>

    <script>
    (function(){
        var btn    = document.getElementById('tw-wa-btn');
        var popup  = document.getElementById('tw-wa-popup');
        var close  = document.getElementById('tw-wa-close');
        var badge  = btn ? btn.querySelector('.tw-wa-badge') : null;
        var iconO  = btn ? btn.querySelector('.tw-wa-btn-open') : null;
        var iconC  = btn ? btn.querySelector('.tw-wa-btn-close') : null;
        if (!btn || !popup) return;

        // The open chat popup is tall enough to sit over the Instagram
        // button, which shares the same bottom-right corner — hide it
        // while the popup is up so the two never visually overlap. Looked
        // up fresh on each call rather than cached at parse time — this
        // script runs before the Instagram widget's markup (output later
        // in wp_footer) exists in the DOM yet.
        function openPopup() {
            popup.hidden = false;
            btn.setAttribute('aria-expanded','true');
            if (badge)  badge.style.display  = 'none';
            if (iconO)  iconO.style.display  = 'none';
            if (iconC)  iconC.style.display  = '';
            ['tw-ig-widget', 'tw-social-toggle', 'tw-fb-widget', 'tw-li-widget', 'tw-tw-widget', 'tw-pin-widget'].forEach(function (id) {
                var w = document.getElementById(id);
                if (w) w.classList.add('tw-social-hidden');
            });
        }
        function closePopup() {
            popup.hidden = true;
            btn.setAttribute('aria-expanded','false');
            if (iconO) iconO.style.display = '';
            if (iconC) iconC.style.display = 'none';
            ['tw-ig-widget', 'tw-social-toggle', 'tw-fb-widget', 'tw-li-widget', 'tw-tw-widget', 'tw-pin-widget'].forEach(function (id) {
                var w = document.getElementById(id);
                if (w) w.classList.remove('tw-social-hidden');
            });
        }

        btn.addEventListener('click', function(e){
            e.stopPropagation();
            popup.hidden ? openPopup() : closePopup();
        });
        if (close) close.addEventListener('click', closePopup);
        document.addEventListener('click', function(e){
            if (!popup.hidden && !document.getElementById('tw-wa-widget').contains(e.target)) closePopup();
        });

        // Auto-open after 6s on first visit
        if (!sessionStorage.getItem('tw_wa_shown')) {
            setTimeout(function(){ openPopup(); sessionStorage.setItem('tw_wa_shown','1'); }, 6000);
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'tw_whatsapp_widget');

// ============================================================
// FLOATING INSTAGRAM FOLLOW BUTTON
// Sits just above the WhatsApp widget in the bottom-right corner.
// ============================================================
function tw_instagram_float_button() {
    if ( is_admin() ) { return; }
    $ig_url = get_option( 'tw_instagram_url', 'https://www.instagram.com/1tripwiser/' );
    if ( ! $ig_url ) { return; }
    /* Nudge up only when the WhatsApp widget is actually rendered, so the
       button doesn't float in mid-air when no number is configured. */
    $wa_active = (bool) get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    ?>
    <div id="tw-ig-widget" class="<?php echo $wa_active ? 'has-wa' : ''; ?>">
        <a class="tw-ig-btn" href="<?php echo esc_url( $ig_url ); ?>" target="_blank" rel="noopener"
           aria-label="<?php esc_attr_e( 'Follow us on Instagram', 'mytheme' ); ?>">
            <i class="fab fa-instagram" aria-hidden="true"></i>
            <span class="tw-ig-btn-label"><?php esc_html_e( 'Follow us', 'mytheme' ); ?></span>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'tw_instagram_float_button');

// ============================================================
// FLOATING FACEBOOK FOLLOW BUTTON
// Stacks just above the Instagram button in the bottom-right corner.
// ============================================================
function tw_facebook_float_button() {
    if ( is_admin() ) { return; }
    $fb_url = get_option( 'tw_facebook_url', 'https://www.facebook.com/profile.php?id=100067013363504' );
    if ( ! $fb_url ) { return; }
    $wa_active = (bool) get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    ?>
    <div id="tw-fb-widget" class="<?php echo $wa_active ? 'has-wa' : ''; ?>">
        <a class="tw-fb-btn" href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener"
           aria-label="<?php esc_attr_e( 'Follow us on Facebook', 'mytheme' ); ?>">
            <i class="fab fa-facebook-f" aria-hidden="true"></i>
            <span class="tw-fb-btn-label"><?php esc_html_e( 'Follow us', 'mytheme' ); ?></span>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'tw_facebook_float_button');

// ============================================================
// FLOATING LINKEDIN FOLLOW BUTTON
// Stacks just above the Facebook button in the bottom-right corner.
// ============================================================
function tw_linkedin_float_button() {
    if ( is_admin() ) { return; }
    $li_url = get_option( 'tw_linkedin_url', 'https://www.linkedin.com/company/1tripwiser/' );
    if ( ! $li_url ) { return; }
    $wa_active = (bool) get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    ?>
    <div id="tw-li-widget" class="<?php echo $wa_active ? 'has-wa' : ''; ?>">
        <a class="tw-li-btn" href="<?php echo esc_url( $li_url ); ?>" target="_blank" rel="noopener"
           aria-label="<?php esc_attr_e( 'Follow us on LinkedIn', 'mytheme' ); ?>">
            <i class="fab fa-linkedin-in" aria-hidden="true"></i>
            <span class="tw-li-btn-label"><?php esc_html_e( 'Follow us', 'mytheme' ); ?></span>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'tw_linkedin_float_button');

// ============================================================
// FLOATING TWITTER / X FOLLOW BUTTON
// Stacks just above the LinkedIn button in the bottom-right corner.
// ============================================================
function tw_twitter_float_button() {
    if ( is_admin() ) { return; }
    $tw_url = get_option( 'tw_twitter_url', 'https://x.com/1Tripwiser' );
    if ( ! $tw_url ) { return; }
    $wa_active = (bool) get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    ?>
    <div id="tw-tw-widget" class="<?php echo $wa_active ? 'has-wa' : ''; ?>">
        <a class="tw-tw-btn" href="<?php echo esc_url( $tw_url ); ?>" target="_blank" rel="noopener"
           aria-label="<?php esc_attr_e( 'Follow us on X (Twitter)', 'mytheme' ); ?>">
            <i class="fab fa-x-twitter" aria-hidden="true"></i>
            <span class="tw-tw-btn-label"><?php esc_html_e( 'Follow us', 'mytheme' ); ?></span>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'tw_twitter_float_button');

// ============================================================
// FLOATING PINTEREST FOLLOW BUTTON
// Stacks just above the Twitter/X button in the bottom-right corner.
// ============================================================
function tw_pinterest_float_button() {
    if ( is_admin() ) { return; }
    $pin_url = get_option( 'tw_pinterest_url', 'https://in.pinterest.com/1tripwiser/' );
    if ( ! $pin_url ) { return; }
    $wa_active = (bool) get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    ?>
    <div id="tw-pin-widget" class="<?php echo $wa_active ? 'has-wa' : ''; ?>">
        <a class="tw-pin-btn" href="<?php echo esc_url( $pin_url ); ?>" target="_blank" rel="noopener"
           aria-label="<?php esc_attr_e( 'Follow us on Pinterest', 'mytheme' ); ?>">
            <i class="fab fa-pinterest-p" aria-hidden="true"></i>
            <span class="tw-pin-btn-label"><?php esc_html_e( 'Follow us', 'mytheme' ); ?></span>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'tw_pinterest_float_button');

// ============================================================
// FLOATING SOCIAL TOGGLE — arrow that expands/collapses Facebook,
// LinkedIn, X and Pinterest. Instagram stays visible on its own; only
// the "extra" buttons hide behind this so the corner doesn't get
// crowded.
// ============================================================
function tw_social_toggle_button() {
    if ( is_admin() ) { return; }
    $fb_url  = get_option( 'tw_facebook_url', 'https://www.facebook.com/profile.php?id=100067013363504' );
    $li_url  = get_option( 'tw_linkedin_url', 'https://www.linkedin.com/company/1tripwiser/' );
    $tw_url  = get_option( 'tw_twitter_url', 'https://x.com/1Tripwiser' );
    $pin_url = get_option( 'tw_pinterest_url', 'https://in.pinterest.com/1tripwiser/' );
    if ( ! $fb_url && ! $li_url && ! $tw_url && ! $pin_url ) { return; }
    $wa_active = (bool) get_option( 'tw_wa_widget_number', get_option( 'tw_pat_whatsapp', '' ) );
    ?>
    <div id="tw-social-toggle" class="<?php echo $wa_active ? 'has-wa' : ''; ?>">
        <button type="button" class="tw-social-toggle-btn" id="tw-social-toggle-btn" aria-expanded="false"
                aria-label="<?php esc_attr_e( 'Show more ways to follow us', 'mytheme' ); ?>">
            <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
        </button>
    </div>
    <script>
    (function () {
        var btn = document.getElementById('tw-social-toggle-btn');
        if (!btn) return;
        var targets = ['tw-fb-widget', 'tw-li-widget', 'tw-tw-widget', 'tw-pin-widget'];
        btn.addEventListener('click', function () {
            var expanding = btn.getAttribute('aria-expanded') !== 'true';
            btn.setAttribute('aria-expanded', expanding ? 'true' : 'false');
            btn.classList.toggle('is-open', expanding);
            targets.forEach(function (id) {
                var w = document.getElementById(id);
                if (w) w.classList.toggle('tw-social-expanded', expanding);
            });
        });
    })();
    </script>
    <?php
}
add_action('wp_footer', 'tw_social_toggle_button');

// ============================================================
// WHATSAPP CLOUD API — send message when inquiry submitted
// ============================================================
function tw_send_whatsapp_inquiry_notification( $post_id, $data ) {
    $api_token     = get_option('tw_wa_api_token', '');
    $phone_id      = get_option('tw_wa_phone_id', '');
    if ( ! $api_token || ! $phone_id ) return; // API not configured

    $to_number = preg_replace('/[^0-9]/', '', $data['phone'] ?? '');
    if ( strlen($to_number) < 7 ) return; // no valid phone

    // Add country code if missing (default India +91)
    if ( strlen($to_number) === 10 && substr($to_number, 0, 1) !== '9' ) {
        $to_number = '91' . $to_number;
    } elseif ( strlen($to_number) === 10 ) {
        $to_number = '91' . $to_number;
    }

    $template_name = get_option('tw_wa_template_name', '');
    $template_lang = get_option('tw_wa_template_lang', 'en');

    if ( $template_name ) {
        // Send via approved template
        $body = json_encode(array(
            'messaging_product' => 'whatsapp',
            'to'                => $to_number,
            'type'              => 'template',
            'template'          => array(
                'name'       => $template_name,
                'language'   => array('code' => $template_lang),
                'components' => array(
                    array(
                        'type'       => 'body',
                        'parameters' => array(
                            array('type' => 'text', 'text' => sanitize_text_field($data['name'] ?? 'Traveller')),
                            array('type' => 'text', 'text' => sanitize_text_field($data['destination'] ?? 'your destination')),
                        ),
                    ),
                ),
            ),
        ));
    } else {
        // Free-form text (works only if user messaged the business within 24h)
        $msg = "Hi " . ($data['name'] ?? 'there') . "!\n\n"
             . "Thanks for your trip inquiry with *1TripWiser*! Here's your summary:\n\n"
             . "*Destination:* " . ($data['destination'] ?? '-') . "\n"
             . "*Travel Date:* "  . ($data['date'] ?? '-') . "\n"
             . "*Duration:* "     . ($data['duration'] ?? '-') . "\n"
             . "*Budget:* "        . ($data['budget'] ?? '-') . "\n"
             . "*Adults:* "        . ($data['adults'] ?? '1') . "  |  *Children:* " . ($data['children'] ?? '0') . "\n"
             . "*Trip Type:* "     . ($data['trip_type'] ?? '-') . "\n\n"
             . "Our team will send your personalised quotation shortly. Stay tuned!\n\n"
             . "_— 1TripWiser Team_";

        $body = json_encode(array(
            'messaging_product' => 'whatsapp',
            'to'                => $to_number,
            'type'              => 'text',
            'text'              => array('preview_url' => false, 'body' => $msg),
        ));
    }

    wp_remote_post(
        'https://graph.facebook.com/v19.0/' . $phone_id . '/messages',
        array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type'  => 'application/json',
            ),
            'body'    => $body,
            'timeout' => 10,
        )
    );
}

// Hook into the existing inquiry save
add_action('tw_trip_inquiry_saved', 'tw_send_whatsapp_inquiry_notification', 10, 2);

// ============================================================
// GOOGLE SHEET SYNC — Plan My Trip submissions only.
// Package-page enquiries (mytheme_handle_package_inquiry) never fire
// tw_trip_inquiry_saved, so they're structurally excluded from this.
//
// Called synchronously (blocking) on the AJAX request itself. Two
// alternatives were tried and rejected: a non-blocking wp_remote_post()
// doesn't reliably finish its TLS handshake before the process moves on
// (the request just never lands, with no error anywhere), and deferring
// to WP-Cron is no better here — this environment's wp-cron.php doesn't
// reliably execute pending events even when hit directly. A blocking
// call costs the form a little latency (Apps Script typically responds
// in under a second once warm), but it's the one approach that's
// actually verifiable — it completes, or it reports why it didn't.
// ============================================================
function tw_push_trip_inquiry_to_sheet( $post_id, $data ) {
    $webhook = get_option( 'tw_pat_sheet_webhook_url', '' );
    if ( ! $webhook ) { return; }

    $response = wp_remote_post( $webhook, array(
        'body'     => wp_json_encode( $data ),
        'headers'  => array( 'Content-Type' => 'application/json' ),
        'timeout'  => 15,
        'blocking' => true,
    ) );

    $log = array( 'time' => current_time( 'mysql' ), 'post_id' => $post_id );
    if ( is_wp_error( $response ) ) {
        $log['status']  = 'error';
        $log['message'] = $response->get_error_message();
    } else {
        $code = wp_remote_retrieve_response_code( $response );
        if ( $code >= 200 && $code < 300 ) {
            $log['status']  = 'success';
            $log['message'] = 'HTTP ' . $code;
        } else {
            $log['status']  = 'error';
            $log['message'] = 'HTTP ' . $code . ' — ' . wp_trim_words( wp_remote_retrieve_body( $response ), 20 );
        }
    }
    update_option( 'tw_pat_sheet_last_sync', $log, false );
}
add_action( 'tw_trip_inquiry_saved', 'tw_push_trip_inquiry_to_sheet', 10, 2 );

// ============================================================
// SHARED HELPER — settings page chrome (header + breadcrumb)
// ============================================================
function tw_settings_page_header( $title, $icon, $description = '' ) {
    $logo_html = '<span style="display:inline-flex;align-items:center;gap:10px;font-size:1.5rem;font-weight:800;color:#0d1526;font-family:var(--accent-font, \'Saira\', sans-serif);margin-bottom:4px"><span style="color:#D83550">1</span>TRIPWISER</span>';
    ?>
    <style>
    .tw-admin-wrap { max-width:900px; }
    .tw-admin-header { background:linear-gradient(135deg,#0d1526 0%,#0a1e30 100%); border-radius:10px; padding:28px 32px; margin-bottom:28px; display:flex; align-items:center; gap:20px; }
    .tw-admin-header-icon { font-size:2.4rem; flex-shrink:0; }
    .tw-admin-header h1 { color:#fff !important; font-size:1.45rem !important; margin:0 0 4px !important; padding:0 !important; }
    .tw-admin-header p { color:rgba(255,255,255,0.6); margin:0; font-size:0.9rem; }
    .tw-admin-card { background:#fff; border:1px solid #e0e6ed; border-radius:10px; margin-bottom:24px; overflow:hidden; }
    .tw-admin-card-head { background:#f8fafc; border-bottom:1px solid #e0e6ed; padding:16px 24px; display:flex; align-items:center; gap:10px; }
    .tw-admin-card-head h2 { margin:0; font-size:1rem; color:#0d1526; }
    .tw-admin-card-head p { margin:4px 0 0; font-size:0.82rem; color:#667085; }
    .tw-admin-card-body { padding:20px 24px; }
    .tw-admin-card-body .form-table th { padding:16px 10px 16px 0; width:220px; }
    .tw-admin-note { border-radius:8px; padding:14px 18px; margin-bottom:20px; font-size:0.88rem; line-height:1.6; }
    .tw-admin-note.info  { background:#e8f4fd; border:1px solid #bee3f8; color:#1a365d; }
    .tw-admin-note.warn  { background:#fff8e1; border:1px solid #ffc107; color:#7b5000; }
    .tw-admin-note.success { background:#f0fff4; border:1px solid #9ae6b4; color:#1c4532; }
    .tw-admin-quicklinks { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:14px; }
    .tw-admin-ql { display:flex; align-items:center; gap:12px; background:#f8fafc; border:1px solid #e0e6ed; border-radius:9px; padding:14px 16px; text-decoration:none; color:#0d1526; font-weight:700; font-size:0.9rem; transition:border-color .2s,background .2s; }
    .tw-admin-ql:hover { border-color:#1B93B0; background:#eef7fb; color:#1B93B0; }
    .tw-admin-ql-icon { font-size:1.4rem; flex-shrink:0; }
    </style>
    <div class="wrap tw-admin-wrap">
        <div class="tw-admin-header">
            <div class="tw-admin-header-icon"><?php echo $icon; ?></div>
            <div>
                <div><?php echo $logo_html; ?></div>
                <h1><?php echo esc_html($title); ?></h1>
                <?php if ($description) : ?><p><?php echo esc_html($description); ?></p><?php endif; ?>
            </div>
        </div>
    <?php
}
function tw_settings_page_footer() {
    echo '</div>'; // .wrap
}

// ============================================================
// OVERVIEW DASHBOARD PAGE
// ============================================================
function tw_render_overview_page() {
    tw_settings_page_header('Settings Overview', '<i class="fi-rr-chart-histogram" aria-hidden="true"></i>', 'All site settings at a glance — click any card to jump straight there.');
    $inquiry_count = wp_count_posts('trip_inquiry')->publish ?? 0;
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2><i class="fi-rr-bolt" aria-hidden="true"></i> Quick Actions</h2></div>
        <div class="tw-admin-card-body">
            <div class="tw-admin-quicklinks">
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-clapperboard" aria-hidden="true"></i></span><span>Hero Banner<br><small style="font-weight:500;color:#667085">Video / image background</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-plan-trip-settings')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-plane" aria-hidden="true"></i></span><span>Plan A Trip Page<br><small style="font-weight:500;color:#667085">Hero text &amp; WhatsApp number</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-blog-settings')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-note" aria-hidden="true"></i></span><span>Blog &amp; Affiliates Page<br><small style="font-weight:500;color:#667085">Page hero &amp; partner banner</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-luxury-hero-settings')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-gem" aria-hidden="true"></i></span><span>LUXURY Page<br><small style="font-weight:500;color:#667085">Scroll-scrubbed hero video</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-widget-settings')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-comment" aria-hidden="true"></i></span><span>WhatsApp Widget<br><small style="font-weight:500;color:#667085">Floating chat button</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-api-settings')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-brain-circuit" aria-hidden="true"></i></span><span>WhatsApp API<br><small style="font-weight:500;color:#667085">Auto quotation messages</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=trip-inquiries')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-clipboard-list" aria-hidden="true"></i></span><span>Trip Inquiries<br><small style="font-weight:500;color:#667085"><?php echo esc_html($inquiry_count); ?> submissions</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('edit.php')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-memo" aria-hidden="true"></i></span><span>Blog Posts<br><small style="font-weight:500;color:#667085">Write &amp; manage articles</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('edit.php?post_type=tw_affiliate')); ?>">
                    <span class="tw-admin-ql-icon"><i class="fi-rr-link" aria-hidden="true"></i></span><span>Affiliate Links<br><small style="font-weight:500;color:#667085">Manage partner links</small></span>
                </a>
            </div>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2><i class="fi-rr-clipboard-list" aria-hidden="true"></i> Current Configuration Summary</h2></div>
        <div class="tw-admin-card-body">
            <table class="widefat striped" style="border:none">
                <tbody>
                    <tr><td style="width:260px;font-weight:700">Hero Video</td><td><?php $v=get_option('tw_hero_video_url',''); echo $v ? '<a href="'.esc_url($v).'" target="_blank">'.esc_html(substr($v,0,60)).'…</a>' : '<span style="color:#999">Not set — using dark background</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Hero Image</td><td><?php $i=get_option('tw_hero_image_url',''); echo $i ? '<a href="'.esc_url($i).'" target="_blank">Set <i class="fi-rr-check" aria-hidden="true"></i></a>' : '<span style="color:#999">Not set</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Instagram Feed ID</td><td><?php echo esc_html(get_option('tw_instagram_feed_id', 1)); ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">LUXURY Page Video</td><td><?php $lv=get_option('tw_luxury_hero_video_url',''); echo $lv ? '<a href="'.esc_url($lv).'" target="_blank">'.esc_html(substr($lv,0,60)).'…</a>' : '<span style="color:#999">Using theme default clip</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-luxury-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Plan A Trip WhatsApp</td><td><?php echo esc_html(get_option('tw_pat_whatsapp','Not set')); ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-plan-trip-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">WhatsApp Widget Number</td><td><?php $n=get_option('tw_wa_widget_number',get_option('tw_pat_whatsapp','')); echo $n ? esc_html($n) : '<span style="color:#999">Not set</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-widget-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">WhatsApp Cloud API</td><td><?php echo get_option('tw_wa_api_token','') ? '<span style="color:green"><i class="fi-rr-check" aria-hidden="true"></i> Configured</span>' : '<span style="color:#999">Not configured</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-api-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Blog Page Title</td><td><?php echo esc_html(get_option('tw_blog_title','Blogs + Affiliates')); ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-blog-settings')); ?>">Edit →</a></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    tw_settings_page_footer();
}

// ============================================================
// HERO BANNER SETTINGS PAGE
// ============================================================
function tw_render_hero_settings_page() {
    wp_enqueue_media(); // load WP media picker scripts
    tw_settings_page_header('Hero Banner', '<i class="fi-rr-clapperboard" aria-hidden="true"></i>', 'Control the video or image that plays behind the homepage hero section.');
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div>
                <h2>Background Media</h2>
                <p>Video takes priority. If no video is set, the image is used. If neither is set, a dark gradient is shown.</p>
            </div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tripwiser_hero_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_hero_video_url">Video URL</label></th>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px">
                                <input type="url" id="tw_hero_video_url" name="tw_hero_video_url"
                                       value="<?php echo esc_attr(get_option('tw_hero_video_url','')); ?>"
                                       style="flex:1;min-width:300px" placeholder="YouTube URL  or  direct .mp4 / .webm URL">
                                <button type="button" class="button button-secondary tw-media-pick" data-target="tw_hero_video_url" data-type="video">
                                    <i class="fi-rr-folder" aria-hidden="true"></i> Select from Media Library
                                </button>
                            </div>
                            <p class="description">
                                <strong>Recommended:</strong> Upload an .mp4 file to <a href="<?php echo esc_url(admin_url('media-new.php')); ?>" target="_blank">Media → Add New</a>, then click <em>Select from Media Library</em> above — no YouTube player, no controls, perfect cover fill.<br>
                                Also accepts a <strong>YouTube link</strong> (youtu.be or youtube.com/watch?v=). Either way the video plays muted, looped and auto-started.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_hero_image_url">Fallback Image URL</label></th>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px">
                                <input type="url" id="tw_hero_image_url" name="tw_hero_image_url"
                                       value="<?php echo esc_attr(get_option('tw_hero_image_url','')); ?>"
                                       style="flex:1;min-width:300px" placeholder="https://yoursite.com/hero-image.jpg">
                                <button type="button" class="button button-secondary tw-media-pick" data-target="tw_hero_image_url" data-type="image">
                                    <i class="fi-rr-picture" aria-hidden="true"></i> Select from Media Library
                                </button>
                            </div>
                            <p class="description">Used when no video is set.</p>
                            <?php $img = get_option('tw_hero_image_url',''); if ($img) : ?>
                            <div style="margin-top:12px">
                                <img src="<?php echo esc_url($img); ?>" style="max-width:360px;border-radius:8px;border:1px solid #dde5ef;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                                <p style="margin:6px 0 0;font-size:0.8rem;color:#667085">Current fallback image</p>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_hero_mobile_image_url">Mobile Image URL <span style="font-weight:400;color:#1B93B0">(mobile only)</span></label></th>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px">
                                <input type="url" id="tw_hero_mobile_image_url" name="tw_hero_mobile_image_url"
                                       value="<?php echo esc_attr(get_option('tw_hero_mobile_image_url','')); ?>"
                                       style="flex:1;min-width:300px" placeholder="https://yoursite.com/hero-mobile.jpg">
                                <button type="button" class="button button-secondary tw-media-pick" data-target="tw_hero_mobile_image_url" data-type="image">
                                    <i class="fi-rr-picture" aria-hidden="true"></i> Select from Media Library
                                </button>
                            </div>
                            <p class="description">
                                Shown on phones (≤768px) <strong>instead of the video</strong> — YouTube embeds look stretched on portrait screens.
                                A vertical / portrait image works best. <em>Leave blank to show just the dark gradient background on mobile.</em>
                            </p>
                            <?php $mimg = get_option('tw_hero_mobile_image_url',''); if ($mimg) : ?>
                            <div style="margin-top:12px">
                                <img src="<?php echo esc_url($mimg); ?>" style="max-width:200px;border-radius:8px;border:1px solid #dde5ef;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                                <p style="margin:6px 0 0;font-size:0.8rem;color:#667085">Current mobile image</p>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <div class="tw-admin-card" style="margin-top:24px;margin-bottom:0">
                    <div class="tw-admin-card-head">
                        <div>
                            <h2>Floating Images</h2>
                            <p>Three small photos that float around the hero video, tilted and gently animated. Each appears from a dot and scales up to full size on page load. Leave any blank to hide that one.</p>
                        </div>
                    </div>
                    <div class="tw-admin-card-body">
                        <table class="form-table">
                            <?php
                            $tw_float_fields = array(
                                'tw_hero_float_img_tr' => 'Top Right',
                                'tw_hero_float_img_ml' => 'Middle Left',
                                'tw_hero_float_img_br' => 'Bottom Right',
                            );
                            foreach ( $tw_float_fields as $tw_ff_key => $tw_ff_label ) :
                                $tw_ff_val = get_option( $tw_ff_key, '' );
                            ?>
                            <tr>
                                <th><label for="<?php echo esc_attr( $tw_ff_key ); ?>"><?php echo esc_html( $tw_ff_label ); ?></label></th>
                                <td>
                                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px">
                                        <input type="url" id="<?php echo esc_attr( $tw_ff_key ); ?>" name="<?php echo esc_attr( $tw_ff_key ); ?>"
                                               value="<?php echo esc_attr( $tw_ff_val ); ?>"
                                               style="flex:1;min-width:300px" placeholder="https://yoursite.com/floating-image.jpg">
                                        <button type="button" class="button button-secondary tw-media-pick" data-target="<?php echo esc_attr( $tw_ff_key ); ?>" data-type="image">
                                            <i class="fi-rr-picture" aria-hidden="true"></i> Select from Media Library
                                        </button>
                                    </div>
                                    <?php if ( $tw_ff_val ) : ?>
                                    <div style="margin-top:12px">
                                        <img src="<?php echo esc_url( $tw_ff_val ); ?>" style="max-width:160px;border-radius:8px;border:1px solid #dde5ef;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
                <div class="tw-admin-note info" style="margin-top:8px">
                    <i class="fi-rr-bulb" aria-hidden="true"></i> <strong>Tip:</strong> Upload your video via <a href="<?php echo esc_url(admin_url('media-new.php')); ?>">Media → Add New</a>, then use <em>Select from Media Library</em> — clean full-screen background, zero player controls. Landscape 1920×1080 .mp4 works best.
                </div>
                <div class="tw-admin-card" style="margin-top:24px;margin-bottom:0">
                    <div class="tw-admin-card-head">
                        <div>
                            <h2>Instagram Feed</h2>
                            <p>Controls the feed used in the homepage Instagram section.</p>
                        </div>
                    </div>
                    <div class="tw-admin-card-body">
                        <table class="form-table">
                            <tr>
                                <th><label for="tw_instagram_feed_id">Feed ID</label></th>
                                <td>
                                    <input type="number" min="1" step="1" id="tw_instagram_feed_id" name="tw_instagram_feed_id"
                                           value="<?php echo esc_attr(get_option('tw_instagram_feed_id', 1)); ?>"
                                           class="small-text">
                                    <p class="description">
                                        This renders the shortcode as <code>[instagram-feed feed="ID"]</code>. Update this when the Instagram Feed plugin feed ID changes.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php submit_button('Save Hero Settings', 'primary', 'submit', true, ['style'=>'margin-top:8px']); ?>
            </form>
        </div>
    </div>
    <script>
    (function ($) {
        var frames = {};
        $('.tw-media-pick').on('click', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var mtype  = $(this).data('type');
            if ( frames[target] ) { frames[target].open(); return; }
            frames[target] = wp.media({
                title   : mtype === 'video' ? 'Select Hero Video' : 'Select Hero Image',
                button  : { text: mtype === 'video' ? 'Use This Video' : 'Use This Image' },
                library : { type: mtype },
                multiple: false,
            });
            frames[target].on('select', function () {
                var att = frames[target].state().get('selection').first().toJSON();
                $('#' + target).val(att.url);
            });
            frames[target].open();
        });
    }(jQuery));
    </script>
    <?php tw_settings_page_footer();
}

// ============================================================
// LUXURY PAGE SETTINGS
// ============================================================
function tw_render_luxury_hero_settings_page() {
    wp_enqueue_media();
    $tw_luxury_default_video = get_template_directory_uri() . '/assets/video/tw-luxury-hero.mp4';
    tw_settings_page_header('LUXURY Page', '<i class="fi-rr-gem" aria-hidden="true"></i>', 'Swap the scroll-scrubbed background video on the /luxury/ page — no code changes needed.');
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div>
                <h2>Scroll-Scrubbed Hero Video</h2>
                <p>This clip plays frame-by-frame as visitors scroll through the hero, "why" cards and journeys grid — it never plays on its own timeline.</p>
            </div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tripwiser_luxury_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_luxury_hero_video_url">Video URL</label></th>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px">
                                <input type="url" id="tw_luxury_hero_video_url" name="tw_luxury_hero_video_url"
                                       value="<?php echo esc_attr(get_option('tw_luxury_hero_video_url','')); ?>"
                                       style="flex:1;min-width:300px" placeholder="<?php echo esc_attr($tw_luxury_default_video); ?>">
                                <button type="button" class="button button-secondary tw-media-pick" data-target="tw_luxury_hero_video_url" data-type="video">
                                    <i class="fi-rr-folder" aria-hidden="true"></i> Select from Media Library
                                </button>
                            </div>
                            <p class="description">
                                Leave blank to use the theme's built-in default clip.<br>
                                <strong>For smooth scrubbing:</strong> re-encode as all-intra H.264 before uploading (every frame a keyframe —
                                <code>ffmpeg -i in.mp4 -vf "scale=1280:-2" -an -c:v libx264 -crf 23 -g 1 -keyint_min 1 -sc_threshold 0 -pix_fmt yuv420p -movflags +faststart out.mp4</code>).
                                A normal export still works, but seeking will feel less smooth while scrolling. H.265/HEVC files will not play in Chrome — export H.264.
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save LUXURY Settings', 'primary', 'submit', true, ['style'=>'margin-top:8px']); ?>
            </form>
        </div>
    </div>
    <script>
    (function ($) {
        var frames = {};
        $('.tw-media-pick').on('click', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var mtype  = $(this).data('type');
            if ( frames[target] ) { frames[target].open(); return; }
            frames[target] = wp.media({
                title   : 'Select LUXURY Hero Video',
                button  : { text: 'Use This Video' },
                library : { type: mtype },
                multiple: false,
            });
            frames[target].on('select', function () {
                var att = frames[target].state().get('selection').first().toJSON();
                $('#' + target).val(att.url);
            });
            frames[target].open();
        });
    }(jQuery));
    </script>
    <?php tw_settings_page_footer();
}

// ============================================================
// PLAN A TRIP PAGE SETTINGS
// ============================================================
function tw_render_plan_trip_settings_page() {
    tw_settings_page_header('Plan A Trip Page', '<i class="fi-rr-plane" aria-hidden="true"></i>', 'Edit the hero text and WhatsApp number for the Plan A Trip form page.');
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Hero Section Text</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_pat_hero_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_pat_kicker">Kicker (small label above title)</label></th>
                        <td>
                            <input type="text" id="tw_pat_kicker" name="tw_pat_kicker"
                                   value="<?php echo esc_attr(get_option('tw_pat_kicker','YOUR PERSONALISED TRIP PLANNER')); ?>"
                                   class="large-text">
                            <p class="description">Short uppercase text shown above the main heading. E.g. "YOUR PERSONALISED TRIP PLANNER"</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_pat_title">Main Heading</label></th>
                        <td>
                            <input type="text" id="tw_pat_title" name="tw_pat_title"
                                   value="<?php echo esc_attr(get_option('tw_pat_title','Plan Your Dream Trip')); ?>"
                                   class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_pat_subtitle">Subheading / Description</label></th>
                        <td>
                            <textarea id="tw_pat_subtitle" name="tw_pat_subtitle" class="large-text" rows="3"><?php echo esc_textarea(get_option('tw_pat_subtitle',"Tell us your dream destination, travel dates, and budget — we'll craft a personalised itinerary just for you.")); ?></textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Page Text'); ?>
            </form>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div><h2>WhatsApp Number</h2><p>The number customers are directed to when they click "Get My Quote on WhatsApp".</p></div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_pat_whatsapp_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_pat_whatsapp">WhatsApp Number</label></th>
                        <td>
                            <input type="text" id="tw_pat_whatsapp" name="tw_pat_whatsapp"
                                   value="<?php echo esc_attr(get_option('tw_pat_whatsapp','919999999999')); ?>"
                                   class="regular-text" placeholder="919876543210">
                            <p class="description">
                                Country code + number with <strong>no</strong> spaces, dashes, or + sign.<br>
                                Example for India: <code>919876543210</code> (91 = India code, then 10-digit number)
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save WhatsApp Number'); ?>
            </form>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div><h2>Google Sheet Sync</h2><p>Every Plan My Trip submission gets appended as a new row. Package-page enquiries are saved separately and never sent here.</p></div>
        </div>
        <div class="tw-admin-card-body">
            <?php $tw_sheet_log = get_option( 'tw_pat_sheet_last_sync' ); if ( $tw_sheet_log ) : ?>
            <div class="tw-admin-note <?php echo 'success' === $tw_sheet_log['status'] ? 'success' : 'warn'; ?>">
                <strong>Last sync attempt</strong> (<?php echo esc_html( $tw_sheet_log['time'] ); ?>, inquiry #<?php echo esc_html( $tw_sheet_log['post_id'] ); ?>):
                <?php echo 'success' === $tw_sheet_log['status'] ? 'reached the sheet successfully.' : esc_html( $tw_sheet_log['message'] ); ?>
            </div>
            <?php endif; ?>
            <div class="tw-admin-note info">
                <strong>One-time setup</strong> (do this in the Google Sheet itself, not here):
                <ol style="margin:8px 0 0 20px;">
                    <li>Open your sheet → <strong>Extensions → Apps Script</strong>.</li>
                    <li>Delete anything in the editor and paste the script below.</li>
                    <li><strong>Deploy → New deployment</strong> → type <strong>Web app</strong> → Execute as "Me", Who has access "Anyone" → <strong>Deploy</strong>.</li>
                    <li>Copy the Web app URL it gives you and paste it into the field below.</li>
                </ol>
            </div>
            <p><strong>Apps Script to paste:</strong></p>
            <textarea readonly onclick="this.select()" rows="18" class="large-text code" style="font-family:Consolas,Monaco,monospace;font-size:12px;">function doPost(e) {
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheets()[0];
  var data = JSON.parse(e.postData.contents);

  var headers = ['Timestamp', 'Name', 'Phone', 'Email', 'Destination', 'Travel Date', 'Duration', 'Time Preference', 'Trip Type', 'Adults', 'Children', 'Budget', 'Departing From', 'Notes'];
  if (sheet.getLastRow() === 0) {
    sheet.appendRow(headers);
  }

  sheet.appendRow([
    new Date(),
    data.name || '',
    data.phone || '',
    data.email || '',
    data.destination || '',
    data.date || '',
    data.duration || '',
    data.time_pref || '',
    data.trip_type || '',
    data.adults || '',
    data.children || '',
    data.budget || '',
    data.departing || '',
    data.notes || ''
  ]);

  return ContentService.createTextOutput(JSON.stringify({ status: 'ok' })).setMimeType(ContentService.MimeType.JSON);
}</textarea>
            <form method="post" action="options.php" style="margin-top:16px;">
                <?php settings_fields('tw_pat_sheet_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_pat_sheet_webhook_url">Web App URL</label></th>
                        <td>
                            <input type="url" id="tw_pat_sheet_webhook_url" name="tw_pat_sheet_webhook_url"
                                   value="<?php echo esc_attr(get_option('tw_pat_sheet_webhook_url','')); ?>"
                                   class="large-text" placeholder="https://script.google.com/macros/s/.../exec">
                            <p class="description">Leave blank to turn syncing off.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Web App URL'); ?>
            </form>
        </div>
    </div>
    <?php tw_settings_page_footer();
}

// ============================================================
// BLOG & AFFILIATES PAGE SETTINGS
// ============================================================
function tw_render_blog_settings_page() {
    tw_settings_page_header('Blog & Affiliates Page', '<i class="fi-rr-note" aria-hidden="true"></i>', 'Edit the hero banner text and affiliate partner section on the Blog & Affiliates page.');
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Page Hero Text</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_blog_hero_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_blog_kicker">Kicker (small label above title)</label></th>
                        <td><input type="text" id="tw_blog_kicker" name="tw_blog_kicker" value="<?php echo esc_attr(get_option('tw_blog_kicker','TRAVEL GUIDES & AFFILIATE PICKS')); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th><label for="tw_blog_title">Main Heading</label></th>
                        <td><input type="text" id="tw_blog_title" name="tw_blog_title" value="<?php echo esc_attr(get_option('tw_blog_title','Blogs + Affiliates')); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th><label for="tw_blog_subtitle">Subheading / Description</label></th>
                        <td><textarea id="tw_blog_subtitle" name="tw_blog_subtitle" class="large-text" rows="3"><?php echo esc_textarea(get_option('tw_blog_subtitle','Honest travel guides. Trusted tools. Every link we share is something we actually use and believe in.')); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button('Save Hero Text'); ?>
            </form>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Affiliate Partner Banner</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_blog_aff_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_blog_aff_heading">Banner Heading</label></th>
                        <td><input type="text" id="tw_blog_aff_heading" name="tw_blog_aff_heading" value="<?php echo esc_attr(get_option('tw_blog_aff_heading','Our Trusted Travel Partners')); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th><label for="tw_blog_aff_text">Banner Body Text</label></th>
                        <td><textarea id="tw_blog_aff_text" name="tw_blog_aff_text" class="large-text" rows="3"><?php echo esc_textarea(get_option('tw_blog_aff_text',"We partner with travel platforms we personally trust. When you book through our links, you support our free content at no extra cost to you.")); ?></textarea></td>
                    </tr>
                </table>
                <div class="tw-admin-note info">
                    <i class="fi-rr-bulb" aria-hidden="true"></i> <strong>Individual affiliate partner links</strong> (Booking.com, Skyscanner, etc.) are managed separately under the
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=tw_affiliate')); ?>"><strong>Affiliate Links</strong></a> menu in the sidebar.
                </div>
                <?php submit_button('Save Banner Text'); ?>
            </form>
        </div>
    </div>
    <?php tw_settings_page_footer();
}

// ============================================================
// WHATSAPP WIDGET SETTINGS PAGE
// ============================================================
function tw_render_wa_widget_settings_page() {
    tw_settings_page_header('WhatsApp Chat Widget', '<i class="fi-rr-comment" aria-hidden="true"></i>', 'The green floating chat button that appears on every page of your website.');
    ?>
    <div class="tw-admin-note success">
        <i class="fi-rr-check-circle" aria-hidden="true"></i> <strong>No API needed.</strong> Just enter your WhatsApp number below and the widget will work immediately — visitors click the button and are taken straight to a WhatsApp chat with you.
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Widget Settings</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_wa_widget_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_wa_widget_number">Your WhatsApp Business Number</label></th>
                        <td>
                            <input type="text" id="tw_wa_widget_number" name="tw_wa_widget_number"
                                   value="<?php echo esc_attr(get_option('tw_wa_widget_number', get_option('tw_pat_whatsapp',''))); ?>"
                                   class="regular-text" placeholder="919876543210">
                            <p class="description">Country code + number, <strong>no + or spaces</strong>. India example: <code>919876543210</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_wa_widget_greeting">Greeting Message in Popup</label></th>
                        <td>
                            <input type="text" id="tw_wa_widget_greeting" name="tw_wa_widget_greeting"
                                   value="<?php echo esc_attr(get_option('tw_wa_widget_greeting','Hi there! How can we help you plan your perfect trip?')); ?>"
                                   class="large-text">
                            <p class="description">The chat bubble message visitors see when the popup opens. Keep it friendly and short.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_wa_widget_message">Pre-filled Message for Visitor</label></th>
                        <td>
                            <input type="text" id="tw_wa_widget_message" name="tw_wa_widget_message"
                                   value="<?php echo esc_attr(get_option('tw_wa_widget_message','Hi! I have a question about a trip.')); ?>"
                                   class="large-text">
                            <p class="description">This text is pre-typed in the visitor's WhatsApp chat window when they open the link. They can edit it before sending.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Widget Settings'); ?>
            </form>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>How It Works</h2></div>
        <div class="tw-admin-card-body">
            <ol style="line-height:2;color:#444;padding-left:20px">
                <li>A green WhatsApp button appears fixed in the <strong>bottom-right corner</strong> of every page.</li>
                <li>After <strong>6 seconds</strong>, the popup automatically opens once per session (uses browser sessionStorage).</li>
                <li>Clicking "Start Chat on WhatsApp" opens <strong>WhatsApp Web or the app</strong> with the pre-filled message ready to send.</li>
                <li>The widget also shows a <strong>notification badge</strong> to draw attention.</li>
            </ol>
        </div>
    </div>
    <?php tw_settings_page_footer();
}

// ============================================================
// WHATSAPP CLOUD API SETTINGS PAGE
// ============================================================
function tw_render_wa_api_settings_page() {
    tw_settings_page_header('WhatsApp Cloud API', '<i class="fi-rr-brain-circuit" aria-hidden="true"></i>', 'Send automatic trip inquiry confirmations directly to a customer\'s WhatsApp when they fill the Plan A Trip form.');
    ?>
    <div class="tw-admin-note warn">
        <i class="fi-rr-triangle-warning" aria-hidden="true"></i> <strong>Requires a Meta Business API account.</strong> This is separate from the chat widget above — it uses the official WhatsApp Business Cloud API to <em>proactively send</em> messages to customers without them messaging you first. You must have a verified Meta Business Account and an approved message template.
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div><h2>API Credentials</h2><p>Get these from your <a href="https://developers.facebook.com/apps/" target="_blank">Meta Developer Console</a> → Your App → WhatsApp → API Setup.</p></div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_wa_api_creds_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_wa_phone_id">Phone Number ID</label></th>
                        <td>
                            <input type="text" id="tw_wa_phone_id" name="tw_wa_phone_id"
                                   value="<?php echo esc_attr(get_option('tw_wa_phone_id','')); ?>"
                                   class="regular-text" placeholder="123456789012345">
                            <p class="description">Found in Meta Developer Console → WhatsApp → API Setup → "Phone Number ID".</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_wa_api_token">Permanent Access Token</label></th>
                        <td>
                            <input type="password" id="tw_wa_api_token" name="tw_wa_api_token"
                                   value="<?php echo esc_attr(get_option('tw_wa_api_token','')); ?>"
                                   class="large-text" placeholder="EAAxxxxx...">
                            <p class="description">
                                Generate in <strong>Meta Business Manager → Settings → System Users → Generate Token</strong> with <code>whatsapp_business_messaging</code> permission.<br>
                                Do <strong>not</strong> use a temporary token — it expires after a few hours.
                            </p>
                            <?php if (get_option('tw_wa_api_token','')) : ?>
                            <p style="color:green;font-weight:700;margin-top:6px"><i class="fi-rr-check" aria-hidden="true"></i> Token saved</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save API Credentials'); ?>
            </form>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div><h2>Message Template</h2><p>Required for sending first-contact messages. Leave blank to send a free-form message (only works if the customer has messaged you within 24 hours).</p></div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_wa_api_template_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_wa_template_name">Template Name</label></th>
                        <td>
                            <input type="text" id="tw_wa_template_name" name="tw_wa_template_name"
                                   value="<?php echo esc_attr(get_option('tw_wa_template_name','')); ?>"
                                   class="regular-text" placeholder="trip_inquiry_confirmation">
                            <p class="description">
                                The exact name of your <strong>approved template</strong> in Meta Business Manager → WhatsApp Manager → Message Templates.<br>
                                The template must have exactly <strong>2 body variables</strong>: <code>{{1}}</code> = customer name, <code>{{2}}</code> = destination.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_wa_template_lang">Template Language Code</label></th>
                        <td>
                            <input type="text" id="tw_wa_template_lang" name="tw_wa_template_lang"
                                   value="<?php echo esc_attr(get_option('tw_wa_template_lang','en')); ?>"
                                   class="small-text" placeholder="en">
                            <p class="description">Must match exactly what was submitted to Meta. Common codes: <code>en</code>, <code>en_US</code>, <code>hi</code> (Hindi), <code>en_GB</code>.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Template Settings'); ?>
            </form>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>How Automatic Messages Work</h2></div>
        <div class="tw-admin-card-body">
            <ol style="line-height:2.2;color:#444;padding-left:20px">
                <li>Customer fills the <strong>Plan A Trip</strong> form and clicks "Get My Quote on WhatsApp".</li>
                <li>The form data is saved to <a href="<?php echo esc_url(admin_url('admin.php?page=trip-inquiries')); ?>">Trip Inquiries</a>.</li>
                <li>Your server calls the WhatsApp Cloud API with the customer's phone number.</li>
                <li>The customer instantly receives a WhatsApp message with their trip summary: destination, date, budget, and traveller count.</li>
                <li>Your team can then send the detailed PDF quotation manually in the same WhatsApp thread.</li>
            </ol>
            <div class="tw-admin-note info" style="margin-top:4px">
                <i class="fi-rr-book-open-reader" aria-hidden="true"></i> <strong>Need a template?</strong> Create one at <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank">WhatsApp Manager → Message Templates</a>. Submit for approval — Meta usually approves within a few hours.
            </div>
        </div>
    </div>
    <?php tw_settings_page_footer();
}

// Keep old slug working (redirect to new overview) for any saved bookmarks
function tw_legacy_settings_redirect() {
    if ( is_admin() && isset($_GET['page']) && $_GET['page'] === 'tripwiser-settings' ) {
        wp_safe_redirect( admin_url('admin.php?page=tw-settings') );
        exit;
    }
}
add_action('admin_init', 'tw_legacy_settings_redirect');

// Stub so the old menu registration doesn't fatal if referenced elsewhere
function mytheme_render_settings_page() {
    wp_safe_redirect( admin_url('admin.php?page=tw-settings') );
    exit;
}

// ============================================================
// AJAX: Save Trip Inquiry (Plan A Trip form submission)
// ============================================================
function mytheme_submit_trip_inquiry() {
    check_ajax_referer('tw_trip_inquiry_nonce', 'nonce');

    $name        = isset($_POST['name'])        ? sanitize_text_field(wp_unslash($_POST['name']))        : '';
    $phone       = isset($_POST['phone'])       ? sanitize_text_field(wp_unslash($_POST['phone']))       : '';
    $email       = isset($_POST['email'])       ? sanitize_email(wp_unslash($_POST['email']))            : '';
    $destination = isset($_POST['destination']) ? sanitize_text_field(wp_unslash($_POST['destination'])) : '';
    $date        = isset($_POST['date'])        ? sanitize_text_field(wp_unslash($_POST['date']))        : '';
    $duration    = isset($_POST['duration'])    ? sanitize_text_field(wp_unslash($_POST['duration']))    : '';
    $time_pref   = isset($_POST['time_pref'])   ? sanitize_text_field(wp_unslash($_POST['time_pref']))   : '';
    $trip_type   = isset($_POST['trip_type'])   ? sanitize_text_field(wp_unslash($_POST['trip_type']))   : '';
    $adults      = isset($_POST['adults'])      ? absint($_POST['adults'])                               : 1;
    $children    = isset($_POST['children'])    ? absint($_POST['children'])                             : 0;
    $budget      = isset($_POST['budget'])      ? sanitize_text_field(wp_unslash($_POST['budget']))      : '';
    $departing   = isset($_POST['departing'])   ? sanitize_text_field(wp_unslash($_POST['departing']))   : '';
    $notes       = isset($_POST['notes'])       ? sanitize_textarea_field(wp_unslash($_POST['notes']))   : '';

    if (empty($name) || empty($phone)) {
        wp_send_json_error(array('message' => 'Name and phone are required.'));
        return;
    }

    $post_id = wp_insert_post(array(
        'post_type'   => 'trip_inquiry',
        'post_title'  => sanitize_text_field(sprintf('%s — %s — %s', $name, $destination, $date)),
        'post_status' => 'publish',
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => 'Could not save inquiry.'));
        return;
    }

    $meta = array(
        '_ti_name'        => $name,
        '_ti_phone'       => $phone,
        '_ti_email'       => $email,
        '_ti_destination' => $destination,
        '_ti_date'        => $date,
        '_ti_duration'    => $duration,
        '_ti_time_pref'   => $time_pref,
        '_ti_trip_type'   => $trip_type,
        '_ti_adults'      => $adults,
        '_ti_children'    => $children,
        '_ti_budget'      => $budget,
        '_ti_departing'   => $departing,
        '_ti_notes'       => $notes,
    );
    foreach ($meta as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    // Fire notification hooks (WhatsApp, Google Sheet, ...). Only the Plan
    // My Trip form reaches this action — package/itinerary/destination
    // enquiries are saved through a separate handler
    // (mytheme_handle_enquiry_submission, its own tw_enquiries table) that
    // never fires it, so downstream listeners naturally only see this source.
    do_action('tw_trip_inquiry_saved', $post_id, array(
        'name'        => $name,
        'phone'       => $phone,
        'email'       => $email,
        'destination' => $destination,
        'date'        => $date,
        'duration'    => $duration,
        'time_pref'   => $time_pref,
        'trip_type'   => $trip_type,
        'adults'      => $adults,
        'children'    => $children,
        'budget'      => $budget,
        'departing'   => $departing,
        'notes'       => $notes,
    ));

    $trip_mail_body = implode("\n", array_filter(array(
        'A new Plan A Trip enquiry was submitted on 1TripWiser.',
        '',
        'Name: ' . $name,
        'Phone: ' . $phone,
        'Email: ' . $email,
        'Destination: ' . $destination,
        'Departure Date: ' . $date,
        'Duration: ' . $duration,
        'Preferred Time: ' . $time_pref,
        'Trip Type: ' . $trip_type,
        'Adults: ' . $adults,
        'Children: ' . $children,
        'Budget: ' . $budget,
        'Departing From: ' . $departing,
        $notes ? "Special Requests:\n" . $notes : '',
    ), function ($line) { return $line !== ''; }));

    wp_mail('1tripwiser@gmail.com', sprintf('New Plan A Trip Enquiry — %s', $name), $trip_mail_body, array(
        $email ? ('Reply-To: ' . $name . ' <' . $email . '>') : '',
    ));

    wp_send_json_success(array('message' => 'Inquiry saved successfully.', 'id' => $post_id));
}
add_action('wp_ajax_submit_trip_inquiry',        'mytheme_submit_trip_inquiry');
add_action('wp_ajax_nopriv_submit_trip_inquiry', 'mytheme_submit_trip_inquiry');

/* The Plan a Trip nonce is embedded in the page HTML at render time via
 * wp_localize_script(). If that HTML is served from a full-page cache (this
 * site runs LiteSpeed + an upstream CDN), the embedded nonce can go stale
 * while the cached page itself keeps being served — the AJAX call then
 * fails silently (check_ajax_referer() -> wp_die(-1), a 200 response the
 * JS doesn't treat as an error). admin-ajax.php requests are never
 * page-cached, so fetching a fresh nonce here right before submitting
 * sidesteps that regardless of how old the surrounding page is. */
function mytheme_get_fresh_trip_inquiry_nonce() {
    wp_send_json_success(array('nonce' => wp_create_nonce('tw_trip_inquiry_nonce')));
}
add_action('wp_ajax_mytheme_get_fresh_trip_inquiry_nonce',        'mytheme_get_fresh_trip_inquiry_nonce');
add_action('wp_ajax_nopriv_mytheme_get_fresh_trip_inquiry_nonce', 'mytheme_get_fresh_trip_inquiry_nonce');

// ============================================================
// META BOX: Affiliate links on individual Blog Posts
// ============================================================
function mytheme_add_affiliate_meta_box() {
    add_meta_box(
        'mytheme_blog_affiliates',
        __('Affiliate Links for this Post', 'mytheme'),
        'mytheme_render_affiliate_meta_box',
        'post',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_affiliate_meta_box');

function mytheme_render_affiliate_meta_box($post) {
    wp_nonce_field('mytheme_save_affiliate_meta', 'mytheme_affiliate_meta_nonce');
    $fields = array(
        '_aff_booking_label' => 'Booking.com — Label (e.g. "Book your hotel here")',
        '_aff_booking_url'   => 'Booking.com — URL',
        '_aff_flight_label'  => 'Skyscanner — Label (e.g. "Compare cheap flights")',
        '_aff_flight_url'    => 'Skyscanner — URL',
        '_aff_tours_label'   => 'Viator — Label (e.g. "Book local experiences")',
        '_aff_tours_url'     => 'Viator — URL',
        '_aff_insure_label'  => 'SafetyWing — Label (e.g. "Get travel insurance")',
        '_aff_insure_url'    => 'SafetyWing — URL',
    );
    echo '<p style="color:#666;font-size:12px;margin:0 0 12px">Leave blank to use the global affiliate links from <a href="' . esc_url(admin_url('admin.php?page=tripwiser-settings&tab=blog')) . '">Page Settings</a>.</p>';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        $type  = (strpos($key, '_url') !== false) ? 'url' : 'text';
        echo '<p style="margin:0 0 10px"><label for="' . esc_attr($key) . '" style="display:block;font-weight:600;font-size:12px;margin-bottom:4px">' . esc_html($label) . '</label>';
        echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width:100%" /></p>';
    }
}

function mytheme_save_affiliate_meta($post_id) {
    if (!isset($_POST['mytheme_affiliate_meta_nonce']) || !wp_verify_nonce($_POST['mytheme_affiliate_meta_nonce'], 'mytheme_save_affiliate_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $url_keys  = array('_aff_booking_url', '_aff_flight_url', '_aff_tours_url', '_aff_insure_url');
    $text_keys = array('_aff_booking_label', '_aff_flight_label', '_aff_tours_label', '_aff_insure_label');

    foreach ($url_keys as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, esc_url_raw(wp_unslash($_POST[$key])));
        }
    }
    foreach ($text_keys as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
        }
    }
}
add_action('save_post_post', 'mytheme_save_affiliate_meta');

// ============================================================
// Localize AJAX data for front-end scripts
// ============================================================
function mytheme_localize_ajax_data() {
    wp_localize_script('custom-js', 'twAjax', array(
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tw_trip_inquiry_nonce'),
    ));
    wp_localize_script('custom-js', 'twPackageCompare', array(
        'archiveUrl' => get_post_type_archive_link('travel_package'),
        'maxItems' => 3,
        'minItems' => 2,
    ));
}
add_action('wp_enqueue_scripts', 'mytheme_localize_ajax_data', 20);

// ============================================================
// AJAX: Front-end blog post submission
// ============================================================
function tw_handle_blog_submission() {
    // Security check
    if ( ! isset( $_POST['tw_blog_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tw_blog_nonce'] ) ), 'tw_blog_submit_nonce' ) ) {
        wp_send_json_error( 'Security check failed. Please refresh the page and try again.' );
    }

    // Required fields
    $title   = isset( $_POST['sb_title'] )   ? sanitize_text_field( wp_unslash( $_POST['sb_title'] ) )   : '';
    $content = isset( $_POST['sb_content'] ) ? wp_kses_post( wp_unslash( $_POST['sb_content'] ) )         : '';

    if ( empty( $title ) ) {
        wp_send_json_error( 'Post title is required.' );
    }
    if ( strlen( strip_tags( $content ) ) < 50 ) {
        wp_send_json_error( 'Post content is too short.' );
    }

    // Optional fields
    $excerpt       = isset( $_POST['sb_excerpt'] )        ? sanitize_textarea_field( wp_unslash( $_POST['sb_excerpt'] ) )   : '';
    $tags_raw      = isset( $_POST['sb_tags'] )           ? sanitize_text_field( wp_unslash( $_POST['sb_tags'] ) )           : '';
    $cat_id        = isset( $_POST['sb_category'] )       ? intval( $_POST['sb_category'] )                                   : 0;
    $new_cat_name  = isset( $_POST['sb_new_category'] )   ? sanitize_text_field( wp_unslash( $_POST['sb_new_category'] ) )   : '';
    $author_name   = isset( $_POST['sb_author_name'] )    ? sanitize_text_field( wp_unslash( $_POST['sb_author_name'] ) )    : '';
    $author_email  = isset( $_POST['sb_author_email'] )   ? sanitize_email( wp_unslash( $_POST['sb_author_email'] ) )        : '';
    $author_bio    = isset( $_POST['sb_author_bio'] )     ? sanitize_text_field( wp_unslash( $_POST['sb_author_bio'] ) )     : '';

    // Determine status & author
    $post_status = 'pending'; // default — require review
    $post_author = 1;         // default to site admin

    if ( is_user_logged_in() ) {
        $post_author = get_current_user_id();
        if ( current_user_can( 'publish_posts' ) ) {
            $post_status = 'publish';
        }
    } else {
        // Guest submission — basic spam guard
        if ( empty( $author_name ) || empty( $author_email ) || ! is_email( $author_email ) ) {
            wp_send_json_error( 'Please provide a valid name and email address.' );
        }
    }

    // Handle "new category" option
    if ( $cat_id === 0 && ! empty( $new_cat_name ) ) {
        $new_cat = wp_insert_category( array(
            'cat_name'             => $new_cat_name,
            'category_description' => '',
            'category_nicename'    => sanitize_title( $new_cat_name ),
        ) );
        if ( ! is_wp_error( $new_cat ) ) {
            $cat_id = $new_cat;
        }
    }

    // Insert the post
    $post_data = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_excerpt'  => $excerpt,
        'post_status'   => $post_status,
        'post_type'     => 'post',
        'post_author'   => $post_author,
    );
    if ( $cat_id > 0 ) {
        $post_data['post_category'] = array( $cat_id );
    }

    $post_id = wp_insert_post( $post_data, true );
    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( $post_id->get_error_message() );
    }

    // Tags
    if ( ! empty( $tags_raw ) ) {
        $tags_arr = array_filter( array_map( 'trim', explode( ',', $tags_raw ) ) );
        wp_set_post_tags( $post_id, $tags_arr, false );
    }

    // Guest author meta
    if ( ! is_user_logged_in() ) {
        update_post_meta( $post_id, '_guest_author_name',  $author_name );
        update_post_meta( $post_id, '_guest_author_email', $author_email );
        update_post_meta( $post_id, '_guest_author_bio',   $author_bio );
    }

    // Featured image upload
    if ( ! empty( $_FILES['sb_image']['tmp_name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachment_id = media_handle_upload( 'sb_image', $post_id );
        if ( ! is_wp_error( $attachment_id ) ) {
            set_post_thumbnail( $post_id, $attachment_id );
        }
    }

    // Notify admin of new pending post
    if ( $post_status === 'pending' ) {
        $admin_email = get_option( 'admin_email' );
        $subject     = '[1TripWiser] New blog submission: ' . $title;
        $body        = "A new blog post has been submitted and is awaiting review.\n\n";
        $body       .= 'Title: ' . $title . "\n";
        if ( ! is_user_logged_in() ) {
            $body   .= 'From: ' . $author_name . ' <' . $author_email . ">\n";
        }
        $body       .= 'Review: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";
        wp_mail( $admin_email, $subject, $body );
    }

    wp_send_json_success( array(
        'status' => $post_status,
        'url'    => get_permalink( $post_id ),
    ) );
}
add_action( 'wp_ajax_tw_submit_blog',        'tw_handle_blog_submission' );
add_action( 'wp_ajax_nopriv_tw_submit_blog', 'tw_handle_blog_submission' );

// ============================================================
// AJAX: Update profile personal info
// ============================================================
function tw_update_profile_info() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'You must be logged in.' );
    }
    if ( ! isset( $_POST['tw_profile_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tw_profile_nonce'] ) ), 'tw_profile_update' ) ) {
        wp_send_json_error( 'Security check failed.' );
    }

    $user_id      = get_current_user_id();
    $first_name   = sanitize_text_field( wp_unslash( $_POST['pf_fname']   ?? '' ) );
    $last_name    = sanitize_text_field( wp_unslash( $_POST['pf_lname']   ?? '' ) );
    $display_name = sanitize_text_field( wp_unslash( $_POST['pf_display'] ?? '' ) );
    $description  = sanitize_textarea_field( wp_unslash( $_POST['pf_bio'] ?? '' ) );
    $email        = sanitize_email( wp_unslash( $_POST['pf_email']        ?? '' ) );

    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( 'Please enter a valid email address.' );
    }

    // Check email uniqueness (allow own email)
    $existing = get_user_by( 'email', $email );
    if ( $existing && (int) $existing->ID !== $user_id ) {
        wp_send_json_error( 'That email address is already in use by another account.' );
    }

    $result = wp_update_user( array(
        'ID'           => $user_id,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $display_name ?: ( trim( $first_name . ' ' . $last_name ) ?: get_userdata( $user_id )->user_login ),
        'description'  => $description,
        'user_email'   => $email,
    ) );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }

    wp_send_json_success( array( 'message' => 'Profile updated successfully.' ) );
}
add_action( 'wp_ajax_tw_update_profile_info', 'tw_update_profile_info' );

// ============================================================
// AJAX: Update profile password
// ============================================================
function tw_update_profile_password() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'You must be logged in.' );
    }
    if ( ! isset( $_POST['tw_pw_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tw_pw_nonce'] ) ), 'tw_profile_update' ) ) {
        wp_send_json_error( 'Security check failed.' );
    }

    $user_id      = get_current_user_id();
    $current_pass = wp_unslash( $_POST['pf_cur_pw']  ?? '' );
    $new_pass     = wp_unslash( $_POST['pf_new_pw']  ?? '' );
    $confirm_pass = wp_unslash( $_POST['pf_conf_pw'] ?? '' );

    // Verify current password
    $user = get_userdata( $user_id );
    if ( ! wp_check_password( $current_pass, $user->user_pass, $user_id ) ) {
        wp_send_json_error( 'Your current password is incorrect.' );
    }

    if ( strlen( $new_pass ) < 8 ) {
        wp_send_json_error( 'New password must be at least 8 characters.' );
    }

    if ( $new_pass !== $confirm_pass ) {
        wp_send_json_error( 'Passwords do not match.' );
    }

    wp_set_password( $new_pass, $user_id );

    // Re-authenticate so the user stays logged in after password change
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, false, is_ssl() );

    wp_send_json_success( array( 'message' => 'Password updated successfully.' ) );
}
add_action( 'wp_ajax_tw_update_profile_password', 'tw_update_profile_password' );

// ============================================================
// Auto-create required frontend pages on theme activation
// (profile, login, register, submit-blog) if they don't exist
// ============================================================
function tw_maybe_create_pages() {
    $pages = array(
        array(
            'slug'     => 'home',
            'title'    => 'Home',
            'template' => 'page-home.php',
        ),
        array(
            'slug'     => 'profile',
            'title'    => 'My Profile',
            'template' => 'page-profile.php',
        ),
        array(
            'slug'     => 'login',
            'title'    => 'Login',
            'template' => 'page-login.php',
        ),
        array(
            'slug'     => 'register',
            'title'    => 'Register',
            'template' => 'page-register.php',
        ),
        array(
            'slug'     => 'submit-blog',
            'title'    => 'Submit a Blog Post',
            'template' => 'page-submit-blog.php',
        ),
        array(
            'slug'     => 'blog-affiliates',
            'title'    => 'Blogs + Affiliates',
            'template' => 'page-blog-affiliates.php',
        ),
        array(
            'slug'     => 'plan-a-trip',
            'title'    => 'Plan a Trip',
            'template' => 'page-plan-a-trip.php',
        ),
        array(
            'slug'     => 'package-search',
            'title'    => 'Package Search',
            'template' => 'page-package-search.php',
        ),
        array(
            'slug'     => 'events-festivals',
            'title'    => 'Events & Festivals',
            'template' => 'page-events-festivals.php',
        ),
        array(
            'slug'     => 'womens-group-trips',
            'title'    => "Women's Group Trips",
            'template' => 'page-womens-trips.php',
        ),
        array(
            'slug'     => 'travel-agency-registration',
            'title'    => 'Travel Agency Registration',
            'template' => 'page-agency-register.php',
        ),
        array(
            'slug'     => 'blogs',
            'title'    => 'All Blogs',
            'template' => 'page-all-blogs.php',
        ),
        array(
            'slug'     => 'luxe',
            'title'    => 'LUXE',
            'template' => 'page-luxe.php',
        ),
        array(
            'slug'     => 'luxury',
            'title'    => 'LUXURY',
            'template' => 'page-luxury.php',
        ),
    );

    $home_page_id = 0;

    foreach ( $pages as $page_data ) {
        $existing = get_page_by_path( $page_data['slug'] );
        if ( $existing ) {
            // Make sure the template is set correctly
            if ( get_post_meta( $existing->ID, '_wp_page_template', true ) !== $page_data['template'] ) {
                update_post_meta( $existing->ID, '_wp_page_template', $page_data['template'] );
            }
            if ( $page_data['slug'] === 'home' ) {
                $home_page_id = $existing->ID;
            }
            continue;
        }

        $post_id = wp_insert_post( array(
            'post_title'   => $page_data['title'],
            'post_name'    => $page_data['slug'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ) );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_wp_page_template', $page_data['template'] );
            if ( $page_data['slug'] === 'home' ) {
                $home_page_id = $post_id;
            }
        }
    }

    /*
     * Auto-configure Settings → Reading to use the Home page as a static
     * front page. This ensures the homepage works on any environment
     * (local, staging, production) without manual WP admin steps.
     */
    if ( $home_page_id > 0 ) {
        if ( get_option( 'show_on_front' ) !== 'page' ) {
            update_option( 'show_on_front', 'page' );
        }
        if ( (int) get_option( 'page_on_front' ) !== $home_page_id ) {
            update_option( 'page_on_front', $home_page_id );
        }
    }

    /*
     * Self-heal the "Posts page" setting. This theme has NO dedicated WordPress
     * posts page — the Blogs + Affiliates page runs its own post query via
     * page-blog-affiliates.php. If a custom-template page (most commonly
     * blog-affiliates) gets set as the Posts page, WordPress renders the blog
     * index (home.php) at its URL and ignores the page's own template — which is
     * exactly the "blog-affiliates page not coming up" bug on staging. Clear it.
     */
    $posts_page_id = (int) get_option( 'page_for_posts' );
    if ( $posts_page_id > 0 ) {
        $posts_page = get_post( $posts_page_id );
        if ( $posts_page && in_array( $posts_page->post_name, array( 'blog-affiliates', 'plan-a-trip', 'package-search', 'home' ), true ) ) {
            update_option( 'page_for_posts', 0 );
        }
    }
}
add_action( 'after_switch_theme', 'tw_maybe_create_pages' );
// Also run on init once (idempotent — only creates/updates if something is missing)
add_action( 'init', 'tw_maybe_create_pages', 99 );

/* ═════════════════════════════════════════════════════════════
 * TRAVEL AGENCY REGISTRATION
 * - CPT to store submissions (admin-only)
 * - Public form handler (admin-post)
 * - Admin list table + per-row detail view
 * - CSV export
 * ═════════════════════════════════════════════════════════════ */

/* CPT */
function tw_register_agency_cpt() {
    register_post_type( 'tw_agency', array(
        'labels' => array(
            'name'          => __( 'Travel Agencies', 'mytheme' ),
            'singular_name' => __( 'Travel Agency',  'mytheme' ),
            'menu_name'     => __( 'Travel Agencies','mytheme' ),
            'all_items'     => __( 'All Agencies',   'mytheme' ),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-businessperson',
        'menu_position'       => 28,
        'supports'            => array( 'title' ),
        'capabilities'        => array(
            'create_posts' => 'do_not_allow', // only created via the public form
        ),
        'map_meta_cap'        => true,
        'exclude_from_search' => true,
        'has_archive'         => false,
        'rewrite'             => false,
    ) );
}
add_action( 'init', 'tw_register_agency_cpt', 7 );

/* Form fields — single source of truth */
function tw_agency_fields() {
    return array(
        'company_name'    => array( 'Company Name *',     'text',     true  ),
        'contact_person'  => array( 'Contact Person *',   'text',     true  ),
        'designation'     => array( 'Designation',        'text',     false ),
        'email'           => array( 'Email *',            'email',    true  ),
        'phone'           => array( 'Phone *',            'tel',      true  ),
        'whatsapp'        => array( 'WhatsApp Number',    'tel',      false ),
        'website'         => array( 'Website',            'url',      false ),
        'city'            => array( 'City *',             'text',     true  ),
        'state'           => array( 'State / Region',     'text',     false ),
        'country'         => array( 'Country *',          'text',     true  ),
        'years_active'    => array( 'Years in Business',  'number',   false ),
        'team_size'       => array( 'Team Size',          'number',   false ),
        'specialisation'  => array( 'Specialisation',     'text',     false, 'e.g. Honeymoon, Group Trips, Adventure' ),
        'destinations'    => array( 'Destinations Covered','textarea',false, 'List the main destinations you serve' ),
        'license_number'  => array( 'License / GSTIN',    'text',     false ),
        'notes'           => array( 'Additional Notes',   'textarea', false ),
    );
}

/* Form handler */
add_action( 'admin_post_nopriv_tw_agency_register', 'tw_handle_agency_register' );
add_action( 'admin_post_tw_agency_register',        'tw_handle_agency_register' );
function tw_handle_agency_register() {
    if ( ! isset( $_POST['tw_agency_nonce'] ) || ! wp_verify_nonce( $_POST['tw_agency_nonce'], 'tw_agency_register' ) ) {
        wp_safe_redirect( add_query_arg( 'agency', 'error', wp_get_referer() ?: home_url( '/travel-agency-registration/' ) ) );
        exit;
    }

    $fields = tw_agency_fields();
    $data   = array();

    /* required-field check */
    foreach ( $fields as $key => $f ) {
        $val = isset( $_POST[ $key ] ) ? trim( wp_unslash( $_POST[ $key ] ) ) : '';
        if ( ! empty( $f[2] ) && $val === '' ) {
            wp_safe_redirect( add_query_arg( 'agency', 'missing', wp_get_referer() ?: home_url( '/travel-agency-registration/' ) ) );
            exit;
        }
        if ( $f[1] === 'email' )  { $val = sanitize_email( $val ); }
        elseif ( $f[1] === 'url' )    { $val = esc_url_raw( $val ); }
        elseif ( $f[1] === 'textarea' ){ $val = sanitize_textarea_field( $val ); }
        else                          { $val = sanitize_text_field( $val ); }
        $data[ $key ] = $val;
    }

    $title = $data['company_name'] . ' — ' . $data['contact_person'];
    $post_id = wp_insert_post( array(
        'post_type'   => 'tw_agency',
        'post_title'  => $title,
        'post_status' => 'publish',
    ), true );

    if ( is_wp_error( $post_id ) ) {
        wp_safe_redirect( add_query_arg( 'agency', 'error', wp_get_referer() ?: home_url( '/travel-agency-registration/' ) ) );
        exit;
    }

    foreach ( $data as $k => $v ) {
        update_post_meta( $post_id, '_tw_ag_' . $k, $v );
    }
    update_post_meta( $post_id, '_tw_ag_submitted_at', current_time( 'mysql' ) );
    update_post_meta( $post_id, '_tw_ag_ip', isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '' );

    /* Notify admin */
    $admin_email = get_option( 'admin_email' );
    if ( $admin_email ) {
        $body = "A new travel agency has registered:\n\n";
        foreach ( $fields as $key => $f ) {
            if ( ! empty( $data[ $key ] ) ) { $body .= $f[0] . ': ' . $data[ $key ] . "\n"; }
        }
        $body .= "\nView in admin: " . admin_url( 'post.php?post=' . $post_id . '&action=edit' );
        wp_mail( $admin_email, '[1TRIPWISER] New Travel Agency Registration', $body );
    }

    wp_safe_redirect( add_query_arg( 'agency', 'success', wp_get_referer() ?: home_url( '/travel-agency-registration/' ) ) );
    exit;
}

/* Admin list table columns */
add_filter( 'manage_tw_agency_posts_columns', function ( $cols ) {
    return array(
        'cb'             => '<input type="checkbox" />',
        'agency_company' => __( 'Company',       'mytheme' ),
        'agency_contact' => __( 'Contact',       'mytheme' ),
        'agency_email'   => __( 'Email',         'mytheme' ),
        'agency_phone'   => __( 'Phone',         'mytheme' ),
        'agency_loc'     => __( 'Location',      'mytheme' ),
        'agency_special' => __( 'Specialisation','mytheme' ),
        'date'           => __( 'Submitted',     'mytheme' ),
    );
} );
add_action( 'manage_tw_agency_posts_custom_column', function ( $col, $post_id ) {
    switch ( $col ) {
        case 'agency_company':
            echo '<strong><a href="' . esc_url( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ) . '">' . esc_html( get_post_meta( $post_id, '_tw_ag_company_name', true ) ) . '</a></strong>';
            $w = get_post_meta( $post_id, '_tw_ag_website', true );
            if ( $w ) { echo '<br><a href="' . esc_url( $w ) . '" target="_blank" style="font-size:0.8em;color:#1B93B0">' . esc_html( $w ) . '</a>'; }
            break;
        case 'agency_contact':
            echo esc_html( get_post_meta( $post_id, '_tw_ag_contact_person', true ) );
            $d = get_post_meta( $post_id, '_tw_ag_designation', true );
            if ( $d ) { echo '<br><small style="color:#888">' . esc_html( $d ) . '</small>'; }
            break;
        case 'agency_email':
            $e = get_post_meta( $post_id, '_tw_ag_email', true );
            echo $e ? '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>' : '—';
            break;
        case 'agency_phone':
            $p = get_post_meta( $post_id, '_tw_ag_phone', true );
            $w = get_post_meta( $post_id, '_tw_ag_whatsapp', true );
            echo $p ? esc_html( $p ) : '—';
            if ( $w && $w !== $p ) { echo '<br><small style="color:#25D366">WA: ' . esc_html( $w ) . '</small>'; }
            break;
        case 'agency_loc':
            $city = get_post_meta( $post_id, '_tw_ag_city', true );
            $st   = get_post_meta( $post_id, '_tw_ag_state', true );
            $co   = get_post_meta( $post_id, '_tw_ag_country', true );
            echo esc_html( trim( $city . ( $st ? ', ' . $st : '' ) ) );
            if ( $co ) { echo '<br><small>' . esc_html( $co ) . '</small>'; }
            break;
        case 'agency_special':
            echo esc_html( get_post_meta( $post_id, '_tw_ag_specialisation', true ) ?: '—' );
            break;
    }
}, 10, 2 );

/* Read-only detail meta box */
add_action( 'add_meta_boxes', function () {
    add_meta_box( 'tw_agency_details', 'Agency Submission', function ( $post ) {
        $fields = tw_agency_fields();
        echo '<table class="form-table" style="font-size:0.9em">';
        foreach ( $fields as $key => $f ) {
            $val = get_post_meta( $post->ID, '_tw_ag_' . $key, true );
            if ( $val === '' ) { continue; }
            echo '<tr><th style="width:200px">' . esc_html( str_replace( ' *', '', $f[0] ) ) . '</th><td>';
            if ( $f[1] === 'email' )    { echo '<a href="mailto:' . esc_attr( $val ) . '">' . esc_html( $val ) . '</a>'; }
            elseif ( $f[1] === 'url' )      { echo '<a href="' . esc_url( $val ) . '" target="_blank">' . esc_html( $val ) . '</a>'; }
            elseif ( $f[1] === 'textarea' ) { echo nl2br( esc_html( $val ) ); }
            else                            { echo esc_html( $val ); }
            echo '</td></tr>';
        }
        $sub = get_post_meta( $post->ID, '_tw_ag_submitted_at', true );
        if ( $sub ) { echo '<tr><th>Submitted at</th><td>' . esc_html( $sub ) . '</td></tr>'; }
        echo '</table>';
    }, 'tw_agency', 'normal', 'high' );
} );

/* Export button on the list screen */
add_action( 'restrict_manage_posts', function () {
    global $typenow;
    if ( $typenow !== 'tw_agency' ) { return; }
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=tw_agency_export_csv' ), 'tw_agency_export' );
    echo '<a href="' . esc_url( $url ) . '" class="button button-primary" style="margin-left:8px"><i class="fi-rr-arrow-down" aria-hidden="true"></i> Export CSV</a>';
} );

/* CSV export handler */
add_action( 'admin_post_tw_agency_export_csv', function () {
    if ( ! current_user_can( 'edit_posts' ) ) { wp_die( 'No permission' ); }
    check_admin_referer( 'tw_agency_export' );

    $fields = tw_agency_fields();
    $posts  = get_posts( array(
        'post_type'      => 'tw_agency',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $filename = 'travel-agencies-' . date( 'Y-m-d-Hi' ) . '.csv';
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

    $out = fopen( 'php://output', 'w' );
    fputs( $out, "\xEF\xBB\xBF" ); /* UTF-8 BOM for Excel */

    /* Header row */
    $header = array( 'ID', 'Submitted At' );
    foreach ( $fields as $f ) { $header[] = str_replace( ' *', '', $f[0] ); }
    $header[] = 'IP Address';
    fputcsv( $out, $header );

    /* Data rows */
    foreach ( $posts as $p ) {
        $row = array( $p->ID, get_post_meta( $p->ID, '_tw_ag_submitted_at', true ) );
        foreach ( $fields as $k => $f ) { $row[] = get_post_meta( $p->ID, '_tw_ag_' . $k, true ); }
        $row[] = get_post_meta( $p->ID, '_tw_ag_ip', true );
        fputcsv( $out, $row );
    }
    fclose( $out );
    exit;
} );
