@extends('layouts.admin')

@section('title', $user->name.' — Administration')
@section('heading', 'Fiche membre')

@section('content')

@php
  $p = $user->profile;
  $statusTag = ['active' => 'ok', 'suspended' => 'warn', 'banned' => 'bad'][$user->status] ?? '';
  $demande = $user->demandes->first();
  $sub = $user->activeSubscription();
  $totalReports = $reportsAgainst + ($reportsAsUser ?? 0);
@endphp

<a href="{{ route('admin.users.index') }}" class="adm-back-link"><svg class="ic sm"><use href="#i-arrow"/></svg>Retour à la liste</a>

<div class="adm-uhero">
  <div class="adm-uhero-id">
    @if($p && $p->photo)
      <span class="av photo adm-uhero-av" style="background-image:url('{{ asset('img/'.pathinfo($p->photo, PATHINFO_FILENAME).'.webp') }}')"></span>
    @else
      <span class="av adm-uhero-av" data-av="{{ \Illuminate\Support\Str::substr($user->name, 0, 1) }}"></span>
    @endif
    <div>
      <h2>{{ $user->name }}</h2>
      <div class="meta">
        <span>{{ $user->email }}</span>
        <span class="adm-tag {{ $statusTag }}">{{ \App\Models\User::STATUS_LABELS[$user->status] ?? 'Actif' }}</span>
        <span class="adm-tag gold">{{ $p->verification_level ?? 'Bronze' }}</span>
        @if($user->is_online)<span class="adm-tag ok">● En ligne</span>@endif
        @if($sub)<span class="adm-tag gold">{{ $sub->plan?->name }}</span>@endif
      </div>
      @if($user->status_reason)
        <div class="meta" style="color:var(--heart)">Motif : {{ $user->status_reason }}@if($user->suspended_until) · jusqu'au {{ $user->suspended_until->format('d/m/Y') }}@endif</div>
      @endif
    </div>
  </div>
  <div class="adm-uhero-stats">
    <div><span class="n">{{ $user->created_at->format('d/m/y') }}</span><span class="l">Inscrit</span></div>
    <div><span class="n">{{ $user->last_seen_at ? $user->last_seen_at->locale('fr')->diffForHumans(null, true) : '—' }}</span><span class="l">Activité</span></div>
    <div><span class="n" style="{{ $totalReports ? 'color:var(--heart)' : '' }}">{{ $totalReports }}</span><span class="l">Signalements</span></div>
    <div><span class="n">{{ $blocksBy->count() + $blockedBy->count() }}</span><span class="l">Blocages</span></div>
    <div><span class="n">{{ $subscriptions->count() }}</span><span class="l">Abonnements</span></div>
  </div>
</div>

<div class="adm-split">
  {{-- Informations profil --}}
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-user"/></svg>Profil</h3>
    @if($p)
      <dl class="adm-dl">
        <dt>Genre</dt><dd>{{ $p->gender ?? '—' }}</dd>
        <dt>Âge</dt><dd>{{ $p->birthdate ? \Illuminate\Support\Carbon::parse($p->birthdate)->age.' ans' : '—' }}</dd>
        <dt>Région</dt><dd>{{ $p->region ?? '—' }}</dd>
        <dt>Religion</dt><dd>{{ $p->religion ?? '—' }}</dd>
        <dt>Pratique</dt><dd>{{ $p->practice ?? '—' }}</dd>
        <dt>Profession</dt><dd>{{ $p->profession ?? '—' }}</dd>
        <dt>Téléphone</dt><dd>{{ $p->phone ?? '—' }}</dd>
        <dt>Demande</dt><dd>{{ $demande ? (\App\Models\Demande::STATUS_LABELS[$demande->status] ?? $demande->status) : 'Aucune' }}</dd>
      </dl>
      @if($p->bio)<p style="margin-top:14px;color:var(--muted);font-size:.88rem;font-style:italic">« {{ \Illuminate\Support\Str::limit($p->bio, 240) }} »</p>@endif
    @else
      <p style="color:var(--muted)">Profil non renseigné.</p>
    @endif

    @if($user->photos->count())
      <h3 style="margin-top:22px"><svg class="ic"><use href="#i-eye"/></svg>Galerie ({{ $user->photos->count() }})</h3>
      <div class="adm-gallery">
        @foreach($user->photos as $ph)
          <img src="{{ asset('img/'.$ph->base.'.webp') }}" alt="" loading="lazy" />
        @endforeach
      </div>
    @endif
  </div>

  {{-- Actions de modération --}}
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-verified"/></svg>Actions de modération</h3>

    {{-- Statut --}}
    @if($user->status === 'active')
      <form method="POST" action="{{ route('admin.users.suspend', $user) }}" style="border-top:1px solid var(--line);padding-top:16px;margin-top:8px">
        @csrf
        <label style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);font-weight:600">Suspendre</label>
        <input type="text" name="reason" placeholder="Motif (optionnel)" style="width:100%;border:1px solid var(--line);background:var(--ivory);padding:9px 12px;margin:8px 0;font-family:var(--font-sans);font-size:.85rem" />
        <div style="display:flex;gap:8px;align-items:center">
          <input type="number" name="days" min="1" placeholder="Jours (vide = indéfini)" style="flex:1;border:1px solid var(--line);background:var(--ivory);padding:9px 12px;font-family:var(--font-sans);font-size:.85rem" />
          <button class="adm-btn danger" type="submit"><svg class="ic"><use href="#i-x"/></svg>Suspendre</button>
        </div>
      </form>
      <form method="POST" action="{{ route('admin.users.ban', $user) }}" style="margin-top:12px" onsubmit="return confirm('Bannir définitivement {{ $user->name }} ?');">
        @csrf
        <input type="hidden" name="reason" value="Bannissement administrateur" />
        <button class="adm-btn danger" type="submit" style="width:100%"><svg class="ic"><use href="#i-x"/></svg>Bannir définitivement</button>
      </form>
    @else
      <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" style="border-top:1px solid var(--line);padding-top:16px;margin-top:8px">
        @csrf
        <button class="adm-btn solid" type="submit" style="width:100%"><svg class="ic"><use href="#i-check"/></svg>Réactiver le compte</button>
      </form>
    @endif

    {{-- Vérification --}}
    <form method="POST" action="{{ route('admin.users.verify', $user) }}" style="border-top:1px solid var(--line);padding-top:16px;margin-top:16px">
      @csrf
      <label style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);font-weight:600">Niveau de vérification</label>
      <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
        <select name="level" style="flex:1;border:1px solid var(--line);background:var(--ivory);padding:9px 12px;font-family:var(--font-sans);font-size:.85rem">
          @foreach(['Bronze', 'Argent', 'Or'] as $lv)
            <option value="{{ $lv }}" @selected(($p->verification_level ?? 'Bronze') === $lv)>{{ $lv }}</option>
          @endforeach
        </select>
        <button class="adm-btn" type="submit">Appliquer</button>
      </div>
    </form>

    {{-- Mot de passe + suppression --}}
    <div style="border-top:1px solid var(--line);padding-top:16px;margin-top:16px;display:flex;flex-direction:column;gap:10px">
      <form method="POST" action="{{ route('admin.users.reset', $user) }}" onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->name }} ?');">
        @csrf
        <button class="adm-btn" type="submit" style="width:100%">Réinitialiser le mot de passe</button>
      </form>
      <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('SUPPRESSION DÉFINITIVE du compte de {{ $user->name }}. Cette action est irréversible. Confirmer ?');">
        @csrf @method('DELETE')
        <button class="adm-btn danger" type="submit" style="width:100%"><svg class="ic"><use href="#i-x"/></svg>Supprimer le compte (RGPD)</button>
      </form>
    </div>
  </div>
