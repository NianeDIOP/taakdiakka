@extends('layouts.member')

@section('title', $user->name . ' — TàakDiàkka')

@php
  $p = $user->profile;
  $base = $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($user->name);
  $langs = $p->languages ?? [];
  $val = fn ($v) => filled($v) ? e($v) : '—';
@endphp

@section('content')

<a href="{{ url()->previous() }}" class="lnk profile-back" style="margin-bottom:24px"><svg class="ic sm" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg>Retour</a>

<div class="profile profile--show" style="align-items:start">
  <div>
    <div class="profile-photo" style="aspect-ratio:4/5">
      <picture>
        <source srcset="{{ asset('img/'.$base.'.webp') }}" type="image/webp" />
        <img src="{{ asset('img/'.$base.'.jpg') }}" alt="{{ $user->name }}" />
      </picture>
      <span class="vdot"><svg class="ic"><use href="#i-check"/></svg></span>
    </div>

    {{-- Galerie --}}
    @if($user->photos->count())
      @php
        $visible = \App\Support\FeatureGate::visiblePhotos(auth()->user());
        $shown = $user->photos->take($visible);
        $locked = $user->photos->count() - $shown->count();
      @endphp
      <div style="margin-top:16px">
        <span class="label">En images</span>
        <div class="gallery-grid" style="margin-top:10px;grid-template-columns:repeat(3,1fr)">
          @foreach($shown as $ph)
            <div class="gallery-item">
              <picture>
                <source srcset="{{ asset('img/'.$ph->base.'.webp') }}" type="image/webp" />
                <img src="{{ asset('img/'.$ph->base.'.jpg') }}" alt="Photo" loading="lazy" />
              </picture>
            </div>
          @endforeach
          @if($locked > 0)
            <a href="{{ route('tarifs') }}" class="gallery-item gallery-locked" title="Réservé aux abonnés">
              <span class="gallery-locked-in">
                <svg class="ic"><use href="#i-verified"/></svg>
                <b>+{{ $locked }}</b>
                <small>Photos réservées aux abonnés</small>
              </span>
            </a>
          @endif
        </div>
      </div>
    @endif
  </div>

  <div>
    <span class="label">Profil membre</span>
    <h1 style="font-family:var(--font-serif);font-weight:500;font-size:clamp(1.9rem,4vw,2.8rem);margin:10px 0 6px">
      {{ $user->name }}@if($p->age), {{ $p->age }} ans @endif
    </h1>
    <div class="profile-loc" style="margin-bottom:8px"><svg class="ic"><use href="#i-pin"/></svg>{{ $val($p->region) }}</div>
    @if($user->hasActiveSubscription())
      <span class="profile-badge" style="color:var(--gold);box-shadow:inset 0 0 0 1px var(--gold)"><svg class="ic"><use href="#i-spark"/></svg>Membre Premium</span>
    @endif
    @if($demande)
      <span class="profile-badge"><svg class="ic"><use href="#i-verified"/></svg>Vérifié — niveau {{ $demande->verification_level }}</span>
      @if($demande->status === 'engaged')
        <span class="profile-badge" style="color:var(--heart);box-shadow:inset 0 0 0 1px var(--heart)"><svg class="ic heart"><use href="#i-heart"/></svg>En conversation sérieuse</span>
      @endif
    @endif

    @if($p->bio)
      <p class="profile-quote" style="font-size:1.25rem;margin:20px 0">« {{ $p->bio }} »</p>
    @endif

    {{-- Actions --}}
    @unless($rel['isSelf'])
    @php
      $me            = auth()->user();
      $isBlocked     = $me->hasBlocked($user);
      $blockedByThem = ! $isBlocked && $user->hasBlocked($me);
      $canFriend     = \App\Support\FeatureGate::canSendFriendRequest($me);
      $canMsg        = \App\Support\FeatureGate::canSendMessage($me, $user);
      $msgReason     = \App\Support\FeatureGate::messageBlockReason($me, $user);
    @endphp
    @if($isBlocked)
      <div class="profile-blocked">
        <p><svg class="ic"><use href="#i-x"/></svg> Vous avez bloqué {{ $user->name }}. Aucun échange n'est possible.</p>
        <form action="{{ route('members.unblock', $user) }}" method="POST">@csrf @method('DELETE')
          <button type="submit" class="btn btn-line">Débloquer ce membre</button>
        </form>
      </div>
    @elseif($blockedByThem)
      <div class="profile-blocked">
        <p><svg class="ic"><use href="#i-x"/></svg> Les échanges ne sont pas disponibles avec ce membre.</p>
      </div>
    @else
    {{-- Actions principales : créer le lien, écrire, mettre en favori --}}
    <div class="profile-cta" style="margin:24px 0 6px">
      @if($rel['friendStatus'] === 'friends')
        <button type="button" class="btn btn-line" style="pointer-events:none"><svg class="ic sm heart"><use href="#i-check"/></svg>Amis</button>
      @elseif($rel['friendStatus'] === 'pending_sent')
        <span class="btn btn-line" style="opacity:.7;pointer-events:none"><svg class="ic sm"><use href="#i-check"/></svg>Demande envoyée</span>
      @elseif($rel['friendStatus'] === 'pending_received')
        <span class="btn btn-line" style="opacity:.7;pointer-events:none">Vous a invité(e)</span>
      @elseif($canFriend)
        <form action="{{ route('friends.request', $user) }}" method="POST">@csrf
          <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-user"/></svg>Ajouter comme ami</button>
        </form>
      @else
        <a href="{{ route('tarifs') }}" class="btn btn-line" title="Les demandes d'amis sont réservées aux membres abonnés"><svg class="ic sm"><use href="#i-user"/></svg>Ajouter comme ami&nbsp;<span class="cta-lock">Premium ✨</span></a>
      @endif

      @if($canMsg)
        <form action="{{ route('messages.start', $user) }}" method="POST">@csrf
          <button type="submit" class="btn btn-line"><svg class="ic sm"><use href="#i-message"/></svg>Message</button>
        </form>
      @elseif($msgReason === 'premium')
        <a href="{{ route('tarifs') }}" class="btn btn-line" title="La messagerie est réservée aux membres abonnés"><svg class="ic sm"><use href="#i-message"/></svg>Message&nbsp;<span class="cta-lock">Premium ✨</span></a>
      @else
        <span class="btn btn-line" style="opacity:.6;pointer-events:none" title="Devenez amis acceptés pour pouvoir écrire"><svg class="ic sm"><use href="#i-message"/></svg>Message&nbsp;<span class="cta-lock">Amis requis</span></span>
      @endif

      @if($demande)
        <form action="{{ route('favoris.toggle', $demande) }}" method="POST">@csrf
          <button type="submit" class="btn btn-line"><svg class="ic sm {{ $rel['isFavorite'] ? 'heart' : '' }}"><use href="#i-heart"/></svg>Favori</button>
        </form>
      @endif
    </div>

    {{-- Marques d'attention discrètes : intérêt & suivi --}}
    <div class="profile-cta-sec">
      <form action="{{ route('interests.toggle', $user) }}" method="POST">@csrf
        <button type="submit" class="link-act {{ $rel['isInterested'] || $rel['isMatch'] ? 'on' : '' }}">
          <svg class="ic sm {{ $rel['isInterested'] || $rel['isMatch'] ? 'heart' : '' }}"><use href="#i-heart"/></svg>{{ $rel['isMatch'] ? 'Match ❤' : ($rel['isInterested'] ? 'Intérêt envoyé' : 'Marquer mon intérêt') }}
        </button>
      </form>
      <span class="sep-dot"></span>
      <form action="{{ route('follow.toggle', $user) }}" method="POST">@csrf
        <button type="submit" class="link-act {{ $rel['isFollowing'] ? 'on' : '' }}"><svg class="ic sm"><use href="#i-{{ $rel['isFollowing'] ? 'check' : 'plus' }}"/></svg>{{ $rel['isFollowing'] ? 'Suivi(e)' : 'Suivre' }}</button>
      </form>
    </div>

    {{-- Cadeaux virtuels --}}
    @php $gifts = \App\Models\Gift::active()->get(); @endphp
    @if($gifts->count())
    <div class="gift-section" style="margin:20px 0 12px">
      <button type="button" class="btn btn-line" onclick="document.getElementById('giftModal').showModal()" style="gap:6px">
        <span style="font-size:1.1rem">🎁</span> Envoyer un cadeau
        <span style="font-size:.72rem;color:var(--muted);margin-left:4px">🪙 {{ auth()->user()->coins_balance }}</span>
      </button>
    </div>

    <dialog id="giftModal" class="gift-modal" onclick="if(event.target===this)this.close()">
      <div class="gift-modal-inner">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <h3 style="font-family:var(--font-serif);font-size:1.3rem;font-weight:500;margin:0">Envoyer un cadeau à {{ $user->name }}</h3>
          <button type="button" onclick="this.closest('dialog').close()" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--muted)">&times;</button>
        </div>
        <p style="font-size:.82rem;color:var(--muted);margin-bottom:16px">Solde : <b style="color:var(--gold)">🪙 {{ auth()->user()->coins_balance }}</b> pièces &middot; <a href="{{ route('coins.shop') }}" style="color:var(--gold)">Recharger</a></p>
        <div class="gift-grid">
          @foreach($gifts as $g)
            <form action="{{ route('gifts.send', $user) }}" method="POST" class="gift-item {{ auth()->user()->coins_balance < $g->coins_cost ? 'disabled' : '' }}">@csrf
              <input type="hidden" name="gift_id" value="{{ $g->id }}">
              <span class="gift-emoji">{{ $g->emoji }}</span>
              <span class="gift-name">{{ $g->name }}</span>
              <span class="gift-cost">🪙 {{ $g->coins_cost }}</span>
              <button type="submit" {{ auth()->user()->coins_balance < $g->coins_cost ? 'disabled' : '' }}>Envoyer</button>
            </form>
          @endforeach
        </div>
      </div>
    </dialog>
    @endif

    {{-- Cadeaux reçus --}}
    @php $receivedGifts = \App\Models\SentGift::where('receiver_id', $user->id)->with(['sender', 'gift'])->latest()->take(6)->get(); @endphp
    @if($receivedGifts->count())
    <div class="gifts-received" style="margin:16px 0">
      <span class="label">Cadeaux reçus</span>
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px">
        @foreach($receivedGifts as $sg)
          <div class="gift-received-item" title="De {{ $sg->sender->name }}{{ $sg->message ? ' : '.$sg->message : '' }}">
            <span>{{ $sg->gift->emoji }}</span>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Modération : signaler / bloquer --}}
    <div class="profile-mod">
      <details class="mod-report">
        <summary><svg class="ic sm"><use href="#i-verified"/></svg>Signaler ce membre</summary>
        <form action="{{ route('members.report', $user) }}" method="POST" class="mod-report-form">@csrf
          <select name="reason" required>
            <option value="">Choisir un motif…</option>
            @foreach(\App\Models\Report::REASONS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
          </select>
          <button type="submit" class="btn btn-line">Envoyer</button>
        </form>
      </details>
      <form action="{{ route('members.block', $user) }}" method="POST" onsubmit="return confirm('Bloquer {{ $user->name }} ? Vous ne pourrez plus échanger et ce profil disparaîtra de vos listes.');">@csrf
        <button type="submit" class="mod-block"><svg class="ic sm"><use href="#i-x"/></svg>Bloquer</button>
      </form>
    </div>
    @endif
    @endunless

    <dl class="profile-facts" style="margin-top:24px">
      <div class="row"><dt>Genre</dt><dd>{{ $val($p->gender) }}</dd></div>
      <div class="row"><dt>Recherche</dt><dd>{{ $val($p->seeking) }}</dd></div>
      <div class="row"><dt>Région</dt><dd>{{ $val($p->region) }}</dd></div>
      <div class="row"><dt>Religion</dt><dd>{{ $val($p->religion) }}{{ $p->practice ? ' · '.$p->practice : '' }}</dd></div>
      <div class="row"><dt>Situation</dt><dd>{{ $val($p->marital_status) }}</dd></div>
      <div class="row"><dt>Enfants</dt><dd>{{ is_null($p->children_count) ? '—' : ($p->children_count == 0 ? 'Aucun' : $p->children_count) }}</dd></div>
      <div class="row"><dt>Souhaite des enfants</dt><dd>{{ $val($p->wants_children) }}</dd></div>
      <div class="row"><dt>Type d'union</dt><dd>{{ $val($p->union_type) }}</dd></div>
      <div class="row"><dt>Niveau d'étude</dt><dd>{{ $val($p->education) }}</dd></div>
      <div class="row"><dt>Profession</dt><dd>{{ $val($p->profession) }}</dd></div>
      <div class="row"><dt>Taille</dt><dd>{{ $p->height_cm ? $p->height_cm.' cm' : '—' }}</dd></div>
      <div class="row"><dt>Teint</dt><dd>{{ $val($p->complexion) }}</dd></div>
      <div class="row"><dt>Langues</dt><dd>{{ count($langs) ? implode(', ', $langs) : '—' }}</dd></div>
    </dl>
  </div>
</div>

@if($similaires->count())
<div style="margin-top:50px">
  <div class="sec-head" style="margin-bottom:18px"><h3 style="font-family:var(--font-serif);font-size:1.5rem;font-weight:500">Profils <em style="color:var(--ink)">similaires</em></h3></div>
  <div class="listing">
    @foreach($similaires as $m)
      @include('partials.member-card', ['m' => $m, 'stagger' => $loop->index % 3])
    @endforeach
  </div>
</div>
@endif

@endsection
