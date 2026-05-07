<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ShipmentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $status, public string $shipmentId)
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
        return ['database', FcmChannel::class];
    }

    public function viaQueues(): array
    {
        return [
            'database' => 'notifications',
            FcmChannel::class => 'notifications',
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'Shipment Status Updated',
                body: "Your shipment is now " . $this->status
            ),
            data: [
                'type' => 'shipment_status_updated',
                'shipment_id' => (string) $this->shipmentId,
                'status' => $this->status,
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shipment_status_updated',
            'shipment_id' => $this->shipmentId,
            'status' => $this->status,
        ];
    }
}
