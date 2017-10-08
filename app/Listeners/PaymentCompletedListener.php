<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentCompletedListener implements ShouldQueue
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
     * @param  PaymentCompleted $event
     *
     * @return void
     */
    public function handle(PaymentCompleted $event)
    {
        \Mail::send('emails.payment-completed', ['payment' => $event->payment], function ($message) use($event){
            $message->subject(config('app.name') . ' - Payment success');
            $message->to($event->payment->user->email, $event->payment->user->name);
        });

        \Log::info("Payment completed. " . print_r($event->payment->toArray(), true));
    }
}
