@extends('layouts.member')

@section('title', 'Messages — TàakDiàkka')

@php
  $me = auth()->user();
  $photo = function ($u) {
      $p = $u?->profile;
      return $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u?->name);
  };
  $openers = [
      'As-salâm aleykoum 🤲 Votre profil m\'inspire le sérieux, j\'aimerais faire connaissance.',
      'Bonjour, vos valeurs me correspondent. Pouvons-nous échanger en toute simplicité ?',
      'Salâm, je suis intéressé(e) par votre demande. Êtes-vous disponible pour discuter ?',
      'Bonjour, ravi(e) de découvrir votre profil. Qu\'attendez-vous d\'une union ?',
  ];
@endphp

@section('content')

<div class="m-head">
  <span class="label">Espace membre</span>
  <h2>Mes <em style="color:var(--ink)">messages</em></h2>
  <p>Vos échanges avec les profils que vous avez contactés.</p>
</div>

<div class="msg">
  <div class="msg-list">
    @forelse($conversations as $c)
      @php
        $o = $c->other($me);
        $unread = $c->lastMessage && $c->lastMessage->user_id !== $me->id && ! $c->lastMessage->read_at;
      @endphp
      <a href="{{ route('messages.show', $c) }}" class="conv {{ $active && $active->id === $c->id ? 'active' : '' }} {{ $unread ? 'unread' : '' }}" style="text-decoration:none;color:inherit">
        <span class="av s photo" style="background-image:url('{{ asset('img/'.$photo($o).'.webp') }}')"></span>
        <div class="cmeta">
          <div class="nm">{{ $o?->name ?? 'Membre' }} <small>{{ $c->last_message_at?->locale('fr')->diffForHumans(null, true) }}</small></div>
          <div class="snip">{{ \Illuminate\Support\Str::limit($c->lastMessage?->body ?? 'Nouvelle conversation', 38) }}</div>
        </div>
        @if($unread)<span class="conv-dot"></span>@endif
      </a>
    @empty
      <div style="padding:24px;color:var(--muted);font-size:.88rem">Aucune conversation pour l'instant.</div>
    @endforelse
  </div>

  <div class="msg-thread">
    @if($active)
      @php
        $o = $active->other($me);
        $od = $o?->demandes->first();
      @endphp
      <div class="msg-headbar">
        @if($o)
          <a href="{{ route('members.show', $o) }}" class="msg-head-id">
            <span class="msg-head-av">
              <span class="av s photo" style="background-image:url('{{ asset('img/'.$photo($o).'.webp') }}')"></span>
              <span class="pres-dot {{ $o->is_online ? 'on' : 'off' }}" title="{{ $o->is_online ? 'En ligne' : 'Hors ligne' }}"></span>
            </span>
            <span class="msg-head-txt">
              <b>{{ $o->name }}</b>
              <small><span class="pres-state {{ $o->is_online ? 'on' : 'off' }}">{{ $o->is_online ? 'En ligne' : 'Hors ligne' }}</span>@if($o->profile?->region) · {{ $o->profile->region }}@endif@if($od && $od->status==='engaged') · <span style="color:var(--heart)">en conversation sérieuse</span>@endif</small>
            </span>
          </a>
          <a href="{{ route('members.show', $o) }}" class="lnk msg-head-link">Voir le profil<svg class="ic sm"><use href="#i-arrow"/></svg></a>
        @else
          <b>Membre</b>
        @endif
      </div>
      <div class="msg-body" id="msgBody">
        @forelse($messages as $m)
          <div class="bubble {{ $m->user_id === $me->id ? 'me' : 'them' }}">
            {{ $m->body }}
            <small>{{ $m->created_at->format('H:i') }}@if($m->user_id === $me->id)<span class="ticks {{ $m->read_at ? 'read' : '' }}">{{ $m->read_at ? '✓✓' : '✓' }}</span>@endif</small>
          </div>
        @empty
          <div style="margin:auto;text-align:center;color:var(--muted);max-width:440px">
            <p style="margin-bottom:18px">Démarrez la conversation — choisissez un message d'accroche ou écrivez le vôtre. 🤲</p>
            <div class="msg-suggest">
              @foreach($openers as $op)
                <button type="button" class="suggest" onclick="var i=document.getElementById('msgInput');i.value=this.dataset.t;i.focus()" data-t="{{ $op }}">{{ $op }}</button>
              @endforeach
            </div>
          </div>
        @endforelse
      </div>
      @php
        $canMsg    = \App\Support\FeatureGate::canSendMessage($me, $o);
        $msgReason = \App\Support\FeatureGate::messageBlockReason($me, $o);
        $msgCount  = $active->messages()->count();
      @endphp
      @if($canMsg)
        @if($msgCount <= 10)
          <div class="msg-secnote"><svg class="ic sm"><use href="#i-verified"/></svg> Pour votre sécurité, le partage d'un numéro de téléphone est possible après plus de 10 messages échangés.</div>
        @endif
        <form class="msg-input" action="{{ route('messages.store', $active) }}" method="POST">
          @csrf
          <input id="msgInput" type="text" name="body" placeholder="Écrire un message…" autocomplete="off" required />
          <button type="submit" class="btn btn-primary" style="padding:11px 18px"><svg class="ic sm"><use href="#i-send"/></svg></button>
        </form>
      @elseif($msgReason === 'premium')
        <div class="msg-locked">
          <p><svg class="ic"><use href="#i-verified"/></svg> La messagerie est réservée aux membres abonnés.</p>
          <a href="{{ route('tarifs') }}" class="btn btn-primary">Découvrir les formules ✨</a>
        </div>
      @else
        <div class="msg-locked">
          <p><svg class="ic"><use href="#i-user"/></svg> Vous devez être amis acceptés avec ce membre pour pouvoir lui écrire.</p>
          @if($o)<a href="{{ route('members.show', $o) }}" class="btn btn-line">Voir son profil</a>@endif
        </div>
      @endif
    @else
      <div style="margin:auto;text-align:center;color:var(--muted);padding:40px">
        <svg class="ic" style="width:30px;height:30px;stroke:var(--muted);margin-bottom:12px"><use href="#i-chat"/></svg>
        <p>Sélectionnez une conversation,<br/>ou contactez un membre depuis son profil.</p>
        <a href="{{ route('members.discover') }}" class="lnk" style="display:inline-flex;margin-top:14px">Découvrir des membres<svg class="ic sm"><use href="#i-arrow"/></svg></a>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
  // Défile la conversation jusqu'au dernier message
  var b = document.getElementById('msgBody');
  if (b) b.scrollTop = b.scrollHeight;
</script>
@endpush

@endsection
