<?php
/**
 * 1TRIPWISER — Explore system
 * ------------------------------------------------------------------
 * Unifies the site's categories around taxonomies so they drive BOTH
 * the subheader navigation and every archive filter:
 *
 *   - destination_region  (existing, hierarchical):  India / International
 *                          → zones (North India, Asia…) → destinations (Goa, Bali…)
 *   - event_festival      (new, hierarchical):        Oktoberfest, Rann Utsav…
 *
 * Provides: taxonomy registration (events), starter seeding, taxonomy
 * archive query handling, the subheader mega-menu, and shared helpers
 * used by the archive filter boxes.
 *
 * Included from functions.php.
 *
 * @package my-theme
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* =============================================================
 * 1. EVENTS & FESTIVALS — a package-like custom post type.
 *    Each event is a special, bookable trip (NOT a category): it has
 *    its own page, price, duration, event date, region and Book Now.
 *    Managed in admin just like Travel Packages (shares the meta box).
 * ============================================================= */
function tw_explore_register_event_cpt() {
    register_post_type( 'tw_event', array(
        'labels' => array(
            'name'          => __( 'Events & Festivals', 'mytheme' ),
            'singular_name' => __( 'Event / Festival', 'mytheme' ),
            'add_new'       => __( 'Add Event', 'mytheme' ),
            'add_new_item'  => __( 'Add New Event', 'mytheme' ),
            'edit_item'     => __( 'Edit Event', 'mytheme' ),
            'new_item'      => __( 'New Event', 'mytheme' ),
            'view_item'     => __( 'View Event', 'mytheme' ),
            'search_items'  => __( 'Search Events', 'mytheme' ),
            'menu_name'     => __( 'Events & Festivals', 'mytheme' ),
        ),
        'public'        => true,
        'has_archive'   => false,                 // the /events-festivals/ page is the listing
        'menu_icon'     => 'dashicons-tickets-alt',
        'menu_position' => 27,
        'rewrite'       => array( 'slug' => 'event', 'with_front' => false ),
        'show_in_rest'  => true,
        'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'taxonomies'    => array( 'destination_region' ),
    ) );
}
add_action( 'init', 'tw_explore_register_event_cpt', 5 );

/* -----------------------------------------------------------------
 * ACF FIELD GROUPS — Events & Festivals + Group Trips
 * These PHP-registered (local) groups are the live fallback.
 * If the user imports acf-export-tw-event.json the DB group
 * (key: group_tw_event_v2) takes over and the local one is
 * skipped automatically via the early-return check below.
 * ----------------------------------------------------------------- */

