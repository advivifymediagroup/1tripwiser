<?php get_header(); ?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
  /* brand */
  --green: #306C35;
  --blue:  #0692AF;
  --gold:  #FCB415;

  /* surfaces */
  --page-bg:    #f5f8fa;
  --card-bg:    #ffffff;
  --raised-bg:  #f0f4f8;
  --input-bg:   #ffffff;

  /* text */
  --text:       #1a2535;
  --text-head:  #0D1526;
  --muted:      #6b7a8f;
  --label:      #374151;

  /* borders */
  --border:     #e0e8f0;
  --border-mid: #ccd7e4;

  /* hero */
  --hero-start: #ffffff;
  --hero-mid:   rgba(6,146,175,0.04);

  /* summary box */
  --sum-bg:     rgba(6,146,175,0.06);
  --sum-border: rgba(6,146,175,0.18);
  --sum-val:    #0D1526;

  /* progress circles */
  --circle-bg:  #e8eef4;

  /* back button */
  --back-bg:    #ffffff;
  --back-hover: #0D1526;

  /* select arrow (dark stroke for light bg) */
  --arrow-url: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7a8f' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");

  /* card shadow */
  --card-shadow: 0 6px 24px rgba(0,0,0,0.07);

  /* reset button */
  --reset-color: rgba(26,37,53,0.35);
}

@media (prefers-color-scheme: dark) {
  :root {
    /* surfaces */
    --page-bg:    #0d1526;
    --card-bg:    #111e33;
    --raised-bg:  #172240;
    --input-bg:   #0f1a2e;

    /* text */
    --text:       rgba(255,255,255,0.92);
    --text-head:  #ffffff;
    --muted:      rgba(255,255,255,0.45);
    --label:      rgba(255,255,255,0.6);

    /* borders */
    --border:     rgba(255,255,255,0.08);
    --border-mid: rgba(255,255,255,0.13);

    /* hero */
    --hero-start: #0d1526;
    --hero-mid:   rgba(6,146,175,0.08);

    /* summary box */
    --sum-bg:     rgba(6,146,175,0.08);
    --sum-border: rgba(6,146,175,0.25);
    --sum-val:    rgba(255,255,255,0.92);

    /* progress circles */
    --circle-bg:  #172240;

    /* back button */
    --back-bg:    transparent;
    --back-hover: #ffffff;

    /* select arrow (light stroke for dark bg) */
    --arrow-url: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='rgba(255,255,255,0.4)' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");

    /* card shadow */
    --card-shadow: 0 8px 32px rgba(0,0,0,0.4);

    /* reset button */
    --reset-color: rgba(255,255,255,0.35);
  }

  /* select option background (can't use CSS vars here) */
  select.fi option { background: #111e33; }
}

/* ═══════════════════════════════════════════
   COMPONENT STYLES (theme-agnostic via vars)
═══════════════════════════════════════════ */

body { background-color: var(--page-bg) !important; }

/* ── Plan hero ── */
.plan-hero {
  background: linear-gradient(135deg, var(--hero-start) 0%, var(--hero-mid) 100%);
  padding: 60px 40px 44px;
  text-align: center;
  border-bottom: 1px solid var(--border);
  position: relative;
  overflow: hidden;
}
.plan-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 75% 50%, rgba(6,146,175,0.07) 0%, transparent 55%),
              radial-gradient(circle at 20% 80%, rgba(252,180,21,0.04) 0%, transparent 50%);
  pointer-events: none;
}
.plan-hero-kicker {
  display: inline-block;
  position: relative;
  font-family: 'Nunito', sans-serif;
  font-size: 11px;
  font-weight: 700;
  color: var(--blue);
  text-transform: uppercase;
  letter-spacing: 3px;
  margin-bottom: 14px;
}
.plan-hero-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(42px, 6vw, 66px);
  letter-spacing: 3px;
  color: var(--text-head);
  line-height: 1;
  margin: 0 0 12px;
  position: relative;
}
.plan-hero-title .accent { color: var(--gold); }
.plan-hero-sub {
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  color: var(--muted);
  font-weight: 400;
  max-width: 500px;
  margin: 0 auto;
  position: relative;
}

