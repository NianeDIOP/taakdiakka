@extends('layouts.admin')

@section('title', 'Modération — TàakDiàkka')
@section('heading', 'Modération')

@section('content')

<div class="tabs">
  <a href="{{ route('admin.moderation', ['status' => 'pending']) }}" class="tab {{ $status === 'pending' ? 'active' : '' }}">En attente <span class="badge">{{ $counts['pending'] }}</span></a>
  <a href="{{ route('admin.moderation', ['status' => 'resolved']) }}" class="tab {{ $status === 'resolved' ? 'active' : '' }}">Résolus <span class="badge">{{ $counts['resolved'] }}</span></a>
  <a href="{{ route('admin.moderation', ['status' => 'dismissed']) }}" class="tab {{ $status === 'dismissed' ? 'active' : '' }}">Rejetés <span class="badge">{{ $counts['dismissed'] }}</span></a>
</div>

@forelse($reports as $r)
  @php
    $content = $r->reportable;
    $isUser = $content instanceof \App\Models\User;
    $isComment = $content instanceof \App\Models\Comment;
  @endphp
  <div class="reqline" style="align-items:flex-start">
    <span class="av s" style="background:var(--ivory-2);display:flex;align-items:center;justify-content:center">
      <svg class="ic"><use href="#{{ $isUser ? 'i-user' : ($isComment ? 'i-chat' : 'i-grid') }}"/></svg>
    </span>
    <div class="reqline-id" style="flex:1">
      <b>{{ $isUser ? 'Membre signalé' : ($isComment ? 'Commentaire' : 'Publication') }}</b>
      @if($content && $isUser)
        <p style="margin:6px 0;color:var(--ink);font-size:.92rem">
          <a href="{{ route('members.show', $content) }}" class="lnk">{{ $content->name }}</a>
          @if($content->profile?->region) · {{ $content->profile->region }}@endif
        </p>
      @elseif($content)
        <p style="margin:6px 0;color:var(--ink);font-size:.92rem">{{ \Illuminate\Support\Str::limit($content->body, 220) }}</p>
        <small>Par {{ $content->author_name ?? $content->user?->name ?? 'Membre' }}</small>
      @else
        <p style="margin:6px 0;color:var(--muted)">Contenu supprimé.</p>
      @endif
      <small style="display:block;margin-top:4px">Signalé par <b>{{ $r->reporter?->name ?? 'Membre' }}</b> — {{ $r->reason_label }} — {{ $r->created_at->locale('fr')->diffForHumans() }}</small>
    </div>
    <div class="reqline-act" style="flex-direction:column;gap:8px;align-items:stretch">
      @if($status === 'pending')
        <form action="{{ route('admin.moderation.resolve', $r) }}" method="POST">@csrf @method('PUT')
          <input type="hidden" name="status" value="dismissed" />
          <button class="btn btn-line"><svg class="ic sm"><use href="#i-check"/></svg>Rejeter</button>
        </form>
        @if($content && ! $isUser)
          <form action="{{ route('admin.moderation.delete', $r) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce contenu ?');">@csrf @method('DELETE')
            <button class="btn btn-primary"><svg class="ic sm"><use href="#i-x"/></svg>Supprimer le contenu</button>
          </form>
        @else
          @if($isUser)<a href="{{ route('members.show', $content) }}" class="btn btn-line"><svg class="ic sm"><use href="#i-user"/></svg>Voir le profil</a>@endif
          <form action="{{ route('admin.moderation.resolve', $r) }}" method="POST">@csrf @method('PUT')
            <input type="hidden" name="status" value="resolved" />
            <button class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Marquer résolu</button>
          </form>
        @endif
      @else
        <span class="badge">{{ $r->status_label }}</span>
      @endif
    </div>
  </div>
@empty
  <p class="empty">Aucun signalement {{ $status === 'pending' ? 'en attente' : ($status === 'resolved' ? 'résolu' : 'rejeté') }}. 🤲</p>
@endforelse

@if($reports->hasPages())
  <nav class="pager">
    @if($reports->onFirstPage())
      <span class="pager-btn disabled">Précédent</span>
    @else
      <a class="pager-btn" href="{{ $reports->previousPageUrl() }}" rel="prev">Précédent</a>
    @endif
    <span class="pager-info">Page {{ $reports->currentPage() }} / {{ $reports->lastPage() }}</span>
    @if($reports->hasMorePages())
      <a class="pager-btn" href="{{ $reports->nextPageUrl() }}" rel="next">Suivant</a>
    @else
      <span class="pager-btn disabled">Suivant</span>
    @endif
  </nav>
@endif

@endsection
