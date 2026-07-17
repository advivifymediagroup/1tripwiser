/**
 * LUXURY hero — scroll-scrubbed video + per-character fall/fade/squish.
 *
 * Everything is driven by scroll position, not time: progress runs 0 → 1 as
 * the hero scrolls from fully in-view to fully off-screen. The character
 * fall/fade/squish tracks that progress exactly (instant, reversible). The
 * background video's currentTime chases (progress * duration) with a lerp
 * each animation frame so the seek looks smooth instead of jittery — the
 * video never plays on its own timeline, it's scrubbed frame-by-frame.
 */
(function () {
    var hero = document.querySelector('.tw-luxury-hero');
    if (!hero) { return; }

    // The video scrubs across the whole scrolly wrapper (hero + why cards +
    // journeys), not just the hero — it finishes only once the collection
    // has fully scrolled past.
    var scrolly = document.querySelector('.tw-luxury-scrolly') || hero;

    var chars = hero.querySelectorAll('.tw-luxury-char');
    var video = scrolly.querySelector('.tw-luxury-video');

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) { return; }

    var FALL_DIST  = 70;   // viewBox units — scales with the SVG automatically
    var FALL_DUR   = 0.45; // fraction of scroll-progress each letter's own fall takes
    var SQUISH_AMT = 0.3;
    var VIDEO_LERP = 0.22; // smoothing factor for the video seek — the source clip is
                            // encoded all-intra (every frame a keyframe) so seeks are
                            // cheap; a tighter lerp tracks scroll more closely without
                            // losing smoothness.
    var VIDEO_SPAN = 0.85; // video completes at this fraction of the scrolly scroll, then holds its last frame

    // Why-cards reveal: the headline fall played in reverse — each card rises
    // up from below with the same gravity curve and squish, fading in,
    // staggered left → right. Scrubbed by scroll like everything else.
    var CARD_STAGGER = 0.25; // strictly sequential: each card finishes before the next starts
    var CARD_DUR     = 0.25; // fraction of reveal-progress each card's own rise takes
    var CARD_RISE    = 130;  // px each card rises from
    var CARD_SQUISH  = 0.14; // squash intensity while rising
    var CARD_START   = 0.4;  // scrolly-progress where the first card begins (row pinned on screen)
    // The card window ends at VIDEO_SPAN, so the last card lands exactly as
    // the background clip reaches its final frame.

    var midSec = document.querySelector('.tw-luxury-mid');
    var cards  = midSec ? midSec.querySelectorAll('.tw-luxe-why-card') : [];
    if (midSec && cards.length) { midSec.classList.add('js-cards'); }

    // Journeys section — minimal gold reveals: header hairlines draw open,
    // cards fade up with a gold top hairline drawing across. Scroll-scrubbed.
    var collection = document.querySelector('.tw-luxury-collection');
    var colHead    = collection ? collection.querySelector('.tw-luxe-section-head') : null;
    var tripCards  = collection ? collection.querySelectorAll('.tw-luxe-trip-card') : [];
    var headSmooth = 0;
    var tripSmooth = [];
    if (collection) { collection.classList.add('js-reveal'); }

    var n = chars.length;
    var startMax = 1 - FALL_DUR;
    var stagger  = n > 1 ? startMax / (n - 1) : 0;

    var videoReady    = false;
    var videoCurrent  = 0; // our own tracked (lerped) seek position

    /* Mouse wheels scroll in coarse ticks, so raw scroll-mapped values jump
       in steps. Lerping the progress values toward their scroll targets each
       frame turns those steps into a short glide — still scroll-driven and
       fully reversible (the target is always the exact scroll position; the
       smoothed value just takes a few frames to settle on it). */
    var PROGRESS_LERP = 0.14;
    var heroSmooth = 0;
    var sSmooth    = 0;

    function approach(current, target) {
        var next = current + (target - current) * PROGRESS_LERP;
        return Math.abs(target - next) < 0.0004 ? target : next;
    }

    /* Scrub reliability: streamed MP4s freeze after a frame or two because
       the browser has only buffered the head of the file and every seek
       past it stalls on a network range-request. Fetch the whole clip into
       a blob up-front (it's small) — a blob URL is fully local, so every
       frame seeks instantly in both directions. */
    if (video) {
        var srcEl  = video.querySelector('source');
        var srcUrl = (srcEl && srcEl.src) || video.currentSrc || video.src;
        if (window.fetch && srcUrl) {
            fetch(srcUrl)
                .then(function (r) { return r.blob(); })
                .then(function (b) {
                    video.src = URL.createObjectURL(b); // supersedes the <source>
                    video.load();
                    video.addEventListener('loadedmetadata', function () {
                        videoReady = true;
                    });
                })
                .catch(function () { videoReady = video.readyState >= 1; });
        } else if (video.readyState >= 1) {
            videoReady = true;
        }
    }

    function updateChars(progress) {
        for (var i = 0; i < n; i++) {
            var charStart = i * stagger;
            var local = (progress - charStart) / FALL_DUR;
            if (local < 0) { local = 0; }
            if (local > 1) { local = 1; }

            var eased  = local * local; // ease-in — accelerates like gravity
            var ty     = eased * FALL_DIST;
            var op     = 1 - eased;
            var squish = Math.sin(local * Math.PI) * SQUISH_AMT;
            var sy     = 1 - squish;
            var sx     = 1 + squish * 0.6;

            var el = chars[i];
            el.style.setProperty('--ty', ty);
            el.style.setProperty('--op', op);
            el.style.setProperty('--sy', sy);
            el.style.setProperty('--sx', sx);
        }
    }

    function updateCards(sProgress) {
        if (!cards.length) { return; }
        // Keyed to the SAME scroll axis as the video: cards begin once the row
        // is on screen (CARD_START) and the last card lands exactly at
        // VIDEO_SPAN — the moment the clip reaches its final frame. The
        // stretched window means the background visibly plays underneath
        // while each card rises.
        var p = (sProgress - CARD_START) / (VIDEO_SPAN - CARD_START);
        if (p < 0) { p = 0; }
        if (p > 1) { p = 1; }

        for (var i = 0; i < cards.length; i++) {
            var local = (p - i * CARD_STAGGER) / CARD_DUR;
            if (local < 0) { local = 0; }
            if (local > 1) { local = 1; }

            // Exact mirror of the headline letters: run the fall math on the
            // inverted progress so cards decelerate up into place ("landing"
            // instead of dropping), fade in, and squish mid-motion.
            var f      = 1 - local;
            var eased  = f * f;                                   // gravity curve, reversed
            var ty     = eased * CARD_RISE;
            var op     = 1 - eased;
            var squish = Math.sin(local * Math.PI) * CARD_SQUISH;
            var sy     = 1 - squish;
            var sx     = 1 + squish * 0.6;

            cards[i].style.opacity   = op;
            cards[i].style.transform = 'translateY(' + ty + 'px) scale(' + sx + ',' + sy + ')';
        }
    }

    function updateCollection() {
        if (!collection) { return; }
        var vh = window.innerHeight;

        if (colHead) {
            var hr = colHead.getBoundingClientRect();
            var hp = (vh * 0.92 - hr.top) / (vh * 0.4);
            if (hp < 0) { hp = 0; }
            if (hp > 1) { hp = 1; }
            headSmooth = approach(headSmooth, hp);
            // --draw scales the kicker's flanking hairlines open in CSS.
            colHead.style.setProperty('--draw', headSmooth);
            colHead.style.opacity   = headSmooth;
            colHead.style.transform = 'translateY(' + ((1 - headSmooth) * 18) + 'px)';
        }

        for (var i = 0; i < tripCards.length; i++) {
            var r = tripCards[i].getBoundingClientRect();
            var p = (vh * 0.94 - r.top) / (vh * 0.42);
            p -= (i % 2) * 0.12; // slight left-before-right offset within each grid row
            if (p < 0) { p = 0; }
            if (p > 1) { p = 1; }
            tripSmooth[i] = approach(tripSmooth[i] || 0, p);
            var s = tripSmooth[i];
            tripCards[i].style.opacity   = s;
            tripCards[i].style.transform = 'translateY(' + ((1 - s) * 26) + 'px)';
            tripCards[i].style.setProperty('--line', s);
        }
    }

    function loop() {
        // Headline progress: 0 → 1 as the hero section scrolls out of view.
        var rect = hero.getBoundingClientRect();
        var progress = -rect.top / rect.height;
        if (progress < 0) { progress = 0; }
        if (progress > 1) { progress = 1; }

        heroSmooth = approach(heroSmooth, progress);

        // Drives the kicker / corner frame / divider fade in sync with the falling headline.
        hero.style.setProperty('--progress', heroSmooth);

        updateChars(heroSmooth);

        // Shared scroll axis: 0 → 1 across the scrolly wrapper (hero + mid).
        // Both the video scrub and the card reveal are mapped onto it so
        // they stay in lockstep.
        var sRect = scrolly.getBoundingClientRect();
        var scrollable = sRect.height - window.innerHeight;
        var sProgress = scrollable > 0 ? -sRect.top / scrollable : progress;
        if (sProgress < 0) { sProgress = 0; }
        if (sProgress > 1) { sProgress = 1; }

        sSmooth = approach(sSmooth, sProgress);
        updateCards(sSmooth);
        updateCollection();

        if (!videoReady && video && video.readyState >= 1) { videoReady = true; }

        if (videoReady && video.duration && isFinite(video.duration)) {
            // Finish the clip at VIDEO_SPAN of the wrapper — the same point the
            // last card lands — then hold the final frame for the remainder.
            var vProgress = sProgress / VIDEO_SPAN;
            if (vProgress < 0) { vProgress = 0; }
            if (vProgress > 1) { vProgress = 1; }

            var target = vProgress * video.duration;
            videoCurrent += (target - videoCurrent) * VIDEO_LERP;
            if (videoCurrent < 0) { videoCurrent = 0; }
            // Never seek exactly to duration — some decoders treat it as "ended"
            // and refuse further scrubbing.
            var maxSeek = video.duration - 0.05;
            if (videoCurrent > maxSeek) { videoCurrent = maxSeek; }
            // Only issue a seek when the previous one has completed — piling
            // seeks on top of an in-flight one is what freezes the decoder —
            // and skip redundant writes once the lerp has settled.
            if (!video.seeking && Math.abs(video.currentTime - videoCurrent) > 0.002) {
                video.currentTime = videoCurrent;
            }
        }

        requestAnimationFrame(loop);
    }

    requestAnimationFrame(loop);
})();
