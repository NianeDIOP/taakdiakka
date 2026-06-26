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

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin-bottom:4px">Visiteurs récents 👀</h3>
@if($visitors->count())
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
@if($followers->count())
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