/** Events & Festivals — mirrors Package Details + two event-only fields. */
function tw_event_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
    // If the DB-stored import (group_tw_event_v2) already exists, don't double-register.
    if ( function_exists( 'acf_get_field_group' ) && acf_get_field_group( 'group_tw_event_v2' ) ) { return; }

    $f = array(
        array( 'key' => 'field_twel_img',    'label' => 'Event Image',              'name' => 'package_image',       'type' => 'image',    'return_format' => 'array', 'preview_size' => 'medium', 'library' => 'all' ),
        array( 'key' => 'field_twel_loc',    'label' => 'Location / Destination',   'name' => 'package_location',    'type' => 'text',     'instructions' => 'e.g. Thailand, Munich',  'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_date',   'label' => 'Event Date',               'name' => 'event_date',          'type' => 'text',     'instructions' => 'e.g. 29 May 2026',       'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_fest',   'label' => 'Festival / Event Name',    'name' => 'event_festival_name', 'type' => 'text',     'instructions' => 'e.g. Tomorrowland, Oktoberfest' ),
        array( 'key' => 'field_twel_tag',    'label' => 'Event Tag',                'name' => 'package_tag',         'type' => 'select',   'choices' => array( 'bestseller' => 'Bestseller', 'trending' => 'Trending', 'new' => 'New', 'limited' => 'Limited Seats', 'popular' => 'Popular' ), 'allow_null' => 0, 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_nights', 'label' => 'Total Nights',             'name' => 'total_nights',        'type' => 'number',   'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_days',   'label' => 'Total Days',               'name' => 'total_days',          'type' => 'number',   'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_type',   'label' => 'Trip Type',                'name' => 'package_trip_type',   'type' => 'select',   'choices' => array( 'Group Trip' => 'Group Trip', 'Single Traveller' => 'Single Traveller', 'Private Trip' => 'Private Trip', 'Family Trip' => 'Family Trip', 'Honeymoon' => 'Honeymoon' ), 'allow_null' => 0, 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_amt',    'label' => 'Starting Price (₹)',       'name' => 'package_amount',      'type' => 'number',   'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_emi',    'label' => 'EMI Option',               'name' => 'package_emi',         'type' => 'text',     'instructions' => 'e.g. ₹8,333/mo', 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twel_book',   'label' => 'Book Now Button URL',      'name' => 'package_book_url',    'type' => 'url',      'instructions' => 'Leave blank to use this event page' ),
        array( 'key' => 'field_twel_ov',     'label' => 'Event Overview',           'name' => 'package_overview',    'type' => 'wysiwyg',  'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1 ),
        array( 'key' => 'field_twel_dest',   'label' => 'Linked Destination',       'name' => 'linked_destination',  'type' => 'post_object', 'post_type' => array( 'destination' ), 'post_status' => array( 'publish' ), 'return_format' => 'object', 'ui' => 1, 'allow_null' => 0 ),
        array( 'key' => 'field_twel_reg',    'label' => 'Event Region',             'name' => 'package_region',      'type' => 'checkbox', 'choices' => array( 'india' => 'India', 'international' => 'International', 'asia' => 'Asia', 'europe' => 'Europe' ), 'layout' => 'vertical' ),
        array( 'key' => 'field_twel_mon',    'label' => 'Available Months',         'name' => 'package_months',      'type' => 'checkbox', 'choices' => array( 'january' => 'January', 'february' => 'February', 'march' => 'March', 'april' => 'April', 'may' => 'May', 'june' => 'June', 'july' => 'July', 'august' => 'August', 'september' => 'September', 'october' => 'October', 'november' => 'November', 'december' => 'December' ), 'layout' => 'vertical' ),
    );
    acf_add_local_field_group( array(
        'key' => 'group_tw_event_local', 'title' => 'Event Details', 'fields' => $f,
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'tw_event' ) ) ),
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'active' => true,
    ) );
}
add_action( 'acf/init', 'tw_event_register_acf_fields' );

