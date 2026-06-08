@extends('layouts.app')

@section('content')

<!-- ===================== HERO ===================== -->
<header class="hero">
  <div class="hero-photo reveal in">
    <picture>
      <source srcset="{{ asset('img/hero-couple.webp') }}" type="image/webp" />
      <img src="{{ asset('img/hero-couple.jpg') }}" alt="Couple en tenues de mariage sénégalaises" width="900" height="1350" fetchpriority="high" decoding="async" />
    </picture>
  </div>
  <div class="hero-inner">
    <div class="hero-text">
      <span class="label reveal in">Maison matrimoniale ❤</span>
      <h1 class="reveal in" data-d="1">La rencontre bénie,<br/>l'union <em>sincère</em>.</h1>
      <p class="lead reveal in" data-d="2">Des intentions claires, des profils vérifiés, une communauté bienveillante — pour celles et ceux qui cherchent le mariage.</p>
      <div class="hero-cta reveal in" data-d="3">
        <a href="#rejoindre" class="btn btn-primary">Créer ma demande<svg class="ic sm"><use href="#i-arrow"/></svg></a>
        <a href="#histoires" class="lnk">Voir les histoires</a>
      </div>
      <div class="hero-figs reveal in" data-d="4">
        <div class="fig"><b>10 000+</b><span>Membres</span></div>
        <div class="fig-sep"></div>
        <div class="fig"><b>2 500+</b><span>Rencontres</span></div>
        <div class="fig-sep"></div>
        <div class="fig"><b>850+</b><span>Mariages</span></div>
      </div>
    </div>
  </div>
  <div class="scroll-hint">Découvrir</div>
</header>

<!-- ===================== DEMANDES ===================== -->
<section id="demandes"><div class="wrap">
  <div class="toolbar reveal">
    <div class="t-left">
      <span class="label">Demandes publiques</span>
      <h2>Des demandes en mariage <em>sérieuses</em></h2>
      <p>Parcourez les profils de celles et ceux qui cherchent à se marier. Avec ou sans photo, toujours vérifiés.</p>
    </div>
    <a href="{{ route('demandes.index') }}" class="lnk">Toutes les demandes<svg class="ic sm"><use href="#i-arrow"/></svg></a>
  </div>

  <form class="filters reveal" method="GET" action="{{ route('demandes.index') }}">
    <div class="fl"><label>Recherche</label><input type="text" name="q" placeholder="Profession, valeurs…"/></div>
    <div class="fl"><label>Je cherche</label><select name="seeking"><option value="">Indifférent</option><option value="Une épouse">Une épouse</option><option value="Un époux">Un époux</option></select></div>
    <div class="fl"><label>Âge</label><select name="age"><option value="">Tous</option><option value="20-25">20 – 25</option><option value="26-32">26 – 32</option><option value="33-40">33 – 40</option><option value="41+">41 +</option></select></div>
    <div class="fl"><label>Région</label><select name="region"><option value="">Toutes</option><option value="Dakar">Dakar</option><option value="Thiès">Thiès</option><option value="Saint-Louis">Saint-Louis</option><option value="Touba">Touba</option><option value="Ziguinchor">Ziguinchor</option><option value="Diaspora">Diaspora</option></select></div>
    <div class="fl"><label>Pratique</label><select name="pratique"><option value="">Toutes</option><option value="Pratiquant">Pratiquant(e)</option></select></div>
    <button class="btn btn-primary fl-btn"><svg class="ic sm"><use href="#i-search"/></svg>Filtrer</button>
  </form>

  <div class="listing">
    @forelse($demandes as $d)
      @include('partials.demande-card', ['d' => $d, 'stagger' => $loop->index % 3])
    @empty
      <p style="padding:34px;color:var(--muted)">Aucune demande pour le moment.</p>
    @endforelse
  </div>

  <div class="more-row reveal"><a href="{{ route('demandes.index') }}" class="btn btn-line">Voir toutes les demandes<svg class="ic sm"><use href="#i-arrow"/></svg></a></div>
