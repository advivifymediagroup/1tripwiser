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
            <a class="<?php echo $plan_active ? 'active' : ''; ?>" href="#">💰Blogs + Affiliates</a>
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
