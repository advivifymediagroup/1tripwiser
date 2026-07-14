/**
 * TripWiser Global Animations
 * - Scroll-triggered reveal (data-reveal attribute + IntersectionObserver)
 * - Auto-stagger for card grids on the homepage
 * - Number scramble effect for stat counters
 * - 3D card tilt on hover (mouse parallax)
 * - Hero parallax background scroll
 * - Button ripple effect
 * - Active nav link
 * - Nav scroll shrink
 * - User dropdown
 * - Floating labels on auth inputs
 * - Page loader
 */
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════════
       1. PAGE LOADER
    ═══════════════════════════════════════════════════════════════ */
    var loader = document.getElementById('tw-page-loader');
    if (loader) {
        window.addEventListener('load', function () {
            loader.classList.add('tw-loader-done');
            setTimeout(function () { loader.remove(); }, 600);
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       3. SCROLL REVEAL — [data-reveal] attribute system
    ═══════════════════════════════════════════════════════════════ */

    /* ── Number scramble for stat counters ── */
    function scramble(el, duration) {
        var original = el.textContent.trim();
        var digits   = '0123456789';
        var start    = Date.now();
        var timer    = setInterval(function () {
            var progress = Math.min((Date.now() - start) / duration, 1);
            if (progress >= 1) {
                el.textContent = original;
                clearInterval(timer);
                return;
            }
            /* Digits scramble fast early; lock to real digits past 75% */
            el.textContent = original.split('').map(function (c) {
                return /\d/.test(c) && progress < 0.75
                    ? digits[Math.floor(Math.random() * 10)]
                    : c;
            }).join('');
        }, 50);
    }

    /* ── Auto-stagger card grids (applied before observing) ── */
    var AUTO_GRIDS = [
        '.posts-grid',
        '.tw-blog-grid',
        '.tw-community-stats',
        '.visa-services-grid',
    ];
    AUTO_GRIDS.forEach(function (sel) {
        document.querySelectorAll(sel).forEach(function (grid) {
            var eligible = Array.from(grid.children).filter(function (c) {
                return !c.classList.contains('tw-stat-divider')
                    && !c.classList.contains('no-posts')
                    && !c.hasAttribute('data-reveal');
            });
            eligible.forEach(function (child, i) {
                child.setAttribute('data-reveal', 'scale');
                child.style.setProperty('--rd', (i * 95) + 'ms');
            });
        });
    });

    /* ── Auto-stagger stats bar ── */
    document.querySelectorAll('.tw-stats-bar .tw-stat:not([data-reveal])').forEach(function (el, i) {
        el.setAttribute('data-reveal', 'up');
        el.style.setProperty('--rd', (i * 80) + 'ms');
    });

    /* ── IntersectionObserver for [data-reveal] ── */
    if ('IntersectionObserver' in window) {

        var revObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('tw-in');
                revObs.unobserve(entry.target);

                /* Scramble any numeric text inside this element */
                entry.target.querySelectorAll('.tw-stat-num, strong, [data-scramble]').forEach(function (n) {
                    if (/\d/.test(n.textContent)) scramble(n, 1100);
                });
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        function observeReveal() {
            document.querySelectorAll('[data-reveal]:not(.tw-in)').forEach(function (el) {
                revObs.observe(el);
            });
        }
        /* Initial pass + one deferred pass to catch auto-staggered grids */
        observeReveal();
        setTimeout(observeReveal, 80);

        /* ── Legacy .tw-reveal support (blog page, auth pages, etc.) ── */
        var LEGACY_SEL = [
            '.ba-card', '.ba-featured', '.ba-aff-banner', '.ba-aff-chip',
            '.ba-table-wrap', '.ba-filter-pill',
            '.sp-content', '.sp-author', '.sp-nav-link', '.sp-share', '.sp-comments',
            '.sb-guidelines',
            '.auth-card', '.auth-benefits'
        ].join(',');

        var legObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('tw-revealed');
                    legObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });

        var staggerGroups = {};
        document.querySelectorAll(LEGACY_SEL).forEach(function (el) {
            var key = el.parentElement ? el.parentElement.className : 'r';
            if (!staggerGroups[key]) staggerGroups[key] = 0;
            el.style.transitionDelay = (staggerGroups[key]++ * 0.07) + 's';
            el.classList.add('tw-reveal');
            legObs.observe(el);
        });

    } else {
        /* Fallback: show immediately */
        document.querySelectorAll('[data-reveal]').forEach(function (el) { el.classList.add('tw-in'); });
        document.querySelectorAll('.tw-reveal').forEach(function (el) { el.classList.add('tw-revealed'); });
    }

    /* ═══════════════════════════════════════════════════════════════
       4. 3D CARD TILT (mouse parallax on cards)
    ═══════════════════════════════════════════════════════════════ */
    var TILT_SELECTORS = '.ba-card, .ba-aff-chip, .tw-package-card';

    function initTilt() {
        document.querySelectorAll(TILT_SELECTORS).forEach(function (card) {
            card.style.willChange    = 'transform';
            card.style.transition    = 'transform 0.12s ease, box-shadow 0.2s ease';

            card.addEventListener('mousemove', function (e) {
                var rect  = card.getBoundingClientRect();
                var cx    = rect.left + rect.width  / 2;
                var cy    = rect.top  + rect.height / 2;
                var dx    = (e.clientX - cx) / (rect.width  / 2);
                var dy    = (e.clientY - cy) / (rect.height / 2);
                var rotX  = -dy * 7;
                var rotY  =  dx * 7;
                card.style.transform =
                    'perspective(900px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg) translateY(-4px) scale(1.015)';
                card.style.boxShadow = '0 20px 56px rgba(0,0,0,0.18)';
            });

            card.addEventListener('mouseleave', function () {
                card.style.transform  = '';
                card.style.boxShadow  = '';
                card.style.transition = 'transform 0.35s ease, box-shadow 0.35s ease';
            });

            card.addEventListener('mouseenter', function () {
                card.style.transition = 'transform 0.12s ease, box-shadow 0.12s ease';
            });
        });
    }

    initTilt();
    setTimeout(initTilt, 1500);

    /* ═══════════════════════════════════════════════════════════════
       5. HERO PARALLAX — background scroll (desktop only)
       Disabled on mobile: scale(1.05) causes overflow on narrow viewports
       and parallax scroll feels jarring on touch devices.
    ═══════════════════════════════════════════════════════════════ */
    function updateParallax() {
        var sy = window.scrollY;
        /* CSS pre-expands .tw-hero-bg with inset:-8% so no scale() needed here */
        document.querySelectorAll('.tw-hero-bg, .fp-hero-bg').forEach(function (bg) {
            bg.style.transform = 'translateY(' + Math.round(sy * 0.35) + 'px)';
        });
        document.querySelectorAll('.sp-hero-bg').forEach(function (bg) {
            bg.style.transform = 'translateY(' + Math.round(sy * 0.35) + 'px)';
        });
    }

    /* Run parallax on desktop pointer devices only (no jarring parallax on touch) */
    if (window.matchMedia('(min-width: 769px) and (hover: hover)').matches) {
        updateParallax(); /* set initial position immediately */

        var ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () { updateParallax(); ticking = false; });
                ticking = true;
            }
        }, { passive: true });
    }

    /* ═══════════════════════════════════════════════════════════════
       6. BUTTON RIPPLE EFFECT
    ═══════════════════════════════════════════════════════════════ */
    var RIPPLE_SEL = [
        '.tw-cta', '.ba-write-btn', '.sb-submit-btn', '.ba-table-book-btn',
        '.tw-auth-btn', '.auth-submit-btn', '.ba-filter-pill',
        '.tw-hero-btn', '.tw-community-btn', '.free-itinerary-link'
    ].join(',');

    document.addEventListener('click', function (e) {
        var btn = e.target.closest(RIPPLE_SEL);
        if (!btn) return;

        var rect   = btn.getBoundingClientRect();
        var ripple = document.createElement('span');
        var size   = Math.max(rect.width, rect.height) * 2;
        ripple.style.cssText =
            'position:absolute;border-radius:50%;background:rgba(255,255,255,0.35);' +
            'width:' + size + 'px;height:' + size + 'px;' +
            'left:' + (e.clientX - rect.left - size / 2) + 'px;' +
            'top:'  + (e.clientY - rect.top  - size / 2) + 'px;' +
            'pointer-events:none;transform:scale(0);animation:tw-ripple-anim 0.55s ease-out forwards;';

        var pos = getComputedStyle(btn).position;
        if (pos === 'static') btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
        btn.appendChild(ripple);
        setTimeout(function () { if (ripple.parentNode) ripple.remove(); }, 600);
    });

    /* ═══════════════════════════════════════════════════════════════
       7. ACTIVE NAV LINK
    ═══════════════════════════════════════════════════════════════ */
    var currentPath = window.location.pathname;
    document.querySelectorAll('.tw-nav-link, .tw-mobile-list a').forEach(function (link) {
        try {
            var linkPath = new URL(link.href).pathname;
            if (linkPath !== '/' && currentPath.indexOf(linkPath) === 0) {
                link.classList.add('tw-active');
            } else if (linkPath === '/' && currentPath === '/') {
                link.classList.add('tw-active');
            }
        } catch(e) {}
    });

    /* ═══════════════════════════════════════════════════════════════
       8. NAV SCROLL SHRINK
    ═══════════════════════════════════════════════════════════════ */
    var nav = document.getElementById('tw-nav');
    if (nav) {
        window.addEventListener('scroll', function () {
            nav.classList.toggle('tw-nav-scrolled', window.scrollY > 40);
        }, { passive: true });
    }

    /* ═══════════════════════════════════════════════════════════════
       9. USER DROPDOWN
    ═══════════════════════════════════════════════════════════════ */
    var userTrigger = document.querySelector('.tw-user-trigger');
    var userMenu    = document.querySelector('.tw-user-menu');
    if (userTrigger && userMenu) {
        userTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = userMenu.classList.toggle('open');
            userTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        userMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function (e) { e.stopPropagation(); });
        });

        document.addEventListener('click', function () {
            userMenu.classList.remove('open');
            if (userTrigger) userTrigger.setAttribute('aria-expanded', 'false');
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       10. FLOATING LABEL on auth inputs
    ═══════════════════════════════════════════════════════════════ */
    document.querySelectorAll('.tw-float-group input').forEach(function (inp) {
        function check() {
            inp.closest('.tw-float-group').classList.toggle('has-value', inp.value !== '');
        }
        inp.addEventListener('input', check);
        check();
    });

})();
