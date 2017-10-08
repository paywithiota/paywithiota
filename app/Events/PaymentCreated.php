<?php

namespace App\Events;

use App\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PaymentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Payment $payment
     */
    public $payment;

    /**
     * @var array $metadata
     */
    public $metadata = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Payment $payment, $metadata = [])
    {
        $this->payment = $payment;
        $this->metadata = $metadata;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
