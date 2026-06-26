/* avatars monogramme — neutres */
document.querySelectorAll('.av[data-av]').forEach(el => el.textContent = el.dataset.av.trim());

/* navbar publique + barre de progression (optionnels) */
const progress = document.getElementById('progress');
const nav = document.getElementById('nav');
if (nav || progress) {
  addEventListener('scroll', () => {
    if (nav) nav.classList.toggle('scrolled', scrollY > 40);
    if (progress) {
      const h = document.documentElement.scrollHeight - innerHeight;
      progress.style.width = (scrollY / h * 100) + '%';
    }
  });
}

/* burger (optionnel) */
const burger = document.getElementById('burger');
const navlinks = document.getElementById('navlinks');
if (burger && navlinks) {
  burger.addEventListener('click', () => { navlinks.classList.toggle('open'); document.body.classList.toggle('no-scroll'); });
  navlinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { navlinks.classList.remove('open'); document.body.classList.remove('no-scroll'); }));
}

/* révélations au scroll */
const io = new IntersectionObserver(es => es.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } }), { threshold: 0, rootMargin: '0px 0px -8% 0px' });
document.querySelectorAll('.reveal:not(.in)').forEach(el => io.observe(el));
/* Filet de sécurité : ne jamais laisser de contenu invisible si l'animation ne se déclenche pas. */
setTimeout(() => document.querySelectorAll('.reveal:not(.in)').forEach(el => el.classList.add('in')), 2200);

/* anneau de compatibilité (optionnel) */
const donut = document.getElementById('donut');
if (donut) {
  new IntersectionObserver((e, o) => e.forEach(en => { if (en.isIntersecting) { donut.classList.add('in'); o.disconnect(); } }), { threshold: .4 }).observe(donut);
}

/* compteurs */
function count(el) {
  const target = +el.dataset.count, sfx = el.dataset.suffix || '', dur = 2000, t0 = performance.now();
  (function tick(now) {
    const p = Math.min((now - t0) / dur, 1), e = 1 - Math.pow(1 - p, 3);
    el.textContent = Math.floor(e * target).toLocaleString('fr-FR') + sfx;
    if (p < 1) requestAnimationFrame(tick); else el.textContent = target.toLocaleString('fr-FR') + sfx;
  })(t0);
}
const co = new IntersectionObserver(es => es.forEach(en => { if (en.isIntersecting) { count(en.target); co.unobserve(en.target); } }), { threshold: .5 });
document.querySelectorAll('[data-count]').forEach(c => co.observe(c));

/* toggle anonymat (optionnel) */
const anonRow = document.getElementById('anonRow');
if (anonRow) anonRow.addEventListener('click', () => anonRow.querySelector('.toggle').classList.toggle('off'));

/* like (optionnel) */
document.querySelectorAll('.p-btn.like').forEach(btn => btn.addEventListener('click', () => btn.classList.toggle('liked')));

/* onglets (optionnel) */
document.querySelectorAll('.feed-tabs .tab').forEach(t => t.addEventListener('click', () => {
  document.querySelectorAll('.feed-tabs .tab').forEach(x => x.classList.remove('active')); t.classList.add('active');
}));

/* ---------- Notifications « toast » ---------- */
window.toast = function (msg, type) {
  if (!msg) return;
  let wrap = document.getElementById('td-toasts');
  if (!wrap) { wrap = document.createElement('div'); wrap.id = 'td-toasts'; wrap.className = 'td-toasts'; document.body.appendChild(wrap); }
  const t = document.createElement('div');
  t.className = 'td-toast' + (type ? ' ' + type : '');
  t.setAttribute('role', 'status');
  t.textContent = msg;
  wrap.appendChild(t);
  requestAnimationFrame(() => t.classList.add('show'));
  const close = () => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); };
  t.addEventListener('click', close);
  setTimeout(close, 4400);
};
/* messages flash rendus par le serveur -> toast élégant */
document.querySelectorAll('[data-flash]').forEach(el => { window.toast(el.textContent.trim(), el.dataset.flash); el.remove(); });

/* ---------- Deck « à swiper » (Découvrir) ---------- */
(function () {
  const deck = document.querySelector('[data-deck] .deck');
  if (!deck) return;
  const wrap = deck.closest('[data-deck]');
  const csrf = document.querySelector('meta[name=csrf-token]')?.content;
  let startX = 0, startY = 0, curX = 0, curY = 0, dragging = false, active = null;

  const topCard = () => deck.querySelector('.dcard:not(.gone)');
  function refresh() {
    const cards = [...deck.querySelectorAll('.dcard:not(.gone)')];
    if (!cards.length) wrap.classList.add('deck-done');
    cards.forEach((c, i) => { c.style.zIndex = 100 - i; c.classList.toggle('behind', i > 0); });
  }
  refresh();

  function fly(card, dir) {
    if (!card || card.classList.contains('gone')) return;
    card.classList.add('gone');
    card.style.transition = 'transform .5s ease, opacity .5s ease';
    card.style.transform = 'translateX(' + (dir > 0 ? 620 : -620) + 'px) rotate(' + (dir > 0 ? 20 : -20) + 'deg)';
    card.style.opacity = '0';
    if (dir > 0) {
      fetch(card.dataset.interest, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }).catch(() => {});
      if (window.toast) window.toast('Intérêt envoyé ❤', 'success');
    }
    setTimeout(() => { card.remove(); refresh(); }, 470);
  }

  function start(e) {
    active = topCard(); if (!active) return;
    const t = e.touches ? e.touches[0] : e;
    startX = t.clientX; startY = t.clientY; curX = curY = 0; dragging = true;
    active.style.transition = 'none';
  }
  function move(e) {
    if (!dragging || !active) return;
    const t = e.touches ? e.touches[0] : e;
    curX = t.clientX - startX; curY = t.clientY - startY;
    if (Math.abs(curX) > 8 && e.cancelable) e.preventDefault();
    active.style.transform = 'translate(' + curX + 'px,' + curY * 0.25 + 'px) rotate(' + curX / 18 + 'deg)';
    active.classList.toggle('show-like', curX > 45);
    active.classList.toggle('show-nope', curX < -45);
  }
  function end() {
    if (!dragging || !active) return;
    dragging = false;
    const card = active; active = null;
    if (curX > 95) fly(card, 1);
    else if (curX < -95) fly(card, -1);
    else { card.style.transition = 'transform .3s ease'; card.style.transform = ''; card.classList.remove('show-like', 'show-nope'); }
  }

  deck.addEventListener('touchstart', start, { passive: true });
  deck.addEventListener('touchmove', move, { passive: false });
  deck.addEventListener('touchend', end);
  deck.addEventListener('mousedown', e => { start(e); document.addEventListener('mousemove', move); document.addEventListener('mouseup', up); });
  function up() { end(); document.removeEventListener('mousemove', move); document.removeEventListener('mouseup', up); }

  wrap.querySelector('[data-deck-like]')?.addEventListener('click', () => fly(topCard(), 1));
  wrap.querySelector('[data-deck-nope]')?.addEventListener('click', () => fly(topCard(), -1));
})();