</div></section>

<!-- ===================== ÉTAPES / IA (sombre) ===================== -->
<section id="ia" class="sec--dark on-dark"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">Le chemin</span>
    <h2>Votre chemin vers le <em>mariage</em></h2>
    <p>Quatre étapes pensées pour des rencontres qui ont du sens.</p>
  </div>

  <div class="steps">
    <div class="step-row reveal">
      <div class="no">01</div>
      <div class="st-txt"><h3>Publiez votre demande</h3><p>Dites qui vous êtes et ce que vous recherchez vraiment, en quelques mots choisis.</p></div>
      <svg class="ic lg st-ic"><use href="#i-user"/></svg>
    </div>
    <div class="step-row reveal" data-d="1">
      <div class="no">02</div>
      <div class="st-txt"><h3>Découvrez vos affinités</h3><p>Notre compatibilité met en avant les profils qui vous correspondent vraiment.</p></div>
      <svg class="ic lg st-ic"><use href="#i-spark"/></svg>
    </div>
    <div class="step-row reveal" data-d="2">
      <div class="no">03</div>
      <div class="st-txt"><h3>Échangez en confiance</h3><p>Une messagerie sécurisée et modérée, à votre rythme et en toute discrétion.</p></div>
      <svg class="ic lg st-ic"><use href="#i-message"/></svg>
    </div>
    <div class="step-row reveal" data-d="3">
      <div class="no">04</div>
      <div class="st-txt"><h3>Rencontrez, puis célébrez</h3><p>Des connexions vérifiées qui mènent à de vraies histoires — jusqu'au mariage.</p></div>
      <svg class="ic lg st-ic"><use href="#i-rings"/></svg>
    </div>
  </div>

  <div class="ia-feature reveal">
    <div class="donut" id="donut">
      <svg width="230" height="230" viewBox="0 0 230 230">
        <circle class="track" cx="115" cy="115" r="95"/><circle class="bar" cx="115" cy="115" r="95"/>
      </svg>
      <div class="center"><b>92%</b><small>Compatibilité</small></div>
    </div>
    <div class="if-txt">
      <span class="label">Compatibilité guidée</span>
      <h3>Une affinité mesurée, pas le hasard</h3>
      <p>Chaque suggestion s'appuie sur ce qui compte vraiment pour une union durable. Vous gardez la main, l'algorithme éclaire le chemin.</p>
      <div class="crit"><span>Valeurs</span><span>Objectifs</span><span>Foi</span><span>Localisation</span><span>Personnalité</span></div>
    </div>
  </div>
</div></section>

