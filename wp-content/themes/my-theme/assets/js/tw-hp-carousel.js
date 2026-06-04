/**
 * tw-hp-carousel.js — Homepage card carousel
 * Transforms designated card grids into sliding carousels.
 * Re-initialises automatically when AJAX filter swaps cards.
 */
(function () {
    'use strict';

    var TARGETS = [
        { sel: '#tw-cards-packages',    perView: [3, 2, 1], gap: 20, auto: 5000 },
        { sel: '#tw-cards-itineraries', perView: [3, 2, 1], gap: 20, auto: 5000 },
        { sel: '.tw-events-grid',       perView: [4, 3, 1], gap: 16, auto: 4000 },
        { sel: '.tw-womens-grid',       perView: [3, 2, 1], gap: 20, auto: 5000 },
    ];

    /* instance registry keyed by grid element */
    var registry = [];  // [{grid, instance}] — avoids Map for broader compat

    function registryGet(grid) {
        for (var i = 0; i < registry.length; i++) {
            if (registry[i].grid === grid) { return registry[i].instance; }
        }
        return null;
    }
    function registrySet(grid, inst) {
        for (var i = 0; i < registry.length; i++) {
            if (registry[i].grid === grid) { registry[i].instance = inst; return; }
        }
        registry.push({ grid: grid, instance: inst });
    }

    /* ── helpers ─────────────────────────────────────────── */
    function getPerView(cfg) {
        var w = window.innerWidth || document.documentElement.clientWidth;
        if (w > 900) { return cfg.perView[0]; }
        if (w > 600) { return cfg.perView[1]; }
        return cfg.perView[2];
    }

    function makeEl(tag, cls) {
        var n = document.createElement(tag);
        if (cls) { n.className = cls; }
        return n;
    }

    /* measure viewport width reliably — force reflow if needed */
    function measureVpWidth(el) {
        var w = el.offsetWidth;
        if (w > 0) { return w; }
        /* force reflow */
        void el.getBoundingClientRect();
        w = el.offsetWidth;
        if (w > 0) { return w; }
        /* fall back to parent chain */
        var p = el.parentElement;
        while (p) {
            w = p.offsetWidth;
            if (w > 0) { return w; }
            p = p.parentElement;
        }
        return document.documentElement.clientWidth || 800;
    }

    /* ── Carousel ────────────────────────────────────────── */
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
        this._pv      = 3;
        this._cardW   = 0;
        this._gap     = cfg.gap;
        this._build();
    }

    Carousel.prototype._build = function () {
        var self = this;
        var grid = this.grid;
        var cfg  = this.cfg;

        /* collect direct-child card elements */
        var cards = [];
        for (var i = 0; i < grid.children.length; i++) {
            if (grid.children[i].nodeType === 1) { cards.push(grid.children[i]); }
        }
        if (cards.length === 0) { return; }
        this.cards = cards;

        /* override grid CSS so flex track works */
        grid.style.cssText += ';display:block!important';
        grid.classList.add('tw-carouseled');

        /* build structure */
        var wrap     = makeEl('div', 'tw-hpc-wrap');
        var viewport = makeEl('div', 'tw-hpc-viewport');
        var track    = makeEl('div', 'tw-hpc-track');
        var prev     = makeEl('button', 'tw-hpc-btn tw-hpc-btn--prev');
        var next     = makeEl('button', 'tw-hpc-btn tw-hpc-btn--next');

        prev.innerHTML = '&#8249;';
        next.innerHTML = '&#8250;';
        prev.setAttribute('aria-label', 'Previous');
        next.setAttribute('aria-label', 'Next');
        prev.setAttribute('type', 'button');
        next.setAttribute('type', 'button');

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

        /* set widths after a rAF so layout is fully computed */
        var me = this;
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                me._setWidths();
                me._startAuto();
            });
        });

        /* click events */
        prev.addEventListener('click', function (e) {
            e.preventDefault();
            self.prev();
            self._resetAuto();
        });
        next.addEventListener('click', function (e) {
            e.preventDefault();
            self.next();
            self._resetAuto();
        });

        /* touch swipe */
        var sx = 0;
        wrap.addEventListener('touchstart', function (e) {
            sx = e.changedTouches[0].clientX;
        }, { passive: true });
        wrap.addEventListener('touchend', function (e) {
            var dx = sx - e.changedTouches[0].clientX;
            if (Math.abs(dx) > 40) {
                dx > 0 ? self.next() : self.prev();
                self._resetAuto();
            }
        }, { passive: true });

        /* pause on hover */
        wrap.addEventListener('mouseenter', function () { clearInterval(self.timer); });
        wrap.addEventListener('mouseleave', function () { self._startAuto(); });
    };

    Carousel.prototype._setWidths = function () {
        var pv   = getPerView(this.cfg);
        var gap  = this.cfg.gap;
        var vpW  = measureVpWidth(this.viewport);
        var cardW = Math.floor((vpW - gap * (pv - 1)) / pv);

        this.cards.forEach(function (c) {
            c.style.flex     = '0 0 ' + cardW + 'px';
            c.style.width    = cardW + 'px';
            c.style.minWidth = cardW + 'px';
            c.style.maxWidth = cardW + 'px';
            c.style.boxSizing = 'border-box';
        });

        this.track.style.gap = gap + 'px';
        this._pv    = pv;
        this._cardW = cardW;
        this._gap   = gap;

        this.goto(Math.min(this.index, Math.max(0, this.cards.length - pv)), true);
    };

    Carousel.prototype.goto = function (n, instant) {
        var pv  = this._pv;
        var max = Math.max(0, this.cards.length - pv);
        this.index = Math.max(0, Math.min(n, max));

        var offset = this.index * (this._cardW + this._gap);

        this.track.style.transition = instant
            ? 'none'
            : 'transform 0.45s cubic-bezier(0.25,0.46,0.45,0.94)';
        this.track.style.transform = 'translateX(-' + offset + 'px)';

        this.prevBtn.style.opacity = this.index <= 0   ? '0.3' : '1';
        this.nextBtn.style.opacity = this.index >= max ? '0.3' : '1';
        /* hide arrows when only 1 card or fits in view */
        var hide = this.cards.length <= pv;
        this.prevBtn.style.visibility = hide ? 'hidden' : 'visible';
        this.nextBtn.style.visibility = hide ? 'hidden' : 'visible';
    };

    Carousel.prototype.next = function () {
        var max = Math.max(0, this.cards.length - this._pv);
        this.goto(this.index >= max ? 0 : this.index + 1);
    };

    Carousel.prototype.prev = function () {
        var max = Math.max(0, this.cards.length - this._pv);
        this.goto(this.index <= 0 ? max : this.index - 1);
    };

    Carousel.prototype._startAuto = function () {
        var self = this;
        var ms   = this.cfg.auto;
        if (!ms || this.cards.length <= this._pv) { return; }
        clearInterval(this.timer);
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
            this.cards.forEach(function (c) {
                c.style.flex = c.style.width = c.style.minWidth = c.style.maxWidth = '';
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
            try {
                if (grid.matches && grid.matches(TARGETS[i].sel)) { cfg = TARGETS[i]; break; }
            } catch (e) { /* noop */ }
        }
        if (!cfg) { return; }

        var existing = registryGet(grid);
        if (existing) { try { existing.destroy(); } catch (e) { /* noop */ } }

        registrySet(grid, new Carousel(grid, cfg));
    }

    function initAll() {
        TARGETS.forEach(function (t) {
            var els = document.querySelectorAll(t.sel);
            for (var i = 0; i < els.length; i++) { initGrid(els[i]); }
        });
    }

    /* ── public API (tw-filters.js calls this after AJAX) ── */
    window.TwHPCarousel = { init: initAll, initGrid: initGrid };

    /* ── resize ──────────────────────────────────────────── */
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            registry.forEach(function (r) {
                try { r.instance._setWidths(); } catch (e) { /* noop */ }
            });
        }, 200);
    });

    /* ── boot ────────────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

}());
