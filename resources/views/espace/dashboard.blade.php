@extends('layouts.member')

@section('title', 'Tableau de bord — TàakDiàkka')
@section('pagetitle', 'Tableau de bord')

@section('content')
@php $c = $profile->completion; @endphp

<div style="display:flex;align-items:center;gap:20px;margin-bottom:14px">
  <div style="width:72px;height:90px;flex-shrink:0;background:var(--ivory-2);box-shadow:inset 0 0 0 1px var(--line);overflow:hidden;display:grid;place-items:center">
    @if($profile->photo)
      @php $pb = pathinfo($profile->photo, PATHINFO_FILENAME); @endphp
      <img src="{{ asset('img/'.$pb.'.webp') }}" alt="" style="width:100%;height:100%;object-fit:cover" onerror="this.onerror=null;this.src='{{ asset('img/'.$pb.'.jpg') }}'" />
    @else
      <span style="font-family:var(--font-serif);font-size:1.7rem;color:var(--muted)">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
    @endif
  </div>
  <div>
    <span class="label">Mon espace</span>
    <h2 style="font-family:var(--font-serif);font-weight:500;font-size:clamp(1.9rem,4vw,2.8rem);margin:10px 0 0">
      Bonjour, {{ auth()->user()->name }} <span style="color:var(--gold)">❤</span>
    </h2>
  </div>
</div>
<p style="color:var(--muted);max-width:560px;margin-bottom:30px">Gérez votre profil, votre demande en mariage et votre participation à la communauté.</p>

<div class="completion">
  <div class="top">
    <div>
      <strong style="font-size:1.02rem">Complétion de votre profil</strong><br/>
      <small>Un profil complet inspire confiance et attire davantage de demandes sérieuses.</small>
    </div>
    <span class="pc">{{ $c }}%</span>
  </div>
  <div class="bar"><i style="width:{{ $c }}%"></i></div>
  <a class="lnk" href="{{ route('profile.edit') }}" style="display:inline-flex;margin-top:14px">
    {{ $c < 100 ? 'Compléter mon profil' : 'Mettre à jour mon profil' }}<svg class="ic sm"><use href="#i-arrow"/></svg>
  </a>
</div>

<div class="stat-row">
  <a href="{{ route('matchs') }}" class="stat-card">
    <span class="stat-n" style="color:var(--heart)">{{ $stats['matchs'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-heart"/></svg>Matchs</span>
  </a>
  <a href="{{ route('matchs') }}" class="stat-card">
    <span class="stat-n">{{ $stats['interests'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-spark"/></svg>Intérêts reçus</span>
  </a>
  <a href="{{ route('visitors') }}" class="stat-card">
    <span class="stat-n">{{ $stats['visitors'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-eye"/></svg>Visiteurs</span>
  </a>
  <a href="{{ route('visitors') }}" class="stat-card">
    <span class="stat-n">{{ $stats['followers'] }}</span>
    <span class="stat-l"><svg class="ic sm"><use href="#i-user"/></svg>Abonnés</span>
  </a>
</div>

<div class="dash-grid">
  <div class="dash-card"><svg class="ic lg"><use href="#i-user"/></svg><h3>Mon profil</h3><p>Religion, enfants, union, taille, teint, études…</p><a href="{{ route('profile.show') }}" class="lnk">Voir / modifier</a></div>
  <div class="dash-card"><svg class="ic lg"><use href="#i-rings"/></svg><h3>Ma demande</h3><p>Publiez ou modifiez votre demande en mariage.</p><a href="{{ route('demandes.mine') }}" class="lnk">Gérer</a></div>
  <div class="dash-card"><svg class="ic lg"><use href="#i-message"/></svg><h3>Mes messages</h3><p>Vos échanges avec les profils contactés.</p><a href="{{ route('messages') }}" class="lnk">Ouvrir</a></div>
  <div class="dash-card"><svg class="ic lg"><use href="#i-chat"/></svg><h3>Communauté</h3><p>Vos confessions, conseils et témoignages.</p><a href="{{ route('communaute') }}" class="lnk">Voir</a></div>
  <div class="dash-card"><svg class="ic lg"><use href="#i-heart"/></svg><h3>Mes favoris</h3><p>Les profils que vous avez remarqués.</p><a href="{{ route('favoris') }}" class="lnk">Parcourir</a></div>
  <div class="dash-card"><svg class="ic lg"><use href="#i-verified"/></svg><h3>Vérification</h3><p>Augmentez votre niveau de confiance (Bronze → Or).</p><a href="{{ route('verification') }}" class="lnk">Vérifier</a></div>
</div>

@endsection
