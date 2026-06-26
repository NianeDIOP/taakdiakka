@php
  $icon = $icon ?? 'heart';
  $compact = $compact ?? false;
@endphp
<div class="empty-state{{ $compact ? ' compact' : '' }}">
  <span class="empty-ic"><svg class="ic"><use href="#i-{{ $icon }}"/></svg></span>
  @isset($title)<h4>{{ $title }}</h4>@endisset
  <p>{!! $text ?? '' !!}</p>
  @isset($ctaUrl)<a href="{{ $ctaUrl }}" class="btn btn-line"><svg class="ic sm"><use href="#i-{{ $ctaIcon ?? 'arrow' }}"/></svg>{{ $ctaLabel ?? 'Découvrir' }}</a>@endisset
</div>
