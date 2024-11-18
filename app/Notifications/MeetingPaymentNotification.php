<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MeetingPaymentNotification extends Notification
{
    use Queueable;

    protected $meeting;
    protected $paymentLink;

    public function __construct($meeting, $paymentLink)
    {
        $this->meeting = $meeting;
        $this->paymentLink = $paymentLink;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirm Your Meeting with Payment')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new meeting scheduled.')
            ->line('Meeting Time: ' . $this->meeting->start_time)
            ->action('Complete Payment to Confirm', $this->paymentLink)
            ->line('Thank you for using our service!');
    }
}
