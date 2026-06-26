@extends('layouts.app')

@section('title', 'Histoires de réussite — TàakDiàkka')
@section('description', 'Ils se sont rencontrés sur TàakDiàkka et écrivent aujourd\'hui leur histoire. Découvrez leurs témoignages.')

@push('styles')
<link rel="preload" as="image" href="{{ asset('img/histoires-hero.webp') }}" type="image/webp" fetchpriority="high" />
@endpush

@section('content')

<section class="dmd-hero dmd-hero--dark">
  <div class="dmd-hero-photo">
    <picture>
      <source srcset="{{ asset('img/histoires-hero.webp') }}" type="image/webp" />
      <img src="{{ asset('img/histoires-hero.jpg') }}" alt="Couple en tenues de mariage sénégalaises" width="1600" height="686" fetchpriority="high" decoding="async" />
    </picture>
  </div>
  <div class="wrap">
    <div class="toolbar reveal">
      <div class="t-left">
        <span class="label">Ils se sont dit oui ❤️</span>
        <h2>Histoires de <em>réussite</em></h2>
        <p>Ils se sont rencontrés sur TàakDiàkka. Aujourd'hui, ils écrivent leur histoire.</p>
      </div>
    </div>
  </div>
</section>

<section id="histoires" class="sec--dark on-dark"><div class="wrap" style="padding-top:clamp(40px,5vw,64px)">
  <div class="stories">
    @forelse($stories as $s)
      @include('partials.story-card', ['s' => $s, 'stagger' => $loop->index % 3])
    @empty
      <p style="color:var(--muted-d);padding:34px">Aucune histoire pour le moment.</p>
    @endforelse
  </div>

  <div class="cta" style="margin-top:clamp(60px,8vw,90px)">
    <span class="label center">À votre tour</span>
    <h2 style="font-size:clamp(2rem,4.5vw,3.4rem);margin:18px auto 22px">Et si la prochaine histoire <em>était la vôtre</em> ?</h2>
    <div class="hero-cta" style="justify-content:center">
      <a href="{{ route('demandes.index') }}" class="btn btn-primary">Parcourir les demandes<svg class="ic sm"><use href="#i-arrow"/></svg></a>
      <a href="{{ route('register') }}" class="lnk">Créer ma demande</a>
    </div>
  </div>
</div></section>

@endsection
