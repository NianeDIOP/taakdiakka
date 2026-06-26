@extends('layouts.auth')

@section('title', 'Connexion — TàakDiàkka')
@section('description', 'Connectez-vous à votre espace TàakDiàkka pour retrouver vos demandes, vos messages et vos profils compatibles.')

@section('content')
<h1>Bon retour</h1>
<p class="sub">Connectez-vous pour retrouver la communauté. ❤</p>

@if(session('status'))
  <p style="background:var(--ivory-2);border-left:2px solid var(--gold);padding:12px 14px;font-size:.88rem;color:var(--ink);margin-bottom:24px">{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('login') }}">
  @csrf

  <div class="auth-field @error('email') err @enderror">
    <label>Adresse e-mail</label>
    <input type="email" name="email" value="{{ old('email') }}" autofocus required />
    @error('email')<span class="auth-err">{{ $message }}</span>@enderror
  </div>

  <div class="auth-field">
    <label>Mot de passe</label>
    <input type="password" name="password" required />
  </div>

  <div class="auth-row">
    <label><input type="checkbox" name="remember" /> Se souvenir de moi</label>
    <a href="{{ route('password.request') }}" style="color:var(--gold);font-weight:600">Mot de passe oublié ?</a>
  </div>

  <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-user"/></svg>Se connecter</button>
</form>

<div class="auth-alt">Pas encore de compte ? <a href="{{ route('register') }}">Créer mon compte</a></div>
@endsection
