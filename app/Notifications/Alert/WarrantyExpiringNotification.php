<?php

namespace App\Notifications\Alert;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarrantyExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Alert $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Warranty Expiring Alert: ' . $this->alert->asset->asset_tag)
                    ->greeting('Hello!')
                    ->line($this->alert->message)
                    ->action('View Asset', route('assets.show', $this->alert->asset_id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'message' => $this->alert->message,
            'asset_id' => $this->alert->asset_id,
            'asset_tag' => $this->alert->asset->asset_tag,
            'type' => $this->alert->type,
        ];
    }
}
