<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Taux de commission par défaut
    |--------------------------------------------------------------------------
    |
    | Le taux de commission en pourcentage appliqué par défaut aux nouveaux
    | affiliés. Ce taux peut être personnalisé pour chaque affilié.
    | Peut être modifié depuis l'interface admin.
    |
    */
    'default_commission_rate' => 10,

    /*
    |--------------------------------------------------------------------------
    | Approbation automatique des affiliés
    |--------------------------------------------------------------------------
    |
    | Si activé, les nouveaux affiliés sont automatiquement approuvés.
    | Sinon, ils doivent être approuvés manuellement par un administrateur.
    | Peut être modifié depuis l'interface admin.
    |
    */
    'auto_approve' => true,

    /*
    |--------------------------------------------------------------------------
    | Approbation automatique des commissions
    |--------------------------------------------------------------------------
    |
    | Si activé, les commissions sont automatiquement approuvées lors de la
    | création. Sinon, elles doivent être approuvées manuellement.
    | Peut être modifié depuis l'interface admin.
    |
    */
    'auto_approve_commissions' => false,

    /*
    |--------------------------------------------------------------------------
    | Montant minimum de paiement
    |--------------------------------------------------------------------------
    |
    | Le montant minimum que doit atteindre un affilié avant de pouvoir
    | demander un paiement.
    | Peut être modifié depuis l'interface admin.
    |
    */
    'minimum_payout' => 50,

    /*
    |--------------------------------------------------------------------------
    | Durée du cookie de parrainage (en jours)
    |--------------------------------------------------------------------------
    |
    | La durée pendant laquelle le cookie de parrainage est valide.
    | Par exemple, si un visiteur clique sur un lien de parrainage et
    | s'inscrit 10 jours plus tard, il sera toujours compté comme parrainage.
    | Peut être modifié depuis l'interface admin.
    |
    */
    'cookie_lifetime' => 30,

    /*
    |--------------------------------------------------------------------------
    | Méthodes de paiement disponibles
    |--------------------------------------------------------------------------
    |
    | Les méthodes de paiement que les affiliés peuvent choisir pour
    | recevoir leurs commissions.
    |
    */
    'payment_methods' => [
        'paypal' => 'PayPal',
        'bank_transfer' => 'Virement bancaire',
        'other' => 'Autre',
    ],

    /*
    |--------------------------------------------------------------------------
    | Périodes de rapport
    |--------------------------------------------------------------------------
    |
    | Les périodes disponibles pour générer des rapports de performance.
    |
    */
    'report_periods' => [
        'today' => "Aujourd'hui",
        'yesterday' => 'Hier',
        'this_week' => 'Cette semaine',
        'last_week' => 'Semaine dernière',
        'this_month' => 'Ce mois',
        'last_month' => 'Mois dernier',
        'this_year' => 'Cette année',
        'all_time' => 'Tout le temps',
    ],
];
