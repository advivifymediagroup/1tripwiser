<?php
/**
 * Template Name: My Profile
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

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
/* ================================================================
   PROFILE PAGE
   ================================================================ */
:root {
    --pf-bg:     #f5f8fa;
    --pf-card:   #ffffff;
    --pf-raised: #eef2f7;
    --pf-text:   #1a2535;
    --pf-muted:  #6b7a8f;
    --pf-border: #dde5ef;
    --pf-head:   #0D1526;
    --pf-shadow: 0 8px 32px rgba(0,0,0,0.08);
    --gold:  #FCB415;
    --blue:  #0692AF;
    --green: #306C35;
    --red:   #D5374F;
}

body { background: var(--pf-bg) !important; font-family: 'Nunito', sans-serif; color: var(--pf-text); }

/* ── Hero banner ── */
.pf-hero {
    background: linear-gradient(135deg, #0d1526 0%, #0a1e30 55%, #071a2a 100%);
    padding: 52px 24px 80px;
    position: relative;
    overflow: hidden;
}
.pf-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 30% 50%, rgba(6,146,175,0.18) 0%, transparent 60%);
}

/* ── Avatar card overlapping hero ── */
.pf-page {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 24px 88px;
}

.pf-identity {
    position: relative;
    margin-top: -56px;
    background: var(--pf-card);
    border: 1px solid var(--pf-border);
    border-radius: 20px;
    padding: 28px 32px 24px;
    box-shadow: var(--pf-shadow);
    display: flex;
    align-items: flex-end;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}

.pf-avatar-wrap {
    width: 88px; height: 88px;
    border-radius: 50%;
    border: 4px solid var(--gold);
    overflow: hidden;
    flex-shrink: 0;
    background: rgba(252,180,21,0.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem;
    box-shadow: 0 6px 24px rgba(252,180,21,0.25);
}
.pf-avatar-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }

.pf-identity-info { flex: 1 1 200px; }

.pf-display-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.9rem; color: var(--pf-head);
    letter-spacing: 0.04em; line-height: 1;
    margin-bottom: 4px;
}
.pf-username { font-size: 0.85rem; color: var(--pf-muted); font-weight: 700; margin-bottom: 8px; }
.pf-username span { color: var(--blue); }

.pf-badges { display: flex; gap: 8px; flex-wrap: wrap; }
.pf-badge {
    font-size: 0.72rem; font-weight: 800; letter-spacing: 0.06em;
    padding: 3px 12px; border-radius: 999px;
    background: rgba(6,146,175,0.1); color: var(--blue);
    border: 1px solid rgba(6,146,175,0.2);
}
.pf-badge.gold { background: rgba(252,180,21,0.1); color: #c08a00; border-color: rgba(252,180,21,0.3); }

.pf-stats-row {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    margin-left: auto;
    align-items: flex-end;
    padding-bottom: 4px;
}
.pf-stat { text-align: center; }
.pf-stat-num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.6rem; color: var(--pf-head);
    letter-spacing: 0.04em; line-height: 1;
}
.pf-stat-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--pf-muted); }

/* ── Layout grid ── */
.pf-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
}

/* ── Cards ── */
.pf-card {
    background: var(--pf-card);
    border: 1px solid var(--pf-border);
    border-radius: 18px;
    box-shadow: var(--pf-shadow);
    overflow: hidden;
}

.pf-card-head {
    padding: 20px 28px 16px;
    border-bottom: 1px solid var(--pf-border);
    background: var(--pf-raised);
    display: flex; align-items: center; justify-content: space-between;
}
.pf-card-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.15rem; color: var(--pf-head);
    letter-spacing: 0.04em;
}
.pf-card-body { padding: 24px 28px; }

/* ── Form fields ── */
.pf-field { margin-bottom: 20px; }
.pf-field:last-child { margin-bottom: 0; }
.pf-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }

.pf-label {
    display: block;
    font-size: 0.78rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--pf-muted); margin-bottom: 7px;
}

.pf-input, .pf-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1.5px solid var(--pf-border);
    border-radius: 9px;
    font-family: 'Nunito', sans-serif;
    font-size: 0.93rem; color: var(--pf-text);
    background: var(--pf-bg);
    box-sizing: border-box;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.pf-input:focus, .pf-textarea:focus {
    outline: none; border-color: var(--blue);
    background: #fff; box-shadow: 0 0 0 3px rgba(6,146,175,0.1);
}
.pf-input::placeholder, .pf-textarea::placeholder { color: #b0bac9; }
.pf-textarea { resize: vertical; min-height: 90px; line-height: 1.65; }
.pf-hint { font-size: 0.76rem; color: var(--pf-muted); margin-top: 5px; line-height: 1.5; }

/* ── Section divider ── */
.pf-section-label {
    font-size: 0.72rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--blue);
    padding: 16px 0 12px;
    border-bottom: 1px solid var(--pf-border);
    margin-bottom: 20px;
}

