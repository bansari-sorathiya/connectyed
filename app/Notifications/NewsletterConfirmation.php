<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewsletterConfirmation extends Notification
{
    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Confirm Your Newsletter Subscription')
                    ->greeting('Hello!')
                    ->line('Thank you for subscribing to our newsletter.')
                    ->action('Confirm Subscription', url('/confirm-newsletter?email=' . urlencode($this->email)))
                    ->line('If you did not subscribe, no further action is required.')
                    ->line('To unsubscribe from our newsletter, click here: ' . url('/unsubscribe-newsletter?email=' . urlencode($this->email)));
    }
}
