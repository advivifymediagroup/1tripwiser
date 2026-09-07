<?php
/**
 * Template Name: Travel Agency Registration
 *
 * Public form for travel agencies to register their contact details
 * and business information. Submissions are stored as tw_agency posts
 * and viewable under "Travel Agencies" in the WP admin.
 *
 * @package my-theme
 */
get_header();

$fields = function_exists( 'tw_agency_fields' ) ? tw_agency_fields() : array();
$status = isset( $_GET['agency'] ) ? sanitize_key( $_GET['agency'] ) : '';
?>

<main class="main-content tw-agency-page">

    <section class="tw-agency-hero">
        <div class="tw-agency-hero-overlay" aria-hidden="true"></div>
        <div class="container tw-agency-hero-inner">
            <span class="tw-agency-hero-kicker">PARTNER WITH US</span>
            <h1 class="tw-h1 tw-agency-hero-title">Travel Agency <span>Registration</span></h1>
            <p class="tw-agency-hero-sub">Join the 1TRIPWISER partner network. Share your details below and our team will get in touch within 48 hours.</p>
            <div class="tw-agency-hero-perks">
                <span><i class="fi-rr-check" aria-hidden="true"></i> Quality leads</span>
                <span><i class="fi-rr-check" aria-hidden="true"></i> Verified profile listing</span>
                <span><i class="fi-rr-check" aria-hidden="true"></i> Marketing support</span>
            </div>
        </div>
    </section>

    <div class="container tw-agency-wrap">

        <?php if ( $status === 'success' ) : ?>
            <div class="tw-agency-notice success">
                <strong><i class="fi-rr-confetti" aria-hidden="true"></i> Thank you for registering!</strong>
                <p>Your details have been received. Our partnerships team will reach out within 48 hours.</p>
            </div>
        <?php elseif ( $status === 'missing' ) : ?>
            <div class="tw-agency-notice error">
                <strong>Please fill all required fields.</strong>
                <p>Fields marked with * are required. Scroll down and complete them to continue.</p>
            </div>
        <?php elseif ( $status === 'error' ) : ?>
            <div class="tw-agency-notice error">
                <strong>Something went wrong.</strong>
                <p>Please try again or email us directly at <?php echo esc_html( get_option( 'admin_email' ) ); ?>.</p>
            </div>
        <?php endif; ?>

        <form class="tw-agency-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" autocomplete="on">
            <input type="hidden" name="action" value="tw_agency_register">
            <?php wp_nonce_field( 'tw_agency_register', 'tw_agency_nonce' ); ?>

            <div class="tw-agency-form-grid">
                <?php foreach ( $fields as $key => $f ) :
                    list( $label, $type, $required ) = $f;
                    $placeholder = isset( $f[3] ) ? $f[3] : '';
                    $is_full = ( $type === 'textarea' ) || in_array( $key, array( 'specialisation', 'destinations', 'notes' ), true );
                ?>
                <div class="tw-agency-field<?php echo $is_full ? ' tw-agency-field--full' : ''; ?>">
                    <label for="ag-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
                    <?php if ( $type === 'textarea' ) : ?>
                        <textarea id="ag-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"
                                  rows="3" placeholder="<?php echo esc_attr( $placeholder ); ?>"
                                  <?php echo $required ? 'required' : ''; ?>></textarea>
                    <?php else : ?>
                        <input type="<?php echo esc_attr( $type ); ?>"
                               id="ag-<?php echo esc_attr( $key ); ?>"
                               name="<?php echo esc_attr( $key ); ?>"
                               placeholder="<?php echo esc_attr( $placeholder ); ?>"
                               <?php echo $required ? 'required' : ''; ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="tw-agency-consent">
                <label>
                    <input type="checkbox" required>
                    I agree to be contacted by 1TRIPWISER regarding partnership opportunities.
                </label>
            </div>

            <button type="submit" class="tw-agency-submit">Submit Registration</button>
        </form>

    </div>
</main>

<?php get_footer(); ?>
