<?php

namespace App\Listeners;

use Laravel\Spark\Spark;
use Laravel\Spark\Events\Auth\UserRegistered;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

class CreateIotaSeed
{
    /**
     * The notification repository instance.
     *
     * @var NotificationRepository
     */
    protected $notifications;

    /**
     * Create a new listener instance.
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
     * @param  UserRegistered $event
     *
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // Generate IOTA seed and save to user
        $event->user->update([
            'iota_seed' => $this->generateSeed()
        ]);
    }

    /**
     * Generate Seed
     * @return string
     */
    public function generateSeed()
    {
        $seed = '';
        $allowed_characters = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '9',
        ];

        for ($i = 0; $i < 81; $i++) {
            // Cryptographically secure. (7.1 + built in)
            // http://php.net/manual/en/function.random-int.php
            $seed .= $allowed_characters[random_int(0, count($allowed_characters) - 1)];
        }

        return $seed;
    }
}
