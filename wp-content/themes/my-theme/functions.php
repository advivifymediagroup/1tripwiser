<?php

/* ── Tribe community forum (custom post type, replies, likes, leaderboard) ── */
require_once get_template_directory() . '/includes/forum.php';

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
    /* Use filemtime() as the version string so the browser cache busts EVERY time
       style.css is modified. Without this, WordPress falls back to the WP core
       version (e.g. ?ver=7.0) which never changes, so updated CSS stays cached. */
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_ver  = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0';
    wp_enqueue_style('main-style', get_stylesheet_uri(), array(), $style_ver);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css');
    // Animations CSS (global) — same filemtime cache-busting
    $anim_path = get_template_directory() . '/assets/css/tw-animations.css';
    $anim_ver  = file_exists( $anim_path ) ? filemtime( $anim_path ) : '1.1';
    wp_enqueue_style('tw-animations', get_template_directory_uri() . '/assets/css/tw-animations.css', array(), $anim_ver);
    wp_enqueue_script('jquery');
    // Custom JS placeholder (kept for legacy localize_script hook)
    if ( file_exists( get_template_directory() . '/assets/js/custom.js' ) ) {
        wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), '1.0', true);
    } else {
        // Register a dummy handle so localize_script still works
        wp_register_script('custom-js', '', array('jquery'), '1.0', true);
        wp_enqueue_script('custom-js');
    }
    // Global animations (canvas bubbles, scroll reveal, tilt, ripple)
    wp_enqueue_script('tw-animations', get_template_directory_uri() . '/assets/js/tw-animations.js', array(), '1.1', true);
}

