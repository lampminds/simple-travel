<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use App\Services\PriceFormatService;
use App\Support\BookingPassengersSnapshot;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
{
    public function __construct(
        public Booking $booking,
        public string $agencyLabel,
        public string $packageLabel,
        public string $bookingUrl,
    ) {
        $this->booking->loadMissing(['currency']);
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
        $totalFormatted = app(PriceFormatService::class)->formatWithCurrency(
            (float) $this->booking->subtotal,
            currency: $this->booking->currency,
            accountId: (int) $this->booking->operator_id,
        );

        $customerNote = '';
        $remarksCustomer = $this->booking->remarks_customer;
        if (is_array($remarksCustomer)) {
            $customerNote = trim((string) ($remarksCustomer['note'] ?? ''));
        }

        $mail = (new MailMessage)
            ->subject(__('account.reservations.notifications.operator_created_mail_subject', [
                'code' => $this->booking->booking_code,
            ]))
            ->greeting(__('account.reservations.notifications.operator_created_mail_greeting'))
            ->line(__('account.reservations.notifications.operator_created_mail_intro', [
                'agency' => $this->agencyLabel,
                'code' => $this->booking->booking_code,
            ]))
            ->line(__('account.reservations.notifications.operator_created_mail_package', [
                'package' => $this->packageLabel,
            ]))
            ->line(__('account.reservations.notifications.operator_created_mail_dates', [
                'dates' => locale_date($this->booking->travel_start_date).' — '.locale_date($this->booking->travel_end_date),
            ]))
            ->line(__('account.reservations.notifications.operator_created_mail_passengers', [
                'passengers' => BookingPassengersSnapshot::formatSummary($this->booking->passengers_snapshot),
            ]))
            ->line(__('account.reservations.notifications.operator_created_mail_total', [
                'total' => $totalFormatted,
            ]));

        if ($customerNote !== '') {
            $mail->line(__('account.reservations.notifications.operator_created_mail_remarks', [
                'remarks' => $customerNote,
            ]));
        }

        return $mail
            ->action(__('account.reservations.notifications.operator_created_mail_action'), $this->bookingUrl)
            ->line(__('account.reservations.notifications.operator_created_mail_footer'));
    }
}
