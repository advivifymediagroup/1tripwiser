/**
 * tw-hp-carousel.js — Homepage card carousel
 *
 * Transforms designated card grids into sliding carousels.
 * Works with the AJAX filter (tw-filters.js) — re-inits automatically
 * when a filtered grid is swapped.
 *
 * Shows 3 cards on desktop, 2 on tablet (≤900px), 1 on mobile (≤600px).
 * Auto-plays, pauses on hover, supports touch swipe.
 */
(function () {
    'use strict';

    /* ── config ─────────────────────────────────────────── */
    var TARGETS = [
        { sel: '#tw-cards-packages',    perView: [3, 2, 1], gap: 20, auto: 5000 },
        { sel: '#tw-cards-itineraries', perView: [3, 2, 1], gap: 20, auto: 5000 },
        { sel: '.tw-events-grid',       perView: [4, 3, 1], gap: 16, auto: 4000 },
        { sel: '.tw-womens-grid',       perView: [3, 2, 1], gap: 20, auto: 5000 },
    ];

    /* map from grid element → Carousel instance (for resize / re-init) */
    var registry = typeof Map !== 'undefined' ? new Map() : null;

    /* ── helpers ─────────────────────────────────────────── */
    function pv(cfg) {
        var w = window.innerWidth;
        if (w > 900) return cfg.perView[0];
        if (w > 600) return cfg.perView[1];
        return cfg.perView[2];
    }

    function el(tag, cls) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        return n;
    }

    /* ── Carousel constructor ────────────────────────────── */
    function Carousel(grid, cfg) {
        this.grid     = grid;
        this.cfg      = cfg;
        this.index    = 0;
        this.timer    = null;
        this.cards    = [];
        this.viewport = null;
        this.track    = null;
        this.prevBtn  = null;
        this.nextBtn  = null;
        this._build();
    }

    Carousel.prototype._build = function () {
        var self  = this;
        var grid  = this.grid;
        var cfg   = this.cfg;

        /* collect direct-child card elements */
        this.cards = Array.prototype.slice.call(grid.children).filter(function (c) {
            return c.nodeType === 1;
        });
        if (this.cards.length === 0) { return; }

        /* neutralise any existing grid CSS on the container */
        grid.style.display    = 'block';
        grid.style.gridTemplateColumns = '';
        grid.classList.add('tw-carouseled');

        /* build DOM */
        var wrap     = el('div', 'tw-hpc-wrap');
        var viewport = el('div', 'tw-hpc-viewport');
        var track    = el('div', 'tw-hpc-track');
        var prev     = el('button', 'tw-hpc-btn tw-hpc-btn--prev');
        var next     = el('button', 'tw-hpc-btn tw-hpc-btn--next');

        prev.innerHTML = '&#8249;';
        next.innerHTML = '&#8250;';
        prev.setAttribute('aria-label', 'Previous');
        next.setAttribute('aria-label', 'Next');

        this.cards.forEach(function (c) { track.appendChild(c); });

        viewport.appendChild(track);
        wrap.appendChild(prev);
        wrap.appendChild(viewport);
        wrap.appendChild(next);
        grid.appendChild(wrap);

        this.viewport = viewport;
        this.track    = track;
        this.prevBtn  = prev;
        this.nextBtn  = next;

        /* set initial card widths */
        this._setWidths();

        /* events */
        prev.addEventListener('click', function () { self.prev(); self._resetAuto(); });
        next.addEventListener('click', function () { self.next(); self._resetAuto(); });

        /* swipe */
        var sx = 0;
        wrap.addEventListener('touchstart', function (e) { sx = e.changedTouches[0].clientX; }, { passive: true });
        wrap.addEventListener('touchend', function (e) {
            var d = sx - e.changedTouches[0].clientX;
            if (Math.abs(d) > 40) { d > 0 ? self.next() : self.prev(); self._resetAuto(); }
        }, { passive: true });

        /* pause on hover */
        wrap.addEventListener('mouseenter', function () { clearInterval(self.timer); });
        wrap.addEventListener('mouseleave', function () { self._startAuto(); });

        this.goto(0, true);
        this._startAuto();
    };

    Carousel.prototype._setWidths = function () {
        var perView = pv(this.cfg);
        var gap     = this.cfg.gap;
        var vpW     = this.viewport.offsetWidth || 800;
        var cardW   = (vpW - gap * (perView - 1)) / perView;

        this.cards.forEach(function (c) {
            c.style.flex     = '0 0 ' + cardW + 'px';
            c.style.width    = cardW + 'px';
            c.style.maxWidth = cardW + 'px';
            c.style.boxSizing = 'border-box';
        });

        this.track.style.gap = gap + 'px';
        this._perView = perView;
        this._cardW   = cardW;
        this._gap     = gap;
        this.goto(this.index, true);
    };

    Carousel.prototype.goto = function (n, instant) {
        var max   = Math.max(0, this.cards.length - this._perView);
        this.index = Math.max(0, Math.min(n, max));

        var offset = this.index * (this._cardW + this._gap);

        this.track.style.transition = instant
            ? 'none'
            : 'transform 0.45s cubic-bezier(0.25,0.46,0.45,0.94)';
        this.track.style.transform = 'translateX(-' + offset + 'px)';

        this.prevBtn.style.opacity = this.index <= 0   ? '0.35' : '1';
        this.nextBtn.style.opacity = this.index >= max ? '0.35' : '1';
    };

    Carousel.prototype.next = function () {
        var max = Math.max(0, this.cards.length - this._perView);
        this.goto(this.index >= max ? 0 : this.index + 1);
    };

    Carousel.prototype.prev = function () {
        var max = Math.max(0, this.cards.length - this._perView);
        this.goto(this.index <= 0 ? max : this.index - 1);
    };

    Carousel.prototype._startAuto = function () {
        var self = this;
        var ms   = this.cfg.auto;
        if (!ms || this.cards.length <= this._perView) { return; }
        this.timer = setInterval(function () { self.next(); }, ms);
    };

    Carousel.prototype._resetAuto = function () {
        clearInterval(this.timer);
        this._startAuto();
    };

    Carousel.prototype.destroy = function () {
        clearInterval(this.timer);
        var wrap = this.grid.querySelector('.tw-hpc-wrap');
        if (wrap) {
            var self = this;
            self.cards.forEach(function (c) {
                c.style.flex = c.style.width = c.style.maxWidth = '';
                self.grid.insertBefore(c, wrap);
            });
            wrap.parentNode.removeChild(wrap);
        }
        this.grid.classList.remove('tw-carouseled');
        this.grid.style.display = '';
    };

    /* ── init / re-init ──────────────────────────────────── */
    function initGrid(grid) {
        var cfg = null;
        for (var i = 0; i < TARGETS.length; i++) {
            try { if (grid.matches(TARGETS[i].sel)) { cfg = TARGETS[i]; break; } } catch (e) { /* noop */ }
        }
        if (!cfg) { return; }

        if (registry) {
            if (registry.has(grid)) { registry.get(grid).destroy(); }
            registry.set(grid, new Carousel(grid, cfg));
        } else {
            new Carousel(grid, cfg);
        }
    }

    function initAll() {
        TARGETS.forEach(function (t) {
            document.querySelectorAll(t.sel).forEach(function (g) { initGrid(g); });
        });
    }

    /* ── public API (used by tw-filters.js) ─────────────── */
    window.TwHPCarousel = { init: initAll, initGrid: initGrid };

    /* ── resize handler ──────────────────────────────────── */
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (registry) {
                registry.forEach(function (inst) { inst._setWidths(); });
            }
        }, 200);
    });

    /* ── boot ────────────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

}());
