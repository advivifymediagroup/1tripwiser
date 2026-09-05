<?php
/**
 * Reusable "Book Now" / "Enquire" form — shared by package, itinerary and
 * destination single pages. Submits to mytheme_handle_enquiry_submission(),
 * which stores rows in the dedicated tw_enquiries table (never trip_inquiry)
 * and emails 1tripwiser@gmail.com.
 *
 * Expected $args: type ('package'|'itinerary'|'destination'), ref_id,
 * anchor (section id), kicker, heading, intro, submit_label.
 *
 * @package my-theme
 */

$tw_enq_type    = isset( $args['type'] ) ? $args['type'] : 'package';
$tw_enq_ref_id  = isset( $args['ref_id'] ) ? absint( $args['ref_id'] ) : 0;
$tw_enq_anchor  = isset( $args['anchor'] ) ? sanitize_key( $args['anchor'] ) : 'enquiry';
$tw_enq_kicker  = isset( $args['kicker'] ) ? $args['kicker'] : __( 'Interested?', 'mytheme' );
$tw_enq_heading = isset( $args['heading'] ) ? $args['heading'] : __( 'Get a callback', 'mytheme' );
$tw_enq_intro   = isset( $args['intro'] ) ? $args['intro'] : __( 'Share your details and our travel expert will get in touch.', 'mytheme' );
$tw_enq_submit  = isset( $args['submit_label'] ) ? $args['submit_label'] : __( 'Send Enquiry', 'mytheme' );
?>
<section class="tw-package-enquiry dest-article-section" id="<?php echo esc_attr( $tw_enq_anchor ); ?>">
    <div class="tw-package-enquiry-copy">
        <span><?php echo esc_html( $tw_enq_kicker ); ?></span>
        <h2><?php echo esc_html( $tw_enq_heading ); ?></h2>
        <p><?php echo esc_html( $tw_enq_intro ); ?></p>
    </div>
    <?php if ( isset( $_GET['enquiry'] ) && $_GET['enquiry'] === 'success' ) : ?>
        <div class="tw-form-notice success"><?php esc_html_e( 'Thanks. Your enquiry has been received.', 'mytheme' ); ?></div>
    <?php elseif ( isset( $_GET['enquiry'] ) && $_GET['enquiry'] === 'error' ) : ?>
        <div class="tw-form-notice error"><?php esc_html_e( 'Please fill your name and phone number.', 'mytheme' ); ?></div>
    <?php endif; ?>
    <form class="tw-package-enquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
        <input type="hidden" name="action" value="mytheme_enquiry">
        <input type="hidden" name="enquiry_type" value="<?php echo esc_attr( $tw_enq_type ); ?>">
        <input type="hidden" name="ref_id" value="<?php echo esc_attr( $tw_enq_ref_id ); ?>">
        <input type="hidden" name="enquiry_anchor" value="<?php echo esc_attr( $tw_enq_anchor ); ?>">
        <?php wp_nonce_field( 'mytheme_enquiry', 'mytheme_enquiry_nonce' ); ?>
        <div class="tw-form-grid">
            <label><span><?php esc_html_e( 'Name *', 'mytheme' ); ?></span><input type="text" name="name" required></label>
            <label><span><?php esc_html_e( 'Phone *', 'mytheme' ); ?></span><input type="tel" name="phone" required></label>
            <label><span><?php esc_html_e( 'Email', 'mytheme' ); ?></span><input type="email" name="email"></label>
            <label><span><?php esc_html_e( 'Preferred Travel Date', 'mytheme' ); ?></span><input type="date" name="date"></label>
            <label><span><?php esc_html_e( 'Adults', 'mytheme' ); ?></span><input type="number" name="adults" min="1" value="1"></label>
            <label><span><?php esc_html_e( 'Budget', 'mytheme' ); ?></span>
                <select name="budget">
                    <option value=""><?php esc_html_e( 'Select budget range', 'mytheme' ); ?></option>
                    <option><?php esc_html_e( 'Budget - Under Rs. 25,000', 'mytheme' ); ?></option>
                    <option><?php esc_html_e( 'Mid-range - Rs. 25K–Rs. 60K', 'mytheme' ); ?></option>
                    <option><?php esc_html_e( 'Premium - Rs. 60K–Rs. 1.5L', 'mytheme' ); ?></option>
                    <option><?php esc_html_e( 'Luxury - Above Rs. 1.5L', 'mytheme' ); ?></option>
                </select>
            </label>
            <label class="tw-form-full"><span><?php esc_html_e( 'Message', 'mytheme' ); ?></span>
                <textarea name="message" rows="4" placeholder="<?php esc_attr_e( 'Tell us your travel dates, group size, or custom requests.', 'mytheme' ); ?>"></textarea>
            </label>
        </div>
        <button type="submit"><?php echo esc_html( $tw_enq_submit ); ?></button>
    </form>
</section>