/** Group Trips — identical structure to Package Details with group-trip-specific tweaks. */
function tw_group_trip_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }

    $f = array(
        array( 'key' => 'field_twgt_img',    'label' => 'Trip Image',               'name' => 'package_image',       'type' => 'image',    'return_format' => 'array', 'preview_size' => 'medium', 'library' => 'all' ),
        array( 'key' => 'field_twgt_loc',    'label' => 'Location / Destination',   'name' => 'package_location',    'type' => 'text',     'instructions' => 'e.g. Manali, Bali, Meghalaya', 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_dep',    'label' => 'Departure Date',           'name' => 'event_date',          'type' => 'text',     'instructions' => 'Fixed departure date — e.g. 15 June 2026', 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_tag',    'label' => 'Trip Tag',                 'name' => 'package_tag',         'type' => 'select',   'choices' => array( 'bestseller' => 'Bestseller', 'trending' => 'Trending', 'new' => 'New', 'limited' => 'Limited Seats', 'popular' => 'Popular' ), 'allow_null' => 0, 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_nights', 'label' => 'Total Nights',             'name' => 'total_nights',        'type' => 'number',   'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_days',   'label' => 'Total Days',               'name' => 'total_days',          'type' => 'number',   'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_type',   'label' => 'Trip Type',                'name' => 'package_trip_type',   'type' => 'select',   'choices' => array( 'Group Trip' => 'Group Trip', 'Backpacking' => 'Backpacking', 'Adventure' => 'Adventure', 'Trekking' => 'Trekking', 'Weekend Getaway' => 'Weekend Getaway', 'Family Trip' => 'Family Trip' ), 'allow_null' => 0, 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_amt',    'label' => 'Price Per Person (₹)',     'name' => 'package_amount',      'type' => 'number',   'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_emi',    'label' => 'EMI Option',               'name' => 'package_emi',         'type' => 'text',     'instructions' => 'e.g. ₹2,166/mo', 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_size',   'label' => 'Group Size',               'name' => 'group_size',          'type' => 'text',     'instructions' => 'e.g. 8–15 Pax', 'wrapper' => array( 'width' => '50' ) ),
        array( 'key' => 'field_twgt_book',   'label' => 'Book Now Button URL',      'name' => 'package_book_url',    'type' => 'url',      'instructions' => 'Leave blank to use this group trip page' ),
        array( 'key' => 'field_twgt_ov',     'label' => 'Trip Overview',            'name' => 'package_overview',    'type' => 'wysiwyg',  'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1 ),
        array( 'key' => 'field_twgt_dest',   'label' => 'Linked Destination',       'name' => 'linked_destination',  'type' => 'post_object', 'post_type' => array( 'destination' ), 'post_status' => array( 'publish' ), 'return_format' => 'object', 'ui' => 1, 'allow_null' => 0 ),
        array( 'key' => 'field_twgt_reg',    'label' => 'Trip Region',              'name' => 'package_region',      'type' => 'checkbox', 'choices' => array( 'india' => 'India', 'international' => 'International', 'asia' => 'Asia', 'europe' => 'Europe' ), 'layout' => 'vertical' ),
        array( 'key' => 'field_twgt_mon',    'label' => 'Available Months',         'name' => 'package_months',      'type' => 'checkbox', 'choices' => array( 'january' => 'January', 'february' => 'February', 'march' => 'March', 'april' => 'April', 'may' => 'May', 'june' => 'June', 'july' => 'July', 'august' => 'August', 'september' => 'September', 'october' => 'October', 'november' => 'November', 'december' => 'December' ), 'layout' => 'vertical' ),
    );
    acf_add_local_field_group( array(
        'key' => 'group_tw_group_trip', 'title' => 'Group Trip Details', 'fields' => $f,
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'group_trip' ) ) ),
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'active' => true,
    ) );
}
add_action( 'acf/init', 'tw_group_trip_register_acf_fields' );

/* =============================================================
 * 1b. GROUP TRIPS — dedicated CPT with its own admin section
 * ============================================================= */
function tw_groups_register_cpt() {
    register_post_type( 'group_trip', array(
        'labels' => array(
            'name'          => __( 'Group Trips', 'mytheme' ),
            'singular_name' => __( 'Group Trip', 'mytheme' ),
            'add_new_item'  => __( 'Add New Group Trip', 'mytheme' ),
            'edit_item'     => __( 'Edit Group Trip', 'mytheme' ),
            'new_item'      => __( 'New Group Trip', 'mytheme' ),
            'view_item'     => __( 'View Group Trip', 'mytheme' ),
            'search_items'  => __( 'Search Group Trips', 'mytheme' ),
            'menu_name'     => __( 'Group Trips', 'mytheme' ),
        ),
        'public'        => true,
        'has_archive'   => 'group-trips',
        'menu_icon'     => 'dashicons-groups',
        'menu_position' => 26,
        'rewrite'       => array( 'slug' => 'group-trips', 'with_front' => false ),
        'show_in_rest'  => true,
        'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'taxonomies'    => array( 'destination_region', 'trip_style' ),
    ) );
}
add_action( 'init', 'tw_groups_register_cpt', 6 );

