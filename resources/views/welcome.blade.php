@extends('layouts.app')

@section('title', 'TàakDiàkka — Maison matrimoniale & mariages vérifiés')
@section('description', 'Plateforme matrimoniale sénégalaise et musulmane : demandes en mariage vérifiées, compatibilité guidée et communauté bienveillante. Inscrivez-vous gratuitement.')

@push('styles')
<link rel="preload" as="image" href="{{ asset('img/hero-couple.webp') }}" type="image/webp" fetchpriority="high" />
@endpush

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
        <a href="{{ route('register') }}" class="btn btn-primary">Créer ma demande<svg class="ic sm"><use href="#i-arrow"/></svg></a>
        <a href="#histoires" class="lnk">Voir les histoires</a>
      </div>
      <div class="hero-figs reveal in" data-d="4">
        <div class="fig"><b data-count="10000" data-suffix="+">0</b><span>Membres</span></div>
        <div class="fig-sep"></div>
        <div class="fig"><b data-count="2500" data-suffix="+">0</b><span>Rencontres</span></div>
        <div class="fig-sep"></div>
        <div class="fig"><b data-count="850" data-suffix="+">0</b><span>Mariages</span></div>
      </div>
    </div>
  </div>
  <div class="scroll-hint">Découvrir</div>
</header>

<!-- ===================== CARROUSEL PUBLICITAIRE ===================== -->
@php $ads = \App\Models\Ad::active()->get(); @endphp
@if($ads->count())
<div class="ad-section" id="adSection">
  <div class="ad-track" id="adTrack">
    @foreach($ads as $ad)
    <div class="ad-slide">
      <img src="{{ asset('img/'.$ad->image) }}" alt="Publicité" class="ad-slide-img" loading="lazy" />
      @if($ad->contact)
      <div class="ad-slide-cta">
        <a href="{{ $ad->ctaHref() }}" class="btn btn-primary" target="_blank" rel="noopener noreferrer" style="gap:8px">
          @if($ad->cta_type === 'whatsapp')
            <svg class="ic sm" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.849L.057 23.428a.5.5 0 00.611.611l5.579-1.475A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.9 0-3.7-.52-5.25-1.425l-.375-.225-3.875 1.025 1.025-3.75-.25-.4A9.955 9.955 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
          @else
            <svg class="ic sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.09 6.09l1.27-.83a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
          @endif
          {{ $ad->cta_label }}
        </a>
      </div>
      @endif
    </div>
    @endforeach
  </div>

  @if($ads->count() > 1)
  {{-- Flèches --}}
  <div class="ad-arrows">
    <button class="ad-arrow" id="adPrev" aria-label="Précédent">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <button class="ad-arrow" id="adNext" aria-label="Suivant">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18l6-6-6-6"/></svg>
    </button>
  </div>
  {{-- Points --}}
  <div class="ad-dots" id="adDots">
    @foreach($ads as $i => $ad)
      <button class="ad-dot {{ $i === 0 ? 'on' : '' }}" data-idx="{{ $i }}" aria-label="Publicité {{ $i+1 }}"></button>
    @endforeach
  </div>
  @endif
</div>

@push('scripts')
<script>
(function(){
  var track=document.getElementById('adTrack');
  var dots=[].slice.call(document.querySelectorAll('.ad-dot'));
  if(!track||!dots.length)return;
  var total={{ $ads->count() }},cur=0,timer;

  function go(n){
    cur=(n+total)%total;
    track.style.transform='translateX(-'+cur*100+'%)';
    dots.forEach(function(d,i){d.classList.toggle('on',i===cur);});
  }

  function next(){go(cur+1);}
  function prev(){go(cur-1);}
  function startAuto(){timer=setInterval(next,5000);}
  function stopAuto(){clearInterval(timer);}

  document.getElementById('adNext')?.addEventListener('click',function(){stopAuto();next();startAuto();});
  document.getElementById('adPrev')?.addEventListener('click',function(){stopAuto();prev();startAuto();});
  dots.forEach(function(d){d.addEventListener('click',function(){stopAuto();go(+d.dataset.idx);startAuto();});});

  /* Swipe tactile */
  var sx=0;
  track.addEventListener('touchstart',function(e){sx=e.touches[0].clientX;},{ passive:true });
  track.addEventListener('touchend',function(e){
    var dx=e.changedTouches[0].clientX-sx;
    if(Math.abs(dx)>40){stopAuto();dx<0?next():prev();startAuto();}
  },{ passive:true });

  if(total>1)startAuto();
})();
</script>
@endpush
@endif

