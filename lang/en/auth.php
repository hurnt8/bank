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

    'failed'   => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'client_login_title'  => 'Client Portal',
    'client_login_sub'    => 'Sign in to access your account',
    'client_brand_title'  => 'Your ' . site_name() . '<br>client space',
    'client_brand_sub'    => 'Track your applications, manage your profile and access all your documents securely.',

    'staff_login_title'   => 'Administration Portal',
    'staff_login_sub'     => 'Restricted to authorised personnel only',
    'staff_brand_title'   => site_name() . '<br>Administration',
    'staff_brand_sub'     => 'Secure access to management tools, case tracking and user administration.',

    'email'               => 'Email address',
    'email_staff'         => 'Professional email',
    'email_ph'            => 'you@example.com',
    'email_ph_staff'      => 'agent@mellenthinfinancial.online',
    'password_label'      => 'Password',
    'remember'            => 'Remember me',
    'remember_staff'      => 'Stay signed in',
    'submit'              => 'Sign in',
    'submit_staff'        => 'Access dashboard',
    'back_site'           => 'Back to site',
    'staff_portal_link'   => 'Agent / admin portal',
    'client_portal_link'  => 'Client space',
    'staff_restricted'    => 'Restricted access — Authorised staff only',
    'staff_notice'        => 'This portal is reserved for ' . site_name() . ' staff. All logins are logged.',
    'or_staff'            => 'Are you an agent or admin?',
    'or_client'           => 'Are you a client?',

    'stat_clients'        => 'Satisfied clients',
    'stat_amount'         => 'Max loan / file',
    'stat_time'           => 'Guaranteed response',
    'stat_years'          => 'Years of experience',

    'feature_secure'      => 'Encrypted data',
    'feature_currencies'  => '6 currencies accepted',
    'feature_certified'   => 'EU certified',
    'feature_fast'        => 'Response in 48h',

    'role_superadmin'     => 'Super Administrator',
    'role_superadmin_sub' => 'Global management & roles',
    'role_admin'          => 'Administrator',
    'role_admin_sub'      => 'Case management',

    'identifier'          => 'Email or phone',
    'identifier_ph'       => 'your@email.com or +44...',
    'forgot_password'     => 'Forgot password?',
    'portal_clients_only' => 'This portal is for clients only.',

    'otp_title'           => 'Verification',
    'otp_heading'         => 'Security code',
    'otp_subtitle'        => 'We sent a 6-digit code to',
    'otp_enter'           => 'Enter the code received by email',
    'otp_verify_btn'      => 'Verify',
    'otp_resend'          => 'Resend code',
    'otp_resend_in'       => 'Resend in',
    'otp_back'            => 'Change account',
    'otp_verifying'       => 'Verifying…',
    'otp_invalid'         => 'Invalid code. :remaining attempt(s) remaining.',
    'otp_expired'         => 'This code has expired. Request a new one.',
    'otp_too_many'        => 'Too many attempts. Try again in :seconds seconds.',
    'otp_resend_limit'    => 'Too many resend attempts. Try again in a few minutes.',
    'otp_send_failed'     => 'Unable to send code. Please try again.',
    'otp_session_expired' => 'Session expired. Please log in again.',
    'otp_resend_success'  => 'New code sent!',

    // Remembered account
    'change_account' => 'Switch account',

    // Account blocked
    'account_blocked'                     => 'Your account has been blocked after too many incorrect attempts. Check your email for the unblock link.',
    'account_blocked_notified'            => 'Too many incorrect attempts. Your account has been blocked. An unblock link has been sent to your email.',
    'account_unblocked'                   => 'Your account has been successfully unblocked. You can now log in.',
    'unblock_invalid'                     => 'This unblock link is invalid or has expired. Please contact support.',

    'account_blocked_email_subject'       => 'Your ' . site_name() . ' account has been blocked',
    'account_blocked_email_title'         => 'Account temporarily blocked',
    'account_blocked_email_intro'         => 'Your account has been temporarily blocked following several incorrect login attempts.',
    'account_blocked_email_reason_title'  => 'Why was my account blocked?',
    'account_blocked_email_reason_body'   => '4 incorrect OTP codes were entered consecutively during a login attempt. As a security measure, access has been suspended.',
    'account_blocked_email_btn'           => 'Unblock my account',
    'account_blocked_email_fallback'      => 'If the button does not work, copy this link into your browser:',
    'account_blocked_email_notice'        => 'If you did not make these attempts, do not click this link and contact ' . site_name() . ' support immediately.',
    'account_blocked_email_footer'        => 'Link valid for 48 hours.',

    'otp_email_subject'      => 'Your login code — ' . site_name(),
    'otp_email_title'        => 'Verification code',
    'otp_email_intro'        => 'Here is your one-time login code. Never share it with anyone.',
    'otp_email_code_label'   => 'Your code',
    'otp_email_expiry'       => 'This code expires in 10 minutes.',
    'otp_email_notice_title' => 'Important security notice',
    'otp_email_notice_body'  => site_name() . ' will never ask for this code by phone or message. If you did not request this code, please ignore this email.',
    'otp_email_footer'       => 'If you did not request this code, ignore this email.',

    // PWA install banner (login pages)
    'pwa_install_title' => 'Install the app',
    'pwa_install_hint'  => 'Quick access · Notifications · Offline mode',
    'pwa_install_btn'   => 'Install',
    'pwa_ios_title'     => 'Install the ' . site_name() . ' app on your iPhone',
    'pwa_ios_step1'     => 'Tap <strong>Share</strong> in Safari',
    'pwa_ios_step2'     => 'Choose <strong>Add to Home Screen</strong>',
    'pwa_ios_step3'     => 'Tap <strong>Add</strong> — you\'re done!',
    'pwa_close'         => 'Close',

];
