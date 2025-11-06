<?php

namespace App\Notifications\Clearance;

use App\Models\Clearance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClearanceRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Clearance $clearance)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Clearance Rejected #'.$this->clearance->id)
            ->greeting('Hello '.$this->clearance->staff?->name)
            ->line('Your clearance request was rejected.')
            ->line('Remarks: '.($this->clearance->remarks ?? '—'))
            ->action('View Request', url('/clearances/'.$this->clearance->id));
    }
}

