<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'Ces identifiants ne correspondent à aucun compte.',
    'password' => 'Le mot de passe fourni est incorrect.',
    'throttle' => 'Trop de tentatives. Réessayez dans :seconds secondes.',

    'client_login_title'  => 'Espace Client',
    'client_login_sub'    => 'Connectez-vous pour accéder à votre compte',
    'client_brand_title'  => 'Votre espace<br>client ' . site_name(),
    'client_brand_sub'    => 'Suivez vos demandes, gérez votre profil et accédez à tous vos documents en toute sécurité.',

    'staff_login_title'   => 'Portail Administration',
    'staff_login_sub'     => 'Réservé exclusivement au personnel autorisé',
    'staff_brand_title'   => 'Administration<br>' . site_name(),
    'staff_brand_sub'     => 'Accès sécurisé aux outils de gestion, de suivi des dossiers et d\'administration des utilisateurs.',

    'email'               => 'Adresse email',
    'email_staff'         => 'Email professionnel',
    'email_ph'            => 'vous@exemple.com',
    'email_ph_staff'      => 'agent@mellenthinfinancial.online',
    'password_label'      => 'Mot de passe',
    'remember'            => 'Se souvenir de moi',
    'remember_staff'      => 'Rester connecté',
    'submit'              => 'Se connecter',
    'submit_staff'        => 'Accéder au tableau de bord',
    'back_site'           => 'Retour au site',
    'staff_portal_link'   => 'Portail agent / administrateur',
    'client_portal_link'  => 'Espace client',
    'staff_restricted'    => 'Accès restreint — Personnel autorisé',
    'staff_notice'        => 'Ce portail est réservé aux agents ' . site_name() . '. Toutes les connexions sont journalisées.',
    'or_staff'            => 'Vous êtes agent ou admin ?',
    'or_client'           => 'Vous êtes client ?',

    'stat_clients'        => 'Clients satisfaits',
    'stat_amount'         => 'Prêt max / dossier',
    'stat_time'           => 'Réponse garantie',
    'stat_years'          => "Ans d'expérience",

    'feature_secure'      => 'Données chiffrées',
    'feature_currencies'  => '6 devises acceptées',
    'feature_certified'   => 'Agrément européen',
    'feature_fast'        => 'Réponse en 48h',

    'role_superadmin'     => 'Super Administrateur',
    'role_superadmin_sub' => 'Gestion globale & rôles',
    'role_admin'          => 'Administrateur',
    'role_admin_sub'      => 'Gestion des dossiers',

    // Identifier (email or phone)
    'identifier'          => 'Email ou téléphone',
    'identifier_ph'       => 'votre@email.com ou +33...',
    'forgot_password'     => 'Mot de passe oublié ?',
    'portal_clients_only' => 'Ce portail est réservé aux clients.',

    // OTP page
    'otp_title'           => 'Vérification',
    'otp_heading'         => 'Code de sécurité',
    'otp_subtitle'        => 'Nous avons envoyé un code à 6 chiffres à',
    'otp_enter'           => 'Saisissez le code reçu par email',
    'otp_verify_btn'      => 'Vérifier',
    'otp_resend'          => 'Renvoyer le code',
    'otp_resend_in'       => 'Renvoyer dans',
    'otp_back'            => 'Changer de compte',
    'otp_verifying'       => 'Vérification en cours…',
    'otp_invalid'         => 'Code incorrect. Il vous reste :remaining tentative(s).',
    'otp_expired'         => 'Ce code a expiré. Demandez-en un nouveau.',
    'otp_too_many'        => 'Trop de tentatives. Réessayez dans :seconds secondes.',
    'otp_resend_limit'    => 'Trop de renvois. Réessayez dans quelques minutes.',
    'otp_send_failed'     => 'Impossible d\'envoyer le code. Réessayez.',
    'otp_session_expired' => 'Session expirée. Reconnectez-vous.',
    'otp_resend_success'  => 'Nouveau code envoyé !',

    // Compte mémorisé
    'change_account' => 'Changer de compte',

    // Compte bloqué
    'account_blocked'                     => 'Votre compte est bloqué suite à trop de tentatives incorrectes. Consultez votre email pour recevoir le lien de déblocage.',
    'account_blocked_notified'            => 'Trop de tentatives incorrectes. Votre compte a été bloqué. Un lien de déblocage vous a été envoyé par email.',
    'account_unblocked'                   => 'Votre compte a été débloqué avec succès. Vous pouvez maintenant vous connecter.',
    'unblock_invalid'                     => 'Ce lien de déblocage est invalide ou a expiré. Contactez le support.',

    'account_blocked_email_subject'       => 'Votre compte ' . site_name() . ' a été bloqué',
    'account_blocked_email_title'         => 'Compte temporairement bloqué',
    'account_blocked_email_intro'         => 'Votre compte a été temporairement bloqué suite à plusieurs tentatives de connexion incorrectes.',
    'account_blocked_email_reason_title'  => 'Pourquoi ce blocage ?',
    'account_blocked_email_reason_body'   => '4 codes OTP incorrects ont été saisis consécutivement lors d\'une tentative de connexion à votre compte. Par mesure de sécurité, l\'accès a été suspendu.',
    'account_blocked_email_btn'           => 'Débloquer mon compte',
    'account_blocked_email_fallback'      => 'Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :',
    'account_blocked_email_notice'        => 'Si vous n\'êtes pas à l\'origine de ces tentatives, ne cliquez pas sur ce lien et contactez immédiatement le support ' . site_name() . '.',
    'account_blocked_email_footer'        => 'Lien valable 48 heures.',

    // OTP email
    'otp_email_subject'      => 'Votre code de connexion — ' . site_name(),
    'otp_email_title'        => 'Code de vérification',
    'otp_email_intro'        => 'Voici votre code de connexion à usage unique. Ne le communiquez à personne.',
    'otp_email_code_label'   => 'Votre code',
    'otp_email_expiry'       => 'Ce code expire dans 10 minutes.',
    'otp_email_notice_title' => 'Sécurité importante',
    'otp_email_notice_body'  => site_name() . ' ne vous demandera jamais ce code par téléphone ou par message. Si vous n\'avez pas demandé ce code, ignorez cet email.',
    'otp_email_footer'       => 'Si vous n\'avez pas demandé ce code, ignorez cet email.',

    // Bannière d'installation PWA (pages de connexion)
    'pwa_install_title' => 'Installer l\'application',
    'pwa_install_hint'  => 'Accès rapide · Notifications · Mode hors-ligne',
    'pwa_install_btn'   => 'Installer',
    'pwa_ios_title'     => 'Installer l\'app ' . site_name() . ' sur votre iPhone',
    'pwa_ios_step1'     => 'Appuyez sur <strong>Partager</strong> dans Safari',
    'pwa_ios_step2'     => 'Choisissez <strong>Sur l\'écran d\'accueil</strong>',
    'pwa_ios_step3'     => 'Appuyez sur <strong>Ajouter</strong> — c\'est fait !',
    'pwa_close'         => 'Fermer',

];
