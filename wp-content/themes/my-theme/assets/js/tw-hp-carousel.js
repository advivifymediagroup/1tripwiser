/**
 * tw-hp-carousel.js — Homepage card carousel
 * Transforms designated card grids into sliding carousels.
 */
(function () {
    'use strict';

    var TARGETS = [
        { sel: '#tw-cards-packages',    perView: [3, 2, 1], gap: 20, auto: 5000 },
        { sel: '#tw-cards-itineraries', perView: [3, 2, 1], gap: 20, auto: 5000 },
        { sel: '.tw-events-grid',       perView: [4, 3, 1], gap: 16, auto: 4000 },
        { sel: '.tw-womens-grid',       perView: [3, 2, 1], gap: 20, auto: 5000 },
    ];

    /* simple registry — no Map needed */
    var registry = [];
    function regGet(grid) {
        for (var i = 0; i < registry.length; i++) {
            if (registry[i][0] === grid) return registry[i][1];
        }
        return null;
    }
    function regSet(grid, inst) {
        for (var i = 0; i < registry.length; i++) {
            if (registry[i][0] === grid) { registry[i][1] = inst; return; }
        }
        registry.push([grid, inst]);
    }

    function pv(cfg) {
        var w = window.innerWidth || document.documentElement.clientWidth || 1024;
        return w > 900 ? cfg.perView[0] : w > 600 ? cfg.perView[1] : cfg.perView[2];
    }

    /* ─── Carousel ───────────────────────────────────────── */
    function Carousel(grid, cfg) {
        this.grid  = grid;
        this.cfg   = cfg;
        this.idx   = 0;
        this.timer = null;
        this.cards = [];
        this.vp    = null;   // viewport
        this.track = null;
        this.prev  = null;
        this.next  = null;
        this._pv   = 1;
        this._cw   = 0;      // card width px
        this._gap  = cfg.gap;
        this._alive = true;
        this._init();
    }

    Carousel.prototype._init = function () {
        var self = this;
        var g    = this.grid;

        /* collect cards */
        var cards = [];
        for (var i = 0; i < g.children.length; i++) {
            if (g.children[i].nodeType === 1) cards.push(g.children[i]);
        }
        if (!cards.length) return;
        this.cards = cards;

        /* force grid into block layout — use setProperty for priority */
        g.style.setProperty('display', 'block', 'important');
        g.classList.add('tw-carouseled');

        /* build DOM */
        var wrap  = mk('div', 'tw-hpc-wrap');
        var vp    = mk('div', 'tw-hpc-viewport');
        var track = mk('div', 'tw-hpc-track');
        var prev  = mkBtn('&#8249;', 'tw-hpc-btn tw-hpc-btn--prev', 'Previous');
        var next  = mkBtn('&#8250;', 'tw-hpc-btn tw-hpc-btn--next', 'Next');

        track.style.cssText = 'display:flex;gap:' + this.cfg.gap + 'px;will-change:transform;';
        vp.style.cssText    = 'flex:1 1 auto;min-width:0;overflow:hidden;';
        wrap.style.cssText  = 'display:flex;align-items:center;gap:8px;width:100%;';

        cards.forEach(function (c) { track.appendChild(c); });
        vp.appendChild(track);
        wrap.appendChild(prev);
        wrap.appendChild(vp);
        wrap.appendChild(next);
        g.appendChild(wrap);

        this.vp    = vp;
        this.track = track;
        this.prev  = prev;
        this.next  = next;

        /* events */
        prev.addEventListener('click', function (e) { e.preventDefault(); self.go(self.idx - 1); self._reset(); });
        next.addEventListener('click', function (e) { e.preventDefault(); self.go(self.idx + 1); self._reset(); });

        /* swipe */
        var sx = 0;
        wrap.addEventListener('touchstart', function (e) { sx = e.changedTouches[0].clientX; }, { passive: true });
        wrap.addEventListener('touchend', function (e) {
            var d = sx - e.changedTouches[0].clientX;
            if (Math.abs(d) > 40) { d > 0 ? self.go(self.idx + 1) : self.go(self.idx - 1); self._reset(); }
        }, { passive: true });

        wrap.addEventListener('mouseenter', function () { clearInterval(self.timer); });
        wrap.addEventListener('mouseleave', function () { self._auto(); });

        /* measure + position after browser has painted */
        requestAnimationFrame(function () {
            if (!self._alive) return;
            self._measure();
            self._auto();
        });
    };

    Carousel.prototype._measure = function () {
        var perView = pv(this.cfg);
        var gap     = this.cfg.gap;

        /* get viewport width — walk up until we get a real value */
        var vpW = 0;
        var el  = this.vp;
        while (el && vpW <= 0) { vpW = el.getBoundingClientRect().width; el = el.parentElement; }
        if (vpW <= 0) vpW = document.documentElement.clientWidth - 120;

        var cardW = Math.floor((vpW - gap * (perView - 1)) / perView);

        /* set card widths using setProperty so they win over any CSS */
        this.cards.forEach(function (c) {
            c.style.setProperty('flex',      '0 0 ' + cardW + 'px', 'important');
            c.style.setProperty('width',     cardW + 'px',           'important');
            c.style.setProperty('min-width', cardW + 'px',           'important');
            c.style.setProperty('max-width', cardW + 'px',           'important');
            c.style.setProperty('box-sizing','border-box',           'important');
        });

        this.track.style.gap = gap + 'px';
        this._pv  = perView;
        this._cw  = cardW;
        this._gap = gap;

        this._render(true);
    };

    Carousel.prototype.go = function (n) {
        var max = Math.max(0, this.cards.length - this._pv);
        this.idx = Math.max(0, Math.min(n, max));
        this._render(false);
    };

    Carousel.prototype._render = function (instant) {
        var max    = Math.max(0, this.cards.length - this._pv);
        var offset = this.idx * (this._cw + this._gap);
        var hide   = this.cards.length <= this._pv;

        this.track.style.transition = instant ? 'none' : 'transform .45s cubic-bezier(.25,.46,.45,.94)';
        this.track.style.transform  = 'translateX(-' + offset + 'px)';

        /* show/hide arrows */
        this.prev.style.display = hide ? 'none' : 'flex';
        this.next.style.display = hide ? 'none' : 'flex';
        this.prev.style.opacity = this.idx <= 0   ? '0.35' : '1';
        this.next.style.opacity = this.idx >= max ? '0.35' : '1';
    };

    /* wrap-around next/prev */
    Carousel.prototype.next_c = function () {
        var max = Math.max(0, this.cards.length - this._pv);
        this.go(this.idx >= max ? 0 : this.idx + 1);
    };

    Carousel.prototype._auto = function () {
        if (!this.cfg.auto || this.cards.length <= this._pv) return;
        var self = this;
        clearInterval(this.timer);
        this.timer = setInterval(function () { self.next_c(); }, self.cfg.auto);
    };

    Carousel.prototype._reset = function () {
        clearInterval(this.timer);
        this._auto();
    };

    Carousel.prototype.destroy = function () {
        this._alive = false;
        clearInterval(this.timer);
        var wrap = this.grid.querySelector('.tw-hpc-wrap');
        if (wrap) {
            var self = this;
            this.cards.forEach(function (c) {
                ['flex','width','min-width','max-width','box-sizing'].forEach(function (p) {
                    c.style.removeProperty(p);
                });
                self.grid.insertBefore(c, wrap);
            });
            wrap.parentNode.removeChild(wrap);
        }
        this.grid.style.removeProperty('display');
        this.grid.classList.remove('tw-carouseled');
    };

    /* ─── helpers ────────────────────────────────────────── */
    function mk(tag, cls) {
        var el = document.createElement(tag);
        el.className = cls;
        return el;
    }
    function mkBtn(html, cls, label) {
        var b = mk('button', cls);
        b.innerHTML = html;
        b.setAttribute('type', 'button');
        b.setAttribute('aria-label', label);
        return b;
    }

    /* ─── public init ────────────────────────────────────── */
    function initGrid(grid) {
        var cfg = null;
        for (var i = 0; i < TARGETS.length; i++) {
            try { if (grid.matches(TARGETS[i].sel)) { cfg = TARGETS[i]; break; } } catch (e) {}
        }
        if (!cfg) return;

        var old = regGet(grid);
        if (old) { try { old.destroy(); } catch (e) {} }

        /* force block BEFORE creating instance so offsetWidth is correct */
        grid.style.setProperty('display', 'block', 'important');

        regSet(grid, new Carousel(grid, cfg));
    }

    function initAll() {
        TARGETS.forEach(function (t) {
            var els = document.querySelectorAll(t.sel);
            for (var i = 0; i < els.length; i++) initGrid(els[i]);
        });
    }

    /* public — called by tw-filters.js after AJAX */
    window.TwHPCarousel = { init: initAll, initGrid: initGrid };

    /* resize */
    var rt;
    window.addEventListener('resize', function () {
        clearTimeout(rt);
        rt = setTimeout(function () {
            registry.forEach(function (r) { try { r[1]._measure(); } catch (e) {} });
        }, 200);
    });

    /* boot */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
}());
