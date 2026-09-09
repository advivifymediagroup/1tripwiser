<?php
/*
Template Name: Package Search
*/

get_header();

$selected_region = isset($_GET['region']) ? sanitize_key(wp_unslash($_GET['region'])) : '';
$selected_budget = isset($_GET['budget']) ? sanitize_key(wp_unslash($_GET['budget'])) : '';
$selected_duration = isset($_GET['duration']) ? sanitize_key(wp_unslash($_GET['duration'])) : '';
$selected_trip_type = isset($_GET['trip_type']) ? sanitize_text_field(wp_unslash($_GET['trip_type'])) : '';
$selected_month = isset($_GET['month']) ? sanitize_key(wp_unslash($_GET['month'])) : '';
$selected_tag = isset($_GET['tag']) ? sanitize_key(wp_unslash($_GET['tag'])) : '';
$selected_destination = isset($_GET['destination']) ? absint($_GET['destination']) : 0;
$search_keyword = isset($_GET['package_keyword']) ? sanitize_text_field(wp_unslash($_GET['package_keyword'])) : '';

$meta_query = array('relation' => 'AND');

if ($selected_region && array_key_exists($selected_region, mytheme_package_region_options())) {
    $meta_query[] = array(
        'key' => 'package_region',
        'value' => '"' . $selected_region . '"',
        'compare' => 'LIKE',
    );
}

if ($selected_budget) {
    $budget_ranges = array(
        'under-30000' => array('compare' => '<=', 'value' => 30000),
        '30000-60000' => array('compare' => 'BETWEEN', 'value' => array(30000, 60000)),
        '60000-100000' => array('compare' => 'BETWEEN', 'value' => array(60000, 100000)),
        'above-100000' => array('compare' => '>=', 'value' => 100000),
    );

    if (isset($budget_ranges[$selected_budget])) {
        $meta_query[] = array(
            'key' => 'package_amount',
            'value' => $budget_ranges[$selected_budget]['value'],
            'type' => 'NUMERIC',
            'compare' => $budget_ranges[$selected_budget]['compare'],
        );
    }
}

if ($selected_duration) {
    $duration_ranges = array(
        '1-3' => array(1, 3),
        '4-7' => array(4, 7),
        '8-14' => array(8, 14),
        '15-plus' => array(15, 365),
    );

    if (isset($duration_ranges[$selected_duration])) {
        $meta_query[] = array(
            'key' => 'total_days',
            'value' => $duration_ranges[$selected_duration],
            'type' => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
    }
}

if ($selected_trip_type && array_key_exists($selected_trip_type, mytheme_package_trip_type_options())) {
    $meta_query[] = array(
        'key' => 'package_trip_type',
        'value' => $selected_trip_type,
        'compare' => '=',
    );
}

if ($selected_month && array_key_exists($selected_month, mytheme_package_month_options())) {
    $meta_query[] = array(
        'key' => 'package_months',
        'value' => '"' . $selected_month . '"',
        'compare' => 'LIKE',
    );
}

if ($selected_tag && array_key_exists($selected_tag, mytheme_package_tag_options())) {
    $meta_query[] = array(
        'key' => 'package_tag',
        'value' => $selected_tag,
        'compare' => '=',
    );
}

if ($selected_destination) {
    $meta_query[] = array(
        'key' => 'linked_destination',
        'value' => $selected_destination,
        'compare' => '=',
    );
}

$paged = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
$query_args = array(
    'post_type' => 'travel_package',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => $paged,
);

if ($search_keyword) {
    $query_args['s'] = $search_keyword;
}

if (count($meta_query) > 1) {
    $query_args['meta_query'] = $meta_query;
}

$packages = new WP_Query($query_args);
$destinations = get_posts(array(
    'post_type' => 'destination',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
));
?>

<main class="main-content package-search-page">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>

        <header class="archive-header travel-archive-header">
            <span>Find your next trip</span>
            <h1 class="tw-h1"><?php the_title(); ?></h1>
            <p class="archive-description">Search packages by region, budget, duration, trip type, month, tag, or destination.</p>
        </header>

        <form class="package-search-form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
            <div class="package-search-grid">
                <label>
                    <span>Keyword</span>
                    <input type="search" name="package_keyword" value="<?php echo esc_attr($search_keyword); ?>" placeholder="Search packages">
                </label>

                <label>
                    <span>Region</span>
                    <select name="region">
                        <option value="">Any region</option>
                        <?php foreach (mytheme_package_region_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_region, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Budget</span>
                    <select name="budget">
                        <option value="">Any budget</option>
                        <option value="under-30000" <?php selected($selected_budget, 'under-30000'); ?>>Under 30K</option>
                        <option value="30000-60000" <?php selected($selected_budget, '30000-60000'); ?>>30K - 60K</option>
                        <option value="60000-100000" <?php selected($selected_budget, '60000-100000'); ?>>60K - 1L</option>
                        <option value="above-100000" <?php selected($selected_budget, 'above-100000'); ?>>Above 1L</option>
                    </select>
                </label>

                <label>
                    <span>Duration</span>
                    <select name="duration">
                        <option value="">Any duration</option>
                        <option value="1-3" <?php selected($selected_duration, '1-3'); ?>>1 - 3 days</option>
                        <option value="4-7" <?php selected($selected_duration, '4-7'); ?>>4 - 7 days</option>
                        <option value="8-14" <?php selected($selected_duration, '8-14'); ?>>8 - 14 days</option>
                        <option value="15-plus" <?php selected($selected_duration, '15-plus'); ?>>15+ days</option>
                    </select>
                </label>

                <label>
                    <span>Trip Type</span>
                    <select name="trip_type">
                        <option value="">Any trip type</option>
                        <?php foreach (mytheme_package_trip_type_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_trip_type, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Month</span>
                    <select name="month">
                        <option value="">Any month</option>
                        <?php foreach (mytheme_package_month_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_month, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Tag</span>
                    <select name="tag">
                        <option value="">Any tag</option>
                        <?php foreach (mytheme_package_tag_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_tag, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Destination</span>
                    <select name="destination">
                        <option value="">Any destination</option>
                        <?php foreach ($destinations as $destination) : ?>
                            <option value="<?php echo esc_attr($destination->ID); ?>" <?php selected($selected_destination, $destination->ID); ?>>
                                <?php echo esc_html(get_the_title($destination)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="package-search-actions">
                <button type="submit">Search Packages</button>
                <a href="<?php echo esc_url(get_permalink()); ?>">Reset</a>
            </div>
        </form>

        <div class="package-search-results-heading">
            <h2><?php echo esc_html($packages->found_posts); ?> packages found</h2>
        </div>

        <?php if ($packages->have_posts()) : ?>
            <div class="posts-grid">
                <?php while ($packages->have_posts()) : $packages->the_post(); ?>
                    <?php mytheme_package_card(); ?>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'total' => $packages->max_num_pages,
                    'current' => $paged,
                    'add_args' => array_filter(array(
                        'package_keyword' => $search_keyword,
                        'region' => $selected_region,
                        'budget' => $selected_budget,
                        'duration' => $selected_duration,
                        'trip_type' => $selected_trip_type,
                        'month' => $selected_month,
                        'tag' => $selected_tag,
                        'destination' => $selected_destination,
                    )),
                    'prev_text' => __('Previous', 'mytheme'),
                    'next_text' => __('Next', 'mytheme'),
                ));
                ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <?php mytheme_render_package_empty_state(
                get_permalink(),
                __('Try changing your filters, reset the search, or ask us to build a custom itinerary around your budget.', 'mytheme')
            ); ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
