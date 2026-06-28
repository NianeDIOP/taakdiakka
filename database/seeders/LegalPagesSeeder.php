<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        Page::where('slug', 'conditions')->update(['body' => self::CGU]);
        Page::where('slug', 'confidentialite')->update(['body' => self::PRIVACY]);
        Page::where('slug', 'mentions-legales')->update(['body' => self::MENTIONS]);
    }

    private const CGU = <<<'MD'
## Conditions Générales d'Utilisation de TàakDiàkka

*Dernière mise à jour : 28 juin 2026*

### Article 1 — Objet
Les présentes Conditions Générales d'Utilisation (CGU) régissent l'accès et l'utilisation de la plateforme TàakDiàkka, service de rencontres matrimoniales en ligne édité par SamaSokhla, accessible à l'adresse taakdiakka.samasokhla.com.

### Article 2 — Accès au service
L'inscription est réservée aux personnes physiques âgées d'au moins **18 ans**. L'utilisateur s'engage à fournir des informations exactes, complètes et à jour lors de son inscription et sur son profil. Toute usurpation d'identité est interdite et pourra faire l'objet de poursuites conformément à la législation sénégalaise.

### Article 3 — Inscription et compte
- L'inscription nécessite un nom, une adresse e-mail valide et un mot de passe.
- Chaque personne ne peut créer qu'un seul compte.
- L'utilisateur est responsable de la confidentialité de ses identifiants.
- L'administration se réserve le droit de suspendre ou supprimer tout compte en cas de manquement aux présentes CGU.

### Article 4 — Fonctionnalités et abonnements
TàakDiàkka propose une formule gratuite (Découverte) avec des fonctionnalités de base, et des formules payantes (Premium) offrant un accès étendu. Les tarifs, avantages et durées sont détaillés sur la page Abonnements du site. Le paiement s'effectue via les moyens de paiement proposés (Wave, Orange Money, Free Money, carte bancaire) à travers la plateforme sécurisée PayDunya.

### Article 5 — Comportement attendu
Les membres s'engagent à :
- Interagir avec **respect, bienveillance et honnêteté** ;
- Ne publier aucun contenu à caractère diffamatoire, haineux, discriminatoire, obscène ou illicite ;
- Ne pas harceler, menacer ou intimider d'autres membres ;
- Ne pas partager de coordonnées personnelles (téléphone, e-mail, réseaux sociaux) dans les messages avant d'avoir noué une relation de confiance ;
- Ne pas utiliser la plateforme à des fins commerciales, publicitaires ou frauduleuses.

### Article 6 — Modération
L'équipe de modération de TàakDiàkka se réserve le droit de :
- Supprimer tout contenu jugé inapproprié ou contraire aux CGU ;
- Suspendre temporairement un compte en cas de comportement signalé et vérifié ;
- Bannir définitivement un compte en cas de manquements graves ou répétés.

Les membres peuvent signaler tout contenu ou comportement inapproprié via la fonction de signalement. Chaque signalement est examiné avec diligence.

### Article 7 — Propriété intellectuelle
L'ensemble des contenus de la plateforme (textes, visuels, logo, design) est protégé par le droit de la propriété intellectuelle. Toute reproduction, même partielle, sans autorisation écrite préalable est interdite.

Les contenus publiés par les utilisateurs (textes, photos) restent leur propriété. En les publiant sur TàakDiàkka, ils accordent une licence d'utilisation non exclusive pour l'affichage sur la plateforme.

### Article 8 — Responsabilité
TàakDiàkka facilite la mise en relation entre personnes en quête de mariage, mais **ne garantit pas** l'issue des échanges. La plateforme décline toute responsabilité quant aux suites données aux contacts noués entre membres, et invite à la prudence et au discernement dans toute démarche.

### Article 9 — Résiliation
L'utilisateur peut à tout moment supprimer son compte depuis les paramètres de son profil. La suppression entraîne l'effacement définitif des données personnelles associées, conformément à la politique de confidentialité.

### Article 10 — Droit applicable
Les présentes CGU sont régies par le droit sénégalais. En cas de litige, les parties s'engagent à rechercher une solution amiable avant toute action judiciaire. À défaut, les tribunaux de Dakar seront compétents.

### Contact
Pour toute question relative aux présentes CGU : contact@taakdiakka.com
MD;

    private const PRIVACY = <<<'MD'
## Politique de Confidentialité et de Protection des Données Personnelles

*Dernière mise à jour : 28 juin 2026*

La présente politique est établie conformément à la **Loi n°2008-12 du 25 janvier 2008** portant sur la protection des données à caractère personnel en République du Sénégal, et aux dispositions de la **Commission de Protection des Données Personnelles (CDP)**.

### 1. Responsable du traitement
Le responsable du traitement des données est SamaSokhla, éditeur de la plateforme TàakDiàkka, accessible à l'adresse taakdiakka.samasokhla.com.

### 2. Données collectées
Dans le cadre de l'utilisation de TàakDiàkka, nous collectons :
- **Données d'identification** : nom, prénom, adresse e-mail, mot de passe (chiffré), date de naissance, genre ;
- **Données de profil** : région, religion, pratique religieuse, profession, biographie, photos ;
- **Données de la demande de mariage** : critères et préférences matrimoniales ;
- **Données d'utilisation** : dernière connexion, type d'appareil, navigateur, adresse IP ;
- **Données de transaction** : montant, référence de paiement, formule souscrite (les données bancaires sont traitées exclusivement par PayDunya et ne sont jamais stockées sur nos serveurs).

