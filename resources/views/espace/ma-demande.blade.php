@extends('layouts.member')

@section('title', 'Ma demande — TàakDiàkka')

@section('content')

<div class="m-head">
  <span class="label">Espace membre</span>
  <h2>Ma <em style="color:var(--ink)">demande</em> en mariage</h2>
  <p>Votre demande se compose automatiquement à partir de votre profil. Gérez ici sa visibilité.</p>
</div>

@if(! $profileOk)
  {{-- Profil insuffisant : on ne peut pas activer la demande --}}
  <div class="completion">
    <div class="top"><div>
      <strong>Complétez votre profil pour activer votre demande</strong><br/>
      <small>Au minimum votre genre, pour vous présenter aux bonnes personnes. Profil complété à {{ $completion }}%.</small>
    </div></div>
    <div class="bar" style="height:7px;background:var(--ivory-2);margin:16px 0 4px;position:relative;overflow:hidden">
      <i style="position:absolute;inset:0 auto 0 0;width:{{ max($completion,4) }}%;background:var(--gold)"></i>
    </div>
    <a class="btn btn-primary" href="{{ route('profile.edit') }}" style="margin-top:14px"><svg class="ic sm"><use href="#i-user"/></svg>Compléter mon profil</a>
  </div>

@elseif(! $demande)
  {{-- Profil ok mais demande non encore active --}}
  <div class="empty" style="text-align:center;padding:50px 20px">
    <svg class="ic lg" style="color:var(--gold)"><use href="#i-rings"/></svg>
    <p style="margin:16px 0 0">Votre profil est prêt — activez votre demande pour être visible.</p>
    <form action="{{ route('demandes.publish') }}" method="POST" style="margin-top:18px">@csrf
      <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-rings"/></svg>Activer ma demande de mariage</button>
    </form>
  </div>

@else
  @php
    $pb = $demande->photo ? pathinfo($demande->photo, PATHINFO_FILENAME) : null;
    $statusMeta = [
      'active'    => ['Recherche active', 'var(--gold)'],
      'suspended' => ['En pause', 'var(--muted)'],
      'engaged'   => ['En conversation sérieuse', 'var(--heart)'],
    ][$demande->status] ?? ['Recherche active', 'var(--gold)'];
  @endphp

  <div class="md-panel">
    {{-- Aperçu de la demande --}}
    <div class="md-preview">
      <span class="label">Tel que les membres vous voient</span>
      <div class="md-card">
        @if($pb)
          <img src="{{ asset('img/'.$pb.'.webp') }}" alt="" onerror="this.onerror=null;this.src='{{ asset('img/'.$pb.'.jpg') }}'"/>
        @else
          <span class="md-card-ph">{{ $demande->initial ?: '·' }}</span>
        @endif
        <div class="md-card-info">
          <b>{{ $demande->display_name }}</b>
          <span><svg class="ic sm"><use href="#i-pin"/></svg>{{ $demande->region }}</span>
          <span>{{ $demande->profession ?: 'Profession non renseignée' }}</span>
        </div>
      </div>
      <a href="{{ route('members.show', auth()->id()) }}" class="lnk" style="margin-top:14px;display:inline-flex">Voir ma fiche complète<svg class="ic sm"><use href="#i-arrow"/></svg></a>
    </div>

    {{-- Gestion de l'état --}}
    <div class="md-manage">
      <div class="md-status">
        <span class="md-dot" style="background:{{ $statusMeta[1] }}"></span>
        <div>
          <b>{{ $statusMeta[0] }}</b>
          <small>État actuel de votre demande</small>
        </div>
      </div>

      @if($demande->status === 'engaged')
        <p class="md-note">🤝 Vos contacts et prétendants voient que vous êtes en conversation sérieuse. Vous recevez moins de nouvelles sollicitations.</p>
      @elseif($demande->status === 'suspended')
        <p class="md-note">⏸️ Votre demande est masquée des suggestions et de la recherche. Réactivez-la quand vous le souhaitez.</p>
      @else
        <p class="md-note">✨ Votre demande est visible. {{ $contactsCount }} conversation{{ $contactsCount > 1 ? 's' : '' }} en cours.</p>
      @endif

      <div class="md-actions">
        @if($demande->status !== 'active')
          <form action="{{ route('demandes.status') }}" method="POST">@csrf @method('PUT')
            <input type="hidden" name="status" value="active" />
            <button class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Reprendre ma recherche</button>
          </form>
        @endif
        @if($demande->status !== 'engaged')
          <form action="{{ route('demandes.status') }}" method="POST">@csrf @method('PUT')
            <input type="hidden" name="status" value="engaged" />
            <button class="btn btn-line"><svg class="ic sm heart"><use href="#i-heart"/></svg>Je suis en conversation sérieuse</button>
          </form>
        @endif
        @if($demande->status !== 'suspended')
          <form action="{{ route('demandes.status') }}" method="POST">@csrf @method('PUT')
            <input type="hidden" name="status" value="suspended" />
            <button class="btn btn-line"><svg class="ic sm"><use href="#i-eye"/></svg>Mettre en pause</button>
          </form>
        @endif
      </div>

      <div class="sep" style="margin:22px 0;border-top:1px solid var(--line)"></div>

      <div class="md-actions">
        <a href="{{ route('profile.edit') }}" class="btn btn-line"><svg class="ic sm"><use href="#i-user"/></svg>Modifier (via mon profil)</a>
        <form action="{{ route('demandes.destroy', $demande) }}" method="POST" onsubmit="return confirm('Annuler définitivement votre demande ? Vous n\'apparaîtrez plus dans les recherches.');">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-line" style="color:var(--heart);box-shadow:inset 0 0 0 1px var(--heart)"><svg class="ic sm"><use href="#i-x"/></svg>Annuler ma demande</button>
        </form>
      </div>
    </div>
  </div>
@endif

@endsection
