@php $siteName = $siteName ?? \App\Models\Setting::siteName(); @endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width,initial-scale=1" /></head>
<body style="margin:0;padding:0;background:#efe9db;font-family:'Segoe UI',Helvetica,Arial,sans-serif;color:#1a1712">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#efe9db;padding:32px 0">
    <tr><td align="center">
      <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#fcfaf4;border:1px solid rgba(26,23,18,.12)">
        {{-- En-tête --}}
        <tr><td style="background:#1a1712;padding:26px 32px;text-align:center">
          <img src="{{ \App\Models\Setting::logo() }}" alt="" width="44" height="44" style="display:inline-block;vertical-align:middle" />
          <span style="color:#fcfaf4;font-size:22px;font-weight:600;letter-spacing:.5px;vertical-align:middle;margin-left:10px">{{ $siteName }}</span>
        </td></tr>
        {{-- Corps --}}
        <tr><td style="padding:34px 36px">
          @yield('content')
        </td></tr>
        {{-- Pied --}}
        <tr><td style="padding:20px 36px;border-top:1px solid rgba(26,23,18,.1);color:#6f695c;font-size:12px;line-height:1.6">
          {{ $siteName }} — La maison matrimoniale du Sénégal et de la diaspora.<br />
          Vous recevez cet e-mail car vous êtes inscrit(e) sur {{ $siteName }}.
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
