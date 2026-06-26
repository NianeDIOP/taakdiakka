@php $stagger = $stagger ?? 0; @endphp
<article class="lcard reveal {{ $d->is_discret ? 'discret-card' : '' }}" @unless($d->is_discret) data-av="{{ $d->initial }}" @endunless @if($stagger) data-d="{{ $stagger }}" @endif>
  @if($d->is_discret)
    <div class="discret-ic"><svg class="ic lg"><use href="#i-user"/></svg><span>Photo sur demande</span></div>
  @else
    @php $base = pathinfo($d->photo, PATHINFO_FILENAME); @endphp
    <img class="bg" src="{{ asset('img/'.$base.'.webp') }}" alt="{{ $d->name }}" width="760" height="950" loading="lazy" decoding="async" onerror="this.onerror=function(){this.remove()};this.src='{{ asset('img/'.$base.'.jpg') }}'"/>
  @endif
  <div class="scrim"></div>
  <span class="vdot"><svg class="ic"><use href="#i-check"/></svg></span>
  @auth
    <form class="favform" action="{{ route('favoris.toggle', $d) }}" method="POST">@csrf
      <button type="submit" class="favbtn {{ in_array($d->id, $favoriteIds ?? []) ? 'on' : '' }}" aria-label="Ajouter aux favoris">
        <svg class="ic"><use href="#i-heart"/></svg>
      </button>
    </form>
  @endauth
  <a href="{{ route('demandes.show', $d) }}" class="card-link" aria-label="Voir le profil de {{ $d->display_name }}"></a>
  <div class="lc-info">
    <div class="lc-id"><b>{{ $d->display_name }}</b><div class="loc"><svg class="ic"><use href="#i-pin"/></svg>{{ $d->region }}</div></div>
    <div class="lc-tags">@foreach(array_slice($d->tags ?? [], 0, 2) as $t)<span>{{ $t }}</span>@endforeach</div>
    <div class="lc-foot">
      <span class="lc-date"><svg class="ic sm"><use href="#i-calendar"/></svg>{{ ucfirst($d->posted) }}</span>
      <span class="lc-prof">{{ $d->is_discret ? 'Profil discret' : $d->profession }}</span>
    </div>
  </div>
</article>
