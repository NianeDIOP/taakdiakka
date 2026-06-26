(function () {
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const AUTH = !!window.TD_AUTH;
  const headers = { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' };

  function postJSON(url, data) {
    return fetch(url, {
      method: 'POST',
      headers: { ...headers, 'Content-Type': 'application/json' },
      body: JSON.stringify(data || {}),
    }).then((r) => (r.ok ? r.json() : Promise.reject(r)));
  }

  const LABELS = { like: "J'aime", love: "J'adore", amine: 'Amine', support: 'Soutien', wow: 'Bravo' };
  const EMOJIS = { like: '👍', love: '❤️', amine: '🤲', support: '💪', wow: '✨' };
  const REPORT_REASONS = {
    spam: 'Spam ou publicité',
    inapproprie: 'Contenu inapproprié',
    harcelement: 'Harcèlement ou propos haineux',
    faux_profil: 'Faux profil ou identité usurpée',
    autre: 'Autre',
  };

  /* ---------- Signalement ---------- */
  function closeAllReportPickers(except) {
    document.querySelectorAll('.report-picker:not([hidden])').forEach((p) => { if (p !== except) p.hidden = true; });
    document.querySelectorAll('.p-menu.open').forEach((m) => m.classList.remove('open'));
  }
  function reportCommentPicker(comment) {
    let picker = comment.querySelector('[data-creport-picker]');
    if (picker) return picker;
    picker = document.createElement('div');
    picker.className = 'report-picker';
    picker.dataset.creportPicker = '1';
    picker.hidden = true;
    Object.entries(REPORT_REASONS).forEach(([key, label]) => {
      const b = document.createElement('button');
      b.type = 'button'; b.dataset.creportReason = key; b.textContent = label;
      picker.appendChild(b);
    });
    comment.querySelector('.creact').insertAdjacentElement('afterend', picker);
    return picker;
  }
  function sendReport(url, reason) {
    return postJSON(url, { reason: reason || 'autre' }).then(() => (window.toast ? window.toast('Merci, votre signalement a été transmis à la modération.', 'success') : alert('Merci, votre signalement a été transmis à la modération.')));
  }

  /* ---------- Réactions ---------- */
  function applyReaction(post, data) {
    const sum = post.querySelector('[data-react-sum]');
    sum.innerHTML = data.total > 0
      ? '<span class="re-emojis">' + (data.emojis || []).join('') + '</span><span data-react-total>' + data.total + '</span>'
      : '';
    const btn = post.querySelector('[data-react-toggle]');
    if (btn) {
      btn.classList.toggle('on', !!data.mine);
      btn.querySelector('.re-ic').textContent = data.mine ? EMOJIS[data.mine] : '👍';
      btn.querySelector('[data-react-label]').textContent = data.mine ? LABELS[data.mine] : "J'aime";
    }
  }
  function react(post, type) {
    postJSON('/communaute/' + post.dataset.post + '/reaction', { type })
      .then((data) => applyReaction(post, data)).catch(() => {});
  }

  /* ---------- Commentaires ---------- */

  function commentNode(c, isReply) {
    const el = document.createElement('div');
    el.className = 'comment' + (isReply ? ' is-reply' : '');
    el.dataset.comment = c.id;

    let actions = '';
    if (AUTH) {
      actions += '<button class="clike' + (c.liked ? ' on' : '') + '" data-clike>J\'aime' +
        '<span data-clcount>' + (c.likes ? ' · ' + c.likes : '') + '</span></button>';
      if (!isReply) actions += '<button class="creply" data-creply>Répondre</button>';
      actions += '<button class="creport" data-creport>Signaler</button>';
    } else if (c.likes) {
      actions += '<span>👍 ' + c.likes + '</span>';
    }
    actions += '<span class="cago">' + c.ago + '</span>';

    // Corps : tronqué avec « Lire la suite » si le commentaire est long
    const bodyHtml = c.long
      ? '<div class="p-body-wrap"><p data-excerpt>' + c.excerpt + ' <button type="button" class="read-more" data-readmore>Lire la suite</button></p><p data-fulltext hidden>' + c.body + '</p></div>'
      : '<p>' + c.body + '</p>'; // c.body est déjà échappé/enrichi côté serveur

    el.innerHTML =
      '<span class="av s photo" style="background-image:url(\'' + c.photo + '\')"></span>' +
      '<div class="cbody"><div class="cbubble"><span class="cn">' + c.name + '</span>' + bodyHtml + '</div>' +
      '<div class="creact">' + actions + '</div>' +
      (isReply ? '' : '<div class="replies" data-replies></div>' +
        (AUTH ? '<form class="reply-form" data-rform hidden><input type="text" name="body" placeholder="Répondre…" autocomplete="off" required /><button type="button" class="emoji-btn" data-emoji-toggle aria-label="Émojis">😊</button><button type="submit" aria-label="Envoyer"><svg class="ic"><use href="#i-send"/></svg></button></form>' : '')) +
      '</div>';

    if (!isReply && c.replies && c.replies.length) {
      const rbox = el.querySelector('[data-replies]');
      c.replies.forEach((r) => rbox.appendChild(commentNode(r, true)));
    }
    return el;
  }

  function loadComments(post, append) {
    const box = post.querySelector('[data-comments]');
    const list = post.querySelector('[data-clist]');
    const more = post.querySelector('[data-cmore]');
    const page = append ? parseInt(more.dataset.next, 10) : 1;
    if (!append) list.innerHTML = '<p class="c-loading">Chargement…</p>';

    fetch('/communaute/' + post.dataset.post + '/commentaires?page=' + page, { headers })
      .then((r) => r.json())
      .then((data) => {
        if (!append) list.innerHTML = '';
        data.items.forEach((c) => list.appendChild(commentNode(c, false)));
        if (data.has_more) { more.hidden = false; more.dataset.next = data.next; } else { more.hidden = true; }
        const cc = post.querySelector('[data-ccount]');
        if (cc) cc.textContent = data.total;
        box.dataset.loaded = '1';
      });
  }

  /* ---------- Délégation de clics ---------- */
  document.addEventListener('click', function (e) {
    // « Lire la suite » d'une publication longue
    const readmore = e.target.closest('[data-readmore]');
    if (readmore) {
      const wrap = readmore.closest('.p-body-wrap');
      if (wrap) {
        wrap.querySelector('[data-excerpt]').hidden = true;
        wrap.querySelector('[data-fulltext]').hidden = false;
      }
      return;
    }
    // like de commentaire
    const clike = e.target.closest('[data-clike]');
    if (clike) {
      const comment = clike.closest('.comment');
      postJSON('/communaute/commentaires/' + comment.dataset.comment + '/like').then((d) => {
        clike.classList.toggle('on', d.liked);
        clike.querySelector('[data-clcount]').textContent = d.count ? ' · ' + d.count : '';
      });
      return;
    }
    // afficher le formulaire de réponse
    const creply = e.target.closest('[data-creply]');
    if (creply) {
      const form = creply.closest('.comment').querySelector('[data-rform]');
      if (form) { form.hidden = !form.hidden; if (!form.hidden) form.querySelector('input').focus(); }
      return;
    }

    // signaler un commentaire — ouvre le sélecteur de motif
    const creport = e.target.closest('[data-creport]');
    if (creport) {
      const comment = creport.closest('.comment');
      const picker = reportCommentPicker(comment);
      const open = picker.hidden;
      closeAllReportPickers();
      picker.hidden = !open;
      return;
    }
    const creportReason = e.target.closest('[data-creport-reason]');
    if (creportReason) {
      const picker = creportReason.closest('.report-picker');
      const comment = picker.closest('.comment');
      picker.hidden = true;
      sendReport('/communaute/commentaires/' + comment.dataset.comment + '/signaler', creportReason.dataset.creportReason);
      return;
    }

    // menu / signalement de publication
    const menuToggle = e.target.closest('[data-menu-toggle]');
    if (menuToggle) {
      const menu = menuToggle.closest('.p-menu');
      const open = !menu.classList.contains('open');
      closeAllReportPickers();
      menu.classList.toggle('open', open);
      return;
    }
    const reportToggle = e.target.closest('[data-report-toggle]');
    if (reportToggle) {
      const article = reportToggle.closest('.post');
      const picker = article.querySelector('[data-report-picker]');
      closeAllReportPickers();
      picker.hidden = false;
      article.querySelector('.p-menu')?.classList.remove('open');
      return;
    }
    const reportReason = e.target.closest('[data-report-reason]');
    if (reportReason) {
      const picker = reportReason.closest('[data-report-picker]');
      const article = reportReason.closest('.post');
      picker.hidden = true;
      sendReport('/communaute/' + article.dataset.post + '/signaler', reportReason.dataset.reportReason);
      return;
    }
    if (!e.target.closest('.report-picker') && !e.target.closest('.p-menu')) closeAllReportPickers();

    // Question du jour : « Répondre » pré-remplit le composeur
    const qa = e.target.closest('[data-qotd-answer]');
    if (qa) {
      const ta = document.querySelector('#composer textarea[name="body"]');
      const sel = document.querySelector('#composer select[name="theme"]');
      if (sel) sel.value = 'Question';
      if (ta) {
        ta.value = '« ' + qa.dataset.q + ' »\n';
        ta.dispatchEvent(new Event('input'));
        ta.focus();
        ta.scrollIntoView({ block: 'center', behavior: 'smooth' });
        ta.setSelectionRange(ta.value.length, ta.value.length);
      }
      return;
    }

    const post = e.target.closest('.post');
    if (!post) return;
    if (e.target.closest('[data-react-toggle]')) { react(post, 'like'); return; }
    const pick = e.target.closest('[data-react-type]');
    if (pick) { react(post, pick.dataset.reactType); return; }
    if (e.target.closest('[data-comments-toggle]')) {
      const box = post.querySelector('[data-comments]');
      box.hidden = !box.hidden;
      if (!box.hidden && !box.dataset.loaded) loadComments(post, false);
      return;
    }
    if (e.target.closest('[data-cmore]')) { loadComments(post, true); return; }
    const voteBtn = e.target.closest('[data-vote]');
    if (voteBtn && !voteBtn.disabled) {
      const pollEl = post.querySelector('[data-poll]');
      postJSON('/communaute/' + post.dataset.post + '/voter', { choice: +voteBtn.dataset.vote }).then((d) => {
        if (!d || !d.options) return;
        pollEl.classList.add('revealed');
        pollEl.querySelectorAll('.poll-opt').forEach((b) => {
          const o = d.options[+b.dataset.vote];
          if (!o) return;
          b.classList.toggle('mine', d.myVote === o.i);
          b.querySelector('.poll-bar').style.width = o.pct + '%';
          b.querySelector('.poll-pct').textContent = o.pct + '%';
        });
        const t = pollEl.querySelector('[data-poll-total]');
        if (t) t.textContent = d.total + ' vote' + (d.total > 1 ? 's' : '');
      });
      return;
    }
    if (e.target.closest('[data-save-toggle]')) {
      const btn = e.target.closest('[data-save-toggle]');
      postJSON('/communaute/' + post.dataset.post + '/enregistrer').then((d) => {
        btn.classList.toggle('on', d.saved);
        const label = btn.querySelector('[data-save-label]');
        if (label) label.textContent = d.saved ? 'Enregistré' : 'Enregistrer';
        if (window.toast) window.toast(d.saved ? 'Publication enregistrée ✓' : 'Retiré des enregistrements', 'success');
      });
      return;
    }
    if (e.target.closest('[data-share]')) {
      const url = location.origin + '/communaute/' + post.dataset.post;
      if (navigator.share) navigator.share({ url }).catch(() => {});
      else navigator.clipboard?.writeText(url).then(() => (window.toast ? window.toast('Lien copié ✓', 'success') : alert('Lien copié ✓')));
    }
  });

  /* ---------- Soumission de commentaires / réponses ---------- */
  document.addEventListener('submit', function (e) {
    const post = e.target.closest('.post');
    if (!post) return;

    // réponse
    const rform = e.target.closest('[data-rform]');
    if (rform) {
      e.preventDefault();
      const parent = rform.closest('.comment');
      const input = rform.querySelector('input[name="body"]');
      const body = input.value.trim();
      if (!body) return;
      postJSON('/communaute/' + post.dataset.post + '/commentaires', { body, parent_id: parent.dataset.comment })
        .then((data) => {
          parent.querySelector('[data-replies]').appendChild(commentNode(data.comment, true));
          input.value = ''; rform.hidden = true;
        });
      return;
    }

    // commentaire racine
    const cform = e.target.closest('[data-cform]');
    if (cform) {
      e.preventDefault();
      const input = cform.querySelector('input[name="body"]');
      const body = input.value.trim();
      if (!body) return;
      postJSON('/communaute/' + post.dataset.post + '/commentaires', { body }).then((data) => {
        const list = post.querySelector('[data-clist]');
        const loading = list.querySelector('.c-loading'); if (loading) loading.remove();
        list.insertBefore(commentNode(data.comment, false), list.firstChild);
        input.value = '';
        const cc = post.querySelector('[data-ccount]'); if (cc) cc.textContent = data.total;
      });
    }
  });

  /* ---------- Composeur ---------- */
  document.querySelectorAll('.composer .q').forEach((t) => {
    t.addEventListener('input', () => { t.style.height = 'auto'; t.style.height = t.scrollHeight + 'px'; });
  });
  const phBtn = document.getElementById('composerPhotoBtn');
  if (phBtn) {
    const input = document.getElementById('composerImage');
    const prev = document.getElementById('composerPreview');
    const prevImg = document.getElementById('composerPreviewImg');
    const prevX = document.getElementById('composerPreviewX');
    phBtn.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
      const f = input.files[0]; if (!f) return;
      prevImg.src = URL.createObjectURL(f); prev.hidden = false; phBtn.classList.add('on');
    });
    prevX.addEventListener('click', () => { input.value = ''; prev.hidden = true; phBtn.classList.remove('on'); });
  }
  document.querySelectorAll('#anonRow').forEach((row) => {
    const cb = row.querySelector('input[type=checkbox]'); if (!cb) return;
    row.addEventListener('click', (ev) => { ev.preventDefault(); cb.checked = !cb.checked; row.classList.toggle('on', cb.checked); });
  });

  /* Composeur : bouton Sondage */
  const pollBtn = document.querySelector('[data-poll-btn]');
  const pollFields = document.getElementById('pollFields');
  if (pollBtn && pollFields) {
    pollBtn.addEventListener('click', () => {
      const show = pollFields.hidden;
      pollFields.hidden = !show;
      pollBtn.classList.toggle('on', show);
      if (show) pollFields.querySelector('input').focus();
      else pollFields.querySelectorAll('input').forEach((i) => (i.value = ''));
    });
  }

  /* ---------- Question du jour : masquable, mémorisée pour la journée ---------- */
  (function () {
    const qotd = document.querySelector('[data-qotd]');
    if (!qotd) return;
    const KEY = 'td_qotd_dismissed';
    const today = qotd.dataset.day;
    try { if (localStorage.getItem(KEY) === today) qotd.remove(); } catch (e) {}
    const dismiss = () => { try { localStorage.setItem(KEY, today); } catch (e) {} qotd.style.transition = 'opacity .25s, transform .25s'; qotd.style.opacity = '0'; qotd.style.transform = 'translateY(-6px)'; setTimeout(() => qotd.remove(), 260); };
    qotd.addEventListener('click', (e) => {
      if (e.target.closest('[data-qotd-dismiss]')) { e.preventDefault(); dismiss(); }
      else if (e.target.closest('[data-qotd-answer]')) { setTimeout(dismiss, 60); }
    });
  })();

  /* ---------- Défilement infini du fil ---------- */
  (function () {
    const feed = document.querySelector('.feed');
    if (!feed) return;
    const moreRow = feed.querySelector('.more-row');
    if (!moreRow) return;
    const moreLink = moreRow.querySelector('a[href*="page="]');
    if (!moreLink) return; // pas de page suivante (ou page d'accueil)
    const params = new URLSearchParams(location.search);
    const theme = params.get('theme') || '';
    const tag = params.get('tag') || '';
    let next = 2, loading = false, done = false;
    try { next = +(new URL(moreLink.href).searchParams.get('page')) || 2; } catch (e) {}
    moreRow.innerHTML = '<span class="feed-loading">Chargement…</span>';

    function loadMore() {
      if (loading || done) return;
      loading = true;
      const q = new URLSearchParams({ page: next });
      if (theme) q.set('theme', theme);
      if (tag) q.set('tag', tag);
      fetch('/communaute/charger?' + q.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then((r) => r.json())
        .then((d) => {
          const tmp = document.createElement('div');
          tmp.innerHTML = d.html;
          Array.from(tmp.children).forEach((el) => { el.classList.add('in'); feed.insertBefore(el, moreRow); });
          next = d.next; loading = false;
          if (!d.hasMore) { done = true; moreRow.innerHTML = '<span class="feed-loading">Vous avez tout vu. 🤲</span>'; obs.disconnect(); return; }
          requestAnimationFrame(() => { if (moreRow.getBoundingClientRect().top < innerHeight + 500) loadMore(); });
        })
        .catch(() => { loading = false; moreRow.innerHTML = '<a class="btn btn-line" href="?page=' + next + '">Voir plus de publications</a>'; });
    }

    const obs = new IntersectionObserver((es) => es.forEach((en) => { if (en.isIntersecting) loadMore(); }), { rootMargin: '500px' });
    obs.observe(moreRow);
  })();

  /* ---------- Sélecteur d'émojis & stickers (catégories, récents) ---------- */
  (function () {
    const CATS = [
      { id: 'recent',  icon: '🕘', name: 'Récents' },
      { id: 'faces',   icon: '😊', name: 'Visages',  list: ['😊','😄','😍','🥰','😌','☺️','😂','🤗','😅','😉','🙂','😇','🤩','😎','🥹','😢','😮','🤔','😴','🤭','😘','😋','🙃','😬'] },
      { id: 'hearts',  icon: '❤️', name: 'Cœurs',    list: ['❤️','🧡','💛','💚','💙','💜','🤍','🩷','💖','💕','💗','💓','💝','💞','💌','😻','🌹','💐','🌷','🌺'] },
      { id: 'faith',   icon: '🤲', name: 'Foi',      list: ['🤲','🙏','🌙','🕌','☪️','✨','📿','🕋','🌟','💫','🧕','🌸','🤍','☁️'] },
      { id: 'symbols', icon: '🎉', name: 'Symboles', list: ['💍','🎉','🎊','🥂','💪','🤝','👍','👏','🙌','🔥','💯','⭐','💎','🌻','🦋','☕','🍯','🎁','📍','💬'] },
    ];
    const STICKERS = ['🤲','🌙','💍','❤️','✨','🕌','🌹','🥰','🙏','💐'];
    const RK = 'td_emoji_recent';
    let panel = null, grid = null, targetInput = null, active = 'faces';

    const recents = () => { try { return JSON.parse(localStorage.getItem(RK)) || []; } catch (e) { return []; } };
    const pushRecent = (em) => { let r = recents().filter((x) => x !== em); r.unshift(em); try { localStorage.setItem(RK, JSON.stringify(r.slice(0, 18))); } catch (e) {} };

    function renderGrid(list) {
      grid.innerHTML = list && list.length
        ? list.map((em) => '<button type="button" class="emoji-cell" data-emoji="' + em + '">' + em + '</button>').join('')
        : '<div class="emoji-empty">Vos émojis récents apparaîtront ici.</div>';
    }
    function showCat(id) {
      active = id;
      panel.querySelectorAll('.emoji-tab').forEach((t) => t.classList.toggle('on', t.dataset.cat === id));
      renderGrid(id === 'recent' ? recents() : (CATS.find((c) => c.id === id) || {}).list);
    }

    function buildPanel() {
      panel = document.createElement('div');
      panel.className = 'emoji-panel';
      panel.hidden = true;
      panel.innerHTML =
        '<div class="emoji-tabs">' + CATS.map((c) => '<button type="button" class="emoji-tab" data-cat="' + c.id + '" title="' + c.name + '">' + c.icon + '</button>').join('') + '</div>' +
        '<div class="emoji-grid"></div>' +
        '<div class="emoji-stickers">' + STICKERS.map((em) => '<button type="button" class="sticker-cell" data-emoji="' + em + '">' + em + '</button>').join('') + '</div>';
      document.body.appendChild(panel);
      grid = panel.querySelector('.emoji-grid');
    }

    document.addEventListener('click', function (e) {
      const toggle = e.target.closest('[data-emoji-toggle]');
      if (toggle) {
        e.preventDefault();
        if (!panel) buildPanel();
        const form = toggle.closest('form');
        targetInput = form ? form.querySelector('input[name="body"], textarea[name="body"], textarea') : null;
        const r = toggle.getBoundingClientRect();
        const PW = 286, PH = 326;
        panel.style.left = Math.max(8, Math.min(r.left, window.innerWidth - PW - 8)) + 'px';
        panel.style.top = Math.max(8, r.top + window.scrollY - 8 - PH) + 'px';
        panel.hidden = !panel.hidden;
        if (!panel.hidden) showCat(recents().length ? 'recent' : 'faces');
        return;
      }
      const tab = e.target.closest('.emoji-tab');
      if (tab) { showCat(tab.dataset.cat); return; }
      const pick = e.target.closest('[data-emoji]');
      if (pick && targetInput) {
        targetInput.value += pick.dataset.emoji;
        targetInput.focus();
        pushRecent(pick.dataset.emoji);
        return; // le panneau reste ouvert pour en ajouter plusieurs
      }
      if (panel && !panel.hidden && !e.target.closest('.emoji-panel') && !e.target.closest('[data-emoji-toggle]')) panel.hidden = true;
    });
  })();

  /* ---------- Mises à jour automatiques (polling) ---------- */
  const feed = document.querySelector('.community-grid .feed');
  if (feed) {
    const firstPost = feed.querySelector('.post[data-published]');
    let since = firstPost ? firstPost.dataset.published : new Date().toISOString();

    function pollNew() {
      if (document.hidden) return;
      const params = new URLSearchParams({ since });
      if (window.TD_THEME && window.TD_THEME !== 'Tout') params.set('theme', window.TD_THEME);
      if (window.TD_TAG) params.set('tag', window.TD_TAG);
      fetch('/communaute/nouveautes?' + params.toString(), { headers })
        .then((r) => r.json())
        .then((data) => {
          if (data.count > 0) showNewBanner(data.count);
        }).catch(() => {});
    }

    function showNewBanner(count) {
      let banner = feed.querySelector('.new-posts-banner');
      const label = count + ' nouvelle' + (count > 1 ? 's' : '') + ' publication' + (count > 1 ? 's' : '');
      if (banner) { banner.querySelector('span').textContent = label; return; }
      banner = document.createElement('button');
      banner.className = 'new-posts-banner reveal';
      banner.innerHTML = '<span>' + label + '</span><b>Afficher</b>';
      banner.addEventListener('click', () => location.reload());
      feed.insertBefore(banner, feed.firstChild);
    }

    function pollCounters() {
      if (document.hidden) return;
      const ids = Array.from(feed.querySelectorAll('.post[data-post]')).map((p) => p.dataset.post);
      if (!ids.length) return;
      postJSON('/communaute/compteurs', { ids }).then((items) => {
        items.forEach((item) => {
          const post = feed.querySelector('.post[data-post="' + item.id + '"]');
          if (!post) return;
          const sum = post.querySelector('[data-react-sum]');
          if (sum) {
            sum.innerHTML = item.total > 0
              ? '<span class="re-emojis">' + (item.emojis || []).join('') + '</span><span data-react-total>' + item.total + '</span>'
              : '';
          }
          const cc = post.querySelector('[data-ccount]');
          if (cc) cc.textContent = item.comments;
        });
      }).catch(() => {});
    }

    setInterval(pollNew, 20000);
    setInterval(pollCounters, 25000);

    /* ---------- Temps réel (Reverb via pusher-js) ---------- */
    // Si le serveur WebSocket tourne, on est notifié instantanément et on
    // déclenche un poll immédiat (compte exact). Sinon, le polling ci-dessus
    // sert de repli — aucune régression.
    if (window.TD_REVERB && window.Pusher) {
      try {
        const cfg = window.TD_REVERB;
        // Si le host configuré est local, on suit le domaine courant
        // (sinon le temps réel casse sur téléphone/LAN/production).
        const localHost = !cfg.host || cfg.host === 'localhost' || cfg.host === '127.0.0.1' || cfg.host === '0.0.0.0';
        const wsHost = localHost ? location.hostname : cfg.host;
        const pusher = new window.Pusher(cfg.key, {
          wsHost: wsHost,
          wsPort: cfg.port,
          wssPort: cfg.port,
          forceTLS: cfg.scheme === 'https',
          enabledTransports: ['ws', 'wss'],
          disableStats: true,
          cluster: 'mt1',
        });
        const channel = pusher.subscribe('communaute');
        channel.bind('post.created', function () {
          // Notification instantanée : on récupère le compte exact côté serveur.
          pollNew();
        });
      } catch (e) { /* repli silencieux sur le polling */ }
    }
  }
})();
