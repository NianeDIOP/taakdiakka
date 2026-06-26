@extends('layouts.auth')

@section('title', 'Inscription gratuite — TàakDiàkka')
@section('description', 'Créez votre compte TàakDiàkka gratuitement : publiez votre demande en mariage, découvrez des profils vérifiés et échangez en toute confiance.')

@section('content')
<h1>Créer mon compte</h1>
<p class="sub">Rejoignez la maison matrimoniale — c'est gratuit. ❤</p>

<form method="POST" action="{{ route('register') }}">
  @csrf

  <div class="auth-field @error('name') err @enderror">
    <label>Nom complet</label>
    <input type="text" name="name" value="{{ old('name') }}" autofocus required />
    @error('name')<span class="auth-err">{{ $message }}</span>@enderror
  </div>

  <div class="auth-field @error('email') err @enderror">
    <label>Adresse e-mail</label>
    <input type="email" name="email" value="{{ old('email') }}" required />
    @error('email')<span class="auth-err">{{ $message }}</span>@enderror
  </div>

  <div class="auth-field @error('password') err @enderror">
    <label>Mot de passe</label>
    <input type="password" name="password" required />
    @error('password')<span class="auth-err">{{ $message }}</span>@enderror
  </div>

  <div class="auth-field">
    <label>Confirmer le mot de passe</label>
    <input type="password" name="password_confirmation" required />
  </div>

  <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-heart"/></svg>Créer mon compte</button>
</form>

<div class="auth-alt">Déjà membre ? <a href="{{ route('login') }}">Se connecter</a></div>
@endsection
