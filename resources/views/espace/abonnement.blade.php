@extends('layouts.member')

@section('title', 'Mon abonnement — TàakDiàkka')

@php
  $inf = fn ($n) => $n === PHP_INT_MAX;
  $premiumPlan = $plans->firstWhere('is_premium', true);
@endphp

@section('content')

<div class="m-head">
  <span class="label">Mon compte</span>
  <h2>Mon <em style="color:var(--ink)">abonnement</em></h2>
  <p>Votre formule actuelle, ce qu'elle inclut, et comment débloquer plus.</p>
</div>

{{-- Statut actuel --}}
<div class="sub-status {{ $isPremium ? 'is-premium' : '' }}">
  <div>
    <span class="sub-status-label">Formule actuelle</span>
    <div class="sub-status-plan">
      {{ $isPremium ? ($sub?->plan?->name ?? 'Premium') : 'Découverte (gratuit)' }}
      @if($isPremium)<svg class="ic"><use href="#i-spark"/></svg>@endif
    </div>
    @if($isPremium && $sub?->ends_at)
      <span class="sub-status-meta">Actif jusqu'au {{ $sub->ends_at->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
    @elseif(! $isPremium)
      <span class="sub-status-meta">Gratuit, pour toujours</span>
    @endif
  </div>
  @unless($isPremium)
    <a href="{{ route('tarifs') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-spark"/></svg>Passer au Premium</a>
  @else
    <a href="{{ route('tarifs') }}" class="btn btn-line">Gérer / changer de formule</a>
  @endunless
</div>

@unless($enforced)
  <p class="sub-note">ℹ️ La plateforme est actuellement en accès libre : toutes les fonctionnalités sont disponibles pour tous les membres. Les limites ci-dessous s'appliqueront une fois l'abonnement activé par l'administration.</p>
@endunless

{{-- Ce que comprend ma formule (limites actuelles) --}}
<div class="sub-grid">
  <div class="sub-card">
    <h3>Ce que vous pouvez faire</h3>
    <ul class="sub-list">
      <li class="ok"><svg class="ic sm"><use href="#i-check"/></svg>Parcourir les membres compatibles</li>
      <li class="ok"><svg class="ic sm"><use href="#i-check"/></svg>Participer à la communauté</li>
      <li class="{{ $limits['friend'] ? 'ok' : 'no' }}"><svg class="ic sm"><use href="#i-{{ $limits['friend'] ? 'check' : 'x' }}"/></svg>
        Envoyer des demandes d'ami {{ $limits['friend'] ? '' : '(réservé aux abonnés)' }}</li>
      <li class="{{ $inf($limits['messages']) ? 'ok' : 'partial' }}"><svg class="ic sm"><use href="#i-{{ $inf($limits['messages']) ? 'check' : 'message' }}"/></svg>
        Messages : {{ $inf($limits['messages']) ? 'illimités' : $limits['messages'].' gratuits par contact' }}</li>
      <li class="{{ $inf($limits['photos']) ? 'ok' : 'partial' }}"><svg class="ic sm"><use href="#i-{{ $inf($limits['photos']) ? 'check' : 'eye' }}"/></svg>
        Photos visibles par profil : {{ $inf($limits['photos']) ? 'toutes' : $limits['photos'] }}</li>
      <li class="{{ $limits['visitors'] ? 'ok' : 'no' }}"><svg class="ic sm"><use href="#i-{{ $limits['visitors'] ? 'check' : 'x' }}"/></svg>
        Voir qui a visité mon profil {{ $limits['visitors'] ? '' : '(réservé aux abonnés)' }}</li>
    </ul>
  </div>

  @unless($isPremium)
    <div class="sub-card sub-card-premium">
      <h3><svg class="ic sm"><use href="#i-spark"/></svg>En passant au Premium</h3>
      <ul class="sub-list">
        @foreach(($premiumPlan?->features ?? ['Demandes d\'ami', 'Messages illimités', 'Toutes les photos', 'Voir vos visiteurs', 'Badge Premium']) as $feat)
          <li class="ok"><svg class="ic sm"><use href="#i-check"/></svg>{{ $feat }}</li>
        @endforeach
      </ul>
      @if($premiumPlan)
        <div class="sub-price">
          @if($premiumPlan->compare_at_price)<s>{{ number_format($premiumPlan->compare_at_price, 0, ',', ' ') }}</s>@endif
          <b>{{ number_format($premiumPlan->price, 0, ',', ' ') }} FCFA</b><span>/ mois</span>
        </div>
      @endif
      <a href="{{ route('tarifs') }}" class="btn btn-primary" style="width:100%;justify-content:center">Voir les formules</a>
    </div>
  @endunless
</div>

{{-- Vérification (niveau de confiance, distinct de l'abonnement) --}}
<div class="sub-card" style="margin-top:4px">
  <h3><svg class="ic sm"><use href="#i-verified"/></svg>Niveau de vérification</h3>
  <p style="color:var(--muted);font-size:.9rem;margin-bottom:14px">
    Indépendant de l'abonnement : la vérification renforce la confiance des autres membres.
    Votre niveau actuel : <b style="color:var(--ink)">{{ $verifLevel }}</b>.
  </p>
  <div class="verif-track">
    @foreach(['Bronze' => 'Téléphone', 'Argent' => "Pièce d'identité", 'Or' => 'Selfie'] as $lvl => $how)
      @php $rank = \App\Models\Profile::VERIF_RANK[$verifLevel] ?? 1; $thisRank = \App\Models\Profile::VERIF_RANK[$lvl] ?? 1; @endphp
      <div class="verif-step {{ $thisRank <= $rank ? 'done' : '' }}">
        <span class="verif-dot">@if($thisRank <= $rank)<svg class="ic sm"><use href="#i-check"/></svg>@endif</span>
        <b>{{ $lvl }}</b><small>{{ $how }}</small>
      </div>
    @endforeach
  </div>
  <a href="{{ route('verification') }}" class="btn btn-line" style="margin-top:16px"><svg class="ic sm"><use href="#i-verified"/></svg>Gérer ma vérification</a>
</div>

@endsection
