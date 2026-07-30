<?php
/**
 * Single Destination — wide editorial layout: one continuous article column
 * with a sticky table of contents alongside it, rather than a stack of
 * separate boxed sections.
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $id          = get_the_ID();
    $destination = mytheme_get_destination_data( $id );

    /* Hero image — try ACF, then WP featured thumbnail, always at FULL size */
    $hero_img = '';
    if ( ! empty( $destination['image'] ) ) {
        $hero_img = mytheme_get_image_url( $destination['image'], 'full' );
    }
    if ( ! $hero_img && has_post_thumbnail( $id ) ) {
        $hero_img = get_the_post_thumbnail_url( $id, 'full' );
    }

    $archive = get_post_type_archive_link( 'destination' );

    /* Guide sections that actually have content — these become both the
       in-page <h2> blocks and the numbered contents list. */
    $guide_sections = array_values( array_filter(
        mytheme_get_destination_guide_sections( $id ),
        function ( $section ) { return ! empty( $section['content'] ); }
    ) );

    $has_faqs = function_exists( 'have_rows' ) && have_rows( 'faqs', $id );

    /* Single contents list covering the whole page, in render order. */
    $toc = array();
    if ( $destination['short_intro'] || $destination['overview'] ) {
        $toc[] = array( 'id' => 'dest-overview', 'label' => __( 'Overview', 'mytheme' ) );
    }
    foreach ( $guide_sections as $i => $section ) {
        $toc[] = array( 'id' => 'dest-guide-' . ( $i + 1 ), 'label' => $section['label'] );
    }
    if ( $has_faqs ) {
        $toc[] = array( 'id' => 'dest-faqs', 'label' => __( 'FAQs', 'mytheme' ) );
    }
    ?>

