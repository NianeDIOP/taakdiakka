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
  <div class="lc-info">
    <div class="lc-id"><b>{{ $d->display_name }}</b><div class="loc"><svg class="ic"><use href="#i-pin"/></svg>{{ $d->region }}</div></div>
    <div class="lc-job">{{ $d->is_discret ? 'Profil discret' : $d->profession }}</div>
    <p class="lc-quote">« {{ $d->quote }} »</p>
    <div class="lc-tags">@foreach($d->tags ?? [] as $t)<span>{{ $t }}</span>@endforeach</div>
    <div class="lc-foot"><span class="lc-date"><svg class="ic sm"><use href="#i-calendar"/></svg>{{ ucfirst($d->posted) }}</span><a class="lnk">Contacter</a></div>
  </div>
</article>
