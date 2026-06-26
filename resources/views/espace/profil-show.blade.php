@extends('layouts.member')

@section('title', 'Mon profil — TàakDiàkka')
@section('pagetitle', 'Mon profil')

@php
  $langs = $profile->languages ?? [];
  $val = fn ($v) => filled($v) ? e($v) : '—';
@endphp

@section('content')

<div style="max-width:820px">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;margin-bottom:30px">
    <div>
      <span class="label">Mon profil</span>
      <h2 style="font-family:var(--font-serif);font-weight:500;font-size:clamp(1.8rem,3.6vw,2.5rem);margin:12px 0 4px">
        {{ auth()->user()->name }}@if($profile->age), {{ $profile->age }} ans @endif
      </h2>
      <p style="color:var(--muted)">Complété à <strong style="color:var(--ink)">{{ $profile->completion }}%</strong></p>
    </div>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-user"/></svg>Modifier mon profil</a>
  </div>

  @if($profile->completion === 0)
    <div class="completion">
      <div class="top"><div><strong>Votre profil est vide</strong><br/><small>Renseignez vos informations pour inspirer confiance.</small></div></div>
      <a class="lnk" href="{{ route('profile.edit') }}" style="display:inline-flex;margin-top:12px">Compléter mon profil<svg class="ic sm"><use href="#i-arrow"/></svg></a>
    </div>
  @else
    <div class="profile profile--show" style="align-items:start">
      <div class="profile-photo" style="aspect-ratio:4/5">
        @if($profile->photo)
          @php $pb = pathinfo($profile->photo, PATHINFO_FILENAME); @endphp
          <img src="{{ asset('img/'.$pb.'.webp') }}" alt="Ma photo" onerror="this.onerror=null;this.src='{{ asset('img/'.$pb.'.jpg') }}'" />
        @else
          <div class="discret-ic" style="position:static;transform:none"><svg class="ic lg"><use href="#i-user"/></svg><span>Aucune photo</span></div>
        @endif
      </div>

      <div>
        @if($profile->bio)
          <p class="profile-quote" style="font-size:1.3rem;margin-bottom:26px">« {{ $profile->bio }} »</p>
        @endif

        <dl class="profile-facts" style="margin-bottom:28px">
          <div class="row"><dt>Genre</dt><dd>{{ $val($profile->gender) }}</dd></div>
          <div class="row"><dt>Recherche</dt><dd>{{ $val($profile->seeking) }}</dd></div>
          <div class="row"><dt>Région</dt><dd>{{ $val($profile->region) }}</dd></div>
          <div class="row"><dt>Religion</dt><dd>{{ $val($profile->religion) }}{{ $profile->practice ? ' · '.$profile->practice : '' }}</dd></div>
          <div class="row"><dt>Situation</dt><dd>{{ $val($profile->marital_status) }}</dd></div>
          <div class="row"><dt>Enfants</dt><dd>{{ is_null($profile->children_count) ? '—' : ($profile->children_count == 0 ? 'Aucun' : $profile->children_count) }}</dd></div>
          <div class="row"><dt>Souhaite des enfants</dt><dd>{{ $val($profile->wants_children) }}</dd></div>
          <div class="row"><dt>Type d'union</dt><dd>{{ $val($profile->union_type) }}</dd></div>
          <div class="row"><dt>Niveau d'étude</dt><dd>{{ $val($profile->education) }}</dd></div>
          <div class="row"><dt>Profession</dt><dd>{{ $val($profile->profession) }}</dd></div>
          <div class="row"><dt>Taille</dt><dd>{{ $profile->height_cm ? $profile->height_cm.' cm' : '—' }}</dd></div>
          <div class="row"><dt>Teint</dt><dd>{{ $val($profile->complexion) }}</dd></div>
          <div class="row"><dt>Langues</dt><dd>{{ count($langs) ? implode(', ', $langs) : '—' }}</dd></div>
        </dl>

        <div class="hero-cta">
          <a href="{{ route('profile.edit') }}" class="btn btn-line">Modifier</a>
          <a href="{{ route('demandes.mine') }}" class="lnk">Publier ma demande en mariage</a>
        </div>
      </div>
    </div>

    @php $myPhotos = auth()->user()->photos; @endphp
    @if($myPhotos->count())
      <div style="margin-top:34px">
        <span class="label">Galerie</span>
        <div class="gallery-grid" style="margin-top:14px">
          @foreach($myPhotos as $ph)
            <div class="gallery-item">
              <picture>
                <source srcset="{{ asset('img/'.$ph->base.'.webp') }}" type="image/webp" />
                <img src="{{ asset('img/'.$ph->base.'.jpg') }}" alt="Photo de galerie" loading="lazy" />
              </picture>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  @endif
</div>

@endsection
