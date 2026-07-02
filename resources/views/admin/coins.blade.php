@extends('layouts.admin')

@section('title', 'Pièces & Cadeaux — Admin')

@section('content')

<div class="adm-head">
  <h1>Pièces d'or &amp; Cadeaux</h1>
</div>

@if(session('status'))
  <div class="flash" data-flash="success">{{ session('status') }}</div>
@endif

{{-- KPIs --}}
<div class="adm-kpis" style="margin-bottom:32px">
  <div class="adm-kpi"><div class="adm-kpi-val">{{ number_format($totalPurchased, 0, ',', ' ') }}</div><div class="adm-kpi-lbl">🪙 Pièces achetées</div></div>
  <div class="adm-kpi"><div class="adm-kpi-val">{{ number_format($totalSpent, 0, ',', ' ') }}</div><div class="adm-kpi-lbl">🪙 Pièces dépensées</div></div>
  <div class="adm-kpi"><div class="adm-kpi-val">{{ number_format($totalPurchased - $totalSpent, 0, ',', ' ') }}</div><div class="adm-kpi-lbl">🪙 Solde circulant</div></div>
</div>

{{-- Coût Spotlight --}}
<section style="margin-bottom:40px">
  <h2 class="adm-section-title">Spotlight</h2>
  <form action="{{ route('admin.coins.cost.update') }}" method="POST" style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end">
    @csrf @method('PUT')
    <div>
      <label class="adm-label">Coût (pièces)</label>
      <input type="number" name="spotlight_cost" value="{{ $spotlightCost }}" min="1" class="adm-input" style="width:120px" />
    </div>
    <div>
      <label class="adm-label">Durée (heures)</label>
      <input type="number" name="spotlight_hours" value="{{ $spotlightHours }}" min="1" max="168" class="adm-input" style="width:120px" />
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
  </form>
</section>

{{-- Packs de pièces --}}
<section style="margin-bottom:40px">
  <h2 class="adm-section-title">Packs de pièces</h2>
  <table class="adm-table">
    <thead><tr><th>Nom</th><th>Pièces</th><th>Bonus</th><th>Prix (FCFA)</th><th>Populaire</th><th>Ordre</th><th>Actif</th><th></th></tr></thead>
    <tbody>
      @foreach($packs as $pack)
      <tr>
        <form action="{{ route('admin.coins.pack.update', $pack) }}" method="POST" style="display:contents">@csrf @method('PUT')
          <td><input type="text" name="name" value="{{ $pack->name }}" class="adm-input" style="width:120px" /></td>
          <td><input type="number" name="coins" value="{{ $pack->coins }}" min="1" class="adm-input" style="width:80px" /></td>
          <td><input type="number" name="bonus_coins" value="{{ $pack->bonus_coins }}" min="0" class="adm-input" style="width:80px" /></td>
          <td><input type="number" name="price" value="{{ $pack->price }}" min="1" class="adm-input" style="width:100px" /></td>
          <td style="text-align:center"><input type="checkbox" name="is_popular" value="1" {{ $pack->is_popular ? 'checked' : '' }} /></td>
          <td><input type="number" name="sort_order" value="{{ $pack->sort_order }}" min="0" class="adm-input" style="width:60px" /></td>
          <td style="text-align:center"><input type="checkbox" name="is_active" value="1" {{ $pack->is_active ? 'checked' : '' }} /></td>
          <td><button type="submit" class="btn btn-line" style="padding:4px 12px;font-size:.78rem">Sauver</button></td>
        </form>
      </tr>
      @endforeach
    </tbody>
  </table>
</section>

{{-- Catalogue cadeaux --}}
<section style="margin-bottom:40px">
  <h2 class="adm-section-title">Catalogue cadeaux</h2>
  <table class="adm-table">
    <thead><tr><th>Emoji</th><th>Nom</th><th>Catégorie</th><th>Coût (🪙)</th><th>Ordre</th><th>Actif</th><th></th></tr></thead>
    <tbody>
      @foreach($gifts as $gift)
      <tr>
        <form action="{{ route('admin.coins.gift.update', $gift) }}" method="POST" style="display:contents">@csrf @method('PUT')
          <td><input type="text" name="emoji" value="{{ $gift->emoji }}" class="adm-input" style="width:60px;text-align:center" /></td>
          <td><input type="text" name="name" value="{{ $gift->name }}" class="adm-input" style="width:120px" /></td>
          <td>
            <select name="category" class="adm-input">
              @foreach(['classique','spirituel','premium','exclusif'] as $cat)
                <option value="{{ $cat }}" {{ $gift->category === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
              @endforeach
            </select>
          </td>
          <td><input type="number" name="coins_cost" value="{{ $gift->coins_cost }}" min="1" class="adm-input" style="width:80px" /></td>
          <td><input type="number" name="sort_order" value="{{ $gift->sort_order }}" min="0" class="adm-input" style="width:60px" /></td>
          <td style="text-align:center"><input type="checkbox" name="is_active" value="1" {{ $gift->is_active ? 'checked' : '' }} /></td>
          <td><button type="submit" class="btn btn-line" style="padding:4px 12px;font-size:.78rem">Sauver</button></td>
        </form>
      </tr>
      @endforeach
    </tbody>
  </table>
</section>

{{-- Dernières transactions --}}
<section>
  <h2 class="adm-section-title">Dernières transactions</h2>
  <table class="adm-table">
    <thead><tr><th>Membre</th><th>Type</th><th>Pièces</th><th>Solde après</th><th>Description</th><th>Date</th></tr></thead>
    <tbody>
      @foreach($recentTx as $tx)
      <tr>
        <td>{{ $tx->user?->name ?? '—' }}</td>
        <td><code>{{ $tx->type }}</code></td>
        <td style="font-weight:700;color:{{ $tx->coins > 0 ? '#2a7a3b' : 'var(--heart)' }}">{{ $tx->coins > 0 ? '+' : '' }}{{ $tx->coins }}</td>
        <td>{{ $tx->balance_after }}</td>
        <td style="font-size:.8rem;color:var(--muted)">{{ $tx->description }}</td>
        <td style="font-size:.78rem;color:var(--muted)">{{ $tx->created_at->format('d/m H:i') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</section>

@endsection
