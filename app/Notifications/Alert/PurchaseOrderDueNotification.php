<?php

namespace App\Notifications\Alert;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Alert $alert) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/purchase-orders');
        $subject = str_contains($this->alert->type ?? '', 'Overdue') ? 'Purchase Order Overdue' : 'Purchase Order Due Soon';
        return (new MailMessage)
            ->subject($subject)
            ->greeting('FYI')
            ->line($this->alert->message ?? 'A purchase order is approaching its expected date.')
            ->when($this->alert->due_date, fn ($m) => $m->line('Expected: ' . optional($this->alert->due_date)->toDateString()))
            ->action('View Purchase Orders', $url)
            ->line('You are receiving this because of your role or permissions.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->alert->type,
            'message' => $this->alert->message,
            'due_date' => optional($this->alert->due_date)->toDateString(),
            'link' => '/purchase-orders',
        ];
    }
}

