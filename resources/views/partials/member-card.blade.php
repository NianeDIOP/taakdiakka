@php
  $p = $m->profile;
  $base = $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($m->name);
  $stagger = $stagger ?? 0;
@endphp
<article class="lcard reveal" @if($stagger) data-d="{{ $stagger }}" @endif>
  <img class="bg" src="{{ asset('img/'.$base.'.webp') }}" alt="{{ $m->name }}" width="760" height="950" loading="lazy" decoding="async" onerror="this.onerror=function(){this.remove()};this.src='{{ asset('img/'.$base.'.jpg') }}'"/>
  <div class="scrim"></div>
  <span class="vdot"><svg class="ic"><use href="#i-check"/></svg></span>
  @if(($m->boost_active ?? 0) > 0)
    <span class="lc-boost"><svg class="ic sm"><use href="#i-spark"/></svg>En avant</span>
  @endif
  @auth
    @php $fd = $m->demandes->first(); @endphp
    @if($fd)
      <form class="favform" action="{{ route('favoris.toggle', $fd) }}" method="POST">@csrf
        <button type="submit" class="favbtn {{ in_array($fd->id, $favoriteIds ?? []) ? 'on' : '' }}" aria-label="Ajouter aux favoris">
          <svg class="ic"><use href="#i-heart"/></svg>
        </button>
      </form>
    @endif
  @endauth
  @if(optional($m->demandes->first())->status === 'engaged')
    <span class="lc-flag"><svg class="ic sm heart"><use href="#i-heart"/></svg>En conversation</span>
  @endif
  <a href="{{ route('members.show', $m) }}" class="card-link" aria-label="Voir le profil de {{ $m->name }}"></a>
  <div class="lc-info">
    <div class="lc-id">
      <b>{{ \Illuminate\Support\Str::before($m->name, ' ') }}@if($p && $p->age), {{ $p->age }} ans @endif</b>
      <div class="loc"><svg class="ic"><use href="#i-pin"/></svg>{{ $p->region ?? '—' }}</div>
    </div>
    <div class="lc-foot">
      <span class="lc-date"><svg class="ic sm"><use href="#i-crescent"/></svg>{{ $p->religion ?? '—' }}</span>
      <span class="lc-prof">{{ $p->profession ?? '—' }}</span>
    </div>
  </div>
</article>
