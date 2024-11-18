<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->customVerificationUrl($notifiable);
        
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Click the button below to verify your email address.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');
    }
    
    protected function customVerificationUrl($notifiable)
    {   
        // Generate the signed URL with proper configuration     
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification())
            ]
        );
        
        // Use the APP_URL from your .env
        $frontendUrl = config('app.url');
        
        // Construct the final URL
        return "{$frontendUrl}/email/verify?verification_url=" . urlencode($signedUrl);
    }
}