/* ── Save button ── */
.pf-save-btn {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #FCB415 0%, #f09a00 100%);
    color: #0d1526;
    font-family: 'Nunito', sans-serif;
    font-size: 0.95rem; font-weight: 800;
    border: none; border-radius: 9px;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
    position: relative; overflow: hidden;
    margin-top: 8px;
}
.pf-save-btn:hover   { opacity: 0.88; transform: translateY(-1px); }
.pf-save-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* ── Messages ── */
.pf-msg {
    padding: 13px 16px; border-radius: 9px;
    font-size: 0.88rem; font-weight: 700;
    margin-bottom: 20px; display: none; line-height: 1.5;
}
.pf-msg.success { background: rgba(48,108,53,0.1); border: 1px solid rgba(48,108,53,0.25); color: var(--green); }
.pf-msg.error   { background: rgba(213,55,79,0.08); border: 1px solid rgba(213,55,79,0.25); color: var(--red); }

/* ── Password toggle ── */
.pf-pw-group { position: relative; }
.pf-pw-group .pf-input { padding-right: 44px; }
.pf-pw-toggle {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--pf-muted); font-size: 1rem; padding: 4px;
    transition: color 0.2s;
}
.pf-pw-toggle:hover { color: var(--blue); }

/* ── Gravatar note ── */
.pf-avatar-note {
    font-size: 0.8rem; color: var(--pf-muted); line-height: 1.6;
    text-align: center; padding: 16px 20px;
    background: var(--pf-raised);
    border-radius: 10px;
    border: 1px solid var(--pf-border);
    margin-bottom: 20px;
}
.pf-avatar-note a { color: var(--blue); font-weight: 700; text-decoration: none; }

