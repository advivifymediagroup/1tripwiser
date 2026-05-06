<?php

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
    wp_enqueue_style('main-style', get_stylesheet_uri());
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), '1.0', true);
}


add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');

// Register travel content types and taxonomies
function mytheme_register_travel_content() {
    register_taxonomy('destination', array('post', 'itinerary', 'travel_package'), array(
        'labels' => array(
            'name' => __('Destinations', 'mytheme'),
            'singular_name' => __('Destination', 'mytheme'),
            'search_items' => __('Search Destinations', 'mytheme'),
            'all_items' => __('All Destinations', 'mytheme'),
            'edit_item' => __('Edit Destination', 'mytheme'),
            'update_item' => __('Update Destination', 'mytheme'),
            'add_new_item' => __('Add New Destination', 'mytheme'),
            'new_item_name' => __('New Destination Name', 'mytheme'),
            'menu_name' => __('Destinations', 'mytheme'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'destinations'),
    ));

    register_taxonomy('trip_style', array('post', 'itinerary', 'travel_package'), array(
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
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
        'taxonomies' => array('destination', 'trip_style'),
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
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
        'taxonomies' => array('destination', 'trip_style'),
    ));
}
add_action('init', 'mytheme_register_travel_content');

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
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form'));
}
add_action('after_setup_theme', 'mytheme_theme_support');

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
