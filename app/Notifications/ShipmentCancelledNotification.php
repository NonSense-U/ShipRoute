<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class ShipmentCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Shipment $shipment, public string $user_role, public string $reason)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', FcmChannel::class];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => 'notifications',
            FcmChannel::class => 'notifications',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم إلغاء الشحنة')
            ->line('تم إلغاء شحنتك من قِبل ' . ($this->user_role === 'merchant' ? 'التاجر' : 'السائق') . '.')
            ->line('السبب: ' . $this->reason)
            ->line('يرجى مراجعة تفاصيل الشحنة لمزيد من المعلومات.');
    }


    public function toFcm(object $notifiable): FcmMessage
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'تم إلغاء الشحنة',
                body: "تم إلغاء شحنتك من قِبل " . ($this->user_role === 'merchant' ? 'التاجر' : 'السائق') . ".\nالسبب: " . $this->reason . "\nيرجى مراجعة تفاصيل الشحنة لمزيد من المعلومات."
            ),
            data: [
                'type' => 'shipment_cancelled',
                'shipment_id' => (string) $this->shipment->id,
                'reason' => $this->reason,
            ]
        );
    }
}
