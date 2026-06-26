@extends('layouts.member')

@section('title', 'Notifications — TàakDiàkka')

@php
  $icon = fn ($t) => match ($t) {
      'friend_request' => 'i-user',
      'friend_accept'  => 'i-check',
      'interest'       => 'i-heart',
      'message'        => 'i-message',
      'community'      => 'i-chat',
      'mention'        => 'i-spark',
      'follow'         => 'i-plus',
      default          => 'i-bell',
  };
  $photo = function ($n) {
      $p = $n->actor?->profile;
      return $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($n->actor?->name);
  };
@endphp

@section('content')

<div class="m-head">
  <span class="label">Activité</span>
  <h2>Vos <em style="color:var(--ink)">notifications</em></h2>
  <p>Tout ce qui se passe autour de votre profil.</p>
</div>

@if($notifications->count())
  <div class="notif-list">
    @foreach($notifications as $n)
      <a href="{{ $n->url ?? '#' }}" class="notif {{ $n->read_at ? '' : 'unread' }}">
        @if($n->actor)
          <span class="av photo notif-av" style="background-image:url('{{ asset('img/'.$photo($n).'.webp') }}')"></span>
        @else
          <span class="notif-ic"><svg class="ic"><use href="#{{ $icon($n->type) }}"/></svg></span>
        @endif
        <span class="notif-body">
          <b>{{ $n->body }}</b>
          <small>{{ $n->created_at?->locale('fr')->diffForHumans() }}</small>
        </span>
        <svg class="ic sm notif-type"><use href="#{{ $icon($n->type) }}"/></svg>
      </a>
    @endforeach
  </div>
@else
  @include('partials.empty', ['icon' => 'bell', 'title' => 'Aucune notification', 'text' => 'Vous serez prévenu(e) ici dès qu\'un membre interagit avec vous. 🤲'])
@endif

@endsection
