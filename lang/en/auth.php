<?php

/**
 * English authentication messages (login, throttle, email verification, etc.).
 */

return [

    /**
     * Shown above forms when $errors->any(); details use <x-form-field-error /> per field.
     */
    'form_errors_summary' => 'Something went wrong. Please check the messages below each field.',

    /** Shown after session is invalidated because kicked_out was set on the user. */
    'kicked_out_session' => 'Your session was ended by an administrator. Please sign in again.',

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'login' => [
        'title' => 'Log in',
        'heading' => 'Welcome back!',
        'intro' => 'Enter your email address and password to sign in.',
        'email' => 'Email',
        'password' => 'Password',
        'placeholder_email' => 'Email',
        'placeholder_password' => 'Password',
        'forgot_password' => 'Forgot your password?',
        'remember_me' => 'Remember me',
        'submit' => 'Log in',
        'or' => 'OR',
        'google' => 'Google',
        'facebook' => 'Facebook',
        'social_auth_failed' => 'We could not complete social sign in. Please try again.',
        'social_email_required' => 'We could not get your email from the social provider. Please sign in with email and password.',
        'social_user_not_found' => 'No account was found for that email. Please register first with email and password.',
        'social_invitation_only' => 'Social login is only available for employee invitation links.',
        'social_invitation_only_help' => 'Google/Facebook sign in is available only from an employee invitation link.',
        'social_existing_user_use_password' => 'An account already exists for that email. Please sign in with email and password.',
        'no_account' => 'Don’t have an account?',
        'create_account' => 'Create account',
    ],

    'register' => [
        'title' => 'Create account',
        'heading' => 'Create account',
        'intro' => 'Fill in your company and personal details to sign up.',
        'important' => 'Important:',
        'important_text' => 'If your company already has an account, do not register here on your own — ask your administrator for an invitation.',
        'company_name' => 'Company name',
        'company_type' => 'Company type',
        'your_name' => 'Your name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'placeholder_company_name' => 'Your company name',
        'placeholder_name' => 'Your name',
        'placeholder_email' => 'Email',
        'placeholder_password' => 'Minimum 8 characters',
        'placeholder_password_confirmation' => 'Repeat your password',
        'password_show' => 'Show password',
        'password_hide' => 'Hide password',
        'password_generate' => 'Generate random password',
        'select_type' => 'Select type...',
        'submit' => 'Create account',
        'or' => 'OR',
        'signup_google' => 'Continue with Google',
        'signup_facebook' => 'Continue with Facebook',
        'social_not_available_for_company_signup' => 'Social signup is not available for company registration.',
        'social_auth_failed' => 'We could not complete social sign in. Please try again.',
        'social_email_required' => 'We could not get your email from the social provider. Please use the registration form.',
        'already_have_account' => 'Already have an account?',
        'login' => 'Log in',
        'carousel_slide1_title' => 'Manage your business with ease',
        'carousel_slide1_text' => 'Make your application stand out with a high-quality landing page designed and developed by professionals.',
        'carousel_slide2_title' => 'The best way to showcase your app',
        'carousel_slide2_text' => 'Present your product clearly and attractively to your customers.',
        'carousel_slide3_title' => 'Solutions that convert leads into customers',
        'carousel_slide3_text' => 'Tools designed to boost your operations and sales.',

        'intro_internal' => 'Complete your details to join your company’s existing account.',
        'intro_external' => 'You were invited to try the platform. Register your company and your user.',
        'invitation_from' => 'Invited by :name (:company)',
        'important_text_internal' => 'This link is personal. Enter the email you want to use for your account; it must not already be registered. You will verify this address after signing up, same as a normal registration.',
        'important_text_external' => 'This link is personal. You will create a new company. Enter the email you want to use; it must not already be registered. You will verify this address after signing up.',
        'invited_company' => 'Company',
        'invitation_invalid' => 'This invitation is invalid or has expired.',
        'invitation_email_mismatch' => 'The email must match the invitation.',
    ],

    'verification' => [
        'title' => 'Verify your email',
        'heading' => 'Verify your email',
        'intro' => 'We sent you a welcome email with a link to verify your address. Click that link to activate your account.',
        'link_sent' => 'A new verification link has been sent to your email address.',
        'no_email' => 'If you did not receive the email, you can request that we send you another one.',
        'resend_button' => 'Resend verification email',
        'continue' => 'Continue',
        'email_subject' => 'Welcome to :app – Verify your email',
        'email_greeting' => 'Hello :name!',
        'email_body' => 'Thanks for signing up. To activate your account, please verify your email address by clicking the button below.',
        'email_action' => 'Verify email',
        'email_footer' => 'If you did not create an account, you can ignore this message.',
        'email_salutation' => 'Regards, :app',
        'verified' => 'Your email has been verified. You can continue using your account.',
        'already_verified' => 'Your email was already verified.',
    ],

];
