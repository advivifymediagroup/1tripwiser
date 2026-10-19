<?php
/**
 * Customer-facing emails: welcome (newsletter / popup / signup) and query
 * confirmation (package, itinerary, destination, plan-a-trip enquiries).
 * HTML lives in /emails/*.html with {{placeholders}}; admin notifications
 * stay plain-text where they were.
 */

const TW_MAIL_SUPPORT = '1tripwiser@gmail.com';

function tw_mail_render($template, array $vars) {
    $html = file_get_contents(get_template_directory() . '/emails/' . $template . '.html');
    if ($html === false) {
        return '';
    }
    $map = array();
    foreach ($vars as $key => $value) {
        $map['{{' . $key . '}}'] = (string) $value;
    }
    // The templates ship with a "[Company postal address]" placeholder line.
    $address = trim((string) get_option('tw_mail_postal_address', ''));
    $map['[Company postal address]<br>'] = $address !== '' ? esc_html($address) . '<br>' : '';
    return strtr($html, $map);
}

function tw_mail_send_customer($to, $subject, $html) {
    if (!is_email($to) || $html === '') {
        return false;
    }
    $from_name = static function () { return '1TripWiser'; };
    add_filter('wp_mail_from_name', $from_name, 99);
    $sent = wp_mail($to, $subject, $html, array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: 1TripWiser <' . TW_MAIL_SUPPORT . '>',
    ));
    remove_filter('wp_mail_from_name', $from_name, 99);
    return $sent;
}

function tw_unsubscribe_sig($email) {
    return hash_hmac('sha256', strtolower($email), wp_salt('auth'));
}

function tw_unsubscribe_url($email) {
    return add_query_arg(array(
        'tw_unsub' => rawurlencode($email),
        'tw_sig'   => tw_unsubscribe_sig($email),
    ), home_url('/'));
}

add_action('init', function () {
    if (!isset($_GET['tw_unsub'], $_GET['tw_sig'])) {
        return;
    }
    $email = sanitize_email(wp_unslash($_GET['tw_unsub']));
    $sig   = sanitize_text_field(wp_unslash($_GET['tw_sig']));
    nocache_headers();
    if (!is_email($email) || !hash_equals(tw_unsubscribe_sig($email), $sig)) {
        wp_die(esc_html__('This unsubscribe link is invalid.', 'mytheme'), '', array('response' => 400));
    }
    global $wpdb;
    $wpdb->delete(mytheme_newsletter_table_name(), array('email' => $email), array('%s'));
    wp_die(
        esc_html__('You have been unsubscribed. You will no longer receive emails from 1TripWiser.', 'mytheme'),
        esc_html__('Unsubscribed', 'mytheme'),
        array('response' => 200, 'link_url' => home_url('/'), 'link_text' => '1tripwiser.com')
    );
});

/**
 * $once = true skips addresses already welcomed in the last 90 days, so the
 * popup -> newsletter two-step doesn't send two welcome emails.
 */
function tw_send_welcome_email($email, $name = '', $once = true) {
    if (!is_email($email)) {
        return false;
    }
    $marker = 'tw_welcomed_' . md5(strtolower($email));
    if ($once && get_transient($marker)) {
        return false;
    }
    $first = strtok(trim($name), ' ');
    $html  = tw_mail_render('welcome', array(
        'first_name'      => esc_html($first ?: 'there'),
        'unsubscribe_url' => esc_url(tw_unsubscribe_url($email)),
    ));
    $sent = tw_mail_send_customer($email, 'Welcome to 1TripWiser', $html);
    if ($sent) {
        set_transient($marker, 1, 90 * DAY_IN_SECONDS);
    }
    return $sent;
}

/**
 * $d keys (all optional): destination, travel_dates, travellers, budget, phone.
 */
function tw_send_query_confirmation($email, $name, $query_id, array $d = array()) {
    if (!is_email($email)) {
        return false;
    }
    $d = wp_parse_args($d, array(
        'destination'  => '',
        'travel_dates' => '',
        'travellers'   => '',
        'budget'       => '',
        'phone'        => '',
    ));
    $first = strtok(trim($name), ' ');
    $html  = tw_mail_render('query-confirmation', array(
        'first_name'   => esc_html($first ?: 'there'),
        'query_id'     => absint($query_id),
        'destination'  => esc_html($d['destination'] ?: 'your dream trip'),
        'travel_dates' => esc_html($d['travel_dates'] ?: 'Flexible'),
        'travellers'   => esc_html($d['travellers'] ?: 'Not specified'),
        'budget'       => esc_html($d['budget'] ?: 'Not specified'),
        'phone'        => esc_html($d['phone'] ?: 'Not provided'),
    ));
    return tw_mail_send_customer($email, sprintf("We've received your trip query (#%d)", $query_id), $html);
}

function tw_format_travellers($adults, $children = 0) {
    $adults   = max(1, (int) $adults);
    $children = max(0, (int) $children);
    $out = sprintf(_n('%d adult', '%d adults', $adults, 'mytheme'), $adults);
    if ($children) {
        $out .= ', ' . sprintf(_n('%d child', '%d children', $children, 'mytheme'), $children);
    }
    return $out;
}
