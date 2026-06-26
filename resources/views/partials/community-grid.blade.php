@php
  $homepage = $homepage ?? false;
  $tag = $tag ?? null;
  $themeEmoji = ['Confession' => '🌙', 'Conseil' => '💡', 'Témoignage' => '💍', 'Question' => '❓'];
  $themeCounts = \App\Models\Post::selectRaw('theme, COUNT(*) as c')->groupBy('theme')->pluck('c', 'theme');

  // Question du jour — change chaque jour, stable sur la journée.
  $qList = [
    "Quelle qualité comptez-vous le plus chez votre futur(e) époux(se) ?",
    "Pour vous, qu'est-ce qui fait la réussite d'un mariage ?",
    "Quel conseil donneriez-vous à quelqu'un qui cherche à se marier ?",
    "Qu'attendez-vous d'abord d'un foyer : la sérénité, la foi, l'ambition ?",
    "Comment savoir que c'est la bonne personne, selon vous ?",
    "Quelle est, pour vous, la plus belle preuve d'amour au quotidien ?",
    "Famille et mariage : quel équilibre recherchez-vous ?",
  ];
  $qotd = $qList[(int) date('z') % count($qList)];

  // Tendances — hashtags les plus utilisés récemment (mis en cache).
  $trending = \Illuminate\Support\Facades\Cache::remember('community_trending', 300, function () {
    $counts = [];
    foreach (\App\Models\Post::whereNotNull('published_at')->latest('published_at')->take(300)->pluck('body') as $b) {
      preg_match_all('/#([A-Za-zÀ-ÖØ-öø-ÿ0-9_]{2,30})/u', (string) $b, $m);
      foreach ($m[1] as $t) { $k = mb_strtolower($t); $counts[$k] = ($counts[$k] ?? 0) + 1; }
    }
    arsort($counts);
    return array_slice($counts, 0, 8, true);
  });
