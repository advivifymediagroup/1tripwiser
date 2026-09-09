<?php
/**
 * Template Name: My Profile
 * Styles are in style.css
 */

// Must be logged in
if ( ! is_user_logged_in() ) {
    wp_safe_redirect( home_url('/login/?redirect_to=' . rawurlencode(get_permalink())) );
    exit;
}

get_header();

$user      = wp_get_current_user();
$uid       = $user->ID;
$fname     = get_user_meta($uid, 'first_name',   true);
$lname     = get_user_meta($uid, 'last_name',    true);
$bio       = get_user_meta($uid, 'description',  true);
$join_date = date('F Y', strtotime($user->user_registered));

// Post counts
$pub_count  = count_user_posts($uid, 'post', true);
$pend_count = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_author=%d AND post_type='post' AND post_status='pending'", $uid
));

// Recent posts
$my_posts = get_posts(array(
    'author'         => $uid,
    'post_status'    => array('publish','pending','draft'),
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
));

// Profile update message from URL
$notice = '';
if ( isset($_GET['updated']) && $_GET['updated'] === '1' ) {
    $notice = 'success';
} elseif ( isset($_GET['error']) ) {
    $notice = sanitize_text_field($_GET['error']);
}
?>

<!-- Hero -->
<section class="pf-hero"></section>

