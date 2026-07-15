<?php
/**
 * Template Name: Register
 */

// Already logged in → redirect home
if ( is_user_logged_in() ) {
    wp_safe_redirect( home_url('/') );
    exit;
}

get_header();
?>

<div class="auth-page">

    <!-- Hero -->
    <section class="auth-hero">
        <div class="auth-hero-kicker"><i class="fi-rr-globe" aria-hidden="true"></i> Join the Community</div>
        <h1>Create Your <span>Free Account</span></h1>
        <p>Join thousands of travellers sharing honest guides, tips, and experiences.</p>
    </section>

    <div class="auth-wrap">

        <div class="auth-msg" id="auth-reg-msg"></div>

        <div class="auth-card" id="tw-reg-card">

            <form id="tw-register-form" novalidate>
                <?php wp_nonce_field( 'tw_register_nonce', 'tw_register_nonce' ); ?>

                <!-- Name row -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div class="tw-float-group">
                        <input type="text" id="tw_first_name" name="tw_first_name" autocomplete="given-name" required>
                        <label for="tw_first_name">First Name</label>
                    </div>
                    <div class="tw-float-group">
                        <input type="text" id="tw_last_name" name="tw_last_name" autocomplete="family-name">
                        <label for="tw_last_name">Last Name</label>
                    </div>
                </div>

                <!-- Username -->
                <div class="tw-float-group">
                    <input type="text" id="tw_reg_username" name="tw_reg_username"
                           autocomplete="username" required pattern="[a-zA-Z0-9_\-]{3,}">
                    <label for="tw_reg_username">Username</label>
                </div>
                <p style="font-size:0.74rem;color:#b0bac9;margin:-12px 0 16px;padding-left:4px">
                    3+ characters, letters, numbers, underscores only.
                </p>

                <!-- Email -->
                <div class="tw-float-group">
                    <input type="email" id="tw_reg_email" name="tw_reg_email"
                           autocomplete="email" required>
                    <label for="tw_reg_email">Email Address</label>
                </div>

                <!-- Password -->
                <div class="tw-float-group">
                    <input type="password" id="tw_reg_password" name="tw_reg_password"
                           autocomplete="new-password" required minlength="8">
                    <label for="tw_reg_password">Password</label>
                    <button type="button" class="toggle-pw" data-target="tw_reg_password" aria-label="Show/hide password"><i class="fi-rr-eye" aria-hidden="true"></i></button>
                </div>
                <!-- Strength meter -->
                <div class="pw-strength">
                    <div class="pw-strength-bar" id="pw-bar"></div>
                </div>
                <span class="pw-strength-label" id="pw-label"></span>

                <!-- Confirm password -->
                <div class="tw-float-group" style="margin-top:16px">
                    <input type="password" id="tw_reg_confirm" name="tw_reg_confirm"
                           autocomplete="new-password" required>
                    <label for="tw_reg_confirm">Confirm Password</label>
                    <button type="button" class="toggle-pw" data-target="tw_reg_confirm" aria-label="Show/hide password"><i class="fi-rr-eye" aria-hidden="true"></i></button>
                </div>

                <!-- Terms -->
                <div class="auth-check-row">
                    <input type="checkbox" id="tw_terms" name="tw_terms" required>
                    <label for="tw_terms">
                        I agree to the <a href="#" target="_blank">Terms of Service</a> and
                        <a href="#" target="_blank">Privacy Policy</a>. I understand my posts are subject to editorial review.
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn" id="tw-reg-btn">
                    <span id="tw-reg-btn-text">Create Account →</span>
                </button>
            </form>

            <div class="auth-divider">Already a member?</div>
            <div class="auth-footer-text" style="margin-top:0">
                <a href="<?php echo esc_url( home_url('/login/') ); ?>">← Log in to your account</a>
            </div>
        </div>

        <!-- Benefits -->
        <div class="auth-benefits">
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-edit-alt" aria-hidden="true"></i></span> Publish travel guides</div>
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-map" aria-hidden="true"></i></span> Save your trip plans</div>
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-star" aria-hidden="true"></i></span> Build your travel profile</div>
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-bell" aria-hidden="true"></i></span> Get deal alerts</div>
        </div>

    </div>
