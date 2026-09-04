<?php
/**
 * Template Name: App Coming Soon
 * Styles are in style.css (APP — COMING SOON PAGE section)
 */
get_header();
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap">

<section class="app-hero" id="app-hero">
    <canvas id="app-3d-canvas" class="app-3d-canvas" aria-hidden="true"></canvas>

    <div class="app-hero-inner">
      <div class="app-hero-copy">
        <span class="app-kicker" data-anime="kicker"><span class="dot" aria-hidden="true"></span> Launching Soon</span>
        <h1 class="app-hero-title" data-anime="title">Your next trip<br>fits in your <span class="accent">pocket.</span></h1>
        <p class="app-hero-sub" data-anime="sub">
            Plan with Wisey AI, drop Secret Pins, and take 300K+ travellers with you.
        </p>

        <div class="app-hero-actions" data-anime="actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary">Explore the Website</a>
            <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="btn-secondary">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i> Follow for updates
            </a>
        </div>

        <div class="app-store-badges" data-anime="meta">
            <span class="app-store-badge"><i class="fa-brands fa-apple" aria-hidden="true"></i> App Store — Coming Soon</span>
            <span class="app-store-badge"><i class="fa-brands fa-google-play" aria-hidden="true"></i> Google Play — Coming Soon</span>
        </div>
      </div>

        <!-- Interactive phone walkthrough — sign-in through feed, Trails, Explore, TripBook, Profile, live theme switch -->
        <div class="app-demo-wrap" id="app-demo-wrap" data-anime="demo">
        <div id="tw-app-demo">
          <div class="stage">
            <div class="phone-wrap">
              <div class="phone-scale">
                <div class="phone-glow">
                  <div class="phone">
                    <div class="phone-screen" id="phoneScreen" data-theme="dark">
                      <div class="notch"></div>
                      <div id="slotA" class="slot"></div>
                      <div id="slotB" class="slot" style="transform:translateX(100%);opacity:0;"></div>
                      <div class="home-indicator"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="rail">
              <div class="segbar" id="segbar"></div>
              <div class="step-meta">
                <span>STEP <b id="stepIdx">01</b>/08</span>
                <b id="stepLabel">SIGN IN</b>
              </div>
              <div class="transport">
                <button class="tbtn" id="btnPrev" title="Previous">&lsaquo;</button>
                <button class="tbtn play" id="btnPlay" title="Play / pause"></button>
                <button class="tbtn" id="btnNext" title="Next">&rsaquo;</button>
                <button class="tbtn" id="btnRestart" title="Restart">&#8635;</button>
              </div>
              <div class="steplist" id="steplist"></div>
            </div>
          </div>
        </div>
        </div>
    </div>
</section>

<section class="app-features">
    <div class="app-features-inner">
        <span class="app-kicker">What's Inside</span>
        <h2 class="app-section-title">Everything your trip needs</h2>
        <div class="app-feature-grid">
            <div class="app-feature-card" data-reveal>
                <div class="app-feature-icon"><i class="fa-solid fa-robot" aria-hidden="true"></i></div>
                <h3>Wisey — AI Trip Planner</h3>
                <p>A live agentic chat that builds day-by-day itineraries, matches packages and answers questions in real time.</p>
            </div>
            <div class="app-feature-card" data-reveal>
                <div class="app-feature-icon"><i class="fa-solid fa-route" aria-hidden="true"></i></div>
                <h3>TripBook</h3>
                <p>An auto-generated travelogue with your km travelled, cities visited and full trip timelines.</p>
            </div>
            <div class="app-feature-card" data-reveal>
                <div class="app-feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></div>
                <h3>Secret Pins</h3>
                <p>Geo-locked photo &amp; video drops that unlock only when another traveller is within 40 metres.</p>
            </div>
            <div class="app-feature-card" data-reveal>
                <div class="app-feature-icon"><i class="fa-solid fa-clapperboard" aria-hidden="true"></i></div>
                <h3>Stories &amp; Trails</h3>
                <p>Full-screen stories and a vertical short-clip feed built for sharing the trip as it happens.</p>
            </div>
            <div class="app-feature-card" data-reveal>
                <div class="app-feature-icon"><i class="fa-solid fa-coins" aria-hidden="true"></i></div>
                <h3>WiseCoins</h3>
                <p>Earn coins for check-ins, cheers and posts — spend them to boost your own posts and reach more travellers.</p>
            </div>
            <div class="app-feature-card" data-reveal>
                <div class="app-feature-icon"><i class="fa-solid fa-bolt" aria-hidden="true"></i></div>
                <h3>Post Boosting</h3>
                <p>Put your best travel content in front of more of the community with targeted reach plans.</p>
            </div>
        </div>
    </div>
</section>

<section class="app-final-cta">
    <h2>Be there on day one.</h2>
    <p>Follow along on Instagram — we'll announce the launch there first.</p>
    <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="btn-primary">
        <i class="fa-brands fa-instagram" aria-hidden="true"></i> Follow @1tripwiser
    </a>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
