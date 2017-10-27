<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        '2fa_name' => "PayWithIOTA.com",
        'vendor' => 'PayWithIOTA.com',
        'product' => 'Pay with IOTA',
        'street' => 'India',
        'location' => 'India',
        'phone' => '000-000-000',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = "info@paywithiota.com";

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        "nivesh@centire.in"
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        Spark::useTwoFactorAuth();

        //  Spark::useStripe()->noCardUpFront()->trialDays(10);

        Spark::freePlan()
            ->features([
                'First', 'Second', 'Third'
            ]);

       /* Spark::plan('Basic', 'provider-id-1')
            ->price(10)
            ->features([
                'First', 'Second', 'Third'
            ]);*/

    }
}
