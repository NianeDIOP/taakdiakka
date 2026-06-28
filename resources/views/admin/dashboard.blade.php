@extends('layouts.admin')

@section('title', 'Tableau de bord — Administration')
@section('heading', 'Tableau de bord')

@section('content')

@php
  $maxSignup = max(1, $signupSeries->max('count'));
  $maxRev = max(1, $revenueSeries->max('amount'));
  $verifColors = ['Or' => 'gold', 'Argent' => 'ok', 'Bronze' => 'warn'];
  $fcfa = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA';
  $hour = (int) now()->format('H');
  $greet = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
  $firstName = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
@endphp

<div class="adm-hero">
  <div>
    <span class="adm-hero-date">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
    <h2>{{ $greet }}, {{ $firstName }} 👋</h2>
    <p>Voici l'activité de TàakDiàkka.
      @if($kpis['online_now']) <b style="color:#fff">{{ $kpis['online_now'] }}</b> membre{{ $kpis['online_now'] > 1 ? 's' : '' }} en ligne ·@endif
      <b style="color:#fff">{{ $kpis['members_today'] }}</b> inscription{{ $kpis['members_today'] > 1 ? 's' : '' }} aujourd'hui.
    </p>
  </div>
  <div class="adm-hero-quick">
    @if($kpis['reports_pending'])
      <a href="{{ route('admin.moderation') }}" class="adm-hero-btn alert"><svg class="ic"><use href="#i-flag"/></svg>À modérer <span class="pill">{{ $kpis['reports_pending'] }}</span></a>
    @endif
    <a href="{{ route('admin.users.index') }}" class="adm-hero-btn"><svg class="ic"><use href="#i-user"/></svg>Utilisateurs</a>
    @if($kpis['subs_pending'])
      <a href="{{ route('admin.billing.subscriptions') }}" class="adm-hero-btn"><svg class="ic"><use href="#i-spark"/></svg>Paiements <span class="pill">{{ $kpis['subs_pending'] }}</span></a>
    @endif
  </div>
</div>

<div class="kpi-grid">
  <div class="kpi gold">
    <div class="kpi-label">Revenus ce mois</div>
    <div class="kpi-val">{{ $fcfa($kpis['revenue_month']) }}</div>
    <div class="kpi-sub">{{ $fcfa($kpis['revenue_total']) }} au total</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Abonnés payants</div>
    <div class="kpi-val">{{ $kpis['subs_active'] }}</div>
    <div class="kpi-sub">{{ $kpis['subs_pending'] }} paiement(s) en attente · <a href="{{ route('admin.billing.subscriptions') }}" class="lnk">Gérer</a></div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Membres</div>
    <div class="kpi-val">{{ number_format($kpis['members_total'], 0, ',', ' ') }}</div>
    <div class="kpi-sub">+{{ $kpis['members_today'] }} aujourd'hui · +{{ $kpis['members_week'] }} cette semaine</div>
  </div>
  <div class="kpi accent">
    <div class="kpi-label">En ligne maintenant</div>
    <div class="kpi-val">{{ $kpis['online_now'] }}</div>
    <div class="kpi-sub">Actifs dans les 5 dernières minutes</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Conversion</div>
    <div class="kpi-val">{{ $kpis['conversion'] }}%</div>
    <div class="kpi-sub">Gratuit → Premium</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Demandes actives</div>
    <div class="kpi-val">{{ $kpis['demandes_active'] }}</div>
    <div class="kpi-sub">Profils en recherche de mariage</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Communauté</div>
    <div class="kpi-val">{{ $kpis['posts_total'] }}</div>
    <div class="kpi-sub">+{{ $kpis['posts_today'] }} aujourd'hui · {{ $kpis['comments_total'] }} commentaires · <a href="{{ route('admin.community') }}" class="lnk">Gérer</a></div>
  </div>
  <div class="kpi {{ $kpis['reports_pending'] ? 'warn' : '' }}">
    <div class="kpi-label">Signalements</div>
    <div class="kpi-val">{{ $kpis['reports_pending'] }}</div>
    <div class="kpi-sub"><a href="{{ route('admin.moderation') }}" class="lnk">À traiter</a></div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Boosts actifs</div>
    <div class="kpi-val">{{ $kpis['boosts_active'] }}</div>
    <div class="kpi-sub">Profils mis en avant</div>
  </div>
  <div class="kpi {{ $kpis['suspended'] ? 'warn' : '' }}">
    <div class="kpi-label">Comptes bloqués</div>
    <div class="kpi-val">{{ $kpis['suspended'] }}</div>
    <div class="kpi-sub">Suspendus ou bannis · {{ $kpis['blocks_total'] }} blocages · <a href="{{ route('admin.blocks') }}" class="lnk">Voir</a></div>
  </div>
</div>

