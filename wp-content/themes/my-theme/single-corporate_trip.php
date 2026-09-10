<?php
/**
 * Single Corporate Trip.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id = get_the_ID();

    $price = '';
    foreach ( array( get_post_meta( $id, 'package_amount', true ), get_post_meta( $id, '_package_amount', true ), get_post_meta( $id, '_starting_price', true ) ) as $_v ) {
        if ( is_numeric( $_v ) && $_v > 0 ) { $price = $_v; break; }
    }

    $nights    = (int) ( get_post_meta( $id, 'total_nights', true ) ?: mytheme_get_travel_field( 'total_nights', $id ) );
    $days      = (int) ( get_post_meta( $id, 'total_days', true ) ?: mytheme_get_travel_field( 'total_days', $id ) );
    $duration  = $nights ? $nights . ' Nights ' . ( $days ?: $nights + 1 ) . ' Days' : mytheme_get_travel_field( 'trip_duration', $id );
    $location  = get_post_meta( $id, 'package_location', true ) ?: mytheme_get_travel_field( 'destination_name', $id );
    $season    = get_post_meta( $id, 'event_date', true ) ?: mytheme_get_travel_field( 'event_date', $id );
    $team_size = get_post_meta( $id, 'group_size', true ) ?: mytheme_get_travel_field( 'group_size', $id );
    $trip_type = get_post_meta( $id, 'package_trip_type', true ) ?: 'Corporate Trip';
    $tag       = get_post_meta( $id, 'package_tag', true );
    $overview  = get_post_meta( $id, 'package_overview', true );
    $route     = get_post_meta( $id, 'route_summary', true ) ?: mytheme_get_travel_field( 'route_summary', $id );
    $best_time = get_post_meta( $id, 'best_time', true ) ?: mytheme_get_travel_field( 'best_time', $id );
    $objectives = get_post_meta( $id, 'corporate_objectives', true );
    $inclusions = get_post_meta( $id, 'corporate_inclusions', true );
    $thumb     = get_the_post_thumbnail_url( $id, 'full' );
    $regions   = get_the_terms( $id, 'destination_region' );
    $region    = ( $regions && ! is_wp_error( $regions ) ) ? $regions[0] : null;

    $day_rows = array();
    if ( function_exists( 'get_field' ) && have_rows( 'itinerary_days', $id ) ) {
        while ( have_rows( 'itinerary_days', $id ) ) {
            the_row();
            $day_rows[] = array(
                'title'      => get_sub_field( 'day_title' ),
                'details'    => get_sub_field( 'day_details' ),
                'route'      => get_sub_field( 'day_route' ),
                'stay'       => get_sub_field( 'day_stay' ),
                'meals'      => get_sub_field( 'day_meals' ),
                'transfer'   => get_sub_field( 'day_transfer' ),
                'highlights' => get_sub_field( 'day_highlights' ),
            );
        }
    }

    $body_content = trim( get_the_content() );
    $has_faqs     = function_exists( 'have_rows' ) && have_rows( 'faqs', $id );
    $archive_url  = get_post_type_archive_link( 'corporate_trip' ) ?: home_url( '/corporate-trips/' );
    ?>

<main class="main-content explore-page dest-single">

    <section class="explore-hero ev-hero-cinematic<?php echo $thumb ? ' has-hero-img' : ''; ?>"
             <?php if ( $thumb ) : ?>style="background-image:url('<?php echo esc_url( $thumb ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>&rsaquo;</span>
                <a href="<?php echo esc_url( $archive_url ); ?>">Corporate Trips</a><span>&rsaquo;</span>
                <span><?php the_title(); ?></span>
            </nav>

            <h1 class="tw-h1"><?php the_title(); ?></h1>

            <?php if ( $location || $region ) : ?>
            <p class="explore-hero-sub">
                <?php echo esc_html( $location ?: $region->name ); ?>
                <?php if ( $region && $location ) : ?> &middot; <?php echo esc_html( $region->name ); ?><?php endif; ?>
            </p>
            <?php endif; ?>

            <div class="ev-hero-facts">
                <span class="ev-hero-fact ev-hero-fact--kicker">
                    <?php echo esc_html( $trip_type ); ?>
                    <?php if ( $tag ) : ?>&nbsp;&middot;&nbsp;<span class="ev-hero-fact-tag"><?php echo esc_html( ucwords( str_replace( '-', ' ', $tag ) ) ); ?></span><?php endif; ?>
                </span>
                <?php if ( $duration ) : ?><span class="ev-hero-fact"><i class="fa-regular fa-clock ev-fact-icon"></i><?php echo esc_html( $duration ); ?></span><?php endif; ?>
                <?php if ( $season ) : ?><span class="ev-hero-fact"><i class="fa-regular fa-calendar ev-fact-icon"></i><?php echo esc_html( $season ); ?></span><?php endif; ?>
                <?php if ( $team_size ) : ?><span class="ev-hero-fact"><i class="fa-solid fa-people-group ev-fact-icon"></i><?php echo esc_html( $team_size ); ?></span><?php endif; ?>
                <?php if ( $price ) : ?>
                    <span class="ev-hero-fact ev-hero-fact--price"><i class="fa-solid fa-tag ev-fact-icon"></i><?php echo esc_html( mytheme_format_rupee_amount( $price ) ); ?> <small>/ person</small></span>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container dest-single-wrap">
        <div class="dest-single-layout">

            <aside class="dest-rail">
                <nav class="dest-toc" aria-label="On this page">
                    <div class="dest-toc-title">Table of Contents</div>
                    <ol class="dest-toc-list">
                        <?php if ( $overview ) : ?><li><a href="#corp-overview"><span class="dest-toc-num">1</span><span class="dest-toc-label">Overview</span></a></li><?php endif; ?>
                        <?php if ( $route ) : ?><li><a href="#corp-route"><span class="dest-toc-num">2</span><span class="dest-toc-label">Route</span></a></li><?php endif; ?>
                        <?php if ( $objectives ) : ?><li><a href="#corp-objectives"><span class="dest-toc-num">3</span><span class="dest-toc-label">Objectives</span></a></li><?php endif; ?>
                        <?php if ( $inclusions ) : ?><li><a href="#corp-inclusions"><span class="dest-toc-num">4</span><span class="dest-toc-label">Inclusions</span></a></li><?php endif; ?>
                        <?php if ( $day_rows ) : ?><li><a href="#corp-days"><span class="dest-toc-num">5</span><span class="dest-toc-label">Itinerary</span></a></li><?php endif; ?>
                        <?php if ( $has_faqs ) : ?><li><a href="#corp-faqs"><span class="dest-toc-num">6</span><span class="dest-toc-label">FAQs</span></a></li><?php endif; ?>
                    </ol>
                </nav>
            </aside>

            <article class="dest-article ev-content-styled">
                <?php if ( $overview ) : ?>
                <section id="corp-overview" class="dest-article-section">
                    <h2>Overview</h2>
                    <?php echo wp_kses_post( wpautop( $overview ) ); ?>
                </section>
                <?php endif; ?>

                <?php if ( $route ) : ?>
                <section id="corp-route" class="dest-article-section">
                    <h2>Route / Venue Plan</h2>
                    <?php echo wp_kses_post( wpautop( $route ) ); ?>
                </section>
                <?php endif; ?>

                <?php if ( $objectives ) : ?>
                <section id="corp-objectives" class="dest-article-section">
                    <h2>Corporate Objectives</h2>
                    <?php echo wp_kses_post( wpautop( $objectives ) ); ?>
                </section>
                <?php endif; ?>

                <?php if ( $inclusions ) : ?>
                <section id="corp-inclusions" class="dest-article-section">
                    <h2>Corporate Inclusions</h2>
                    <?php echo wp_kses_post( wpautop( $inclusions ) ); ?>
                </section>
                <?php endif; ?>

                <?php if ( $day_rows ) : ?>
                <section id="corp-days" class="dest-article-section">
                    <h2>Itinerary</h2>
                    <div class="itinerary-days itinerary-timeline dest-itin-days">
                        <?php foreach ( $day_rows as $d_i => $d ) : $day_number = $d_i + 1; ?>
                        <details class="itinerary-day" <?php echo $day_number === 1 ? 'open' : ''; ?>>
                            <summary>
                                <span class="itinerary-day-marker"><?php echo esc_html( $day_number ); ?></span>
                                <span class="itinerary-day-summary">
                                    <strong><?php echo esc_html( $d['title'] ?: sprintf( 'Day %d', $day_number ) ); ?></strong>
                                    <?php if ( $d['route'] ) : ?><em><?php echo esc_html( $d['route'] ); ?></em><?php endif; ?>
                                </span>
                                <span class="itinerary-day-toggle" aria-hidden="true"></span>
                            </summary>
                            <div class="itinerary-day-panel">
                                <?php if ( $d['stay'] || $d['meals'] || $d['transfer'] || $d['highlights'] ) : ?>
                                <div class="itinerary-day-chips">
                                    <?php if ( $d['stay'] ) : ?><span><i class="fa-solid fa-bed"></i><?php echo esc_html( $d['stay'] ); ?></span><?php endif; ?>
                                    <?php if ( $d['meals'] ) : ?><span><i class="fa-solid fa-utensils"></i><?php echo esc_html( $d['meals'] ); ?></span><?php endif; ?>
                                    <?php if ( $d['transfer'] ) : ?><span><i class="fa-solid fa-route"></i><?php echo esc_html( $d['transfer'] ); ?></span><?php endif; ?>
                                    <?php if ( $d['highlights'] ) : ?><span><i class="fa-solid fa-star"></i><?php echo esc_html( $d['highlights'] ); ?></span><?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="itinerary-day-copy"><?php echo wp_kses_post( wpautop( $d['details'] ) ); ?></div>
                            </div>
                        </details>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ( $body_content ) : ?>
                <section id="corp-more" class="dest-article-section">
                    <h2>Good to Know</h2>
                    <?php the_content(); ?>
                </section>
                <?php endif; ?>

                <?php
                get_template_part( 'template-parts/enquiry-form', null, array(
                    'type'    => 'corporate_trip',
                    'ref_id'  => $id,
                    'anchor'  => 'corporate-enquiry',
                    'kicker'  => __( 'Planning for a team?', 'mytheme' ),
                    'heading' => sprintf( __( 'Get a corporate trip plan for %s', 'mytheme' ), get_the_title() ),
                    'intro'   => __( 'Share your team size, dates and goals. Our corporate travel expert will help shape the right plan.', 'mytheme' ),
                ) );
                ?>

                <?php if ( $has_faqs ) : ?>
                <section id="corp-faqs" class="dest-article-section dest-article-faqs">
                    <?php mytheme_render_faq_section( $id, __( 'Corporate Trip FAQs', 'mytheme' ) ); ?>
                </section>
                <?php endif; ?>

                <footer class="dest-article-cta">
                    <a href="#corporate-enquiry" class="btn-primary">Plan This Offsite</a>
                    <a href="<?php echo esc_url( $archive_url ); ?>" class="btn-secondary">All Corporate Trips</a>
                </footer>
            </article>

            <aside class="dest-booking">
                <div class="dest-rail-card">
                    <?php if ( $price ) : ?>
                    <div class="dest-rail-price">
                        <span>Starting from</span>
                        <strong><?php echo esc_html( mytheme_format_rupee_amount( $price ) ); ?></strong>
                    </div>
                    <?php endif; ?>
                    <?php
                    $rail_rows = array_filter( array(
                        'Duration'    => $duration,
                        'Availability'=> $season,
                        'Team Size'   => $team_size,
                        'Destination' => $location,
                        'Trip Type'   => $trip_type,
                        'Best Time'   => $best_time,
                    ) );
                    if ( $rail_rows ) : ?>
                    <dl class="dest-rail-facts">
                        <?php foreach ( $rail_rows as $label => $val ) : ?>
                        <div class="dest-rail-fact">
                            <dt><?php echo esc_html( $label ); ?></dt>
                            <dd><?php echo esc_html( $val ); ?></dd>
                        </div>
                        <?php endforeach; ?>
                    </dl>
                    <?php endif; ?>
                    <a class="btn-primary btn-block" href="#corporate-enquiry">Request Proposal</a>
                    <a class="btn-secondary btn-block" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>">Plan a Trip</a>
                </div>
                <?php mytheme_render_enquiry_popup_card(); ?>
            </aside>

        </div>
    </div>
</main>

<?php
endwhile;
get_footer();
