<?php
/**
 * Template Name: Submit a Blog Post
 *
 * Front-end blog submission form. Logged-in editors/admins publish directly;
 * everyone else submits as "pending" for admin review.
 * Styles are in style.css
 */

get_header();

$is_logged_in  = is_user_logged_in();
$can_publish   = current_user_can('publish_posts');
$categories    = get_categories(array('hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
?>

<!-- HERO -->
<section class="sb-hero">
    <div class="sb-hero-kicker"><i class="fi-rr-edit-alt" aria-hidden="true"></i> Community Blog</div>
    <h1>Share Your <span>Travel Story</span></h1>
    <p>Write a guide, tip, or travel experience — inspire thousands of explorers worldwide.</p>
</section>

<div class="sb-page">
    <div class="sb-form-card" id="sb-form-card">

        <!-- Form header -->
        <div class="sb-form-header">
            <div class="sb-form-header-title"><i class="fi-rr-memo" aria-hidden="true"></i> New Blog Post</div>
            <?php if ($can_publish) : ?>
            <span class="sb-status-badge publish"><i class="fi-rr-check" aria-hidden="true"></i> Will publish immediately</span>
            <?php else : ?>
            <span class="sb-status-badge pending"><i class="fi-rr-hourglass" aria-hidden="true"></i> Pending review before publishing</span>
            <?php endif; ?>
        </div>

        <!-- Success screen (hidden until submit) -->
        <div class="sb-success-screen" id="sb-success-screen">
            <div class="sb-success-icon"><i class="fi-rr-confetti" aria-hidden="true"></i></div>
            <h2>Post Submitted!</h2>
            <p id="sb-success-msg">Your post has been submitted and is pending review. We'll publish it within 24–48 hours.</p>
            <div class="sb-success-btns">
                <a href="<?php echo esc_url(home_url('/blog-affiliates/')); ?>" class="btn-primary">← Back to Blog</a>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="btn-secondary">Submit Another</a>
            </div>
        </div>

        <!-- The form -->
        <form class="sb-form-body" id="sb-submit-form" enctype="multipart/form-data" novalidate>
            <?php wp_nonce_field('tw_blog_submit_nonce', 'tw_blog_nonce'); ?>

            <!-- ① Post basics -->
            <div class="sb-section-title">① Post Details</div>

            <?php if (!$is_logged_in) : ?>
            <div class="sb-login-notice">
                <i class="fi-rr-bulb" aria-hidden="true"></i> <strong>Have an account?</strong> <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">Log in</a> to publish under your name. Or fill in your details below to submit as a guest.
            </div>
            <?php endif; ?>

            <div class="sb-field">
                <label class="sb-label" for="sb_title">Post Title <span class="req">*</span></label>
                <input type="text" id="sb_title" name="sb_title" class="sb-input"
                       placeholder="e.g. 10 Hidden Gems in Bali You Must Visit" maxlength="200" required>
            </div>

            <div class="sb-field-row">
                <div class="sb-field no-margin">
                    <label class="sb-label" for="sb_category">Category</label>
                    <select id="sb_category" name="sb_category" class="sb-select">
                        <option value="">— Select a category —</option>
                        <?php foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                        <option value="new">+ Add new category…</option>
                    </select>
                </div>
                <div class="sb-field no-margin" id="sb-new-cat-wrap" style="display:none">
                    <label class="sb-label" for="sb_new_category">New Category Name</label>
                    <input type="text" id="sb_new_category" name="sb_new_category" class="sb-input" placeholder="e.g. Budget Travel">
                </div>
            </div>

            <div class="sb-field mt-18">
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
                    <button type="button" class="sb-img-preview-remove" id="sb-img-remove"><i class="fi-rr-cross-small" aria-hidden="true"></i> Remove</button>
                </div>
                <div class="sb-img-zone" id="sb-img-zone">
                    <input type="file" id="sb_image" name="sb_image" accept="image/*">
                    <div class="sb-img-icon"><i class="fi-rr-picture" aria-hidden="true"></i></div>
                    <p><strong>Click to upload</strong> or drag &amp; drop<br>JPG, PNG, WebP · Max 4 MB</p>
                </div>
            </div>

            <hr class="sb-divider">

            <!-- ③ Content -->
            <div class="sb-section-title">③ Post Content <span class="sb-section-note">— write your full article here</span></div>
            <div class="sb-field">
                <textarea id="sb_content" name="sb_content" class="sb-textarea sb-content-area"
                          placeholder="Start writing your travel story… Use blank lines to separate paragraphs. You can use *bold*, _italic_, and ## Heading 2 formatting." required></textarea>
                <p class="sb-hint"><i class="fi-rr-bulb" aria-hidden="true"></i> Aim for at least 500 words. Well-structured posts with headings and tips perform best.</p>
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
                <div class="sb-field no-margin">
                    <label class="sb-label" for="sb_author_name">Your Name <span class="req">*</span></label>
                    <input type="text" id="sb_author_name" name="sb_author_name" class="sb-input"
                           placeholder="Jane Doe" maxlength="80" required>
                </div>
                <div class="sb-field no-margin">
                    <label class="sb-label" for="sb_author_email">Email <span class="req">*</span></label>
                    <input type="email" id="sb_author_email" name="sb_author_email" class="sb-input"
                           placeholder="jane@example.com" required>
                    <p class="sb-hint no-margin">Not published. Used to notify you when your post goes live.</p>
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
                <button type="submit" class="btn-primary btn-lg btn-block" id="sb-submit-btn">
                    <span id="sb-btn-text"><i class="fi-rr-plane" aria-hidden="true"></i> Submit Post</span>
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
        <h3><i class="fi-rr-clipboard-list" aria-hidden="true"></i> Submission Guidelines</h3>
        <ul>
            <li><span class="icon"><i class="fi-rr-check-circle" aria-hidden="true"></i></span> Write original, first-hand travel experiences or helpful guides. No copy-pasted content.</li>
            <li><span class="icon"><i class="fi-rr-check-circle" aria-hidden="true"></i></span> Include practical tips — costs, transport, opening hours, insider advice.</li>
            <li><span class="icon"><i class="fi-rr-check-circle" aria-hidden="true"></i></span> Minimum 400 words. The more detailed, the better for our readers.</li>
            <li><span class="icon"><i class="fi-rr-ban" aria-hidden="true"></i></span> No promotional content, spam links, or affiliate links in the body (we handle monetisation separately).</li>
            <li><span class="icon"><i class="fi-rr-ban" aria-hidden="true"></i></span> No AI-generated text. We check all submissions. Authentic voices only.</li>
            <li><span class="icon"><i class="fi-rr-camera" aria-hidden="true"></i></span> Use only images you own or that have a Creative Commons licence.</li>
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

        btnText.innerHTML = '<i class="fi-rr-hourglass" aria-hidden="true"></i> Submitting…';
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
                    ? '<i class="fi-rr-confetti" aria-hidden="true"></i> Your post is live! <a href="' + data.data.url + '" style="color:inherit;font-weight:800">View it here →</a>'
                    : '<i class="fi-rr-check-circle" aria-hidden="true"></i> Post submitted! Our team will review it and publish within 48 hours. Thank you!';
                document.getElementById('sb-success-msg').innerHTML = msg;
                // If published, update the "Back to blog" button
                if (data.data.status === 'publish') {
                    var primary = successScr.querySelector('.btn-primary');
                    if (primary) primary.href = data.data.url;
                }
                // Show success, hide form
                form.style.display = 'none';
                document.querySelector('.sb-form-header').style.display = 'none';
                successScr.style.display = 'block';
            } else {
                showError(data.data || 'Something went wrong. Please try again.');
                btnText.innerHTML = '<i class="fi-rr-plane" aria-hidden="true"></i> Submit Post';
                submitBtn.disabled  = false;
            }
        })
        .catch(function () {
            showError('Network error. Please check your connection and try again.');
            btnText.innerHTML = '<i class="fi-rr-plane" aria-hidden="true"></i> Submit Post';
            submitBtn.disabled  = false;
        });
    });

    function showError(msg) {
        errMsg.textContent    = msg;
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
