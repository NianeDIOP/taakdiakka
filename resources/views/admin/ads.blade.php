@extends('layouts.admin')
@section('title', 'Publicités — Administration')
@section('heading', 'Espace publicitaire')

@section('content')

<div class="adm-head">
  <div>
    <h1>Publicités</h1>
    <p style="color:var(--muted);font-size:.9rem;margin-top:4px">
      Bannières affichées en carrousel sur la page d'accueil · Format <strong>970 × 250 px</strong> · JPG / PNG / WEBP
    </p>
  </div>
</div>

@if(session('status'))
  <div class="flash adm-flash">{{ session('status') }}</div>
@endif

{{-- Statistiques rapides --}}
@php
  $total   = $ads->count();
  $actives = $ads->filter(fn($a) => $a->is_active && !$a->isExpired())->count();
  $revenue = $ads->sum('price');
@endphp
<div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:28px">
  @foreach([
    ['Publicités totales',  $total,   '#i-spark',    'var(--gold)'],
    ['Actives maintenant',  $actives, '#i-check',    '#22c55e'],
    ['CA total (FCFA)',     number_format($revenue, 0, ',', ' '), '#i-coin', 'var(--gold)'],
  ] as [$label, $val, $icon, $color])
  <div style="flex:1;min-width:160px;background:var(--ivory-2);border:1px solid var(--line);border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px">
    <svg class="ic" style="color:{{ $color }};font-size:1.5rem;flex-shrink:0"><use href="{{ $icon }}"/></svg>
    <div>
      <div style="font-size:1.3rem;font-weight:700;color:var(--ink)">{{ $val }}</div>
      <div style="font-size:.78rem;color:var(--muted)">{{ $label }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Ajouter une pub --}}
