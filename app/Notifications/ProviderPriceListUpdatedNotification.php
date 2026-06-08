<?php

namespace App\Notifications;

use App\Models\Account;
use App\Models\PriceList;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ProviderPriceListUpdatedNotification extends Notification
{
    public function __construct(
        public PriceList $priceList,
        public Account $providerAccount,
        public ?string $customMessage = null,
        public ?string $ccEmail = null,
    ) {
        $this->priceList->loadMissing(['currency.lmpCurrency']);
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
        $providerLabel = (string) ($this->providerAccount->commercial_name
            ?: $this->providerAccount->name
            ?: $this->providerAccount->nick
            ?: '#'.$this->providerAccount->id);

        $mailMessage = (new MailMessage)
            ->subject(__('account.price_lists.notification_mail.subject', [
                'provider' => $providerLabel,
                'list' => $this->priceList->name,
            ]))
            ->greeting(__('account.price_lists.notification_mail.greeting'))
            ->line(__('account.price_lists.notification_mail.line_intro', [
                'provider' => $providerLabel,
                'list' => $this->priceList->name,
            ]));

        $customMessage = trim((string) ($this->customMessage ?? ''));
        if ($customMessage !== '') {
            $mailMessage->line(new HtmlString(
                '<blockquote style="border-left: 4px solid #cbd5e1; margin: 16px 0; padding: 8px 16px; color: #475569;">'
                .'<p style="margin: 0; font-style: italic; font-weight: bold;">'
                .nl2br(e($customMessage))
                .'</p></blockquote>'
            ));
        }

        $mailMessage->line(__('account.price_lists.notification_mail.footer'));

        if ($this->ccEmail !== null && trim($this->ccEmail) !== '') {
            $mailMessage->cc(trim($this->ccEmail));
        }

        return $mailMessage;
    }
}
