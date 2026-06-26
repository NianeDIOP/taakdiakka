@extends('layouts.member')

@section('title', 'Découvrir les membres — TàakDiàkka')

@section('content')

<div class="m-head">
  <span class="label">Exploration</span>
  <h2>Découvrir nos <em style="color:var(--ink)">membres</em></h2>
  <p>{{ $sought === 'Femme' ? 'Des femmes' : ($sought === 'Homme' ? 'Des hommes' : 'Des profils') }} sérieux et vérifiés, affinés selon vos critères.</p>
</div>

@if($needsInfo)
  <div class="completion" style="margin-bottom:28px">
    <div class="top"><div>
      <strong>Renseignez votre genre pour voir des profils ciblés</strong><br/>
      <small>Nous présentons uniquement les profils du genre que vous recherchez.</small>
    </div></div>
    <a class="lnk" href="{{ route('profile.edit') }}" style="display:inline-flex;margin-top:12px">Compléter mon profil<svg class="ic sm"><use href="#i-arrow"/></svg></a>
  </div>
@endif

<form method="GET" action="{{ route('members.discover') }}" class="mfilters">
  <div class="mfilters-grid">
    <div class="fgroup">
      <span class="lab">Recherche</span>
      <input type="text" name="q" value="{{ $f['q'] }}" placeholder="Nom, profession…" />
    </div>
    <div class="fgroup">
      <span class="lab">Région</span>
      <select name="region"><option value="">Toutes</option>
        @foreach($options['region'] as $o)<option @selected($f['region']===$o)>{{ $o }}</option>@endforeach
      </select>
    </div>
    <div class="fgroup">
      <span class="lab">Religion</span>
      <select name="religion"><option value="">Toutes</option>
        @foreach($options['religion'] as $o)<option @selected($f['religion']===$o)>{{ $o }}</option>@endforeach
      </select>
    </div>
    <div class="fgroup">
      <span class="lab">Pratique</span>
      <select name="practice"><option value="">Toutes</option>
        @foreach($options['practice'] as $o)<option @selected($f['practice']===$o)>{{ $o }}</option>@endforeach
      </select>
    </div>
    <div class="fgroup">
      <span class="lab">Âge min.</span>
      <input type="number" name="age_min" min="18" max="99" value="{{ $f['age_min'] }}" placeholder="18" />
    </div>
    <div class="fgroup">
      <span class="lab">Âge max.</span>
      <input type="number" name="age_max" min="18" max="99" value="{{ $f['age_max'] }}" placeholder="99" />
    </div>
  </div>
  <div class="mfilters-actions">
    <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-search"/></svg>Filtrer</button>
    <a href="{{ route('members.discover') }}" class="lnk">Réinitialiser</a>
    <span class="mfilters-count">{{ $members->total() }} membre{{ $members->total() > 1 ? 's' : '' }}</span>
  </div>
</form>

@php $deck = $members->onFirstPage() ? $members->take(8) : collect(); @endphp
@if($deck->count())
<section class="deck-wrap reveal" data-deck>
  <div class="deck-head">
    <span class="label center">Découverte rapide</span>
    <p class="deck-hint">Glissez à droite si un profil vous plaît, à gauche pour passer — ou utilisez les boutons.</p>
  </div>
  <div class="deck">
    @foreach($deck as $i => $m)
      @php $dp = $m->profile; $db = $dp && $dp->photo ? pathinfo($dp->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($m->name); @endphp
      <article class="dcard" data-dcard data-interest="{{ route('interests.toggle', $m) }}">
        <div class="dcard-photo" style="background-image:url('{{ asset('img/'.$db.'.webp') }}')"></div>
        <span class="dcard-like">Intéressé ❤</span>
        <span class="dcard-nope">Passer</span>
        <a href="{{ route('members.show', $m) }}" class="dcard-info">
          <b>{{ \Illuminate\Support\Str::before($m->name, ' ') }}@if($dp && $dp->age), {{ $dp->age }} ans @endif</b>
          <span class="dcard-meta"><svg class="ic sm"><use href="#i-pin"/></svg>{{ $dp->region ?? '—' }}@if($dp && $dp->profession) · {{ $dp->profession }}@endif</span>
        </a>
      </article>
    @endforeach
    <div class="deck-empty">Vous avez parcouru les suggestions. Explorez toute la liste ci-dessous 👇</div>
  </div>
  <div class="deck-actions">
    <button type="button" class="deck-btn nope" data-deck-nope aria-label="Passer"><svg class="ic"><use href="#i-x"/></svg></button>
    <button type="button" class="deck-btn like" data-deck-like aria-label="Marquer mon intérêt"><svg class="ic heart"><use href="#i-heart"/></svg></button>
  </div>
</section>
@endif

@if($members->count())
  <div class="listing">
    @foreach($members as $m)
      @include('partials.member-card', ['m' => $m, 'stagger' => $loop->index % 3])
    @endforeach
  </div>

  @if($members->hasPages())
    <nav class="pager">
      @if($members->onFirstPage())
        <span class="pager-btn disabled">Précédent</span>
      @else
        <a class="pager-btn" href="{{ $members->previousPageUrl() }}" rel="prev">Précédent</a>
      @endif
      <span class="pager-info">Page {{ $members->currentPage() }} / {{ $members->lastPage() }}</span>
      @if($members->hasMorePages())
        <a class="pager-btn" href="{{ $members->nextPageUrl() }}" rel="next">Suivant</a>
      @else
        <span class="pager-btn disabled">Suivant</span>
      @endif
    </nav>
  @endif
@else
  <p class="empty">Aucun membre ne correspond à ces critères. <a href="{{ route('members.discover') }}" class="lnk">Réinitialiser les filtres</a></p>
@endif

@endsection
