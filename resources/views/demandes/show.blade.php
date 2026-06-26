@extends('layouts.app')

@section('title', $demande->display_name . ' — TàakDiàkka')
@section('description', 'Profil de ' . $demande->display_name . ' sur TàakDiàkka — ' . $demande->region . '.')

@section('content')

<section class="profile-section"><div class="wrap profile-wrap">
  <a href="{{ route('demandes.index') }}" class="lnk profile-back"><svg class="ic sm" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg>Retour aux demandes</a>

  <div class="profile">
    <div class="profile-photo reveal in">
      @if($demande->is_discret)
        <div class="discret-ic"><svg class="ic lg"><use href="#i-user"/></svg><span>Photo sur demande</span></div>
      @else
        @php $base = pathinfo($demande->photo, PATHINFO_FILENAME); @endphp
        <picture>
          <source srcset="{{ asset('img/'.$base.'.webp') }}" type="image/webp" />
          <img src="{{ asset('img/'.$base.'.jpg') }}" alt="{{ $demande->name }}" width="760" height="950" decoding="async" />
        </picture>
      @endif
      <span class="vdot"><svg class="ic"><use href="#i-check"/></svg></span>
    </div>

    <div class="profile-meta reveal in" data-d="1">
      <span class="label">Demande publique</span>
      <h1>{{ $demande->display_name }}</h1>
      <div class="profile-loc"><svg class="ic"><use href="#i-pin"/></svg>{{ $demande->region }}</div>
      <div class="profile-job">{{ $demande->is_discret ? 'Profil discret' : $demande->profession }}</div>

      <span class="profile-badge"><svg class="ic"><use href="#i-verified"/></svg>Vérifié — niveau {{ $demande->verification_level }}</span>

      <p class="profile-quote">« {{ $demande->quote }} »</p>

      <dl class="profile-facts">
        <div class="row"><dt>Recherche</dt><dd>{{ $demande->seeking ?? '—' }}</dd></div>
        <div class="row"><dt>Âge</dt><dd>{{ $demande->age }} ans</dd></div>
        <div class="row"><dt>Région</dt><dd>{{ $demande->region }}</dd></div>
        <div class="row"><dt>Publiée</dt><dd>{{ ucfirst($demande->posted) }}</dd></div>
      </dl>

      <div class="tags-light">@foreach($demande->tags ?? [] as $t)<span>{{ $t }}</span>@endforeach</div>

      <div class="profile-cta">
        @auth
          @if($demande->user_id && $demande->user_id !== auth()->id())
            @php
              $owner = $demande->user;
              $isInterested = $owner && auth()->user()->isInterestedIn($owner);
              $isMatch = $owner && auth()->user()->isMatchedWith($owner);
              $isFollowing = $owner && auth()->user()->isFollowing($owner);
            @endphp
            <form action="{{ route('messages.contact', $demande) }}" method="POST">@csrf
              <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-message"/></svg>Contacter{{ $demande->is_discret ? '' : ' '.$demande->name }}</button>
            </form>
            <form action="{{ route('interests.toggle', $demande->user_id) }}" method="POST">@csrf
              <button type="submit" class="btn btn-line">
                <svg class="ic sm {{ $isInterested || $isMatch ? 'heart' : '' }}"><use href="#i-heart"/></svg>{{ $isMatch ? 'Match ❤' : ($isInterested ? 'Intérêt envoyé' : 'Je suis intéressé(e)') }}
              </button>
            </form>
            <form action="{{ route('follow.toggle', $demande->user_id) }}" method="POST">@csrf
              <button type="submit" class="btn btn-line">
                <svg class="ic sm"><use href="#i-{{ $isFollowing ? 'check' : 'plus' }}"/></svg>{{ $isFollowing ? 'Suivi(e)' : 'Suivre' }}
              </button>
            </form>
          @elseif($demande->user_id === auth()->id())
            <span class="btn btn-line" style="opacity:.6;pointer-events:none">C'est votre demande</span>
          @else
            <span class="btn btn-line" style="opacity:.55;pointer-events:none">Contact indisponible</span>
          @endif
        @else
          <a href="{{ route('login') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-message"/></svg>Contacter{{ $demande->is_discret ? '' : ' '.$demande->name }}</a>
        @endauth
        @auth
          @php $isFav = in_array($demande->id, $favoriteIds ?? []); @endphp
          <form action="{{ route('favoris.toggle', $demande) }}" method="POST">@csrf
            <button type="submit" class="btn btn-line">
              <svg class="ic sm {{ $isFav ? 'heart' : '' }}"><use href="#i-heart"/></svg>{{ $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
            </button>
          </form>
        @endauth
        <a href="#" class="lnk">Signaler</a>
      </div>
    </div>
  </div>

  @php $gallery = (! $demande->is_discret && $demande->user) ? $demande->user->photos : collect(); @endphp
  @if($gallery->count())
    <div class="profile-gallery">
      <span class="label">En images</span>
      <div class="gallery-grid" style="margin-top:14px">
        @foreach($gallery as $ph)
          <div class="gallery-item">
            <picture>
              <source srcset="{{ asset('img/'.$ph->base.'.webp') }}" type="image/webp" />
              <img src="{{ asset('img/'.$ph->base.'.jpg') }}" alt="{{ $demande->name }}" loading="lazy" />
            </picture>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div></section>

@if($similaires->count())
<section id="similaires" class="sec--alt"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">À découvrir</span>
    <h2>Demandes <em>similaires</em></h2>
  </div>
  <div class="listing">
    @foreach($similaires as $d)
      @include('partials.demande-card', ['d' => $d, 'stagger' => $loop->index % 3])
    @endforeach
  </div>
</div></section>
@endif

@endsection
