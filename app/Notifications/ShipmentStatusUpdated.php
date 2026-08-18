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
        return [FcmChannel::class];
    }

    public function viaQueues(): array
    {
        return [
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
                title: 'تحديث حالة الشحنة',
                body: "تم تحديث حالة شحنتك إلى: " . $this->status . "\n" . ($this->status === "delivered" ? "يرجى تقييم السائق لضمان جودة الخدمة.\n شكرا لاختيارك حمولة!" : ''),
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
