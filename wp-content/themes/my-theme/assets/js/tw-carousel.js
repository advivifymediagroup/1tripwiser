/**
 * tw-carousel.js — Auto image carousel for 1TRIPWISER
 * Finds 2+ images inside any designated content area and converts them
 * into a sliding carousel with autoplay, prev/next buttons and dot navigation.
 * No external dependencies.
 */
(function () {
    'use strict';

    var AUTOPLAY_MS = 4000;
    // Selectors that may contain multiple post images
    var CONTENT_SELECTORS = [
        '.ev-single-content',
        '.post-content',
        '.tw-package-content',
        '.entry-content',
    ];

    function buildCarousel(imgs) {
        var carousel  = el('div', 'tw-img-carousel');
        var track     = el('div', 'tw-img-carousel-track');
        var dotsWrap  = el('div', 'tw-img-carousel-dots');
        var prevBtn   = el('button', 'tw-img-carousel-btn tw-img-carousel-btn--prev');
        var nextBtn   = el('button', 'tw-img-carousel-btn tw-img-carousel-btn--next');
        var countBadge = el('span', 'tw-img-carousel-count');

        prevBtn.innerHTML = '&#8249;';
        nextBtn.innerHTML = '&#8250;';
        prevBtn.setAttribute('aria-label', 'Previous');
        nextBtn.setAttribute('aria-label', 'Next');

        var slides = [];
        var dots   = [];
        var current = 0;
        var timer;

        imgs.forEach(function (img, i) {
            var slide = el('div', 'tw-img-carousel-slide');
            var clone = img.cloneNode(true);
            clone.removeAttribute('width');
            clone.removeAttribute('height');
            slide.appendChild(clone);
            track.appendChild(slide);
            slides.push(slide);

            var dot = el('button', 'tw-img-carousel-dot' + (i === 0 ? ' active' : ''));
            dot.setAttribute('aria-label', 'Slide ' + (i + 1));
            dot.addEventListener('click', function () { goTo(i); resetTimer(); });
            dotsWrap.appendChild(dot);
            dots.push(dot);
        });

        carousel.appendChild(track);
        carousel.appendChild(prevBtn);
        carousel.appendChild(nextBtn);
        carousel.appendChild(dotsWrap);
        carousel.appendChild(countBadge);

        function goTo(n) {
            current = ((n % slides.length) + slides.length) % slides.length;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            dots.forEach(function (d, i) { d.classList.toggle('active', i === current); });
            countBadge.textContent = (current + 1) + ' / ' + slides.length;
        }

        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(function () { goTo(current + 1); }, AUTOPLAY_MS);
        }

        prevBtn.addEventListener('click', function () { goTo(current - 1); resetTimer(); });
        nextBtn.addEventListener('click', function () { goTo(current + 1); resetTimer(); });

        // Pause on hover
        carousel.addEventListener('mouseenter', function () { clearInterval(timer); });
        carousel.addEventListener('mouseleave', function () { resetTimer(); });

        // Touch/swipe support
        var touchStartX = 0;
        carousel.addEventListener('touchstart', function (e) { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
        carousel.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) { goTo(diff > 0 ? current + 1 : current - 1); resetTimer(); }
        }, { passive: true });

        goTo(0);
        resetTimer();

        return carousel;
    }

    function el(tag, cls) {
        var node = document.createElement(tag);
        node.className = cls;
        return node;
    }

    function collectImages(area) {
        // Gather top-level images — either standalone <img>, or inside <figure> / <a>
        var seen = new Set();
        var imgs = [];

        area.querySelectorAll('img').forEach(function (img) {
            // Skip tiny icons / decorative images (width hint < 80 px)
            if (img.width && img.width < 80) return;
            // Skip images already inside a carousel
            if (img.closest('.tw-img-carousel')) return;
            // Deduplicate by src
            var src = img.getAttribute('src') || '';
            if (!src || seen.has(src)) return;
            seen.add(src);
            imgs.push(img);
        });

        return imgs;
    }

    function removeImageFromDom(img) {
        // Walk up: figure.wp-block-image > figure > a > img — remove the outermost container
        var node = img;
        var parents = ['FIGURE', 'A'];
        while (node.parentElement && parents.indexOf(node.parentElement.tagName) !== -1) {
            node = node.parentElement;
        }
        if (node.parentElement) {
            node.parentElement.removeChild(node);
        }
    }

    function initArea(area) {
        var imgs = collectImages(area);
        if (imgs.length < 2) return;

        // Build carousel from cloned images
        var carousel = buildCarousel(imgs);

        // Remove originals from the DOM
        imgs.forEach(function (img) { removeImageFromDom(img); });

        // Insert carousel at the top of the content area
        area.insertBefore(carousel, area.firstChild);
    }

    function init() {
        CONTENT_SELECTORS.forEach(function (sel) {
            document.querySelectorAll(sel).forEach(function (area) {
                initArea(area);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
