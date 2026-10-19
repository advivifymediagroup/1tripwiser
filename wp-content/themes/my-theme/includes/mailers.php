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

function tw_mail_send_customer($to, $subject, $html, array $extra_headers = array()) {
    if (!is_email($to) || $html === '') {
        return false;
    }
    $from_name = static function () { return '1TripWiser'; };
    add_filter('wp_mail_from_name', $from_name, 99);
    $sent = wp_mail($to, $subject, $html, array_merge(array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: 1TripWiser <' . TW_MAIL_SUPPORT . '>',
    ), $extra_headers));
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
    $unsub = tw_unsubscribe_url($email);
    $sent  = tw_mail_send_customer($email, 'Welcome to 1TripWiser', $html, array(
        'List-Unsubscribe: <' . $unsub . '>',
        'List-Unsubscribe-Post: List-Unsubscribe=One-Click',
    ));
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

/* ── Sending: Gmail SMTP + plain-text part ──────────────────────────────
 * Without SMTP the host's PHP mail() sends as wordpress@<domain>, which is
 * unauthenticated and lands in spam/Promotions. When an app password is saved
 * under 1TripWiser → Email Sender, all site mail goes out through Gmail.
 * ponytail: Gmail-only (500 sends/day); swap host/port here for another SMTP.
 */
function tw_smtp_config() {
    $user = get_option('tw_smtp_user', '') ?: TW_MAIL_SUPPORT;
    $pass = get_option('tw_smtp_pass', '');
    return ($user && $pass) ? array($user, $pass) : null;
}

function tw_html_to_text($html) {
    $html = preg_replace('#<(style|head)[^>]*>.*?</\1>#is', '', $html);
    $html = preg_replace('#<a [^>]*href="([^"]+)"[^>]*>(.*?)</a>#is', '$2 ($1)', $html);
    $html = preg_replace('#</(p|tr|h1|h2|div)>|<br\s*/?>#i', "\n", $html);
    $text = html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/[\x{2007}\x{034F}\x{00A0}]/u', ' ', $text);
    return trim(preg_replace("/[ \t]+\n|\n{3,}/", "\n\n", $text));
}

add_action('phpmailer_init', function ($mailer) {
    if ($cfg = tw_smtp_config()) {
        $mailer->isSMTP();
        $mailer->Host       = 'smtp.gmail.com';
        $mailer->Port       = 587;
        $mailer->SMTPAuth   = true;
        $mailer->SMTPSecure = 'tls';
        $mailer->Username   = $cfg[0];
        $mailer->Password   = $cfg[1];
    }
    if ($mailer->ContentType === 'text/html' && $mailer->AltBody === '') {
        $mailer->AltBody = tw_html_to_text($mailer->Body);
    }
});

add_filter('wp_mail_from', function ($from) {
    $cfg = tw_smtp_config();
    return $cfg ? $cfg[0] : $from;
});

/* ── Admin: 1TripWiser → Email Sender ─────────────────────────────────── */
add_action('admin_init', function () {
    register_setting('tw_smtp_group', 'tw_smtp_user', array('sanitize_callback' => 'sanitize_email'));
    register_setting('tw_smtp_group', 'tw_smtp_pass', array('sanitize_callback' => function ($v) {
        $v = preg_replace('/\s+/', '', (string) $v); // Google shows app passwords in 4-letter groups
        return $v !== '' ? $v : get_option('tw_smtp_pass', '');
    }));
});

add_action('admin_menu', function () {
    add_submenu_page(
        'tw-settings',
        __('Email Sender', 'mytheme'),
        __('<i class="fi-rr-envelope" aria-hidden="true"></i> Email Sender', 'mytheme'),
        'manage_options',
        'tw-email-settings',
        'tw_render_email_settings_page'
    );
}, 20);

add_action('admin_post_tw_smtp_test', function () {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Forbidden', 'mytheme'));
    }
    check_admin_referer('tw_smtp_test');
    $err = '';
    $capture = function ($e) use (&$err) { $err = $e->get_error_message(); };
    add_action('wp_mail_failed', $capture);
    $me   = wp_get_current_user();
    $sent = tw_send_welcome_email($me->user_email, $me->display_name, false);
    remove_action('wp_mail_failed', $capture);
    wp_safe_redirect(add_query_arg(array(
        'page'    => 'tw-email-settings',
        'tw_test' => $sent ? 'ok' : 'fail',
        'tw_err'  => rawurlencode($err),
    ), admin_url('admin.php')));
    exit;
});

function tw_render_email_settings_page() {
    tw_settings_page_header('Email Sender', '<i class="fi-rr-envelope" aria-hidden="true"></i>', 'Send welcome and query-confirmation emails from your Gmail so they authenticate and reach the inbox.');
    $cfg  = tw_smtp_config();
    $test = isset($_GET['tw_test']) ? sanitize_key($_GET['tw_test']) : '';
    ?>
    <?php if ($test === 'ok') : ?>
        <div class="tw-admin-note success">Test email sent to <?php echo esc_html(wp_get_current_user()->user_email); ?>. Check its inbox (and spam).</div>
    <?php elseif ($test === 'fail') : ?>
        <div class="tw-admin-note warn">Test email failed.<?php if (!empty($_GET['tw_err'])) { echo ' ' . esc_html(rawurldecode(wp_unslash($_GET['tw_err']))); } ?></div>
    <?php endif; ?>

    <div class="tw-admin-note <?php echo $cfg ? 'success' : 'warn'; ?>">
        <?php if ($cfg) : ?>
            Sending through Gmail as <strong><?php echo esc_html($cfg[0]); ?></strong>.
        <?php else : ?>
            <strong>Not configured.</strong> Emails currently go out as wordpress@<?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?> via the web host, which usually lands in spam or Promotions.
        <?php endif; ?>
    </div>

    <div class="tw-admin-card">
        <div class="tw-admin-card-head">
            <div><h2>Gmail SMTP</h2><p>Turn on 2-Step Verification for the Gmail account, then create an <strong>App password</strong> at <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">myaccount.google.com/apppasswords</a>. Use that 16-letter password here, not the normal Gmail password.</p></div>
        </div>
        <div class="tw-admin-card-body">
            <form method="post" action="options.php">
                <?php settings_fields('tw_smtp_group'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="tw_smtp_user">Gmail address</label></th>
                        <td><input type="email" id="tw_smtp_user" name="tw_smtp_user" value="<?php echo esc_attr(get_option('tw_smtp_user', '') ?: TW_MAIL_SUPPORT); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="tw_smtp_pass">App password</label></th>
                        <td>
                            <input type="password" id="tw_smtp_pass" name="tw_smtp_pass" value="" class="regular-text" autocomplete="new-password" placeholder="<?php echo get_option('tw_smtp_pass', '') ? esc_attr__('Saved — leave blank to keep', 'mytheme') : 'xxxx xxxx xxxx xxxx'; ?>">
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save'); ?>
            </form>
        </div>
    </div>

    <?php if ($cfg) : ?>
    <div class="tw-admin-card">
        <div class="tw-admin-card-head"><div><h2>Send a test</h2><p>Sends the welcome email to your own account address.</p></div></div>
        <div class="tw-admin-card-body">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="tw_smtp_test">
                <?php wp_nonce_field('tw_smtp_test'); submit_button('Send test email', 'secondary', 'submit', false); ?>
            </form>
        </div>
    </div>
    <?php endif;
    tw_settings_page_footer();
}
