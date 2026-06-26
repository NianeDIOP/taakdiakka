{{-- $items, $title, $em, $sub, $more (url), $moreLabel, $emptyText --}}
@if($items->count())
  <section class="msection">
    <div class="msection-head">
      <h3>{{ $title }} <em style="color:var(--ink)">{{ $em ?? '' }}</em></h3>
      @isset($more)<a href="{{ $more }}" class="lnk">{{ $moreLabel ?? 'Tout voir' }}<svg class="ic sm"><use href="#i-arrow"/></svg></a>@endisset
    </div>
    @isset($sub)<p class="msection-sub">{{ $sub }}</p>@endisset
    <div class="listing">
      @foreach($items as $m)
        @include('partials.member-card', ['m' => $m, 'stagger' => $loop->index % 3])
      @endforeach
    </div>
  </section>
@endif
