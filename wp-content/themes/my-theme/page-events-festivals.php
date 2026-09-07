<?php
/**
 * Template Name: Events & Festivals
 *
 * Landing page for the Events & Festivals content type (tw_event) — each event
 * is a special, bookable package. Sections: hero, all event packages grid, and
 * an "upcoming by month" listing grouped by each event's Event Date.
 *
 * @package my-theme
 */
get_header();

$tw_events = new WP_Query( array(
    'post_type'      => 'tw_event',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'no_found_rows'  => true,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$tw_rows    = array();
$tw_buckets = array();
if ( $tw_events->have_posts() ) {
    foreach ( $tw_events->posts as $p ) {
        $date   = mytheme_get_travel_field( 'event_date', $p->ID );
        $ts     = $date ? strtotime( $date ) : 0;
        // Price: only accept numeric values — never display raw ACF field-key strings
        $price = '';
        foreach ( array(
            get_post_meta( $p->ID, 'package_amount',  true ),
            get_post_meta( $p->ID, 'starting_price',  true ),
            get_post_meta( $p->ID, '_starting_price', true ),
            get_post_meta( $p->ID, '_package_amount', true ),
        ) as $_v ) {
            if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
        }
        // Duration: ACF group stores total_nights + total_days; old meta fallback = trip_duration
        $nights = (int) mytheme_get_travel_field( 'total_nights', $p->ID );
        $days   = (int) mytheme_get_travel_field( 'total_days', $p->ID );
        $dur    = mytheme_get_travel_field( 'trip_duration', $p->ID );
        if ( ! $dur && $nights ) {
            $dur = $nights . ' Nights ' . ( $days ? $days : $nights + 1 ) . ' Days';
        }
        $row = array(
            'id'      => $p->ID,
            'title'   => get_the_title( $p->ID ),
            'url'     => get_permalink( $p->ID ),
            'book'    => get_permalink( $p->ID ) . '#event-enquiry',
            'thumb'   => get_the_post_thumbnail_url( $p->ID, 'large' ),
            'date'    => $date,
            'ts'      => $ts,
            'price'   => $price,
            'dur'     => $dur,
            'excerpt' => wp_trim_words( get_the_excerpt( $p->ID ), 18, '…' ),
        );
        $tw_rows[] = $row;
        if ( $ts ) {
            $key = date( 'Ym', $ts );
            if ( ! isset( $tw_buckets[ $key ] ) ) {
                $tw_buckets[ $key ] = array( 'label' => date_i18n( 'F Y', $ts ), 'items' => array() );
            }
            $tw_buckets[ $key ]['items'][] = $row;
        }
    }
    wp_reset_postdata();
}
ksort( $tw_buckets );

if ( ! function_exists( 'tw_ev_price_html' ) ) :
function tw_ev_price_html( $price ) {
    if ( ! $price ) { return ''; }
    $amt = function_exists( 'mytheme_format_rupee_amount' ) ? mytheme_format_rupee_amount( $price ) : $price;
    return '<div class="ev-card-price"><span>Starts from</span><strong>' . esc_html( $amt ) . '</strong></div>';
}
endif;

if ( ! function_exists( 'tw_ev_card' ) ) :
function tw_ev_card( $row ) {
    ?>
    <article class="ev-card">
        <a class="ev-card-img" href="<?php echo esc_url( $row['url'] ); ?>">
            <?php if ( $row['thumb'] ) : ?><img src="<?php echo esc_url( $row['thumb'] ); ?>" alt="" loading="lazy"><?php else : ?><span class="ev-card-ph"><i class="fi-rr-ticket-alt" aria-hidden="true"></i></span><?php endif; ?>
            <span class="ev-card-tag">Event</span>
        </a>
        <div class="ev-card-body">
            <div class="ev-card-meta">
                <?php if ( $row['dur'] ) : ?><span><?php echo esc_html( $row['dur'] ); ?></span><?php endif; ?>
                <?php if ( $row['date'] ) : ?><span class="ev-card-date"><?php echo esc_html( $row['date'] ); ?></span><?php endif; ?>
            </div>
            <h3 class="ev-card-title"><a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['title'] ); ?></a></h3>
            <p class="ev-card-excerpt"><?php echo esc_html( $row['excerpt'] ); ?></p>
            <div class="ev-card-foot">
                <?php echo tw_ev_price_html( $row['price'] ); // phpcs:ignore ?>
                <a class="btn-primary btn-sm" href="<?php echo esc_url( $row['book'] ); ?>">Book Now</a>
            </div>
        </div>
    </article>
    <?php
}
endif;
?>

<main class="main-content explore-page ev-page">

    <!-- HERO -->
    <section class="explore-hero">
        <div class="explore-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <span class="explore-hero-kicker">Plan around the moment</span>
            <h1 class="tw-h1">Events &amp; <span class="accent-red">Festivals</span></h1>
            <p class="explore-hero-sub">Beyond ordinary — live the story worth telling. Bookable trips built around the world's best events &amp; festivals.</p>
        </div>
    </section>

    <div class="container explore-wrap">

        <!-- ALL EVENT PACKAGES -->
        <section class="ev-section">
            <div class="ev-section-head">
                <h2 class="tw-h2">Explore All Event Packages</h2>
                <p>Hand-crafted, ready-to-book trips timed perfectly around each celebration.</p>
            </div>
            <?php if ( $tw_rows ) : ?>
                <div class="ev-grid">
                    <?php foreach ( $tw_rows as $row ) { tw_ev_card( $row ); } ?>
                </div>
            <?php else : ?>
                <div class="tribe-empty">
                    <div class="tribe-empty-icon"><i class="fi-rr-ticket-alt" aria-hidden="true"></i></div>
                    <h3>No event packages yet</h3>
                    <p>Add one under <strong>Events &amp; Festivals → Add Event</strong> in the dashboard — it works just like a Travel Package.</p>
                    <a class="btn-primary" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><i class="fi-rr-plane" aria-hidden="true"></i> Plan a Trip — Free</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- BY MONTH -->
        <?php if ( $tw_buckets ) : ?>
        <section class="ev-section">
            <div class="ev-section-head">
                <h2 class="tw-h2">Upcoming by Month</h2>
                <p>Dated departures, easy to plan around.</p>
            </div>
            <?php foreach ( $tw_buckets as $bucket ) : ?>
                <div class="ev-month">
                    <h3 class="ev-month-label"><?php echo esc_html( strtoupper( $bucket['label'] ) ); ?></h3>
                    <div class="ev-month-rows">
                        <?php foreach ( $bucket['items'] as $row ) :
                            $d = $row['ts'] ? date_i18n( 'j', $row['ts'] ) : '';
                            $m = $row['ts'] ? date_i18n( 'M', $row['ts'] ) : ''; ?>
                            <div class="ev-row">
                                <div class="ev-row-date"><strong><?php echo esc_html( $d ); ?></strong><span><?php echo esc_html( $m ); ?></span></div>
                                <a class="ev-row-img" href="<?php echo esc_url( $row['url'] ); ?>">
                                    <?php if ( $row['thumb'] ) : ?><img src="<?php echo esc_url( $row['thumb'] ); ?>" alt="" loading="lazy"><?php else : ?><span class="ev-card-ph"><i class="fi-rr-ticket-alt" aria-hidden="true"></i></span><?php endif; ?>
                                </a>
                                <div class="ev-row-body">
                                    <h4 class="ev-row-title"><a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['title'] ); ?></a></h4>
                                    <?php if ( $row['dur'] ) : ?><div class="ev-row-dur"><?php echo esc_html( $row['dur'] ); ?></div><?php endif; ?>
                                    <p class="ev-row-excerpt"><?php echo esc_html( $row['excerpt'] ); ?></p>
                                </div>
                                <div class="ev-row-foot">
                                    <?php echo tw_ev_price_html( $row['price'] ); // phpcs:ignore ?>
                                    <a class="btn-primary btn-sm" href="<?php echo esc_url( $row['book'] ); ?>">Book Now</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
