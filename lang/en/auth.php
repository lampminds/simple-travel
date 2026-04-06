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

    'register' => [
        'title' => 'Create account',
        'heading' => 'Create account',
        'intro' => 'Fill in your company and personal details to sign up.',
        'important' => 'Important:',
        'important_text' => 'If your company already has an account, do not register here; you should sign up via an invitation from your company administrator (we will implement this later).',
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
        'select_type' => 'Select type...',
        'submit' => 'Create account',
        'or' => 'OR',
        'signup_github' => 'Sign up with Github',
        'already_have_account' => 'Already have an account?',
        'login' => 'Log in',
        'carousel_slide1_title' => 'Manage your business with ease',
        'carousel_slide1_text' => 'Make your application stand out with a high-quality landing page designed and developed by professionals.',
        'carousel_slide2_title' => 'The best way to showcase your app',
        'carousel_slide2_text' => 'Present your product clearly and attractively to your customers.',
        'carousel_slide3_title' => 'Solutions that convert leads into customers',
        'carousel_slide3_text' => 'Tools designed to boost your operations and sales.',
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
        'verified' => 'Your email has been verified. You can continue using your account.',
        'already_verified' => 'Your email was already verified.',
    ],

];
