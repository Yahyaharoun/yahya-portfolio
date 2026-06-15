<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewPartnershipProposal extends Notification
{
    use Queueable;

    protected $contract;

    /**
     * Create a new notification instance.
     */
    public function __construct($contract)
    {
        $this->contract = $contract;
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
        $company = $this->contract->company ?? 'Une entreprise';
        return (new WebPushMessage)
            ->title('🤝 Nouvelle proposition de Partenariat !')
            ->icon('/favicon.ico')
            ->body("{$company} souhaite collaborer avec vous.")
            ->action('Voir', '/admin');
    }
}
