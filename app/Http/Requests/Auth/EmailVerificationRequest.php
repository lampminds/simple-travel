<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

class EmailVerificationRequest extends BaseEmailVerificationRequest
{
    public function authorize(): bool
    {
        if (! hash_equals((string) $this->user()->getRouteKey(), (string) $this->route('user'))) {
            return false;
        }

        if (! hash_equals(sha1($this->user()->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }
}
