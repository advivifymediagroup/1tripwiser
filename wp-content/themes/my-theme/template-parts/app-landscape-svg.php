<?php
/**
 * Hand-drawn Alps/lake landscape illustration used inside the phone mockup
 * on the App Coming Soon page (TripBook, Home Feed and Post Boosting
 * slides) — included multiple times per page, so gradient IDs are suffixed
 * uniquely per call to keep them valid when several copies share one DOM.
 * Matches the ADVIVIFY Figma export exactly.
 */
static $app2_landscape_i = 0;
$app2_landscape_i++;
$u = $app2_landscape_i;
?>
<svg class="app2-landscape" viewBox="0 0 292 212" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs>
        <linearGradient id="app2-sky-<?php echo $u; ?>" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#4E6076"></stop>
            <stop offset="45%" stop-color="#8A9AAB"></stop>
            <stop offset="72%" stop-color="#C2CBD2"></stop>
            <stop offset="100%" stop-color="#9FAAB4"></stop>
        </linearGradient>
        <linearGradient id="app2-far-<?php echo $u; ?>" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#3D4C60"></stop>
            <stop offset="100%" stop-color="#7E8C9B"></stop>
        </linearGradient>
        <linearGradient id="app2-near-<?php echo $u; ?>" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#2B3746"></stop>
            <stop offset="100%" stop-color="#4C5A6A"></stop>
        </linearGradient>
        <linearGradient id="app2-water-<?php echo $u; ?>" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#9BA9B4"></stop>
            <stop offset="100%" stop-color="#3A4653"></stop>
        </linearGradient>
        <linearGradient id="app2-mist-<?php echo $u; ?>" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="rgba(255,255,255,0)"></stop>
            <stop offset="55%" stop-color="rgba(232,238,242,0.72)"></stop>
            <stop offset="100%" stop-color="rgba(232,238,242,0)"></stop>
        </linearGradient>
    </defs>
    <rect width="292" height="212" fill="url(#app2-sky-<?php echo $u; ?>)"></rect>
    <polygon points="-10,118 44,60 92,96 132,52 186,110 232,74 302,120 302,150 -10,150" fill="url(#app2-far-<?php echo $u; ?>)" opacity="0.85"></polygon>
    <polygon points="-10,140 38,96 84,128 126,92 178,134 226,104 302,146 302,166 -10,166" fill="url(#app2-near-<?php echo $u; ?>)" opacity="0.9"></polygon>
    <rect x="-10" y="104" width="312" height="46" fill="url(#app2-mist-<?php echo $u; ?>)"></rect>
    <rect x="0" y="148" width="292" height="64" fill="url(#app2-water-<?php echo $u; ?>)"></rect>
    <rect x="0" y="148" width="292" height="3" fill="rgba(255,255,255,0.25)"></rect>
    <g fill="rgba(255,255,255,0.12)">
        <rect x="24" y="162" width="58" height="1.6" rx="0.8"></rect>
        <rect x="140" y="172" width="80" height="1.6" rx="0.8"></rect>
        <rect x="60" y="184" width="46" height="1.6" rx="0.8"></rect>
    </g>
    <rect width="292" height="212" fill="rgba(10,14,20,0.06)"></rect>
</svg>