<!-- ===================== COMMUNAUTÉ ===================== -->
<section id="communaute" class="sec--alt"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">La communauté</span>
    <h2>Confessions &amp; <em>entraide</em></h2>
    <p>Un espace bienveillant pour partager, poser ses questions et s'entraider — anonymement ou non.</p>
  </div>

  <div class="community-grid">
    <div class="feed">
      <div class="feed-tabs reveal">
        <span class="tab active">Tout</span><span class="tab">Confessions</span><span class="tab">Conseils</span>
        <span class="tab">Témoignages</span><span class="tab">Questions</span>
      </div>

      <div class="composer reveal">
        <div class="row"><span class="av" data-av="VS"></span>
          <input class="q" type="text" placeholder="Partagez une confession, une question, un conseil…"/></div>
        <div class="row2">
          <div class="anon" id="anonRow"><div class="toggle"></div>Publier anonymement</div>
          <button class="btn btn-primary">Publier<svg class="ic sm"><use href="#i-send"/></svg></button>
        </div>
      </div>

      <article class="post reveal">
        <div class="p-head"><span class="av" data-av="?"></span>
          <div><div class="name">Anonyme</div>
            <div class="meta"><svg class="ic sm"><use href="#i-calendar"/></svg>7 juin, 14h32<span class="dot"></span>Dakar</div></div>
          <span class="theme">🌙 Confession</span></div>
        <p class="p-body">J'ai 34 ans. Je souhaite me marier, mais je n'ai pas encore les moyens d'organiser une grande cérémonie. Est-ce que la sincérité suffit aujourd'hui ? 🤲</p>
        <div class="reacts"><span class="e">❤️</span><span class="e">🤲</span><span class="e">🌹</span> 248 réactions · 62 réponses</div>
        <div class="p-actions">
          <button class="p-btn like"><svg class="ic"><use href="#i-heart"/></svg>J'aime</button>
          <button class="p-btn"><svg class="ic"><use href="#i-chat"/></svg>Répondre</button>
          <button class="p-btn"><svg class="ic"><use href="#i-share"/></svg>Partager</button>
        </div>
        <div class="comments">
          <div class="comment"><span class="av s" data-av="FD"></span>
            <div class="cbody"><span class="cn">Fatou D.<svg class="ic"><use href="#i-verified"/></svg></span>
              <p>La sincérité passe avant tout. Une union sincère vaut mille cérémonies. Reste fidèle à tes valeurs. ❤️</p>
              <div class="creact"><span>👍 J'aime · 84</span><span>Répondre</span></div></div></div>
          <div class="comment"><span class="av s" data-av="IS"></span>
            <div class="cbody"><span class="cn">Ibrahima S.<svg class="ic"><use href="#i-verified"/></svg></span>
              <p>Beaucoup de mariages réussis ont commencé simplement. L'essentiel, c'est l'intention. Courage 💪🤲</p>
              <div class="creact"><span>👍 J'aime · 51</span><span>Répondre</span></div></div></div>
          <div class="add-comment"><input type="text" placeholder="Répondre avec bienveillance… 💬"/>
            <button><svg class="ic"><use href="#i-send"/></svg></button></div>
        </div>
      </article>

      <article class="post reveal" data-d="1">
        <div class="p-head"><span class="av" data-av="MF"></span>
          <div><div class="name">Maïmouna F.<svg class="ic"><use href="#i-verified"/></svg></div>
            <div class="meta"><svg class="ic sm"><use href="#i-calendar"/></svg>6 juin, 19h05<span class="dot"></span>Rufisque</div></div>
          <span class="theme">💍 Témoignage</span></div>
        <p class="p-body">Alhamdoulillah, fiancée depuis hier à quelqu'un rencontré ici. La bienveillance de cette communauté m'a portée tout au long du chemin. 🤲💍✨</p>
        <div class="reacts"><span class="e">❤️</span><span class="e">🎉</span><span class="e">😍</span> 1 204 réactions · 203 réponses</div>
        <div class="p-actions">
          <button class="p-btn like"><svg class="ic"><use href="#i-heart"/></svg>J'aime</button>
          <button class="p-btn"><svg class="ic"><use href="#i-chat"/></svg>Répondre</button>
          <button class="p-btn"><svg class="ic"><use href="#i-share"/></svg>Partager</button>
        </div>
        <div class="comments">
          <div class="comment"><span class="av s" data-av="KB"></span>
            <div class="cbody"><span class="cn">Khady B.</span>
              <p>Quelle belle nouvelle, qu'Allah bénisse votre union ! 🎉🤲</p>
              <div class="creact"><span>👍 J'aime · 67</span><span>Répondre</span></div></div></div>
          <div class="add-comment"><input type="text" placeholder="Répondre avec bienveillance… 💬"/>
            <button><svg class="ic"><use href="#i-send"/></svg></button></div>
        </div>
      </article>

      <article class="post reveal" data-d="2">
        <div class="p-head"><span class="av" data-av="OD"></span>
          <div><div class="name">Ousmane D.<svg class="ic"><use href="#i-verified"/></svg></div>
            <div class="meta"><svg class="ic sm"><use href="#i-calendar"/></svg>5 juin, 09h47<span class="dot"></span>Diaspora · Italie</div></div>
          <span class="theme">💡 Conseil</span></div>
        <p class="p-body">La dot, la cérémonie, l'implication des familles… La transparence dès le départ évite bien des malentendus. Quel est votre regard ? 🤝</p>
        <div class="reacts"><span class="e">👍</span><span class="e">🤝</span><span class="e">🙏</span> 540 réactions · 128 réponses</div>
        <div class="p-actions">
          <button class="p-btn like"><svg class="ic"><use href="#i-heart"/></svg>J'aime</button>
          <button class="p-btn"><svg class="ic"><use href="#i-chat"/></svg>Répondre</button>
          <button class="p-btn"><svg class="ic"><use href="#i-share"/></svg>Partager</button>
        </div>
        <div class="comments">
          <div class="comment"><span class="av s" data-av="AN"></span>
            <div class="cbody"><span class="cn">Aïssatou N.<svg class="ic"><use href="#i-verified"/></svg></span>
              <p>Tellement vrai. La transparence évite bien des déceptions. Merci pour ce rappel 🙏</p>
              <div class="creact"><span>👍 J'aime · 38</span><span>Répondre</span></div></div></div>
          <div class="add-comment"><input type="text" placeholder="Répondre avec bienveillance… 💬"/>
            <button><svg class="ic"><use href="#i-send"/></svg></button></div>
        </div>
      </article>
    </div>

    <aside class="side">
      <div class="swidget reveal">
        <h4>Thèmes du moment</h4>
        <div class="theme-list">
          <a href="#">Mariage &amp; foi<span>1 240</span></a>
          <a href="#">Conseils aux fiancés<span>847</span></a>
          <a href="#">Vie de couple<span>692</span></a>
          <a href="#">Diaspora &amp; distance<span>513</span></a>
          <a href="#">Premiers échanges<span>438</span></a>
        </div>
      </div>
      <div class="swidget reveal" data-d="1">
        <h4>Membres en ligne</h4>
        <div class="online">
          <div class="o-row"><span class="av s" data-av="AN"></span><div><div class="nm">Aïssatou N.</div><div class="st">en ligne</div></div></div>
          <div class="o-row"><span class="av s" data-av="MS"></span><div><div class="nm">Mamadou S.</div><div class="st">en ligne</div></div></div>
          <div class="o-row"><span class="av s" data-av="RG"></span><div><div class="nm">Rama G.</div><div class="st">en ligne</div></div></div>
          <div class="o-row"><span class="av s" data-av="AK"></span><div><div class="nm">Abdou K.</div><div class="st">en ligne</div></div></div>
        </div>
      </div>
    </aside>
  </div>
