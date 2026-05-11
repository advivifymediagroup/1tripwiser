<?php
/**
 * Template Name: Submit a Blog Post
 *
 * Front-end blog submission form. Logged-in editors/admins publish directly;
 * everyone else submits as "pending" for admin review.
 */

get_header();

$is_logged_in  = is_user_logged_in();
$can_publish   = current_user_can('publish_posts');
$categories    = get_categories(array('hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
/* ================================================================
   SUBMIT BLOG PAGE
   ================================================================ */
:root {
    --sb-bg:      #f5f8fa;
    --sb-card:    #ffffff;
    --sb-raised:  #eef2f7;
    --sb-text:    #1a2535;
    --sb-muted:   #6b7a8f;
    --sb-border:  #dde5ef;
    --sb-head:    #0D1526;
    --sb-shadow:  0 8px 32px rgba(0,0,0,0.08);
    --gold:  #FCB415;
    --blue:  #0692AF;
    --green: #306C35;
    --red:   #D5374F;
}

body { background-color: var(--sb-bg) !important; font-family: 'Nunito', sans-serif; color: var(--sb-text); }

/* ── Hero ── */
.sb-hero {
    background: linear-gradient(135deg, #0d1526 0%, #0a1e30 60%, #071522 100%);
    padding: 72px 24px 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.sb-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 50% 0%, rgba(6,146,175,0.18) 0%, transparent 70%);
}
.sb-hero-kicker {
    position: relative;
    display: inline-block;
    font-size: 0.72rem; font-weight: 800;
    letter-spacing: 0.18em; text-transform: uppercase;
    color: var(--blue);
    background: rgba(6,146,175,0.12);
    border: 1px solid rgba(6,146,175,0.3);
    border-radius: 999px;
    padding: 5px 18px;
    margin-bottom: 20px;
}
.sb-hero h1 {
    position: relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.4rem, 6vw, 4rem);
    color: #fff;
    letter-spacing: 0.04em;
    line-height: 1.05;
    margin-bottom: 16px;
}
.sb-hero h1 span { color: var(--gold); }
.sb-hero p {
    position: relative;
    font-size: 1rem;
    color: rgba(255,255,255,0.55);
    max-width: 520px;
    margin: 0 auto;
    line-height: 1.7;
}

/* ── Page layout ── */
.sb-page {
    max-width: 820px;
    margin: 0 auto;
    padding: 52px 24px 96px;
}

/* ── Form card ── */
.sb-form-card {
    background: var(--sb-card);
    border: 1px solid var(--sb-border);
    border-radius: 20px;
    box-shadow: var(--sb-shadow);
    overflow: hidden;
}

.sb-form-header {
    padding: 28px 36px 24px;
    border-bottom: 1px solid var(--sb-border);
    background: var(--sb-raised);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.sb-form-header-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.35rem;
    color: var(--sb-head);
    letter-spacing: 0.04em;
}
.sb-status-badge {
    font-size: 0.75rem; font-weight: 800;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 4px 14px; border-radius: 999px;
}
.sb-status-badge.publish { background: rgba(48,108,53,0.12); color: var(--green); border: 1px solid rgba(48,108,53,0.25); }
.sb-status-badge.pending { background: rgba(252,180,21,0.12); color: #c08a00; border: 1px solid rgba(252,180,21,0.3); }

.sb-form-body { padding: 36px; }

/* ── Field groups ── */
.sb-field { margin-bottom: 24px; }
.sb-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
.sb-label {
    display: block;
    font-size: 0.8rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--sb-muted);
    margin-bottom: 8px;
}
.sb-label .req { color: var(--red); margin-left: 2px; }

.sb-input,
.sb-select,
.sb-textarea {
    width: 100%;
    padding: 13px 16px;
    border: 1.5px solid var(--sb-border);
    border-radius: 10px;
    font-family: 'Nunito', sans-serif;
    font-size: 0.95rem;
    color: var(--sb-text);
    background: var(--sb-bg);
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
    appearance: none;
}
.sb-input:focus, .sb-select:focus, .sb-textarea:focus {
    outline: none;
    border-color: var(--blue);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(6,146,175,0.1);
}
.sb-input::placeholder, .sb-textarea::placeholder { color: #b0bac9; }
.sb-textarea { resize: vertical; min-height: 80px; line-height: 1.65; }
.sb-textarea.sb-content-area { min-height: 320px; font-size: 0.93rem; }
.sb-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7a8f' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 40px;
}

.sb-hint { font-size: 0.78rem; color: var(--sb-muted); margin-top: 6px; line-height: 1.5; }

/* ── Image upload zone ── */
.sb-img-zone {
    border: 2px dashed var(--sb-border);
    border-radius: 12px;
    padding: 32px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    background: var(--sb-bg);
    position: relative;
}
.sb-img-zone:hover, .sb-img-zone.drag-over {
    border-color: var(--blue);
    background: rgba(6,146,175,0.04);
}
.sb-img-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.sb-img-icon { font-size: 2.2rem; margin-bottom: 10px; }
.sb-img-zone p { color: var(--sb-muted); font-size: 0.87rem; margin: 0; line-height: 1.6; }
.sb-img-zone strong { color: var(--blue); font-weight: 800; }
.sb-img-preview {
    display: none;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    max-height: 240px;
    background: #000;
}
.sb-img-preview img { width: 100%; height: 240px; object-fit: cover; display: block; opacity: 0.92; }
.sb-img-preview-remove {
    position: absolute; top: 10px; right: 10px;
    background: rgba(0,0,0,0.65); color: #fff;
    border: none; border-radius: 6px;
    font-size: 0.78rem; font-weight: 800;
    padding: 4px 10px; cursor: pointer;
    transition: background 0.2s;
}
.sb-img-preview-remove:hover { background: rgba(213,55,79,0.85); }

/* ── Tags pills input ── */
.sb-tags-wrap {
    border: 1.5px solid var(--sb-border);
    border-radius: 10px;
    background: var(--sb-bg);
    padding: 8px 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
    cursor: text;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    min-height: 48px;
}
.sb-tags-wrap:focus-within {
    border-color: var(--blue);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(6,146,175,0.1);
}
.sb-tag-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(6,146,175,0.1); border: 1px solid rgba(6,146,175,0.22);
    color: var(--blue); padding: 3px 10px 3px 12px;
    border-radius: 999px; font-size: 0.8rem; font-weight: 700;
}
.sb-tag-pill button {
    background: none; border: none; color: var(--blue);
    cursor: pointer; font-size: 0.9rem; line-height: 1; padding: 0 2px;
    opacity: 0.6; transition: opacity 0.2s;
}
.sb-tag-pill button:hover { opacity: 1; }
.sb-tags-input {
    border: none; background: transparent; outline: none;
    font-family: 'Nunito', sans-serif; font-size: 0.93rem;
    color: var(--sb-text); min-width: 80px; flex: 1;
    padding: 4px 4px;
}
.sb-tags-input::placeholder { color: #b0bac9; }

/* ── Login notice ── */
.sb-login-notice {
    background: rgba(252,180,21,0.07);
    border: 1px solid rgba(252,180,21,0.25);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 28px;
    font-size: 0.9rem;
    color: var(--sb-text);
}
.sb-login-notice a { color: var(--blue); font-weight: 800; text-decoration: none; }
.sb-login-notice a:hover { text-decoration: underline; }
.sb-section-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.1rem; letter-spacing: 0.05em;
    color: var(--sb-muted); margin-bottom: 18px;
    padding-bottom: 8px; border-bottom: 1px solid var(--sb-border);
}

