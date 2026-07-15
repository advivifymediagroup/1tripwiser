<?php
/**
 * Template Name: Login
 */

// Already logged in → redirect home
if ( is_user_logged_in() ) {
    wp_safe_redirect( home_url('/') );
    exit;
}

get_header();

$redirect = isset( $_GET['redirect_to'] ) ? esc_url( $_GET['redirect_to'] ) : home_url('/');
$error    = '';
if ( isset( $_GET['login'] ) && $_GET['login'] === 'failed' ) {
    $error = 'Invalid username or password. Please try again.';
}
?>

<div class="auth-page">

    <!-- Hero -->
    <section class="auth-hero">
        <div class="auth-hero-kicker"><i class="fi-rr-plane" aria-hidden="true"></i> Welcome Back</div>
        <h1>Log In to <span>1TripWiser</span></h1>
        <p>Your travel dashboard, saved trips, and community posts are waiting.</p>
    </section>

    <div class="auth-wrap">

        <!-- Error from failed WP login redirect -->
        <?php if ( $error ) : ?>
        <div class="auth-msg error" style="display:block"><?php echo esc_html( $error ); ?></div>
        <?php endif; ?>

        <!-- AJAX message -->
        <div class="auth-msg" id="auth-login-msg"></div>

        <div class="auth-card">

            <form id="tw-login-form" novalidate>
                <?php wp_nonce_field( 'tw_login_nonce', 'tw_login_nonce' ); ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>">

                <!-- Username / email -->
                <div class="tw-float-group">
                    <input type="text" id="tw_username" name="tw_username" autocomplete="username" required>
                    <label for="tw_username">Username or Email</label>
                </div>

                <!-- Password -->
                <div class="tw-float-group">
                    <input type="password" id="tw_password" name="tw_password" autocomplete="current-password" required>
                    <label for="tw_password">Password</label>
                    <button type="button" class="toggle-pw" aria-label="Show/hide password" data-target="tw_password"><i class="fi-rr-eye" aria-hidden="true"></i></button>
                </div>

                <!-- Forgot + Remember row -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;margin-top:-8px">
                    <div class="auth-check-row" style="margin:0">
                        <input type="checkbox" id="tw_remember" name="tw_remember" value="1">
                        <label for="tw_remember" style="color:#6b7a8f;font-size:0.83rem;cursor:pointer">Remember me</label>
                    </div>
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="font-size:0.8rem;font-weight:700;color:#6b7a8f;text-decoration:none">Forgot password?</a>
                </div>

                <button type="submit" class="auth-submit-btn" id="tw-login-btn">
                    <span id="tw-login-btn-text">Log In →</span>
                </button>
            </form>

            <div class="auth-divider">or</div>

            <div class="auth-footer-text" style="margin-top:0">
                Don't have an account? <a href="<?php echo esc_url( home_url('/register/') ); ?>">Sign up free →</a>
            </div>
        </div>

        <!-- Benefits -->
        <div class="auth-benefits">
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-edit-alt" aria-hidden="true"></i></span> Publish travel guides</div>
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-map" aria-hidden="true"></i></span> Save your trip plans</div>
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-comment" aria-hidden="true"></i></span> Join the community</div>
            <div class="auth-benefit"><span class="icon"><i class="fi-rr-bell" aria-hidden="true"></i></span> Get travel deal alerts</div>
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

    /* AJAX login */
    var form    = document.getElementById('tw-login-form');
    var btn     = document.getElementById('tw-login-btn');
    var btnText = document.getElementById('tw-login-btn-text');
    var msg     = document.getElementById('auth-login-msg');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.style.display = 'none';

        var user = document.getElementById('tw_username').value.trim();
        var pass = document.getElementById('tw_password').value;

        if (!user || !pass) {
            showMsg('Please fill in all fields.', 'error');
            return;
        }

        btnText.innerHTML = '<i class="fi-rr-hourglass" aria-hidden="true"></i> Logging in…';
        btn.disabled = true;

        var fd = new FormData(form);
        fd.append('action', 'tw_ajax_login');

        fetch(<?php echo json_encode( admin_url('admin-ajax.php') ); ?>, {
            method: 'POST', body: fd, credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showMsg('<i class="fi-rr-check-circle" aria-hidden="true"></i> Logged in! Redirecting…', 'success');
                setTimeout(function () {
                    window.location.href = data.data.redirect || <?php echo json_encode( home_url('/') ); ?>;
                }, 700);
            } else {
                showMsg(data.data || 'Login failed. Please check your credentials.', 'error');
                btnText.textContent = 'Log In →';
                btn.disabled = false;
            }
        })
        .catch(function () {
            showMsg('Network error. Please try again.', 'error');
            btnText.textContent = 'Log In →';
            btn.disabled = false;
        });
    });

    function showMsg(text, type) {
        msg.textContent   = text;
        msg.className     = 'auth-msg ' + type;
        msg.style.display = 'block';
    }
})();
</script>

<?php get_footer(); ?>
