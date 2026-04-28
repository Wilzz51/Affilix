# Affilix
Complete referral &amp; commission system for ClientXCMS

# Addon Affilix — ClientXCMS

Système d'affiliation complet pour ClientXCMS : codes de parrainage, suivi des clics, commissions automatiques et tableau de bord affilié.

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

Cela crée les 4 tables nécessaires : `affiliates`, `referrals`, `affiliate_commissions`, `affiliate_clicks`.

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

Quand un affilié choisit la méthode **Balance**, le montant de la commission est crédité automatiquement sur le solde de son compte client lors du paiement paradmin.

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

.
