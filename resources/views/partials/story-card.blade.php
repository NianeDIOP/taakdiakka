@php $stagger = $stagger ?? 0; @endphp
<div class="story reveal" @if($stagger) data-d="{{ $stagger }}" @endif>
  <span class="badge"><svg class="ic sm {{ $s->badge_heart ? 'heart' : '' }}"><use href="#i-{{ $s->badge_icon }}"/></svg>{{ $s->badge_label }}</span>
  <p class="quote">{{ $s->quote }}</p>
  @php
    $names = preg_split('/\s*&\s*/', $s->couple);
    $a = \App\Support\Avatar::photo($names[0] ?? null);
    $b = \App\Support\Avatar::photo($names[1] ?? null);
  @endphp
  <div class="who">
    <span class="couple-ava">
      <span class="av s photo" style="background-image:url('{{ asset('img/'.$a.'.webp') }}')"></span>
      <span class="av s photo" style="background-image:url('{{ asset('img/'.$b.'.webp') }}')"></span>
    </span>
    <div><b>{{ $s->couple }}</b><small>{{ $s->location }}</small></div>
  </div>
</div>
