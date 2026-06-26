@extends('layouts.app')

@section('title', 'Inscriptions fermées — ' . \App\Models\Setting::siteName())

@section('content')
<div class="wrap" style="max-width:520px;padding:clamp(70px,11vw,120px) 0;text-align:center">
  <img src="{{ \App\Models\Setting::logo() }}" alt="" width="56" height="56" style="margin-bottom:22px" />
  <h1 style="font-family:var(--font-serif);font-size:clamp(1.9rem,4.5vw,2.6rem);font-weight:500;margin-bottom:14px">Inscriptions momentanément fermées</h1>
  <p style="color:var(--muted);font-size:1.05rem;line-height:1.65;margin-bottom:28px">
    Les nouvelles inscriptions sur {{ \App\Models\Setting::siteName() }} sont temporairement suspendues. Revenez bientôt — vous pourrez alors créer votre compte. 🤲
  </p>
  <a href="{{ route('login') }}" class="btn btn-line">J'ai déjà un compte</a>
  <a href="{{ route('home') }}" class="btn btn-primary">Retour à l'accueil</a>
</div>
@endsection
