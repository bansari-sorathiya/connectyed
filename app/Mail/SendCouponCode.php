<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendCouponCode extends Mailable
{
    public $client;
    public $couponCode;

    public function __construct($client, $couponCode)
    {
        $this->client = $client;
        $this->couponCode = $couponCode;
    }

    public function build()
    {
        return $this->subject('Your Coupon Code')
                    ->view('emails.send_coupon_code')
                    ->with([
                        'name' => $this->client->name,
                        'couponCode' => $this->couponCode,
                    ]);
    }
}
