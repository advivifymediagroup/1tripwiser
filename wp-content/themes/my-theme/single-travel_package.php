<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <?php mytheme_breadcrumbs(); ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $package = mytheme_get_package_data();
            $package_image = mytheme_get_image_url($package['image'], 'large');
            $book_url = $package['book_url'] ? $package['book_url'] : mytheme_get_plan_trip_url();
            ?>
            <article class="single-post travel-single">
                <header class="post-header travel-single-header">
                    <span><?php echo $package['tag'] ? esc_html($package['tag']) : 'Travel package'; ?></span>
                    <h1><?php the_title(); ?></h1>
                    <div class="travel-meta header-meta">
                        <?php if ($package['location']) : ?><span><?php echo esc_html($package['location']); ?></span><?php endif; ?>
                        <?php if ($package['duration']) : ?><span><?php echo esc_html($package['duration']); ?></span><?php endif; ?>
                        <?php if ($package['trip_type']) : ?><span><?php echo esc_html($package['trip_type']); ?></span><?php endif; ?>
                    </div>
                </header>

                <?php if ($package_image || has_post_thumbnail()) : ?>
                    <div class="post-thumbnail travel-hero-image">
                        <?php if ($package_image) : ?>
                            <img src="<?php echo esc_url($package_image); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="travel-detail-grid">
                    <?php if ($package['location']) : ?><div class="travel-detail"><span>Location</span><strong><?php echo esc_html($package['location']); ?></strong></div><?php endif; ?>
                    <?php if ($package['duration']) : ?><div class="travel-detail"><span>Duration</span><strong><?php echo esc_html($package['duration']); ?></strong></div><?php endif; ?>
                    <?php if ($package['trip_type']) : ?><div class="travel-detail"><span>Trip Type</span><strong><?php echo esc_html($package['trip_type']); ?></strong></div><?php endif; ?>
                    <?php if ($package['amount']) : ?><div class="travel-detail"><span>Amount</span><strong><?php echo esc_html($package['amount']); ?></strong></div><?php endif; ?>
                    <?php if ($package['emi']) : ?><div class="travel-detail"><span>EMI Option</span><strong><?php echo esc_html($package['emi']); ?></strong></div><?php endif; ?>
                </div>

                <div class="post-content">
                    <?php
                    if ($package['overview']) {
                        echo wp_kses_post($package['overview']);
                    }
                    ?>
                </div>

                <section class="tw-package-enquiry" id="package-enquiry">
                    <div class="tw-package-enquiry-copy">
                        <span>Interested in this package?</span>
                        <h2>Get a callback for <?php the_title(); ?></h2>
                        <p>Share your basic details and our travel expert will help with dates, pricing, inclusions, and customisation.</p>
                    </div>

                    <?php if (isset($_GET['package_enquiry']) && $_GET['package_enquiry'] === 'success') : ?>
                        <div class="tw-form-notice success">Thanks. Your enquiry has been received.</div>
                    <?php elseif (isset($_GET['package_enquiry']) && $_GET['package_enquiry'] === 'error') : ?>
                        <div class="tw-form-notice error">Please fill your name and phone number.</div>
                    <?php endif; ?>

                    <form class="tw-package-enquiry-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                        <input type="hidden" name="action" value="mytheme_package_inquiry">
                        <input type="hidden" name="package_id" value="<?php echo esc_attr(get_the_ID()); ?>">
                        <?php wp_nonce_field('mytheme_package_inquiry', 'mytheme_package_inquiry_nonce'); ?>

                        <div class="tw-form-grid">
                            <label>
                                <span>Name *</span>
                                <input type="text" name="name" required>
                            </label>
                            <label>
                                <span>Phone *</span>
                                <input type="tel" name="phone" required>
                            </label>
                            <label>
                                <span>Email</span>
                                <input type="email" name="email">
                            </label>
                            <label>
                                <span>Preferred Travel Date</span>
                                <input type="date" name="date">
                            </label>
                            <label>
                                <span>Adults</span>
                                <input type="number" name="adults" min="1" value="1">
                            </label>
                            <label>
                                <span>Budget</span>
                                <select name="budget">
                                    <option value="">Select budget range</option>
                                    <option value="Budget - Under Rs. 25,000">Budget - Under Rs. 25,000</option>
                                    <option value="Mid-range - Rs. 25K-Rs. 60K">Mid-range - Rs. 25K-Rs. 60K</option>
                                    <option value="Premium - Rs. 60K-Rs. 1.5L">Premium - Rs. 60K-Rs. 1.5L</option>
                                    <option value="Luxury - Above Rs. 1.5L">Luxury - Above Rs. 1.5L</option>
                                </select>
                            </label>
                            <label class="tw-form-full">
                                <span>Message</span>
                                <textarea name="message" rows="4" placeholder="Tell us your travel dates, group size, or custom requests."></textarea>
                            </label>
                        </div>

                        <button type="submit">Send Enquiry</button>
                    </form>
                </section>

                <?php mytheme_render_faq_section(get_the_ID(), 'Package FAQs'); ?>

                <footer class="post-footer travel-cta">
                    <a href="#package-enquiry" class="btn-primary">Enquire Now</a>
                    <!-- <a href="<?php echo esc_url(mytheme_get_plan_trip_url()); ?>" class="btn-secondary">Customize Trip</a> -->
                    <a href="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>" class="btn-secondary">All Packages</a>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
