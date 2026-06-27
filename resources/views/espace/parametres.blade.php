@extends('layouts.member')

@section('title', 'Paramètres — TàakDiàkka')

@section('content')

<div class="m-head">
  <span class="label">Mon compte</span>
  <h2><em style="color:var(--ink)">Paramètres</em></h2>
  <p>Gérez votre profil, vos informations de connexion, votre confidentialité et votre sécurité.</p>
</div>

@include('partials.plan-status')

{{-- Mon profil --}}
<div class="settings-card" style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap">
  <div>
    <h3 style="margin-bottom:4px">Mon profil</h3>
    <p class="desc" style="margin:0">Vos informations personnelles, photo et présentation — base de votre demande.</p>
  </div>
  <div class="hero-cta" style="gap:12px">
    <a href="{{ route('profile.show') }}" class="btn btn-line"><svg class="ic sm"><use href="#i-user"/></svg>Voir</a>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-user"/></svg>Modifier mon profil</a>
  </div>
</div>

{{-- Compte --}}
<div class="settings-card">
  <h3>Informations du compte</h3>
  <p class="desc">Votre nom et votre adresse e-mail de connexion.</p>
  <form method="POST" action="{{ route('settings.account') }}">
    @csrf @method('PUT')
    <div class="fgroup @error('name') err @enderror">
      <span class="lab">Nom complet</span>
      <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required />
      @error('name')<span class="err-msg">{{ $message }}</span>@enderror
    </div>
    <div class="fgroup @error('email') err @enderror">
      <span class="lab">Adresse e-mail</span>
      <input type="text" name="email" value="{{ old('email', auth()->user()->email) }}" required />
      @error('email')<span class="err-msg">{{ $message }}</span>@enderror
    </div>
    <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Enregistrer</button>
  </form>
</div>

{{-- Mot de passe --}}
<div class="settings-card">
  <h3>Mot de passe</h3>
  <p class="desc">Choisissez un mot de passe solide (8 caractères minimum).</p>
  <form method="POST" action="{{ route('settings.password') }}">
    @csrf @method('PUT')
    <div class="fgroup @error('current_password') err @enderror">
      <span class="lab">Mot de passe actuel</span>
      <input type="password" name="current_password" required />
      @error('current_password')<span class="err-msg">{{ $message }}</span>@enderror
    </div>
    <div class="form-row">
      <div class="fgroup @error('password') err @enderror">
        <span class="lab">Nouveau mot de passe</span>
        <input type="password" name="password" required />
        @error('password')<span class="err-msg">{{ $message }}</span>@enderror
      </div>
      <div class="fgroup">
        <span class="lab">Confirmer</span>
        <input type="password" name="password_confirmation" required />
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Modifier le mot de passe</button>
  </form>
</div>

{{-- Notifications e-mail --}}
<div class="settings-card">
  <h3>Notifications par e-mail</h3>
  <p class="desc">Choisissez si vous souhaitez recevoir des e-mails (demandes d'ami, messages, vérification…).</p>
  <form method="POST" action="{{ route('settings.notifications') }}">
    @csrf @method('PUT')
    <label class="switch-row">
      <input type="checkbox" name="email_opt_in" value="1" @checked(auth()->user()->email_opt_in) onchange="this.form.submit()" />
      <span><b>Recevoir les e-mails</b><small>Désactivé, vous ne recevrez plus aucune notification par e-mail.</small></span>
    </label>
  </form>
</div>

{{-- Confidentialité --}}
<div class="settings-card">
  <h3>Confidentialité</h3>
  <p class="desc">Contrôlez la visibilité de votre photo auprès des autres membres.</p>
  @if($demande)
    <form method="POST" action="{{ route('settings.privacy') }}">
      @csrf @method('PUT')
      <label class="switch-row">
        <input type="checkbox" name="is_discret" value="1" @checked($demande->is_discret) onchange="this.form.submit()" />
        <span><b>Profil discret</b><small>Votre photo est masquée ; les membres voient « Photo sur demande ».</small></span>
      </label>
    </form>
    <p class="desc" style="margin-top:14px">Pour mettre votre demande en pause ou la marquer « en conversation sérieuse », rendez-vous sur <a href="{{ route('demandes.mine') }}" class="lnk">Ma demande</a>.</p>
  @else
    <p class="desc">Activez d'abord votre demande depuis <a href="{{ route('demandes.mine') }}" class="lnk">Ma demande</a> pour gérer sa confidentialité.</p>
  @endif
</div>

{{-- Zone de danger --}}
<div class="settings-card danger">
  <h3>Supprimer mon compte</h3>
  <p class="desc">Action définitive : votre profil, votre demande, vos messages et vos relations seront effacés.</p>
  <form method="POST" action="{{ route('settings.destroy') }}" onsubmit="return confirm('Supprimer définitivement votre compte ? Cette action est irréversible.');">
    @csrf @method('DELETE')
    <div class="fgroup @error('current_password') err @enderror" style="max-width:320px">
      <span class="lab">Confirmez avec votre mot de passe</span>
      <input type="password" name="current_password" required />
      @error('current_password')<span class="err-msg">{{ $message }}</span>@enderror
    </div>
    <button type="submit" class="btn btn-line" style="color:var(--heart);box-shadow:inset 0 0 0 1px var(--heart)"><svg class="ic sm"><use href="#i-x"/></svg>Supprimer définitivement mon compte</button>
  </form>
</div>

@endsection
