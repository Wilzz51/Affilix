# Affilix
Complete referral &amp; commission system for ClientXCMS
![Affilix Logo](https://files.sx-heberg.fr/api/shares/auxwhATq/files/4f117ea0-ca44-4078-8163-52a5367328eb?download=false)

# Addon Affilix — ClientXCMS

Système d'affiliation complet pour ClientXCMS : codes de parrainage, suivi des clics, commissions automatiques et tableau de bord affilié.

![Affilix Logo](https://files.sx-heberg.fr/api/shares/p9GLROGl/files/eb18cd39-7949-45d2-b599-9ead8288dca6?download=false)

## Prérequis

- ClientXCMS avec PHP >= 8.4 et Laravel >= 12
- Accès SSH ou terminal sur le serveur

## Installation

### 1. Copier l'addon

Placez le dossier `Affilix` dans le répertoire `addons` de votre ClientXCMS :

```
addons/
└── Affilix/
```

### 2. Exécuter les migrations

```
php artisan migrate
```

Cela crée les 5 tables nécessaires : `affiliates`, `referrals`, `affiliate_commissions`, `affiliate_clicks`, `affiliation_settings`.

### 3. Vider le cache

```
php artisan cache:clear
php artisan view:clear
```

L'addon se charge automatiquement via le système de ServiceProvider de ClientXCMS — aucune autre configuration n'est requise.

## Configuration

Rendez-vous sur **Admin > Paramètres > Affiliation** pour configurer :

| Paramètre | Défaut | Description |
|---|---|---|
| Taux de commission | 10% | Appliqué à chaque vente parrainée |
| Montant minimum de paiement | 50 | Seuil avant qu'un affilié puisse être payé |
| Durée du cookie | 30 jours | Durée de mémorisation d'un clic sur le lien |
| Approbation automatique des affiliés | Oui | Activer/désactiver la validation manuelle |
| Approbation automatique des commissions | Non | Les commissions doivent être approuvées manuellement |
| Méthodes de paiement | Balance, PayPal, Virement | Méthodes proposées aux affiliés |

## Utilisation

### Espace client

Les clients accèdent à leur espace affiliation via le menu **Affiliation** dans l'espace client :

1. **S'inscrire** — Remplir le formulaire et choisir une méthode de paiement
2. **Partager le lien** — Copier le lien de parrainage depuis le dashboard
3. **Suivre les gains** — Dashboard avec clics, parrainages, conversions et commissions

Le lien de parrainage a la forme : `https://votresite.com/ref/CODE`

### Espace admin

Gérez le programme depuis **Admin > Affiliation** :

- **Affiliés** — Liste, approbation, modification du taux, suspension
- **Commissions** — Approbation et paiement en lot (avec référence de paiement)
- **Paramètres** — Configuration globale du programme

### Paiement par balance

Quand un affilié choisit la méthode **Balance**, le montant de la commission est crédité automatiquement sur le solde de son compte client lors du paiement par l'admin.

## Permissions

Les permissions suivantes sont disponibles pour les rôles admin :

| Permission | Description |
|---|---|
| `affiliation.view` | Voir les affiliés et commissions |
| `affiliation.manage` | Modifier les affiliés |
| `affiliation.commissions.view` | Voir les commissions |
| `affiliation.commissions.approve` | Approuver les commissions |
| `affiliation.commissions.pay` | Marquer les commissions comme payées |
| `affiliation.settings` | Modifier les paramètres |

## Fonctionnement technique

- **Suivi des clics** : Un cookie est posé lors d'un clic sur `/ref/CODE`, valable selon la durée configurée
- **Création de parrainage** : À l'inscription (`Illuminate\Auth\Events\Registered`), le cookie est lu et un `Referral` est créé
- **Création de commission** : À chaque facture complétée (`App\Events\Core\Invoice\InvoiceCompleted`), une commission est générée si le client a un parrainage actif ou si le cookie est encore présent
- **Utilisateurs existants** : Si un client déjà inscrit clique sur un lien puis achète, la commission est quand même créée via le cookie de session

## Données collectées — RGPD

Cet addon traite des données à caractère personnel. L'opérateur du site est **responsable de traitement** au sens du Règlement (UE) 2016/679 (RGPD) et doit s'assurer que ces traitements sont couverts par sa documentation légale.

> Cette section est fournie à titre informatif. Elle ne constitue pas un avis juridique. Le responsable de traitement reste seul compétent pour apprécier la conformité de son installation au regard de sa situation spécifique.

### Traitements mis en œuvre

| Table | Catégories de données | Finalité | Durée de conservation recommandée |
|---|---|---|---|
| `affiliates` | Identifiant client, méthode de paiement, coordonnées PayPal ou bancaires | Gestion du programme et versement des commissions | Durée de la relation contractuelle + 5 ans (archivage comptable) |
| `referrals` | Identifiant du filleul, date d'inscription, date du premier achat | Attribution des commissions et suivi des conversions | Durée du contrat affilié + 1 an |
| `affiliate_commissions` | Montants, référence de paiement, horodatages | Traçabilité comptable des versements | 10 ans (obligation légale de conservation des pièces comptables) |
| `affiliate_clicks` | Adresse IP, user agent, code de parrainage, URL visitée | Statistiques de performance (clics par affilié) | 1 an |

### Cookie de suivi

Un cookie est posé lors d'un clic sur un lien de parrainage (`/ref/CODE`). Il contient uniquement le code de parrainage, qui n'est pas une donnée personnelle au sens du RGPD.

- **Durée** : configurable (défaut 30 jours)
- **Finalité** : rattacher une inscription ou un achat à l'affilié concerné
- **Base légale** : à qualifier par le responsable de traitement — intérêt légitime ou consentement selon l'interprétation retenue de la directive ePrivacy 2002/58/CE

### Obligations du responsable de traitement

Les points suivants relèvent de la responsabilité de l'opérateur, non de l'addon :

1. **Information des personnes** — La politique de confidentialité doit décrire le programme d'affiliation, les catégories de données traitées, les finalités et les durées de conservation.
2. **Droit à l'effacement** — En cas de demande d'un filleul, le responsable de traitement est tenu de supprimer ses entrées dans `referrals` et d'anonymiser les données personnelles non nécessaires dans les `affiliate_commissions` associées (nom, email, coordonnées de paiement). Les données comptables strictement nécessaires (montant, date, référence de paiement) doivent en revanche être conservées conformément à l'obligation légale de 10 ans — l'exception prévue à l'Article 17(3)(b) du RGPD s'applique.
3. **Droit d'accès** — Un affilié peut exercer son droit d'accès aux données le concernant (commissions, parrainages, coordonnées de paiement).
4. **Tiers destinataires** — En cas d'utilisation de PayPal comme méthode de paiement, les données transmises (identité, montant) sont traitées par PayPal en qualité de **responsable de traitement indépendant**, pour ses propres finalités (lutte contre la fraude, conformité financière). Ce transfert doit être mentionné dans la politique de confidentialité comme communication à un tiers.

### Mesures techniques intégrées (Privacy by Design — Article 25 RGPD)

L'addon applique les mesures de minimisation suivantes, sans que celles-ci constituent à elles seules une conformité au RGPD :

- Le nom de famille des filleuls est masqué dans l'interface affilié (`Prénom I.`) afin de limiter l'exposition des données entre utilisateurs
- L'accès aux données complètes est restreint à l'interface d'administration

.