<div class="pf-page">

    <!-- Identity card -->
    <div class="pf-identity">
        <div class="pf-avatar-wrap">
            <?php echo get_avatar($uid, 88, '', '', array('class' => '')) ?: '<i class="fi-rr-user" aria-hidden="true"></i>'; ?>
        </div>
        <div class="pf-identity-info">
            <div class="pf-display-name"><?php echo esc_html($user->display_name ?: $user->user_login); ?></div>
            <div class="pf-username">@<span><?php echo esc_html($user->user_login); ?></span> &nbsp;·&nbsp; Member since <?php echo esc_html($join_date); ?></div>
            <div class="pf-badges">
                <?php foreach ($user->roles as $role) :
                    $labels = array('administrator'=>'Admin','editor'=>'Editor','author'=>'Author','contributor'=>'Contributor','subscriber'=>'Traveller');
                    $label  = isset($labels[$role]) ? $labels[$role] : ucfirst($role);
                    $cls    = in_array($role, array('administrator','editor')) ? 'gold' : '';
                ?>
                <span class="pf-badge <?php echo esc_attr($cls); ?>"><?php echo esc_html($label); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="pf-stats-row">
            <div class="pf-stat">
                <div class="pf-stat-num"><?php echo esc_html($pub_count); ?></div>
                <div class="pf-stat-label">Published</div>
            </div>
            <?php if ($pend_count) : ?>
            <div class="pf-stat">
                <div class="pf-stat-num"><?php echo esc_html($pend_count); ?></div>
                <div class="pf-stat-label">Pending</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="pf-grid">

        <!-- LEFT: Edit form -->
        <div>
            <!-- Notice from redirect -->
            <?php if ($notice === 'success') : ?>
            <div class="pf-msg success is-visible"><i class="fi-rr-check-circle" aria-hidden="true"></i> Profile updated successfully!</div>
            <?php elseif ($notice) : ?>
            <div class="pf-msg error is-visible"><i class="fi-rr-triangle-warning" aria-hidden="true"></i> <?php echo esc_html($notice); ?></div>
            <?php endif; ?>

            <!-- AJAX messages -->
            <div class="pf-msg" id="pf-msg"></div>

            <!-- Personal info -->
            <div class="pf-card mb-20">
                <div class="pf-card-head">
                    <span class="pf-card-title"><i class="fi-rr-pencil" aria-hidden="true"></i> Personal Information</span>
                </div>
                <div class="pf-card-body">
                    <form id="pf-info-form" novalidate>
                        <?php wp_nonce_field('tw_profile_update', 'tw_profile_nonce'); ?>

                        <div class="pf-field-row">
                            <div>
                                <label class="pf-label" for="pf_fname">First Name</label>
                                <input type="text" id="pf_fname" name="pf_fname" class="pf-input"
                                       value="<?php echo esc_attr($fname); ?>" placeholder="Jane">
                            </div>
                            <div>
                                <label class="pf-label" for="pf_lname">Last Name</label>
                                <input type="text" id="pf_lname" name="pf_lname" class="pf-input"
                                       value="<?php echo esc_attr($lname); ?>" placeholder="Doe">
                            </div>
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_display">Display Name</label>
                            <input type="text" id="pf_display" name="pf_display" class="pf-input"
                                   value="<?php echo esc_attr($user->display_name); ?>" placeholder="How your name appears publicly">
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_bio">About Me / Bio</label>
                            <textarea id="pf_bio" name="pf_bio" class="pf-textarea"
                                      placeholder="Tell travellers a bit about yourself…" rows="4"><?php echo esc_textarea($bio); ?></textarea>
                            <p class="pf-hint">This appears on your blog posts as the author bio.</p>
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_email">Email Address</label>
                            <input type="email" id="pf_email" name="pf_email" class="pf-input"
                                   value="<?php echo esc_attr($user->user_email); ?>">
                            <p class="pf-hint">Changing your email requires you to confirm the new address.</p>
                        </div>

                        <button type="submit" class="btn-primary btn-lg btn-block" id="pf-info-btn">
                            <span id="pf-info-btn-text">Save Changes</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change password -->
            <div class="pf-card">
                <div class="pf-card-head">
                    <span class="pf-card-title"><i class="fi-rr-lock" aria-hidden="true"></i> Change Password</span>
                </div>
                <div class="pf-card-body">
                    <div class="pf-msg" id="pf-pw-msg"></div>
                    <form id="pf-pw-form" novalidate>
                        <?php wp_nonce_field('tw_profile_update', 'tw_pw_nonce'); ?>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_cur_pw">Current Password</label>
                            <div class="pf-pw-group">
                                <input type="password" id="pf_cur_pw" name="pf_cur_pw" class="pf-input"
                                       placeholder="Enter your current password" autocomplete="current-password">
                                <button type="button" class="pf-pw-toggle" data-target="pf_cur_pw"><i class="fi-rr-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_new_pw">New Password</label>
                            <div class="pf-pw-group">
                                <input type="password" id="pf_new_pw" name="pf_new_pw" class="pf-input"
                                       placeholder="At least 8 characters" autocomplete="new-password">
                                <button type="button" class="pf-pw-toggle" data-target="pf_new_pw"><i class="fi-rr-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_conf_pw">Confirm New Password</label>
                            <div class="pf-pw-group">
                                <input type="password" id="pf_conf_pw" name="pf_conf_pw" class="pf-input"
                                       placeholder="Repeat new password" autocomplete="new-password">
                                <button type="button" class="pf-pw-toggle" data-target="pf_conf_pw"><i class="fi-rr-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary btn-lg btn-block" id="pf-pw-btn">
                            <span id="pf-pw-btn-text">Update Password</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Sidebar -->
        <div>
            <!-- Gravatar note -->
            <div class="pf-card mb-20">
                <div class="pf-card-head"><span class="pf-card-title"><i class="fi-rr-picture" aria-hidden="true"></i> Profile Photo</span></div>
                <div class="pf-card-body">
                    <div class="pf-avatar-wrap">
                        <div class="pf-avatar-circle">
                            <?php echo get_avatar($uid, 80, '', '', array('class'=>'')) ?: '<i class="fi-rr-user" aria-hidden="true"></i>'; ?>
                        </div>
                    </div>
                    <div class="pf-avatar-note">
                        Profile photos are powered by <a href="https://gravatar.com" target="_blank" rel="noopener">Gravatar</a>. To change your photo, create or update your account at <strong>gravatar.com</strong> using the same email address.
                    </div>
                </div>
            </div>

            <!-- Quick info -->
            <div class="pf-card mb-20">
                <div class="pf-card-head"><span class="pf-card-title">ℹ Account Details</span></div>
                <div class="pf-card-body tight">
                    <div class="pf-info-row">
                        <div class="pf-info-icon"><i class="fi-rr-user" aria-hidden="true"></i></div>
                        <div>
                            <div class="pf-info-label">Username</div>
                            <div class="pf-info-val"><?php echo esc_html($user->user_login); ?></div>
                        </div>
                    </div>
                    <div class="pf-info-row">
                        <div class="pf-info-icon"><i class="fi-rr-calendar" aria-hidden="true"></i></div>
                        <div>
                            <div class="pf-info-label">Member Since</div>
                            <div class="pf-info-val"><?php echo esc_html($join_date); ?></div>
                        </div>
                    </div>
                    <div class="pf-info-row">
                        <div class="pf-info-icon"><i class="fi-rr-edit-alt" aria-hidden="true"></i></div>
                        <div>
                            <div class="pf-info-label">Published Posts</div>
                            <div class="pf-info-val"><?php echo esc_html($pub_count); ?></div>
                        </div>
                    </div>
                    <div class="pf-info-row">
                        <div class="pf-info-icon"><i class="fi-rr-envelope" aria-hidden="true"></i></div>
                        <div>
                            <div class="pf-info-label">Email</div>
                            <div class="pf-info-val wrap-all"><?php echo esc_html($user->user_email); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My posts -->
            <div class="pf-card">
                <div class="pf-card-head">
                    <span class="pf-card-title"><i class="fi-rr-memo" aria-hidden="true"></i> My Posts</span>
                    <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>"
                       class="pf-new-post-link">+ New Post</a>
                </div>
                <div class="pf-card-body tight">
                    <?php if ($my_posts) : ?>
                        <?php foreach ($my_posts as $p) :
                            $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail');
                            $status = $p->post_status;
                        ?>
                        <div class="pf-post-item">
                            <div class="pf-post-thumb">
                                <?php if ($thumb) : ?>
                                <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy">
                                <?php else : ?>
                                <i class="fi-rr-plane" aria-hidden="true"></i>
                                <?php endif; ?>
                            </div>
                            <div class="pf-inline-flex-1">
                                <div class="pf-post-title">
                                    <?php if ($status === 'publish') : ?>
                                    <a href="<?php echo esc_url(get_permalink($p->ID)); ?>"><?php echo esc_html($p->post_title); ?></a>
                                    <?php else : ?>
                                    <?php echo esc_html($p->post_title); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="pf-post-meta">
                                    <span><?php echo esc_html(get_the_date('M j, Y', $p)); ?></span>
                                    <span class="pf-post-status <?php echo esc_attr($status); ?>"><?php echo esc_html(ucfirst($status)); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                    <div class="pf-no-posts">
                        <p>No posts yet.<br><a href="<?php echo esc_url(home_url('/submit-blog/')); ?>">Write your first travel guide →</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /.pf-grid -->
