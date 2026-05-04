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
    echo '<li><a href="' . home_url() . '">Home</a></li>';
    echo '<li><a href="' . get_permalink(get_option('page_for_posts')) . '">Blog</a></li>';
    echo '<li><a href="#">Destinations</a></li>';
    echo '<li><a href="#">Packages</a></li>';
    echo '<li><a href="#" class="cta-btn">Plan My Trip</a></li>';
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
