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

    var compareStorageKey = 'twPackageCompareIds';
    var compareMax = window.twPackageCompare && twPackageCompare.maxItems ? parseInt(twPackageCompare.maxItems, 10) : 3;
    var compareMin = window.twPackageCompare && twPackageCompare.minItems ? parseInt(twPackageCompare.minItems, 10) : 2;
    var compareArchiveUrl = window.twPackageCompare && twPackageCompare.archiveUrl ? twPackageCompare.archiveUrl : '/';
    var $compareTray = $('[data-package-compare-tray]');
    var $compareCount = $('[data-package-compare-count]');

    function getCompareIds() {
        try {
            var stored = JSON.parse(window.localStorage.getItem(compareStorageKey) || '[]');
            return $.grep(stored, function(id, index) {
                return id && $.inArray(id, stored) === index;
            }).slice(0, compareMax);
        } catch (error) {
            return [];
        }
    }

    function setCompareIds(ids) {
        window.localStorage.setItem(compareStorageKey, JSON.stringify(ids.slice(0, compareMax)));
    }

    function updateCompareUi() {
        var ids = getCompareIds();
        $('[data-package-compare-id]').each(function() {
            var $input = $(this);
            var id = String($input.data('package-compare-id'));
            $input.prop('checked', $.inArray(id, ids) !== -1);
        });

        if (!$compareTray.length) {
            return;
        }

        if (ids.length) {
            $compareTray.prop('hidden', false);
        } else {
            $compareTray.prop('hidden', true);
        }

        if (ids.length < compareMin) {
            $compareCount.text(ids.length + ' selected. Select ' + compareMin + '-' + compareMax + ' packages.');
        } else {
            $compareCount.text(ids.length + ' selected. Ready to compare.');
        }
    }

    $(document).on('change', '[data-package-compare-id]', function() {
        var $input = $(this);
        var id = String($input.data('package-compare-id'));
        var ids = getCompareIds();
        var existingIndex = $.inArray(id, ids);

        if ($input.is(':checked')) {
            if (existingIndex === -1) {
                if (ids.length >= compareMax) {
                    $input.prop('checked', false);
                    window.alert('You can compare up to ' + compareMax + ' packages at a time.');
                    return;
                }
                ids.push(id);
            }
        } else if (existingIndex !== -1) {
            ids.splice(existingIndex, 1);
        }

        setCompareIds(ids);
        updateCompareUi();
    });

    $(document).on('click', '[data-package-compare-clear]', function() {
        setCompareIds([]);
        updateCompareUi();
        window.location.href = compareArchiveUrl;
    });

    $(document).on('click', '[data-package-compare-open]', function() {
        var ids = getCompareIds();

        if (ids.length < compareMin) {
            window.alert('Please select at least ' + compareMin + ' packages to compare.');
            return;
        }

        var separator = compareArchiveUrl.indexOf('?') === -1 ? '?' : '&';
        window.location.href = compareArchiveUrl + separator + 'compare_packages=' + ids.join(',') + '#package-comparison';
    });

    updateCompareUi();
});
