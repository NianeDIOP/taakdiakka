@extends('layouts.member')

@section('title', 'Mes demandes — TàakDiàkka')

@php
  $photo = function ($u) {
      $p = $u->profile;
      return $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($u->name);
  };
@endphp

@section('content')

<div class="m-head">
  <span class="label">Mise en relation</span>
  <h2>Mes <em style="color:var(--ink)">demandes</em></h2>
  <p>Vos demandes d'ami reçues, envoyées, vos amis et vos conversations.</p>
</div>

<div class="tabs" id="hubTabs">
  <button class="tab active" data-tab="recus">Reçues <span class="badge">{{ $received->count() }}</span></button>
  <button class="tab" data-tab="envoyes">Envoyées <span class="badge">{{ $sent->count() }}</span></button>
  <button class="tab" data-tab="amis">Amis <span class="badge">{{ $friends->count() }}</span></button>
  <button class="tab" data-tab="messages">Messages <span class="badge">{{ $conversations->count() }}</span></button>
</div>

{{-- Reçues --}}
<div class="tab-panel active" data-panel="recus">
  @forelse($received as $r)
    @php $u = $r->sender; @endphp
    <div class="reqline">
      <a href="{{ route('members.show', $u) }}" class="av photo" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
      <div class="reqline-id">
        <a href="{{ route('members.show', $u) }}"><b>{{ $u->name }}</b></a>
        <small>{{ $u->profile?->profession ?? '—' }} · {{ $u->profile?->region ?? '—' }}</small>
      </div>
      <div class="reqline-act">
        <form action="{{ route('friends.accept', $r) }}" method="POST">@csrf
          <button class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Accepter</button>
        </form>
        <form action="{{ route('friends.decline', $r) }}" method="POST">@csrf
          <button class="btn btn-line"><svg class="ic sm"><use href="#i-x"/></svg>Refuser</button>
        </form>
      </div>
    </div>
  @empty
    @include('partials.empty', ['icon' => 'user', 'title' => 'Aucune demande reçue', 'text' => 'Personne ne vous a encore écrit. Complétez votre profil pour inspirer confiance. 🤲', 'ctaUrl' => route('members.discover'), 'ctaLabel' => 'Découvrir des membres'])
  @endforelse
</div>

{{-- Envoyées --}}
<div class="tab-panel" data-panel="envoyes">
  @forelse($sent as $r)
    @php $u = $r->receiver; @endphp
    <div class="reqline">
      <a href="{{ route('members.show', $u) }}" class="av photo" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
      <div class="reqline-id">
        <a href="{{ route('members.show', $u) }}"><b>{{ $u->name }}</b></a>
        <small>En attente de réponse</small>
      </div>
      <div class="reqline-act">
        <form action="{{ route('friends.cancel', $r) }}" method="POST">@csrf @method('DELETE')
          <button class="btn btn-line"><svg class="ic sm"><use href="#i-x"/></svg>Annuler</button>
        </form>
      </div>
    </div>
  @empty
    @include('partials.empty', ['icon' => 'arrow', 'title' => 'Aucune demande envoyée', 'text' => "Vous n'avez pas encore envoyé de demande. Parcourez les profils et faites le premier pas.", 'ctaUrl' => route('members.discover'), 'ctaLabel' => 'Découvrir des membres'])
  @endforelse
</div>

{{-- Amis --}}
<div class="tab-panel" data-panel="amis">
  @forelse($friends as $u)
    <div class="reqline">
      <a href="{{ route('members.show', $u) }}" class="av photo" style="background-image:url('{{ asset('img/'.$photo($u).'.webp') }}')"></a>
      <div class="reqline-id">
        <a href="{{ route('members.show', $u) }}"><b>{{ $u->name }}</b></a>
        <small>{{ $u->profile?->profession ?? '—' }} · {{ $u->profile?->region ?? '—' }}</small>
      </div>
      <div class="reqline-act">
        <form action="{{ route('messages.start', $u) }}" method="POST">@csrf
          <button class="btn btn-primary"><svg class="ic sm"><use href="#i-message"/></svg>Discuter</button>
        </form>
      </div>
    </div>
  @empty
    @include('partials.empty', ['icon' => 'heart', 'title' => 'Pas encore d\'amis', 'text' => 'Envoyez une demande depuis un profil pour créer votre premier lien. ✨', 'ctaUrl' => route('members.discover'), 'ctaLabel' => 'Découvrir des membres'])
  @endforelse
</div>

{{-- Messages --}}
<div class="tab-panel" data-panel="messages">
  @forelse($conversations as $c)
    @php $other = $c->users->firstWhere('id', '!=', auth()->id()); @endphp
    @if($other)
      <a href="{{ route('messages.show', $c) }}" class="reqline reqline-link">
        <span class="av photo" style="background-image:url('{{ asset('img/'.$photo($other).'.webp') }}')"></span>
        <div class="reqline-id">
          <b>{{ $other->name }}</b>
          <small>{{ \Illuminate\Support\Str::limit($c->lastMessage?->body ?? 'Démarrez la conversation…', 60) }}</small>
        </div>
        <span class="reqline-act"><svg class="ic sm"><use href="#i-arrow"/></svg></span>
      </a>
    @endif
  @empty
    @include('partials.empty', ['icon' => 'message', 'title' => 'Aucune conversation', 'text' => 'Vos échanges apparaîtront ici. Trouvez quelqu\'un à qui écrire pour commencer.', 'ctaUrl' => route('members.discover'), 'ctaLabel' => 'Trouver un profil'])
  @endforelse
</div>

@push('scripts')
<script>
  document.querySelectorAll('#hubTabs .tab').forEach(t => {
    t.addEventListener('click', () => {
      document.querySelectorAll('#hubTabs .tab').forEach(x => x.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(x => x.classList.remove('active'));
      t.classList.add('active');
      document.querySelector('.tab-panel[data-panel="' + t.dataset.tab + '"]').classList.add('active');
    });
  });
</script>
@endpush

@endsection
