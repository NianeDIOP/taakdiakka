@extends('layouts.admin')

@section('title', 'Journal — Administration')
@section('heading', "Journal d'activité")

@section('content')

<div class="adm-card">
  <h3><svg class="ic"><use href="#i-bell"/></svg>Actions des administrateurs</h3>

  @forelse($logs as $log)
    <div class="adm-log-row">
      <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($log->admin?->name ?? '?', 0, 1) }}"></span>
      <div class="adm-log-body">
        <b>{{ $log->admin?->name ?? 'Admin supprimé' }}</b>
        <span class="adm-log-action">{{ $log->action }}</span>
        @if($log->details)<span class="adm-log-det">— {{ $log->details }}</span>@endif
        @if($log->target_type)<small>{{ class_basename($log->target_type) }}#{{ $log->target_id }}</small>@endif
      </div>
      <span class="adm-log-time">{{ $log->created_at->locale('fr')->diffForHumans() }}</span>
    </div>
  @empty
    <p class="empty">Aucune action enregistrée pour l'instant.</p>
  @endforelse

  @if($logs->hasPages())
    <nav class="pager" style="margin-top:18px">
      @if($logs->onFirstPage())<span class="pager-btn disabled">Précédent</span>
      @else<a class="pager-btn" href="{{ $logs->previousPageUrl() }}">Précédent</a>@endif
      <span class="pager-info">Page {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>
      @if($logs->hasMorePages())<a class="pager-btn" href="{{ $logs->nextPageUrl() }}">Suivant</a>
      @else<span class="pager-btn disabled">Suivant</span>@endif
    </nav>
  @endif
</div>

@endsection
