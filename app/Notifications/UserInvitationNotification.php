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
        $this->invitation->loadMissing(['account', 'role']);
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
        $invitedName = trim((string) $invitation->name);
        $roleName = trim((string) ($invitation->role?->name ?? ''));
        $accountName = $invitation->account?->commercial_name
            ?? $invitation->account?->name
            ?? '';
        $expiresAt = $invitation->expires_at?->timezone(config('app.timezone'));
        $daysUntilExpiration = $expiresAt
            ? max(0, (int) ceil(now(config('app.timezone'))->floatDiffInDays($expiresAt, false)))
            : 0;

        $url = route('register', ['token' => $invitation->token], true);

        $isInternal = $invitation->type === UserInvitation::TYPE_INTERNAL;
        $mailMessage = (new MailMessage)
            ->subject(__('invitations.mail.subject', [
                'name' => $invitedName !== '' ? $invitedName : $invitation->email,
                'app' => config('app.name'),
            ]))
            ->greeting(__('invitations.mail.greeting'))
            ->line($isInternal
                ? __('invitations.mail.line_internal', ['company' => $accountName])
                : __('invitations.mail.line_external', ['company' => $accountName, 'app' => config('app.name')]));

        if ($isInternal && $roleName !== '') {
            $mailMessage->line(__('invitations.mail.role', ['role' => $roleName]));
        }

        if (! $isInternal) {
            $mailMessage
                ->line(__('invitations.mail.external_provider'))
                ->line(__('invitations.mail.external_agency'))
                ->line(__('invitations.mail.external_operator'));
        }

        return $mailMessage
            ->line(__('invitations.mail.expires_in_days', ['days' => $daysUntilExpiration]))
            ->action(__('invitations.mail.action'), $url)
            ->line(__('invitations.mail.footer'));
    }
}