</div></section>

<!-- ===================== HISTOIRES (sombre) ===================== -->
<section id="histoires" class="sec--dark on-dark"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">Ils se sont dit oui ❤️</span>
    <h2>Histoires de <em>réussite</em></h2>
    <p>Ils se sont rencontrés sur TàakDiàkka. Aujourd'hui, ils écrivent leur histoire.</p>
  </div>
  <div class="stories">
    <div class="story reveal">
      <span class="badge"><svg class="ic sm"><use href="#i-rings"/></svg>Mariés en 2025</span>
      <p class="quote">Trois mois après notre premier message, il demandait ma main.</p>
      <div class="who"><span class="av s" data-av="AM"></span><div><b>Awa &amp; Modou</b><small>Dakar · Sénégal</small></div></div>
    </div>
    <div class="story reveal" data-d="1">
      <span class="badge"><svg class="ic sm heart"><use href="#i-heart"/></svg>Fiancés</span>
      <p class="quote">La compatibilité affichait 94%. Le destin avait raison.</p>
      <div class="who"><span class="av s" data-av="FC"></span><div><b>Fatou &amp; Cheikh</b><small>Paris · Diaspora</small></div></div>
    </div>
    <div class="story reveal" data-d="2">
      <span class="badge"><svg class="ic sm"><use href="#i-rings"/></svg>Mariés en 2024</span>
      <p class="quote">Une plateforme sérieuse, des intentions claires. Tout a été simple.</p>
      <div class="who"><span class="av s" data-av="AI"></span><div><b>Aïcha &amp; Ibrahima</b><small>Thiès · Sénégal</small></div></div>
    </div>
  </div>
