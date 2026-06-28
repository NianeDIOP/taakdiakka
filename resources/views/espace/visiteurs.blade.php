@extends('layouts.member')

@section('title', 'Visiteurs & abonnés — TàakDiàkka')

@php
  $photo = function ($u) {
      $p = $u->profile;
      return $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u->name);
  };
@endphp

@section('content')

<div class="m-head">
  <span class="label">Visibilité</span>
  <h2>Qui a vu votre <em style="color:var(--ink)">profil</em></h2>
  <p>Les membres qui ont consulté votre demande et ceux qui vous suivent.</p>
</div>

@include('partials.boost-cta')

@php
  $lockTeaser = function ($count, $label) {
      $n = max(min($count, 6), 3);
      $cards = '';
      for ($i = 0; $i < $n; $i++) {
          $cards .= '<div class="rel-card locked"><span class="av rel-ava"></span><b>Membre</b><span class="rel-tag">' . $label . '</span></div>';
      }
      return $cards;
  };
@endphp

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin-bottom:4px">Visiteurs récents 👀</h3>
@if(! $canSee && $visitors->count())
  <div class="locked-teaser">
    <div class="rel-grid locked-grid" aria-hidden="true">{!! $lockTeaser($visitors->count(), 'A vu votre profil') !!}</div>
    <div class="locked-overlay">
      <span class="locked-ic"><svg class="ic"><use href="#i-eye"/></svg></span>
      <b>{{ $visitors->count() }} membre{{ $visitors->count() > 1 ? 's ont' : ' a' }} vu votre profil</b>
      <p>Découvrez qui s'intéresse à vous en passant Premium.</p>
      <a href="{{ route('tarifs') }}" class="btn btn-primary">Voir les formules ✨</a>
    </div>
  </div>
@elseif($visitors->count())
  <div class="rel-grid">
    @foreach($visitors as $u)
      <div class="rel-card">
        <a href="{{ route('members.show', $u) }}" class="av photo rel-ava" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
        <a href="{{ route('members.show', $u) }}" style="text-decoration:none;color:inherit"><b>{{ $u->name }}</b></a>
        <span class="rel-tag">A vu votre profil · {{ $u->pivot->updated_at?->locale('fr')->diffForHumans() }}</span>
        <div class="rel-actions">
          <form action="{{ route('follow.toggle', $u) }}" method="POST">@csrf
            <button type="submit" class="btn btn-line">
              <svg class="ic sm {{ $followingIds->contains($u->id) ? 'heart' : '' }}"><use href="#i-{{ $followingIds->contains($u->id) ? 'check' : 'plus' }}"/></svg>{{ $followingIds->contains($u->id) ? 'Suivi(e)' : 'Suivre' }}
            </button>
          </form>
          <form action="{{ route('messages.start', $u) }}" method="POST">@csrf
            <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-message"/></svg>Discuter</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
@else
  @include('partials.empty', ['icon' => 'eye', 'title' => 'Aucune visite pour l\'instant', 'text' => 'Complétez votre profil et publiez votre demande pour attirer l\'attention. ✨', 'ctaUrl' => route('profile.edit'), 'ctaLabel' => 'Compléter mon profil', 'ctaIcon' => 'user'])
@endif

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin:10px 0 4px">Qui me suit 💫</h3>
@if(! $canSee && $followers->count())
  <div class="locked-teaser">
    <div class="rel-grid locked-grid" aria-hidden="true">{!! $lockTeaser($followers->count(), 'Vous suit') !!}</div>
    <div class="locked-overlay">
      <span class="locked-ic"><svg class="ic"><use href="#i-user"/></svg></span>
      <b>{{ $followers->count() }} membre{{ $followers->count() > 1 ? 's vous suivent' : ' vous suit' }}</b>
      <p>Passez Premium pour voir qui vous suit et échanger.</p>
      <a href="{{ route('tarifs') }}" class="btn btn-primary">Voir les formules ✨</a>
    </div>
  </div>
@elseif($followers->count())
  <div class="rel-grid">
    @foreach($followers as $u)
      <div class="rel-card">
        <a href="{{ route('members.show', $u) }}" class="av photo rel-ava" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
        <a href="{{ route('members.show', $u) }}" style="text-decoration:none;color:inherit"><b>{{ $u->name }}</b></a>
        <span class="rel-tag">Vous suit</span>
        <div class="rel-actions">
          <form action="{{ route('follow.toggle', $u) }}" method="POST">@csrf
            <button type="submit" class="btn btn-line">
              <svg class="ic sm {{ $followingIds->contains($u->id) ? 'heart' : '' }}"><use href="#i-{{ $followingIds->contains($u->id) ? 'check' : 'plus' }}"/></svg>{{ $followingIds->contains($u->id) ? 'Suivi(e)' : 'Suivre en retour' }}
            </button>
          </form>
          <form action="{{ route('messages.start', $u) }}" method="POST">@csrf
            <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-message"/></svg>Discuter</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
@else
  <p style="color:var(--muted);margin:14px 0">Personne ne vous suit encore. 🤲</p>
@endif

@endsection
