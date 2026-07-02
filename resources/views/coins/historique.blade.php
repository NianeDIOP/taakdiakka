@extends('layouts.member')

@section('title', 'Historique pièces — TàakDiàkka')

@section('content')

<div class="m-head">
  <span class="label">Pièces d'or</span>
  <h2 style="font-family:var(--font-serif);font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:500">Historique</h2>
</div>

<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px;padding:16px;background:var(--paper);border:1px solid var(--line)">
  <span style="font-size:2rem">🪙</span>
  <div>
    <span style="font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Solde actuel</span>
    <div style="font-family:var(--font-serif);font-size:1.8rem;font-weight:500;color:var(--gold)">{{ number_format($balance, 0, ',', ' ') }}</div>
  </div>
  <a href="{{ route('coins.shop') }}" class="btn btn-primary" style="margin-left:auto">Recharger</a>
</div>

@if($transactions->isEmpty())
  <p style="color:var(--muted);text-align:center;padding:40px 0">Aucune transaction pour le moment.</p>
@else
<div style="border:1px solid var(--line)">
  @foreach($transactions as $tx)
    @php
      $isCredit = $tx->coins > 0;
      $typeLabels = [
        'purchase'        => 'Achat de pièces',
        'purchase_pending'=> 'Achat en attente',
        'spend_gift'      => 'Cadeau envoyé',
        'spend_spotlight' => 'Spotlight activé',
        'bonus'           => 'Bonus offert',
        'refund'          => 'Remboursement',
      ];
    @endphp
    <div style="display:grid;grid-template-columns:1fr auto auto;gap:12px 20px;align-items:center;padding:14px 18px;border-bottom:1px solid var(--line)">
      <div>
        <div style="font-weight:600;font-size:.9rem">{{ $typeLabels[$tx->type] ?? $tx->type }}</div>
        <div style="font-size:.76rem;color:var(--muted);margin-top:2px">{{ $tx->description }}</div>
        <div style="font-size:.7rem;color:var(--muted);margin-top:1px">{{ $tx->created_at->format('d/m/Y H:i') }}</div>
      </div>
      <div style="font-size:1rem;font-weight:700;color:{{ $isCredit ? '#2a7a3b' : 'var(--heart)' }};white-space:nowrap;text-align:right">
        {{ $isCredit ? '+' : '' }}{{ $tx->coins }} 🪙
      </div>
      <div style="font-size:.78rem;color:var(--muted);white-space:nowrap;text-align:right">
        = {{ $tx->balance_after }} 🪙
      </div>
    </div>
  @endforeach
</div>

<div style="margin-top:20px">{{ $transactions->links() }}</div>
@endif

@endsection