/* ── Divider ── */
.sb-divider { border: none; border-top: 1px solid var(--sb-border); margin: 32px 0; }

/* ── Submit area ── */
.sb-submit-row {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 8px;
}
.sb-submit-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 36px;
    background: linear-gradient(135deg, #FCB415 0%, #f09a00 100%);
    color: #0D1526; font-family: 'Nunito', sans-serif;
    font-size: 1rem; font-weight: 800;
    border: none; border-radius: 10px; cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
    letter-spacing: 0.02em;
}
.sb-submit-btn:hover { opacity: 0.88; transform: translateY(-1px); }
.sb-submit-btn:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }
.sb-submit-note { font-size: 0.82rem; color: var(--sb-muted); line-height: 1.55; }

/* ── Status messages ── */
.sb-msg {
    padding: 14px 20px; border-radius: 10px;
    font-size: 0.9rem; font-weight: 700;
    margin-top: 16px; display: none;
}
.sb-msg.success { background: rgba(48,108,53,0.1); border: 1px solid rgba(48,108,53,0.25); color: var(--green); }
.sb-msg.error   { background: rgba(213,55,79,0.08); border: 1px solid rgba(213,55,79,0.25); color: var(--red); }

/* ── Success screen ── */
.sb-success-screen {
    display: none;
    text-align: center;
    padding: 64px 40px;
}
.sb-success-icon { font-size: 4rem; margin-bottom: 20px; }
.sb-success-screen h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.2rem; color: var(--sb-head);
    letter-spacing: 0.04em; margin-bottom: 14px;
}
.sb-success-screen p { font-size: 0.97rem; color: var(--sb-muted); max-width: 420px; margin: 0 auto 28px; line-height: 1.7; }
.sb-success-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.sb-success-btn {
    padding: 11px 26px; border-radius: 10px; font-weight: 800;
    font-size: 0.88rem; text-decoration: none; transition: opacity 0.2s;
}
.sb-success-btn.primary { background: linear-gradient(135deg, #FCB415, #f09a00); color: #0D1526; }
.sb-success-btn.secondary { background: var(--sb-raised); color: var(--sb-text); border: 1px solid var(--sb-border); }
.sb-success-btn:hover { opacity: 0.88; }

/* ── Guidelines card ── */
.sb-guidelines {
    background: var(--sb-card);
    border: 1px solid var(--sb-border);
    border-radius: 16px;
    padding: 28px 32px;
    margin-top: 32px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.05);
}
.sb-guidelines h3 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.2rem; color: var(--sb-head);
    letter-spacing: 0.04em; margin-bottom: 16px;
}
.sb-guidelines ul { list-style: none; padding: 0; margin: 0; }
.sb-guidelines li {
    display: flex; gap: 10px; align-items: flex-start;
    font-size: 0.87rem; color: var(--sb-muted);
    line-height: 1.6; margin-bottom: 10px;
}
.sb-guidelines li:last-child { margin-bottom: 0; }
.sb-guidelines li .icon { font-size: 1rem; flex-shrink: 0; margin-top: 2px; }