### 3. Finalités du traitement
Conformément à l'article 4 de la Loi n°2008-12, les données sont collectées pour des **finalités déterminées, explicites et légitimes** :
- Création et gestion du compte utilisateur ;
- Mise en relation entre membres partageant des critères compatibles ;
- Amélioration et personnalisation du service ;
- Communication de notifications liées au service (messages, demandes d'amis, expirations d'abonnement) ;
- Modération et sécurité de la plateforme ;
- Statistiques anonymisées pour l'amélioration du service.

### 4. Base légale du traitement
Les traitements de données reposent sur :
- Le **consentement** de l'utilisateur, recueilli lors de l'inscription (article 5 de la Loi n°2008-12) ;
- L'**exécution du contrat** liant l'utilisateur à TàakDiàkka (CGU acceptées) ;
- L'**intérêt légitime** du responsable du traitement pour la sécurité de la plateforme.

### 5. Durée de conservation
- Les données du compte sont conservées tant que le compte est actif ;
- En cas de suppression du compte, les données personnelles sont effacées dans un délai de **30 jours** ;
- Les données de transaction (montant, date) sont conservées **5 ans** conformément aux obligations comptables ;
- Les journaux de modération sont conservés **1 an** après résolution.

### 6. Droits des personnes concernées
Conformément aux articles 62 à 68 de la Loi n°2008-12, vous disposez des droits suivants :
- **Droit d'accès** : obtenir la communication de vos données personnelles détenues ;
- **Droit de rectification** : faire modifier toute donnée inexacte ou incomplète ;
- **Droit de suppression** : demander l'effacement de vos données (droit à l'oubli) ;
- **Droit d'opposition** : vous opposer à un traitement de vos données pour motif légitime ;
- **Droit à la portabilité** : récupérer vos données dans un format structuré.

Pour exercer vos droits, contactez-nous à : **contact@taakdiakka.com**

Vous pouvez également adresser une réclamation à la **Commission de Protection des Données Personnelles (CDP)** :
Immeuble Artimon, 2ème étage, Rue Félix Eboué x Blaise Diagne, Dakar — contact@cdp.sn — www.cdp.sn

### 7. Sécurité des données
Conformément à l'article 71 de la Loi n°2008-12, nous mettons en œuvre des mesures techniques et organisationnelles pour protéger vos données :
- Chiffrement des mots de passe (bcrypt) ;
- Connexion sécurisée (HTTPS/TLS) ;
- Accès restreint aux données (seuls les administrateurs autorisés) ;
- Système de détection anti-partage de coordonnées dans les messages ;
- Journalisation des actions d'administration.

### 8. Transfert de données
Vos données sont hébergées en **France** (OVH SAS, Roubaix). Conformément à l'article 49 de la Loi n°2008-12, ce transfert est encadré par les garanties de protection adéquates offertes par la législation française et européenne (RGPD).

Aucune donnée personnelle n'est transmise à des tiers à des fins commerciales ou publicitaires.

### 9. Cookies et traceurs
TàakDiàkka utilise des cookies strictement nécessaires au fonctionnement du service (session, authentification, jeton CSRF). Aucun cookie publicitaire ou de profilage n'est utilisé. Si un outil d'analyse (Google Analytics) est activé, il l'est dans le respect de l'anonymisation des adresses IP.

### 10. Modification de la politique
La présente politique peut être mise à jour. En cas de modification substantielle, les utilisateurs seront informés par notification sur la plateforme. La date de dernière mise à jour est indiquée en haut du document.

### Contact
contact@taakdiakka.com
MD;

    private const MENTIONS = <<<'MD'
## Mentions Légales

### Éditeur du site
**TàakDiàkka** — Plateforme de rencontres matrimoniales
Éditée par **SamaSokhla**
Dakar, Sénégal
contact@taakdiakka.com — taakdiakka.samasokhla.com

### Directeur de la publication
Le directeur de la publication est le représentant légal de SamaSokhla.

### Hébergement
Le site est hébergé par :
**OVH SAS**
2 Rue Kellermann — 59100 Roubaix, France
+33 9 72 10 10 07 — www.ovh.com

### Propriété intellectuelle
L'ensemble des éléments constituant le site TàakDiàkka (textes, graphismes, logiciels, photographies, images, vidéos, sons, plans, noms, logos, marques, créations et œuvres protégeables diverses, bases de données, etc.) ainsi que le site lui-même, relèvent des législations sénégalaises et internationales sur le droit d'auteur et la propriété intellectuelle.

Ces éléments sont la propriété exclusive de SamaSokhla. Aucune reproduction, représentation, modification, publication, transmission, dénaturation, totale ou partielle du site ou de son contenu, par quelque procédé que ce soit, et sur quelque support que ce soit ne pourra être effectuée sans l'autorisation écrite préalable de SamaSokhla.

### Protection des données personnelles
La collecte et le traitement des données personnelles effectués sur ce site sont soumis à la **Loi n°2008-12 du 25 janvier 2008** portant sur la protection des données à caractère personnel en République du Sénégal.

Pour toute question relative à la protection de vos données, veuillez consulter notre Politique de Confidentialité ou contacter la **Commission de Protection des Données Personnelles (CDP)** : contact@cdp.sn — www.cdp.sn.

### Règlement des litiges
Les présentes mentions légales sont régies par le droit sénégalais. En cas de litige, et à défaut de résolution amiable, les tribunaux de Dakar seront seuls compétents.
MD;
}