<!-- ===================== DEMANDES ===================== -->
<section id="demandes"><div class="wrap">
  <div class="sec-head center reveal">
    <span class="label center">Un cercle protégé</span>
    <h2>La rencontre, en toute <em>discrétion</em></h2>
    <p style="max-width:560px;margin:14px auto 0;color:var(--muted)">
      Chez TàakDiàkka, les profils ne sont jamais exposés au public. On y entre par la confiance.
    </p>
  </div>

  <div class="privacy-grid reveal">
    <div class="privacy-card">
      <svg class="ic lg"><use href="#i-verified"/></svg>
      <h3>Profils vérifiés</h3>
      <p>Chaque membre passe par une vérification par paliers. Vous échangez avec de vraies personnes, sérieuses.</p>
    </div>
    <div class="privacy-card">
      <svg class="ic lg"><use href="#i-user"/></svg>
      <h3>Visibilité maîtrisée</h3>
      <p>Vos photos et informations restent privées, visibles uniquement par les membres connectés.</p>
    </div>
    <div class="privacy-card">
      <svg class="ic lg"><use href="#i-heart"/></svg>
      <h3>Compatibilité guidée</h3>
      <p>Dès votre inscription, nous vous présentons les profils qui correspondent vraiment à votre recherche.</p>
    </div>
  </div>

  <div class="privacy-cta reveal">
    <a href="{{ route('register') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-heart"/></svg>Rejoindre gratuitement</a>
    <a href="{{ route('login') }}" class="lnk">J'ai déjà un compte<svg class="ic sm"><use href="#i-arrow"/></svg></a>
  </div>
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
      <span class="st-ic"><svg class="ic"><use href="#i-user"/></svg></span>
    </div>
    <div class="step-row reveal" data-d="1">
      <div class="no">02</div>
      <div class="st-txt"><h3>Découvrez vos affinités</h3><p>Notre compatibilité met en avant les profils qui vous correspondent vraiment.</p></div>
      <span class="st-ic"><svg class="ic"><use href="#i-spark"/></svg></span>
    </div>
    <div class="step-row reveal" data-d="2">
      <div class="no">03</div>
      <div class="st-txt"><h3>Échangez en confiance</h3><p>Une messagerie sécurisée et modérée, à votre rythme et en toute discrétion.</p></div>
      <span class="st-ic"><svg class="ic"><use href="#i-message"/></svg></span>
    </div>
    <div class="step-row reveal" data-d="3">
      <div class="no">04</div>
      <div class="st-txt"><h3>Rencontrez, puis célébrez</h3><p>Des connexions vérifiées qui mènent à de vraies histoires — jusqu'au mariage.</p></div>
      <span class="st-ic"><svg class="ic"><use href="#i-rings"/></svg></span>
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

  @include('partials.community-carousel', ['posts' => $posts])
</div></section>

<!-- ===================== HISTOIRES (sombre) ===================== -->
<section id="histoires" class="sec--dark on-dark"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">Ils se sont dit oui ❤️</span>
    <h2>Histoires de <em>réussite</em></h2>
    <p>Ils se sont rencontrés sur TàakDiàkka. Aujourd'hui, ils écrivent leur histoire.</p>
  </div>
  <div class="trust-row reveal">
    <div class="stars" aria-label="Note de 4,9 sur 5">★★★★★</div>
    <p><b>4,9/5</b> · la confiance d'une communauté qui croit au mariage <span class="dot"></span> <b>850+</b> unions célébrées</p>
  </div>
  <div class="stories stories-carousel">
    @foreach($stories as $s)
      @include('partials.story-card', ['s' => $s, 'stagger' => $loop->index % 3])
    @endforeach
  </div>
  <div class="more-row reveal"><a href="{{ route('histoires') }}" class="btn btn-line">Voir toutes les histoires<svg class="ic sm"><use href="#i-arrow"/></svg></a></div>
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
      <a href="{{ route('register') }}" class="btn btn-primary">Créer ma demande<svg class="ic sm"><use href="#i-arrow"/></svg></a>
      <a href="#" class="lnk">Découvrir les abonnements</a>
    </div>
  </div>
</div></section>

<!-- ===================== TARIFS ===================== -->
@include('partials.pricing')

<!-- ===================== FAQ ===================== -->
<section id="faq" class="sec--alt"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">Questions fréquentes</span>
    <h2>Vous vous <em>demandez</em> ?</h2>
    <p>L'essentiel à savoir avant de commencer votre recherche.</p>
  </div>

  <div class="faq reveal">
    <details class="faq-item">
      <summary>Comment fonctionne TàakDiàkka ?<span class="faq-plus"></span></summary>
      <p>Vous publiez une demande en mariage, vous parcourez des profils vérifiés, et vous échangez en toute confiance via la messagerie. Notre compatibilité met en avant les profils qui vous correspondent vraiment.</p>
    </details>
    <details class="faq-item">
      <summary>L'inscription est-elle gratuite ?<span class="faq-plus"></span></summary>
      <p>Oui. La formule Découverte est gratuite à vie : créer une demande, parcourir les profils, participer à la communauté. Les options avancées (messages illimités, filtres, mises en avant) sont disponibles via les abonnements Premium et Prestige.</p>
    </details>
    <details class="faq-item">
      <summary>Mes informations sont-elles confidentielles ?<span class="faq-plus"></span></summary>
      <p>Absolument. Vous choisissez ce que vous partagez : vous pouvez publier un profil discret (sans nom ni photo). Vos documents de vérification restent strictement confidentiels et ne servent qu'à confirmer votre identité.</p>
    </details>
    <details class="faq-item">
      <summary>Comment être vérifié(e) ?<span class="faq-plus"></span></summary>
      <p>La vérification se fait par paliers : Bronze (e-mail et téléphone), Argent (pièce d'identité), Or (identité + selfie de correspondance). Un badge 🟢 vérifié inspire confiance et attire davantage de demandes sérieuses.</p>
    </details>
    <details class="faq-item">
      <summary>Quels moyens de paiement acceptez-vous ?<span class="faq-plus"></span></summary>
      <p>Wave, Orange Money, Free Money et carte bancaire — adaptés au Sénégal comme à la diaspora. Le paiement est sécurisé et sans engagement : vous pouvez arrêter à tout moment.</p>
    </details>
  </div>
</div></section>

@endsection