<div class="adm-card">
  <h3><svg class="ic"><use href="#i-grid"/></svg>Inscriptions — 14 derniers jours</h3>
  <div style="display:flex;align-items:flex-end;gap:6px;height:140px">
    @foreach($signupSeries as $pt)
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end" title="{{ $pt['date'] }} : {{ $pt['count'] }}">
        <div style="width:100%;background:var(--gold);height:{{ max(4, (int) round($pt['count'] / $maxSignup * 110)) }}px;opacity:{{ $pt['count'] ? 1 : .25 }}"></div>
        <small style="font-size:.6rem;color:var(--muted)">{{ \Illuminate\Support\Carbon::parse($pt['date'])->format('d/m') }}</small>
      </div>
    @endforeach
  </div>
</div>

<div class="adm-card">
  <h3><svg class="ic"><use href="#i-spark"/></svg>Revenus encaissés — 30 derniers jours</h3>
  <div style="display:flex;align-items:flex-end;gap:3px;height:140px">
    @foreach($revenueSeries as $pt)
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end" title="{{ \Illuminate\Support\Carbon::parse($pt['date'])->format('d/m') }} : {{ number_format($pt['amount'], 0, ',', ' ') }} FCFA">
        <div style="width:100%;background:var(--ink);height:{{ max(3, (int) round($pt['amount'] / $maxRev * 110)) }}px;opacity:{{ $pt['amount'] ? 1 : .18 }}"></div>
      </div>
    @endforeach
  </div>
  <div style="display:flex;justify-content:space-between;margin-top:8px;font-size:.7rem;color:var(--muted)">
    <span>{{ \Illuminate\Support\Carbon::parse($revenueSeries->first()['date'])->format('d/m') }}</span>
    <span>Total période : {{ $fcfa($revenueSeries->sum('amount')) }}</span>
    <span>{{ \Illuminate\Support\Carbon::parse($revenueSeries->last()['date'])->format('d/m') }}</span>
  </div>
</div>

<div class="adm-split">
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-user"/></svg>Répartition par genre</h3>
    @php $totalG = max(1, $byGender->sum()); @endphp
    @forelse($byGender as $g => $c)
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:5px">
          <span>{{ $g === 'F' ? 'Femmes' : ($g === 'M' ? 'Hommes' : $g) }}</span>
          <b>{{ $c }} ({{ round($c / $totalG * 100) }}%)</b>
        </div>
        <div style="height:8px;background:var(--ivory-2)"><div style="height:100%;width:{{ round($c / $totalG * 100) }}%;background:{{ $g === 'F' ? 'var(--heart)' : 'var(--gold)' }}"></div></div>
      </div>
    @empty
      <p style="color:var(--muted)">Aucune donnée.</p>
    @endforelse

    <h3 style="margin-top:24px"><svg class="ic"><use href="#i-verified"/></svg>Vérification</h3>
    <div class="adm-actions-cell">
      @forelse($byVerif as $level => $c)
        <span class="adm-tag {{ $verifColors[$level] ?? '' }}">{{ $level }} · {{ $c }}</span>
      @empty
        <span style="color:var(--muted)">Aucune donnée.</span>
      @endforelse
    </div>
  </div>

  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-pin"/></svg>Top régions</h3>
    @php $maxR = max(1, $byRegion->max() ?: 1); @endphp
    @forelse($byRegion as $region => $c)
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:5px">
          <span>{{ $region }}</span><b>{{ $c }}</b>
        </div>
        <div style="height:8px;background:var(--ivory-2)"><div style="height:100%;width:{{ round($c / $maxR * 100) }}%;background:var(--gold)"></div></div>
      </div>
    @empty
      <p style="color:var(--muted)">Aucune donnée.</p>
    @endforelse
  </div>
</div>

<div class="adm-card">
  <h3><svg class="ic"><use href="#i-spark"/></svg>Derniers inscrits</h3>
  <table class="adm-table">
    <thead><tr><th>Membre</th><th>Région</th><th>Inscrit</th><th></th></tr></thead>
    <tbody>
      @foreach($recentMembers as $m)
        @php $mav = $m->profile && $m->profile->photo ? pathinfo($m->profile->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($m->name); @endphp
        <tr>
          <td><div class="u-cell">
            @if($mav)
              <span class="av s photo" style="background-image:url('{{ asset('img/'.$mav.'.webp') }}')"></span>
            @else
              <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($m->name, 0, 1) }}"></span>
            @endif
            <div><b>{{ $m->name }}</b><small>{{ $m->email }}</small></div>
          </div></td>
          <td>{{ $m->profile?->region ?? '—' }}</td>
          <td>{{ $m->created_at->locale('fr')->diffForHumans() }}</td>
          <td><a href="{{ route('admin.users.show', $m) }}" class="adm-btn">Voir</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

@endsection
