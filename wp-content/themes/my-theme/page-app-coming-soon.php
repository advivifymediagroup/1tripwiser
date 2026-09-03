<?php
/**
 * Template Name: App Coming Soon
 * Styles are in style.css (APP — COMING SOON PAGE section)
 */
get_header();
?>

<section class="app-hero" id="app-hero">
    <canvas id="app-3d-canvas" class="app-3d-canvas" aria-hidden="true"></canvas>

    <div class="app-hero-inner">
        <span class="app-kicker" data-anime="kicker"><span class="dot" aria-hidden="true"></span> Launching Soon</span>
        <h1 class="app-hero-title" data-anime="title">Your next trip<br>fits in your <span class="accent">pocket.</span></h1>
        <p class="app-hero-sub" data-anime="sub">
            The 1TripWiser app is almost here — plan itineraries with Wisey AI, drop Secret Pins,
            earn WiseCoins and share every trip with a community of 300K+ travellers.
        </p>

        <div class="app-hero-actions" data-anime="actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary">Explore the Website</a>
            <a href="https://www.instagram.com/1tripwiser/" target="_blank" rel="noopener" class="btn-secondary">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i> Follow for updates
            </a>
        </div>

        <div class="app-store-badges" data-anime="badges">
            <span class="app-store-badge"><i class="fa-brands fa-apple" aria-hidden="true"></i> App Store — Coming Soon</span>
            <span class="app-store-badge"><i class="fa-brands fa-google-play" aria-hidden="true"></i> Google Play — Coming Soon</span>
        </div>

        <!-- Phone demo — recreated from the live app build -->
        <div class="app-phone-stage" id="app-phone-stage">
            <div class="app-phone-glow" aria-hidden="true"></div>
            <div class="app-phone" id="app-phone">
                <span class="app-phone-live-badge"><span class="dot" aria-hidden="true"></span> Live preview</span>
                <div class="app-phone-notch" aria-hidden="true"></div>
                <div class="app-phone-screen">
                    <div class="app-phone-peaks" aria-hidden="true"></div>
                    <div class="app-phone-content">
                        <div class="app-phone-icon"><i class="fa-solid fa-location-arrow" aria-hidden="true"></i></div>
                        <div class="app-phone-brand"><span>1</span>TRIPWISER</div>
                        <div class="app-phone-tag">Wiser trips &middot; Better memories</div>
                        <div class="app-phone-headline">Every trip deserves<br>a better story.</div>
                        <div class="app-phone-avatars">
                            <span class="stack"><span></span><span></span><span></span></span>
                            Join travellers mapping the world
                        </div>
                        <div class="app-phone-card">
                            <div class="app-phone-card-title">Welcome back</div>
                            <div class="app-phone-card-sub">Sign in to pick up where you left off</div>
                            <div class="app-phone-field"><i class="fa-regular fa-envelope" aria-hidden="true"></i> Email</div>
                            <div class="app-phone-field"><i class="fa-solid fa-lock" aria-hidden="true"></i> Password</div>
                            <div class="app-phone-btn">Sign In &rarr;</div>
                        </div>
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
                <h3>WiseCoins &amp; Rewards</h3>
                <p>Earn coins for check-ins, cheers and posts — redeem them for real lounge access, vouchers and discounts.</p>
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

        /* Wireframe globe */
        var globe = new THREE.Mesh(
            new THREE.IcosahedronGeometry(3.4, 2),
            new THREE.MeshBasicMaterial({ color: 0x25d7f2, wireframe: true, transparent: true, opacity: 0.16 })
        );
        scene.add(globe);

        /* Particle points scattered around it */
        var particleCount = 220;
        var positions = new Float32Array(particleCount * 3);
        for (var i = 0; i < particleCount; i++) {
            var r = 4.4 + Math.random() * 2.2;
            var theta = Math.random() * Math.PI * 2;
            var phi = Math.acos((Math.random() * 2) - 1);
            positions[i * 3]     = r * Math.sin(phi) * Math.cos(theta);
            positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
            positions[i * 3 + 2] = r * Math.cos(phi);
        }
        var particleGeo = new THREE.BufferGeometry();
        particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        var particleMat = new THREE.PointsMaterial({ color: 0xe5127d, size: 0.05, transparent: true, opacity: 0.75 });
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

        function animate() {
            requestAnimationFrame(animate);
            globe.rotation.y += 0.0016;
            globe.rotation.x += 0.0005;
            particles.rotation.y -= 0.0009;
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
            .add({ targets: '[data-anime="badges"]', opacity: [0, 1], translateY: [16, 0], duration: 700 }, '-=550')
            .add({ targets: '#app-phone-stage', opacity: [0, 1], scale: [0.92, 1], duration: 900 }, '-=600');

        if (reduceMotion) return;

        var phone = document.getElementById('app-phone');
        var stage = document.getElementById('app-phone-stage');

        /* Idle float */
        anime({
            targets: phone,
            translateY: [-10, 10],
            duration: 3600,
            easing: 'easeInOutSine',
            direction: 'alternate',
            loop: true
        });

        /* Pointer-tracked 3D tilt */
        if (stage) {
            stage.addEventListener('mousemove', function (e) {
                var rect = stage.getBoundingClientRect();
                var px = (e.clientX - rect.left) / rect.width - 0.5;
                var py = (e.clientY - rect.top) / rect.height - 0.5;
                anime({
                    targets: phone,
                    rotateY: px * 22,
                    rotateX: py * -22,
                    duration: 400,
                    easing: 'easeOutQuad'
                });
            });
            stage.addEventListener('mouseleave', function () {
                anime({ targets: phone, rotateY: 0, rotateX: 0, duration: 600, easing: 'easeOutElastic(1, .6)' });
            });
        }

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

<?php get_footer(); ?>
