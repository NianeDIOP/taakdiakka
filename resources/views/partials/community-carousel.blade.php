@php $posts = $posts ?? collect(); @endphp
@if($posts->count())
<div class="cca reveal">
  @foreach($posts as $p)
    @php $digest = $p->reactionDigest(); $cc = $p->post_comments_count ?? 0; @endphp
    <a href="{{ route('communaute.show', $p) }}" class="cca-card">
      <div class="cca-top">
        @if($p->author_photo)
          <span class="av s photo" style="background-image:url('{{ asset('img/'.$p->author_photo.'.webp') }}')"></span>
        @else
          <span class="av s" data-av="{{ $p->initial }}"></span>
        @endif
        <div class="cca-id">
          <b>{{ $p->display_name }}@if($p->author_verified)<svg class="ic"><use href="#i-verified"/></svg>@endif</b>
          <small>{{ $p->theme_emoji }} {{ $p->theme }}</small>
        </div>
      </div>
      <p class="cca-body">{{ \Illuminate\Support\Str::limit(strip_tags($p->body), 150) }}</p>
      <div class="cca-foot">
        <span class="cca-stat"><svg class="ic"><use href="#i-heart"/></svg>{{ $digest['total'] }}</span>
        <span class="cca-stat"><svg class="ic"><use href="#i-chat"/></svg>{{ $cc }}</span>
        <span class="cca-stat"><svg class="ic"><use href="#i-share"/></svg></span>
        <span class="cca-go">Lire<svg class="ic sm"><use href="#i-arrow"/></svg></span>
      </div>
    </a>
  @endforeach
  <a href="{{ route('communaute') }}" class="cca-card cca-more">
    <span>Voir toute la communauté</span>
    <svg class="ic"><use href="#i-arrow"/></svg>
  </a>
</div>
@else
  <p style="color:var(--muted);text-align:center">La communauté s'anime bientôt. 🤲</p>
@endif
