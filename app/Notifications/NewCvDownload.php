<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewCvDownload extends Notification
{
    use Queueable;

    protected $contactData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $name = $this->contactData['name'] ?? 'Quelqu\'un';
        return (new WebPushMessage)
            ->title('🚀 Nouveau téléchargement de CV')
            ->icon('/favicon.ico')
            ->body("{$name} vient de télécharger votre CV.")
            ->action('Voir', '/admin');
    }
}