<main class="main-content explore-page dest-single">

    <!-- CINEMATIC HERO -->
    <section class="explore-hero ev-hero-cinematic<?php echo $hero_img ? ' has-hero-img' : ''; ?>"
             <?php if ( $hero_img ) : ?>style="background-image:url('<?php echo esc_url( $hero_img ); ?>')"<?php endif; ?>>
        <div class="explore-hero-overlay ev-hero-overlay" aria-hidden="true"></div>
        <div class="container explore-hero-inner">
            <nav class="explore-crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a><span>›</span>
                <a href="<?php echo esc_url( $archive ); ?>">Destinations</a><span>›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <span class="explore-hero-kicker">Destination</span>
            <h1 class="explore-hero-title"><?php the_title(); ?></h1>
            <?php if ( $destination['country'] ) : ?>
            <p class="explore-hero-sub"><?php echo esc_html( $destination['country'] ); ?></p>
            <?php endif; ?>
            <div class="ev-hero-facts">
                <?php if ( $destination['country'] )        : ?><span class="ev-hero-fact"><i class="fa-solid fa-location-dot ev-fact-icon"></i><?php echo esc_html( $destination['country'] ); ?></span><?php endif; ?>
                <?php if ( $destination['best_time'] )      : ?><span class="ev-hero-fact"><i class="fa-solid fa-sun ev-fact-icon"></i><?php echo esc_html( $destination['best_time'] ); ?></span><?php endif; ?>
                <?php if ( $destination['ideal_duration'] ) : ?><span class="ev-hero-fact"><i class="fa-regular fa-clock ev-fact-icon"></i><?php echo esc_html( $destination['ideal_duration'] ); ?></span><?php endif; ?>
                <?php if ( $destination['starting_price'] ) : ?><span class="ev-hero-fact ev-hero-fact--price"><i class="fa-solid fa-tag ev-fact-icon"></i><?php echo esc_html( $destination['starting_price'] ); ?> <small>onwards</small></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="container dest-single-wrap">
        <div class="dest-single-layout">

            <!-- Sticky rail: contents + at-a-glance facts + CTAs -->
            <aside class="dest-rail">
                <?php if ( $toc ) : ?>
                <nav class="dest-toc" aria-label="<?php esc_attr_e( 'On this page', 'mytheme' ); ?>">
                    <div class="dest-toc-title"><?php esc_html_e( 'Table of Contents', 'mytheme' ); ?></div>
                    <ol class="dest-toc-list">
                        <?php foreach ( $toc as $t_i => $t ) : ?>
                        <li>
                            <a href="#<?php echo esc_attr( $t['id'] ); ?>" data-dest-toc="<?php echo esc_attr( $t['id'] ); ?>">
                                <span class="dest-toc-num"><?php echo esc_html( $t_i + 1 ); ?></span>
                                <span class="dest-toc-label"><?php echo esc_html( $t['label'] ); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>

                <div class="dest-rail-card">
                    <?php if ( $destination['starting_price'] ) : ?>
                    <div class="dest-rail-price">
                        <span><?php esc_html_e( 'Packages from', 'mytheme' ); ?></span>
                        <strong><?php echo esc_html( $destination['starting_price'] ); ?></strong>
                        <small><?php esc_html_e( 'per person', 'mytheme' ); ?></small>
                    </div>
                    <?php endif; ?>
                    <?php
                    $rail_rows = array_filter( array(
                        __( 'Country', 'mytheme' )        => $destination['country'],
                        __( 'Best Time', 'mytheme' )      => $destination['best_time'],
                        __( 'Ideal Duration', 'mytheme' ) => $destination['ideal_duration'],
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
                    <a class="dest-rail-btn" href="<?php echo esc_url( get_post_type_archive_link('travel_package') ); ?>"><?php esc_html_e( 'View Packages', 'mytheme' ); ?></a>
                    <a class="dest-rail-btn dest-rail-btn--ghost" href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>"><?php esc_html_e( 'Plan a Trip', 'mytheme' ); ?></a>
                </div>
            </aside>

            <!-- One continuous article — no per-section boxes -->
            <article class="dest-article ev-content-styled">

                <?php if ( $destination['short_intro'] || $destination['overview'] ) : ?>
                <section id="dest-overview" class="dest-article-section">
                    <h2><?php esc_html_e( 'Overview', 'mytheme' ); ?></h2>
                    <?php if ( $destination['short_intro'] ) : ?>
                        <div class="dest-lede"><?php echo wp_kses_post( wpautop( $destination['short_intro'] ) ); ?></div>
                    <?php endif; ?>
                    <?php if ( $destination['overview'] ) : ?>
                        <?php echo wp_kses_post( $destination['overview'] ); ?>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <?php foreach ( $guide_sections as $g_i => $section ) : ?>
                <section id="dest-guide-<?php echo esc_attr( $g_i + 1 ); ?>" class="dest-article-section">
                    <h2><?php echo esc_html( $section['label'] ); ?></h2>
                    <?php echo wp_kses_post( wpautop( $section['content'] ) ); ?>
                </section>
                <?php endforeach; ?>

                <?php if ( $has_faqs ) : ?>
                <section id="dest-faqs" class="dest-article-section dest-article-faqs">
                    <?php mytheme_render_faq_section( $id, __( 'Destination FAQs', 'mytheme' ) ); ?>
                </section>
                <?php endif; ?>

                <footer class="dest-article-cta">
                    <a href="<?php echo esc_url( get_post_type_archive_link('travel_package') ); ?>" class="btn-primary"><?php esc_html_e( 'View Packages', 'mytheme' ); ?></a>
                    <a href="<?php echo esc_url( mytheme_get_plan_trip_url() ); ?>" class="btn-secondary"><?php esc_html_e( 'Plan a Trip', 'mytheme' ); ?></a>
                </footer>

                <a class="tribe-back-link" href="<?php echo esc_url( $archive ); ?>">← <?php esc_html_e( 'All Destinations', 'mytheme' ); ?></a>
            </article>

        </div>
    </div>

</main>

<?php if ( $toc ) : ?>
<script>
/* Highlight the contents entry for whichever section is currently in view. */
(function () {
    var links = document.querySelectorAll('[data-dest-toc]');
    if (!links.length) { return; }

    var targets = [];
    links.forEach(function (link) {
        var el = document.getElementById(link.getAttribute('data-dest-toc'));
        if (el) { targets.push({ link: link, el: el }); }
    });
    if (!targets.length) { return; }

    function sync() {
        /* Measure against the viewport — offsetTop is relative to the nearest
           positioned ancestor, which is wrong inside this grid layout. */
        var current = targets[0];
        targets.forEach(function (t) {
            if (t.el.getBoundingClientRect().top <= 140) { current = t; }
        });
        targets.forEach(function (t) {
            t.link.classList.toggle('is-current', t === current);
        });
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
        if (ticking) { return; }
        ticking = true;
        window.requestAnimationFrame(function () { sync(); ticking = false; });
    }, { passive: true });
    sync();
}());
</script>
<?php endif; ?>

<?php
endwhile;
get_footer();
