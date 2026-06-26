<footer>
  <div class="foot">
    <div>
      <a href="{{ url('/') }}" class="brand"><img src="{{ \App\Models\Setting::logo() }}" alt="" width="46" height="46" loading="lazy" /><span class="wm">Tàak<b>Diàkka</b></span></a>
      <p>La rencontre bénie, l'union sincère. La maison matrimoniale du Sénégal et de la diaspora.</p>
      <div class="socials">
        <a href="#"><svg class="ic"><use href="#i-heart"/></svg></a>
        <a href="#"><svg class="ic"><use href="#i-chat"/></svg></a>
        <a href="#"><svg class="ic"><use href="#i-send"/></svg></a>
      </div>
    </div>
    <div><h4>Plateforme</h4><ul><li><a href="{{ route('demandes.index') }}">Demandes en mariage</a></li><li><a href="{{ route('home') }}#faq">FAQ</a></li><li><a href="{{ route('tarifs') }}">Abonnements</a></li><li><a href="#">Vérification</a></li></ul></div>
    <div><h4>Communauté</h4><ul><li><a href="{{ route('communaute') }}">Confessions</a></li><li><a href="{{ route('communaute') }}">Conseils &amp; entraide</a></li><li><a href="{{ route('histoires') }}">Histoires de réussite</a></li><li><a href="#">Charte de bienveillance</a></li></ul></div>
    <div><h4>Légal</h4><ul><li><a href="{{ route('page', 'conditions') }}">Conditions d'utilisation</a></li><li><a href="{{ route('page', 'confidentialite') }}">Confidentialité (RGPD)</a></li><li><a href="{{ route('page', 'mentions-legales') }}">Mentions légales</a></li><li><a href="{{ route('communaute') }}">Contact</a></li></ul></div>
  </div>
  <div class="foot-bottom">© {{ date('Y') }} TÀAKDIÀKKA · FAIT AVEC ❤️ POUR LE SÉNÉGAL &amp; LA DIASPORA · MAQUETTE DE DÉMONSTRATION</div>
</footer>