</div>

<script>
(function () {
    /* Toggle password visibility */
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = document.getElementById(btn.dataset.target);
            var show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            btn.innerHTML = show ? '<i class="fi-rr-eye-crossed" aria-hidden="true"></i>' : '<i class="fi-rr-eye" aria-hidden="true"></i>';
        });
    });

    /* Password strength meter */
    var pwInp   = document.getElementById('tw_reg_password');
    var pwBar   = document.getElementById('pw-bar');
    var pwLabel = document.getElementById('pw-label');

    pwInp.addEventListener('input', function () {
        var pw = this.value;
        var score = 0;
        if (pw.length >= 8)  score++;
        if (pw.length >= 12) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;

        var labels = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
        var colors = ['', '#D5374F', '#1B93B0', '#D83550', '#1B93B0', '#1B93B0'];
        var widths = ['0%', '20%', '40%', '60%', '80%', '100%'];

        pwBar.style.width      = pw.length ? widths[score] || '20%' : '0%';
        pwBar.style.background = pw.length ? colors[score] || '#D5374F' : '';
        pwLabel.textContent    = pw.length ? (labels[score] || 'Weak') : '';
        pwLabel.style.color    = pw.length ? (colors[score] || '#D5374F') : '';
    });

    /* AJAX register */
    var form    = document.getElementById('tw-register-form');
    var btn     = document.getElementById('tw-reg-btn');
    var btnText = document.getElementById('tw-reg-btn-text');
    var msg     = document.getElementById('auth-reg-msg');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.style.display = 'none';

        var fname    = document.getElementById('tw_first_name').value.trim();
        var username = document.getElementById('tw_reg_username').value.trim();
        var email    = document.getElementById('tw_reg_email').value.trim();
        var password = document.getElementById('tw_reg_password').value;
        var confirm  = document.getElementById('tw_reg_confirm').value;
        var terms    = document.getElementById('tw_terms').checked;

        if (!fname)                         { showMsg('Please enter your first name.', 'error'); return; }
        if (username.length < 3)            { showMsg('Username must be at least 3 characters.', 'error'); return; }
        if (!/^[a-zA-Z0-9_\-]+$/.test(username)) { showMsg('Username can only contain letters, numbers, - and _.', 'error'); return; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showMsg('Please enter a valid email address.', 'error'); return; }
        if (password.length < 8)            { showMsg('Password must be at least 8 characters.', 'error'); return; }
        if (password !== confirm)           { showMsg('Passwords do not match.', 'error'); return; }
        if (!terms)                         { showMsg('Please accept the Terms of Service to continue.', 'error'); return; }

        btnText.innerHTML = '<i class="fi-rr-hourglass" aria-hidden="true"></i> Creating account…';
        btn.disabled = true;

        var fd = new FormData(form);
        fd.append('action', 'tw_ajax_register');

        fetch(<?php echo json_encode( admin_url('admin-ajax.php') ); ?>, {
            method: 'POST', body: fd, credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showMsg('<i class="fi-rr-confetti" aria-hidden="true"></i> Account created! Redirecting…', 'success');
                setTimeout(function () {
                    window.location.href = data.data.redirect || <?php echo json_encode( home_url('/') ); ?>;
                }, 900);
            } else {
                showMsg(data.data || 'Registration failed. Please try again.', 'error');
                btnText.textContent = 'Create Account →';
                btn.disabled = false;
            }
        })
        .catch(function () {
            showMsg('Network error. Please try again.', 'error');
            btnText.textContent = 'Create Account →';
            btn.disabled = false;
        });
    });

    function showMsg(text, type) {
        msg.textContent   = text;
        msg.className     = 'auth-msg ' + type;
        msg.style.display = 'block';
        msg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
})();
</script>

<?php get_footer(); ?>
