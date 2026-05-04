jQuery(document).ready(function($) {
    // Mobile menu toggle
    $('.mobile-menu-toggle').click(function() {
        $('.main-nav').toggleClass('active');
        $(this).toggleClass('active');
    });

    // Close mobile menu when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('.main-nav, .mobile-menu-toggle').length) {
            $('.main-nav').removeClass('active');
            $('.mobile-menu-toggle').removeClass('active');
        }
    });
});