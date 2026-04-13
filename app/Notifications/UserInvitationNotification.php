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

        $url = route('register', ['token' => $invitation->token], true);

        $isInternal = $invitation->type === UserInvitation::TYPE_INTERNAL;

        return (new MailMessage)
            ->subject(__('invitations.mail.subject', ['app' => config('app.name')]))
            ->greeting(__('invitations.mail.greeting'))
            ->line($isInternal
                ? __('invitations.mail.line_internal', ['company' => $accountName])
                : __('invitations.mail.line_external', ['company' => $accountName]))
            ->line(__('invitations.mail.expires', ['date' => $invitation->expires_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—']))
            ->action(__('invitations.mail.action'), $url)
            ->line(__('invitations.mail.footer'));
    }
}
