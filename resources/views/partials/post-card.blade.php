@php
  $stagger = $stagger ?? 0;
  $digest = $p->reactionDigest();
  $mine = $p->myReaction(auth()->id());
  $types = \App\Models\PostReaction::TYPES;
  $cCount = $p->post_comments_count ?? $p->postComments()->count();
  $isSaved = isset($savedIds)
    ? in_array($p->id, $savedIds)
    : (auth()->id() ? \Illuminate\Support\Facades\DB::table('saved_posts')->where('user_id', auth()->id())->where('post_id', $p->id)->exists() : false);
  $tone = 'post--' . \Illuminate\Support\Str::slug($p->theme ?? 'autre');
@endphp
<article class="post reveal {{ $tone }}" data-post="{{ $p->id }}" data-published="{{ $p->published_at?->toIso8601String() }}" @if($stagger) data-d="{{ $stagger }}" @endif>
  <div class="p-head">
    @if($p->author_photo)
      <span class="av photo" style="background-image:url('{{ asset('img/'.$p->author_photo.'.webp') }}')"></span>
    @else
      <span class="av" data-av="{{ $p->initial }}"></span>
    @endif
    <div class="p-id">
      <div class="name">{{ $p->display_name }}@if($p->author_verified)<svg class="ic"><use href="#i-verified"/></svg>@endif</div>
      <div class="meta"><svg class="ic sm"><use href="#i-calendar"/></svg>{{ $p->posted }}@if($p->location)<span class="dot"></span>{{ $p->location }}@endif</div>
    </div>
    <span class="theme">{{ $p->theme_emoji }} {{ $p->theme }}</span>
    @auth
      <div class="p-menu">
        <button class="p-menu-btn" data-menu-toggle aria-label="Options"><svg class="ic sm"><use href="#i-more"/></svg></button>
        <div class="p-menu-list">
          <button type="button" data-report-toggle><svg class="ic sm"><use href="#i-flag"/></svg>Signaler</button>
        </div>
      </div>
      <div class="report-picker" data-report-picker hidden>
        @foreach(\App\Models\Report::REASONS as $key => $label)
          <button type="button" data-report-reason="{{ $key }}">{{ $label }}</button>
        @endforeach
      </div>
    @endauth
  </div>

  @php $full = $full ?? false; $isLong = ! $full && \Illuminate\Support\Str::length($p->body) > 280; @endphp
  @if($isLong)
    <div class="p-body-wrap">
      <p class="p-body" data-excerpt>{{ \Illuminate\Support\Str::limit($p->body, 280) }} <button type="button" class="read-more" data-readmore>Lire la suite</button></p>
      <p class="p-body" data-fulltext hidden>{!! \App\Support\TextEnricher::render($p->body) !!}</p>
    </div>
  @else
    <p class="p-body">{!! \App\Support\TextEnricher::render($p->body) !!}</p>
  @endif

  @if($p->image_base)
    <a href="{{ asset('img/'.$p->image_base.'.jpg') }}" target="_blank" class="p-image">
      <picture>
        <source srcset="{{ asset('img/'.$p->image_base.'.webp') }}" type="image/webp" />
        <img src="{{ asset('img/'.$p->image_base.'.jpg') }}" alt="Image de la publication" loading="lazy" />
      </picture>
    </a>
  @endif

  <div class="reacts">
    <span class="re-sum" data-react-sum>
      @if($digest['total'])
        <span class="re-emojis">{{ implode('', $digest['emojis']) }}</span>
        <span data-react-total>{{ $digest['total'] }}</span>
      @endif
    </span>
    <button class="re-link" data-comments-toggle><span data-ccount>{{ $cCount }}</span> commentaire{{ $cCount > 1 ? 's' : '' }}</button>
  </div>

  <div class="p-actions">
    @auth
      <div class="react-wrap">
        <button class="p-btn like {{ $mine ? 'on' : '' }}" data-react-toggle>
          <span class="re-ic">{{ $mine ? $types[$mine][0] : '👍' }}</span><span data-react-label>{{ $mine ? $types[$mine][1] : "J'aime" }}</span>
        </button>
        <div class="react-picker">
          @foreach($types as $key => [$emoji, $label])
            <button type="button" data-react-type="{{ $key }}" title="{{ $label }}">{{ $emoji }}</button>
          @endforeach
        </div>
      </div>
      <button class="p-btn" data-comments-toggle><svg class="ic"><use href="#i-chat"/></svg>Commenter</button>
    @else
      <a href="{{ route('login') }}" class="p-btn"><svg class="ic"><use href="#i-heart"/></svg>J'aime</a>
      <button class="p-btn" data-comments-toggle><svg class="ic"><use href="#i-chat"/></svg>Commenter</button>
    @endauth
    <button class="p-btn" data-share><svg class="ic"><use href="#i-share"/></svg>Partager</button>
    @auth
      <button class="p-btn save {{ $isSaved ? 'on' : '' }}" data-save-toggle aria-label="Enregistrer la publication">
        <svg class="ic"><use href="#i-bookmark"/></svg><span data-save-label>{{ $isSaved ? 'Enregistré' : 'Enregistrer' }}</span>
      </button>
    @endauth
  </div>

  <div class="comments" data-comments hidden>
    <div class="comments-list" data-clist></div>
    <button class="load-more-comments" data-cmore data-next="1" hidden>Voir les commentaires précédents</button>
    @auth
      <form class="add-comment" data-cform>
        <span class="av s photo" style="background-image:url('{{ asset('img/'.(auth()->user()->profile && auth()->user()->profile->photo ? pathinfo(auth()->user()->profile->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo(auth()->user()->name)).'.webp') }}')"></span>
        <input type="text" name="body" placeholder="Répondre avec bienveillance… 💬" autocomplete="off" required />
        <button type="button" class="emoji-btn" data-emoji-toggle aria-label="Émojis">😊</button>
        <button type="submit" aria-label="Envoyer"><svg class="ic"><use href="#i-send"/></svg></button>
      </form>
    @else
      <a href="{{ route('login') }}" class="comment-login-cta"><svg class="ic sm"><use href="#i-heart"/></svg>Connectez-vous pour commenter et participer</a>
    @endauth
  </div>
</article>
