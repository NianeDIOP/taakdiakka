{{-- Encart « Mon statut » d'abonnement (Premium actif ou incitation gratuit). --}}
@php
  $planUser = auth()->user();
  $planSub  = $planUser?->activeSubscription();
  $planPrem = $planSub && $planSub->isCurrentlyActive();
@endphp
@if($planPrem)
  <div class="plan-card is-prem">
    <span class="plan-ic"><svg class="ic"><use href="#i-spark"/></svg></span>
    <div class="plan-body">
      <span class="label">Mon abonnement</span>
      <b>Membre Premium ✨</b>
      <small>{{ $planSub->plan?->name }}@if($planSub->ends_at) · actif jusqu'au {{ $planSub->ends_at->locale('fr')->isoFormat('D MMMM YYYY') }}@endif</small>
    </div>
    <a href="{{ route('subscription.mine') }}" class="btn btn-line">Gérer</a>
  </div>
@elseif(\App\Support\FeatureGate::monetizationEnabled() && $planUser && ! $planUser->isAdminUser())
  <div class="plan-card is-free">
    <span class="plan-ic"><svg class="ic"><use href="#i-spark"/></svg></span>
    <div class="plan-body">
      <span class="label">Compte gratuit</span>
      <b>Passez Premium pour aller plus loin</b>
      <ul class="plan-perks">
        <li>Envoyer des demandes d'amis</li>
        <li>Discuter librement par message</li>
        <li>Plus de visibilité pour votre profil</li>
      </ul>
    </div>
    <a href="{{ route('tarifs') }}" class="btn btn-primary">Voir les formules ✨</a>
  </div>
@endif
