@extends('layouts.member')

@section('title', 'Abonnement activé — TàakDiàkka')

@section('content')
<div class="mwrap" style="max-width:560px">
  <div class="sim-card reveal" style="text-align:center">
    <div class="success-check"><svg class="ic"><use href="#i-check"/></svg></div>
    <h2 style="font-family:var(--font-serif);font-size:2rem;font-weight:500;margin:14px 0 6px">Bienvenue dans {{ $subscription->plan->name }} ✨</h2>
    <p style="color:var(--muted);margin-bottom:20px">Votre abonnement est actif. Vous profitez désormais de toutes les fonctionnalités premium.</p>

    <div class="sim-amount">
      <span>{{ $subscription->plan->name }}</span>
      <b>{{ number_format($subscription->amount, 0, ',', ' ') }} FCFA</b>
    </div>
    @if($subscription->ends_at)
      <div class="sim-ref">Valable jusqu'au {{ $subscription->ends_at->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
    @endif

    <a href="{{ route('dashboard') }}" class="btn btn-primary" style="width:100%;margin-top:22px;justify-content:center">Découvrir mes nouveaux avantages</a>
    <a href="{{ route('settings') }}" class="btn btn-line" style="width:100%;margin-top:10px;justify-content:center">Gérer mon abonnement</a>
  </div>
</div>
@endsection