</div>

{{-- Historique des abonnements --}}
<div class="adm-card">
  <h3><svg class="ic"><use href="#i-spark"/></svg>Historique des abonnements</h3>
  @if($subscriptions->count())
    <table class="adm-table">
      <thead><tr><th>Formule</th><th>Montant</th><th>Durée</th><th>Statut</th><th>Début</th><th>Fin</th></tr></thead>
      <tbody>
        @foreach($subscriptions as $s)
          @php $tag = ['active' => 'ok', 'pending' => 'warn', 'expired' => '', 'cancelled' => 'bad'][$s->status] ?? ''; @endphp
          <tr>
            <td><b>{{ $s->plan?->name ?? '—' }}</b></td>
            <td>{{ number_format($s->amount, 0, ',', ' ') }} FCFA</td>
            <td>{{ $s->months ?? 1 }} mois</td>
            <td><span class="adm-tag {{ $tag }}">{{ $s->status_label }}</span></td>
            <td>{{ $s->starts_at?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $s->ends_at?->format('d/m/Y') ?? '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p style="color:var(--muted)">Aucun abonnement.</p>
  @endif
</div>

{{-- Blocages --}}
@if($blocksBy->count() || $blockedBy->count())
<div class="adm-split">
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-x"/></svg>A bloqué ({{ $blocksBy->count() }})</h3>
    @forelse($blocksBy as $b)
      <div class="reqline" style="padding:8px 0;border-bottom:1px solid var(--line)">
        <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($b->name, 0, 1) }}"></span>
        <div style="flex:1"><b>{{ $b->name }}</b><small style="display:block;color:var(--muted)">{{ \Illuminate\Support\Carbon::parse($b->created_at)->format('d/m/Y') }}</small></div>
        <a href="{{ route('admin.users.show', $b->id) }}" class="adm-btn">Voir</a>
      </div>
    @empty
      <p style="color:var(--muted)">Aucun.</p>
    @endforelse
  </div>
  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-x"/></svg>Bloqué par ({{ $blockedBy->count() }})</h3>
    @forelse($blockedBy as $b)
      <div class="reqline" style="padding:8px 0;border-bottom:1px solid var(--line)">
        <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($b->name, 0, 1) }}"></span>
        <div style="flex:1"><b>{{ $b->name }}</b><small style="display:block;color:var(--muted)">{{ \Illuminate\Support\Carbon::parse($b->created_at)->format('d/m/Y') }}</small></div>
        <a href="{{ route('admin.users.show', $b->id) }}" class="adm-btn">Voir</a>
      </div>
    @empty
      <p style="color:var(--muted)">Aucun.</p>
    @endforelse
  </div>
</div>
@endif

{{-- Signalements reçus --}}
@if($reportsReceived->count())
<div class="adm-card">
  <h3 style="color:var(--heart)"><svg class="ic"><use href="#i-flag"/></svg>Signalements reçus contre ce membre</h3>
  @foreach($reportsReceived as $r)
    <div class="reqline" style="padding:10px 0;border-bottom:1px solid var(--line)">
      <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($r->reporter?->name ?? '?', 0, 1) }}"></span>
      <div style="flex:1">
        <b>{{ $r->reporter?->name ?? 'Membre' }}</b> — {{ $r->reason_label }}
        <small style="display:block;color:var(--muted)">{{ $r->created_at->locale('fr')->diffForHumans() }} · <span class="adm-tag {{ ['pending' => 'warn', 'resolved' => 'ok', 'dismissed' => ''][$r->status] ?? '' }}">{{ $r->status_label }}</span></small>
      </div>
    </div>
  @endforeach
</div>
@endif

@endsection
