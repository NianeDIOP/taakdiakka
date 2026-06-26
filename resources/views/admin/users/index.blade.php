@extends('layouts.admin')

@section('title', 'Utilisateurs — Administration')
@section('heading', 'Utilisateurs')

@section('content')

<form method="GET" class="adm-filters">
  <div class="fld" style="flex:1;min-width:220px">
    <label>Recherche</label>
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nom ou e-mail…" />
  </div>
  <div class="fld">
    <label>Genre</label>
    <select name="gender">
      <option value="">Tous</option>
      <option value="Femme" @selected(($filters['gender'] ?? '') === 'Femme')>Femmes</option>
      <option value="Homme" @selected(($filters['gender'] ?? '') === 'Homme')>Hommes</option>
    </select>
  </div>
  <div class="fld">
    <label>Vérification</label>
    <select name="verif">
      <option value="">Tous</option>
      @foreach(['Bronze', 'Argent', 'Or'] as $lv)
        <option value="{{ $lv }}" @selected(($filters['verif'] ?? '') === $lv)>{{ $lv }}</option>
      @endforeach
    </select>
  </div>
  <div class="fld">
    <label>Statut</label>
    <select name="status">
      <option value="">Tous</option>
      <option value="active" @selected(($filters['status'] ?? '') === 'active')>Actif</option>
      <option value="suspended" @selected(($filters['status'] ?? '') === 'suspended')>Suspendu</option>
      <option value="banned" @selected(($filters['status'] ?? '') === 'banned')>Banni</option>
    </select>
  </div>
  <div class="fld">
    <label>Tri</label>
    <select name="sort">
      <option value="recent" @selected(($filters['sort'] ?? '') === 'recent')>Récents</option>
      <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Anciens</option>
      <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Nom A→Z</option>
    </select>
  </div>
  <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-search"/></svg>Filtrer</button>
</form>

<div class="adm-card" style="padding:0;overflow-x:auto">
  <table class="adm-table">
    <thead>
      <tr><th>Membre</th><th>Genre</th><th>Région</th><th>Vérif.</th><th>Statut</th><th>Inscrit</th><th></th></tr>
    </thead>
    <tbody>
      @forelse($users as $u)
        @php
          $p = $u->profile;
          $statusTag = ['active' => 'ok', 'suspended' => 'warn', 'banned' => 'bad'][$u->status] ?? '';
          $verifTag = ['Or' => 'gold', 'Argent' => 'ok', 'Bronze' => 'warn'][$p->verification_level ?? 'Bronze'] ?? '';
          $av = $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u->name);
        @endphp
        <tr>
          <td>
            <div class="u-cell">
              @if($av)
                <span class="av s photo" style="background-image:url('{{ asset('img/'.$av.'.webp') }}')"></span>
              @else
                <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($u->name, 0, 1) }}"></span>
              @endif
              <div><b>{{ $u->name }}</b><small>{{ $u->email }}</small></div>
            </div>
          </td>
          <td>{{ $p?->gender ?? '—' }}</td>
          <td>{{ $p?->region ?? '—' }}</td>
          <td><span class="adm-tag {{ $verifTag }}">{{ $p->verification_level ?? 'Bronze' }}</span></td>
          <td><span class="adm-tag {{ $statusTag }}">{{ \App\Models\User::STATUS_LABELS[$u->status] ?? 'Actif' }}</span></td>
          <td>{{ $u->created_at->format('d/m/Y') }}</td>
          <td><a href="{{ route('admin.users.show', $u) }}" class="adm-btn">Gérer</a></td>
        </tr>
      @empty
        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">Aucun membre trouvé.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($users->hasPages())
  <nav class="pager">
    @if($users->onFirstPage())
      <span class="pager-btn disabled">Précédent</span>
    @else
      <a class="pager-btn" href="{{ $users->previousPageUrl() }}" rel="prev">Précédent</a>
    @endif
    <span class="pager-info">Page {{ $users->currentPage() }} / {{ $users->lastPage() }} · {{ $users->total() }} membres</span>
    @if($users->hasMorePages())
      <a class="pager-btn" href="{{ $users->nextPageUrl() }}" rel="next">Suivant</a>
    @else
      <span class="pager-btn disabled">Suivant</span>
    @endif
  </nav>
@endif

@endsection
