@extends('layouts.admin')
@section('title', 'Publicités — Administration')
@section('heading', 'Bannières publicitaires')

@section('content')

<div class="adm-head">
  <h1>Publicités</h1>
  <p style="color:var(--muted);font-size:.9rem;margin-top:4px">
    Format recommandé : <strong>970 × 250 px</strong>. Affichées en carrousel sur la page d'accueil.
  </p>
</div>

@if(session('status'))
  <div class="flash adm-flash">{{ session('status') }}</div>
@endif

{{-- Ajouter une pub --}}
<section class="adm-card" style="margin-bottom:32px">
  <h3 style="margin-bottom:18px"><svg class="ic"><use href="#i-spark"/></svg>Ajouter une publicité</h3>
  <form method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="adm-form-grid">
      <label class="adm-field" style="grid-column:1/-1">
        <span>Image (PNG/JPG/WEBP — 970×250 px recommandé, max 4 Mo) *</span>
        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" required />
      </label>
      <label class="adm-field">
        <span>Numéro de contact</span>
        <input type="text" name="contact" placeholder="+221771234567" maxlength="40" />
      </label>
      <label class="adm-field">
        <span>Action CTA</span>
        <select name="cta_type">
          <option value="whatsapp">WhatsApp</option>
          <option value="call">Appel téléphonique</option>
        </select>
      </label>
      <label class="adm-field">
        <span>Libellé du bouton</span>
        <input type="text" name="cta_label" value="Nous contacter" maxlength="80" />
      </label>
      <label class="adm-field">
        <span>Ordre d'affichage</span>
        <input type="number" name="sort_order" value="{{ $ads->count() }}" min="0" style="width:100px" />
      </label>
    </div>
    <div style="display:flex;align-items:center;gap:18px;margin-top:14px;flex-wrap:wrap">
      <label style="display:flex;align-items:center;gap:8px;font-size:.88rem">
        <input type="checkbox" name="is_active" value="1" checked />
        Visible immédiatement
      </label>
      <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-spark"/></svg>Ajouter</button>
    </div>
  </form>
</section>

{{-- Liste des pubs --}}
<section>
  <h2 class="adm-section-title">Publicités actives ({{ $ads->count() }})</h2>

  @forelse($ads as $ad)
  <div class="adm-card" style="margin-bottom:18px">
    <form method="POST" action="{{ route('admin.ads.update', $ad) }}" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start">
        {{-- Aperçu image --}}
        <div style="flex-shrink:0">
          <img src="{{ asset('img/'.$ad->image) }}" alt="Pub"
               style="width:220px;height:57px;object-fit:cover;border:1px solid var(--line);display:block" />
          <label style="font-size:.75rem;color:var(--muted);margin-top:6px;display:block">
            Changer l'image<br/>
            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" style="margin-top:4px" />
          </label>
        </div>

        {{-- Champs --}}
        <div style="flex:1;min-width:220px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <label class="adm-field">
            <span>Contact</span>
            <input type="text" name="contact" value="{{ $ad->contact }}" maxlength="40" />
          </label>
          <label class="adm-field">
            <span>CTA</span>
            <select name="cta_type">
              <option value="whatsapp" @selected($ad->cta_type === 'whatsapp')>WhatsApp</option>
              <option value="call" @selected($ad->cta_type === 'call')>Appel</option>
            </select>
          </label>
          <label class="adm-field">
            <span>Libellé bouton</span>
            <input type="text" name="cta_label" value="{{ $ad->cta_label }}" maxlength="80" />
          </label>
          <label class="adm-field">
            <span>Ordre</span>
            <input type="number" name="sort_order" value="{{ $ad->sort_order }}" min="0" style="width:80px" />
          </label>
        </div>

        {{-- Actions --}}
        <div style="display:flex;flex-direction:column;gap:10px;align-items:flex-end;flex-shrink:0">
          <label style="display:flex;align-items:center;gap:6px;font-size:.84rem">
            <input type="checkbox" name="is_active" value="1" @checked($ad->is_active) />
            Active
          </label>
          <button type="submit" class="btn btn-line" style="padding:7px 16px;font-size:.8rem">Sauvegarder</button>
        </div>
      </div>
    </form>

    <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}" style="margin-top:10px;text-align:right"
          onsubmit="return confirm('Supprimer cette publicité ?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-line" style="padding:5px 12px;font-size:.76rem;color:var(--heart);border-color:var(--heart)">
        <svg class="ic sm"><use href="#i-x"/></svg>Supprimer
      </button>
    </form>
  </div>
  @empty
    <p class="empty">Aucune publicité pour l'instant. Ajoutez-en une ci-dessus.</p>
  @endforelse
</section>

@endsection