<section class="adm-card" style="margin-bottom:32px">
  <h3 style="margin-bottom:18px"><svg class="ic"><use href="#i-spark"/></svg>Nouvelle publicité</h3>
  <form method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="adm-form-grid">

      {{-- Image --}}
      <label class="adm-field" style="grid-column:1/-1">
        <span>Image (PNG / JPG / WEBP — 970 × 250 px recommandé, max 4 Mo) *</span>
        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" required />
      </label>

      {{-- Client --}}
      <label class="adm-field">
        <span>Nom du client / annonceur</span>
        <input type="text" name="client_name" placeholder="Ex : Boutique Al Baraka" maxlength="100" />
      </label>

      {{-- Prix --}}
      <label class="adm-field">
        <span>Tarif facturé (FCFA)</span>
        <input type="number" name="price" value="0" min="0" step="500" />
      </label>

      {{-- Durée --}}
      <label class="adm-field">
        <span>Durée (jours)</span>
        <input type="number" name="duration_days" value="30" min="1" max="3650" style="width:120px" />
      </label>

      {{-- Début --}}
      <label class="adm-field">
        <span>Date de début (vide = aujourd'hui)</span>
        <input type="date" name="starts_at" />
      </label>

      {{-- Contact --}}
      <label class="adm-field">
        <span>Numéro de contact de l'annonceur</span>
        <input type="text" name="contact" placeholder="+221771234567" maxlength="40" />
      </label>

      {{-- CTA type --}}
      <label class="adm-field">
        <span>Action du bouton CTA</span>
        <select name="cta_type">
          <option value="whatsapp">WhatsApp</option>
          <option value="call">Appel téléphonique</option>
        </select>
      </label>

      {{-- CTA label --}}
      <label class="adm-field">
        <span>Libellé du bouton</span>
        <input type="text" name="cta_label" value="Nous contacter" maxlength="80" />
      </label>

      {{-- Ordre --}}
      <label class="adm-field">
        <span>Ordre d'affichage</span>
        <input type="number" name="sort_order" value="{{ $ads->count() }}" min="0" style="width:100px" />
      </label>

      {{-- Notes --}}
      <label class="adm-field" style="grid-column:1/-1">
        <span>Notes internes (invisible sur le site)</span>
        <textarea name="notes" rows="2" maxlength="400" placeholder="Ex : payé par virement, renouvellement prévu en août…"></textarea>
      </label>
    </div>

    <div style="display:flex;align-items:center;gap:18px;margin-top:14px;flex-wrap:wrap">
      <label style="display:flex;align-items:center;gap:8px;font-size:.88rem">
        <input type="checkbox" name="is_active" value="1" checked />
        Visible immédiatement
      </label>
      <button type="submit" class="btn btn-primary">
        <svg class="ic sm"><use href="#i-spark"/></svg>Publier la publicité
      </button>
    </div>
  </form>
</section>

{{-- Liste des pubs --}}
<section>
  <h2 class="adm-section-title" style="margin-bottom:16px">
    Toutes les publicités ({{ $total }})
  </h2>

  @forelse($ads as $ad)
  @php
    $expired  = $ad->isExpired();
    $running  = $ad->is_active && !$expired;
    $statusLabel = $expired ? 'Expirée' : ($ad->is_active ? 'Active' : 'Inactive');
    $statusColor = $expired ? '#ef4444' : ($ad->is_active ? '#22c55e' : 'var(--muted)');
  @endphp
  <div class="adm-card" style="margin-bottom:18px;border-left:3px solid {{ $statusColor }}">

    {{-- En-tête de la carte --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap">
      <span style="font-weight:600;font-size:.95rem;flex:1;min-width:160px">
        {{ $ad->client_name ?: 'Annonceur #'.$ad->id }}
      </span>
      <span style="font-size:.78rem;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $statusColor }}22;color:{{ $statusColor }}">
        {{ $statusLabel }}
      </span>
      @if($ad->price > 0)
      <span style="font-size:.82rem;color:var(--gold);font-weight:600">
        {{ number_format($ad->price, 0, ',', ' ') }} FCFA
      </span>
      @endif
      @if($ad->expires_at)
      <span style="font-size:.78rem;color:var(--muted)">
        {{ $ad->starts_at?->format('d/m/Y') ?? '—' }} → {{ $ad->expires_at->format('d/m/Y') }}
        ({{ $ad->duration_days }}j)
      </span>
      @endif
    </div>

    <form method="POST" action="{{ route('admin.ads.update', $ad) }}" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start">

        {{-- Aperçu image --}}
        <div style="flex-shrink:0">
          <img src="{{ asset('img/'.$ad->image) }}" alt="Pub"
               style="width:220px;height:57px;object-fit:cover;border:1px solid var(--line);display:block;border-radius:4px" />
          <label style="font-size:.75rem;color:var(--muted);margin-top:6px;display:block">
            Changer l'image
            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" style="margin-top:4px" />
          </label>
        </div>

        {{-- Champs édition --}}
        <div style="flex:1;min-width:240px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <label class="adm-field">
            <span>Client</span>
            <input type="text" name="client_name" value="{{ $ad->client_name }}" maxlength="100" />
          </label>
          <label class="adm-field">
            <span>Tarif (FCFA)</span>
            <input type="number" name="price" value="{{ $ad->price }}" min="0" step="500" />
          </label>
          <label class="adm-field">
            <span>Durée (jours)</span>
            <input type="number" name="duration_days" value="{{ $ad->duration_days }}" min="1" max="3650" />
          </label>
          <label class="adm-field">
            <span>Date de début</span>
            <input type="date" name="starts_at" value="{{ $ad->starts_at?->format('Y-m-d') }}" />
          </label>
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
          <label class="adm-field" style="grid-column:1/-1">
            <span>Notes internes</span>
            <textarea name="notes" rows="2" maxlength="400">{{ $ad->notes }}</textarea>
          </label>
        </div>

        {{-- Actions --}}
        <div style="display:flex;flex-direction:column;gap:10px;align-items:flex-end;flex-shrink:0">
          <label style="display:flex;align-items:center;gap:6px;font-size:.84rem">
            <input type="checkbox" name="is_active" value="1" @checked($ad->is_active) />
            Active
          </label>
          <button type="submit" class="btn btn-line" style="padding:7px 16px;font-size:.8rem">
            <svg class="ic sm"><use href="#i-check"/></svg>Sauvegarder
          </button>
        </div>
      </div>
    </form>

    @if($ad->notes)
    <div style="margin-top:12px;padding:10px 14px;background:var(--ivory-2);border-radius:8px;font-size:.82rem;color:var(--muted);border-left:3px solid var(--line)">
      <strong>Notes :</strong> {{ $ad->notes }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}"
          style="margin-top:10px;text-align:right"
          onsubmit="return confirm('Supprimer définitivement cette publicité ?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-line"
              style="padding:5px 12px;font-size:.76rem;color:var(--heart);border-color:var(--heart)">
        <svg class="ic sm"><use href="#i-x"/></svg>Supprimer
      </button>
    </form>
  </div>
  @empty
    <div style="text-align:center;padding:48px;color:var(--muted)">
      <svg class="ic" style="font-size:2.5rem;margin-bottom:12px;display:block;margin-inline:auto"><use href="#i-spark"/></svg>
      Aucune publicité pour l'instant. Ajoutez-en une ci-dessus.
    </div>
  @endforelse
</section>

@endsection