(function () {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── Three.js — rotating particle globe behind the hero ── */
    function initGlobe() {
        var canvas = document.getElementById('app-3d-canvas');
        if (!canvas || typeof THREE === 'undefined') return;

        var hero = document.getElementById('app-hero');
        var renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera(55, 1, 0.1, 100);
        camera.position.z = 9;

        function size() {
            var w = hero.offsetWidth, h = hero.offsetHeight;
            renderer.setSize(w, h, false);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
        }

        /* Dual-tone wireframe globe — a fine blue lattice with a coarser pink layer
           riding just inside it, so the sphere reads with real depth instead of
           a single flat mesh. */
        var globe = new THREE.Mesh(
            new THREE.IcosahedronGeometry(3.4, 2),
            new THREE.MeshBasicMaterial({ color: 0x25d7f2, wireframe: true, transparent: true, opacity: 0.16 })
        );
        scene.add(globe);
        var globeInner = new THREE.Mesh(
            new THREE.IcosahedronGeometry(3.15, 1),
            new THREE.MeshBasicMaterial({ color: 0xe5127d, wireframe: true, transparent: true, opacity: 0.1 })
        );
        scene.add(globeInner);

        /* Flight-path arcs — curved lines between points on the globe surface,
           each with a travelling dash so it reads as a route being drawn, not
           a static decoration. Distinctly "travel network", not generic globe. */
        var arcGroup = new THREE.Group();
        var arcLines = [];
        function pointOnSphere(r) {
            var theta = Math.random() * Math.PI * 2;
            var phi = Math.acos((Math.random() * 2) - 1);
            return new THREE.Vector3(
                r * Math.sin(phi) * Math.cos(theta),
                r * Math.sin(phi) * Math.sin(theta),
                r * Math.cos(phi)
            );
        }
        for (var a = 0; a < 5; a++) {
            var pA = pointOnSphere(3.4);
            var pB = pointOnSphere(3.4);
            var mid = pA.clone().add(pB).multiplyScalar(0.5).normalize().multiplyScalar(3.4 + 1.1 + Math.random() * 0.6);
            var curve = new THREE.QuadraticBezierCurve3(pA, mid, pB);
            var pts = curve.getPoints(64);
            var arcGeo = new THREE.BufferGeometry().setFromPoints(pts);
            var arcMat = new THREE.LineDashedMaterial({
                color: a % 2 === 0 ? 0xff6bb0 : 0x5fe3f5,
                transparent: true,
                opacity: 0.55,
                dashSize: 0.28,
                gapSize: 0.22
            });
            var line = new THREE.Line(arcGeo, arcMat);
            line.computeLineDistances();
            arcGroup.add(line);
            arcLines.push({ line: line, mat: arcMat, offset: Math.random() * 10 });
        }
        scene.add(arcGroup);

        /* Particle points scattered around it, two tones for a little depth */
        var particleCount = 260;
        var positions = new Float32Array(particleCount * 3);
        var colors = new Float32Array(particleCount * 3);
        var colorA = new THREE.Color(0xe5127d);
        var colorB = new THREE.Color(0x5fe3f5);
        for (var i = 0; i < particleCount; i++) {
            var r = 4.4 + Math.random() * 2.4;
            var theta = Math.random() * Math.PI * 2;
            var phi = Math.acos((Math.random() * 2) - 1);
            positions[i * 3]     = r * Math.sin(phi) * Math.cos(theta);
            positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
            positions[i * 3 + 2] = r * Math.cos(phi);
            var c = (i % 3 === 0 ? colorB : colorA);
            colors[i * 3] = c.r; colors[i * 3 + 1] = c.g; colors[i * 3 + 2] = c.b;
        }
        var particleGeo = new THREE.BufferGeometry();
        particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        particleGeo.setAttribute('color', new THREE.BufferAttribute(colors, 3));
        var particleMat = new THREE.PointsMaterial({ size: 0.055, vertexColors: true, transparent: true, opacity: 0.8 });
        var particles = new THREE.Points(particleGeo, particleMat);
        scene.add(particles);

        size();
        window.addEventListener('resize', size);

        var mouseX = 0, mouseY = 0;
        hero.addEventListener('mousemove', function (e) {
            var rect = hero.getBoundingClientRect();
            mouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
            mouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
        });

        if (reduceMotion) {
            renderer.render(scene, camera);
            return;
        }

        var clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);
            var t = clock.getElapsedTime();
            globe.rotation.y += 0.0016;
            globe.rotation.x += 0.0005;
            globeInner.rotation.y -= 0.0011;
            globeInner.rotation.x += 0.0004;
            arcGroup.rotation.y += 0.0016;
            arcGroup.rotation.x += 0.0005;
            particles.rotation.y -= 0.0009;
            arcLines.forEach(function (a) {
                a.mat.dashOffset = -((t * 0.6 + a.offset) % 4);
            });
            camera.position.x += (mouseX * 1.1 - camera.position.x) * 0.02;
            camera.position.y += (-mouseY * 1.1 - camera.position.y) * 0.02;
            camera.lookAt(scene.position);
            renderer.render(scene, camera);
        }
        animate();
    }

    /* ── anime.js — hero entrance, phone float, mouse-tracked 3D tilt ── */
    function initAnime() {
        if (typeof anime === 'undefined') return;

        anime.timeline({ easing: 'easeOutExpo' })
            .add({ targets: '[data-anime="kicker"]', opacity: [0, 1], translateY: [-14, 0], duration: 600 })
            .add({ targets: '[data-anime="title"]', opacity: [0, 1], translateY: [24, 0], duration: 800 }, '-=350')
            .add({ targets: '[data-anime="sub"]', opacity: [0, 1], translateY: [18, 0], duration: 700 }, '-=500')
            .add({ targets: '[data-anime="actions"]', opacity: [0, 1], translateY: [16, 0], duration: 700 }, '-=500')
            .add({ targets: '[data-anime="meta"]', opacity: [0, 1], translateY: [12, 0], duration: 600 }, '-=500')
            .add({ targets: '#app-demo-wrap', opacity: [0, 1], translateY: [24, 0], duration: 900 }, '-=500');

        if (reduceMotion) return;

        /* Feature cards — staggered reveal on scroll */
        var cards = document.querySelectorAll('.app-feature-card[data-reveal]');
        if (cards.length && window.IntersectionObserver) {
            cards.forEach(function (c) { c.style.opacity = 0; });
            var obs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    anime({
                        targets: entry.target,
                        opacity: [0, 1],
                        translateY: [26, 0],
                        duration: 650,
                        easing: 'easeOutCubic'
                    });
                    obs.unobserve(entry.target);
                });
            }, { threshold: 0.15 });
            cards.forEach(function (c) { obs.observe(c); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initGlobe(); initAnime(); });
    } else {
        initGlobe();
        initAnime();
    }
})();
</script>

