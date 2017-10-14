<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PaymentCreated $event
     *
     * @return void
     */
    public function handle(PaymentCreated $event)
    {
        /** \Mail::send('emails.payment-created', ['payment' => $event->payment], function ($message) use ($event){
         * $message->subject(config('app.name') . ' - Payment created');
         * $message->to($event->payment->user->email, $event->payment->user->name);
         * }); */

        // \Log::info("Payment created. " . print_r($event->payment->toArray(), true));
    }
}
