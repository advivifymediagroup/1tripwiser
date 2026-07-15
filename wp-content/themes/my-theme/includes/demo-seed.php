<?php
/**
 * 1TRIPWISER — Demo content seeder
 * ------------------------------------------------------------------
 * Tools → "Demo Content": one click populates sample content across
 * every feature so the site can be shown end-to-end to leadership.
 * Idempotent (skips items that already exist) and reversible (every
 * demo post is tagged with _tw_demo=1 so it can be removed cleanly).
 *
 * @package my-theme
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* =============================================================
 * ADMIN PAGE
 * ============================================================= */
add_action( 'admin_menu', function () {
    add_management_page(
        'TripWiser Demo Content',
        'Demo Content',
        'manage_options',
        'tw-demo-content',
        'tw_demo_admin_page'
    );
} );

function tw_demo_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }

    $notice = '';
    if ( isset( $_POST['tw_demo_action'] ) && check_admin_referer( 'tw_demo_run', 'tw_demo_nonce' ) ) {
        if ( $_POST['tw_demo_action'] === 'generate' ) {
            $n = tw_demo_generate();
            $notice = "<div class='notice notice-success'><p><i class='fi-rr-check-circle' aria-hidden='true'></i> Demo content generated. Created {$n} new item(s). Re-running only adds what's missing.</p></div>";
        } elseif ( $_POST['tw_demo_action'] === 'remove' ) {
            $n = tw_demo_remove();
            $notice = "<div class='notice notice-success'><p><i class='fi-rr-trash' aria-hidden='true'></i> Removed {$n} demo item(s).</p></div>";
        }
    }

    echo '<div class="wrap">';
    echo '<h1>TripWiser Demo Content</h1>';
    echo wp_kses_post( $notice );
    echo '<p>Populate the whole site with realistic sample content — travel packages, itineraries, group trips, destinations, event-festival packages, blog posts and Tribe forum topics — to demo every feature. Featured images are pulled from picsum.photos.</p>';
    echo '<form method="post" style="margin-top:18px;display:flex;gap:12px;flex-wrap:wrap">';
    wp_nonce_field( 'tw_demo_run', 'tw_demo_nonce' );
    echo '<button class="button button-primary button-hero" name="tw_demo_action" value="generate">Generate Demo Content</button>';
    echo '<button class="button button-secondary button-hero" name="tw_demo_action" value="remove" onclick="return confirm(\'Remove all demo content?\')">Remove Demo Content</button>';
    echo '</form>';
    echo '<p style="color:#666;margin-top:16px">Tip: image download needs outbound internet from the server. If images fail, the content still seeds and cards show placeholder art.</p>';
    echo '</div>';
}

/* =============================================================
 * HELPERS
 * ============================================================= */

/** Ensure a region destination term under a top → zone hierarchy; return leaf term id. */
function tw_demo_region( $dest, $zone, $top ) {
    if ( ! function_exists( 'tw_explore_ensure_term' ) ) { return 0; }
    $top_id  = tw_explore_ensure_term( $top, 'destination_region', 0 );
    $zone_id = tw_explore_ensure_term( $zone, 'destination_region', $top_id );
    return tw_explore_ensure_term( $dest, 'destination_region', $zone_id );
}

/** Sideload a featured image (resilient — silently skips on failure). */
function tw_demo_thumb( $post_id, $seed ) {
    if ( has_post_thumbnail( $post_id ) ) { return; }
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $url = 'https://picsum.photos/seed/' . rawurlencode( $seed ) . '/900/600';
    $att = @media_sideload_image( $url, $post_id, get_the_title( $post_id ), 'id' );
    if ( ! is_wp_error( $att ) ) { set_post_thumbnail( $post_id, $att ); }
}

/**
 * Create a demo post if one with the slug doesn't already exist.
 * Returns post ID (new or existing), and a flag whether it was newly created.
 */
function tw_demo_post( $args, &$created_count ) {
    $existing = get_page_by_path( $args['post_name'], OBJECT, $args['post_type'] );
    if ( $existing ) { return $existing->ID; }

    $post_id = wp_insert_post( array(
        'post_type'    => $args['post_type'],
        'post_title'   => $args['post_title'],
        'post_name'    => $args['post_name'],
        'post_content' => $args['post_content'],
        'post_excerpt' => isset( $args['post_excerpt'] ) ? $args['post_excerpt'] : '',
        'post_status'  => 'publish',
    ), true );

    if ( is_wp_error( $post_id ) ) { return 0; }
    update_post_meta( $post_id, '_tw_demo', 1 );
    $created_count++;

    if ( ! empty( $args['meta'] ) ) {
        foreach ( $args['meta'] as $k => $v ) { update_post_meta( $post_id, $k, $v ); }
    }
    if ( ! empty( $args['regions'] ) ) {
        wp_set_object_terms( $post_id, $args['regions'], 'destination_region' );
    }
    if ( ! empty( $args['category'] ) ) {
        $cat_id = get_cat_ID( $args['category'] );
        if ( ! $cat_id ) { $cat_id = wp_create_category( $args['category'] ); }
        if ( $cat_id ) { wp_set_post_categories( $post_id, array( $cat_id ) ); }
    }
    if ( ! empty( $args['forum_cat'] ) ) {
        $t = get_term_by( 'slug', $args['forum_cat'], 'forum_category' );
        if ( $t ) { wp_set_object_terms( $post_id, array( (int) $t->term_id ), 'forum_category' ); }
    }
    if ( ! empty( $args['thumb_seed'] ) ) { tw_demo_thumb( $post_id, $args['thumb_seed'] ); }

    return $post_id;
}

/**
 * Build a complete meta set so EVERY field the templates read is filled —
 * the unified simple keys (single-page detail rows), the package_* keys
 * (cards + package hero) and the itinerary_* keys (itinerary cards).
 */
function tw_demo_full_meta( $c ) {
    $c = array_merge( array(
        'location' => '', 'price' => '', 'nights' => '', 'days' => '', 'duration' => '',
        'best_time' => '', 'group_size' => '', 'trip_type' => '', 'tag' => '', 'emi' => '',
        'route' => '', 'overview' => '', 'book_url' => '', 'event_date' => '',
    ), $c );

    $m = array(
        // Unified simple fields (single-page "Travel Details" rows + fallbacks)
        '_destination_name'        => $c['location'],
        '_trip_duration'           => $c['duration'],
        '_starting_price'          => $c['price'],
        '_best_time'               => $c['best_time'],
        '_group_size'              => $c['group_size'],
        '_route_summary'           => $c['route'],
        // package_* keys (package card + single hero)
        '_package_location'        => $c['location'],
        '_package_amount'          => $c['price'],
        '_package_nights'          => $c['nights'],
        '_package_days'            => $c['days'],
        '_package_trip_type'       => $c['trip_type'],
        '_package_tag'             => $c['tag'],
        '_package_emi'             => $c['emi'],
        '_package_overview'        => $c['overview'],
        // itinerary_* keys (itinerary card)
        '_itinerary_duration'      => $c['duration'],
        '_itinerary_best_time'     => $c['best_time'],
        '_itinerary_route_summary' => $c['route'],
        '_itinerary_budget'        => $c['price'],
    );
    if ( $c['book_url'] !== '' )   { $m['_package_book_url'] = $c['book_url']; }
    if ( $c['event_date'] !== '' ) { $m['_event_date'] = $c['event_date']; }
    return array_filter( $m, function ( $v ) { return $v !== '' && $v !== null; } );
}

