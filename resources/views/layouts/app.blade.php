<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>@yield('title', 'TàakDiàkka — La rencontre bénie')</title>
<meta name="description" content="@yield('description', 'TàakDiàkka — Maison matrimoniale. Demandes en mariage vérifiées, compatibilité guidée, communauté bienveillante.')" />
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/taakdiakka.css') }}" />
<link rel="preload" as="image" href="{{ asset('img/hero-couple.webp') }}" type="image/webp" fetchpriority="high" />
@stack('styles')
</head>
<body>

@include('partials.icons')

<div class="progress" id="progress"></div>

@include('partials.nav')

@yield('content')

@include('partials.footer')

<script src="{{ asset('js/taakdiakka.js') }}"></script>
@stack('scripts')
</body>
</html>