<!-- Interactive walkthrough — icon set, screen renderers, playback state machine -->
<script>
(function () {
/* ---------------- icon set ---------------- */
const I = {
  coin:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 9.5c0-1.2 1.3-2 3-2s3 .8 3 2-1.3 1.5-3 2-3 .8-3 2 1.3 2 3 2 3-.8 3-2" stroke-linecap="round"/></svg>',
  calendar:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3.5" y="5" width="17" height="16" rx="3"/><path d="M8 3v4M16 3v4M3.5 10h17"/></svg>',
  bell:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 10a6 6 0 1 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>',
  chat:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12a8 8 0 1 1 3.2 6.4L4 20l1.3-3.6A7.96 7.96 0 0 1 4 12Z"/></svg>',
  search:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/></svg>',
  heart:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 20.5s-7.5-4.7-9.8-9.4C.6 7.6 2.3 4 5.9 3.4c2-.3 3.9.6 5 2.3a1 1 0 0 0 1.6 0c1.1-1.7 3-2.6 5-2.3 3.6.6 5.3 4.2 3.8 7.7C19.6 15.8 12 20.5 12 20.5Z"/></svg>',
  comment:'<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M4 12a8 8 0 1 1 3.2 6.4L4 20l1.3-3.6A7.96 7.96 0 0 1 4 12Z"/></svg>',
  share:'<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7"/><path d="M16 6l-4-4-4 4M12 2.5V15"/></svg>',
  bookmark:'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M6 3.5h12a1 1 0 0 1 1 1V21l-7-4-7 4V4.5a1 1 0 0 1 1-1Z"/></svg>',
  gear:'<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M19.4 13.5a7.6 7.6 0 0 0 0-3l2-1.4-2-3.4-2.3.8a7.6 7.6 0 0 0-2.6-1.5L14 2.5h-4l-.5 2.5a7.6 7.6 0 0 0-2.6 1.5l-2.3-.8-2 3.4 2 1.4a7.6 7.6 0 0 0 0 3l-2 1.4 2 3.4 2.3-.8a7.6 7.6 0 0 0 2.6 1.5l.5 2.5h4l.5-2.5a7.6 7.6 0 0 0 2.6-1.5l2.3.8 2-3.4-2-1.4Z"/></svg>',
  back:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5 8 12l7 7"/></svg>',
  play2:'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>',
  home:'<svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11.5 12 4l8 7.5"/><path d="M6 10v9.5a1 1 0 0 0 1 1h3.5v-6h3v6H17a1 1 0 0 0 1-1V10"/></svg>',
  homeF:'<svg width="21" height="21" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 3 11h2v9h5v-6h4v6h5v-9h2z"/></svg>',
  compass:'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m15 9-4 6-2-2 4-6z"/></svg>',
  compassF:'<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="9.3" opacity=".18"/><path d="M12 2.5A9.5 9.5 0 1 0 21.5 12 9.5 9.5 0 0 0 12 2.5Zm3.6 6-3 6.4-2.4-2.4 2.4-2.4z"/></svg>',
  book:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 19a2.5 2.5 0 0 1 2.5-2.5H20"/></svg>',
  bookF:'<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6.5 3H21v16H6.5A2.5 2.5 0 0 0 4 21.5v-16A2.5 2.5 0 0 1 6.5 3Z"/></svg>',
  person:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20c1.2-3.8 4.2-5.8 7.5-5.8s6.3 2 7.5 5.8"/></svg>',
  personF:'<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="7.6" r="4"/><path d="M4 20.2c1.3-4.4 4.5-6.7 8-6.7s6.7 2.3 8 6.7z"/></svg>',
  plus:'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>',
  pencil:'<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20.5 4.6 17 16 5.6a2 2 0 0 1 2.8 0l1.6 1.6a2 2 0 0 1 0 2.8L9 21.4l-3.5.6z"/></svg>',
  trash:'<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14M9 7V4.5h6V7M6 7l1 13h10l1-13"/></svg>',
  pin:'<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.6A2.6 2.6 0 1 1 12 6.4a2.6 2.6 0 0 1 0 5.2Z"/></svg>',
  mail:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 6.5 8 6 8-6"/></svg>',
  lock:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>',
  arrowR:'<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
  trophy:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M7 5H4v1a4 4 0 0 0 4 4M17 5h3v1a4 4 0 0 1-4 4M9 21h6M12 15v6"/></svg>',
  check:'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m4 12 6 6L20 6"/></svg>',
  sun:'<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.4M12 19v2.5M4.6 4.6l1.7 1.7M17.7 17.7l1.7 1.7M2.5 12h2.4M19 12h2.5M4.6 19.4l1.7-1.7M17.7 6.3l1.7-1.7"/></svg>',
  moon:'<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 14.3A8.7 8.7 0 1 1 9.7 4a7 7 0 0 0 10.3 10.3Z"/></svg>',
  wave:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M4 12h2M18 12h2M12 4v2M12 18v2M6.3 6.3l1.5 1.5M16.2 16.2l1.5 1.5M6.3 17.7l1.5-1.5M16.2 7.8l1.5-1.5"/></svg>',
};
const google = '<svg width="16" height="16" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.4 0 6.4 1.2 8.8 3.5l6.5-6.5C35.3 2.5 30 0 24 0 14.6 0 6.5 5.4 2.6 13.2l7.6 5.9C12.1 13 17.6 9.5 24 9.5Z"/><path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.6c-.6 3-2.3 5.5-4.8 7.2l7.4 5.7c4.3-4 6.8-9.9 6.8-17.4Z"/><path fill="#FBBC05" d="M10.2 19.1a14.5 14.5 0 0 0 0 9.8l-7.6 5.9a24 24 0 0 1 0-21.6z"/><path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.4-5.7c-2.1 1.4-4.8 2.2-8.5 2.2-6.4 0-11.9-3.5-14-8.6l-7.6 5.9C6.5 42.6 14.6 48 24 48Z"/></svg>';

/* ---------- small helpers ---------- */
const avatarLetters = (name,cls) => `<div class="avatar ${cls}" style="width:100%;height:100%">${name}</div>`;

function screenShell(inner, {tab=null, statusOverlay=false}={}){
  return `<div class="app">
    <div class="statusbar" style="${statusOverlay?'position:absolute;z-index:25;color:#fff':''}">
      <span>9:41</span>
      <span class="icons">
        <svg width="17" height="10" viewBox="0 0 17 10"><rect x="0" y="6" width="3" height="4" rx=".5" fill="currentColor"/><rect x="4.5" y="4" width="3" height="6" rx=".5" fill="currentColor"/><rect x="9" y="2" width="3" height="8" rx=".5" fill="currentColor"/><rect x="13.5" y="0" width="3" height="10" rx=".5" fill="currentColor"/></svg>
        <svg width="15" height="11" viewBox="0 0 15 11" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M1.5 4.2a10 10 0 0 1 12 0M4 6.6a6 6 0 0 1 7 0M6.7 9a2.4 2.4 0 0 1 1.6 0"/></svg>
        <svg width="24" height="11" viewBox="0 0 24 11"><rect x=".5" y=".5" width="20" height="10" rx="2.5" fill="none" stroke="currentColor"/><rect x="2" y="2" width="15" height="7" rx="1.2" fill="currentColor"/><rect x="21" y="3.3" width="2" height="4.4" rx="1" fill="currentColor"/></svg>
      </span>
    </div>
    <div class="screen-body">${inner}</div>
    ${tab? tabbar(tab) : ''}
  </div>`;
}

function tabbar(active){
  const items = [
    {k:'home',label:'Home',icon:I.home,iconF:I.homeF},
    {k:'discover',label:'Explore',icon:I.compass,iconF:I.compassF},
    {k:'create',fab:true},
    {k:'tripbook',label:'TripBook',icon:I.book,iconF:I.bookF},
    {k:'profile',label:'Profile',icon:I.person,iconF:I.personF},
  ];
  return `<div class="tabbar">${items.map(it=>{
    if(it.fab) return `<div class="tab-fab" data-nav="create">${I.plus}</div>`;
    const isActive = it.k===active;
    return `<div class="tab ${isActive?'active':''}" data-nav="${it.k}">${isActive?it.iconF:it.icon}<span>${it.label}</span></div>`;
  }).join('')}</div>`;
}

/* ---------- screen renderers ---------- */
function renderSignIn(){
  return `<div class="app" style="background:
      radial-gradient(120% 60% at 50% 0%, #4a2350 0%, #2a1a42 38%, #14192c 72%);">
    <div class="statusbar" style="position:absolute;z-index:25;color:#fff">
      <span>9:41</span><span class="icons" style="color:#fff">${''}</span>
    </div>
    <div style="position:absolute;inset:0;overflow:hidden">
      <div style="position:absolute;top:64px;left:0;right:0;display:flex;flex-direction:column;align-items:center;padding:0 26px;color:#fff;text-align:center">
        <div style="width:58px;height:58px;border-radius:18px;background:linear-gradient(135deg,#F2489B,#8B2FBF 55%,#5FE3F5);display:flex;align-items:center;justify-content:center;box-shadow:0 14px 26px rgba(242,72,155,.4);margin-bottom:14px">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="#fff"><path d="M3 11 21 3l-8 18-3-7-7-3Z"/></svg>
        </div>
        <div style="font-weight:800;font-size:19px;letter-spacing:.08em">
          <span style="color:#6be6ff">1</span>TRIPWISER
        </div>
        <div style="font-size:11.5px;color:rgba(255,255,255,.75);letter-spacing:.05em;margin-top:3px">Wiser trips · Better memories</div>
        <div style="font-weight:800;font-size:24px;line-height:1.2;margin-top:22px">Every trip deserves<br>a better story.</div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:16px">
          <div style="display:flex">
            <div class="avatar grad-a" style="width:24px;height:24px;font-size:9px;border:2px solid rgba(255,255,255,.5)">P</div>
            <div class="avatar grad-c" style="width:24px;height:24px;font-size:9px;border:2px solid rgba(255,255,255,.5);margin-left:-8px">K</div>
            <div class="avatar grad-b" style="width:24px;height:24px;font-size:9px;border:2px solid rgba(255,255,255,.5);margin-left:-8px">A</div>
          </div>
          <span style="font-size:11.5px;color:rgba(255,255,255,.8)">Join travelers mapping the world</span>
        </div>
      </div>
      <div style="position:absolute;left:0;right:0;bottom:0;top:398px;background:linear-gradient(180deg,rgba(11,18,32,.4),#0b1220 26%);border-radius:28px 28px 0 0;padding:26px 20px;">
        <div style="font-weight:800;font-size:23px;color:#fff">Welcome back</div>
        <div style="font-size:12.5px;color:#9aa3b2;margin-top:5px;margin-bottom:18px">Sign in to pick up where you left off</div>
        <div class="input" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.14);color:#fff;margin-bottom:10px">${I.mail}<span>demo@tripwiser.com</span></div>
        <div class="input" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.14);color:#fff;margin-bottom:16px">${I.lock}<span style="letter-spacing:3px">••••••••</span></div>
        <div id="signinBtn" class="btn-primary" style="height:52px">Sign In ${I.arrowR}</div>
        <div style="display:flex;align-items:center;gap:10px;margin:16px 0;color:#6b7386;font-size:11.5px">
          <div style="flex:1;height:1px;background:rgba(255,255,255,.12)"></div>or continue with<div style="flex:1;height:1px;background:rgba(255,255,255,.12)"></div>
        </div>
        <div class="input" style="justify-content:center;background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.14);color:#fff;font-weight:700">${google}<span>Google</span></div>
        <div style="text-align:center;margin-top:14px;font-size:12.5px;color:#9aa3b2">New here? <b style="color:#F2489B">Create an account</b></div>
      </div>
    </div>
  </div>`;
}

function renderHome(){
  return screenShell(`<div class="scrolly">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
      <div class="pill" style="background:var(--c-brand-tertiary);color:var(--c-on-brand-tertiary)">${I.coin}1,248</div>
      <div style="flex:1"></div>
      <div class="icon-btn">${I.calendar}</div>
      <div class="icon-btn">${I.bell}<span style="position:absolute;top:6px;right:7px;width:6px;height:6px;border-radius:50%;background:var(--c-brand)"></span></div>
      <div class="icon-btn">${I.chat}</div>
    </div>

    <div style="display:flex;gap:14px;overflow:hidden;margin-bottom:18px">
      <div style="display:flex;flex-direction:column;align-items:center;gap:5px;flex:0 0 auto">
        <div style="width:56px;height:56px;border-radius:50%;padding:2.5px;background:conic-gradient(from 180deg,#F2489B,#8B2FBF,#5FE3F5,#F2489B)">
          <div style="width:100%;height:100%;border-radius:50%;background:var(--c-surface-2);display:flex;align-items:center;justify-content:center;border:2px solid var(--c-surface)">${I.play2}</div>
        </div>
        <span style="font-size:9.5px;font-weight:700;color:var(--c-on-2)">Trails</span>
      </div>
      <div style="display:flex;flex-direction:column;align-items:center;gap:5px;flex:0 0 auto">
        <div style="width:56px;height:56px;border-radius:50%;position:relative">
          <div class="avatar grad-d" style="width:100%;height:100%;font-size:18px;border:2px solid var(--c-surface)">D</div>
          <div style="position:absolute;bottom:-2px;right:-2px;width:19px;height:19px;border-radius:50%;background:var(--c-brand);border:2.5px solid var(--c-surface);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:800;line-height:1">+</div>
        </div>
        <span style="font-size:9.5px;font-weight:700;color:var(--c-on-2)">Your story</span>
      </div>
      ${['Priya','Kenji','Aria','Marco'].map((n,i)=>`
      <div style="display:flex;flex-direction:column;align-items:center;gap:5px;flex:0 0 auto">
        <div class="avatar grad-${['b','c','e','a'][i]}" style="width:56px;height:56px;font-size:18px;border:2.5px solid var(--c-brand)">${n[0]}</div>
        <span style="font-size:9.5px;font-weight:700;color:var(--c-on-2)">${n}</span>
      </div>`).join('')}
    </div>

    <div class="card" style="padding:14px 16px;display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:38px;height:38px;border-radius:12px;background:var(--c-brand-tertiary);color:var(--c-on-brand-tertiary);display:flex;align-items:center;justify-content:center">${I.trophy}</div>
      <div style="flex:1">
        <div style="display:flex;justify-content:space-between;font-size:12.5px;font-weight:700;margin-bottom:5px"><span>Lv 3 · Explorer</span><span style="color:var(--c-on-2);font-weight:600">1,840 / 3,000 XP</span></div>
        <div style="height:6px;border-radius:3px;background:var(--c-surface-2)"><div style="width:61%;height:100%;border-radius:3px;background:linear-gradient(90deg,var(--c-brand),var(--c-brand-2))"></div></div>
      </div>
    </div>

    <div class="card" style="padding:14px;margin-bottom:14px">
      <div style="display:flex;align-items:center;gap:9px;margin-bottom:11px">
        <div class="avatar grad-b" style="width:34px;height:34px;font-size:13px">P</div>
        <div style="flex:1"><div style="font-weight:700;font-size:13.5px">priya.wanders</div><div style="font-size:11px;color:var(--c-on-3)">2h ago · Kyoto, Japan</div></div>
      </div>
      <div class="post-media grad-a"><div class="contour"></div><span class="loc">${I.pin} Fushimi Inari</span></div>
      <div style="font-size:13px;line-height:1.45;margin-top:10px;color:var(--c-on-2)">Golden hour at Fushimi Inari — 10,000 torii gates and not a single regret 🧡</div>
      <div class="actions-row">
        <div class="act heart" id="likeBtn">${I.heart}<span class="count" id="likeCount">482</span></div>
        <div class="act">${I.comment}<span class="count">36</span></div>
        <div class="act">${I.share}</div>
        <div class="grow"></div>
        <div class="pill" style="background:linear-gradient(120deg,var(--c-brand),#F0479B);color:#fff;padding:5px 11px">Boost</div>
        <div class="act">${I.bookmark}</div>
      </div>
    </div>

    <div class="card" style="padding:14px;position:relative;overflow:hidden;background:linear-gradient(120deg,#1c1030,#241040 60%,#3a1030);border-color:transparent">
      <div style="position:absolute;inset:0;opacity:.35;background:radial-gradient(140px 100px at 90% 10%, #caa54a, transparent)"></div>
      <div style="position:relative;display:flex;align-items:center;gap:12px">
        <div style="flex:1">
          <span class="pill tag-featured" style="padding:3px 9px;font-size:9.5px;margin-bottom:7px;display:inline-block">Featured</span>
          <div style="font-weight:800;font-size:14.5px;color:#f3e6c9">LUXE — Kyoto Imperial Retreat</div>
          <div style="font-size:11.5px;color:#cbb98f;margin-top:4px">Explore LUXE ${I.arrowR}</div>
        </div>
        <div style="width:52px;height:52px;border-radius:14px" class="grad-b"></div>
      </div>
    </div>
  </div>`, {tab:'home'});
}

function renderTrails(){
  return `<div class="app">
    <div class="reel"></div><div class="reel-scrim"></div>
    <div class="statusbar" style="position:absolute;z-index:25;color:#fff">
      <span>9:41</span>
    </div>
    <div style="position:absolute;top:8px;left:14px;z-index:25;display:flex;align-items:center;gap:10px;color:#fff">
      <div class="icon-btn" style="background:rgba(255,255,255,.14);margin-top:38px">${I.back}</div>
      <div style="font-weight:800;font-size:15px;margin-top:38px">Trails</div>
    </div>
    <div class="story-segs">${[70,0,0,0].map(w=>`<i><b style="width:${w}%"></b></i>`).join('')}</div>
    <div class="reel-bottom">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <div class="avatar grad-e" style="width:30px;height:30px;font-size:12px;border:1.5px solid #fff">K</div>
        <b style="font-size:13px">@kenji.explores</b>
        <span class="pill" style="background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.5);font-size:10px;padding:3px 9px">Follow</span>
      </div>
      <div style="font-size:13px;line-height:1.4">Sunrise trek to Kedarkantha — day 2 of 4 🏔️</div>
    </div>
    <div class="reel-side">
      <div class="act2">${I.heart}<span>9.2K</span></div>
      <div class="act2">${I.comment}<span>214</span></div>
      <div class="act2">${I.share}<span>Share</span></div>
      <div class="act2">${I.bookmark}<span>Save</span></div>
    </div>
  </div>`;
}

function renderExplore(){
  const card = (grad,badge,badgeCls,title,price)=>`
    <div style="flex:0 0 148px">
      <div class="post-media ${grad}" style="height:104px;align-items:flex-start"><div class="contour"></div><span class="pill ${badgeCls}" style="font-size:9px;padding:4px 8px">${badge}</span></div>
      <div style="font-weight:700;font-size:12.5px;margin-top:7px;line-height:1.3">${title}</div>
      <div style="font-size:11.5px;color:var(--c-on-2);margin-top:2px">${price}</div>
    </div>`;
  return screenShell(`<div class="scrolly">
    <div class="h-title">Explore</div>
    <div class="input" style="margin-bottom:6px">${I.search}<span class="ph">Search destinations, travellers, trips…</span></div>
    <div class="section-title">Women-only trips</div>
    <div style="display:flex;gap:12px;overflow:hidden">
      ${card('grad-d','Women-only','tag-women','Bali Sisterhood Retreat','₹64,900 · 6D/5N')}
      ${card('grad-b','Women-only','tag-women','Spiti Valley Circle','₹41,500 · 5D/4N')}
    </div>
    <div class="section-title">LUXE — by invitation</div>
    <div style="display:flex;gap:12px;overflow:hidden">
      ${card('grad-c','LUXE','tag-luxe','Maldives Private Villas','₹2.4L · 4D/3N')}
      ${card('grad-a','LUXE','tag-luxe','Swiss Alpine Chalet','₹3.1L · 5D/4N')}
    </div>
    <div class="section-title">Popular this week</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
      <div class="post-media grad-e" style="height:88px"><div class="contour"></div></div>
      <div class="post-media grad-a" style="height:88px"><div class="contour"></div></div>
    </div>
  </div>`,{tab:'discover'});
}

function renderTripbook(){
  const trip = (grad,title,dates)=>`
    <div class="card" style="display:flex;align-items:center;gap:12px;padding:12px;margin-bottom:10px">
      <div class="${grad}" style="width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff">${I.pin}</div>
      <div style="flex:1"><div style="font-weight:700;font-size:13.5px">${title}</div><div style="font-size:11.5px;color:var(--c-on-2);margin-top:2px">${dates}</div></div>
      <div class="icon-btn" style="width:30px;height:30px">${I.pencil}</div>
      <div class="icon-btn" style="width:30px;height:30px;color:#e6577a">${I.trash}</div>
    </div>`;
  return screenShell(`<div class="scrolly">
    <div class="h-title" style="margin-bottom:2px">TripBook</div>
    <div style="font-size:12.5px;color:var(--c-on-2);margin-bottom:16px">4 trips · 2 countries</div>
    <div class="seg" style="margin-bottom:16px;position:relative">
      <div class="thumb" style="left:4px;width:calc(50% - 6px)"></div>
      <b class="on"><span>My Trips</span></b><b><span>Saved</span></b>
    </div>
    ${trip('grad-a','Goa Weekend Hop','Aug 3 – Aug 4, 2026')}
    ${trip('grad-c','Rishikesh Rafting Loop','Sep 12 – Sep 14, 2026')}
    ${trip('grad-b','Udaipur Lake Trail','Oct 2 – Oct 5, 2026')}
    ${trip('grad-e','Coorg Coffee Trail','Nov 20 – Nov 22, 2026')}
  </div>`,{tab:'tripbook'});
}

function renderProfile(){
  return screenShell(`<div class="scrolly">
    <div style="display:flex;justify-content:flex-end;margin-bottom:2px">
      <div class="icon-btn" id="gearBtn" style="cursor:pointer">${I.gear}</div>
    </div>
    <div style="display:flex;flex-direction:column;align-items:center;margin-top:-6px">
      <div style="width:78px;height:78px;border-radius:50%;padding:3px;background:conic-gradient(from 180deg,#F2489B,#8B2FBF,#5FE3F5,#F2489B)">
        <div class="avatar grad-d" style="width:100%;height:100%;font-size:26px;border:2.5px solid var(--c-surface)">D</div>
      </div>
      <div style="font-weight:800;font-size:16.5px;margin-top:10px">Demo Wanderer</div>
      <div style="font-size:12px;color:var(--c-on-2)">@wanderlust_demo</div>
      <div style="font-size:12px;color:var(--c-on-2);margin-top:6px;text-align:center">Chasing sunsets &amp; street food 🌍</div>
    </div>
    <div style="display:flex;margin:18px 0" >
      <div class="stat"><b>24</b><span>Posts</span></div>
      <div class="stat"><b>1.8K</b><span>Followers</span></div>
      <div class="stat"><b>312</b><span>Following</span></div>
    </div>
    <div class="card" style="padding:12px 14px;display:flex;align-items:center;gap:10px;margin-bottom:12px">
      <div style="width:34px;height:34px;border-radius:10px;background:var(--c-brand-tertiary);color:var(--c-on-brand-tertiary);display:flex;align-items:center;justify-content:center">${I.trophy}</div>
      <div style="flex:1;font-weight:700;font-size:13px">Level 3 · Explorer</div>
      <div style="font-size:11px;color:var(--c-on-2);font-weight:600">61%</div>
    </div>
    <div class="card" style="padding:12px 14px;margin-bottom:6px;display:flex;align-items:center;gap:10px;background:linear-gradient(120deg,var(--c-brand-tertiary),transparent)">
      <div style="width:34px;height:34px;border-radius:10px;background:var(--c-surface-2);display:flex;align-items:center;justify-content:center">${I.coin}</div>
      <div style="font-size:12px;color:var(--c-on-2)">Boost your posts to reach more travellers</div>
    </div>
    <div class="seg" style="margin:16px 0 4px;position:relative">
      <div class="thumb" style="left:4px;width:calc(50% - 6px)"></div>
      <b class="on"><span>Posts</span></b><b><span>Saved</span></b>
    </div>
    <div class="grid6">
      <div class="grad-a"></div><div class="grad-b"></div><div class="grad-c"></div>
      <div class="grad-d"></div><div class="grad-e"></div><div class="grad-a"></div>
    </div>
  </div>`,{tab:'profile'});
}

function renderSettings(){
  return screenShell(`<div class="scrolly">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
      <div class="icon-btn">${I.back}</div>
      <div style="font-weight:800;font-size:18px">Settings</div>
    </div>

    <div class="section-title" style="margin-top:0">Appearance</div>
    <div class="seg" id="themeSeg" style="position:relative;margin-bottom:18px">
      <div class="thumb" id="themeThumb" style="left:4px;width:calc(33.33% - 5px)"></div>
      <b class="on" data-mode="dark"><span>${I.moon} Dark</span></b>
      <b data-mode="light"><span>${I.sun} Light</span></b>
      <b data-mode="auto"><span>${I.wave} Auto</span></b>
    </div>

    <div class="section-title">Privacy</div>
    <div class="card" style="padding:4px 14px;margin-bottom:18px">
      <div class="row-item"><span style="flex:1;font-size:13px;font-weight:600">Public profile</span><div class="switch"><i></i></div></div>
    </div>

    <div class="section-title">Your level</div>
    <div class="card" style="padding:14px;margin-bottom:18px">
      <div style="display:flex;justify-content:space-between;font-size:12.5px;font-weight:700;margin-bottom:8px"><span>Lv 3 · Explorer</span><span style="color:var(--c-on-2);font-weight:600">1,840 / 3,000 XP</span></div>
      <div style="height:6px;border-radius:3px;background:var(--c-surface-2);margin-bottom:12px"><div style="width:61%;height:100%;border-radius:3px;background:linear-gradient(90deg,var(--c-brand),var(--c-brand-2))"></div></div>
      <div class="perk done"><div class="dot">${I.check}</div>Priority feed placement</div>
      <div class="perk done"><div class="dot">${I.check}</div>Explorer badge on profile</div>
      <div class="perk"><div class="dot">4</div>Next: Level 4 — custom trail cover art</div>
      <div style="font-size:10.5px;font-style:italic;color:var(--c-on-3);margin-top:8px">Affiliate perks are on the roadmap — nothing monetary yet.</div>
    </div>

    <div class="section-title">Account</div>
    <div class="card" style="padding:4px 14px">
      <div class="row-item" style="color:#e6577a"><span style="font-size:13px;font-weight:700">Sign out</span></div>
    </div>
  </div>`);
}

/* ---------------- app state machine ---------------- */
const STEPS = [
  {id:'signin', label:'Sign In', theme:'dark', dwell:3800, render:renderSignIn},
  {id:'home1', label:'Home', theme:'dark', dwell:3400, render:renderHome},
  {id:'trails', label:'Trails', theme:'dark', dwell:3000, render:renderTrails},
  {id:'explore', label:'Explore', theme:'dark', dwell:3200, render:renderExplore},
  {id:'tripbook', label:'TripBook', theme:'dark', dwell:3000, render:renderTripbook},
  {id:'profile', label:'Profile', theme:'dark', dwell:3000, render:renderProfile},
  {id:'settings', label:'Settings', theme:'dark', dwell:4400, render:renderSettings, autoFlip:2100},
  {id:'home2', label:'Light mode', theme:'light', dwell:3600, render:renderHome},
];
const NAV_MAP = {home:1, discover:3, create:4, tripbook:4, profile:5};

let cur = 0, playing = true, liked = false, manualThemeTouched=false;
let slotFlag = 'A';
let dwellTimer=null, dwellStart=0, dwellDur=0, rafId=null;
const phoneScreen = document.getElementById('phoneScreen');
const stepIdxEl = document.getElementById('stepIdx');
const stepLabelEl = document.getElementById('stepLabel');
const btnPlay = document.getElementById('btnPlay');

function buildRail(){
  const seg = document.getElementById('segbar');
  const list = document.getElementById('steplist');
  seg.innerHTML = STEPS.map((s,i)=>`<button data-i="${i}"><b></b></button>`).join('');
  list.innerHTML = STEPS.map((s,i)=>`<button data-i="${i}"><span class="n">0${i+1}</span>${s.label}<span class="dot2"></span></button>`).join('');
  seg.querySelectorAll('button').forEach(b=>b.addEventListener('click',()=>{manualThemeTouched=false; goTo(+b.dataset.i,true);}));
  list.querySelectorAll('button').forEach(b=>b.addEventListener('click',()=>{manualThemeTouched=false; goTo(+b.dataset.i,true);}));
}
buildRail();

function paintRail(progress){
  const segs = document.querySelectorAll('#segbar button');
  const items = document.querySelectorAll('#steplist button');
  segs.forEach((b,i)=>{
    const bar = b.querySelector('b');
    if(i<cur) bar.style.width='100%';
    else if(i===cur) bar.style.width = (progress*100)+'%';
    else bar.style.width='0%';
  });
  items.forEach((b,i)=>b.classList.toggle('active', i===cur));
  stepIdxEl.textContent = String(cur+1).padStart(2,'0');
  stepLabelEl.textContent = STEPS[cur].label.toUpperCase();
}

function wireInteractive(root, stepIndex){
  root.querySelectorAll('[data-nav]').forEach(el=>{
    el.addEventListener('click',()=>{
      const key = el.dataset.nav;
      if(NAV_MAP[key]!==undefined){ manualThemeTouched=false; goTo(NAV_MAP[key], true); }
    });
  });
  const like = root.querySelector('#likeBtn');
  if(like) like.addEventListener('click',()=>{
    liked=!liked;
    like.classList.toggle('liked',liked);
    like.querySelector('.count').textContent = liked?'483':'482';
  });
  const gear = root.querySelector('#gearBtn');
  if(gear) gear.addEventListener('click',()=>{ manualThemeTouched=false; goTo(6,true); });
  const signinBtn = root.querySelector('#signinBtn');
  if(signinBtn) signinBtn.addEventListener('click',()=>tapAdvance(signinBtn));
  const seg = root.querySelector('#themeSeg');
  if(seg){
    seg.querySelectorAll('b').forEach(b=>b.addEventListener('click',()=>{
      manualThemeTouched=true;
      setThemeSeg(seg, b.dataset.mode);
    }));
  }
}

function setThemeSeg(seg,mode){
  const idx = {dark:0,light:1,auto:2}[mode];
  seg.querySelectorAll('b').forEach(b=>b.classList.toggle('on', b.dataset.mode===mode));
  seg.querySelector('#themeThumb').style.left = `calc(${idx*33.333}% + 4px)`;
  phoneScreen.dataset.theme = mode==='light' ? 'light' : 'dark';
}

function tapAdvance(btn){
  btn.classList.add('pressed');
  setTimeout(()=>{btn.classList.remove('pressed'); advance();},260);
}

function renderInto(slotEl, step){
  slotEl.innerHTML = step.render();
  wireInteractive(slotEl, STEPS.indexOf(step));
  if(step.id==='settings'){
    const seg = slotEl.querySelector('#themeSeg');
    setThemeSeg(seg,'dark');
  }
}

function goTo(index, userInitiated){
  index = Math.max(0, Math.min(STEPS.length-1, index));
  if(index===cur && userInitiated!==true) return;
  clearTimers();
  const dir = index>cur ? 1 : -1;
  cur = index;
  manualThemeTouched = manualThemeTouched && false;
  const showEl = document.getElementById(slotFlag==='A'?'slotB':'slotA');
  const hideEl = document.getElementById(slotFlag==='A'?'slotA':'slotB');
  const step = STEPS[cur];
  phoneScreen.dataset.theme = step.theme;
  showEl.style.transition='none';
  showEl.style.transform = `translateX(${dir*100}%)`;
  showEl.style.opacity='0';
  renderInto(showEl, step);
  void showEl.offsetWidth;
  showEl.style.transition='';
  requestAnimationFrame(()=>{
    showEl.style.transform='translateX(0)';
    showEl.style.opacity='1';
    hideEl.style.transform = `translateX(${-dir*30}%)`;
    hideEl.style.opacity='0';
  });
  slotFlag = slotFlag==='A' ? 'B' : 'A';
  paintRail(0);
  startDwell(step);
}

function advance(){
  if(cur < STEPS.length-1) goTo(cur+1,true);
  else goTo(0,true);
}
function retreat(){ goTo(cur-1,true); }

function clearTimers(){
  if(dwellTimer) clearTimeout(dwellTimer);
  if(rafId) cancelAnimationFrame(rafId);
}

function startDwell(step){
  clearTimers();
  dwellStart = performance.now();
  dwellDur = step.dwell;
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function tick(now){
    if(!playing) { rafId = requestAnimationFrame(tick); dwellStart = now - progressElapsed(); return; }
    const el = now - dwellStart;
    const p = Math.min(1, el/dwellDur);
    paintRail(p);
    if(step.autoFlip && !manualThemeTouched && el>=step.autoFlip && el < step.autoFlip+50){
      const seg = document.querySelector('.slot:not([style*="opacity: 0"]) #themeSeg') || document.querySelector('#themeSeg');
      if(seg) setThemeSeg(seg,'light');
    }
    if(playing && el>=dwellDur && !reduced){
      advance();
      return;
    }
    rafId = requestAnimationFrame(tick);
  }
  let pausedAt = 0;
  function progressElapsed(){ return pausedAt; }
  rafId = requestAnimationFrame(tick);
  if(reduced){ playing=false; updatePlayIcon(); paintRail(0); }
}

function updatePlayIcon(){
  btnPlay.innerHTML = playing
    ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="#fff"><rect x="5" y="4" width="5" height="16" rx="1.5"/><rect x="14" y="4" width="5" height="16" rx="1.5"/></svg>'
    : '<svg width="16" height="16" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>';
}

document.getElementById('btnPlay').addEventListener('click',()=>{
  playing = !playing;
  updatePlayIcon();
});
document.getElementById('btnNext').addEventListener('click',()=>advance());
document.getElementById('btnPrev').addEventListener('click',()=>retreat());
document.getElementById('btnRestart').addEventListener('click',()=>{playing=true;updatePlayIcon();goTo(0,true);});
document.addEventListener('keydown',(e)=>{
  if(e.key==='ArrowRight') advance();
  if(e.key==='ArrowLeft') retreat();
  if(e.key===' '){ e.preventDefault(); playing=!playing; updatePlayIcon(); }
});

/* init */
(function init(){
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  playing = !reduced;
  updatePlayIcon();
  const slotA = document.getElementById('slotA');
  phoneScreen.dataset.theme = STEPS[0].theme;
  renderInto(slotA, STEPS[0]);
  paintRail(0);
  startDwell(STEPS[0]);
})();
})();
</script>

<?php get_footer(); ?>