/* =============================================================
 * GENERATE
 * ============================================================= */
function tw_demo_generate() {
    $created = 0;

    /* ---- Region leaves we need (ensures hierarchy too) ---- */
    $bali     = tw_demo_region( 'Bali', 'Asia', 'International' );
    $thailand = tw_demo_region( 'Thailand', 'Asia', 'International' );
    $singapore= tw_demo_region( 'Singapore', 'Asia', 'International' );
    $vietnam  = tw_demo_region( 'Vietnam', 'Asia', 'International' );
    $spain    = tw_demo_region( 'Spain', 'Europe', 'International' );
    $germany  = tw_demo_region( 'Germany', 'Europe', 'International' );
    $neth     = tw_demo_region( 'Netherlands', 'Europe', 'International' );
    $switz    = tw_demo_region( 'Switzerland', 'Europe', 'International' );
    $kerala   = tw_demo_region( 'Kerala', 'South India', 'India' );
    $goa      = tw_demo_region( 'Goa', 'South India', 'India' );
    $ladakh   = tw_demo_region( 'Ladakh', 'North India', 'India' );
    $himachal = tw_demo_region( 'Himachal', 'North India', 'India' );
    $rajasthan= tw_demo_region( 'Rajasthan', 'North India', 'India' );
    $megh     = tw_demo_region( 'Meghalaya', 'North-East India', 'India' );

    /* ---- TRAVEL PACKAGES ---- */
    $packages = array(
        array(
            'title' => 'Bali Bliss — Island Escape', 'slug' => 'demo-bali-bliss', 'regions' => array( $bali ), 'thumb' => 'bali1',
            'excerpt' => 'Beaches, temples and rice terraces across Ubud, Seminyak and Nusa Penida.',
            'content' => '<p>Experience the Island of Gods with ancient temples, lush terraced rice fields, vibrant beach clubs and world-class surfing. Includes accommodation, flights, daily breakfast, a Kecak fire-dance show and a guided Nusa Penida day trip.</p>',
            'core' => array( 'location' => 'Bali, Indonesia', 'price' => '49999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'April to October', 'group_size' => '2–12 Pax', 'trip_type' => 'Group Trip', 'tag' => 'Best Seller', 'emi' => '₹8,333/mo', 'route' => 'Mumbai → Denpasar → Ubud → Seminyak → Nusa Penida → Bali', 'overview' => 'Ancient temples, lush terraced rice fields and vibrant beach clubs — Bali has it all.' ),
        ),
        array(
            'title' => 'Kerala Backwaters & Beaches', 'slug' => 'demo-kerala-backwaters', 'regions' => array( $kerala ), 'thumb' => 'kerala1',
            'excerpt' => 'Houseboats in Alleppey, tea hills of Munnar and the beaches of Kovalam.',
            'content' => '<p>Glide through the mirror-like backwaters of Alleppey on a private houseboat, wake up among Munnar\'s misty tea gardens and end the trip on the golden sands of Kovalam. A perfect mix of nature, culture and relaxation.</p>',
            'core' => array( 'location' => 'Kerala, India', 'price' => '24999', 'nights' => '5', 'days' => '6', 'duration' => '5 Nights 6 Days', 'best_time' => 'September to March', 'group_size' => '2–8 Pax', 'trip_type' => 'Couple Trip', 'tag' => 'Popular', 'emi' => '₹4,166/mo', 'route' => 'Cochin → Munnar → Thekkady → Alleppey → Kovalam → Trivandrum', 'overview' => 'God\'s own country — backwaters, mist-covered hills and pristine beaches all in one trip.' ),
        ),
        array(
            'title' => 'Ladakh High-Altitude Adventure', 'slug' => 'demo-ladakh-adventure', 'regions' => array( $ladakh ), 'thumb' => 'ladakh1',
            'excerpt' => 'Pangong Lake, Nubra Valley and the highest motorable passes in the world.',
            'content' => '<p>Ride across the roof of the world — Khardung La, Chang La and the otherworldly Pangong Lake. Camp under a billion stars in Nubra Valley, spot Bactrian camels and witness monastic life at Hemis and Thiksey. Trip includes permits, accommodation and 4×4 jeep support.</p>',
            'core' => array( 'location' => 'Ladakh, India', 'price' => '32999', 'nights' => '7', 'days' => '8', 'duration' => '7 Nights 8 Days', 'best_time' => 'June to September', 'group_size' => '6–15 Pax', 'trip_type' => 'Group Trip', 'tag' => 'Adventure', 'emi' => '₹5,499/mo', 'route' => 'Delhi → Leh → Nubra Valley → Pangong Lake → Leh → Delhi', 'overview' => 'High-altitude desert landscapes, ancient monasteries and some of the world\'s highest motorable passes.' ),
        ),
        array(
            'title' => 'Vietnam Explorer', 'slug' => 'demo-vietnam-package', 'regions' => array( $vietnam ), 'thumb' => 'vietpkg1',
            'excerpt' => 'Hanoi, Ha Long Bay, Hoi An and Ho Chi Minh in one seamless trip.',
            'content' => '<p>Journey from the bustling streets of Hanoi to the emerald karst islands of Ha Long Bay, the lantern-lit lanes of Hoi An and the electric energy of Ho Chi Minh City. Includes a 2-night Ha Long Bay cruise, heritage walking tours and street-food trails.</p>',
            'core' => array( 'location' => 'Vietnam', 'price' => '59999', 'nights' => '7', 'days' => '8', 'duration' => '7 Nights 8 Days', 'best_time' => 'November to April', 'group_size' => '2–10 Pax', 'trip_type' => 'International', 'tag' => 'Trending', 'emi' => '₹9,999/mo', 'route' => 'Delhi → Hanoi → Ha Long Bay → Hue → Hoi An → Ho Chi Minh City → Delhi', 'overview' => 'From Hanoi\'s ancient quarter to Ha Long Bay\'s karst islands — Vietnam is a feast for every sense.' ),
        ),
        array(
            'title' => 'Switzerland Dream Escape', 'slug' => 'demo-switzerland-package', 'regions' => array( $switz ), 'thumb' => 'swisspkg1',
            'excerpt' => 'Zurich, Lucerne, Interlaken and the iconic Jungfraujoch.',
            'content' => '<p>Lose yourself in chocolate-box Swiss scenery — the medieval old town of Zurich, the mirrored chapel bridge in Lucerne, tandem paragliding in Interlaken and the "Top of Europe" at Jungfraujoch (3,454 m). Includes Swiss Travel Pass for unlimited trains and cable cars.</p>',
            'core' => array( 'location' => 'Switzerland', 'price' => '189999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'June to September', 'group_size' => '2–8 Pax', 'trip_type' => 'Luxury', 'tag' => 'Premium', 'emi' => '₹31,666/mo', 'route' => 'Delhi → Zurich → Lucerne → Interlaken → Jungfraujoch → Bern → Delhi', 'overview' => 'Alpine meadows, glacier railways and chalet villages — Switzerland is nature\'s masterpiece.' ),
        ),
    );
    foreach ( $packages as $p ) {
        tw_demo_post( array(
            'post_type'    => 'travel_package',
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_content' => $p['content'],
            'post_excerpt' => $p['excerpt'],
            'regions'      => array_filter( $p['regions'] ),
            'meta'         => tw_demo_full_meta( $p['core'] ),
            'thumb_seed'   => $p['thumb'],
        ), $created );
    }

    /* ---- EVENTS & FESTIVALS (special bookable packages — the tw_event CPT) ---- */
    $events_data = array(
        array(
            'title' => 'Thailand Full Moon Party', 'slug' => 'demo-event-fullmoon', 'regions' => array( $thailand ), 'thumb' => 'thai1',
            'excerpt' => 'Phuket, Krabi and the legendary Full Moon Party at Koh Phangan.',
            'content' => '<p>The world\'s biggest beach rave — island-hop through Koh Samui\'s beach clubs, attend the iconic Full Moon Party at Haad Rin beach and wind down on the turquoise shores of Phuket. Package includes flights, hotels, party entry wristbands and guided excursions.</p>',
            'acf' => array( 'destination_name' => 'Thailand', 'trip_duration' => '6 Nights 7 Days', 'starting_price' => 49999, 'event_date' => '29 May 2026', 'best_time' => 'May to November', 'group_size' => '8–20 Pax', 'route_summary' => 'Bangkok → Koh Samui → Koh Phangan → Phuket', 'book_url' => '' ),
        ),
        array(
            'title' => 'Amsterdam Tomorrowland Beats', 'slug' => 'demo-event-tomorrowland', 'regions' => array( $neth ), 'thumb' => 'ams1',
            'excerpt' => 'Amsterdam canals, Brussels and the Tomorrowland mainstage in Boom, Belgium.',
            'content' => '<p>Two of Europe\'s most iconic cities wrapped around the world\'s greatest music festival. Canal cruises and museum hopping in Amsterdam, waffles and beer in Brussels, then three days at Tomorrowland\'s legendary mainstage in Boom. Full package: flights, festival tickets, hotels and transfers.</p>',
            'acf' => array( 'destination_name' => 'Belgium & Netherlands', 'trip_duration' => '7 Nights 8 Days', 'starting_price' => 299999, 'event_date' => '18 July 2026', 'best_time' => 'July', 'group_size' => '4–12 Pax', 'route_summary' => 'Mumbai → Amsterdam → Brussels → Boom (Tomorrowland) → Amsterdam', 'book_url' => '' ),
        ),
        array(
            'title' => 'Spain La Tomatina Fiesta', 'slug' => 'demo-event-tomatina', 'regions' => array( $spain ), 'thumb' => 'spain1',
            'excerpt' => "Barcelona, Valencia and the world's biggest tomato fight in Bu\xc3\xb1ol.",
            'content' => '<p>Spend 10 days in vibrant Spain — explore Barcelona\'s Sagrada Família, wander Valencia\'s City of Arts and Sciences, then hurl 150,000 kg of tomatoes at strangers in the world\'s messiest street festival. Flights, hotels, La Tomatina entry and city tours included.</p>',
            'acf' => array( 'destination_name' => 'Spain', 'trip_duration' => '9 Nights 10 Days', 'starting_price' => 149990, 'event_date' => '26 August 2026', 'best_time' => 'August', 'group_size' => '4–15 Pax', 'route_summary' => "Delhi \xe2\x86\x92 Barcelona \xe2\x86\x92 Valencia \xe2\x86\x92 Bu\xc3\xb1ol \xe2\x86\x92 Madrid \xe2\x86\x92 Delhi", 'book_url' => '' ),
        ),
        array(
            'title' => 'Germany Oktoberfest Experience', 'slug' => 'demo-event-oktoberfest', 'regions' => array( $germany ), 'thumb' => 'munich1',
            'excerpt' => 'Munich beer halls, Bavarian castles and the Oktoberfest grounds.',
            'content' => '<p>The world\'s most famous folk festival, paired with Bavaria\'s greatest hits. Giant beer tents in Munich\'s Theresienwiese, fairytale Neuschwanstein Castle, a day trip to Salzburg and a scenic alpine drive. Package includes Oktoberfest reserved-tent seats — no queuing.</p>',
            'acf' => array( 'destination_name' => 'Germany', 'trip_duration' => '6 Nights 7 Days', 'starting_price' => 179990, 'event_date' => '21 September 2026', 'best_time' => 'September to October', 'group_size' => '4–20 Pax', 'route_summary' => 'Delhi → Munich → Fussen → Neuschwanstein → Salzburg → Munich → Delhi', 'book_url' => '' ),
        ),
        array(
            'title' => 'Singapore F1 Grand Prix Tour', 'slug' => 'demo-event-f1', 'regions' => array( $singapore ), 'thumb' => 'sg1',
            'excerpt' => 'Marina Bay night race, Universal Studios and Gardens by the Bay.',
            'content' => '<p>Watch Formula 1 cars tear through the stunning Marina Bay Street Circuit under the Singapore skyline at night — the only F1 night race in the world. Combine with Universal Studios Sentosa, the Cloud Forest at Gardens by the Bay and a hawker-centre food trail. Paddock Club passes available.</p>',
            'acf' => array( 'destination_name' => 'Singapore', 'trip_duration' => '5 Nights 6 Days', 'starting_price' => 199990, 'event_date' => '4 October 2026', 'best_time' => 'October', 'group_size' => '2–10 Pax', 'route_summary' => 'Chennai → Singapore → Marina Bay → Sentosa → Gardens by the Bay → Chennai', 'book_url' => '' ),
        ),
    );
    foreach ( $events_data as $ev ) {
        $ev_meta = array(
            '_destination_name' => $ev['acf']['destination_name'],
            '_trip_duration'    => $ev['acf']['trip_duration'],
            '_starting_price'   => (string) $ev['acf']['starting_price'],
            '_event_date'       => $ev['acf']['event_date'],
            '_best_time'        => $ev['acf']['best_time'],
            '_group_size'       => $ev['acf']['group_size'],
            '_route_summary'    => $ev['acf']['route_summary'],
        );
        if ( ! empty( $ev['acf']['book_url'] ) ) { $ev_meta['_book_url'] = $ev['acf']['book_url']; }

        $eid = tw_demo_post( array(
            'post_type'    => 'tw_event',
            'post_title'   => $ev['title'],
            'post_name'    => $ev['slug'],
            'post_content' => $ev['content'],
            'post_excerpt' => $ev['excerpt'],
            'regions'      => array_filter( $ev['regions'] ),
            'meta'         => $ev_meta,
            'thumb_seed'   => $ev['thumb'],
        ), $created );

        // Also write via ACF so the "Event Details" admin panel shows all values
        if ( $eid && function_exists( 'update_field' ) ) {
            foreach ( $ev['acf'] as $field_name => $value ) {
                if ( $value !== '' && $value !== null ) {
                    update_field( $field_name, $value, $eid );
                }
            }
        }
    }

    /* ---- ITINERARIES ---- */
    $itineraries = array(
        array(
            'title' => 'Goa 4-Day Beach Hopping', 'slug' => 'demo-goa-itinerary', 'regions' => array( $goa ), 'thumb' => 'goa1',
            'excerpt' => 'North to South Goa: Baga, Anjuna, Palolem and a sunset cruise.',
            'content' => '<p>Start at the buzzing shacks of Baga, ride through the flea markets of Anjuna, catch a traditional Goan spice plantation tour and finish with the quiet white sands of Palolem beach. Day-by-day breakdown, hotel recommendations and restaurant picks included.</p>',
            'core' => array( 'location' => 'Goa, India', 'price' => '14999', 'nights' => '3', 'days' => '4', 'duration' => '3 Nights 4 Days', 'best_time' => 'November to February', 'group_size' => '2–10 Pax', 'trip_type' => 'Leisure', 'tag' => 'Budget Friendly', 'emi' => '₹2,499/mo', 'route' => 'Mumbai → Goa (Baga) → Anjuna → Colva → Palolem → Vasco → Mumbai', 'overview' => 'Sun, sand, spice markets and Goan seafood — a 4-day deep dive into India\'s beach capital.' ),
        ),
        array(
            'title' => 'Rajasthan Heritage Route', 'slug' => 'demo-rajasthan-itinerary', 'regions' => array( $rajasthan ), 'thumb' => 'raj1',
            'excerpt' => 'Jaipur forts, Udaipur lakes and the golden city of Jaisalmer.',
            'content' => '<p>Journey through the Pink City\'s Amber Fort, the lakeside palaces of Udaipur, the blue city of Jodhpur and end with a camel safari in the golden dunes of Jaisalmer. Covers heritage hotels, local cuisine trails, festival calendar and desert camp bookings.</p>',
            'core' => array( 'location' => 'Rajasthan, India', 'price' => '28999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'October to March', 'group_size' => '2–15 Pax', 'trip_type' => 'Heritage', 'tag' => 'Cultural', 'emi' => '₹4,833/mo', 'route' => 'Delhi → Jaipur → Pushkar → Jodhpur → Udaipur → Jaisalmer → Delhi', 'overview' => 'Forts, palaces, camels and desert sunsets — Rajasthan is India\'s most dramatic road trip.' ),
        ),
        array(
            'title' => 'Vietnam North to South', 'slug' => 'demo-vietnam-itinerary', 'regions' => array( $vietnam ), 'thumb' => 'viet1',
            'excerpt' => 'Hanoi, Ha Long Bay cruise, Hoi An lanterns and Ho Chi Minh City.',
            'content' => '<p>The classic Vietnam route reimagined — Hanoi street food walks, a 2-night cruise through Ha Long Bay\'s limestone pillars, the tailor shops and lantern festival of Hoi An, and HCMC\'s War Remnants Museum and rooftop bars. Full day-by-day guide, visa tips and local hacks included.</p>',
            'core' => array( 'location' => 'Vietnam', 'price' => '59999', 'nights' => '8', 'days' => '9', 'duration' => '8 Nights 9 Days', 'best_time' => 'November to April', 'group_size' => '2–10 Pax', 'trip_type' => 'International', 'tag' => 'Trending', 'emi' => '₹9,999/mo', 'route' => 'Delhi → Hanoi → Ha Long Bay → Da Nang → Hoi An → Ho Chi Minh City → Delhi', 'overview' => 'One country, a thousand flavours — Vietnam from north to south is the ultimate Southeast Asia journey.' ),
        ),
        array(
            'title' => 'Switzerland Alpine Trail', 'slug' => 'demo-switzerland-itinerary', 'regions' => array( $switz ), 'thumb' => 'swiss1',
            'excerpt' => 'Zurich, Interlaken, Jungfraujoch and the Glacier Express.',
            'content' => '<p>Tick off Switzerland\'s greatest hits: Bahnhofstrasse in Zurich, Chapel Bridge in Lucerne, bungee-jumping over Interlaken, the Jungfraujoch at 3,454 m and the legendary Glacier Express slow train through the Alps. Includes Swiss Travel Pass, hotel picks and mountain restaurant bookings.</p>',
            'core' => array( 'location' => 'Switzerland', 'price' => '189999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'June to September', 'group_size' => '2–8 Pax', 'trip_type' => 'Luxury', 'tag' => 'Premium', 'emi' => '₹31,666/mo', 'route' => 'Delhi → Zurich → Lucerne → Interlaken → Jungfraujoch → Zermatt → Bern → Delhi', 'overview' => 'Glaciers, alpine meadows and the world\'s most scenic train journeys — Switzerland done right.' ),
        ),
    );
    foreach ( $itineraries as $i ) {
        tw_demo_post( array(
            'post_type'    => 'itinerary',
            'post_title'   => $i['title'],
            'post_name'    => $i['slug'],
            'post_content' => $i['content'],
            'post_excerpt' => $i['excerpt'],
            'regions'      => array_filter( $i['regions'] ),
            'meta'         => tw_demo_full_meta( $i['core'] ),
            'thumb_seed'   => $i['thumb'],
        ), $created );
    }

    /* ---- GROUP TRIPS ---- */
    $groups = array(
        array(
            'title' => 'Manali Backpacking Group Trip', 'slug' => 'demo-manali-group', 'regions' => array( $himachal ), 'thumb' => 'manali1',
            'excerpt' => 'Fixed-departure Manali + Kasol backpacking with a fun crew.',
            'content' => '<p>Meet your tribe and hit the hills. This fixed-departure group trip covers the Rohtang Pass snow experience, bonfire nights in Kasol, a Kheerganga hot-spring trek and the old Manali village vibe. Hostel accommodation, all meals and a group trek leader included.</p>',
            'core' => array( 'location' => 'Himachal Pradesh, India', 'price' => '12999', 'nights' => '5', 'days' => '6', 'duration' => '5 Nights 6 Days', 'best_time' => 'June to September', 'group_size' => '8–15 Pax', 'trip_type' => 'Backpacking', 'tag' => 'Fixed Departure', 'emi' => '₹2,166/mo', 'route' => 'Delhi → Manali → Rohtang → Kasol → Kheerganga → Kasol → Delhi', 'overview' => 'Mountains, bonfires, waterfalls and new friends — this is what group travel is all about.' ),
        ),
        array(
            'title' => 'Spiti Valley Group Expedition', 'slug' => 'demo-spiti-group', 'regions' => array( $himachal ), 'thumb' => 'spiti1',
            'excerpt' => 'Cold-desert monasteries, Chandratal lake and Komic village.',
            'content' => '<p>One of India\'s most demanding (and rewarding) group journeys. Cross Kunzum La at 4,590 m, stay at Key Monastery, walk the world\'s highest post-office village Hikkim, camp at Chandratal Lake and drive the Spiti river valley to Manali on the Hampta Pass route.</p>',
            'core' => array( 'location' => 'Spiti Valley, Himachal Pradesh', 'price' => '21999', 'nights' => '7', 'days' => '8', 'duration' => '7 Nights 8 Days', 'best_time' => 'June to September', 'group_size' => '6–12 Pax', 'trip_type' => 'Adventure', 'tag' => 'High Altitude', 'emi' => '₹3,666/mo', 'route' => 'Delhi → Shimla → Narkanda → Kaza → Hikkim → Chandratal → Manali → Delhi', 'overview' => 'Cold-desert moonscapes, ancient gompas and Himalayan hospitality — Spiti is a world apart.' ),
        ),
        array(
            'title' => 'Bali Group Getaway', 'slug' => 'demo-bali-group', 'regions' => array( $bali ), 'thumb' => 'baligrp1',
            'excerpt' => 'Meet new people on a curated Bali group departure.',
            'content' => '<p>Travel solo, experience Bali with a crew. Includes Tegallalang rice terrace sunrise, a sunrise trek up Mount Batur, a half-day Ubud art & culture walk, a Seminyak beach club sunset and a day trip to Nusa Penida. All logistics handled — just show up and explore.</p>',
            'core' => array( 'location' => 'Bali, Indonesia', 'price' => '46999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'April to October', 'group_size' => '8–16 Pax', 'trip_type' => 'Group Trip', 'tag' => 'Solo Friendly', 'emi' => '₹7,833/mo', 'route' => 'Mumbai → Denpasar → Ubud → Mt Batur → Seminyak → Nusa Penida → Bali', 'overview' => 'The perfect solo trip — come alone, leave with a group of lifelong travel friends.' ),
        ),
        array(
            'title' => 'Meghalaya Living Roots Trek', 'slug' => 'demo-meghalaya-group', 'regions' => array( $megh ), 'thumb' => 'megh1',
            'excerpt' => 'Double-decker living root bridges and the cleanest village in Asia.',
            'content' => '<p>Trek to the legendary double-decker living root bridge of Nongriat, swim in natural pools, visit Mawlynnong (Asia\'s cleanest village), kayak through Dawki\'s crystal-clear river and explore the massive cave systems of Krem Mawmluh. Small group, experienced guide, full board camping included.</p>',
            'core' => array( 'location' => 'Meghalaya, India', 'price' => '18999', 'nights' => '5', 'days' => '6', 'duration' => '5 Nights 6 Days', 'best_time' => 'October to May', 'group_size' => '6–12 Pax', 'trip_type' => 'Trekking', 'tag' => 'Offbeat', 'emi' => '₹3,166/mo', 'route' => 'Guwahati → Shillong → Cherrapunji → Nongriat → Mawlynnong → Dawki → Guwahati', 'overview' => 'Northeast India\'s best-kept secret — cloud forests, living bridges and the world\'s wettest place.' ),
        ),
    );
    foreach ( $groups as $g ) {
        tw_demo_post( array(
            'post_type'    => 'group_trip',
            'post_title'   => $g['title'],
            'post_name'    => $g['slug'],
            'post_content' => $g['content'],
            'post_excerpt' => $g['excerpt'],
            'regions'      => array_filter( $g['regions'] ),
            'meta'         => tw_demo_full_meta( $g['core'] ),
            'thumb_seed'   => $g['thumb'],
        ), $created );
    }

    /* ---- WOMEN'S GROUP TRIPS ---- */
    $womens_trips = array(
        array(
            'title' => "Kerala Wellness Retreat — Women's Edition", 'slug' => 'demo-womens-kerala', 'regions' => array( $kerala ), 'thumb' => 'wkeral1',
            'excerpt' => "Ayurveda, backwaters and beach yoga — a restorative Kerala journey for women.",
            'content' => '<p>Reconnect with yourself on this curated Kerala wellness trip designed exclusively for women. Daily yoga at sunrise, traditional Ayurvedic massages, houseboat stay in Alleppey\'s backwaters, a cooking class with a local family and a private sunset beach session in Kovalam. Female trip leader throughout.</p>',
            'core' => array( 'location' => 'Kerala, India', 'price' => '22999', 'nights' => '5', 'days' => '6', 'duration' => '5 Nights 6 Days', 'best_time' => 'October to March', 'group_size' => '6–12 Women', 'trip_type' => 'Women Trip', 'tag' => 'Bestseller', 'emi' => '₹3,833/mo', 'route' => 'Cochin → Munnar → Alleppey → Kovalam → Trivandrum', 'overview' => 'A restorative journey through Kerala\'s backwaters and beaches — designed entirely for women.' ),
        ),
        array(
            'title' => "Kasol & Kheerganga — Women's Trek", 'slug' => 'demo-womens-kasol', 'regions' => array( $himachal ), 'thumb' => 'wkasol1',
            'excerpt' => "Bonfire nights, mountain treks and an all-women crew in the Parvati Valley.",
            'content' => '<p>Trek the legendary Kheerganga trail with a group of fearless women. Camp under stars in the Parvati Valley, take a dip in the natural hot springs at Kheerganga, explore Kasol\'s riverside cafes and share stories around the bonfire. A female trek leader ensures safety and fun the entire way.</p>',
            'core' => array( 'location' => 'Kasol, Himachal Pradesh', 'price' => '9999', 'nights' => '3', 'days' => '4', 'duration' => '3 Nights 4 Days', 'best_time' => 'May to October', 'group_size' => '8–15 Women', 'trip_type' => 'Women Trip', 'tag' => 'Trending', 'emi' => '₹1,666/mo', 'route' => 'Delhi → Kasol → Kheerganga → Kasol → Delhi', 'overview' => 'Mountains, hot springs and an all-women vibe — the perfect first trek for women travellers.' ),
        ),
        array(
            'title' => "Bali — Women's Solo Travellers Getaway", 'slug' => 'demo-womens-bali', 'regions' => array( $bali ), 'thumb' => 'wbali1',
            'excerpt' => "Temple walks, rice terraces and sunset cocktails with your new best friends.",
            'content' => '<p>Travel solo, arrive as a stranger, leave as family. This Bali women\'s group trip covers the spiritual temples of Ubud, a sunrise trek up Mount Batur, a traditional Balinese cooking class, a beach club evening in Seminyak and a day trip to the crystal waters of Nusa Penida. A female guide is with you throughout.</p>',
            'core' => array( 'location' => 'Bali, Indonesia', 'price' => '44999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'April to October', 'group_size' => '8–14 Women', 'trip_type' => 'Women Trip', 'tag' => 'Popular', 'emi' => '₹7,499/mo', 'route' => 'Mumbai → Bali → Ubud → Mt Batur → Seminyak → Nusa Penida → Bali', 'overview' => 'Come alone, leave with a tribe — the ultimate Bali experience for women solo travellers.' ),
        ),
        array(
            'title' => "Rajasthan — Women's Heritage Trail", 'slug' => 'demo-womens-rajasthan', 'regions' => array( $rajasthan ), 'thumb' => 'wraj1',
            'excerpt' => "Forts, palaces, desert sunsets and an all-women road trip across Rajasthan.",
            'content' => '<p>Explore the royal heritage of Rajasthan in the safest, most empowering way possible — with an all-women group and a female guide. From the Pink City\'s Amber Fort to the blue streets of Jodhpur, the lake city of Udaipur and a desert camp in Jaisalmer. Authentic experiences, local home-stay dinners and zero compromise on safety.</p>',
            'core' => array( 'location' => 'Rajasthan, India', 'price' => '26999', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'October to March', 'group_size' => '8–16 Women', 'trip_type' => 'Women Trip', 'tag' => 'New', 'emi' => '₹4,499/mo', 'route' => 'Delhi → Jaipur → Jodhpur → Udaipur → Jaisalmer → Delhi', 'overview' => 'A royal road trip across Rajasthan — safe, stunning and exclusively for women.' ),
        ),
    );
    foreach ( $womens_trips as $wt ) {
        tw_demo_post( array(
            'post_type'    => 'group_trip',
            'post_title'   => $wt['title'],
            'post_name'    => $wt['slug'],
            'post_content' => $wt['content'],
            'post_excerpt' => $wt['excerpt'],
            'regions'      => array_filter( $wt['regions'] ),
            'meta'         => tw_demo_full_meta( $wt['core'] ),
            'thumb_seed'   => $wt['thumb'],
        ), $created );
    }

    /* ---- LUXE TRIPS (luxury / private travel) ---- */
    $luxe_trips = array(
        array(
            'title' => 'Mediterranean Yacht Charter', 'slug' => 'demo-luxe-yacht', 'regions' => array( $spain ), 'thumb' => 'luxe-yacht-mediterranean',
            'excerpt' => 'Seven nights aboard a private 50m crewed yacht through Mallorca, Ibiza and the Côte d\'Azur.',
            'content' => '<p>Seven nights at sea on a privately chartered 50-metre motor yacht with a crew of eight — captain, chef, two stewards, two deckhands, an engineer and a tender driver. The yacht sails by night and anchors by day, never docking in busy marinas so guests step ashore on their own quiet beach each morning.</p>'
                . '<p>The week opens in Mallorca with a chef\'s tasting on the foredeck and a slow drift to the Formentera sandbars. From there, an evening crossing to Ibiza, where the yacht moors off Cala Llonga and an after-hours table is arranged at a hillside finca that doesn\'t take public bookings. The northern leg is for the French Riviera — Saint-Tropez, Antibes, Monaco — with a helicopter on call for inland day-trips to Eze or a Michelin-three-star lunch in Mougins.</p>'
                . '<p>Every detail is scripted in advance with a private travel director: dietary preferences are sent to the chef before embarkation, the wine list is built around guest provenance, and an NDA covers the crew throughout the engagement. Photography is at guests\' discretion only.</p>',
            'core' => array( 'location' => 'Western Mediterranean', 'price' => '4500000', 'nights' => '7', 'days' => '8', 'duration' => '7 Nights 8 Days', 'best_time' => 'June to September', 'group_size' => 'Up to 10 Guests', 'trip_type' => 'Private Yacht', 'tag' => 'signature', 'emi' => 'On request', 'route' => 'Mallorca → Formentera → Ibiza → Saint-Tropez → Antibes → Monaco', 'overview' => 'Seven nights of quiet Mediterranean summer aboard a privately chartered 50m yacht. The crew is briefed weeks in advance; nothing is rented twice, and nothing is shared.' ),
            'highlights' => "Chef\'s tasting on the foredeck at sunset\nAnchor swim at the Formentera sandbars before crowds arrive\nAfter-hours table at an Ibiza hillside finca with no public bookings\nHelicopter access from Saint-Tropez to a Mougins three-star lunch\nDawn paddle-board through Calanques de Marseille\nPrivate beach drop-off at Pampelonne with butler service\nSeabob, jet-ski and certified dive instructors aboard\nOptional photographer on the final evening only",
            'inclusions' => "Sole use of a 50-metre yacht with full crew of eight\nPrivate chef with bespoke menu planned to guest profiles\nAll premium dining, wines and a curated open bar\nButler service in every cabin\nHelicopter transfers (up to two day-trips)\nAll water sports — jet skis, seabob, paddleboards, kayaks, sailing dinghy\nNDA-bound crew, no third-party media on board\nDoor-to-door private transfers from Palma de Mallorca",
        ),
        array(
            'title' => 'Aman Bhutan — Five Lodges Private Tour', 'slug' => 'demo-luxe-bhutan', 'regions' => array( $bhutan ?? 0 ), 'thumb' => 'luxe-bhutan-mountains',
            'excerpt' => 'Eight nights across all five Amankora lodges with private guide and helicopter transfers.',
            'content' => '<p>Eight nights traversing all five Amankora lodges — Paro, Thimphu, Punakha, Gangtey, Bumthang — done at the slow pace the kingdom demands. A single licensed guide accompanies the party from arrival to departure, with the same driver throughout, so the relationship deepens as the valleys open out.</p>'
                . '<p>The route is reverse-engineered around the seasons. Punakha for the long-stem prayer-flag valley walks, Gangtey for the black-necked cranes if travelling in autumn, Bumthang for the cluster of monasteries that few foreign guests reach. A helicopter handles the Bumthang–Paro return so the final day is reserved for the pre-dawn climb to Taktsang, the Tiger\'s Nest, before any group arrives.</p>'
                . '<p>One evening is held for an audience with a senior monk at Punakha Dzong, arranged privately. Another for a hot-stone bath in a riverside farmhouse with the family who runs it. A private archery match with a national-team coach is offered at Thimphu — competitive, surprising, and the photograph nobody takes home.</p>',
            'core' => array( 'location' => 'Bhutan', 'price' => '3200000', 'nights' => '8', 'days' => '9', 'duration' => '8 Nights 9 Days', 'best_time' => 'March to May, September to November', 'group_size' => '2 Guests', 'trip_type' => 'Bespoke', 'tag' => 'limited', 'emi' => 'On request', 'route' => 'Paro → Thimphu → Punakha → Gangtey → Bumthang → Paro', 'overview' => 'The full Aman circuit, done privately and slowly. The most considered way to see the last Himalayan kingdom — five lodges, one guide, eight nights.' ),
            'highlights' => "Audience with a senior monk at Punakha Dzong\nHelicopter transfer between Bumthang and Paro\nPre-dawn private climb to Taktsang (the Tiger\'s Nest)\nArchery match with a member of the Bhutanese national team\nRiverside hot-stone bath at a private farmhouse\nLong-stem prayer-flag walk above Punakha\nBlessing ceremony at a 16th-century lhakhang in Bumthang\nWeaving and indigo-dye demonstration with a master textile family",
            'inclusions' => "All five Amankora lodges — Paro, Thimphu, Punakha, Gangtey, Bumthang\nPrivate licensed Bhutanese guide and dedicated driver throughout\nInternal helicopter transfer (Bumthang → Paro)\nAll meals, premium beverages, in-suite dining where preferred\nDaily wellness program designed around the day\'s walk\nGovernment visa and royalty contributions\nNight-by-night NDA, no images on social channels\nDoor-to-door transfers in armoured Land Cruiser",
        ),
        array(
            'title' => 'Maldives — Private Atoll Reserve', 'slug' => 'demo-luxe-maldives', 'regions' => array( $singapore ?? 0 ), 'thumb' => 'luxe-maldives-overwater-villa',
            'excerpt' => 'A six-villa private reserve in the Baa Atoll, taken in its entirety. One party at a time.',
            'content' => '<p>A six-villa private reserve in the Baa Atoll — a UNESCO Biosphere — taken in its entirety for the duration of stay. The island operates for one party at a time; the staff of forty resets between guests so no overlap is possible.</p>'
                . '<p>A resident marine biologist plans the week around manta-ray season, hosts a night-snorkel through the house reef, and arranges a dawn dolphin cruise on the reserve\'s 32-metre support yacht. The master chef arrives the day before the party and stays through; the kitchen is unmenued, working entirely to brief.</p>'
                . '<p>A typical evening: butler-drawn bath at sundown, candlelit dinner laid on a sandbank at low tide, then a fire-lit nightcap on the western beach with no other lights for kilometres. The week closes with a privately catered farewell aboard the support yacht as it carries the party back to the seaplane.</p>',
            'core' => array( 'location' => 'Baa Atoll, Maldives', 'price' => '5800000', 'nights' => '6', 'days' => '7', 'duration' => '6 Nights 7 Days', 'best_time' => 'November to April', 'group_size' => 'Up to 12 Guests', 'trip_type' => 'Private Villa', 'tag' => 'signature', 'emi' => 'On request', 'route' => 'Malé → Private Reserve, Baa Atoll → Malé', 'overview' => 'A UNESCO-biosphere island reserve, taken privately for one party. Six pavilions, a marine biologist on call, a master chef in residence. The most discreet week in the Indian Ocean.' ),
            'highlights' => "Sole-use of a six-villa private island in the Baa Atoll\nResident marine biologist and manta-ray season expedition\nCandlelit sandbank dinner with the chef cooking on the bar\nDawn dolphin cruise aboard a 32-metre support yacht\nHouse-reef night snorkel with bio-luminescence\nButler-drawn bath rituals from a Maldivian apothecary\nPrivate sunset cocktails on an uninhabited neighbouring island\nNDA-bound staff team of forty, fully reset between guests",
            'inclusions' => "Sole occupancy of six beach pavilions for six nights\nReturn seaplane and private launch transfers from Malé\nMaster chef in residence with unmenued kitchen\nAll meals, premium beverages, private cellar of curated wines\nResident marine biologist and dive instructor\n32m support yacht for two excursion days\nButler per pavilion, complimentary spa rituals\nAll motor-yachts and water sports — diving, manta excursions, jet-ski",
        ),
        array(
            'title' => 'Royal Rajasthan by Private Train', 'slug' => 'demo-luxe-rajasthan-train', 'regions' => array( $rajasthan ), 'thumb' => 'luxe-rajasthan-palace-jaipur',
            'excerpt' => 'Eight nights aboard a charter carriage of the Maharajas\' Express across Rajasthan.',
            'content' => '<p>Eight nights aboard a privately chartered carriage of the Maharajas\' Express — the carriage detached from public service for the duration, with its own butler, chef de partie and security team. The train moves at night, holding by day so the morning view from the dining car opens onto a different palace each sunrise.</p>'
                . '<p>The route runs Delhi to Delhi via Jaipur, Ranthambore, Jodhpur, Udaipur and Bikaner. At each city, a palace dinner is hosted by a descendant of the original ruling family — the Marwars at Umaid Bhawan, the Mewars beside Lake Pichola — with the food prepared in the household\'s own kitchens and served on the family\'s heirloom silver.</p>'
                . '<p>Beyond the train: private tiger drives at Ranthambore with a senior naturalist, an after-hours visit to the Mehrangarh armoury, a polo demonstration laid on at the Bikaner Cavalry, and an audience with a Rajput descendant in Udaipur whose private collection is normally closed to all visitors. A personal valet travels with the party from boarding to disembarkation.</p>',
            'core' => array( 'location' => 'Rajasthan, India', 'price' => '2800000', 'nights' => '8', 'days' => '9', 'duration' => '8 Nights 9 Days', 'best_time' => 'October to March', 'group_size' => '2–4 Guests', 'trip_type' => 'Private Rail', 'tag' => 'bespoke', 'emi' => 'On request', 'route' => 'Delhi → Jaipur → Ranthambore → Jodhpur → Udaipur → Bikaner → Delhi', 'overview' => 'India\'s royal trail, taken privately. A charter carriage of the Maharajas\' Express, palace dinners with descendants of the original ruling families, after-hours museum access and private tiger drives at Ranthambore.' ),
            'highlights' => "Palace dinner at Umaid Bhawan with the Marwar royal family\nPrivate tiger drive at Ranthambore with a senior naturalist\nAudience with a Rajput descendant in Udaipur — closed collection\nAfter-hours visit to the Mehrangarh armoury at Jodhpur\nPolo demonstration laid on at the Bikaner Cavalry\nChef\'s table on the dining car as the train rolls into Jaipur at dawn\nPrivate aarti at the City Palace ghats, Udaipur\nBlessing ceremony at Karni Mata, with a temple priest as guide",
            'inclusions' => "Private carriage of the Maharajas\' Express for eight nights\nDedicated butler, chef de partie and personal valet\nAll meals on board and at the palaces — banquet style on heirloom silver\nPalace stays where the route holds overnight (Udaipur)\nPrivate tiger jeeps and naturalist at Ranthambore\nAll ground transfers in chauffeured BMW 7 / Range Rover\nGuided after-hours museum access at four cities\nReturn private transfer Delhi airport ↔ rail",
        ),
    );
    foreach ( $luxe_trips as $lt ) {
        $pid = tw_demo_post( array(
            'post_type'    => 'tw_luxe',
            'post_title'   => $lt['title'],
            'post_name'    => $lt['slug'],
            'post_content' => $lt['content'],
            'post_excerpt' => $lt['excerpt'],
            'regions'      => array_filter( $lt['regions'] ),
            'meta'         => tw_demo_full_meta( $lt['core'] ),
            'thumb_seed'   => $lt['thumb'],
        ), $created );
        // tw_demo_post returns the existing ID if the slug exists. Refresh body + LUXE-only
        // meta on demo posts so re-running the seeder propagates richer content without
        // touching non-demo edits.
        $existing = get_page_by_path( $lt['slug'], OBJECT, 'tw_luxe' );
        if ( $existing && get_post_meta( $existing->ID, '_tw_demo', true ) ) {
            wp_update_post( array(
                'ID'           => $existing->ID,
                'post_content' => $lt['content'],
                'post_excerpt' => $lt['excerpt'],
            ) );
            // Re-apply the full meta set (also writes non-underscored keys the LUXE template reads).
            $full = tw_demo_full_meta( $lt['core'] );
            foreach ( $full as $k => $v ) { update_post_meta( $existing->ID, $k, $v ); }
            update_post_meta( $existing->ID, 'package_location',  $lt['core']['location'] );
            update_post_meta( $existing->ID, 'package_amount',    $lt['core']['price'] );
            update_post_meta( $existing->ID, 'package_overview',  $lt['core']['overview'] );
            update_post_meta( $existing->ID, 'package_trip_type', $lt['core']['trip_type'] );
            update_post_meta( $existing->ID, 'package_tag',       $lt['core']['tag'] );
            update_post_meta( $existing->ID, 'event_date',        'On request' );
            update_post_meta( $existing->ID, 'group_size',        $lt['core']['group_size'] );
            update_post_meta( $existing->ID, 'best_time',         $lt['core']['best_time'] );
            update_post_meta( $existing->ID, 'route_summary',     $lt['core']['route'] );
            update_post_meta( $existing->ID, 'total_nights',      $lt['core']['nights'] );
            update_post_meta( $existing->ID, 'total_days',        $lt['core']['days'] );
            update_post_meta( $existing->ID, 'luxe_highlights',   $lt['highlights'] );
            update_post_meta( $existing->ID, 'luxe_inclusions',   $lt['inclusions'] );
        }
    }

    /* ---- DESTINATIONS ---- */
    $dests = array(
        array( 'Bali, Indonesia', 'demo-dest-bali', 'Island of temples, surf and rice terraces.', array( $bali ), 'balidest' ),
        array( 'Kerala, India', 'demo-dest-kerala', 'God\'s own country — backwaters, hills and beaches.', array( $kerala ), 'keraladest' ),
        array( 'Ladakh, India', 'demo-dest-ladakh', 'High-altitude desert of lakes and monasteries.', array( $ladakh ), 'ladakhdest' ),
    );
    foreach ( $dests as $d ) {
        tw_demo_post( array(
            'post_type' => 'destination', 'post_title' => $d[0], 'post_name' => $d[1],
            'post_content' => $d[2], 'post_excerpt' => $d[2],
            'regions' => array_filter( $d[3] ), 'thumb_seed' => $d[4],
        ), $created );
    }

    /* ---- BLOG POSTS ---- */
    $posts = array(
        array( '10 Hidden Beaches in Bali You Must Visit', 'demo-blog-bali-beaches', 'From Nyang Nyang to Green Bowl — the quiet shores most tourists miss in Bali.', 'Destinations', 'blogbali' ),
        array( 'How to Plan a Budget Ladakh Trip', 'demo-blog-ladakh-budget', 'A practical breakdown of costs, permits and the best time to ride to Ladakh.', 'Trip Planning', 'blogladakh' ),
        array( 'Kerala in Monsoon: Is It Worth It?', 'demo-blog-kerala-monsoon', 'Why the rains might be the best — and cheapest — time to see Kerala.', 'Destinations', 'blogkerala' ),
        array( 'The Ultimate Packing Checklist for International Trips', 'demo-blog-packing', 'Everything from documents to chargers so you never overpack again.', 'Travel Tips', 'blogpacking' ),
        array( 'Top 5 Festivals Worth Travelling For in 2026', 'demo-blog-festivals', 'Oktoberfest, La Tomatina, Tomorrowland and more — plan your festival year.', 'Events', 'blogfest' ),
    );
    foreach ( $posts as $b ) {
        tw_demo_post( array(
            'post_type' => 'post', 'post_title' => $b[0], 'post_name' => $b[1],
            'post_content' => $b[2] . "\n\n" . 'This is sample demo content to showcase the blog layout.',
            'post_excerpt' => $b[2], 'category' => $b[3], 'thumb_seed' => $b[4],
        ), $created );
    }

    /* ---- FORUM TOPICS (+ a couple of approved replies & likes) ---- */
    $topics = array(
        array( 'Best time to visit Bali?', 'demo-topic-bali', 'Planning my first Bali trip — when\'s the ideal month to avoid crowds and rain?', 'destinations' ),
        array( 'Solo female travel in Rajasthan — tips?', 'demo-topic-rajasthan', 'Doing a solo Rajasthan trip next month. Any safety tips or must-dos?', 'planning' ),
        array( 'Realistic budget for 7 days in Thailand?', 'demo-topic-thailand', 'Trying to plan a week in Thailand. What did your trip actually cost?', 'planning' ),
        array( 'Share your best Ladakh photos!', 'demo-topic-ladakh', 'Drop your favourite Ladakh shots — building inspiration for my ride.', 'experiences' ),
        array( 'Looking for group-trip buddies for Spiti', 'demo-topic-spiti', 'Anyone joining a Spiti group departure in June? Let\'s connect.', 'group-trips' ),
    );
    foreach ( $topics as $t ) {
        $tid = tw_demo_post( array(
            'post_type' => 'forum_topic', 'post_title' => $t[0], 'post_name' => $t[1],
            'post_content' => $t[2], 'forum_cat' => $t[3],
        ), $created );
        if ( $tid && ! get_post_meta( $tid, '_tw_demo_replied', true ) ) {
            // a couple of approved demo replies
            $c1 = wp_insert_comment( array(
                'comment_post_ID' => $tid, 'comment_content' => 'Great question! I\'d go in the shoulder season — fewer crowds and better prices.',
                'comment_author' => 'TravelWithMaya', 'comment_approved' => 1, 'user_id' => 0,
            ) );
            $c2 = wp_insert_comment( array(
                'comment_post_ID' => $tid, 'comment_content' => 'Seconding this — and book stays early, they fill up fast.',
                'comment_author' => 'NomadArjun', 'comment_approved' => 1, 'user_id' => 0,
            ) );
            if ( $c1 ) { update_comment_meta( $c1, '_tw_reply_likes', 3 ); }
            update_post_meta( $tid, '_tw_topic_likes', wp_rand( 4, 24 ) );
            update_post_meta( $tid, '_tw_topic_views', wp_rand( 40, 320 ) );
            update_post_meta( $tid, '_tw_demo_replied', 1 );
        }
    }

    update_option( 'tw_demo_done', '1' );
    return $created;
}

/* =============================================================
 * REMOVE
 * ============================================================= */
function tw_demo_remove() {
    $removed = 0;
    $ids = get_posts( array(
        'post_type'      => array( 'travel_package', 'itinerary', 'group_trip', 'tw_event', 'tw_luxe', 'destination', 'post', 'forum_topic' ),
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_key'       => '_tw_demo',
        'meta_value'     => 1,
    ) );
    foreach ( $ids as $id ) {
        wp_delete_post( $id, true );
        $removed++;
    }
    delete_option( 'tw_demo_done' );
    return $removed;
}
