<?php

use App\Models\SiteContact;

if (! function_exists('site_identity')) {
    /**
     * Enregistrement SiteContact courant, ou null si la base n'est pas joignable.
     *
     * Les fichiers de lang/ appellent site_name() / site_email() / site_phone() des
     * leur chargement. Ces appels surviennent donc aussi hors contexte HTTP : migrate,
     * db:seed, commandes artisan sur une base encore vide. Sans garde, une table
     * site_contacts absente faisait echouer toute la commande. On retombe ici
     * silencieusement sur les valeurs par defaut.
     *
     * Memoise pour la duree du processus afin d'eviter des requetes SQL repetees.
     */
    function site_identity(): ?SiteContact
    {
        static $contact  = null;
        static $resolved = false;

        if (! $resolved) {
            $resolved = true;

            try {
                $contact = SiteContact::current();
            } catch (\Throwable $e) {
                $contact = null;
            }
        }

        return $contact;
    }
}

if (! function_exists('site_config_fallback')) {
    /**
     * Lecture defensive de la config : elle n'est pas encore montee tres tot
     * dans le bootstrap, ou l'appelant peut s'executer hors application.
     */
    function site_config_fallback(string $key, string $default): string
    {
        try {
            $value = config($key);
        } catch (\Throwable $e) {
            return $default;
        }

        return is_string($value) && $value !== '' ? $value : $default;
    }
}

if (! function_exists('site_name')) {
    /**
     * Nom du site configure par l'admin (SiteContact::name).
     * Repli : APP_NAME, puis une constante.
     */
    function site_name(): string
    {
        return site_identity()?->name
            ?: site_config_fallback('app.name', 'Santander Bank');
    }
}

if (! function_exists('site_email')) {
    /**
     * Email de contact configure par l'admin (SiteContact::email).
     * Repli : MAIL_FROM_ADDRESS, pour ne figer aucun domaine dans le code.
     */
    function site_email(): string
    {
        return site_identity()?->email
            ?: site_config_fallback('mail.from.address', 'contact@example.com');
    }
}

if (! function_exists('site_phone')) {
    /**
     * Telephone de contact configure par l'admin (SiteContact::phone_1).
     */
    function site_phone(): string
    {
        return site_identity()?->phone_1 ?: '';
    }
}

if (! function_exists('site_address')) {
    /**
     * Adresse(s) de l'entreprise configurée(s) par l'admin (SiteContact::address_1..3), séparées par « / ».
     */
    function site_address(): string
    {
        $c = site_identity();

        return $c ? implode(' / ', array_filter([$c->address_1, $c->address_2, $c->address_3])) : '';
    }
}

if (! function_exists('site_phone_href')) {
    /**
     * Numero de telephone nettoye pour un lien tel: (chiffres et + uniquement).
     */
    function site_phone_href(): string
    {
        return preg_replace('/[^\d+]/', '', site_phone());
    }
}
