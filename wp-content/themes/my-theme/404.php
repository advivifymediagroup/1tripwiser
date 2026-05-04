<?php get_header(); ?>

<main class="main-content">
    <div class="container">
        <section class="error-404">
            <h1>404 - Page Not Found</h1>
            <p>Sorry, the page you are looking for does not exist.</p>
            <p>It might have been moved, deleted, or you entered the wrong URL.</p>
            <div class="error-actions">
                <a href="<?php echo home_url(); ?>" class="btn-primary">Go to Homepage</a>
                <a href="javascript:history.back()" class="btn-secondary">Go Back</a>
            </div>
            <div class="search-form-404">
                <?php get_search_form(); ?>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>