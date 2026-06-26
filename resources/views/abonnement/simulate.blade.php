@extends('layouts.app')

@section('title', 'Paiement — TàakDiàkka')

@section('content')
<div class="wrap" style="max-width:520px;padding:clamp(60px,9vw,100px) 0">
  <div class="sim-card reveal">
    <div class="sim-badge">Mode démonstration</div>
    <h2 style="font-family:var(--font-serif);font-size:1.8rem;font-weight:500;margin-bottom:6px">Paiement sécurisé</h2>
    <p style="color:var(--muted);font-size:.9rem;margin-bottom:22px">Ceci est une simulation de paiement. Aucune somme ne sera débitée. En production, cette étape se déroule sur le tunnel sécurisé du prestataire (PayDunya / Wave / Orange Money).</p>

    <div class="sim-amount">
      <span>{{ $label }}</span>
      <b>{{ number_format($amount, 0, ',', ' ') }} FCFA</b>
    </div>

    <div class="sim-ref">Référence : {{ $ref }}</div>

    <a href="{{ $return }}" class="btn btn-primary" style="width:100%;margin-top:22px;justify-content:center">Confirmer le paiement</a>
    <a href="{{ $cancel }}" class="btn btn-line" style="width:100%;margin-top:10px;justify-content:center">Annuler</a>
  </div>
</div>
@endsection