/* ── Progress indicator ── */
.sb-progress {
    display: flex; gap: 0; margin-bottom: 32px;
    border: 1px solid var(--sb-border);
    border-radius: 10px; overflow: hidden;
}
.sb-progress-step {
    flex: 1; padding: 12px 8px; text-align: center;
    font-size: 0.75rem; font-weight: 800;
    letter-spacing: 0.05em; text-transform: uppercase;
    color: var(--sb-muted); background: var(--sb-raised);
    border-right: 1px solid var(--sb-border);
    transition: background 0.2s, color 0.2s;
}
.sb-progress-step:last-child { border-right: none; }
.sb-progress-step.active { background: rgba(6,146,175,0.1); color: var(--blue); }
.sb-progress-step .step-num {
    display: block; font-size: 1rem; margin-bottom: 2px;
}

@media (max-width: 640px) {
    .sb-field-row { grid-template-columns: 1fr; }
    .sb-form-body { padding: 24px 20px; }
    .sb-form-header { padding: 20px 22px; }
    .sb-submit-row { flex-direction: column; align-items: flex-start; }
}
</style>

<!-- HERO -->
<section class="sb-hero">
    <div class="sb-hero-kicker">✍️ Community Blog</div>
    <h1>Share Your <span>Travel Story</span></h1>
    <p>Write a guide, tip, or travel experience — inspire thousands of explorers worldwide.</p>
</section>

