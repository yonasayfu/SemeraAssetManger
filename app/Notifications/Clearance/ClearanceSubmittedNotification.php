<?php

namespace App\Notifications\Clearance;

use App\Models\Clearance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClearanceSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('New Clearance Submitted #'.$this->clearance->id)
            ->greeting('Hello')
            ->line('A staff clearance request has been submitted.')
            ->line('Staff: '.$this->clearance->staff?->name)
            ->action('Review Clearance', url('/admin/clearances/'.$this->clearance->id))
            ->line('Thank you.');
    }
}

