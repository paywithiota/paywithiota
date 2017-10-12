<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

class PaymentCompletedListener implements ShouldQueue
{
    /**
     * The notification repository instance.
     *
     * @var NotificationRepository
     */
    protected $notifications;

    /**
     * Create the event listener.
     *
     * @param  NotificationRepository $notifications
     *
     * @return void
     */
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
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
        $user = $event->payment->user;

        // Send email
        try{
            \Mail::send('emails.payment-completed', ['payment' => $event->payment], function ($message) use ($event, $user){
                $message->subject(config('app.name') . ' - Payment success');
                $message->to($user->email, $user->name);
            });

        }catch (\Exception $e){
            \Log::error("Exception: " . $e->getMessage() . " | Couldn't send Payment completed Email.");
        }

        // Create notification
        $this->notifications->create($user, [
            'icon'        => 'fa-check-circle',
            'body'        => 'Your payment with id ' . base64_encode($event->payment->id) . ' is now complete.',
            'action_text' => 'Payments',
            'action_url'  => route("Payments"),
        ]);

        \Log::info("Payment completed. " . print_r($event->payment->toArray(), true));
    }
}
