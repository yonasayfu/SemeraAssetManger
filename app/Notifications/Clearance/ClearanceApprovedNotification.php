<?php

namespace App\Notifications\Clearance;

use App\Models\Clearance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClearanceApprovedNotification extends Notification implements ShouldQueue
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
        $url = url('/clearances/'.$this->clearance->id.'/pdf');
        return (new MailMessage)
            ->subject('Clearance Approved #'.$this->clearance->id)
            ->greeting('Hello '.$this->clearance->staff?->name)
            ->line('Your clearance request has been approved.')
            ->action('Download Clearance PDF', $url)
            ->line('If you have questions, please contact HR.');
    }
}