</div><!-- /.pf-page -->

<script>
(function () {
    /* ── Password visibility toggle ── */
    document.querySelectorAll('.pf-pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp  = document.getElementById(btn.dataset.target);
            var show = inp.type === 'password';
            inp.type      = show ? 'text' : 'password';
            btn.innerHTML = show ? '<i class="fi-rr-eye-crossed" aria-hidden="true"></i>' : '<i class="fi-rr-eye" aria-hidden="true"></i>';
        });
    });

    var ajaxUrl = <?php echo json_encode(admin_url('admin-ajax.php')); ?>;

    /* ── Profile info form ── */
    var infoForm = document.getElementById('pf-info-form');
    var infoBtn  = document.getElementById('pf-info-btn');
    var infoBtnT = document.getElementById('pf-info-btn-text');
    var infoMsg  = document.getElementById('pf-msg');

    infoForm.addEventListener('submit', function (e) {
        e.preventDefault();
        infoMsg.style.display = 'none';

        var email = document.getElementById('pf_email').value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            show(infoMsg, 'Please enter a valid email address.', 'error');
            return;
        }

        infoBtnT.innerHTML = '<i class="fi-rr-hourglass" aria-hidden="true"></i> Saving…';
        infoBtn.disabled     = true;

        var fd = new FormData(infoForm);
        fd.append('action', 'tw_update_profile_info');

        fetch(ajaxUrl, { method:'POST', body:fd, credentials:'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    show(infoMsg, data.data.message, 'success');
                    // Update displayed name in header if changed
                    var dn = document.querySelector('.tw-user-display');
                    if (dn && data.data.display_name) dn.textContent = data.data.display_name;
                } else {
                    show(infoMsg, (data.data || 'Update failed.'), 'error');
                }
                infoBtnT.textContent = 'Save Changes';
                infoBtn.disabled     = false;
            })
            .catch(function () {
                show(infoMsg, 'Network error. Please try again.', 'error');
                infoBtnT.textContent = 'Save Changes';
                infoBtn.disabled     = false;
            });
    });

    /* ── Password form ── */
    var pwForm = document.getElementById('pf-pw-form');
    var pwBtn  = document.getElementById('pf-pw-btn');
    var pwBtnT = document.getElementById('pf-pw-btn-text');
    var pwMsg  = document.getElementById('pf-pw-msg');

    pwForm.addEventListener('submit', function (e) {
        e.preventDefault();
        pwMsg.style.display = 'none';

        var cur  = document.getElementById('pf_cur_pw').value;
        var nw   = document.getElementById('pf_new_pw').value;
        var conf = document.getElementById('pf_conf_pw').value;

        if (!cur)            { show(pwMsg, 'Please enter your current password.', 'error'); return; }
        if (nw.length < 8)   { show(pwMsg, 'New password must be at least 8 characters.', 'error'); return; }
        if (nw !== conf)     { show(pwMsg, 'New passwords do not match.', 'error'); return; }

        pwBtnT.innerHTML = '<i class="fi-rr-hourglass" aria-hidden="true"></i> Updating…';
        pwBtn.disabled     = true;

        var fd = new FormData(pwForm);
        fd.append('action', 'tw_update_profile_password');

        fetch(ajaxUrl, { method:'POST', body:fd, credentials:'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    show(pwMsg, 'Password updated! You may need to log in again.', 'success');
                    pwForm.reset();
                    // Re-auth cookie is refreshed server-side; just reload after short delay
                    setTimeout(function () { window.location.reload(); }, 2200);
                } else {
                    show(pwMsg, (data.data || 'Update failed.'), 'error');
                    pwBtnT.textContent = 'Update Password';
                    pwBtn.disabled     = false;
                }
            })
            .catch(function () {
                show(pwMsg, 'Network error. Please try again.', 'error');
                pwBtnT.textContent = 'Update Password';
                pwBtn.disabled     = false;
            });
    });

    function show(el, text, type) {
        el.textContent   = text;
        el.className     = 'pf-msg ' + type;
        el.style.display = 'block';
        el.scrollIntoView({ behavior:'smooth', block:'nearest' });
    }
})();
</script>

<?php get_footer(); ?>