add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');

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
    register_taxonomy('destination_region', array('post', 'destination', 'itinerary', 'travel_package'), array(
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
    if (in_array($post_type, array('destination', 'itinerary', 'travel_package'), true)) {
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
    );
}

function mytheme_add_travel_meta_boxes() {
    if (function_exists('acf_add_local_field_group')) {
        return;
    }

    add_meta_box(
        'mytheme_travel_details',
        __('Travel Details', 'mytheme'),
        'mytheme_render_travel_meta_box',
        array('itinerary', 'travel_package'),
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

function mytheme_get_plan_trip_url() {
    $page = get_page_by_path('plan-a-trip');
    return $page ? get_permalink($page) : home_url('/plan-a-trip/');
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
    $current_type = get_post_type();
    $home_active = is_front_page() || is_home();
    $package_active = is_post_type_archive('travel_package') || is_singular('travel_package') || $current_type === 'travel_package';
    $plan_page = get_page_by_path('plan-a-trip');
    $plan_active = $plan_page && is_page($plan_page->ID);
    ?>
    <nav class="travel-tabs" aria-label="<?php esc_attr_e('Primary travel sections', 'mytheme'); ?>">
        <div class="container travel-tabs-inner">
            <a class="<?php echo $home_active ? 'active' : ''; ?>" href="<?php echo esc_url(home_url('/')); ?>">🏡Homepage</a>
            <a class="<?php echo $package_active ? 'active' : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>">📦Packages</a>
            <?php
            $blog_page   = get_page_by_path('blog-affiliates');
            $blog_active = $blog_page && is_page($blog_page->ID);
            ?>
            <a class="<?php echo $blog_active ? 'active' : ''; ?>" href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>">💰Blogs + Affiliates</a>
            <a class="<?php echo $plan_active ? 'active' : ''; ?>" href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>">✈️Plan a Trip</a>
        </div>
    </nav>
    <?php
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

function mytheme_package_card($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $data = mytheme_get_package_data($post_id);
    $image_url = mytheme_get_image_url($data['image'], 'medium');
    $book_url = $data['book_url'] ? $data['book_url'] : get_permalink($post_id);
    ?>
    <article class="post-card package-card">
        <div class="package-media">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
                <?php elseif (has_post_thumbnail($post_id)) : ?>
                    <?php echo get_the_post_thumbnail($post_id, 'medium'); ?>
                <?php endif; ?>
            </a>
            <?php if ($data['tag']) : ?>
                <span class="package-tag"><?php echo esc_html($data['tag']); ?></span>
            <?php endif; ?>
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
                    <?php if ($data['emi']) : ?><small><?php echo esc_html($data['emi']); ?></small><?php endif; ?>
                </div>
                <a href="<?php echo esc_url($book_url); ?>" class="book-now-btn">Book Now</a>
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
                    <summary><?php echo esc_html($question); ?></summary>
                    <div class="tw-faq-answer">
                        <?php echo wp_kses_post(wpautop($answer)); ?>
                    </div>
                </details>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
}

function mytheme_handle_package_inquiry() {
    if (!isset($_POST['mytheme_package_inquiry_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mytheme_package_inquiry_nonce'])), 'mytheme_package_inquiry')) {
        wp_die(esc_html__('Security check failed.', 'mytheme'));
    }

    $package_id = isset($_POST['package_id']) ? absint($_POST['package_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $travel_date = isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '';
    $adults = isset($_POST['adults']) ? max(1, absint($_POST['adults'])) : 1;
    $budget = isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $redirect = isset($_POST['_wp_http_referer']) ? esc_url_raw(wp_unslash($_POST['_wp_http_referer'])) : home_url('/');

    if (!$package_id || get_post_type($package_id) !== 'travel_package' || empty($name) || empty($phone)) {
        wp_safe_redirect(add_query_arg('package_enquiry', 'error', $redirect));
        exit;
    }

    $package = mytheme_get_package_data($package_id);
    $package_title = get_the_title($package_id);
    $destination = $package['location'] ? $package['location'] : $package_title;

    $post_id = wp_insert_post(array(
        'post_type' => 'trip_inquiry',
        'post_title' => sanitize_text_field(sprintf('%s - Package Enquiry - %s', $name, $package_title)),
        'post_status' => 'publish',
    ));

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('package_enquiry', 'error', $redirect));
        exit;
    }

    $meta = array(
        '_ti_name' => $name,
        '_ti_phone' => $phone,
        '_ti_email' => $email,
        '_ti_destination' => $destination,
        '_ti_date' => $travel_date,
        '_ti_duration' => $package['duration'],
        '_ti_time_pref' => '',
        '_ti_trip_type' => $package['trip_type'],
        '_ti_adults' => $adults,
        '_ti_children' => 0,
        '_ti_budget' => $budget ? $budget : $package['amount'],
        '_ti_departing' => '',
        '_ti_notes' => $message,
        '_ti_source' => 'Package Enquiry',
        '_ti_package_id' => $package_id,
        '_ti_package_title' => $package_title,
        '_ti_package_url' => get_permalink($package_id),
    );

    foreach ($meta as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    wp_safe_redirect(add_query_arg('package_enquiry', 'success', $redirect));
    exit;
}
add_action('admin_post_mytheme_package_inquiry', 'mytheme_handle_package_inquiry');
add_action('admin_post_nopriv_mytheme_package_inquiry', 'mytheme_handle_package_inquiry');

function mytheme_travel_filter_options($post_type) {
    if ($post_type === 'travel_package') {
        return array(
            'all' => __('All', 'mytheme'),
            'india' => __('India', 'mytheme'),
            'international' => __('International', 'mytheme'),
            'asia' => __('Asia', 'mytheme'),
            'europe' => __('Europe', 'mytheme'),
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
    $options = mytheme_travel_filter_options($post_type);
    $active_filter = mytheme_get_active_travel_filter($param, $post_type);
    ?>
    <div class="travel-filter-box" aria-label="<?php esc_attr_e('Travel filters', 'mytheme'); ?>">
        <span class="travel-filter-label"><?php esc_html_e('Filter by', 'mytheme'); ?></span>
        <div class="travel-filter-options">
            <?php foreach ($options as $value => $label) : ?>
                <?php
                $url = $value === 'all'
                    ? remove_query_arg($param, $base_url)
                    : add_query_arg($param, $value, $base_url);
                $url = remove_query_arg('paged', $url);
                ?>
                <a class="<?php echo $active_filter === $value ? 'active' : ''; ?>" href="<?php echo esc_url($url . $anchor); ?>">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
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
            <th style="width:160px"><label for="taff_icon"><?php esc_html_e('Icon (emoji)', 'mytheme'); ?></label></th>
            <td>
                <input type="text" id="taff_icon" name="taff_icon" value="<?php echo esc_attr($icon ?: '🔗'); ?>" style="width:80px;font-size:1.4rem;text-align:center" />
                <p class="description">e.g. 🏨 ✈️ 🎟️ 🛡️ 🚗</p>
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
        __('📊 Overview', 'mytheme'),
        'manage_options',
        'tw-settings',
        'tw_render_overview_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Homepage Hero Banner', 'mytheme'),
        __('🎬 Hero Banner', 'mytheme'),
        'manage_options',
        'tw-hero-settings',
        'tw_render_hero_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Plan A Trip Page', 'mytheme'),
        __('✈️ Plan A Trip Page', 'mytheme'),
        'manage_options',
        'tw-plan-trip-settings',
        'tw_render_plan_trip_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('Blog & Affiliates Page', 'mytheme'),
        __('📰 Blog & Affiliates Page', 'mytheme'),
        'manage_options',
        'tw-blog-settings',
        'tw_render_blog_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('WhatsApp Chat Widget', 'mytheme'),
        __('💬 WhatsApp Widget', 'mytheme'),
        'manage_options',
        'tw-wa-widget-settings',
        'tw_render_wa_widget_settings_page'
    );
    add_submenu_page(
        'tw-settings',
        __('WhatsApp Cloud API', 'mytheme'),
        __('🤖 WhatsApp API', 'mytheme'),
        'manage_options',
        'tw-wa-api-settings',
        'tw_render_wa_api_settings_page'
    );
}
add_action('admin_menu', 'mytheme_register_admin_menus');

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

    $headers = array( '#', 'Name', 'Phone', 'Email', 'Destination', 'Travel Date', 'Budget', 'Adults', 'Children', 'Trip Type', 'Submitted' );
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
                    ⬇ Export CSV
                </a>
                <a href="<?php echo $xlsx_url; ?>"
                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#1d7a3a;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:600;line-height:1.4">
                    ⬇ Export Excel
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
                        <td><?php echo esc_html(get_post_meta($id, '_ti_budget', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_adults', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_children', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ti_trip_type', true)); ?></td>
                        <td><?php echo esc_html(get_the_date('d M Y, g:i a')); ?></td>
                    </tr>
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <tr><td colspan="12" style="text-align:center;padding:24px;color:#666;"><?php esc_html_e('No inquiries yet. Form submissions will appear here.', 'mytheme'); ?></td></tr>
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
// SETTINGS: Register options for Plan A Trip + Blog pages + Hero + WhatsApp
// ============================================================
function mytheme_register_page_settings() {
    // Plan A Trip
    foreach (array('tw_pat_kicker', 'tw_pat_title', 'tw_pat_subtitle', 'tw_pat_whatsapp') as $key) {
        register_setting('tripwiser_plan_trip_settings', $key, array('sanitize_callback' => 'sanitize_text_field'));
    }
    // Blog & Affiliates
    register_setting('tripwiser_blog_settings', 'tw_blog_kicker',      array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_blog_settings', 'tw_blog_title',       array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_blog_settings', 'tw_blog_subtitle',    array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_blog_settings', 'tw_blog_aff_heading', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_blog_settings', 'tw_blog_aff_text',    array('sanitize_callback' => 'wp_kses_post'));
    // Hero background
    register_setting('tripwiser_hero_settings', 'tw_hero_video_url',        array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_hero_image_url',        array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_hero_mobile_image_url', array('sanitize_callback' => 'esc_url_raw'));
    register_setting('tripwiser_hero_settings', 'tw_instagram_feed_id', array('sanitize_callback' => 'absint'));
    // WhatsApp widget + Cloud API
    register_setting('tripwiser_wa_settings', 'tw_wa_widget_number',  array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_wa_settings', 'tw_wa_widget_message', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_wa_settings', 'tw_wa_widget_greeting',array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_wa_settings', 'tw_wa_api_token',      array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_wa_settings', 'tw_wa_phone_id',       array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_wa_settings', 'tw_wa_template_name',  array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('tripwiser_wa_settings', 'tw_wa_template_lang',  array('sanitize_callback' => 'sanitize_text_field'));
}
add_action('admin_init', 'mytheme_register_page_settings');

// ============================================================
// WHATSAPP FLOATING CHAT WIDGET (front-end footer injection)
// ============================================================
function tw_whatsapp_widget() {
    if ( is_admin() ) return;
    $wa_num     = get_option('tw_wa_widget_number',  get_option('tw_pat_whatsapp', '') );
    $wa_msg     = get_option('tw_wa_widget_message',  'Hi! I have a question about a trip.');
    $wa_greet   = get_option('tw_wa_widget_greeting', 'Hi there! 👋 How can we help you plan your perfect trip?');
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
                <button class="tw-wa-popup-close" id="tw-wa-close" aria-label="Close chat">✕</button>
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
            <span class="tw-wa-btn-icon tw-wa-btn-close" aria-hidden="true" style="display:none">✕</span>
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

        function openPopup() {
            popup.hidden = false;
            btn.setAttribute('aria-expanded','true');
            if (badge)  badge.style.display  = 'none';
            if (iconO)  iconO.style.display  = 'none';
            if (iconC)  iconC.style.display  = '';
        }
        function closePopup() {
            popup.hidden = true;
            btn.setAttribute('aria-expanded','false');
            if (iconO) iconO.style.display = '';
            if (iconC) iconC.style.display = 'none';
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
        $msg = "Hi " . ($data['name'] ?? 'there') . "! 👋\n\n"
             . "Thanks for your trip inquiry with *1TripWiser*! Here's your summary:\n\n"
             . "📍 *Destination:* " . ($data['destination'] ?? '-') . "\n"
             . "📅 *Travel Date:* "  . ($data['date'] ?? '-') . "\n"
             . "💰 *Budget:* "        . ($data['budget'] ?? '-') . "\n"
             . "👥 *Adults:* "        . ($data['adults'] ?? '1') . "  |  *Children:* " . ($data['children'] ?? '0') . "\n"
             . "🗺️ *Trip Type:* "     . ($data['trip_type'] ?? '-') . "\n\n"
             . "Our team will send your personalised quotation shortly. Stay tuned! ✈️\n\n"
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
// SHARED HELPER — settings page chrome (header + breadcrumb)
// ============================================================
function tw_settings_page_header( $title, $icon, $description = '' ) {
    $logo_html = '<span style="display:inline-flex;align-items:center;gap:10px;font-size:1.5rem;font-weight:800;color:#0d1526;font-family:Georgia,serif;margin-bottom:4px"><span style="color:#FCB415">1</span>TRIPWISER</span>';
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
    .tw-admin-ql:hover { border-color:#0692AF; background:#eef7fb; color:#0692AF; }
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
    tw_settings_page_header('Settings Overview', '📊', 'All site settings at a glance — click any card to jump straight there.');
    $inquiry_count = wp_count_posts('trip_inquiry')->publish ?? 0;
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>⚡ Quick Actions</h2></div>
        <div class="tw-admin-card-body">
            <div class="tw-admin-quicklinks">
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">
                    <span class="tw-admin-ql-icon">🎬</span><span>Hero Banner<br><small style="font-weight:500;color:#667085">Video / image background</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-plan-trip-settings')); ?>">
                    <span class="tw-admin-ql-icon">✈️</span><span>Plan A Trip Page<br><small style="font-weight:500;color:#667085">Hero text &amp; WhatsApp number</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-blog-settings')); ?>">
                    <span class="tw-admin-ql-icon">📰</span><span>Blog &amp; Affiliates Page<br><small style="font-weight:500;color:#667085">Page hero &amp; partner banner</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-widget-settings')); ?>">
                    <span class="tw-admin-ql-icon">💬</span><span>WhatsApp Widget<br><small style="font-weight:500;color:#667085">Floating chat button</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-api-settings')); ?>">
                    <span class="tw-admin-ql-icon">🤖</span><span>WhatsApp API<br><small style="font-weight:500;color:#667085">Auto quotation messages</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('admin.php?page=trip-inquiries')); ?>">
                    <span class="tw-admin-ql-icon">📋</span><span>Trip Inquiries<br><small style="font-weight:500;color:#667085"><?php echo esc_html($inquiry_count); ?> submissions</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('edit.php')); ?>">
                    <span class="tw-admin-ql-icon">📝</span><span>Blog Posts<br><small style="font-weight:500;color:#667085">Write &amp; manage articles</small></span>
                </a>
                <a class="tw-admin-ql" href="<?php echo esc_url(admin_url('edit.php?post_type=tw_affiliate')); ?>">
                    <span class="tw-admin-ql-icon">🔗</span><span>Affiliate Links<br><small style="font-weight:500;color:#667085">Manage partner links</small></span>
                </a>
            </div>
        </div>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>📋 Current Configuration Summary</h2></div>
        <div class="tw-admin-card-body">
            <table class="widefat striped" style="border:none">
                <tbody>
                    <tr><td style="width:260px;font-weight:700">Hero Video</td><td><?php $v=get_option('tw_hero_video_url',''); echo $v ? '<a href="'.esc_url($v).'" target="_blank">'.esc_html(substr($v,0,60)).'…</a>' : '<span style="color:#999">Not set — using dark background</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Hero Image</td><td><?php $i=get_option('tw_hero_image_url',''); echo $i ? '<a href="'.esc_url($i).'" target="_blank">Set ✓</a>' : '<span style="color:#999">Not set</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Instagram Feed ID</td><td><?php echo esc_html(get_option('tw_instagram_feed_id', 1)); ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-hero-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Plan A Trip WhatsApp</td><td><?php echo esc_html(get_option('tw_pat_whatsapp','Not set')); ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-plan-trip-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">WhatsApp Widget Number</td><td><?php $n=get_option('tw_wa_widget_number',get_option('tw_pat_whatsapp','')); echo $n ? esc_html($n) : '<span style="color:#999">Not set</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-widget-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">WhatsApp Cloud API</td><td><?php echo get_option('tw_wa_api_token','') ? '<span style="color:green">✓ Configured</span>' : '<span style="color:#999">Not configured</span>'; ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-wa-api-settings')); ?>">Edit →</a></td></tr>
                    <tr><td style="font-weight:700">Blog Page Title</td><td><?php echo esc_html(get_option('tw_blog_title','BLOGS + AFFILIATES')); ?></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=tw-blog-settings')); ?>">Edit →</a></td></tr>
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
    tw_settings_page_header('Hero Banner', '🎬', 'Control the video or image that plays behind the homepage hero section.');
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
                            <input type="url" id="tw_hero_video_url" name="tw_hero_video_url"
                                   value="<?php echo esc_attr(get_option('tw_hero_video_url','')); ?>"
                                   class="large-text" placeholder="https://www.youtube.com/watch?v=...  or  https://yoursite.com/hero.mp4">
                            <p class="description">
                                Accepts a <strong>YouTube link</strong> (youtu.be or youtube.com/watch?v=) or a direct <strong>.mp4 file URL</strong>.<br>
                                The video plays <em>muted, looped, and auto-started</em> — ideal for scenic travel footage.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_hero_image_url">Fallback Image URL</label></th>
                        <td>
                            <input type="url" id="tw_hero_image_url" name="tw_hero_image_url"
                                   value="<?php echo esc_attr(get_option('tw_hero_image_url','')); ?>"
                                   class="large-text" placeholder="https://yoursite.com/hero-image.jpg">
                            <p class="description">
                                Used when no video is set. Upload your image to
                                <a href="<?php echo esc_url(admin_url('media-new.php')); ?>">Media → Add New</a>,
                                copy the URL, and paste it here.
                            </p>
                            <?php $img = get_option('tw_hero_image_url',''); if ($img) : ?>
                            <div style="margin-top:12px">
                                <img src="<?php echo esc_url($img); ?>" style="max-width:360px;border-radius:8px;border:1px solid #dde5ef;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                                <p style="margin:6px 0 0;font-size:0.8rem;color:#667085">Current fallback image</p>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="tw_hero_mobile_image_url">Mobile Image URL <span style="font-weight:400;color:#0692af">(mobile only)</span></label></th>
                        <td>
                            <input type="url" id="tw_hero_mobile_image_url" name="tw_hero_mobile_image_url"
                                   value="<?php echo esc_attr(get_option('tw_hero_mobile_image_url','')); ?>"
                                   class="large-text" placeholder="https://yoursite.com/hero-mobile.jpg">
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
                <div class="tw-admin-note info" style="margin-top:8px">
                    💡 <strong>Tip:</strong> For best results use a landscape video at 1920×1080 or wider. YouTube videos are embedded as iframes — make sure the video is public. Direct .mp4 files load faster.
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
    <?php tw_settings_page_footer();
}

// ============================================================
// PLAN A TRIP PAGE SETTINGS
// ============================================================
function tw_render_plan_trip_settings_page() {
    tw_settings_page_header('Plan A Trip Page', '✈️', 'Edit the hero text and WhatsApp number for the Plan A Trip form page.');
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Hero Section Text</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tripwiser_plan_trip_settings'); ?>
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
                                   value="<?php echo esc_attr(get_option('tw_pat_title','PLAN YOUR DREAM TRIP')); ?>"
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
                <?php settings_fields('tripwiser_plan_trip_settings'); ?>
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
    <?php tw_settings_page_footer();
}

// ============================================================
// BLOG & AFFILIATES PAGE SETTINGS
// ============================================================
function tw_render_blog_settings_page() {
    tw_settings_page_header('Blog & Affiliates Page', '📰', 'Edit the hero banner text and affiliate partner section on the Blog & Affiliates page.');
    ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Page Hero Text</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tripwiser_blog_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_blog_kicker">Kicker (small label above title)</label></th>
                        <td><input type="text" id="tw_blog_kicker" name="tw_blog_kicker" value="<?php echo esc_attr(get_option('tw_blog_kicker','TRAVEL GUIDES & AFFILIATE PICKS')); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th><label for="tw_blog_title">Main Heading</label></th>
                        <td><input type="text" id="tw_blog_title" name="tw_blog_title" value="<?php echo esc_attr(get_option('tw_blog_title','BLOGS + AFFILIATES')); ?>" class="large-text"></td>
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
                <?php settings_fields('tripwiser_blog_settings'); ?>
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
                    💡 <strong>Individual affiliate partner links</strong> (Booking.com, Skyscanner, etc.) are managed separately under the
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
    tw_settings_page_header('WhatsApp Chat Widget', '💬', 'The green floating chat button that appears on every page of your website.');
    ?>
    <div class="tw-admin-note success">
        ✅ <strong>No API needed.</strong> Just enter your WhatsApp number below and the widget will work immediately — visitors click the button and are taken straight to a WhatsApp chat with you.
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><h2>Widget Settings</h2></div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tripwiser_wa_settings'); ?>
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
                                   value="<?php echo esc_attr(get_option('tw_wa_widget_greeting','Hi there! 👋 How can we help you plan your perfect trip?')); ?>"
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
    tw_settings_page_header('WhatsApp Cloud API', '🤖', 'Send automatic trip inquiry confirmations directly to a customer\'s WhatsApp when they fill the Plan A Trip form.');
    ?>
    <div class="tw-admin-note warn">
        ⚠️ <strong>Requires a Meta Business API account.</strong> This is separate from the chat widget above — it uses the official WhatsApp Business Cloud API to <em>proactively send</em> messages to customers without them messaging you first. You must have a verified Meta Business Account and an approved message template.
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div><h2>API Credentials</h2><p>Get these from your <a href="https://developers.facebook.com/apps/" target="_blank">Meta Developer Console</a> → Your App → WhatsApp → API Setup.</p></div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tripwiser_wa_settings'); ?>
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
                            <p style="color:green;font-weight:700;margin-top:6px">✓ Token saved</p>
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
                <?php settings_fields('tripwiser_wa_settings'); ?>
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
                📖 <strong>Need a template?</strong> Create one at <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank">WhatsApp Manager → Message Templates</a>. Submit for approval — Meta usually approves within a few hours.
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

    // Fire WhatsApp notification hook
    do_action('tw_trip_inquiry_saved', $post_id, array(
        'name'        => $name,
        'phone'       => $phone,
        'email'       => $email,
        'destination' => $destination,
        'date'        => $date,
        'budget'      => $budget,
        'adults'      => $adults,
        'children'    => $children,
        'trip_type'   => $trip_type,
    ));

    wp_send_json_success(array('message' => 'Inquiry saved successfully.', 'id' => $post_id));
}
add_action('wp_ajax_submit_trip_inquiry',        'mytheme_submit_trip_inquiry');
add_action('wp_ajax_nopriv_submit_trip_inquiry', 'mytheme_submit_trip_inquiry');

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
