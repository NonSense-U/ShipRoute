<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewRatingAdded extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
    public string $shipmentId,
        public int $rating,
        public string $comment,
        public string $fullName
    ) {}

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

    public function toFcm(object $notifiable): FcmMessage
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'New Rating Added',
                body: "You received a new rating of " . $this->rating . " from " . $this->fullName . ". Comment: " . $this->comment
            ),
            data: [
                'type' => 'new_rating_added',
                'shipment_id' => (string) $this->shipmentId,
                'rating' => $this->rating,
                'comment' => $this->comment,
                'fullName' => $this->fullName . ". Comment: " . $this->comment,
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
            'type' => 'new_rating_added',
            'shipment_id' => (string) $this->shipmentId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'fullName' => $this->fullName,
        ];
    }
}
