@extends('layouts.member')

@section('title', 'Favoris — TàakDiàkka')

@section('content')

<div class="m-head">
  <span class="label">Espace membre</span>
  <h2>Mes <em style="color:var(--ink)">favoris</em></h2>
  <p>Les membres que vous avez ajoutés avec le cœur ❤.</p>
</div>

@if($members->count())
  <div class="listing" style="margin-bottom:48px">
    @foreach($members as $m)
      @include('partials.member-card', ['m' => $m, 'stagger' => $loop->index % 3])
    @endforeach
  </div>
@else
  <div style="margin-bottom:48px">
    @include('partials.empty', ['icon' => 'heart', 'title' => 'Pas encore de favoris', 'text' => 'Cliquez sur le ❤ d\'un profil pour le retrouver ici, à l\'abri des regards.', 'ctaUrl' => route('members.discover'), 'ctaLabel' => 'Découvrir des membres'])
  </div>
@endif

@if($suggestions->count())
  <div class="msection-head" style="margin-bottom:14px">
    <h3 style="font-family:var(--font-serif);font-size:1.5rem;font-weight:500">Suggestions <em style="color:var(--ink)">pour vous</em></h3>
    <a href="{{ route('members.discover') }}" class="lnk">Découvrir plus<svg class="ic sm"><use href="#i-arrow"/></svg></a>
  </div>
  <div class="listing">
    @foreach($suggestions as $m)
      @include('partials.member-card', ['m' => $m, 'stagger' => $loop->index % 3])
    @endforeach
  </div>
@endif

@endsection