/* Force Classic Editor for tw_event and group_trip so ACF field panels display
   inline and prominently — identical to the Travel Packages admin experience.
   show_in_rest stays true for REST API access; only the admin editor is switched. */
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
    if ( in_array( $post_type, array( 'tw_event', 'group_trip' ), true ) ) {
        return false;
    }
    return $use_block_editor;
}, 10, 2 );

/* Flush rewrites once so /event/, /group-trips/ + region term URLs resolve.
   Bump the version to force a re-flush when rewrite-affecting rules change. */
function tw_explore_maybe_flush() {
    if ( get_option( 'tw_explore_rewrites_v' ) !== '3' ) {
        flush_rewrite_rules( false );
        update_option( 'tw_explore_rewrites_v', '3' );
    }
}
add_action( 'init', 'tw_explore_maybe_flush', 99 );

/* =============================================================
 * 2. STARTER SEEDING (idempotent; admin can edit/expand after)
 * ============================================================= */
function tw_explore_seed() {
    if ( get_option( 'tw_explore_seeded' ) === '1' ) { return; }
    if ( ! taxonomy_exists( 'destination_region' ) ) { return; }

    /* ---- Region tree: India / International → zones → destinations ---- */
    $regions = array(
        'India' => array(
            'icon'  => '🇮🇳',
            'zones' => array(
                'North India'      => array( 'Ladakh', 'Himachal', 'Kashmir', 'Uttarakhand', 'Rajasthan', 'Delhi' ),
                'South India'      => array( 'Kerala', 'Karnataka', 'Tamil Nadu', 'Goa', 'Andaman' ),
                'North-East India' => array( 'Meghalaya', 'Sikkim', 'Assam', 'Arunachal Pradesh' ),
                'West India'       => array( 'Maharashtra', 'Gujarat' ),
            ),
        ),
        'International' => array(
            'icon'  => '🌍',
            'zones' => array(
                'Asia'        => array( 'Bali', 'Thailand', 'Vietnam', 'Singapore', 'Malaysia', 'Japan', 'Bhutan', 'Nepal', 'Sri Lanka', 'Maldives' ),
                'Europe'      => array( 'France', 'Italy', 'Switzerland', 'Iceland', 'Greece' ),
                'Middle East' => array( 'Dubai', 'Abu Dhabi' ),
                'Americas'    => array( 'USA', 'Mexico' ),
            ),
        ),
    );

    foreach ( $regions as $top_name => $data ) {
        $top = tw_explore_ensure_term( $top_name, 'destination_region', 0 );
        if ( ! $top ) { continue; }
        update_term_meta( $top, 'tw_region_icon', $data['icon'] );
        update_term_meta( $top, 'tw_region_top', '1' );
        foreach ( $data['zones'] as $zone_name => $dests ) {
            $zone = tw_explore_ensure_term( $zone_name, 'destination_region', $top );
            if ( ! $zone ) { continue; }
            foreach ( $dests as $dest_name ) {
                tw_explore_ensure_term( $dest_name, 'destination_region', $zone );
            }
        }
    }

    /* Events & Festivals are now a CPT (see demo seeder), not seeded terms. */

    update_option( 'tw_explore_seeded', '1' );
}
add_action( 'init', 'tw_explore_seed', 25 );

/** Create a term if missing; return its ID (or existing ID). */
function tw_explore_ensure_term( $name, $taxonomy, $parent = 0 ) {
    $existing = get_term_by( 'name', $name, $taxonomy );
    if ( $existing && (int) $existing->parent === (int) $parent ) {
        return (int) $existing->term_id;
    }
    $res = wp_insert_term( $name, $taxonomy, array( 'parent' => $parent ) );
    if ( is_wp_error( $res ) ) {
        // Name may exist under a different parent — fall back to any match
        return $existing ? (int) $existing->term_id : 0;
    }
    return (int) $res['term_id'];
}

/* =============================================================
 * 3. TAXONOMY ARCHIVE QUERIES — show trips, not just blog posts
 * ============================================================= */
