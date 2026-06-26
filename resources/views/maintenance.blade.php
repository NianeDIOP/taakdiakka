<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Maintenance — {{ $siteName }}</title>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,400&family=Plus+Jakarta+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/taakdiakka.css') }}" />
</head>
<body>
<div style="min-height:100vh;display:grid;place-items:center;text-align:center;padding:40px">
  <div style="max-width:520px">
    <img src="{{ \App\Models\Setting::logo() }}" alt="" width="64" height="64" style="margin-bottom:24px" />
    <h1 style="font-family:var(--font-serif);font-size:clamp(2rem,5vw,3rem);font-weight:500;margin-bottom:14px">Nous revenons très vite 🌙</h1>
    <p style="color:var(--muted);font-size:1.05rem;line-height:1.6">{{ $siteName }} est momentanément en maintenance pour vous offrir une meilleure expérience. Merci de votre patience.</p>
    <a href="{{ route('login') }}" class="btn btn-line" style="margin-top:28px">Espace membre</a>
  </div>
</div>
</body>
</html>
