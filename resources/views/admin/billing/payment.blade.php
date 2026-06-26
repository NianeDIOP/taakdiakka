@extends('layouts.admin')

@section('title', 'Paiement — Administration')
@section('heading', 'Paramètres de paiement')

@section('content')

@php $isDemo = $active->key() === 'stub'; @endphp

<div class="pay-status {{ $isDemo ? 'demo' : 'live' }}">
  <span class="pay-status-ic"><svg class="ic"><use href="#i-{{ $isDemo ? 'spark' : 'verified' }}"/></svg></span>
  <div>
    <div class="pay-status-title">{{ $active->name() }}@unless($isDemo) · mode {{ $settings['mode'] }}@endunless</div>
    <p>
      @if($isDemo)
        Aucun encaissement réel n'a lieu : les paiements sont simulés. Renseignez vos clés PayDunya puis choisissez « PayDunya » comme prestataire pour passer en production.
      @else
        PayDunya est actif. Les paiements sont traités réellement.
      @endif
    </p>
  </div>
</div>

<form method="POST" action="{{ route('admin.billing.payment.save') }}">
  @csrf

  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-spark"/></svg>Prestataire</h3>
    <div class="adm-form-grid">
      <label>Prestataire de paiement
        <select name="provider">
          <option value="stub" @selected($settings['provider'] === 'stub')>Démonstration (aucun encaissement)</option>
          <option value="paydunya" @selected($settings['provider'] === 'paydunya')>PayDunya</option>
        </select>
      </label>
      <label>Mode PayDunya
        <select name="mode">
          <option value="test" @selected($settings['mode'] === 'test')>Test (sandbox)</option>
          <option value="live" @selected($settings['mode'] === 'live')>Production (live)</option>
        </select>
      </label>
    </div>
    @if($settings['provider'] === 'paydunya' && !$configured)
      <p class="adm-warn">⚠ PayDunya est sélectionné mais les clés sont incomplètes : le système utilisera la démonstration jusqu'à ce que les 3 clés soient renseignées.</p>
    @endif
  </div>

  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-verified"/></svg>Clés API PayDunya
      <span class="adm-tag {{ $configured ? 'ok' : 'warn' }}" style="margin-left:auto">{{ $configured ? '3/3 clés' : 'incomplètes' }}</span>
    </h3>
    <p style="color:var(--muted);font-size:.86rem;margin-bottom:16px">Disponibles dans votre tableau de bord PayDunya → Intégrations → API. Stockées de façon sécurisée, sans aucun redéploiement.</p>
    <div class="adm-form-grid one">
      <label>Master Key<input type="text" name="master_key" value="{{ $settings['master_key'] }}" autocomplete="off" placeholder="xxxxxxxx-xxxx-xxxx" /></label>
      <label>Private Key<input type="text" name="private_key" value="{{ $settings['private_key'] }}" autocomplete="off" placeholder="live_private_… / test_private_…" /></label>
      <label>Token<input type="text" name="token" value="{{ $settings['token'] }}" autocomplete="off" placeholder="xxxxxxxxxxxx" /></label>
    </div>
  </div>

  <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-check"/></svg>Enregistrer les paramètres</button>
</form>

@endsection
