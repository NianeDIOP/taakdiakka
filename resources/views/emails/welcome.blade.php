@extends('emails._layout')

@section('content')
  <h1 style="font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:500;margin:0 0 14px;color:#1a1712">Bienvenue, {{ $name }} 🌙</h1>
  <p style="font-size:15px;line-height:1.7;color:#3a352c;margin:0 0 16px">
    Nous sommes honorés de vous compter parmi la communauté {{ $siteName }}. Votre chemin vers une union sincère et bénie commence ici.
  </p>
  <p style="font-size:15px;line-height:1.7;color:#3a352c;margin:0 0 24px">
    Pour mettre toutes les chances de votre côté, complétez votre profil et publiez votre demande — les profils complets reçoivent bien plus de visites. 🤲
  </p>
  <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="background:#a06d12">
    <a href="{{ route('dashboard') }}" style="display:inline-block;padding:13px 26px;color:#fff;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:.3px">Accéder à mon espace</a>
  </td></tr></table>
@endsection
