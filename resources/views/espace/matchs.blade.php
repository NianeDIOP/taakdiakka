@extends('layouts.member')

@section('title', 'Matchs & intérêts — TàakDiàkka')

@php
  $photo = function ($u) {
      $p = $u->profile;
      return $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u->name);
  };
@endphp

@section('content')

<div class="m-head">
  <span class="label">Mise en relation</span>
  <h2>Mes <em style="color:var(--ink)">matchs</em> & intérêts</h2>
  <p>Vos affinités réciproques et les membres qui s'intéressent à vous.</p>
</div>

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin-bottom:4px">Vos matchs 💞</h3>
@if($matchs->count())
  <div class="rel-grid">
    @foreach($matchs as $u)
      <div class="rel-card">
        <a href="{{ route('members.show', $u) }}" class="av photo rel-ava" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
        <a href="{{ route('members.show', $u) }}" style="text-decoration:none;color:inherit"><b>{{ $u->name }}</b></a>
        <span class="rel-tag" style="color:var(--heart)">❤ Match</span>
        <form action="{{ route('messages.start', $u) }}" method="POST">@csrf
          <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-message"/></svg>Discuter</button>
        </form>
      </div>
    @endforeach
  </div>
@else
  <p style="color:var(--muted);margin:14px 0 40px">Pas encore de match. Marquez votre intérêt sur des profils — quand c'est réciproque, ils apparaissent ici. 🤲</p>
@endif

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin:10px 0 4px">Intérêts reçus</h3>
@if($received->count())
  <div class="rel-grid">
    @foreach($received as $u)
      <div class="rel-card">
        <a href="{{ route('members.show', $u) }}" class="av photo rel-ava" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
        <a href="{{ route('members.show', $u) }}" style="text-decoration:none;color:inherit"><b>{{ $u->name }}</b></a>
        <span class="rel-tag">Vous a remarqué(e)</span>
        <form action="{{ route('interests.toggle', $u) }}" method="POST">@csrf
          <button type="submit" class="btn btn-line"><svg class="ic sm"><use href="#i-heart"/></svg>Intéressé(e) aussi</button>
        </form>
      </div>
    @endforeach
  </div>
@else
  <p style="color:var(--muted);margin:14px 0">Personne ne s'est encore manifesté. Complétez votre profil pour attirer l'attention. ✨</p>
@endif

@endsection
