{{-- Widget flottant : membres en ligne (utilisateurs connectés uniquement) --}}
<div class="online-fab" id="onlineFab">
  <button class="online-fab-btn" id="onlineFabBtn" aria-label="Membres en ligne" title="Membres en ligne">
    <svg class="ic"><use href="#i-user"/></svg>
    <span class="online-fab-dot"></span>
    <span class="online-fab-count" id="onlineFabCount" hidden>0</span>
  </button>
  <div class="online-fab-panel" id="onlineFabPanel" hidden>
    <div class="online-fab-head"><b>Membres en ligne</b><button id="onlineFabClose" aria-label="Fermer"><svg class="ic sm"><use href="#i-x"/></svg></button></div>
    <div class="online-fab-list" id="onlineFabList"><p class="c-loading">Chargement…</p></div>
  </div>
</div>

<script>
  (function () {
    const fab = document.getElementById('onlineFab');
    const btn = document.getElementById('onlineFabBtn');
    const panel = document.getElementById('onlineFabPanel');
    const list = document.getElementById('onlineFabList');
    const count = document.getElementById('onlineFabCount');
    if (!fab) return;
    let loaded = false;

    function render(data) {
      count.hidden = data.count === 0;
      count.textContent = data.count;
      if (!data.items.length) { list.innerHTML = '<p class="c-loading">Personne en ligne pour l\'instant.</p>'; return; }
      list.innerHTML = data.items.map((u) =>
        '<a class="o-row" href="' + u.url + '"><span class="av s photo on" style="background-image:url(\'' + u.photo + '\')"></span>' +
        '<div><div class="nm">' + u.name + '</div><div class="st">en ligne</div></div></a>').join('');
    }
    function load() {
      fetch('{{ route('members.online') }}', { headers: { 'Accept': 'application/json' } })
        .then((r) => r.json()).then(render).catch(() => {});
    }
    load();
    setInterval(load, 60000);

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      panel.hidden = !panel.hidden;
      if (!panel.hidden && !loaded) { load(); loaded = true; }
    });
    document.getElementById('onlineFabClose').addEventListener('click', () => panel.hidden = true);
    document.addEventListener('click', (e) => { if (!fab.contains(e.target)) panel.hidden = true; });
  })();
</script>
