@extends(auth()->check() ? 'layouts.member' : 'layouts.app')

@section('title', 'Communauté — Confessions & entraide · TàakDiàkka')
@section('description', 'Confessions anonymes, conseils et témoignages — la communauté bienveillante de TàakDiàkka.')

@auth
  @section('content')
    <div class="m-head">
      <span class="label">La communauté</span>
      <h2>Confessions &amp; <em style="color:var(--ink)">entraide</em></h2>
      <p>Un espace bienveillant pour partager, poser ses questions et s'entraider — anonymement ou non.</p>
    </div>
    @include('partials.community-grid')
  @endsection
@else
  @push('styles')
  <link rel="preload" as="image" href="{{ asset('img/communaute-hero.webp') }}" type="image/webp" fetchpriority="high" />
  @endpush

  @section('content')
    <section class="dmd-hero">
      <div class="dmd-hero-photo">
        <picture>
          <source srcset="{{ asset('img/communaute-hero.webp') }}" type="image/webp" />
          <img src="{{ asset('img/communaute-hero.jpg') }}" alt="Membres de la communauté TàakDiàkka" width="1600" height="686" fetchpriority="high" decoding="async" />
        </picture>
      </div>
      <div class="wrap">
        <div class="toolbar reveal">
          <div class="t-left">
            <span class="label">La communauté</span>
            <h2>Confessions &amp; <em>entraide</em></h2>
            <p>Un espace bienveillant pour partager, poser ses questions et s'entraider — anonymement ou non.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="communaute"><div class="wrap" style="padding-top:clamp(40px,5vw,64px)">
      @include('partials.community-grid')
    </div></section>
  @endsection
@endauth
