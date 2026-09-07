<?php
/**
 * Template Name: Plan a Trip
 * Styles are in style.css
 */
get_header();

// Pull editable content from WP Options (Admin → Trip Inquiries → Page Settings)
$pat_kicker    = get_option('tw_pat_kicker',   'YOUR PERSONALISED TRIP PLANNER');
$pat_title     = get_option('tw_pat_title',    'Plan Your Dream Trip');
$pat_subtitle  = get_option('tw_pat_subtitle', "Tell us your dream destination, travel dates, and budget — we'll craft a personalised itinerary just for you.");
?>

<div class="plan-hero">
    <?php mytheme_breadcrumbs(); ?>
  <div class="plan-hero-kicker"><?php echo esc_html($pat_kicker); ?></div>
  <h1 class="tw-h1 plan-hero-title"><?php
    // Split title at last space to make last word gold-accented
    $words = explode(' ', $pat_title);
    $last  = array_pop($words);
    echo esc_html(implode(' ', $words)) . ' <span class="accent">' . esc_html($last) . '</span>';
  ?></h1>
  <p class="plan-hero-sub"><?php echo esc_html($pat_subtitle); ?></p>
</div>

<div class="plan-body">

  <div class="plan-card">

    <div class="plan-step active" id="pstep-form">
      <div class="ps-title">YOUR CONTACT DETAILS</div>
      <div class="ps-sub">We'll send your personalised itinerary here first</div>

      <div class="fr">
        <div class="fg no-mb">
          <label class="fl2">Full Name</label>
          <input class="fi" type="text" id="p-name" placeholder="Your name">
        </div>
        <div class="fg no-mb">
          <label class="fl2">WhatsApp Number</label>
          <input class="fi" type="tel" id="p-phone" placeholder="+91 98765 43210">
        </div>
      </div>
      <div class="fg">
        <label class="fl2">Email Address</label>
        <input class="fi" type="email" id="p-email" placeholder="your@email.com">
      </div>

      <hr class="step-divider">

      <div class="ps-title">WHERE DO YOU WANT TO GO?</div>
      <div class="ps-sub">Pick a destination or type your own below</div>

      <div class="tile-g3" id="dest-grid">
        <div class="ptile sel" data-v="Bali">
          <div class="ptile-em"><i class="fi-rr-tree" aria-hidden="true"></i></div>
          <div class="ptile-n">Bali</div>
          <div class="ptile-s">Indonesia</div>
        </div>
        <div class="ptile" data-v="Kashmir">
          <div class="ptile-em"><i class="fi-rr-flower" aria-hidden="true"></i></div>
          <div class="ptile-n">Kashmir</div>
          <div class="ptile-s">India</div>
        </div>
        <div class="ptile" data-v="Europe">
          <div class="ptile-em"><i class="fi-rr-castle" aria-hidden="true"></i></div>
          <div class="ptile-n">Europe</div>
          <div class="ptile-s">Multi-country</div>
        </div>
        <div class="ptile" data-v="Ladakh">
          <div class="ptile-em"><i class="fi-rr-mountain" aria-hidden="true"></i></div>
          <div class="ptile-n">Ladakh</div>
          <div class="ptile-s">India</div>
        </div>
        <div class="ptile" data-v="Maldives">
          <div class="ptile-em"><i class="fi-rr-fish" aria-hidden="true"></i></div>
          <div class="ptile-n">Maldives</div>
          <div class="ptile-s">Islands</div>
        </div>
        <div class="ptile" data-v="Meghalaya">
          <div class="ptile-em"><i class="fi-rr-leaf" aria-hidden="true"></i></div>
          <div class="ptile-n">Meghalaya</div>
          <div class="ptile-s">India</div>
        </div>
      </div>

      <div class="fg mt-16">
        <label class="fl2">Or type your destination</label>
        <input class="fi" type="text" id="custom-dest" placeholder="e.g. Thailand, Rajasthan, Japan...">
      </div>

      <hr class="step-divider">

      <div class="ps-title">WHEN ARE YOU TRAVELLING?</div>
      <div class="ps-sub">Pick your dates and preferred time of travel</div>

      <div class="fr">
        <div class="fg no-mb">
          <label class="fl2">Departure Date</label>
          <input class="fi" type="date" id="p-date" value="<?php echo esc_attr( date_i18n( 'Y-m-d' ) ); ?>">
        </div>
        <div class="fg no-mb">
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
          <div class="ttile-em"><i class="fi-rr-sunrise" aria-hidden="true"></i></div>
          <div class="ttile-n">Morning</div>
          <div class="ttile-s">6am – 12pm</div>
        </div>
        <div class="ttile" data-v="Afternoon">
          <div class="ttile-em"><i class="fi-rr-sun" aria-hidden="true"></i></div>
          <div class="ttile-n">Afternoon</div>
          <div class="ttile-s">12pm – 6pm</div>
        </div>
        <div class="ttile" data-v="Evening">
          <div class="ttile-em"><i class="fi-rr-moon" aria-hidden="true"></i></div>
          <div class="ttile-n">Evening</div>
          <div class="ttile-s">6pm – midnight</div>
        </div>
      </div>

      <span class="sec-label mt-20">Trip Type</span>
      <div class="tile-g3" id="type-grid">
        <div class="ptile sel" data-v="Leisure">
          <div class="ptile-em"><i class="fi-rr-sunrise" aria-hidden="true"></i></div>
          <div class="ptile-n">Leisure</div>
        </div>
        <div class="ptile" data-v="Honeymoon">
          <div class="ptile-em"><i class="fi-rr-rings-wedding" aria-hidden="true"></i></div>
          <div class="ptile-n">Honeymoon</div>
        </div>
        <div class="ptile" data-v="Adventure">
          <div class="ptile-em"><i class="fi-rr-hiking" aria-hidden="true"></i></div>
          <div class="ptile-n">Adventure</div>
        </div>
      </div>

      <span class="sec-label mt-20">Who's Travelling?</span>
      <div class="fr">
        <div class="fg no-mb">
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
        <div class="fg no-mb">
          <label class="fl2">Children</label>
          <select class="fi" id="p-child">
            <option selected>None</option>
            <option>1</option>
            <option>2</option>
            <option>3+</option>
          </select>
        </div>
      </div>

      <hr class="step-divider">

      <div class="ps-title">YOUR BUDGET</div>
      <div class="ps-sub">Per person · We only show options in your range</div>

      <div class="tile-g2" id="budget-grid">
        <div class="btile" data-v="Budget — Under ₹25,000">
          <div class="bt-name"><i class="fi-rr-backpack" aria-hidden="true"></i> Budget</div>
          <div class="bt-range">Under ₹25,000 / person</div>
        </div>
        <div class="btile sel" data-v="Mid-range — ₹25K–₹60K">
          <div class="bt-name"><i class="fi-rr-star" aria-hidden="true"></i> Mid-range</div>
          <div class="bt-range">₹25,000–₹60,000 / person</div>
        </div>
        <div class="btile" data-v="Premium — ₹60K–₹1.5L">
          <div class="bt-name"><i class="fi-rr-gem" aria-hidden="true"></i> Premium</div>
          <div class="bt-range">₹60,000–₹1,50,000 / person</div>
        </div>
        <div class="btile" data-v="Luxury — Above ₹1.5L">
          <div class="bt-name"><i class="fi-rr-crown" aria-hidden="true"></i> Luxury</div>
          <div class="bt-range">Above ₹1,50,000 / person</div>
        </div>
      </div>

      <div class="fg mt-16">
        <label class="fl2">Departing From</label>
        <input class="fi" type="text" id="p-from" placeholder="e.g. Delhi, Mumbai, Bangalore...">
      </div>
      <div class="fg">
        <label class="fl2">Special Requests <span class="field-note-inline">(optional)</span></label>
        <textarea class="fi no-resize" id="p-notes" rows="2" placeholder="Vegetarian meals, anniversary surprise, wheelchair access..."></textarea>
      </div>

      <div class="plan-nav">
        <span></span>
        <button class="btn-nx btn-primary" onclick="submitTrip()">
          <i class="fi-rr-check-circle" aria-hidden="true"></i>
          Get My Personalised Itinerary
        </button>
      </div>
      <div class="pat-submit-note">
        No payment required &middot; Delivered to your WhatsApp &amp; email within minutes
      </div>
    </div>

    <div class="plan-step" id="pstep-success">
      <div class="success-box">
        <div class="succ-icon"><i class="fi-rr-confetti" aria-hidden="true"></i></div>
        <div class="succ-title">YOU'RE ALL SET!</div>
        <p class="succ-sub">
          Your personalised itinerary will reach you on WhatsApp at <strong id="succ-phone"></strong> and by email at <strong id="succ-email"></strong> within minutes.
        </p>
        <div class="succ-stats">
          <div>
            <div class="succ-stat-num">Minutes</div>
            <div class="succ-stat-lbl">Delivery Time</div>
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
        <div class="pat-reset-wrap">
          <button onclick="resetForm()" class="pat-reset-btn">
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

  window.submitTrip = function () {
    collectData();

    if (!tripData.phone) {
      alert('Please enter your WhatsApp number so we can send your itinerary.');
      document.getElementById('p-phone').focus();
      return;
    }
    if (!tripData.name) {
      alert('Please enter your name.');
      document.getElementById('p-name').focus();
      return;
    }

    /* ── Save to WordPress database via AJAX ── */
    if (typeof twAjax !== 'undefined' && twAjax.url) {
      var fd = new FormData();
      fd.append('action',      'submit_trip_inquiry');
      fd.append('nonce',       twAjax.nonce);
      fd.append('name',        tripData.name);
      fd.append('phone',       tripData.phone);
      fd.append('email',       tripData.email);
      fd.append('destination', tripData.dest);
      fd.append('date',        tripData.date);
      fd.append('duration',    tripData.dur);
      fd.append('time_pref',   tripData.time);
      fd.append('trip_type',   tripData.type);
      fd.append('adults',      tripData.adults);
      fd.append('children',    tripData.child);
      fd.append('budget',      tripData.budget);
      fd.append('departing',   tripData.from);
      fd.append('notes',       tripData.notes);

      fetch(twAjax.url, { method: 'POST', body: fd })
        .catch(function (err) { console.warn('1TW: inquiry save failed', err); });
    }

    /* ── Show success screen ── */
    document.getElementById('pstep-form').classList.remove('active');
    document.getElementById('pstep-success').classList.add('active');

    /* Confirm back the contact details their itinerary will be sent to */
    document.getElementById('succ-phone').textContent = tripData.phone || 'the number you provided';
    document.getElementById('succ-email').textContent = tripData.email || 'the email you provided';

    document.querySelector('.plan-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  };

  window.resetForm = function () {
    location.reload();
  };
})();
</script>

<?php get_footer(); ?>
