<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>@yield('code') — TàakDiàkka</title>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet" />
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#f7f3ea;color:#1a1712;
    min-height:100vh;min-height:100dvh;display:grid;place-items:center;padding:40px 22px;text-align:center;-webkit-font-smoothing:antialiased}
  .err-wrap{max-width:520px}
  .err-logo{display:inline-block;margin-bottom:28px}
  .err-logo img{width:60px;height:60px}
  .err-code{font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(5rem,20vw,9rem);line-height:.85;
    font-weight:500;color:#c2972f;letter-spacing:-.02em}
  h1{font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(1.7rem,5vw,2.5rem);font-weight:500;margin:8px 0 16px}
  p{color:#514b41;font-size:1.02rem;line-height:1.7;margin-bottom:32px}
  .err-btn{display:inline-flex;align-items:center;gap:9px;background:#1a1712;color:#f7f3ea;
    padding:14px 30px;font-weight:600;font-size:.9rem;letter-spacing:.02em;text-decoration:none;transition:background .25s,transform .15s}
  .err-btn:hover{background:#c2972f}
  .err-btn:active{transform:translateY(1px)}
  .err-rule{width:46px;height:2px;background:#c2972f;margin:0 auto 26px}
</style>
</head>
<body>
  <div class="err-wrap">
    <a href="{{ url('/') }}" class="err-logo"><img src="{{ asset('img/logo.png') }}" alt="TàakDiàkka" /></a>
    <div class="err-code">@yield('code')</div>
    <div class="err-rule"></div>
    <h1>@yield('title')</h1>
    <p>@yield('message')</p>
    <a href="{{ url('/') }}" class="err-btn">Retour à l'accueil</a>
  </div>
</body>
</html>
