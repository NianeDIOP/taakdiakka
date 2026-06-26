<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>@yield('title', 'TàakDiàkka')</title>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="apple-touch-icon" href="{{ asset('img/logo-mark.png') }}" />
<meta name="theme-color" content="#f7f3ea" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/taakdiakka.css') }}" />
</head>
<body>

@include('partials.icons')

<main class="auth">
  <a href="{{ route('home') }}" class="lnk auth-back"><svg class="ic sm" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg>Accueil</a>

  <div class="auth-card">
    <a href="{{ route('home') }}" class="auth-logo"><img src="{{ asset('img/logo.png') }}" alt="TàakDiàkka" /></a>
    @yield('content')
  </div>
</main>

</body>
</html>