function tw_explore_tax_query( $q ) {
    if ( is_admin() || ! $q->is_main_query() ) { return; }
    if ( $q->is_tax( 'destination_region' ) ) {
        $q->set( 'post_type', array( 'travel_package', 'itinerary', 'group_trip', 'tw_event', 'destination', 'post' ) );
        $q->set( 'posts_per_page', 12 );
    }
}
add_action( 'pre_get_posts', 'tw_explore_tax_query' );

/* Apply ?region taxonomy filtering on the package/itinerary/group/event/destination
   archives so the unified region categories work in every archive filter. */
function tw_explore_archive_filter( $q ) {
    if ( is_admin() || ! $q->is_main_query() ) { return; }
    if ( $q->is_post_type_archive( array( 'travel_package', 'itinerary', 'group_trip', 'tw_event', 'destination' ) ) ) {
        $tax = tw_explore_active_tax_query();
        if ( $tax ) {
            $existing = $q->get( 'tax_query' );
            $q->set( 'tax_query', array_merge( is_array( $existing ) ? $existing : array(), $tax ) );
        }
    }
}
add_action( 'pre_get_posts', 'tw_explore_archive_filter' );

/* Universal search — make WordPress search span every content type so a query
   like "Bali" returns packages, itineraries, group trips, events, destinations,
   blogs and Tribe forum topics together. */
function tw_explore_search_query( $q ) {
    if ( is_admin() || ! $q->is_main_query() ) { return; }
    if ( $q->is_search() ) {
        $q->set( 'post_type', array( 'travel_package', 'itinerary', 'group_trip', 'tw_event', 'destination', 'post', 'forum_topic' ) );
        $q->set( 'posts_per_page', 24 );
    }
}
add_action( 'pre_get_posts', 'tw_explore_search_query' );

/* =============================================================
 * 4. HELPERS
 * ============================================================= */