/* ── My Posts list ── */
.pf-post-item {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid var(--pf-border);
}
.pf-post-item:last-child { border-bottom: none; }
.pf-post-thumb {
    width: 56px; height: 56px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    background: var(--pf-raised);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
}
.pf-post-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pf-post-title { font-size: 0.9rem; font-weight: 800; color: var(--pf-head); line-height: 1.35; margin-bottom: 4px; }
.pf-post-title a { color: inherit; text-decoration: none; transition: color 0.2s; }
.pf-post-title a:hover { color: var(--blue); }
.pf-post-meta { font-size: 0.75rem; color: var(--pf-muted); display: flex; gap: 8px; flex-wrap: wrap; }
.pf-post-status {
    font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em;
    padding: 2px 8px; border-radius: 999px;
}
.pf-post-status.publish { background: rgba(48,108,53,0.1);  color: var(--green); }
.pf-post-status.pending { background: rgba(252,180,21,0.12); color: #c08a00; }
.pf-post-status.draft   { background: rgba(107,122,143,0.12); color: var(--pf-muted); }

.pf-no-posts { text-align: center; padding: 32px 16px; color: var(--pf-muted); font-size: 0.9rem; }
.pf-no-posts a { color: var(--blue); font-weight: 800; text-decoration: none; }

/* ── Quick info sidebar ── */
.pf-info-row {
    display: flex; gap: 10px; align-items: flex-start;
    padding: 11px 0; border-bottom: 1px solid var(--pf-border);
    font-size: 0.87rem;
}
.pf-info-row:last-child { border-bottom: none; }
.pf-info-icon { font-size: 1rem; flex-shrink: 0; width: 22px; text-align: center; margin-top: 1px; }
.pf-info-label { color: var(--pf-muted); font-weight: 700; font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1px; }
.pf-info-val { color: var(--pf-text); font-weight: 600; }

@media (max-width: 768px) {
    .pf-grid { grid-template-columns: 1fr; }
    .pf-identity { flex-direction: column; align-items: flex-start; }
    .pf-stats-row { margin-left: 0; }
    .pf-field-row { grid-template-columns: 1fr; }
    .pf-card-body { padding: 20px 18px; }
}
</style>

<!-- Hero -->
<section class="pf-hero"></section>

<div class="pf-page">

    <!-- Identity card -->
    <div class="pf-identity">
        <div class="pf-avatar-wrap">
            <?php echo get_avatar($uid, 88, '', '', array('class' => '')) ?: '👤'; ?>
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
            <div class="pf-msg success" style="display:block">✅ Profile updated successfully!</div>
            <?php elseif ($notice) : ?>
            <div class="pf-msg error" style="display:block">⚠️ <?php echo esc_html($notice); ?></div>
            <?php endif; ?>

            <!-- AJAX messages -->
            <div class="pf-msg" id="pf-msg"></div>

            <!-- Personal info -->
            <div class="pf-card" style="margin-bottom:20px">
                <div class="pf-card-head">
                    <span class="pf-card-title">✏️ Personal Information</span>
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

                        <button type="submit" class="pf-save-btn" id="pf-info-btn">
                            <span id="pf-info-btn-text">Save Changes</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change password -->
            <div class="pf-card">
                <div class="pf-card-head">
                    <span class="pf-card-title">🔒 Change Password</span>
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
                                <button type="button" class="pf-pw-toggle" data-target="pf_cur_pw">👁</button>
                            </div>
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_new_pw">New Password</label>
                            <div class="pf-pw-group">
                                <input type="password" id="pf_new_pw" name="pf_new_pw" class="pf-input"
                                       placeholder="At least 8 characters" autocomplete="new-password">
                                <button type="button" class="pf-pw-toggle" data-target="pf_new_pw">👁</button>
                            </div>
                        </div>

                        <div class="pf-field">
                            <label class="pf-label" for="pf_conf_pw">Confirm New Password</label>
                            <div class="pf-pw-group">
                                <input type="password" id="pf_conf_pw" name="pf_conf_pw" class="pf-input"
                                       placeholder="Repeat new password" autocomplete="new-password">
                                <button type="button" class="pf-pw-toggle" data-target="pf_conf_pw">👁</button>
                            </div>
                        </div>

                        <button type="submit" class="pf-save-btn" id="pf-pw-btn">
                            <span id="pf-pw-btn-text">Update Password</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Sidebar -->
        <div>
            <!-- Gravatar note -->
            <div class="pf-card" style="margin-bottom:20px">
                <div class="pf-card-head"><span class="pf-card-title">🖼️ Profile Photo</span></div>
                <div class="pf-card-body">
                    <div style="text-align:center;margin-bottom:16px">
                        <div style="width:80px;height:80px;border-radius:50%;overflow:hidden;border:3px solid var(--gold);margin:0 auto 12px;display:flex;align-items:center;justify-content:center;background:rgba(252,180,21,0.1);font-size:2rem;">
                            <?php echo get_avatar($uid, 80, '', '', array('class'=>'')) ?: '👤'; ?>
                        </div>
                    </div>
                    <div class="pf-avatar-note">
                        Profile photos are powered by <a href="https://gravatar.com" target="_blank" rel="noopener">Gravatar</a>. To change your photo, create or update your account at <strong>gravatar.com</strong> using the same email address.
                    </div>
                </div>
            </div>

            <!-- Quick info -->
            <div class="pf-card" style="margin-bottom:20px">
                <div class="pf-card-head"><span class="pf-card-title">ℹ️ Account Details</span></div>
                <div class="pf-card-body" style="padding-top:8px;padding-bottom:8px">
                    <div class="pf-info-row">
                        <div class="pf-info-icon">👤</div>
                        <div>
                            <div class="pf-info-label">Username</div>
                            <div class="pf-info-val"><?php echo esc_html($user->user_login); ?></div>
                        </div>
                    </div>
                    <div class="pf-info-row">
                        <div class="pf-info-icon">📅</div>
                        <div>
                            <div class="pf-info-label">Member Since</div>
                            <div class="pf-info-val"><?php echo esc_html($join_date); ?></div>
                        </div>
                    </div>
                    <div class="pf-info-row">
                        <div class="pf-info-icon">✍️</div>
                        <div>
                            <div class="pf-info-label">Published Posts</div>
                            <div class="pf-info-val"><?php echo esc_html($pub_count); ?></div>
                        </div>
                    </div>
                    <div class="pf-info-row">
                        <div class="pf-info-icon">📧</div>
                        <div>
                            <div class="pf-info-label">Email</div>
                            <div class="pf-info-val" style="word-break:break-all"><?php echo esc_html($user->user_email); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My posts -->
            <div class="pf-card">
                <div class="pf-card-head">
                    <span class="pf-card-title">📝 My Posts</span>
                    <a href="<?php echo esc_url(home_url('/submit-blog/')); ?>"
                       style="font-size:0.78rem;font-weight:800;color:var(--gold);text-decoration:none">+ New Post</a>
                </div>
                <div class="pf-card-body" style="padding-top:8px;padding-bottom:8px">
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
                                ✈️
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;min-width:0">
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
            btn.textContent = show ? '🙈' : '👁';
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

        infoBtnT.textContent = '⏳ Saving…';
        infoBtn.disabled     = true;

        var fd = new FormData(infoForm);
        fd.append('action', 'tw_update_profile_info');

        fetch(ajaxUrl, { method:'POST', body:fd, credentials:'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    show(infoMsg, '✅ ' + data.data.message, 'success');
                    // Update displayed name in header if changed
                    var dn = document.querySelector('.tw-user-display');
                    if (dn && data.data.display_name) dn.textContent = data.data.display_name;
                } else {
                    show(infoMsg, '⚠️ ' + (data.data || 'Update failed.'), 'error');
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

        pwBtnT.textContent = '⏳ Updating…';
        pwBtn.disabled     = true;

        var fd = new FormData(pwForm);
        fd.append('action', 'tw_update_profile_password');

        fetch(ajaxUrl, { method:'POST', body:fd, credentials:'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    show(pwMsg, '✅ Password updated! You may need to log in again.', 'success');
                    pwForm.reset();
                    // Re-auth cookie is refreshed server-side; just reload after short delay
                    setTimeout(function () { window.location.reload(); }, 2200);
                } else {
                    show(pwMsg, '⚠️ ' + (data.data || 'Update failed.'), 'error');
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
