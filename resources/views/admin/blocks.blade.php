@extends('layouts.admin')

@section('title', 'Blocages — Administration')
@section('heading', 'Blocages entre membres')

@section('content')

<div class="kpi-grid" style="margin-bottom:24px">
  <div class="kpi">
    <div class="kpi-label">Blocages actifs</div>
    <div class="kpi-val">{{ $total }}</div>
    <div class="kpi-sub">Relations bloquées entre membres</div>
  </div>
</div>

<form method="GET" class="adm-filters" style="margin-bottom:18px">
  <div class="fld" style="flex:1;min-width:220px">
    <label>Recherche</label>
    <input type="text" name="q" value="{{ $q }}" placeholder="Nom du membre…" />
  </div>
  <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-search"/></svg>Filtrer</button>
</form>

<div class="adm-card" style="padding:0;overflow-x:auto">
  <table class="adm-table">
    <thead><tr><th>Bloqueur</th><th></th><th>Bloqué</th><th>Date</th><th></th></tr></thead>
    <tbody>
      @forelse($blocks as $b)
        <tr>
          <td>
            <div class="u-cell">
              <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($b->blocker_name, 0, 1) }}"></span>
              <div><b>{{ $b->blocker_name }}</b><small>{{ $b->blocker_email }}</small></div>
            </div>
          </td>
          <td style="text-align:center;color:var(--heart);font-size:1.1rem">→</td>
          <td>
            <div class="u-cell">
              <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($b->blocked_name, 0, 1) }}"></span>
              <div><b>{{ $b->blocked_name }}</b><small>{{ $b->blocked_email }}</small></div>
            </div>
          </td>
          <td>{{ \Illuminate\Support\Carbon::parse($b->created_at)->format('d/m/Y H:i') }}</td>
          <td>
            <form method="POST" action="{{ route('admin.blocks.remove', [$b->blocker_id, $b->blocked_id]) }}" onsubmit="return confirm('Lever ce blocage ?');">
              @csrf @method('DELETE')
              <button class="adm-btn danger" type="submit" title="Lever le blocage"><svg class="ic sm"><use href="#i-x"/></svg></button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">Aucun blocage enregistré.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($blocks->hasPages())
  <nav class="pager">
    @if($blocks->onFirstPage())<span class="pager-btn disabled">Précédent</span>
    @else<a class="pager-btn" href="{{ $blocks->previousPageUrl() }}">Précédent</a>@endif
    <span class="pager-info">Page {{ $blocks->currentPage() }} / {{ $blocks->lastPage() }}</span>
    @if($blocks->hasMorePages())<a class="pager-btn" href="{{ $blocks->nextPageUrl() }}">Suivant</a>
    @else<span class="pager-btn disabled">Suivant</span>@endif
  </nav>
@endif

@endsection
