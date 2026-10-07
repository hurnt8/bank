<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Devises (DEPRECATED — seed uniquement)
    |--------------------------------------------------------------------------
    | Ces clés ne sont plus lues en direct nulle part dans l'application : la
    | table `currencies` (voir App\Models\Currency et /admin/currencies) est
    | la source de vérité. Elles ne servent qu'à peupler les données initiales
    | de la migration create_currencies_table. Modifiez les devises via
    | l'admin, pas ici.
    */
    'default_currency' => env('DEFAULT_CURRENCY', 'EUR'),

    'currencies' => ['EUR', 'GBP', 'CHF', 'NOK', 'SEK', 'DKK', 'PLN', 'CZK', 'HUF', 'RON'],

    'currency_symbols' => [
        'EUR' => '€',
        'GBP' => '£',
        'CHF' => 'CHF',
        'NOK' => 'kr',
        'SEK' => 'kr',
        'DKK' => 'kr',
        'PLN' => 'zł',
        'CZK' => 'Kč',
        'HUF' => 'Ft',
        'RON' => 'lei',
    ],

    /*
    |--------------------------------------------------------------------------
    | Badges de statut (appli cliente PWA)
    |--------------------------------------------------------------------------
    | Source unique statut -> (classe .ca-badge--*, icone, couleur de secours
    | pour les usages hors-CSS comme les barres de progression).
    */
    'status_badges' => [
        'invoice' => [
            'sent'      => ['badge' => 'sent',      'icon' => 'fa-paper-plane',   'color' => '#8B5CF6'],
            'paid'      => ['badge' => 'paid',      'icon' => 'fa-circle-check',  'color' => '#00C896'],
            'cancelled' => ['badge' => 'cancelled', 'icon' => 'fa-ban',           'color' => '#7A90AA'],
        ],
        'movement' => [
            'pending'      => ['badge' => 'pending',      'icon' => 'fa-clock',        'color' => '#F59E0B'],
            'fee_required' => ['badge' => 'fee-required', 'icon' => 'fa-circle-info',  'color' => '#4A9EFF'],
            'rejected'     => ['badge' => 'rejected',     'icon' => 'fa-circle-xmark', 'color' => '#FF5A5A'],
            'completed'    => ['badge' => 'completed',    'icon' => 'fa-circle-check', 'color' => '#00C896'],
        ],
    ],

];
