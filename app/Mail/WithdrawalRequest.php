<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WithdrawalRequest extends Mailable
{
    public $matchmaker;
    public $amount;

    public function __construct($matchmaker, $amount)
    {
        $this->matchmaker = $matchmaker;
        $this->amount = $amount;
    }

    public function build()
    {
        return $this->subject('Withdrawal Request')
                    ->view('emails.withdrawal_request')
                    ->with([
                        'name' => $this->matchmaker->name,
                        'email' => $this->matchmaker->email,
                        'amount' => $this->amount,
                    ]);
    }
}
