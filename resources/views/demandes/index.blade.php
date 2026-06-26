@extends('layouts.app')

@section('title', 'Demandes en mariage — TàakDiàkka')
@section('description', 'Parcourez toutes les demandes en mariage vérifiées sur TàakDiàkka. Filtrez par âge, région, pratique.')

@push('styles')
<link rel="preload" as="image" href="{{ asset('img/demandes-hero.webp') }}" type="image/webp" fetchpriority="high" />
@endpush

@section('content')

<section class="dmd-hero">
  <div class="dmd-hero-photo">
    <picture>
      <source srcset="{{ asset('img/demandes-hero.webp') }}" type="image/webp" />
      <img src="{{ asset('img/demandes-hero.jpg') }}" alt="Couples en tenues de mariage sénégalaises" width="1600" height="686" fetchpriority="high" decoding="async" />
    </picture>
  </div>
  <div class="wrap">
    <div class="toolbar reveal">
      <div class="t-left">
        <span class="label">Demandes publiques</span>
        <h2>Toutes les demandes en <em>mariage</em></h2>
        <p>{{ $demandes->total() }} profil{{ $demandes->total() > 1 ? 's' : '' }} vérifié{{ $demandes->total() > 1 ? 's' : '' }}. Affinez votre recherche avec les filtres.</p>
      </div>
      <a href="{{ route('home') }}" class="lnk"><svg class="ic sm"><use href="#i-arrow"/></svg>Retour à l'accueil</a>
    </div>
  </div>
</section>

<section id="demandes"><div class="wrap" style="padding-top:clamp(40px,5vw,64px)">
  <form class="filters reveal" method="GET" action="{{ route('demandes.index') }}">
    <div class="fl"><label>Recherche</label><input type="text" name="q" value="{{ request('q') }}" placeholder="Profession, valeurs…"/></div>
    <div class="fl"><label>Je cherche</label><select name="seeking">
      <option value="">Indifférent</option>
      <option value="Une épouse" @selected(request('seeking') === 'Une épouse')>Une épouse</option>
      <option value="Un époux" @selected(request('seeking') === 'Un époux')>Un époux</option>
    </select></div>
    <div class="fl"><label>Âge</label><select name="age">
      <option value="">Tous</option>
      <option value="20-25" @selected(request('age') === '20-25')>20 – 25</option>
      <option value="26-32" @selected(request('age') === '26-32')>26 – 32</option>
      <option value="33-40" @selected(request('age') === '33-40')>33 – 40</option>
      <option value="41+" @selected(request('age') === '41+')>41 +</option>
    </select></div>
    <div class="fl"><label>Région</label><select name="region">
      <option value="">Toutes</option>
      @foreach(['Dakar','Thiès','Saint-Louis','Touba','Ziguinchor','Diaspora'] as $r)
        <option value="{{ $r }}" @selected(request('region') === $r)>{{ $r }}</option>
      @endforeach
    </select></div>
    <div class="fl"><label>Pratique</label><select name="pratique">
      <option value="">Toutes</option>
      <option value="Pratiquant" @selected(request('pratique') === 'Pratiquant')>Pratiquant(e)</option>
    </select></div>
    <button class="btn btn-primary fl-btn"><svg class="ic sm"><use href="#i-search"/></svg>Filtrer</button>
  </form>

  <div class="listing">
    @forelse($demandes as $d)
      @include('partials.demande-card', ['d' => $d, 'stagger' => $loop->index % 3])
    @empty
      @include('partials.empty', ['icon' => 'search', 'title' => 'Aucun résultat', 'text' => 'Aucune demande ne correspond à vos critères. Essayez d\'élargir votre recherche.', 'ctaUrl' => route('demandes.index'), 'ctaLabel' => 'Réinitialiser les filtres'])
    @endforelse
  </div>

  @if($demandes->hasPages())
    <nav class="pager reveal">
      @if($demandes->onFirstPage())
        <span class="pager-btn disabled">Précédent</span>
      @else
        <a class="pager-btn" href="{{ $demandes->previousPageUrl() }}" rel="prev">Précédent</a>
      @endif
      <span class="pager-info">Page {{ $demandes->currentPage() }} / {{ $demandes->lastPage() }}</span>
      @if($demandes->hasMorePages())
        <a class="pager-btn" href="{{ $demandes->nextPageUrl() }}" rel="next">Suivant</a>
      @else
        <span class="pager-btn disabled">Suivant</span>
      @endif
    </nav>
  @endif
</div></section>

@endsection
