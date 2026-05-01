<?php

return [
    'title' => 'Affiliation',
    'dashboard' => 'Tableau de bord',
    'my_affiliate_account' => 'Mon compte affilié',
    'become_affiliate' => 'Devenir affilié',
    'register' => 'S\'inscrire en tant qu\'affilié',
    
    // Stats
    'stats' => [
        'total_clicks' => 'Clics totaux',
        'total_referrals' => 'Parrainages totaux',
        'successful_referrals' => 'Conversions',
        'conversion_rate' => 'Taux de conversion',
        'total_earnings' => 'Gains totaux',
        'pending_earnings' => 'Gains en attente',
        'paid_earnings' => 'Gains payés',
        'commission_rate' => 'Taux de commission',
    ],
    
    // Referral
    'referral_code' => 'Code de parrainage',
    'referral_link' => 'Lien de parrainage',
    'copy_link' => 'Copier le lien',
    'share_link' => 'Partager le lien',
    'your_referral_code' => 'Votre code de parrainage',
    
    // Commissions
    'commissions' => 'Commissions',
    'commission' => 'Commission',
    'amount' => 'Montant',
    'status' => 'Statut',
    'date' => 'Date',
    'invoice' => 'Facture',
    'description' => 'Description',
    'pending' => 'En attente',
    'approved' => 'Approuvée',
    'paid' => 'Payée',
    'cancelled' => 'Annulée',
    
    // Referrals
    'referrals' => 'Parrainages',
    'customer' => 'Client',
    'registered_at' => 'Inscrit le',
    'first_purchase_at' => 'Premier achat le',
    'clicked' => 'Clic',
    'registered' => 'Inscrit',
    'converted' => 'Converti',
    
    // Settings
    'settings' => 'Paramètres',
    'payment_method' => 'Méthode de paiement',
    'payment_details' => 'Détails de paiement',
    'paypal_email' => 'Email PayPal',
    'bank_account' => 'Compte bancaire',
    'save' => 'Enregistrer',
    
    // Status
    'active' => 'Actif',
    'inactive' => 'Inactif',
    'suspended' => 'Suspendu',
    
    // Messages
    'messages' => [
        'account_created'    => 'Votre compte affilié a été créé avec succès !',
        'pending_approval'   => 'Votre demande a été soumise et sera examinée par notre équipe.',
        'settings_updated'   => 'Paramètres mis à jour avec succès !',
        'link_copied'        => 'Lien copié dans le presse-papiers !',
        'registered_success' => 'Votre compte affilié a été créé avec succès !',
        'registered_pending' => 'Votre demande a été soumise et sera examinée par notre équipe.',
        'already_registered' => 'Vous avez déjà un compte affilié.',
    ],
    
    // Commission
    'commission_description' => 'Commission pour la facture #:id',

    // Settings labels
    'settings_first_order_only'      => 'Commission sur la 1ère commande uniquement',
    'settings_first_order_only_help' => 'Si activé, une seule commission est générée par client parrainé.',

    // Emails
    'emails' => [
        'commission_approved_subject' => 'Votre commission a été approuvée',
        'commission_approved_intro'   => 'Bonne nouvelle ! Votre commission vient d\'être approuvée.',
        'commission_approved_detail'  => 'Elle sera versée lors du prochain paiement.',
        'commission_paid_subject'     => 'Votre commission a été payée',
        'commission_paid_intro'       => 'Votre commission vient d\'être versée.',
        'commission_paid_detail'      => 'Merci de faire partie de notre programme d\'affiliation.',
    ],

    // Admin
    'admin' => [
        'affiliates' => 'Gérer les affiliés',
        'manage_affiliates' => 'Gérer les affiliés',
        'total_affiliates' => 'Total affiliés',
        'active_affiliates' => 'Affiliés actifs',
        'total_commissions' => 'Commissions totales',
        'pending_commissions' => 'Voir et approuver les commissions',
        'approve' => 'Approuver',
        'pay' => 'Payer',
        'cancel' => 'Annuler',
        'approve_selected' => 'Approuver la sélection',
        'pay_selected' => 'Payer la sélection',
        'payment_reference' => 'Référence de paiement',
        'edit_affiliate' => 'Modifier l\'affilié',
        'delete_affiliate' => 'Supprimer l\'affilié',
        'affiliate_updated'       => 'Affilié mis à jour avec succès.',
        'affiliate_deleted'       => 'Affilié supprimé avec succès.',
        'commissions_approved'    => ':count commission(s) approuvée(s) avec succès.',
        'commissions_paid'        => ':count commission(s) marquée(s) comme payée(s).',
        'settings_saved'          => 'Paramètres enregistrés avec succès.',
    ],
];
