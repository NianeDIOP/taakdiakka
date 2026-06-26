@extends('layouts.admin')

@section('title', 'Contenu — Administration')
@section('heading', 'Contenu · Témoignages')

@section('content')

<div class="adm-card">
  <h3><svg class="ic"><use href="#i-plus"/></svg>Ajouter un témoignage</h3>
  <form method="POST" action="{{ route('admin.content.story.store') }}">
    @csrf
    @include('admin.partials.story-fields', ['s' => null])
    <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-check"/></svg>Publier le témoignage</button>
  </form>
</div>

<div class="adm-card">
  <h3><svg class="ic"><use href="#i-rings"/></svg>Témoignages publiés <span class="badge">{{ $stories->count() }}</span></h3>

  @forelse($stories as $s)
    <details class="adm-story">
      <summary>
        <b>{{ $s->couple }}</b>
        <small>{{ $s->location ?: '—' }} · {{ $s->badge_label ?: 'sans badge' }}</small>
      </summary>
      <form method="POST" action="{{ route('admin.content.story.update', $s) }}" style="margin-top:14px">
        @csrf @method('PUT')
        @include('admin.partials.story-fields', ['s' => $s])
        <div style="display:flex;gap:10px">
          <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-check"/></svg>Mettre à jour</button>
        </div>
      </form>
      <form method="POST" action="{{ route('admin.content.story.delete', $s) }}" onsubmit="return confirm('Supprimer ce témoignage ?');" style="margin-top:10px">
        @csrf @method('DELETE')
        <button class="adm-btn danger" type="submit"><svg class="ic"><use href="#i-x"/></svg>Supprimer</button>
      </form>
    </details>
  @empty
    <p class="empty">Aucun témoignage pour l'instant.</p>
  @endforelse
</div>

@endsection