function tw_region_icon( $term_id ) {
    return get_term_meta( $term_id, 'tw_region_icon', true );
}
/** Legacy fallback icon (events are now a CPT; kept to avoid stray call errors). */
function tw_event_icon( $term_id = 0 ) { return '🎉'; }
/** Top-level region terms (India, International). */
function tw_region_top_terms() {
    return get_terms( array(
        'taxonomy'   => 'destination_region',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
}
/** Direct children of a term. */
function tw_region_children( $parent_id ) {
    return get_terms( array(
        'taxonomy'   => 'destination_region',
        'hide_empty' => false,
        'parent'     => (int) $parent_id,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
}
/** Recent Events & Festivals (the CPT). */
function tw_recent_events( $limit = 8 ) {
    return get_posts( array(
        'post_type'      => 'tw_event',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
}
/** URL of the Events & Festivals landing page (fallback: event CPT first post / home). */
function tw_events_page_url() {
    $page = get_page_by_path( 'events-festivals' );
    return $page ? get_permalink( $page->ID ) : home_url( '/events-festivals/' );
}

/** Region/destination terms that actually have group trips attached. */
function tw_group_trip_destinations() {
    $ids = get_posts( array(
        'post_type'      => 'group_trip',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ) );
    if ( empty( $ids ) ) { return array(); }
    $term_ids = wp_get_object_terms( $ids, 'destination_region', array( 'fields' => 'ids' ) );
    if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) { return array(); }
    $terms = array();
    foreach ( array_unique( $term_ids ) as $tid ) {
        $t = get_term( $tid, 'destination_region' );
        if ( $t && ! is_wp_error( $t ) ) { $terms[] = $t; }
    }
    usort( $terms, function ( $a, $b ) { return strcmp( $a->name, $b->name ); } );
    return $terms;
}

/** URL to the group-trips archive filtered by a region term slug. */
function tw_group_trip_region_url( $slug = '' ) {
    $base = get_post_type_archive_link( 'group_trip' );
    return $slug ? add_query_arg( 'region', $slug, $base ) : $base;
}

/* =============================================================
 * 5. SUBHEADER MEGA-MENU  (replaces the old travel-tabs)
 * ============================================================= */
function tw_explore_subheader() {
    $top_terms = tw_region_top_terms();
    $events    = tw_recent_events( 8 );
    ?>
    <nav class="tw-sub" aria-label="<?php esc_attr_e( 'Explore navigation', 'mytheme' ); ?>">
        <div class="container tw-sub-inner">

            <?php foreach ( $top_terms as $top ) :
                $zones = tw_region_children( $top->term_id );
                if ( empty( $zones ) ) { continue; }
                $icon = tw_region_icon( $top->term_id );
            ?>
            <div class="tw-sub-item has-mega">
                <a class="tw-sub-link" href="<?php echo esc_url( get_term_link( $top ) ); ?>">
                    <?php echo esc_html( trim( $icon . ' ' . $top->name ) ); ?>
                    <span class="tw-sub-caret" aria-hidden="true">▾</span>
                </a>
                <div class="tw-mega" role="menu">
                    <div class="tw-mega-grid">
                        <?php foreach ( $zones as $zone ) :
                            $dests = tw_region_children( $zone->term_id ); ?>
                            <div class="tw-mega-col">
                                <a class="tw-mega-head" href="<?php echo esc_url( get_term_link( $zone ) ); ?>"><?php echo esc_html( $zone->name ); ?></a>
                                <?php if ( $dests ) : ?>
                                    <ul class="tw-mega-list">
                                        <?php foreach ( $dests as $d ) : ?>
                                            <li><a href="<?php echo esc_url( get_term_link( $d ) ); ?>"><?php echo esc_html( $d->name ); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php
            /* Group Trips — destinations that have group trips */
            $group_dests = tw_group_trip_destinations();
            ?>
            <div class="tw-sub-item has-mega">
                <a class="tw-sub-link" href="<?php echo esc_url( get_post_type_archive_link( 'group_trip' ) ); ?>">
                    👥 Group Trips
                    <span class="tw-sub-caret" aria-hidden="true">▾</span>
                </a>
                <div class="tw-mega tw-mega-events" role="menu">
                    <?php if ( $group_dests ) : ?>
                    <div class="tw-mega-events-grid">
                        <?php foreach ( $group_dests as $d ) : ?>
                            <a class="tw-mega-event" href="<?php echo esc_url( tw_group_trip_region_url( $d->slug ) ); ?>">
                                <span class="tw-mega-event-icon">📍</span>
                                <span class="tw-mega-event-name"><?php echo esc_html( $d->name ); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else : ?>
                        <div class="tw-mega-empty">
                            <p>No group trips yet.</p>
                            <a class="tw-mega-allcta" href="<?php echo esc_url( get_post_type_archive_link( 'group_trip' ) ); ?>">Browse all group trips →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tw-sub-item has-mega">
                <a class="tw-sub-link" href="<?php echo esc_url( tw_events_page_url() ); ?>">
                    🎉 Events &amp; Festivals
                    <span class="tw-sub-caret" aria-hidden="true">▾</span>
                </a>
                <div class="tw-mega tw-mega-events" role="menu">
                    <?php if ( $events ) : ?>
                    <div class="tw-mega-events-grid">
                        <?php foreach ( $events as $ev ) :
                            $cur = get_queried_object_id() === $ev->ID; ?>
                            <a class="tw-mega-event <?php echo $cur ? 'active' : ''; ?>" href="<?php echo esc_url( get_permalink( $ev->ID ) ); ?>">
                                <span class="tw-mega-event-icon">🎟️</span>
                                <span class="tw-mega-event-name"><?php echo esc_html( get_the_title( $ev->ID ) ); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else : ?>
                        <div class="tw-mega-empty">
                            <p>No events yet.</p>
                            <a class="tw-mega-allcta" href="<?php echo esc_url( tw_events_page_url() ); ?>">View Events &amp; Festivals →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </nav>
    <?php
}

/* =============================================================
 * 6. UNIFIED REGION FILTER  (taxonomy-based, used by archives)
 *    ?region={term-slug} → tax_query on destination_region
 * ============================================================= */
function tw_explore_region_filter_terms() {
    // Top regions + their zones, flattened for a filter row
    $out = array();
    foreach ( tw_region_top_terms() as $top ) {
        $out[] = $top;
        foreach ( tw_region_children( $top->term_id ) as $zone ) {
            $out[] = $zone;
        }
    }
    return $out;
}

/** Build a tax_query array for the active ?region param (or empty). */
function tw_explore_active_tax_query() {
    $tax = array();
    if ( ! empty( $_GET['region'] ) ) {
        $slug = sanitize_title( wp_unslash( $_GET['region'] ) );
        $term = get_term_by( 'slug', $slug, 'destination_region' );
        if ( $term ) {
            $tax[] = array(
                'taxonomy'         => 'destination_region',
                'field'            => 'term_id',
                'terms'            => array( $term->term_id ),
                'include_children' => true,
            );
        }
    }
    return $tax;
}

/**
 * Homepage "Events & Festivals" showcase row.
 * Renders Event CPT posts as cards (image, name, price/date) → each event page.
 */
function tw_explore_events_showcase( $limit = 8 ) {
    $events = tw_recent_events( $limit );
    if ( empty( $events ) ) { return; }
    ?>
    <section class="tw-events-showcase">
        <div class="container">
            <div class="section-heading" data-reveal="up">
                <span>Plan around the moment</span>
                <h2>Events &amp; Festivals</h2>
            </div>
            <div class="tw-events-grid">
                <?php foreach ( $events as $ev ) :
                    $thumb = get_the_post_thumbnail_url( $ev->ID, 'medium_large' );
                    $price = function_exists( 'mytheme_get_travel_field' ) ? ( mytheme_get_travel_field( 'starting_price', $ev->ID ) ?: mytheme_get_travel_field( 'package_amount', $ev->ID ) ) : '';
                    $date  = function_exists( 'mytheme_get_travel_field' ) ? mytheme_get_travel_field( 'event_date', $ev->ID ) : '';
                ?>
                    <a class="tw-event-card" href="<?php echo esc_url( get_permalink( $ev->ID ) ); ?>" data-reveal="scale">
                        <div class="tw-event-card-media" <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
                            <?php if ( ! $thumb ) : ?><span class="tw-event-card-icon">🎟️</span><?php endif; ?>
                        </div>
                        <div class="tw-event-card-body">
                            <h3 class="tw-event-card-name"><?php echo esc_html( get_the_title( $ev->ID ) ); ?></h3>
                            <span class="tw-event-card-count">
                                <?php
                                if ( $date )  { echo esc_html( $date ); }
                                if ( $price ) { echo $date ? ' · ' : ''; echo esc_html( function_exists( 'mytheme_format_rupee_amount' ) ? mytheme_format_rupee_amount( $price ) : $price ); }
                                if ( ! $date && ! $price ) { echo 'View event →'; }
                                ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Render a taxonomy-based region filter row for an archive.
 * $base_url is the archive URL; preserves the design of .travel-filter-box.
 */
function tw_explore_region_filter_box( $base_url, $anchor = '' ) {
    $active = ! empty( $_GET['region'] ) ? sanitize_title( wp_unslash( $_GET['region'] ) ) : '';
    ?>
    <div class="travel-filter-box" aria-label="<?php esc_attr_e( 'Region filters', 'mytheme' ); ?>">
        <span class="travel-filter-label"><?php esc_html_e( 'Region', 'mytheme' ); ?></span>
        <div class="travel-filter-options">
            <a class="<?php echo $active === '' ? 'active' : ''; ?>" href="<?php echo esc_url( remove_query_arg( array( 'region', 'paged' ), $base_url ) . $anchor ); ?>"><?php esc_html_e( 'All', 'mytheme' ); ?></a>
            <?php foreach ( tw_region_top_terms() as $top ) :
                $slug = $top->slug;
                $url  = remove_query_arg( 'paged', add_query_arg( 'region', $slug, $base_url ) );
            ?>
                <a class="<?php echo $active === $slug ? 'active' : ''; ?>" href="<?php echo esc_url( $url . $anchor ); ?>">
                    <?php echo esc_html( trim( tw_region_icon( $top->term_id ) . ' ' . $top->name ) ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * UNIFIED filter bar for the package / itinerary archives — one bar that combines
 * the region (taxonomy) chips AND the meta chips (Budget, Bestseller…). Region uses
 * ?region= (tax_query) and meta uses ?package_filter / ?itinerary_filter (meta_query);
 * both apply via separate pre_get_posts hooks so they can be combined.
 */
function tw_explore_unified_filter_box( $post_type, $base_url, $anchor = '' ) {
    $param         = ( $post_type === 'itinerary' ) ? 'itinerary_filter' : 'package_filter';
    $active_region = ! empty( $_GET['region'] ) ? sanitize_title( wp_unslash( $_GET['region'] ) ) : '';
    $active_meta   = isset( $_GET[ $param ] ) ? sanitize_key( wp_unslash( $_GET[ $param ] ) ) : '';

    /* Region chips: top regions + the popular international zones */
    $region_chips = array();
    foreach ( array( 'India', 'International', 'Asia', 'Europe' ) as $rn ) {
        $t = get_term_by( 'name', $rn, 'destination_region' );
        if ( $t ) { $region_chips[] = $t; }
    }

    /* Meta chips (non-region) */
    $meta_chips = ( $post_type === 'itinerary' )
        ? array( 'budget-under-30k' => __( 'Budget < 30K', 'mytheme' ) )
        : array(
            'budget-under-30k' => __( 'Budget < 30K', 'mytheme' ),
            'bestseller'       => __( 'Bestseller', 'mytheme' ),
            'trending'         => __( 'Trending', 'mytheme' ),
            'new'              => __( 'New', 'mytheme' ),
        );

    /* Helper to build a URL that PRESERVES the other active filter (so they combine) */
    $build = function ( $set_region, $set_meta ) use ( $base_url, $param, $anchor ) {
        $url = remove_query_arg( array( 'region', $param, 'paged' ), $base_url );
        if ( $set_region ) { $url = add_query_arg( 'region', $set_region, $url ); }
        if ( $set_meta )   { $url = add_query_arg( $param, $set_meta, $url ); }
        return esc_url( $url . $anchor );
    };
    ?>
    <div class="tw-filter" aria-label="<?php esc_attr_e( 'Filters', 'mytheme' ); ?>">
        <span class="tw-filter-label"><?php esc_html_e( 'Filter', 'mytheme' ); ?></span>
        <div class="tw-filter-chips">
            <a class="tw-fchip <?php echo ( ! $active_region && ! $active_meta ) ? 'active' : ''; ?>" href="<?php echo $build( '', '' ); ?>"><?php esc_html_e( 'All', 'mytheme' ); ?></a>

            <?php foreach ( $region_chips as $t ) : ?>
                <a class="tw-fchip <?php echo $active_region === $t->slug ? 'active' : ''; ?>" href="<?php echo $build( $t->slug, $active_meta ); ?>">
                    <?php echo esc_html( trim( tw_region_icon( $t->term_id ) . ' ' . $t->name ) ); ?>
                </a>
            <?php endforeach; ?>

            <span class="tw-filter-sep" aria-hidden="true"></span>

            <?php foreach ( $meta_chips as $val => $label ) : ?>
                <a class="tw-fchip tw-fchip-meta <?php echo $active_meta === $val ? 'active' : ''; ?>" href="<?php echo $build( $active_region, $val ); ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
