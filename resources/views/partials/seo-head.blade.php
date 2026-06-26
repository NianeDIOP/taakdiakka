@php
  $gaId    = \App\Models\Setting::get('seo.ga_id');
  $pixelId = \App\Models\Setting::get('seo.pixel_id');
  $keywords = \App\Models\Setting::get('seo.keywords');
  $ogImage = \App\Models\Setting::get('seo.og_image');
  $siteName = \App\Models\Setting::siteName();
  $defTitle = \App\Models\Setting::get('seo.meta_title') ?: 'TàakDiàkka — La rencontre bénie';
  $defDesc  = \App\Models\Setting::get('seo.meta_description') ?: 'TàakDiàkka — Maison matrimoniale. Demandes en mariage vérifiées, compatibilité guidée, communauté bienveillante.';
  $ogImg    = $ogImage ? asset('img/'.$ogImage) : asset('img/communaute-hero.jpg');
  $ld = [
    '@context' => 'https://schema.org',
    '@graph' => [
      ['@type' => 'Organization', 'name' => $siteName, 'url' => url('/'), 'logo' => asset('img/logo.png'), 'description' => $defDesc],
      ['@type' => 'WebSite', 'name' => $siteName, 'url' => url('/'), 'inLanguage' => 'fr'],
    ],
  ];
@endphp
@if($keywords)<meta name="keywords" content="{{ $keywords }}" />@endif
<link rel="canonical" href="{{ url()->current() }}" />
<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:type" content="website" />
<meta property="og:locale" content="fr_FR" />
<meta property="og:title" content="@yield('title', $defTitle)" />
<meta property="og:description" content="@yield('description', $defDesc)" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:image" content="{{ $ogImg }}" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="@yield('title', $defTitle)" />
<meta name="twitter:description" content="@yield('description', $defDesc)" />
<meta name="twitter:image" content="{{ $ogImg }}" />
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@if($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $gaId }}');
</script>
@endif
@if($pixelId)
<script>
  !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
  n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
  document,'script','https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{{ $pixelId }}'); fbq('track', 'PageView');
</script>
@endif
