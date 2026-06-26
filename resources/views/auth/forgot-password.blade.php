@extends('layouts.auth')

@section('title', 'Mot de passe oublié — TàakDiàkka')

@section('content')
<h1>Mot de passe oublié</h1>
<p class="sub">Entrez votre e-mail — nous vous enverrons un lien de réinitialisation.</p>

@if(session('status'))
  <p style="background:var(--ivory-2);border-left:2px solid var(--gold);padding:12px 14px;font-size:.88rem;color:var(--ink);margin-bottom:24px">{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('password.email') }}">
  @csrf
  <div class="auth-field @error('email') err @enderror">
    <label>Adresse e-mail</label>
    <input type="email" name="email" value="{{ old('email') }}" autofocus required />
    @error('email')<span class="auth-err">{{ $message }}</span>@enderror
  </div>
  <button type="submit" class="btn btn-primary"><svg class="ic sm"><use href="#i-send"/></svg>Envoyer le lien</button>
</form>

<div class="auth-alt"><a href="{{ route('login') }}">Retour à la connexion</a></div>
@endsection
