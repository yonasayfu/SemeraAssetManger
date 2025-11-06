<?php

namespace App\Notifications\Alert;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Alert $alert) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/contracts');
        return (new MailMessage)
            ->subject('Contract Expiring')
            ->greeting('Heads up!')
            ->line($this->alert->message ?? 'A contract is expiring soon.')
            ->when($this->alert->due_date, fn ($m) => $m->line('Due: ' . optional($this->alert->due_date)->toDateString()))
            ->action('View Contracts', $url)
            ->line('You are receiving this because of your role or permissions.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->alert->type,
            'message' => $this->alert->message,
            'due_date' => optional($this->alert->due_date)->toDateString(),
            'link' => '/contracts',
        ];
    }
}

