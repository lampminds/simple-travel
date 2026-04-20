<?php

namespace App\Notifications;

use App\Models\UserInvitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification
{

    public function __construct(
        public UserInvitation $invitation
    ) {
        $this->invitation->loadMissing('account');
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invitation = $this->invitation;
        $accountName = $invitation->account?->commercial_name
            ?? $invitation->account?->name
            ?? '';
        $expiresAt = $invitation->expires_at?->timezone(config('app.timezone'));
        $daysUntilExpiration = $expiresAt
            ? max(0, now(config('app.timezone'))->diffInDays($expiresAt, false))
            : 0;

        $url = route('register', ['token' => $invitation->token], true);

        $isInternal = $invitation->type === UserInvitation::TYPE_INTERNAL;
        $mailMessage = (new MailMessage)
            ->subject(__('invitations.mail.subject', ['app' => config('app.name')]))
            ->greeting(__('invitations.mail.greeting'))
            ->line($isInternal
                ? __('invitations.mail.line_internal', ['company' => $accountName])
                : __('invitations.mail.line_external', ['company' => $accountName, 'app' => config('app.name')]));

        if (! $isInternal) {
            $mailMessage->line(__('invitations.mail.marketing_external'));
        }

        return $mailMessage
            ->line(__('invitations.mail.expires_in_days', ['days' => $daysUntilExpiration]))
            ->action(__('invitations.mail.action'), $url)
            ->line(__('invitations.mail.footer'));
    }
}