@endphp
<div class="community-grid">
  <div class="feed">

    <div class="feed-tabs reveal">
      @foreach($themes as $t)
        <a href="{{ $t === 'Tout' ? route('communaute') : route('communaute', ['theme' => $t]) }}" class="tab {{ ($theme === $t && !$tag) ? 'active' : '' }}">{{ $t === 'Tout' ? 'Tout' : $t.'s' }}</a>
      @endforeach
    </div>

    @if($tag)
      <div class="tag-banner reveal">
        Publications avec <strong>#{{ $tag }}</strong>
        <a href="{{ route('communaute') }}" class="tag-clear"><svg class="ic sm"><use href="#i-x"/></svg>Effacer</a>
      </div>
    @endif

    @if(! $tag)
    <div class="qotd reveal" data-qotd data-day="{{ now()->toDateString() }}">
      <span class="qotd-ic">❓</span>
      <div class="qotd-body">
        <span class="label">Question du jour</span>
        <p>{{ $qotd }}</p>
      </div>
      @auth
        <button type="button" class="btn btn-line qotd-btn" data-qotd-answer data-q="{{ $qotd }}"><svg class="ic sm"><use href="#i-chat"/></svg>Répondre</button>
      @else
        <a href="{{ route('login') }}" class="btn btn-line qotd-btn"><svg class="ic sm"><use href="#i-chat"/></svg>Répondre</a>
      @endauth
      <button type="button" class="qotd-x" data-qotd-dismiss aria-label="Masquer la question du jour"><svg class="ic sm"><use href="#i-x"/></svg></button>
    </div>
    @endif

    @auth
    {{-- Composeur de publication --}}
    @php
      $pp = auth()->user()->profile;
      $meAv = $pp && $pp->photo ? pathinfo($pp->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo(auth()->user()->name);
    @endphp
    <form class="composer reveal" method="POST" action="{{ route('community.store') }}" enctype="multipart/form-data" id="composer">
      @csrf
      <div class="row">
        <span class="av photo" style="background-image:url('{{ asset('img/'.$meAv.'.webp') }}')"></span>
        <textarea class="q" name="body" rows="1" placeholder="Partagez une confession, une question, un conseil…" required></textarea>
      </div>

      <div class="composer-preview" id="composerPreview" hidden>
        <img id="composerPreviewImg" alt="Aperçu" />
        <button type="button" class="composer-preview-x" id="composerPreviewX" aria-label="Retirer l'image"><svg class="ic sm"><use href="#i-x"/></svg></button>
      </div>

      <div class="poll-fields" id="pollFields" hidden>
        <span class="poll-fields-hint">Sondage — la question est le texte ci-dessus</span>
        <input type="text" name="poll_options[]" maxlength="80" placeholder="Option 1" />
        <input type="text" name="poll_options[]" maxlength="80" placeholder="Option 2" />
        <input type="text" name="poll_options[]" maxlength="80" placeholder="Option 3 (facultatif)" />
        <input type="text" name="poll_options[]" maxlength="80" placeholder="Option 4 (facultatif)" />
      </div>

      <div class="row2">
        <div class="composer-opts">
          <select name="theme" class="theme-sel">
            <option value="Confession">🌙 Confession</option>
            <option value="Conseil">💡 Conseil</option>
            <option value="Témoignage">💍 Témoignage</option>
            <option value="Question">❓ Question</option>
          </select>
          <input type="file" name="image" id="composerImage" accept="image/jpeg,image/png,image/webp" style="display:none" />
          <button type="button" class="composer-photo" id="composerPhotoBtn"><svg class="ic sm"><use href="#i-eye"/></svg>Photo</button>
          <button type="button" class="composer-photo" id="pollBtn" data-poll-btn><svg class="ic sm"><use href="#i-grid"/></svg>Sondage</button>
          <label class="anon" id="anonRow">
            <input type="checkbox" name="is_anonymous" value="1" style="display:none" />
            <span class="toggle"></span>Publier anonymement
          </label>
        </div>
        <button class="btn btn-primary" type="submit">Publier<svg class="ic sm"><use href="#i-send"/></svg></button>
      </div>
    </form>
    @endauth

    @forelse($posts as $p)
      @include('partials.post-card', ['p' => $p, 'stagger' => $loop->index % 3, 'savedIds' => $savedIds ?? null])
    @empty
      <p style="color:var(--muted)">Aucune publication pour le moment. Soyez le premier à partager. 🤲</p>
    @endforelse

    @if($homepage)
      <div class="more-row" style="text-align:left;margin-top:24px">
        <a class="btn btn-line" href="{{ route('communaute') }}">Voir toute la communauté<svg class="ic sm"><use href="#i-arrow"/></svg></a>
      </div>
    @elseif($posts instanceof \Illuminate\Contracts\Pagination\Paginator && $posts->hasPages())
      <div class="more-row">
        @if($posts->hasMorePages())
          <a class="btn btn-line" href="{{ $posts->nextPageUrl() }}">Voir plus de publications<svg class="ic sm"><use href="#i-arrow"/></svg></a>
        @else
          <span style="color:var(--muted);font-size:.85rem">Vous avez tout vu. 🤲</span>
        @endif
      </div>
    @endif
  </div>

  <aside class="side">
    <div class="swidget swidget-themes reveal">
      <h4>Thèmes du moment</h4>
      <div class="theme-list">
        @foreach($themeEmoji as $t => $emoji)
          <a href="{{ route('communaute', ['theme' => $t]) }}" class="{{ ($theme ?? 'Tout') === $t ? 'on' : '' }}">{{ $emoji }} {{ $t }}s<span>{{ $themeCounts[$t] ?? 0 }}</span></a>
        @endforeach
      </div>
    </div>
    @if(! empty($trending))
    <div class="swidget reveal" data-d="1">
      <h4>Tendances</h4>
      <div class="trend-list">
        @foreach($trending as $t => $count)
          <a href="{{ route('communaute', ['tag' => $t]) }}" class="trend {{ ($tag ?? null) === $t ? 'on' : '' }}">
            <span class="trend-tag">#{{ $t }}</span><span class="trend-c">{{ $count }}</span>
          </a>
        @endforeach
      </div>
    </div>
    @endif
    <div class="swidget reveal" data-d="1">
      <h4>Membres en ligne <span class="online-count">{{ $online->count() }}</span></h4>
      <div class="online">
        @forelse($online as $u)
          @php $oav = $u->profile && $u->profile->photo ? pathinfo($u->profile->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u->name); @endphp
          <a class="o-row" @auth href="{{ route('members.show', $u) }}" @endauth style="text-decoration:none;color:inherit">
            <span class="av s photo on" style="background-image:url('{{ asset('img/'.$oav.'.webp') }}')"></span>
            <div><div class="nm">{{ \Illuminate\Support\Str::before($u->name, ' ') }}</div><div class="st">en ligne</div></div>
          </a>
        @empty
          <p style="color:var(--muted);font-size:.84rem">Personne en ligne pour l'instant.</p>
        @endforelse
      </div>
    </div>
  </aside>
</div>

<script>
  window.TD_AUTH = @json(auth()->check());
  window.TD_THEME = @json($theme ?? 'Tout');
  window.TD_TAG = @json($tag ?? null);
  @if(config('broadcasting.connections.reverb.key'))
  window.TD_REVERB = {
    key:    @json(config('broadcasting.connections.reverb.key')),
    host:   @json(config('broadcasting.connections.reverb.options.host')),
    port:   {{ (int) config('broadcasting.connections.reverb.options.port', 8080) }},
    scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'http')),
  };
  @endif
</script>
@if(config('broadcasting.connections.reverb.key'))
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endif
<script src="{{ asset('js/community.js') }}"></script>
