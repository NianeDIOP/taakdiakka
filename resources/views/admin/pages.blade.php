@extends('layouts.admin')

@section('title', 'Pages légales — Administration')
@section('heading', 'Pages légales')

@section('content')

<p style="color:var(--muted);font-size:.88rem;margin-bottom:20px;max-width:640px">
  Éditez le contenu des pages légales publiques. La mise en forme utilise le Markdown :
  <code>##</code> pour un titre, <code>###</code> pour un sous-titre, une ligne vide pour un nouveau paragraphe.
</p>

@foreach($pages as $page)
  <div class="adm-card">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px">
      <h3 style="margin:0"><svg class="ic"><use href="#i-rings"/></svg>{{ $page->title }}</h3>
      <a href="{{ route('page', $page->slug) }}" target="_blank" class="adm-btn"><svg class="ic"><use href="#i-eye"/></svg>Voir</a>
    </div>
    <form method="POST" action="{{ route('admin.pages.save', $page) }}">
      @csrf @method('PUT')
      <div class="adm-field" style="margin-bottom:14px">
        <span>Titre</span>
        <input type="text" name="title" value="{{ old('title', $page->title) }}" required maxlength="120" />
      </div>
      <div class="adm-field" style="margin-bottom:14px">
        <span>Contenu (Markdown)</span>
        <textarea name="body" rows="12" style="font-family:var(--font-mono,monospace);font-size:.85rem;line-height:1.6">{{ old('body', $page->body) }}</textarea>
      </div>
      <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-check"/></svg>Enregistrer</button>
    </form>
  </div>
@endforeach

@endsection
