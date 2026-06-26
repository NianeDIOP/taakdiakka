<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>@yield('title', \App\Models\Setting::get('seo.meta_title') ?: 'TàakDiàkka — La rencontre bénie')</title>
<meta name="description" content="@yield('description', \App\Models\Setting::get('seo.meta_description') ?: 'TàakDiàkka — Maison matrimoniale. Demandes en mariage vérifiées, compatibilité guidée, communauté bienveillante.')" />
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="apple-touch-icon" href="{{ asset('img/logo-mark.png') }}" />
<meta name="theme-color" content="#f7f3ea" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/taakdiakka.css') }}" />
@include('partials.seo-head')
@stack('styles')
</head>
<body>

<a href="#main" class="skip-link">Aller au contenu</a>

@include('partials.icons')

<div class="progress" id="progress"></div>

@include('partials.nav')

@if(session('status'))
  <div class="flash" data-flash="success">{{ session('status') }}</div>
@endif

<main id="main">@yield('content')</main>

@include('partials.footer')

@auth @include('partials.online-fab') @endauth

{{-- Barre d'onglets mobile (façon application) — navigation ; « Rejoindre » reste en haut --}}
@php $rIs = fn (...$r) => request()->routeIs(...$r) ? 'on' : ''; @endphp
<nav class="tabbar">
  <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'on' : '' }}"><svg class="ic"><use href="#i-grid"/></svg><span>Accueil</span></a>
  <a href="{{ route('communaute') }}" class="{{ $rIs('communaute','communaute.show') }}"><svg class="ic"><use href="#i-chat"/></svg><span>Communauté</span></a>
  <a href="{{ route('histoires') }}" class="{{ $rIs('histoires') }}"><svg class="ic"><use href="#i-rings"/></svg><span>Histoires</span></a>
  @auth
    <a href="{{ route('members.discover') }}" class="{{ $rIs('members.discover') }}"><svg class="ic"><use href="#i-search"/></svg><span>Membres</span></a>
    <a href="{{ route('dashboard') }}" class="{{ $rIs('dashboard') }}"><svg class="ic"><use href="#i-user"/></svg><span>Mon espace</span></a>
  @else
    <a href="{{ route('tarifs') }}" class="{{ $rIs('tarifs') }}"><svg class="ic"><use href="#i-spark"/></svg><span>Tarifs</span></a>
  @endauth
</nav>

<script src="{{ asset('js/taakdiakka.js') }}"></script>
@stack('scripts')
</body>
</html>
