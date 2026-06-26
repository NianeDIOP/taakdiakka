@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe — TàakDiàkka')

@section('content')
<h1>Nouveau mot de passe</h1>
<p class="sub">Choisissez un nouveau mot de passe (8 caractères minimum).</p>

<form method="POST" action="{{ route('password.update') }}">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}" />

  <div class="auth-field @error('email') err @enderror">
    <label>Adresse e-mail</label>
    <input type="email" name="email" value="{{ old('email', $email) }}" required />
    @error('email')<span class="auth-err">{{ $message }}</span>@enderror
  </div>

  <div class="auth-field @error('password') err @enderror">
    <label>Nouveau mot de passe</label>
    <input type="password" name="password" required />
    @error('password')<span class="auth-err">{{ $message }}</span>@enderror
  </div>

  <div class="auth-field">
    <label>Confirmer le mot de passe</label>
    <input type="password" name="password_confirmation" required />
  </div>

  <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-check"/></svg>Réinitialiser</button>
</form>

<div class="auth-alt"><a href="{{ route('login') }}">Retour à la connexion</a></div>
@endsection