/* ── Wrapper ── */
.plan-body {
  max-width: 700px;
  margin: 0 auto 60px;
  padding: 32px 20px 0;
}

/* ── Progress track ── */
.progress-track {
  display: flex;
  align-items: center;
  margin-bottom: 28px;
}
.ps {
  flex: 1;
  text-align: center;
  position: relative;
}
.ps::after {
  content: '';
  position: absolute;
  top: 17px;
  left: 50%;
  right: -50%;
  height: 2px;
  background: var(--border-mid);
  z-index: 0;
  transition: background 0.4s;
}
.ps:last-child::after { display: none; }
.ps.done::after { background: var(--green); }

.pc {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--circle-bg);
  color: var(--muted);
  font-family: 'Nunito', sans-serif;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 6px;
  position: relative;
  z-index: 1;
  border: 2px solid var(--border-mid);
  transition: all 0.3s;
}
.ps.active .pc { background: var(--blue);  color: #fff; border-color: var(--blue); }
.ps.done   .pc { background: var(--green); color: #fff; border-color: var(--green); }

.ps-lbl {
  font-family: 'Nunito', sans-serif;
  font-size: 10px;
  color: var(--muted);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.ps.active .ps-lbl,
.ps.done   .ps-lbl { color: var(--text); }

/* ── Card ── */
.plan-card {
  background: var(--card-bg);
  border-radius: 18px;
  padding: 36px 32px;
  border: 1px solid var(--border-mid);
  box-shadow: var(--card-shadow);
}
.plan-step         { display: none; }
.plan-step.active  { display: block; }

.ps-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 30px;
  letter-spacing: 2px;
  color: var(--text-head);
  margin-bottom: 4px;
}
.ps-sub {
  font-family: 'Nunito', sans-serif;
  font-size: 12px;
  color: var(--muted);
  margin-bottom: 22px;
  font-weight: 400;
}

/* ── Tile grids ── */
.tile-g3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.tile-g2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }

/* ── Destination / type tiles ── */
.ptile {
  border: 1.5px solid var(--border-mid);
  border-radius: 12px;
  padding: 16px 10px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background: var(--raised-bg);
  font-family: 'Nunito', sans-serif;
}
.ptile:hover { border-color: var(--blue); background: rgba(6,146,175,0.08); }
.ptile.sel   { border-color: var(--gold); background: rgba(252,180,21,0.09); }
.ptile-em { font-size: 26px; margin-bottom: 7px; }
.ptile-n  { font-size: 12px; font-weight: 800; color: var(--text); }
.ptile-s  { font-size: 10px; color: var(--muted); margin-top: 2px; }
.ptile.sel .ptile-n { color: var(--gold); }

/* ── Form fields ── */
.fg  { margin-bottom: 14px; }
.fl2 {
  font-family: 'Nunito', sans-serif;
  font-size: 11px;
  font-weight: 800;
  color: var(--label);
  margin-bottom: 5px;
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.fi {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid var(--border-mid);
  border-radius: 10px;
  font-family: 'Nunito', sans-serif;
  font-size: 13px;
  color: var(--text);
  background: var(--input-bg);
  outline: none;
  transition: border-color 0.2s, background 0.2s;
  box-sizing: border-box;
  -webkit-appearance: none;
  appearance: none;
}
.fi:focus        { border-color: var(--blue); }
.fi::placeholder { color: var(--muted); }

select.fi {
  background-image: var(--arrow-url);
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 38px;
}

.fr { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }

/* ── Time tiles ── */
.ttile {
  border: 1.5px solid var(--border-mid);
  border-radius: 12px;
  padding: 14px 10px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background: var(--raised-bg);
  font-family: 'Nunito', sans-serif;
}
.ttile:hover { border-color: var(--blue); background: rgba(6,146,175,0.08); }
.ttile.sel   { border-color: var(--gold); background: rgba(252,180,21,0.09); }
.ttile-em { font-size: 22px; margin-bottom: 5px; }
.ttile-n  { font-size: 11px; font-weight: 800; color: var(--text); }
.ttile-s  { font-size: 10px; color: var(--muted); }
.ttile.sel .ttile-n { color: var(--gold); }

/* ── Budget tiles ── */
.btile {
  border: 1.5px solid var(--border-mid);
  border-radius: 12px;
  padding: 14px 16px;
  cursor: pointer;
  transition: all 0.2s;
  background: var(--raised-bg);
  font-family: 'Nunito', sans-serif;
}
.btile:hover { border-color: var(--blue); background: rgba(6,146,175,0.08); }
.btile.sel   { border-color: var(--gold); background: rgba(252,180,21,0.09); }
.bt-name  { font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 3px; }
.bt-range { font-size: 11px; color: var(--muted); }
.btile.sel .bt-name { color: var(--gold); }

/* ── Summary box ── */
.sum-box {
  background: var(--sum-bg);
  border: 1px solid var(--sum-border);
  border-radius: 12px;
  padding: 16px;
  margin: 16px 0;
}
.sum-title {
  font-family: 'Nunito', sans-serif;
  font-size: 11px;
  font-weight: 800;
  color: var(--blue);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 10px;
}
.sum-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 7px;
}
.sum-row:last-child { margin-bottom: 0; }
.sum-k { font-family: 'Nunito', sans-serif; font-size: 12px; color: var(--muted); }
.sum-v { font-family: 'Nunito', sans-serif; font-size: 12px; font-weight: 800; color: var(--sum-val); }

/* ── Nav buttons ── */
.plan-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 24px;
  gap: 10px;
}
.btn-back {
  font-family: 'Nunito', sans-serif;
  font-size: 12px;
  color: var(--muted);
  padding: 11px 22px;
  border: 1.5px solid var(--border-mid);
  border-radius: 22px;
  cursor: pointer;
  background: var(--back-bg);
  font-weight: 700;
  transition: all 0.2s;
}
.btn-back:hover { color: var(--back-hover); border-color: var(--blue); }
.btn-nx {
  font-family: 'Nunito', sans-serif;
  font-size: 13px;
  font-weight: 800;
  padding: 12px 28px;
  border-radius: 22px;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-primary       { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #07a8cc; }
.btn-wa {
  background: #25d366;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
}
.btn-wa:hover { background: #1da851; transform: translateY(-1px); }

/* ── Divider ── */
.step-divider {
  border: none;
  border-top: 1px solid var(--border);
  margin: 20px 0;
}

/* ── Section label ── */
.sec-label {
  font-family: 'Nunito', sans-serif;
  font-size: 11px;
  font-weight: 800;
  color: var(--label);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 18px 0 8px;
  display: block;
}

/* ── Success ── */
.success-box { text-align: center; padding: 16px 0; }
.succ-icon   { font-size: 58px; margin-bottom: 12px; }
.succ-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 38px;
  letter-spacing: 2px;
  color: var(--text-head);
  margin-bottom: 8px;
}
.succ-sub {
  font-family: 'Nunito', sans-serif;
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 22px;
  line-height: 1.8;
  font-weight: 400;
  max-width: 420px;
  margin-left: auto;
  margin-right: auto;
}
.wa-launch {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #25d366;
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 800;
  padding: 14px 32px;
  border-radius: 32px;
  text-decoration: none;
  box-shadow: 0 6px 20px rgba(37,211,102,0.35);
  transition: all 0.3s;
  margin-bottom: 28px;
}
.wa-launch:hover { background: #1da851; transform: translateY(-2px); color: #fff; }
.succ-stats {
  display: flex;
  justify-content: center;
  gap: 32px;
  margin-top: 20px;
}
.succ-stat-num {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 26px;
  color: var(--gold);
}
.succ-stat-lbl {
  font-family: 'Nunito', sans-serif;
  font-size: 10px;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

@media (max-width: 600px) {
  .plan-card { padding: 24px 18px; }
  .tile-g3   { grid-template-columns: repeat(2, 1fr); }
  .fr        { grid-template-columns: 1fr; }
  .plan-hero { padding: 40px 20px 32px; }
}
</style>

<div class="plan-hero">
  <div class="plan-hero-kicker">Free · No obligation · 24-hour response</div>
  <h1 class="plan-hero-title">PLAN YOUR <span class="accent">PERFECT TRIP</span></h1>
  <p class="plan-hero-sub">Answer 3 quick questions — we'll send a custom quote to your WhatsApp within 24 hours.</p>
</div>

<div class="plan-body">

  <div class="progress-track" id="ptrack">
    <div class="ps active" id="ps1">
      <div class="pc">1</div>
      <div class="ps-lbl">Destination</div>
    </div>
    <div class="ps" id="ps2">
      <div class="pc">2</div>
      <div class="ps-lbl">Date & Time</div>
    </div>
    <div class="ps" id="ps3">
      <div class="pc">3</div>
      <div class="ps-lbl">Budget & Quote</div>
    </div>
  </div>

  <div class="plan-card">

    <div class="plan-step active" id="pstep1">
      <div class="ps-title">WHERE DO YOU WANT TO GO?</div>
      <div class="ps-sub">Pick a destination or type your own below</div>

      <div class="tile-g3" id="dest-grid">
        <div class="ptile sel" data-v="Bali">
          <div class="ptile-em">🌴</div>
          <div class="ptile-n">Bali</div>
          <div class="ptile-s">Indonesia</div>
        </div>
        <div class="ptile" data-v="Kashmir">
          <div class="ptile-em">🌸</div>
          <div class="ptile-n">Kashmir</div>
          <div class="ptile-s">India</div>
        </div>
        <div class="ptile" data-v="Europe">
          <div class="ptile-em">🏰</div>
          <div class="ptile-n">Europe</div>
          <div class="ptile-s">Multi-country</div>
        </div>
        <div class="ptile" data-v="Ladakh">
          <div class="ptile-em">🏔️</div>
          <div class="ptile-n">Ladakh</div>
          <div class="ptile-s">India</div>
        </div>
        <div class="ptile" data-v="Maldives">
          <div class="ptile-em">🐠</div>
          <div class="ptile-n">Maldives</div>
          <div class="ptile-s">Islands</div>
        </div>
        <div class="ptile" data-v="Meghalaya">
          <div class="ptile-em">🌿</div>
          <div class="ptile-n">Meghalaya</div>
          <div class="ptile-s">India</div>
        </div>
      </div>

      <div class="fg" style="margin-top: 16px;">
        <label class="fl2">Or type your destination</label>
        <input class="fi" type="text" id="custom-dest" placeholder="e.g. Thailand, Rajasthan, Japan...">
      </div>

      <div class="plan-nav">
        <span></span>
        <button class="btn-nx btn-primary" onclick="goToStep(2)">Next: Date &amp; Time &rarr;</button>
      </div>
    </div>

    <div class="plan-step" id="pstep2">
      <div class="ps-title">WHEN ARE YOU TRAVELLING?</div>
      <div class="ps-sub">Pick your dates and preferred time of travel</div>

      <div class="fr">
        <div class="fg" style="margin-bottom: 0;">
          <label class="fl2">Departure Date</label>
          <input class="fi" type="date" id="p-date">
        </div>
        <div class="fg" style="margin-bottom: 0;">
          <label class="fl2">Trip Duration</label>
          <select class="fi" id="p-dur">
            <option>3–4 nights</option>
            <option>5–7 nights</option>
            <option selected>8–10 nights</option>
            <option>11–14 nights</option>
            <option>15+ nights</option>
          </select>
        </div>
      </div>

      <span class="sec-label">Preferred Time of Travel</span>
      <div class="tile-g3" id="time-grid">
        <div class="ttile sel" data-v="Morning">
          <div class="ttile-em">🌅</div>
          <div class="ttile-n">Morning</div>
          <div class="ttile-s">6am – 12pm</div>
        </div>
        <div class="ttile" data-v="Afternoon">
          <div class="ttile-em">☀️</div>
          <div class="ttile-n">Afternoon</div>
          <div class="ttile-s">12pm – 6pm</div>
        </div>
        <div class="ttile" data-v="Evening">
          <div class="ttile-em">🌙</div>
          <div class="ttile-n">Evening</div>
          <div class="ttile-s">6pm – midnight</div>
        </div>
      </div>

      <span class="sec-label" style="margin-top: 20px;">Trip Type</span>
      <div class="tile-g3" id="type-grid">
        <div class="ptile sel" data-v="Leisure">
          <div class="ptile-em">🌅</div>
          <div class="ptile-n">Leisure</div>
        </div>
        <div class="ptile" data-v="Honeymoon">
          <div class="ptile-em">💍</div>
          <div class="ptile-n">Honeymoon</div>
        </div>
        <div class="ptile" data-v="Adventure">
          <div class="ptile-em">🧗</div>
          <div class="ptile-n">Adventure</div>
        </div>
      </div>

      <span class="sec-label" style="margin-top: 20px;">Who's Travelling?</span>
      <div class="fr">
        <div class="fg" style="margin-bottom: 0;">
          <label class="fl2">Adults</label>
          <select class="fi" id="p-adults">
            <option>1 (Solo)</option>
            <option selected>2</option>
            <option>3</option>
            <option>4</option>
            <option>5–8</option>
            <option>9+</option>
          </select>
        </div>
        <div class="fg" style="margin-bottom: 0;">
          <label class="fl2">Children</label>
          <select class="fi" id="p-child">
            <option selected>None</option>
            <option>1</option>
            <option>2</option>
            <option>3+</option>
          </select>
        </div>
      </div>

      <div class="plan-nav">
        <button class="btn-back" onclick="goToStep(1)">&larr; Back</button>
        <button class="btn-nx btn-primary" onclick="goToStep(3)">Next: Budget &amp; Quote &rarr;</button>
      </div>
    </div>

    <div class="plan-step" id="pstep3">
      <div class="ps-title">YOUR BUDGET &amp; DETAILS</div>
      <div class="ps-sub">Per person · We only show options in your range</div>

      <div class="tile-g2" id="budget-grid">
        <div class="btile" data-v="Budget — Under ₹25,000">
          <div class="bt-name">🎒 Budget</div>
          <div class="bt-range">Under ₹25,000 / person</div>
        </div>
        <div class="btile sel" data-v="Mid-range — ₹25K–₹60K">
          <div class="bt-name">🌟 Mid-range</div>
          <div class="bt-range">₹25,000–₹60,000 / person</div>
        </div>
        <div class="btile" data-v="Premium — ₹60K–₹1.5L">
          <div class="bt-name">💎 Premium</div>
          <div class="bt-range">₹60,000–₹1,50,000 / person</div>
        </div>
        <div class="btile" data-v="Luxury — Above ₹1.5L">
          <div class="bt-name">👑 Luxury</div>
          <div class="bt-range">Above ₹1,50,000 / person</div>
        </div>
      </div>

      <div class="fg" style="margin-top: 16px;">
        <label class="fl2">Departing From</label>
        <input class="fi" type="text" id="p-from" placeholder="e.g. Delhi, Mumbai, Bangalore...">
      </div>

      <hr class="step-divider">

      <span class="sec-label">Where should we send your quote?</span>

      <div class="fr">
        <div class="fg" style="margin-bottom: 0;">
          <label class="fl2">Full Name</label>
          <input class="fi" type="text" id="p-name" placeholder="Your name">
        </div>
        <div class="fg" style="margin-bottom: 0;">
          <label class="fl2">WhatsApp Number</label>
          <input class="fi" type="tel" id="p-phone" placeholder="+91 98765 43210">
        </div>
      </div>
      <div class="fg">
        <label class="fl2">Email Address</label>
        <input class="fi" type="email" id="p-email" placeholder="your@email.com">
      </div>
      <div class="fg">
        <label class="fl2">Special Requests <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);">(optional)</span></label>
        <textarea class="fi" id="p-notes" rows="2" placeholder="Vegetarian meals, anniversary surprise, wheelchair access..." style="resize: none;"></textarea>
      </div>

      <div class="sum-box" id="sum-box">
        <div class="sum-title">📋 Your Trip Summary</div>
        <div class="sum-row"><span class="sum-k">Destination</span><span class="sum-v" id="s-dest">—</span></div>
        <div class="sum-row"><span class="sum-k">Departure Date</span><span class="sum-v" id="s-date">—</span></div>
        <div class="sum-row"><span class="sum-k">Travel Time</span><span class="sum-v" id="s-time">—</span></div>
        <div class="sum-row"><span class="sum-k">Duration</span><span class="sum-v" id="s-dur">—</span></div>
        <div class="sum-row"><span class="sum-k">Travellers</span><span class="sum-v" id="s-pax">—</span></div>
        <div class="sum-row"><span class="sum-k">Budget</span><span class="sum-v" id="s-budget">—</span></div>
      </div>

      <div class="plan-nav">
        <button class="btn-back" onclick="goToStep(2)">&larr; Back</button>
        <button class="btn-nx btn-wa" onclick="submitTrip()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          Get My Quote on WhatsApp
        </button>
      </div>
      <div style="text-align:center;margin-top:10px;font-family:'Nunito',sans-serif;font-size:11px;color:var(--muted);">
        No payment required &middot; Free custom itinerary in 24 hours
      </div>
    </div>

    <div class="plan-step" id="pstep-success">
      <div class="success-box">
        <div class="succ-icon">🎉</div>
        <div class="succ-title">YOUR TRIP IS READY!</div>
        <p class="succ-sub">
          Click below to open WhatsApp and send us your trip details. Our team will craft a custom itinerary and reply within 24 hours — completely free.
        </p>
        <a class="wa-launch" id="wa-link" href="#" target="_blank">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          Open WhatsApp &amp; Send →
        </a>
        <div class="succ-stats">
          <div>
            <div class="succ-stat-num">24hrs</div>
            <div class="succ-stat-lbl">Response</div>
          </div>
          <div>
            <div class="succ-stat-num">₹0</div>
            <div class="succ-stat-lbl">Planning Fee</div>
          </div>
          <div>
            <div class="succ-stat-num">300K+</div>
            <div class="succ-stat-lbl">Community</div>
          </div>
        </div>
        <div style="margin-top:24px;">
          <button onclick="resetForm()" style="font-family:'Nunito',sans-serif;font-size:12px;color:var(--reset-color);background:transparent;border:none;cursor:pointer;text-decoration:underline;">
            Plan another trip
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function () {
  var tripData = {
    dest:   'Bali',
    date:   '',
    time:   'Morning',
    dur:    '8–10 nights',
    type:   'Leisure',
    adults: '2',
    child:  'None',
    budget: 'Mid-range — ₹25K–₹60K',
    from:   '',
    name:   '',
    phone:  '',
    email:  '',
    notes:  ''
  };
  var currentStep = 1;

  function initTiles(gridId) {
    var g = document.getElementById(gridId);
    if (!g) return;
    g.addEventListener('click', function (e) {
      var t = e.target.closest('.ptile, .ttile, .btile');
      if (!t) return;
      g.querySelectorAll('.ptile, .ttile, .btile').forEach(function (x) {
        x.classList.remove('sel');
      });
      t.classList.add('sel');
    });
  }

  ['dest-grid', 'time-grid', 'type-grid', 'budget-grid'].forEach(initTiles);

  function collectData() {
    /* Step 1 */
    var customDest = document.getElementById('custom-dest');
    var selDest    = document.querySelector('#dest-grid .ptile.sel');
    if (customDest && customDest.value.trim()) {
      tripData.dest = customDest.value.trim();
    } else if (selDest) {
      tripData.dest = selDest.dataset.v;
    }

    var d = document.getElementById('p-date');
    var dur = document.getElementById('p-dur');
    var adults = document.getElementById('p-adults');
    var child = document.getElementById('p-child');
    var selTime = document.querySelector('#time-grid .ttile.sel');
    var selType = document.querySelector('#type-grid .ptile.sel');

    if (d && d.value) {
      /* Format date nicely */
      var parts = d.value.split('-');
      if (parts.length === 3) {
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        tripData.date = parts[2] + ' ' + months[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
      } else {
        tripData.date = d.value;
      }
    } else {
      tripData.date = 'Flexible';
    }
    if (dur)    tripData.dur    = dur.value;
    if (adults) tripData.adults = adults.value;
    if (child)  tripData.child  = child.value;
    if (selTime) tripData.time  = selTime.dataset.v;
    if (selType) tripData.type  = selType.dataset.v;

    var selBudget = document.querySelector('#budget-grid .btile.sel');
    var fromEl  = document.getElementById('p-from');
    var nameEl  = document.getElementById('p-name');
    var phoneEl = document.getElementById('p-phone');
    var emailEl = document.getElementById('p-email');
    var notesEl = document.getElementById('p-notes');

    if (selBudget) tripData.budget = selBudget.dataset.v;
    if (fromEl)  tripData.from  = fromEl.value.trim() || 'Not specified';
    if (nameEl)  tripData.name  = nameEl.value.trim() || 'Traveler';
    if (phoneEl) tripData.phone = phoneEl.value.trim();
    if (emailEl) tripData.email = emailEl.value.trim();
    if (notesEl) tripData.notes = notesEl.value.trim();
  }

  function updateSummary() {
    var pax = tripData.adults + ' adult' + (tripData.adults !== '1 (Solo)' ? 's' : '');
    if (tripData.child && tripData.child !== 'None') {
      pax += ' · ' + tripData.child + ' child' + (tripData.child === '1' ? '' : 'ren');
    }
    document.getElementById('s-dest').textContent   = tripData.dest;
    document.getElementById('s-date').textContent   = tripData.date || 'Flexible';
    document.getElementById('s-time').textContent   = tripData.time + ' flight';
    document.getElementById('s-dur').textContent    = tripData.dur;
    document.getElementById('s-pax').textContent    = pax;
    document.getElementById('s-budget').textContent = tripData.budget.split('—')[0].trim();
  }

  window.goToStep = function (target) {
    collectData();

    var cur = document.getElementById('ps' + currentStep);
    if (cur) { cur.classList.remove('active'); cur.classList.add('done'); }
    document.getElementById('pstep' + currentStep).classList.remove('active');

    currentStep = target;
    var tgt = document.getElementById('ps' + target);
    if (tgt) { tgt.classList.remove('done'); tgt.classList.add('active'); }

    var stepEl = document.getElementById('pstep' + target);
    if (stepEl) stepEl.classList.add('active');

    if (target === 3) updateSummary();

    document.querySelector('.plan-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  };

  window.submitTrip = function () {
    collectData();

    var pax = tripData.adults;
    if (tripData.child && tripData.child !== 'None') {
      pax += ', ' + tripData.child + ' children';
    }

    var msg = [
      'Hi 1tripwiser! 🌍 I want to plan a trip.',
      '',
      '*Destination:* ' + tripData.dest,
      '*Departure Date:* ' + (tripData.date || 'Flexible'),
      '*Preferred Travel Time:* ' + tripData.time,
      '*Duration:* ' + tripData.dur,
      '*Trip Type:* ' + tripData.type,
      '*Travellers:* ' + pax,
      '*Departing From:* ' + tripData.from,
      '*Budget:* ' + tripData.budget,
      '*Name:* ' + tripData.name,
      '*Email:* ' + (tripData.email || 'Not provided'),
      tripData.notes ? '*Special Requests:* ' + tripData.notes : '',
      '',
      'Please send me a custom itinerary! 🙏'
    ].filter(function (l) { return l !== null; }).join('\n');

    
    document.getElementById('pstep3').classList.remove('active');
    document.getElementById('ps3').classList.remove('active');
    document.getElementById('ps3').classList.add('done');
    document.getElementById('pstep-success').classList.add('active');

    /* Build WhatsApp link — replace with real number */
    document.getElementById('wa-link').href =
      'https://wa.me/919999999999?text=' + encodeURIComponent(msg);

    document.querySelector('.plan-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  };

  window.resetForm = function () {
    location.reload();
  };
})();
</script>

<?php get_footer(); ?>
