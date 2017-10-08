<?php

namespace App\Listeners;

use App\Payment;
use App\Util\Iota;
use Laravel\Spark\Events\Auth\UserRegistered;

class CreateIotaSeed
{

    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(Payment $payment, $metadata = [])
    {
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered $event
     *
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // Generate IOTA seed and save to user
        $event->user->forceFill([
            'iota_seed' => (new Iota())->generateSeed()
        ])->save();
    }

}
