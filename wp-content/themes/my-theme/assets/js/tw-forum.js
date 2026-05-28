/**
 * 1TRIPWISER TRIBE — forum frontend interactions
 * - New topic modal + AJAX submission
 * - Like / unlike topics and replies
 * - Mark topic solved / unsolved
 */
(function () {
    'use strict';

    var cfg = window.twForum || {};

    function post(action, data, cb) {
        data = data || {};
        data.action = action;
        data.nonce = cfg.nonce;
        var body = Object.keys(data).map(function (k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
        }).join('&');

        fetch(cfg.ajax, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            credentials: 'same-origin',
            body: body
        })
        .then(function (r) { return r.json(); })
        .then(function (json) { cb(null, json); })
        .catch(function (err) { cb(err); });
    }

    /* ───────────── NEW TOPIC MODAL ───────────── */
    var modal = document.getElementById('tribe-modal');

    function openModal() {
        if (!modal) return;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        var t = document.getElementById('tribe-f-title');
        if (t) setTimeout(function () { t.focus(); }, 50);
    }
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    ['tribe-open-new', 'tribe-open-new-2'].forEach(function (id) {
        var btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', function () {
                if (!cfg.loggedIn) { window.location.href = cfg.loginUrl; return; }
                openModal();
            });
        }
    });

    if (modal) {
        modal.querySelectorAll('[data-tribe-close]').forEach(function (el) {
            el.addEventListener('click', closeModal);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
        });
    }

    /* ───────────── NEW TOPIC SUBMIT ───────────── */
    var form = document.getElementById('tribe-new-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var msg = document.getElementById('tribe-form-msg');
            var btn = document.getElementById('tribe-submit');
            msg.className = 'tribe-form-msg';
            msg.textContent = '';

            var title = form.querySelector('[name="title"]').value.trim();
            var content = form.querySelector('[name="content"]').value.trim();
            var category = form.querySelector('[name="category"]').value;

            if (title.length < 5) {
                msg.className = 'tribe-form-msg error';
                msg.textContent = 'Please enter a clearer title (5+ characters).';
                return;
            }
            if (content.length < 10) {
                msg.className = 'tribe-form-msg error';
                msg.textContent = 'Please add a bit more detail.';
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Posting…';

            post('tw_forum_new_topic', { title: title, content: content, category: category }, function (err, json) {
                if (err || !json) {
                    msg.className = 'tribe-form-msg error';
                    msg.textContent = 'Network error. Please try again.';
                    btn.disabled = false;
                    btn.textContent = 'Post Topic';
                    return;
                }
                if (json.success) {
                    msg.className = 'tribe-form-msg success';
                    msg.textContent = json.data.message + ' Redirecting…';
                    window.location.href = json.data.redirect;
                } else {
                    msg.className = 'tribe-form-msg error';
                    msg.textContent = (json.data && json.data.message) || 'Could not post topic.';
                    btn.disabled = false;
                    btn.textContent = 'Post Topic';
                }
            });
        });
    }

    /* ───────────── LIKE (topic + reply) ───────────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.tribe-like-btn');
        if (!btn) return;

        if (!cfg.loggedIn) { window.location.href = cfg.loginUrl; return; }

        var type = btn.getAttribute('data-type');
        var id = btn.getAttribute('data-id');
        if (btn.dataset.busy === '1') return;
        btn.dataset.busy = '1';

        post('tw_forum_like', { type: type, id: id }, function (err, json) {
            btn.dataset.busy = '';
            if (err || !json || !json.success) return;
            var icon = btn.querySelector('.tribe-like-icon');
            var count = btn.querySelector('.tribe-like-count');
            if (json.data.liked) {
                btn.classList.add('is-liked');
                if (icon) icon.textContent = '❤️';
            } else {
                btn.classList.remove('is-liked');
                if (icon) icon.textContent = '🤍';
            }
            if (count) count.textContent = json.data.count;
        });
    });

    /* ───────────── MARK SOLVED ───────────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.tribe-solve-btn');
        if (!btn) return;

        var id = btn.getAttribute('data-id');
        post('tw_forum_toggle_solved', { id: id }, function (err, json) {
            if (err || !json || !json.success) return;
            var badge = document.getElementById('tribe-solved-badge');
            if (json.data.solved) {
                btn.classList.add('is-solved');
                btn.textContent = '↩️ Mark unsolved';
                if (badge) { badge.textContent = '✅ Solved'; badge.classList.remove('is-off'); }
            } else {
                btn.classList.remove('is-solved');
                btn.textContent = '✅ Mark solved';
                if (badge) { badge.textContent = '⬜ Unsolved'; badge.classList.add('is-off'); }
            }
        });
    });
})();