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
            ->subject('Shipment Cancelled')
            ->line('Your shipment has been cancelled by the ' . $this->user_role . '.')
            ->line('The reason is: ' . $this->reason)
            ->line('Please check the shipment details for more information.');
    }


    public function toFcm(object $notifiable): FcmMessage
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'Shipment Cancelled',
                body: "Your shipment has been cancelled by " . $this->user_role . ".\nThe reason is: " . $this->reason . "\nPlease check the shipment details for more information."
            ),
            data: [
                'type' => 'shipment_cancelled',
                'shipment_id' => (string) $this->shipment->id,
                'reason' => $this->reason,
            ]
        );
    }
}