</div></section>

<!-- ===================== STATS ===================== -->
<section><div class="wrap" style="padding-top:0;padding-bottom:0">
  <div class="stats reveal">
    <div class="stat"><div class="n" data-count="10000" data-suffix="+">0</div><div class="l">Membres actifs</div></div>
    <div class="stat"><div class="n" data-count="2500" data-suffix="+">0</div><div class="l">Rencontres réussies</div></div>
    <div class="stat"><div class="n" data-count="850" data-suffix="+">0</div><div class="l">Mariages célébrés</div></div>
    <div class="stat"><div class="n" data-count="97" data-suffix="%">0</div><div class="l">Profils vérifiés</div></div>
  </div>
</div></section>

<!-- ===================== CTA (sombre) ===================== -->
<section id="rejoindre" class="sec--dark on-dark"><div class="wrap cta">
  <div class="reveal">
    <span class="label center">Votre histoire commence ❤️</span>
    <h2>Votre <em>moitié</em> vous attend, déjà quelque part.</h2>
    <p>Rejoignez gratuitement la maison matrimoniale la plus bienveillante du Sénégal et de la diaspora.</p>
    <div class="hero-cta">
      <a href="#" class="btn btn-primary">Créer ma demande<svg class="ic sm"><use href="#i-arrow"/></svg></a>
      <a href="#" class="lnk">Découvrir les abonnements</a>
    </div>
  </div>
</div></section>

<!-- ===================== TARIFS ===================== -->
<section id="tarifs"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">Abonnements</span>
    <h2>Des formules <em>claires</em></h2>
    <p>Commencez gratuitement. Passez au premium quand vous êtes prêt(e) à aller plus loin.</p>
  </div>
  <div class="pricing">
    <article class="plan reveal">
      <div class="pname">Découverte</div>
      <div class="price">0<span>FCFA</span></div>
      <div class="pper">Gratuit, pour toujours</div>
      <ul>
        <li><svg class="ic"><use href="#i-check"/></svg>Créer une demande en mariage</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Parcourir les profils vérifiés</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Participer à la communauté</li>
        <li><svg class="ic"><use href="#i-check"/></svg>1 photo de profil</li>
        <li><svg class="ic"><use href="#i-check"/></svg>3 prises de contact par mois</li>
      </ul>
      <a href="#" class="btn btn-line">Commencer</a>
    </article>

    <article class="plan featured on-dark reveal" data-d="1">
      <div class="ptag">Recommandé</div>
      <div class="pname">Premium</div>
      <div class="price">4 900<span>FCFA</span></div>
      <div class="pper">par mois</div>
      <ul>
        <li><svg class="ic"><use href="#i-check"/></svg>Tout Découverte, et plus :</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Messages illimités</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Voir qui vous a remarqué(e)</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Filtres de recherche avancés</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Galerie photo (jusqu'à 6 photos)</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Partager des photos dans la communauté</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Badge Premium sur votre profil</li>
      </ul>
      <a href="#" class="btn btn-primary">Choisir Premium</a>
    </article>

    <article class="plan reveal" data-d="2">
      <div class="pname">Prestige</div>
      <div class="price">12 000<span>FCFA</span></div>
      <div class="pper">par mois</div>
      <ul>
        <li><svg class="ic"><use href="#i-check"/></svg>Tout Premium, et plus :</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Vérification Or prioritaire</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Mise en avant de votre profil</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Accompagnement personnalisé</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Galerie illimitée + photos mises en avant</li>
        <li><svg class="ic"><use href="#i-check"/></svg>Support dédié</li>
      </ul>
      <a href="#" class="btn btn-line">Choisir Prestige</a>
    </article>
  </div>
  <p class="pay-note reveal">Paiement sécurisé · Wave · Orange Money · Free Money · Carte bancaire</p>
</div></section>

@endsection
