@extends('layouts.admin')

@section('title', 'Abonnements — Administration')
@section('heading', 'Abonnements & boosts')

@section('content')

<div class="kpi-grid">
  <div class="kpi accent">
    <div class="kpi-label">Abonnés actifs</div>
    <div class="kpi-val">{{ $stats['active'] }}</div>
  </div>
  <div class="kpi gold">
    <div class="kpi-label">Revenu abonnements</div>
    <div class="kpi-val">{{ number_format($stats['revenue'], 0, ',', ' ') }}</div>
    <div class="kpi-sub">FCFA cumulé</div>
  </div>
  <div class="kpi {{ $stats['pending'] ? 'warn' : '' }}">
    <div class="kpi-label">En attente</div>
    <div class="kpi-val">{{ $stats['pending'] }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Boosts</div>
    <div class="kpi-val">{{ $stats['boosts_active'] }}</div>
    <div class="kpi-sub">{{ $stats['boosts_total'] }} total · {{ number_format($stats['boosts_rev'], 0, ',', ' ') }} FCFA</div>
  </div>
</div>

<div class="tabs" style="margin-bottom:18px">
  <a href="{{ route('admin.billing.subscriptions', ['tab' => 'subscriptions']) }}" class="tab {{ $tab === 'subscriptions' ? 'active' : '' }}">Abonnements</a>
  <a href="{{ route('admin.billing.subscriptions', ['tab' => 'boosts']) }}" class="tab {{ $tab === 'boosts' ? 'active' : '' }}">Boosts achetés</a>
</div>

@if($tab === 'boosts' && $boosts)
  <div class="adm-card" style="padding:0;overflow-x:auto">
    <table class="adm-table">
      <thead><tr><th>Membre</th><th>Pack</th><th>Montant</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead>
      <tbody>
        @forelse($boosts as $b)
          @php $active = $b->ends_at && $b->ends_at->isFuture(); @endphp
          <tr>
            <td>
              <div class="u-cell">
                <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($b->user?->name ?? '?', 0, 1) }}"></span>
                <div><b>{{ $b->user?->name ?? 'Supprimé' }}</b><small>{{ $b->user?->email }}</small></div>
              </div>
            </td>
            <td>{{ $b->pack?->name ?? '—' }}</td>
            <td>{{ number_format($b->amount, 0, ',', ' ') }} FCFA</td>
            <td>{{ $b->starts_at?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $b->ends_at?->format('d/m/Y') ?? '—' }}</td>
            <td><span class="adm-tag {{ $active ? 'ok' : '' }}">{{ $active ? 'Actif' : ($b->starts_at ? 'Terminé' : 'Non payé') }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">Aucun boost acheté.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($boosts->hasPages())
    <nav class="pager">
      @if($boosts->onFirstPage())<span class="pager-btn disabled">Précédent</span>
      @else<a class="pager-btn" href="{{ $boosts->previousPageUrl() }}">Précédent</a>@endif
      <span class="pager-info">Page {{ $boosts->currentPage() }} / {{ $boosts->lastPage() }}</span>
      @if($boosts->hasMorePages())<a class="pager-btn" href="{{ $boosts->nextPageUrl() }}">Suivant</a>
      @else<span class="pager-btn disabled">Suivant</span>@endif
    </nav>
  @endif

@else

  <div class="tabs">
    <a href="{{ route('admin.billing.subscriptions') }}" class="tab {{ !$status ? 'active' : '' }}">Tous</a>
    <a href="{{ route('admin.billing.subscriptions', ['status' => 'active']) }}" class="tab {{ $status === 'active' ? 'active' : '' }}">Actifs</a>
    <a href="{{ route('admin.billing.subscriptions', ['status' => 'expired']) }}" class="tab {{ $status === 'expired' ? 'active' : '' }}">Expirés</a>
    <a href="{{ route('admin.billing.subscriptions', ['status' => 'cancelled']) }}" class="tab {{ $status === 'cancelled' ? 'active' : '' }}">Annulés</a>
  </div>

  <div class="adm-card" style="padding:0;overflow-x:auto">
    <table class="adm-table">
      <thead><tr><th>Membre</th><th>Formule</th><th>Montant</th><th>Durée</th><th>Statut</th><th>Échéance</th><th>Souscrit</th><th></th></tr></thead>
      <tbody>
        @forelse($subscriptions as $s)
          @php $tag = ['active' => 'ok', 'pending' => 'warn', 'expired' => '', 'cancelled' => 'bad'][$s->status] ?? ''; @endphp
          <tr>
            <td>
              <div class="u-cell">
                <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($s->user?->name ?? '?', 0, 1) }}"></span>
                <div><b>{{ $s->user?->name ?? 'Supprimé' }}</b><small>{{ $s->user?->email }}</small></div>
              </div>
            </td>
            <td>{{ $s->plan?->name ?? '—' }}</td>
            <td>{{ number_format($s->amount, 0, ',', ' ') }} FCFA</td>
            <td>{{ $s->months ?? 1 }} mois</td>
            <td><span class="adm-tag {{ $tag }}">{{ $s->status_label }}</span></td>
            <td>{{ $s->ends_at?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $s->created_at->format('d/m/Y') }}</td>
            <td>
              @if($s->status === 'active')
                <form method="POST" action="{{ route('admin.billing.subscription.cancel', $s) }}" onsubmit="return confirm('Annuler cet abonnement ?');">
                  @csrf @method('PUT')
                  <button class="adm-btn danger" type="submit">Annuler</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:30px">Aucun abonnement.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($subscriptions->hasPages())
    <nav class="pager">
      @if($subscriptions->onFirstPage())<span class="pager-btn disabled">Précédent</span>
      @else<a class="pager-btn" href="{{ $subscriptions->previousPageUrl() }}">Précédent</a>@endif
      <span class="pager-info">Page {{ $subscriptions->currentPage() }} / {{ $subscriptions->lastPage() }}</span>
      @if($subscriptions->hasMorePages())<a class="pager-btn" href="{{ $subscriptions->nextPageUrl() }}">Suivant</a>
      @else<span class="pager-btn disabled">Suivant</span>@endif
    </nav>
  @endif

@endif

@endsection