<div class="sb-page">
    <div class="sb-form-card" id="sb-form-card">

        <!-- Form header -->
        <div class="sb-form-header">
            <div class="sb-form-header-title">📝 New Blog Post</div>
            <?php if ($can_publish) : ?>
            <span class="sb-status-badge publish">✓ Will publish immediately</span>
            <?php else : ?>
            <span class="sb-status-badge pending">⏳ Pending review before publishing</span>
            <?php endif; ?>
        </div>

        <!-- Success screen (hidden until submit) -->
        <div class="sb-success-screen" id="sb-success-screen">
            <div class="sb-success-icon">🎉</div>
            <h2>Post Submitted!</h2>
            <p id="sb-success-msg">Your post has been submitted and is pending review. We'll publish it within 24–48 hours.</p>
            <div class="sb-success-btns">
                <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="sb-success-btn primary">← Back to Blog</a>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="sb-success-btn secondary">Submit Another</a>
            </div>
        </div>

        <!-- The form -->
        <form class="sb-form-body" id="sb-submit-form" enctype="multipart/form-data" novalidate>
            <?php wp_nonce_field('tw_blog_submit_nonce', 'tw_blog_nonce'); ?>

            <!-- ① Post basics -->
            <div class="sb-section-title">① Post Details</div>

            <?php if (!$is_logged_in) : ?>
            <div class="sb-login-notice">
                💡 <strong>Have an account?</strong> <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">Log in</a> to publish under your name. Or fill in your details below to submit as a guest.
            </div>
            <?php endif; ?>

            <div class="sb-field">
                <label class="sb-label" for="sb_title">Post Title <span class="req">*</span></label>
                <input type="text" id="sb_title" name="sb_title" class="sb-input"
                       placeholder="e.g. 10 Hidden Gems in Bali You Must Visit" maxlength="200" required>
            </div>

            <div class="sb-field-row">
                <div class="sb-field" style="margin:0">
                    <label class="sb-label" for="sb_category">Category</label>
                    <select id="sb_category" name="sb_category" class="sb-select">
                        <option value="">— Select a category —</option>
                        <?php foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                        <option value="new">+ Add new category…</option>
                    </select>
                </div>
                <div class="sb-field" style="margin:0" id="sb-new-cat-wrap" style="display:none">
                    <label class="sb-label" for="sb_new_category">New Category Name</label>
                    <input type="text" id="sb_new_category" name="sb_new_category" class="sb-input" placeholder="e.g. Budget Travel">
                </div>
            </div>

            <div class="sb-field" style="margin-top:18px">
                <label class="sb-label" for="sb_excerpt">Short Teaser / Excerpt</label>
                <textarea id="sb_excerpt" name="sb_excerpt" class="sb-textarea" rows="3"
                          placeholder="A 1–2 sentence hook that tells readers what your post is about…" maxlength="300"></textarea>
                <p class="sb-hint">Shown on the blog listing page. Keep it under 150 characters for best results.</p>
            </div>

            <hr class="sb-divider">

            <!-- ② Featured image -->
            <div class="sb-section-title">② Featured Image</div>
            <div class="sb-field">
                <div class="sb-img-preview" id="sb-img-preview">
                    <img id="sb-img-preview-img" src="" alt="Preview">
                    <button type="button" class="sb-img-preview-remove" id="sb-img-remove">✕ Remove</button>
                </div>
                <div class="sb-img-zone" id="sb-img-zone">
                    <input type="file" id="sb_image" name="sb_image" accept="image/*">
                    <div class="sb-img-icon">🖼️</div>
                    <p><strong>Click to upload</strong> or drag &amp; drop<br>JPG, PNG, WebP · Max 4 MB</p>
                </div>
            </div>

            <hr class="sb-divider">

            <!-- ③ Content -->
            <div class="sb-section-title">③ Post Content <span style="font-size:0.78rem;font-weight:400;text-transform:none;letter-spacing:0">— write your full article here</span></div>
            <div class="sb-field">
                <textarea id="sb_content" name="sb_content" class="sb-textarea sb-content-area"
                          placeholder="Start writing your travel story… Use blank lines to separate paragraphs. You can use *bold*, _italic_, and ## Heading 2 formatting." required></textarea>
                <p class="sb-hint">💡 Aim for at least 500 words. Well-structured posts with headings and tips perform best.</p>
            </div>

            <div class="sb-field">
                <label class="sb-label">Tags</label>
                <div class="sb-tags-wrap" id="sb-tags-wrap">
                    <input type="text" class="sb-tags-input" id="sb-tags-input"
                           placeholder="Type a tag and press Enter…">
                </div>
                <input type="hidden" id="sb_tags" name="sb_tags" value="">
                <p class="sb-hint">Add up to 8 tags (e.g. Bali, Budget Tips, Solo Travel). Press Enter or comma to add each tag.</p>
            </div>

            <hr class="sb-divider">

            <!-- ④ Author info (guests only) -->
            <?php if (!$is_logged_in) : ?>
            <div class="sb-section-title">④ About You</div>
            <div class="sb-field-row">
                <div class="sb-field" style="margin:0">
                    <label class="sb-label" for="sb_author_name">Your Name <span class="req">*</span></label>
                    <input type="text" id="sb_author_name" name="sb_author_name" class="sb-input"
                           placeholder="Jane Doe" maxlength="80" required>
                </div>
                <div class="sb-field" style="margin:0">
                    <label class="sb-label" for="sb_author_email">Email <span class="req">*</span></label>
                    <input type="email" id="sb_author_email" name="sb_author_email" class="sb-input"
                           placeholder="jane@example.com" required>
                    <p class="sb-hint" style="margin:0">Not published. Used to notify you when your post goes live.</p>
                </div>
            </div>
            <div class="sb-field">
                <label class="sb-label" for="sb_author_bio">Short Bio (optional)</label>
                <input type="text" id="sb_author_bio" name="sb_author_bio" class="sb-input"
                       placeholder="e.g. Adventure photographer exploring Southeast Asia" maxlength="160">
            </div>
            <hr class="sb-divider">
            <?php endif; ?>

            <!-- Submit -->
            <div class="sb-submit-row">
                <button type="submit" class="sb-submit-btn" id="sb-submit-btn">
                    <span id="sb-btn-text">✈ Submit Post</span>
                </button>
                <p class="sb-submit-note">
                    <?php if ($can_publish) : ?>
                        Your post will be published immediately to the blog.
                    <?php elseif ($is_logged_in) : ?>
                        Your post will be reviewed by our editorial team before going live (usually within 48 hours).
                    <?php else : ?>
                        By submitting you agree to our content guidelines. We'll review your post before publishing.
                    <?php endif; ?>
                </p>
            </div>
            <div class="sb-msg" id="sb-error-msg"></div>

        </form>
    </div>

    <!-- Writing guidelines card -->
    <div class="sb-guidelines">
        <h3>📋 Submission Guidelines</h3>
        <ul>
            <li><span class="icon">✅</span> Write original, first-hand travel experiences or helpful guides. No copy-pasted content.</li>
            <li><span class="icon">✅</span> Include practical tips — costs, transport, opening hours, insider advice.</li>
            <li><span class="icon">✅</span> Minimum 400 words. The more detailed, the better for our readers.</li>
            <li><span class="icon">🚫</span> No promotional content, spam links, or affiliate links in the body (we handle monetisation separately).</li>
            <li><span class="icon">🚫</span> No AI-generated text. We check all submissions. Authentic voices only.</li>
            <li><span class="icon">📸</span> Use only images you own or that have a Creative Commons licence.</li>
        </ul>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ── Image preview ── */
    var imgInput   = document.getElementById('sb_image');
    var imgZone    = document.getElementById('sb-img-zone');
    var imgPreview = document.getElementById('sb-img-preview');
    var imgPrevImg = document.getElementById('sb-img-preview-img');
    var imgRemove  = document.getElementById('sb-img-remove');

    imgInput.addEventListener('change', function () {
        previewFile(this.files[0]);
    });

    imgZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        imgZone.classList.add('drag-over');
    });
    imgZone.addEventListener('dragleave', function () {
        imgZone.classList.remove('drag-over');
    });
    imgZone.addEventListener('drop', function (e) {
        e.preventDefault();
        imgZone.classList.remove('drag-over');
        var file = e.dataTransfer.files[0];
        if (file) {
            // Assign to input so it gets submitted
            var dt = new DataTransfer();
            dt.items.add(file);
            imgInput.files = dt.files;
            previewFile(file);
        }
    });

    function previewFile(file) {
        if (!file || !file.type.match('image.*')) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            imgPrevImg.src = e.target.result;
            imgPreview.style.display = 'block';
            imgZone.style.display    = 'none';
        };
        reader.readAsDataURL(file);
    }

    imgRemove.addEventListener('click', function () {
        imgInput.value   = '';
        imgPrevImg.src   = '';
        imgPreview.style.display = 'none';
        imgZone.style.display    = '';
    });

    /* ── New category toggle ── */
    var catSelect  = document.getElementById('sb_category');
    var newCatWrap = document.getElementById('sb-new-cat-wrap');
    catSelect.addEventListener('change', function () {
        newCatWrap.style.display = this.value === 'new' ? '' : 'none';
    });

    /* ── Tags pill input ── */
    var tagsWrap   = document.getElementById('sb-tags-wrap');
    var tagsInput  = document.getElementById('sb-tags-input');
    var tagsHidden = document.getElementById('sb_tags');
    var tagsList   = [];

    tagsWrap.addEventListener('click', function () { tagsInput.focus(); });

    tagsInput.addEventListener('keydown', function (e) {
        if ((e.key === 'Enter' || e.key === ',') && this.value.trim()) {
            e.preventDefault();
            addTag(this.value.trim().replace(/,$/, ''));
            this.value = '';
        }
        if (e.key === 'Backspace' && !this.value && tagsList.length) {
            removeTag(tagsList.length - 1);
        }
    });

    function addTag(text) {
        if (!text || tagsList.length >= 8 || tagsList.indexOf(text) !== -1) return;
        tagsList.push(text);
        renderTags();
    }

    function removeTag(index) {
        tagsList.splice(index, 1);
        renderTags();
    }

    function renderTags() {
        var pills = tagsWrap.querySelectorAll('.sb-tag-pill');
        pills.forEach(function (p) { p.remove(); });
        tagsList.forEach(function (tag, i) {
            var pill = document.createElement('span');
            pill.className = 'sb-tag-pill';
            pill.innerHTML = '#' + escHtml(tag) + '<button type="button" aria-label="Remove tag">×</button>';
            pill.querySelector('button').addEventListener('click', function () { removeTag(i); });
            tagsWrap.insertBefore(pill, tagsInput);
        });
        tagsHidden.value = tagsList.join(',');
    }

    /* ── Form submission ── */
    var form      = document.getElementById('sb-submit-form');
    var submitBtn = document.getElementById('sb-submit-btn');
    var btnText   = document.getElementById('sb-btn-text');
    var errMsg    = document.getElementById('sb-error-msg');
    var successScr = document.getElementById('sb-success-screen');
    var formCard  = document.getElementById('sb-form-card');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errMsg.style.display = 'none';

        var title   = document.getElementById('sb_title').value.trim();
        var content = document.getElementById('sb_content').value.trim();

        if (!title) { showError('Please enter a post title.'); return; }
        if (content.length < 100) { showError('Your post content is too short. Please write at least a few sentences.'); return; }

        <?php if (!$is_logged_in) : ?>
        var name  = document.getElementById('sb_author_name').value.trim();
        var email = document.getElementById('sb_author_email').value.trim();
        if (!name)  { showError('Please enter your name.'); return; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('Please enter a valid email address.'); return; }
        <?php endif; ?>

        var fd = new FormData(form);
        fd.append('action', 'tw_submit_blog');

        btnText.textContent = '⏳ Submitting…';
        submitBtn.disabled  = true;

        fetch(<?php echo json_encode(admin_url('admin-ajax.php')); ?>, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                // Show success screen
                var msg = data.data.status === 'publish'
                    ? '🎉 Your post is live! <a href="' + data.data.url + '" style="color:inherit;font-weight:800">View it here →</a>'
                    : '✅ Post submitted! Our team will review it and publish within 48 hours. Thank you!';
                document.getElementById('sb-success-msg').innerHTML = msg;
                // If published, update the "Back to blog" button
                if (data.data.status === 'publish') {
                    var primary = successScr.querySelector('.sb-success-btn.primary');
                    if (primary) primary.href = data.data.url;
                }
                // Show success, hide form
                form.style.display = 'none';
                document.querySelector('.sb-form-header').style.display = 'none';
                successScr.style.display = 'block';
            } else {
                showError(data.data || 'Something went wrong. Please try again.');
                btnText.textContent = '✈ Submit Post';
                submitBtn.disabled  = false;
            }
        })
        .catch(function () {
            showError('Network error. Please check your connection and try again.');
            btnText.textContent = '✈ Submit Post';
            submitBtn.disabled  = false;
        });
    });

    function showError(msg) {
        errMsg.textContent    = '⚠️ ' + msg;
        errMsg.className      = 'sb-msg error';
        errMsg.style.display  = 'block';
        errMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>

<?php get_footer(); ?>
