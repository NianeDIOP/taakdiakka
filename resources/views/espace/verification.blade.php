@extends('layouts.member')

@section('title', 'Vérification — TàakDiàkka')

@php
  $rank = \App\Models\Profile::VERIF_RANK[$current] ?? 1;
@endphp

@section('content')

<div class="m-head">
  <span class="label">Confiance</span>
  <h2>Ma <em style="color:var(--ink)">vérification</em></h2>
  <p>Augmentez votre niveau de confiance : un profil vérifié inspire le sérieux et reçoit plus de demandes.</p>
</div>

<div class="verif-grid">
  @foreach(['Bronze' => 'E-mail et téléphone confirmés.', 'Argent' => "Pièce d'identité vérifiée.", 'Or' => 'Identité + selfie de correspondance.'] as $lvl => $desc)
    @php $lrank = \App\Models\Profile::VERIF_RANK[$lvl]; @endphp
    <div class="verif {{ $current === $lvl ? 'cur' : '' }} {{ $lrank < $rank ? 'passed' : '' }}">
      <span class="tag">
        @if($current === $lvl) Niveau actuel
        @elseif($lrank < $rank) Acquis
        @else Niveau @endif
      </span>
      <div class="lvl">{{ $lvl }} @if($lrank <= $rank)<svg class="ic sm" style="color:var(--gold)"><use href="#i-verified"/></svg>@endif</div>
      <p>{{ $desc }}</p>
    </div>
  @endforeach
</div>

<h3 style="font-family:var(--font-serif);font-size:1.4rem;margin-bottom:8px">Étapes de vérification</h3>
<ul class="steps-list">
  {{-- E-mail (auto) --}}
  <li class="done">
    <span class="st-ic"><svg class="ic sm"><use href="#i-check"/></svg></span>
    Adresse e-mail confirmée
    <span class="act"><small class="step-done">Fait</small></span>
  </li>

  {{-- Téléphone (Bronze) --}}
  <li class="{{ $phoneOk ? 'done' : '' }}">
    <span class="st-ic"><svg class="ic sm"><use href="#i-{{ $phoneOk ? 'check' : 'message' }}"/></svg></span>
    @if($phoneOk)
      Numéro de téléphone confirmé <small style="color:var(--muted)">({{ $phone }})</small>
      <span class="act"><small class="step-done">Fait</small></span>
    @else
      <form action="{{ route('verification.submit') }}" method="POST" class="step-form">
        @csrf <input type="hidden" name="step" value="phone" />
        <span>Vérifier mon numéro de téléphone</span>
        <span class="act">
          <input type="tel" name="phone" placeholder="+221 7…" required />
          <button type="submit" class="btn btn-line">Confirmer</button>
        </span>
      </form>
    @endif
  </li>

  {{-- Pièce d'identité (Argent) --}}
  <li class="{{ $rank >= 2 ? 'done' : '' }}">
    <span class="st-ic"><svg class="ic sm"><use href="#i-{{ $rank >= 2 ? 'check' : 'verified' }}"/></svg></span>
    @if($rank >= 2)
      Pièce d'identité vérifiée <span style="color:var(--gold)">(Argent)</span>
      <span class="act"><small class="step-done">Fait</small></span>
    @else
      <form action="{{ route('verification.submit') }}" method="POST" enctype="multipart/form-data" class="step-form">
        @csrf <input type="hidden" name="step" value="Argent" />
        <span>Téléverser une pièce d'identité <span style="color:var(--gold)">(Argent)</span></span>
        <span class="act">
          <input type="file" name="document" accept="image/*" required />
          <button type="submit" class="btn btn-line">Soumettre</button>
        </span>
      </form>
    @endif
  </li>

  {{-- Selfie (Or) --}}
  <li class="{{ $rank >= 3 ? 'done' : '' }} {{ $rank < 2 ? 'locked' : '' }}">
    <span class="st-ic"><svg class="ic sm"><use href="#i-{{ $rank >= 3 ? 'check' : 'eye' }}"/></svg></span>
    @if($rank >= 3)
      Selfie de correspondance vérifié <span style="color:var(--gold)">(Or)</span>
      <span class="act"><small class="step-done">Fait</small></span>
    @elseif($rank < 2)
      Selfie de correspondance <span style="color:var(--gold)">(Or)</span>
      <span class="act"><small style="color:var(--muted)">🔒 Après l'Argent</small></span>
    @else
      <form action="{{ route('verification.submit') }}" method="POST" enctype="multipart/form-data" class="step-form">
        @csrf <input type="hidden" name="step" value="Or" />
        <span>Selfie de correspondance <span style="color:var(--gold)">(Or)</span></span>
        <span class="act">
          <input type="file" name="document" accept="image/*" required />
          <button type="submit" class="btn btn-line">Soumettre</button>
        </span>
      </form>
    @endif
  </li>
</ul>

@error('phone')<p class="err-msg">{{ $message }}</p>@enderror
@error('document')<p class="err-msg">{{ $message }}</p>@enderror

<p style="color:var(--muted);font-size:.84rem;margin-top:24px">🔒 Vos documents sont confidentiels et utilisés uniquement pour la vérification.</p>

@endsection
