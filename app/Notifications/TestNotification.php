<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {

    }

    /**
     * Delivery channels
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * Assign queues
     */
    public function viaQueues(): array
    {
        return [
            FcmChannel::class => 'notifications',
        ];
    }

    /**
     * Push notification (FCM)
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'New Follow Request',
                body: "requested to follow you."
            ),
            data: [
                'type' => 'new_follow_request',
                'user_id' => (string) "dashboard",
            ]
        );
    }
}
