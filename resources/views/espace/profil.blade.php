@extends('layouts.member')

@section('title', 'Mon profil — TàakDiàkka')
@section('pagetitle', 'Mon profil')

@php
  $sel = old('languages', $profile->languages ?? []);
  $opt = fn ($v, $cur) => old($v, $cur) ;
@endphp

@section('content')

<div style="max-width:780px">
  <span class="label">Mon profil</span>
  <h2 style="font-family:var(--font-serif);font-weight:500;font-size:clamp(1.8rem,3.6vw,2.5rem);margin:12px 0 6px">
    Des informations <em style="color:var(--ink)">cohérentes</em> et sérieuses
  </h2>
  <p style="color:var(--muted);margin-bottom:36px">Renseignez vos informations — la plupart en un clic. Profil actuellement complété à
    <strong style="color:var(--ink)">{{ $profile->completion }}%</strong>.</p>

  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- Photo de profil : upload ou caméra + détection visage --}}
    <div class="fgroup">
      <span class="lab">Photo de profil</span>
      <div class="photo-block">
        <div class="photo-preview" id="phPreview">
          @if($profile->photo)
            @php $pb = pathinfo($profile->photo, PATHINFO_FILENAME); @endphp
            <img id="phImg" src="{{ asset('img/'.$pb.'.webp') }}" alt="Ma photo" onerror="this.onerror=null;this.src='{{ asset('img/'.$pb.'.jpg') }}'" />
          @else
            <div class="ph-ic" id="phPlaceholder"><svg class="ic lg"><use href="#i-user"/></svg><span>Aucune photo</span></div>
          @endif
          <video id="phVideo" playsinline style="display:none"></video>
        </div>
        <div class="photo-tools">
          <div class="photo-btns">
            <button type="button" class="btn btn-line" id="phUploadBtn"><svg class="ic sm"><use href="#i-user"/></svg>Téléverser</button>
            <button type="button" class="btn btn-line" id="phCamBtn"><svg class="ic sm"><use href="#i-eye"/></svg>Prendre une photo</button>
          </div>
          <div class="cam-row" id="phCamControls" style="display:none">
            <button type="button" class="btn btn-primary" id="phCapture"><svg class="ic sm"><use href="#i-check"/></svg>Capturer</button>
            <button type="button" class="btn btn-line" id="phCancelCam">Annuler</button>
          </div>
          <input type="file" name="photo" id="phFile" accept="image/jpeg,image/png,image/webp" style="display:none" />
          <input type="hidden" name="photo_data" id="phData" />
          <div class="photo-status" id="phStatus"><span class="filehint">Détection automatique : un visage est requis. Le genre est vérifié pour cohérence.</span></div>
        </div>
      </div>
    </div>

    {{-- Identité --}}
    <div class="form-row">
      <div class="fgroup @error('gender') err @enderror">
        <span class="lab">Genre</span>
        <select name="gender" id="gender">
          <option value="">Choisir…</option>
          @foreach($options['gender'] as $o)<option @selected($opt('gender',$profile->gender)===$o)>{{ $o }}</option>@endforeach
        </select>
        <span class="filehint">« Je cherche » est déduit automatiquement de votre genre.</span>
      </div>
      <div class="fgroup @error('birthdate') err @enderror">
        <span class="lab">Date de naissance @if($profile->age)· {{ $profile->age }} ans @endif</span>
        <input type="date" name="birthdate" value="{{ old('birthdate', optional($profile->birthdate)->format('Y-m-d')) }}" />
        @error('birthdate')<span class="err-msg">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="form-row">
      <div class="fgroup">
        <span class="lab">Région</span>
        <select name="region"><option value="">Choisir…</option>
          @foreach($options['region'] as $o)<option @selected($opt('region',$profile->region)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
      <div class="fgroup">
        <span class="lab">Situation</span>
        <select name="marital_status"><option value="">Choisir…</option>
          @foreach($options['marital_status'] as $o)<option @selected($opt('marital_status',$profile->marital_status)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
    </div>

    {{-- Foi --}}
    <div class="form-row">
      <div class="fgroup">
        <span class="lab">Religion</span>
        <select name="religion"><option value="">Choisir…</option>
          @foreach($options['religion'] as $o)<option @selected($opt('religion',$profile->religion)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
      <div class="fgroup">
        <span class="lab">Pratique</span>
        <select name="practice"><option value="">Choisir…</option>
          @foreach($options['practice'] as $o)<option @selected($opt('practice',$profile->practice)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
    </div>

    {{-- Famille --}}
    <div class="form-row">
      <div class="fgroup @error('children_count') err @enderror">
        <span class="lab">Nombre d'enfants (0 si aucun)</span>
        <input type="number" name="children_count" min="0" max="15" value="{{ old('children_count', $profile->children_count) }}" />
        @error('children_count')<span class="err-msg">{{ $message }}</span>@enderror
      </div>
      <div class="fgroup">
        <span class="lab">Souhaite des enfants</span>
        <select name="wants_children"><option value="">Choisir…</option>
          @foreach($options['wants_children'] as $o)<option @selected($opt('wants_children',$profile->wants_children)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="fgroup">
        <span class="lab">Type d'union souhaité</span>
        <select name="union_type"><option value="">Choisir…</option>
          @foreach($options['union_type'] as $o)<option @selected($opt('union_type',$profile->union_type)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
      <div class="fgroup">
        <span class="lab">Niveau d'étude</span>
        <select name="education"><option value="">Choisir…</option>
          @foreach($options['education'] as $o)<option @selected($opt('education',$profile->education)===$o)>{{ $o }}</option>@endforeach
        </select>
      </div>
    </div>

    {{-- Physique / pro --}}
    <div class="form-row">
      <div class="fgroup @error('profession') err @enderror">
        <span class="lab">Profession</span>
        <input type="text" name="profession" value="{{ old('profession', $profile->profession) }}" placeholder="Ex. Enseignante" />
        @error('profession')<span class="err-msg">{{ $message }}</span>@enderror
      </div>
      <div class="fgroup @error('height_cm') err @enderror">
        <span class="lab">Taille (cm)</span>
        <input type="number" name="height_cm" min="120" max="220" value="{{ old('height_cm', $profile->height_cm) }}" placeholder="Ex. 172" />
        @error('height_cm')<span class="err-msg">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="fgroup">
      <span class="lab">Teint</span>
      <div class="checks">
        @foreach($options['complexion'] as $o)
          <label><input type="radio" name="complexion" value="{{ $o }}" @checked($opt('complexion',$profile->complexion)===$o)> {{ $o }}</label>
        @endforeach
      </div>
    </div>

    <div class="fgroup">
      <span class="lab">Langues parlées</span>
      <div class="checks">
        @foreach($options['languages'] as $o)
          <label><input type="checkbox" name="languages[]" value="{{ $o }}" @checked(collect($sel)->contains($o))> {{ $o }}</label>
        @endforeach
      </div>
    </div>

    {{-- Présentation --}}
    <div class="fgroup">
      <span class="lab">Présentation — choisir un modèle puis personnaliser</span>
      <select id="bioTpl" style="margin-bottom:12px">
        <option value="">— Insérer un modèle de présentation —</option>
        @foreach($templates as $i => $t)<option value="{{ $i }}">Modèle {{ $i + 1 }}</option>@endforeach
      </select>
      <textarea name="bio" id="bioField" maxlength="600" placeholder="Quelques mots sincères sur qui vous êtes et ce que vous recherchez…">{{ old('bio', $profile->bio) }}</textarea>
      @error('bio')<span class="err-msg">{{ $message }}</span>@enderror
    </div>

    <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Enregistrer mon profil</button>
  </form>

  {{-- Galerie de photos supplémentaires --}}
  <div class="gallery-edit">
    <span class="label">Galerie</span>
    <h3 style="font-family:var(--font-serif);font-weight:500;font-size:1.6rem;margin:10px 0 4px">
      Vos <em style="color:var(--ink)">photos</em> supplémentaires
    </h3>
    <p style="color:var(--muted);margin-bottom:20px">
      Ajoutez jusqu'à {{ $maxPhotos }} photos pour donner vie à votre profil.
      {{ $photos->count() }}/{{ $maxPhotos }} utilisées.
    </p>

    <div class="gallery-grid">
      @foreach($photos as $ph)
        <div class="gallery-item">
          <picture>
            <source srcset="{{ asset('img/'.$ph->base.'.webp') }}" type="image/webp" />
            <img src="{{ asset('img/'.$ph->base.'.jpg') }}" alt="Photo de galerie" loading="lazy" />
          </picture>
          <form action="{{ route('gallery.destroy', $ph) }}" method="POST" class="gallery-del">
            @csrf @method('DELETE')
            <button type="submit" title="Retirer cette photo" aria-label="Retirer">
              <svg class="ic sm"><use href="#i-x"/></svg>
            </button>
          </form>
        </div>
      @endforeach

      @if($photos->count() < $maxPhotos)
        <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" class="gallery-add" id="galAdd">
          @csrf
          <input type="file" name="photo" id="galFile" accept="image/jpeg,image/png,image/webp" style="display:none" />
          <button type="button" id="galBtn">
            <svg class="ic lg"><use href="#i-plus"/></svg>
            <span>Ajouter une photo</span>
          </button>
        </form>
      @endif
    </div>
    @error('photo')<span class="err-msg" style="display:block;margin-top:10px">{{ $message }}</span>@enderror
  </div>

</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script defer src="{{ asset('js/profile-photo.js') }}"></script>
<script>
  const tpls = @json($templates);
  const sel = document.getElementById('bioTpl');
  const field = document.getElementById('bioField');
  sel.addEventListener('change', () => {
    const v = sel.value;
    if (v === '') return;
    if (!field.value.trim() || confirm('Remplacer le texte actuel par ce modèle ?')) {
      field.value = tpls[v];
    }
    sel.value = '';
  });

  // Galerie : déclenche le sélecteur de fichier puis soumet automatiquement
  const galBtn = document.getElementById('galBtn');
  if (galBtn) {
    const galFile = document.getElementById('galFile');
    const galAdd = document.getElementById('galAdd');
    galBtn.addEventListener('click', () => galFile.click());
    galFile.addEventListener('change', () => { if (galFile.files.length) galAdd.submit(); });
  }
</script>
@endpush

@endsection
