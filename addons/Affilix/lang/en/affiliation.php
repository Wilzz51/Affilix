<?php

return [
    'title' => 'Affiliation',
    'dashboard' => 'Dashboard',
    'my_affiliate_account' => 'My Affiliate Account',
    'become_affiliate' => 'Become an Affiliate',
    'register' => 'Register as Affiliate',
    
    // Stats
    'stats' => [
        'total_clicks'  => 'Unique Clicks',
        'unique_clicks' => 'Unique Clicks',
        'total_referrals' => 'Total Referrals',
        'successful_referrals' => 'Conversions',
        'conversion_rate' => 'Conversion Rate',
        'total_earnings' => 'Total Earnings',
        'pending_earnings' => 'Pending Earnings',
        'paid_earnings' => 'Paid Earnings',
        'commission_rate' => 'Commission Rate',
    ],
    
    // Referral
    'referral_code' => 'Referral Code',
    'referral_link' => 'Referral Link',
    'copy_link' => 'Copy Link',
    'share_link' => 'Share Link',
    'your_referral_code' => 'Your Referral Code',
    
    // Commissions
    'commissions' => 'Commissions',
    'commission' => 'Commission',
    'amount' => 'Amount',
    'status' => 'Status',
    'date' => 'Date',
    'invoice' => 'Invoice',
    'description' => 'Description',
    'pending' => 'Pending',
    'approved' => 'Approved',
    'paid' => 'Paid',
    'cancelled' => 'Cancelled',
    
    // Referrals
    'referrals' => 'Referrals',
    'customer' => 'Customer',
    'registered_at' => 'Registered at',
    'first_purchase_at' => 'First Purchase at',
    'clicked' => 'Clicked',
    'registered' => 'Registered',
    'converted' => 'Converted',
    
    // Settings
    'settings' => 'Settings',
    'payment_method' => 'Payment Method',
    'payment_details' => 'Payment Details',
    'paypal_email' => 'PayPal Email',
    'bank_account' => 'Bank Account',
    'save' => 'Save',
    
    // Status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'suspended' => 'Suspended',
    
    // Messages
    'messages' => [
        'account_created'    => 'Your affiliate account has been created successfully!',
        'pending_approval'   => 'Your request has been submitted and will be reviewed by our team.',
        'settings_updated'   => 'Settings updated successfully!',
        'link_copied'        => 'Link copied to clipboard!',
        'registered_success' => 'Your affiliate account has been created successfully!',
        'registered_pending' => 'Your request has been submitted and will be reviewed by our team.',
        'already_registered' => 'You already have an affiliate account.',
    ],
    
    // Commission
    'commission_description'       => 'Commission for invoice #:id',
    'commission_click_description' => 'Commission for unique click',

    // Settings labels
    'settings_first_order_only'             => 'Commission on first order only',
    'settings_first_order_only_help'        => 'If enabled, only one commission is generated per referred customer.',
    'settings_click_remuneration'           => 'Remunerate unique clicks',
    'settings_click_remuneration_help'      => 'If enabled, each unique click on an affiliate link generates a commission.',
    'settings_click_remuneration_rate'      => 'Amount per unique click',
    'settings_click_remuneration_rate_help' => 'Fixed amount credited to the affiliate for each new unique visitor.',

    // Emails
    'emails' => [
        'commission_approved_subject' => 'Your commission has been approved',
        'commission_approved_intro'   => 'Good news! Your commission has just been approved.',
        'commission_approved_detail'  => 'It will be paid out on the next payment run.',
        'commission_paid_subject'     => 'Your commission has been paid',
        'commission_paid_intro'       => 'Your commission has just been paid.',
        'commission_paid_detail'      => 'Thank you for being part of our affiliate program.',
    ],

    // Admin
    'admin' => [
        'affiliates' => 'Manage Affiliates',
        'manage_affiliates' => 'Manage Affiliates',
        'total_affiliates' => 'Total Affiliates',
        'active_affiliates' => 'Active Affiliates',
        'total_commissions' => 'Total Commissions',
        'pending_commissions' => 'View and approve commissions',
        'approve' => 'Approve',
        'pay' => 'Pay',
        'cancel' => 'Cancel',
        'approve_selected' => 'Approve Selected',
        'pay_selected' => 'Pay Selected',
        'payment_reference' => 'Payment Reference',
        'edit_affiliate' => 'Edit Affiliate',
        'delete_affiliate' => 'Delete Affiliate',
        'affiliate_updated'       => 'Affiliate updated successfully.',
        'affiliate_deleted'       => 'Affiliate deleted successfully.',
        'commissions_approved'    => ':count commission(s) approved successfully.',
        'commissions_paid'        => ':count commission(s) marked as paid.',
        'settings_saved'          => 'Settings saved successfully.',
    ],
];
