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
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css');
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

function mytheme_travel_filter_options($post_type) {
    if ($post_type === 'travel_package') {
        return array(
            'all' => __('All', 'mytheme'),
            'india' => __('India', 'mytheme'),
            'international' => __('International', 'mytheme'),
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
            'budget-under-30k' => __('Budget < 30K', 'mytheme'),
        );
    }

    return array(
        'all' => __('All', 'mytheme'),
        'india' => __('India', 'mytheme'),
        'international' => __('International', 'mytheme'),
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
        if (in_array($filter, array('india', 'international'), true)) {
            return array(
                array(
                    'key' => 'package_region',
                    'value' => $filter,
                    'compare' => '=',
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
            $atts     = array(
                'href'   => !empty($item->url) ? $item->url : '#',
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
    add_theme_support('custom-logo');
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

    // ── 3. TRIP INQUIRIES + PAGE SETTINGS ─────────────────
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
    add_submenu_page(
        'trip-inquiries',
        __('Page Settings', 'mytheme'),
        __('Page Settings', 'mytheme'),
        'manage_options',
        'tripwiser-settings',
        'mytheme_render_settings_page'
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
    <div class="wrap">
        <h1><?php esc_html_e('Trip Inquiries', 'mytheme'); ?>
            <span style="font-size:14px;font-weight:normal;color:#666;margin-left:8px;">
                <?php echo esc_html($query->found_posts); ?> total
            </span>
        </h1>
        <table class="wp-list-table widefat fixed striped" style="margin-top:12px">
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th><?php esc_html_e('Name', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Phone', 'mytheme'); ?></th>
                    <th><?php esc_html_e('Email', 'mytheme'); ?></th>
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
                <tr><td colspan="11" style="text-align:center;padding:24px;color:#666;"><?php esc_html_e('No inquiries yet. Form submissions will appear here.', 'mytheme'); ?></td></tr>
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
// SETTINGS: Register options for Plan A Trip + Blog pages
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
    // Affiliate URLs are now managed via the Affiliate Links CPT — not stored as options
}
add_action('admin_init', 'mytheme_register_page_settings');

// ============================================================
// SETTINGS PAGE RENDERER
// ============================================================
function mytheme_render_settings_page() {
    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'plan-trip';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('1TripWiser Page Settings', 'mytheme'); ?></h1>
        <nav class="nav-tab-wrapper" style="margin-bottom:0">
            <a href="?page=tripwiser-settings&tab=plan-trip" class="nav-tab <?php echo $tab === 'plan-trip' ? 'nav-tab-active' : ''; ?>">✈ Plan A Trip</a>
            <a href="?page=tripwiser-settings&tab=blog"      class="nav-tab <?php echo $tab === 'blog'      ? 'nav-tab-active' : ''; ?>">📰 Blog &amp; Affiliates</a>
        </nav>

        <?php if ($tab === 'plan-trip') : ?>
        <div style="background:#fff;border:1px solid #ccd0d4;border-top:none;padding:20px 24px">
        <form method="post" action="options.php">
            <?php settings_fields('tripwiser_plan_trip_settings'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="tw_pat_kicker"><?php esc_html_e('Hero Kicker', 'mytheme'); ?></label></th>
                    <td><input type="text" id="tw_pat_kicker" name="tw_pat_kicker" value="<?php echo esc_attr(get_option('tw_pat_kicker', 'YOUR PERSONALISED TRIP PLANNER')); ?>" class="regular-text" />
                    <p class="description">Small text shown above the main title</p></td>
                </tr>
                <tr>
                    <th><label for="tw_pat_title"><?php esc_html_e('Hero Title', 'mytheme'); ?></label></th>
                    <td><input type="text" id="tw_pat_title" name="tw_pat_title" value="<?php echo esc_attr(get_option('tw_pat_title', 'PLAN YOUR DREAM TRIP')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="tw_pat_subtitle"><?php esc_html_e('Hero Subtitle', 'mytheme'); ?></label></th>
                    <td><textarea id="tw_pat_subtitle" name="tw_pat_subtitle" class="large-text" rows="3"><?php echo esc_textarea(get_option('tw_pat_subtitle', "Tell us your dream destination, travel dates, and budget — we'll craft a personalised itinerary just for you.")); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="tw_pat_whatsapp"><?php esc_html_e('WhatsApp Number', 'mytheme'); ?></label></th>
                    <td><input type="text" id="tw_pat_whatsapp" name="tw_pat_whatsapp" value="<?php echo esc_attr(get_option('tw_pat_whatsapp', '919999999999')); ?>" class="regular-text" />
                    <p class="description">Country code + number, no spaces or +. Example: 919876543210</p></td>
                </tr>
            </table>
            <?php submit_button('Save Plan A Trip Settings'); ?>
        </form>
        </div>

        <?php elseif ($tab === 'blog') : ?>
        <div style="background:#fff;border:1px solid #ccd0d4;border-top:none;padding:20px 24px">
        <form method="post" action="options.php">
            <?php settings_fields('tripwiser_blog_settings'); ?>
            <h2 style="margin-top:0"><?php esc_html_e('Hero Section', 'mytheme'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="tw_blog_kicker"><?php esc_html_e('Hero Kicker', 'mytheme'); ?></label></th>
                    <td><input type="text" id="tw_blog_kicker" name="tw_blog_kicker" value="<?php echo esc_attr(get_option('tw_blog_kicker', 'TRAVEL GUIDES & AFFILIATE PICKS')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="tw_blog_title"><?php esc_html_e('Hero Title', 'mytheme'); ?></label></th>
                    <td><input type="text" id="tw_blog_title" name="tw_blog_title" value="<?php echo esc_attr(get_option('tw_blog_title', 'BLOGS + AFFILIATES')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="tw_blog_subtitle"><?php esc_html_e('Hero Subtitle', 'mytheme'); ?></label></th>
                    <td><textarea id="tw_blog_subtitle" name="tw_blog_subtitle" class="large-text" rows="3"><?php echo esc_textarea(get_option('tw_blog_subtitle', 'Honest travel guides. Trusted tools. Every link we share is something we actually use and believe in.')); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="tw_blog_aff_heading"><?php esc_html_e('Affiliate Banner Heading', 'mytheme'); ?></label></th>
                    <td><input type="text" id="tw_blog_aff_heading" name="tw_blog_aff_heading" value="<?php echo esc_attr(get_option('tw_blog_aff_heading', 'Our Trusted Travel Partners')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="tw_blog_aff_text"><?php esc_html_e('Affiliate Banner Text', 'mytheme'); ?></label></th>
                    <td><textarea id="tw_blog_aff_text" name="tw_blog_aff_text" class="large-text" rows="3"><?php echo esc_textarea(get_option('tw_blog_aff_text', "We partner with travel platforms we personally trust. When you book through our links, you support our free content at no extra cost to you.")); ?></textarea></td>
                </tr>
            </table>
            <div style="background:#f0f6ff;border:1px solid #c3d9ff;border-radius:6px;padding:12px 16px;margin-top:16px">
                <p style="margin:0;font-size:13px">
                    <strong>💡 Affiliate partner links</strong> are now managed under the
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=tw_affiliate')); ?>"><strong>Affiliate Links</strong></a>
                    menu — add or edit each partner there.
                </p>
            </div>
            <?php submit_button('Save Blog & Affiliates Settings'); ?>
        </form>
        </div>
        <?php endif; ?>
    </div>
    <?php
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
