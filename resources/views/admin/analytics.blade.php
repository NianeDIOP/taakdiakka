@extends('layouts.admin')

@section('title', 'Audience — Administration')
@section('heading', 'Audience & connexions')

@section('content')

@php
  $maxAct = max(1, $activitySeries->max('count'));
  $totalDevices = max(1, $deviceCounts->sum());
  $mobileCount = $deviceCounts['mobile'] ?? 0;
  $desktopCount = $deviceCounts['desktop'] ?? 0;
  $mobilePct = round($mobileCount / $totalDevices * 100);
  $desktopPct = 100 - $mobilePct;
@endphp

<div class="kpi-grid">
  <div class="kpi accent">
    <div class="kpi-label">En ligne maintenant</div>
    <div class="kpi-val">{{ $stats['online'] }}</div>
    <div class="kpi-sub">Actifs dans les 5 dernières minutes</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Actifs aujourd'hui</div>
    <div class="kpi-val">{{ $stats['active_today'] }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Actifs cette semaine</div>
    <div class="kpi-val">{{ $stats['active_week'] }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Actifs ce mois</div>
    <div class="kpi-val">{{ $stats['active_month'] }}</div>
    <div class="kpi-sub">{{ $stats['total'] > 0 ? round($stats['active_month'] / $stats['total'] * 100) : 0 }}% des membres</div>
  </div>
  <div class="kpi {{ $stats['never_seen'] ? 'warn' : '' }}">
    <div class="kpi-label">Jamais connectés</div>
    <div class="kpi-val">{{ $stats['never_seen'] }}</div>
    <div class="kpi-sub">Inscrits sans activité</div>
  </div>
</div>

{{-- Graphique d'activité --}}
<div class="adm-card">
  <h3><svg class="ic"><use href="#i-grid"/></svg>Activité — 14 derniers jours</h3>
  <div style="display:flex;align-items:flex-end;gap:6px;height:140px">
    @foreach($activitySeries as $pt)
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end" title="{{ $pt['date'] }} : {{ $pt['count'] }} membres actifs">
        <div style="width:100%;background:var(--gold);height:{{ max(4, (int) round($pt['count'] / $maxAct * 110)) }}px;opacity:{{ $pt['count'] ? 1 : .25 }}"></div>
        <small style="font-size:.6rem;color:var(--muted)">{{ \Illuminate\Support\Carbon::parse($pt['date'])->format('d/m') }}</small>
      </div>
    @endforeach
  </div>
</div>

<div class="adm-split">
  {{-- Répartition par appareil --}}
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-user"/></svg>Type d'appareil</h3>
    @if($totalDevices > 1)
      <div style="margin-bottom:18px">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:5px">
          <span>📱 Mobile</span>
          <b>{{ $mobileCount }} ({{ $mobilePct }}%)</b>
        </div>
        <div style="height:10px;background:var(--ivory-2);border-radius:5px;overflow:hidden">
          <div style="height:100%;width:{{ $mobilePct }}%;background:var(--gold);border-radius:5px"></div>
        </div>
      </div>
      <div>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:5px">
          <span>💻 Desktop</span>
          <b>{{ $desktopCount }} ({{ $desktopPct }}%)</b>
        </div>
        <div style="height:10px;background:var(--ivory-2);border-radius:5px;overflow:hidden">
          <div style="height:100%;width:{{ $desktopPct }}%;background:var(--ink);border-radius:5px"></div>
        </div>
      </div>
    @else
      <p style="color:var(--muted)">Données insuffisantes. Les statistiques apparaîtront après les premières connexions.</p>
    @endif
  </div>

  {{-- Navigateurs --}}
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-search"/></svg>Navigateurs</h3>
    @php $maxBrowser = max(1, $browserCounts->max() ?: 1); @endphp
    @forelse($browserCounts as $browser => $c)
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:5px">
          <span>{{ $browser }}</span><b>{{ $c }}</b>
        </div>
        <div style="height:8px;background:var(--ivory-2);border-radius:4px;overflow:hidden">
          <div style="height:100%;width:{{ round($c / $maxBrowser * 100) }}%;background:var(--gold);border-radius:4px"></div>
        </div>
      </div>
    @empty
      <p style="color:var(--muted)">Données insuffisantes.</p>
    @endforelse
  </div>
</div>

{{-- Membres en ligne --}}
<div class="adm-card">
  <h3><svg class="ic"><use href="#i-user"/></svg>Membres en ligne ({{ $online->count() }})</h3>
  @if($online->count())
    <div class="adm-card" style="padding:0;overflow-x:auto;box-shadow:none;border:0">
      <table class="adm-table">
        <thead><tr><th>Membre</th><th>Appareil</th><th>Navigateur</th><th>Dernière activité</th></tr></thead>
        <tbody>
          @foreach($online as $u)
            <tr>
              <td>
                <div class="u-cell">
                  <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($u->name, 0, 1) }}"></span>
                  <div><b>{{ $u->name }}</b></div>
                </div>
              </td>
              <td><span class="adm-tag {{ $u->last_device === 'mobile' ? 'warn' : 'ok' }}">{{ $u->last_device === 'mobile' ? '📱 Mobile' : '💻 Desktop' }}</span></td>
              <td>{{ $u->last_browser ?? '—' }}</td>
              <td>{{ $u->last_seen_at->locale('fr')->diffForHumans() }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <p style="color:var(--muted)">Aucun membre en ligne actuellement.</p>
  @endif
</div>

@endsection
