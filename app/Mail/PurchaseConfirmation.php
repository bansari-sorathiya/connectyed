<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $productType;
    public $packageDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client, $productType)
    {
        $this->client = $client;
        $this->productType = $productType;
        $this->packageDetails = $this->getPackageDetails($productType);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to ' . ucfirst($this->packageDetails['name']) . ' Package!')
                    ->markdown('emails.purchase-confirmation');
    }

    /**
     * Get package details based on product type
     *
     * @param string $productType
     * @return array
     */
    private function getPackageDetails($productType)
    {
        $packages = [
            'silver_package' => [
                'name' => 'Silver Package',
                'criteria' => 4,
                'features' => [
                    '1 on 1 Blind Date',
                    'Choose up to 4 criteria',
                    '10 days delivery time'
                ]
            ],
            'gold_package' => [
                'name' => 'Gold Package',
                'criteria' => 6,
                'features' => [
                    '1 on 1 Curated Date',
                    'Includes Matchmaker Call',
                    'Choose up to 6 criteria',
                    'Match Screening included',
                    '8 days delivery time'
                ]
            ],
            'platinum_package' => [
                'name' => 'Platinum Package',
                'criteria' => 9,
                'features' => [
                    '1 on 1 Premium Date',
                    'Includes Matchmaker Call',
                    'Choose up to 9 criteria',
                    'Match Screening included',
                    'Match Verification included',
                    'Pick 1 of 2 Matches',
                    '6 days delivery time'
                ]
            ]
        ];

        return $packages[$productType] ?? $packages['silver_package'];
    }
}