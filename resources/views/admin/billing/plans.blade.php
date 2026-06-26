@extends('layouts.admin')

@section('title', 'Formules — Administration')
@section('heading', 'Formules & boosts')

@section('content')

<p style="color:var(--muted);font-size:.88rem;margin-bottom:22px">Modifiez les prix, descriptions et avantages de chaque formule. Les changements sont immédiats sur la page Tarifs.</p>

<div class="plan-admin-grid">
  @foreach($plans as $plan)
    <div class="plan-admin {{ $plan->is_premium ? 'is-premium' : '' }} {{ $plan->is_active ? '' : 'is-off' }}">
      <div class="plan-admin-head">
        <div class="plan-admin-name">{{ $plan->name }}<small>{{ $plan->tagline ?: '—' }}</small></div>
        <div class="plan-admin-price">
          @if($plan->compare_at_price)<s>{{ number_format($plan->compare_at_price, 0, ',', ' ') }}</s>@endif
          <b>{{ $plan->price ? number_format($plan->price, 0, ',', ' ') : 'Gratuit' }}</b>@if($plan->price)<span>FCFA{{ $plan->duration_days ? ' / '.$plan->duration_days.'j' : '' }}</span>@endif
        </div>
      </div>
      <div class="plan-admin-flags">
        <span class="adm-tag {{ $plan->is_active ? 'ok' : 'bad' }}">{{ $plan->is_active ? 'Visible' : 'Masquée' }}</span>
        @if($plan->is_premium)<span class="adm-tag gold">Premium</span>@endif
      </div>

      <form method="POST" action="{{ route('admin.billing.plan.update', $plan) }}">
        @csrf @method('PUT')
        <div class="adm-form-grid">
          <label>Nom<input type="text" name="name" value="{{ $plan->name }}" required /></label>
          <label>Accroche<input type="text" name="tagline" value="{{ $plan->tagline }}" /></label>
          <label>Prix (FCFA)<input type="number" name="price" value="{{ $plan->price }}" min="0" required /></label>
          <label>Prix barré<input type="number" name="compare_at_price" value="{{ $plan->compare_at_price }}" min="0" /></label>
          <label>Durée (jours)<input type="number" name="duration_days" value="{{ $plan->duration_days }}" min="1" placeholder="illimité" /></label>
        </div>
        <label class="adm-block-label">Avantages (un par ligne)</label>
        <textarea name="features" rows="5" class="adm-textarea">{{ implode("\n", $plan->features ?? []) }}</textarea>
        <div class="adm-form-checks">
          <label class="adm-inline-check"><input type="checkbox" name="is_premium" value="1" @checked($plan->is_premium) /> Premium (débloque les fonctionnalités payantes)</label>
          <label class="adm-inline-check"><input type="checkbox" name="is_active" value="1" @checked($plan->is_active) /> Visible sur la page Tarifs</label>
        </div>
        <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-check"/></svg>Enregistrer</button>
      </form>
    </div>
  @endforeach
</div>

<h3 class="adm-subhead"><svg class="ic"><use href="#i-spark"/></svg>Boosts de visibilité</h3>
<div class="boost-admin-grid">
  @foreach($boosts as $b)
    <div class="boost-admin {{ $b->is_active ? '' : 'is-off' }}">
      <form method="POST" action="{{ route('admin.billing.boost.update', $b) }}">
        @csrf @method('PUT')
        <div class="boost-admin-head">
          <span class="boost-admin-price">{{ number_format($b->price, 0, ',', ' ') }}<span>FCFA</span></span>
          <span class="adm-tag {{ $b->is_active ? 'ok' : 'bad' }}">{{ $b->is_active ? 'Actif' : 'Inactif' }}</span>
        </div>
        <label class="boost-admin-fld">Nom<input type="text" name="name" value="{{ $b->name }}" required /></label>
        <label class="boost-admin-fld">Durée (jours)<input type="number" name="duration_days" value="{{ $b->duration_days }}" min="1" required /></label>
        <label class="boost-admin-fld">Prix (FCFA)<input type="number" name="price" value="{{ $b->price }}" min="0" required /></label>
        <label class="adm-inline-check"><input type="checkbox" name="is_active" value="1" @checked($b->is_active) /> Actif</label>
        <button class="adm-btn solid" type="submit" style="width:100%;justify-content:center;margin-top:10px"><svg class="ic"><use href="#i-check"/></svg>Enregistrer</button>
      </form>
    </div>
  @endforeach
</div>

@endsection
