@extends('layouts.member')

@section('title', 'Bienvenue — TàakDiàkka')

@php
  $steps = [1 => 'Vos informations', 2 => 'Votre photo', 3 => 'Votre demande'];
  $me = $profile;
  $av = $me && $me->photo ? pathinfo($me->photo, PATHINFO_FILENAME) : null;
@endphp

@section('content')
<div class="onb">
  <a href="{{ route('dashboard') }}" class="onb-skip">Plus tard →</a>

  {{-- Progression --}}
  <div class="onb-steps">
    @foreach($steps as $i => $label)
      <div class="onb-step {{ $i === $step ? 'on' : '' }} {{ $i < $step ? 'done' : '' }}">
        <span class="onb-num">@if($i < $step)<svg class="ic sm"><use href="#i-check"/></svg>@else{{ $i }}@endif</span>
        <span class="onb-lbl">{{ $label }}</span>
      </div>
      @if(!$loop->last)<span class="onb-bar {{ $i < $step ? 'done' : '' }}"></span>@endif
    @endforeach
  </div>

  <div class="onb-card">

    {{-- Étape 1 : informations --}}
    @if($step === 1)
      <h2>Faisons connaissance 🌙</h2>
      <p class="onb-intro">Ces quelques informations nous permettent de vous proposer des profils vraiment compatibles.</p>
      <form method="POST" action="{{ route('onboarding.profile') }}">
        @csrf
        <div class="onb-grid">
          <label class="adm-field"><span>Je suis *</span>
            <select name="gender" required>
              <option value="">—</option>
              @foreach($options['gender'] as $g)<option value="{{ $g }}" @selected(old('gender', $me->gender) === $g)>{{ $g }}</option>@endforeach
            </select>
          </label>
          <label class="adm-field"><span>Date de naissance *</span><input type="date" name="birthdate" value="{{ old('birthdate', optional($me->birthdate)->format('Y-m-d')) }}" required /></label>
          <label class="adm-field"><span>Région *</span>
            <select name="region" required>
              <option value="">—</option>
              @foreach($options['region'] as $r)<option value="{{ $r }}" @selected(old('region', $me->region) === $r)>{{ $r }}</option>@endforeach
            </select>
          </label>
          <label class="adm-field"><span>Religion</span>
            <select name="religion">
              <option value="">—</option>
              @foreach($options['religion'] as $r)<option value="{{ $r }}" @selected(old('religion', $me->religion) === $r)>{{ $r }}</option>@endforeach
            </select>
          </label>
          <label class="adm-field"><span>Pratique</span>
            <select name="practice">
              <option value="">—</option>
              @foreach($options['practice'] as $p)<option value="{{ $p }}" @selected(old('practice', $me->practice) === $p)>{{ $p }}</option>@endforeach
            </select>
          </label>
          <label class="adm-field"><span>Profession</span><input type="text" name="profession" value="{{ old('profession', $me->profession) }}" placeholder="Ex. Enseignante" /></label>
          <label class="adm-field" style="grid-column:1/-1"><span>Quelques mots sur vous</span><textarea name="bio" rows="3" maxlength="600" placeholder="Présentez-vous en toute sincérité…">{{ old('bio', $me->bio) }}</textarea></label>
        </div>
        @error('gender')<p class="onb-err">{{ $message }}</p>@enderror
        @error('birthdate')<p class="onb-err">{{ $message }}</p>@enderror
        @error('region')<p class="onb-err">{{ $message }}</p>@enderror
        <button class="btn btn-primary onb-next" type="submit">Continuer<svg class="ic sm"><use href="#i-arrow"/></svg></button>
      </form>

    {{-- Étape 2 : photo --}}
    @elseif($step === 2)
      <h2>Ajoutez une photo 📸</h2>
      <p class="onb-intro">Les profils avec photo reçoivent bien plus de visites. Vous pourrez la modifier à tout moment.</p>
      <form method="POST" action="{{ route('onboarding.photo') }}" enctype="multipart/form-data" id="onbPhotoForm">
        @csrf
        <div class="onb-photo">
          <div class="onb-photo-prev" id="onbPrev" @if($av) style="background-image:url('{{ asset('img/'.$av.'.webp') }}')" @endif>
            @unless($av)<svg class="ic"><use href="#i-user"/></svg>@endunless
          </div>
          <label class="btn btn-line">
            <svg class="ic sm"><use href="#i-eye"/></svg>Choisir une photo
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" id="onbPhoto" style="display:none" />
          </label>
        </div>
        <div class="onb-actions">
          <a href="{{ route('onboarding', ['step' => 3]) }}" class="btn btn-line">Passer</a>
          <button class="btn btn-primary" type="submit">Continuer<svg class="ic sm"><use href="#i-arrow"/></svg></button>
        </div>
      </form>

    {{-- Étape 3 : demande --}}
    @else
      <h2>Tout est prêt ✨</h2>
      <p class="onb-intro">Votre profil est complété à <b style="color:var(--ink)">{{ $me->completion }}%</b>. En terminant, votre demande en mariage est activée et votre profil devient visible auprès des membres compatibles.</p>
      <div class="onb-recap">
        <div class="onb-recap-av">
          @if($av)<span class="av photo" style="background-image:url('{{ asset('img/'.$av.'.webp') }}')"></span>@else<span class="av" data-av="{{ \Illuminate\Support\Str::substr($user->name,0,1) }}"></span>@endif
        </div>
        <div>
          <b>{{ \Illuminate\Support\Str::before($user->name, ' ') }}@if($me->age), {{ $me->age }} ans @endif</b>
          <small>{{ $me->region ?? '—' }} · {{ $me->religion ?? '—' }} · {{ $me->profession ?? '—' }}</small>
        </div>
      </div>
      <div class="onb-actions">
        <a href="{{ route('profile.edit') }}" class="btn btn-line">Compléter davantage</a>
        <form method="POST" action="{{ route('onboarding.finish') }}">@csrf
          <button class="btn btn-primary" type="submit"><svg class="ic sm"><use href="#i-check"/></svg>Activer ma demande &amp; découvrir</button>
        </form>
      </div>
    @endif

  </div>
</div>

@push('scripts')
<script>
  (function () {
    const input = document.getElementById('onbPhoto');
    const prev = document.getElementById('onbPrev');
    if (input && prev) {
      input.addEventListener('change', () => {
        const f = input.files[0]; if (!f) return;
        prev.style.backgroundImage = "url('" + URL.createObjectURL(f) + "')";
        prev.innerHTML = '';
      });
    }
  })();
</script>
@endpush
@endsection
