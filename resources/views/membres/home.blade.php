@extends('layouts.member')

@section('title', 'Accueil — TàakDiàkka')

@section('content')

@php
  $me = auth()->user();
  $c = $profile->completion;
  $pb = $profile->photo ? pathinfo($profile->photo, PATHINFO_FILENAME) : null;
  $chips = array_filter([
    $profile->seeking, $profile->region, $profile->religion, $profile->profession,
  ]);
@endphp

<span class="label">Mon espace</span>
<h2 class="m-greet">Bonjour, {{ \Illuminate\Support\Str::before($me->name, ' ') }} <span style="color:var(--gold)">❤</span></h2>

{{-- Carte de statut : profil + demande de mariage --}}
<div class="mystatus">
  <div class="mystatus-ava">
    @if($pb)
      <img src="{{ asset('img/'.$pb.'.webp') }}" alt="" onerror="this.onerror=null;this.src='{{ asset('img/'.$pb.'.jpg') }}'" />
    @else
      <span>{{ strtoupper(mb_substr($me->name, 0, 1)) }}</span>
    @endif
  </div>
  <div class="mystatus-body">
    <div class="mystatus-top">
      <div>
        <b>{{ $me->name }}</b>
        <small>Profil complété à {{ $c }}%@if(! $hasDemande) · demande de mariage non publiée @endif</small>
      </div>
      <span class="mystatus-pc">{{ $c }}%</span>
    </div>
    <div class="mystatus-bar"><i style="width:{{ max($c, 4) }}%"></i></div>

    @if($chips)
      <div class="mystatus-chips">
        @foreach($chips as $ch)<span>{{ $ch }}</span>@endforeach
      </div>
    @endif

    <div class="mystatus-cta">
      <a href="{{ route('profile.edit') }}" class="btn btn-line"><svg class="ic sm"><use href="#i-user"/></svg>{{ $c < 100 ? 'Compléter mon profil' : 'Modifier mon profil' }}</a>
      @if($hasDemande)
        <a href="{{ route('demandes.mine') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-rings"/></svg>Gérer ma demande de mariage</a>
      @elseif($sought)
        <form action="{{ route('demandes.publish') }}" method="POST">@csrf
          <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-rings"/></svg>Publier ma demande de mariage</button>
        </form>
      @else
        <a href="{{ route('profile.edit') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-rings"/></svg>Activer ma demande (compléter le profil)</a>
      @endif
    </div>
  </div>
</div>

{{-- Stats rapides --}}
<div class="stat-row" style="margin:0 0 30px">
  <a href="{{ route('matchs') }}" class="stat-card">
    <span class="stat-n" style="color:var(--heart)">{{ $stats['matchs'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-heart"/></svg>Matchs</span>
  </a>
  <a href="{{ route('visitors') }}" class="stat-card">
    <span class="stat-n">{{ $stats['visitors'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-eye"/></svg>Visiteurs</span>
  </a>
  <a href="{{ route('visitors') }}" class="stat-card">
    <span class="stat-n">{{ $stats['followers'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-user"/></svg>Abonnés</span>
  </a>
  <a href="{{ route('members.discover') }}" class="stat-card">
    <span class="stat-n" style="color:var(--gold)"><svg class="ic" style="width:26px;height:26px"><use href="#i-search"/></svg></span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-arrow"/></svg>Découvrir</span>
  </a>
</div>

@include('partials.boost-cta')

@if($needsInfo)
  <div class="completion" style="margin-bottom:30px">
    <div class="top"><div>
      <strong>Renseignez votre genre pour des suggestions ciblées</strong><br/>
      <small>Nous présentons uniquement les profils du genre que vous recherchez.</small>
    </div></div>
    <a class="lnk" href="{{ route('profile.edit') }}" style="display:inline-flex;margin-top:12px">Compléter mon profil<svg class="ic sm"><use href="#i-arrow"/></svg></a>
  </div>
@endif

{{-- Recherche rapide --}}
<form method="GET" action="{{ route('dashboard') }}" class="quick-search">
  <svg class="ic sm"><use href="#i-search"/></svg>
  <input type="text" name="q" value="{{ $term }}" placeholder="Rechercher par nom, profession, région…" />
  <button type="submit" class="btn btn-primary">Rechercher</button>
  @if($term)<a href="{{ route('dashboard') }}" class="lnk">Effacer</a>@endif
</form>

@if($isSearch)
  <section class="msection">
    <div class="msection-head"><h3>Résultats <span style="color:var(--muted);font-size:1rem">· {{ $results->count() }}</span></h3></div>
    @if($results->count())
      <div class="listing">
        @foreach($results as $m)
          @include('partials.member-card', ['m' => $m, 'stagger' => $loop->index % 3])
        @endforeach
      </div>
    @else
      <p class="empty">Aucun profil ne correspond. <a href="{{ route('members.discover') }}" class="lnk">Essayer Découvrir</a></p>
    @endif
  </section>
@else
  @if($coup && $coup->profile)
    @php $cp = $coup->profile; $cb = $cp->photo ? pathinfo($cp->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($coup->name); @endphp
    <section class="coup reveal">
      <a href="{{ route('members.show', $coup) }}" class="coup-photo" style="background-image:url('{{ asset('img/'.$cb.'.webp') }}')" aria-label="Voir le profil">
        <span class="coup-pct">{{ $coupPct }}<small>%</small></span>
      </a>
      <div class="coup-body">
        <span class="label">Coup de cœur du jour ❤</span>
        <h3>{{ \Illuminate\Support\Str::before($coup->name, ' ') }}@if($cp->age), {{ $cp->age }} ans @endif</h3>
        <p class="coup-meta"><svg class="ic sm"><use href="#i-pin"/></svg>{{ $cp->region ?? '—' }}@if($cp->profession) · {{ $cp->profession }}@endif</p>
        @if($cp->bio)<p class="coup-quote">« {{ \Illuminate\Support\Str::limit($cp->bio, 120) }} »</p>@endif
        <div class="coup-cta">
          <a href="{{ route('members.show', $coup) }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-user"/></svg>Voir le profil</a>
          <form action="{{ route('interests.toggle', $coup) }}" method="POST">@csrf
            <button class="btn btn-line"><svg class="ic sm heart"><use href="#i-heart"/></svg>Marquer mon intérêt</button>
          </form>
        </div>
      </div>
    </section>
  @endif
  @include('partials.member-section', [
    'items' => $compatible, 'title' => 'Profils', 'em' => 'compatibles',
    'sub' => 'Sélectionnés selon votre région, religion et âge.',
    'more' => route('members.discover'), 'moreLabel' => 'Découvrir plus',
  ])

  @include('partials.member-section', [
    'items' => $recent, 'title' => 'Nouveaux', 'em' => 'membres',
    'more' => route('members.discover'), 'moreLabel' => 'Tout voir',
  ])

  @include('partials.member-section', [
    'items' => $visitors, 'title' => 'Ils ont vu votre', 'em' => 'profil',
    'more' => route('visitors'), 'moreLabel' => 'Tous les visiteurs',
  ])

  @if($compatible->isEmpty() && $recent->isEmpty() && ! $needsInfo)
    <p class="empty">Aucun membre disponible pour l'instant. Revenez bientôt. 🤲</p>
  @endif
@endif

@endsection
