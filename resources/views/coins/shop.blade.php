@extends('layouts.member')

@section('title', 'Pièces d\'or — TàakDiàkka')

@section('content')

<div class="m-head">
  <span class="label">Boutique</span>
  <h2>Pièces <em style="color:var(--gold)">d'or</em> 🪙</h2>
  <p>Envoyez des cadeaux, boostez votre profil, démarquez-vous.</p>
</div>

<div style="display:flex;align-items:center;gap:14px;margin-bottom:30px;padding:18px;background:var(--paper);border:1px solid var(--line)">
  <span style="font-size:2.2rem">🪙</span>
  <div>
    <span style="font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Mon solde</span>
    <div style="font-family:var(--font-serif);font-size:2rem;font-weight:500;color:var(--gold)">{{ number_format($balance, 0, ',', ' ') }}</div>
  </div>
  <span style="margin-left:auto;font-size:.8rem;color:var(--muted)">pièces d'or</span>
</div>

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin-bottom:16px">Recharger mon compte</h3>

<div class="coin-grid">
  @foreach($packs as $pack)
    <div class="coin-pack {{ $pack->is_popular ? 'popular' : '' }}">
      <div class="cp-coins">{{ $pack->coins }}</div>
      @if($pack->bonus_coins)
        <div class="cp-bonus">+{{ $pack->bonus_coins }} bonus</div>
      @endif
      <div class="cp-price">{{ number_format($pack->price, 0, ',', ' ') }} FCFA</div>
      <div class="cp-unit">{{ number_format($pack->unit_price, 0, ',', ' ') }} FCFA / pièce</div>
      <form action="{{ route('coins.checkout', $pack) }}" method="POST" style="margin-top:14px">@csrf
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Acheter</button>
      </form>
    </div>
  @endforeach
</div>

<div style="margin-top:40px">
  <h3 style="font-family:var(--font-serif);font-size:1.4rem;margin-bottom:8px">Que faire avec vos pièces ?</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-top:16px">
    <div style="padding:20px;border:1px solid var(--line)">
      <div style="font-size:1.6rem;margin-bottom:8px">🎁</div>
      <b>Cadeaux virtuels</b>
      <p style="color:var(--muted);font-size:.84rem;margin-top:4px">Envoyez une rose, un bouquet ou un diamant à un profil qui vous plaît.</p>
    </div>
    <div style="padding:20px;border:1px solid var(--line)">
      <div style="font-size:1.6rem;margin-bottom:8px">⚡</div>
      <b>Super Intérêt</b>
      <p style="color:var(--muted);font-size:.84rem;margin-top:4px">Marquez votre intérêt de façon visible — la notification est mise en avant.</p>
    </div>
    <div style="padding:20px;border:1px solid var(--line)">
      <div style="font-size:1.6rem;margin-bottom:8px">🔥</div>
      <b>Spotlight 24h</b>
      <p style="color:var(--muted);font-size:.84rem;margin-top:4px">Soyez le premier profil affiché dans Découvrir pendant 24 heures.</p>
    </div>
  </div>
</div>

@endsection
