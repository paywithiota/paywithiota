<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'authy' => [
        'secret' => env('AUTHY_SECRET'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'iota' => [
        'node_url'         => env('IOTA_NODE_URL'),
        'donation_address' => env('IOTA_DONATION_ADDRESS'),
        'units'            => [
            'I'  => 1,
            'KI' => 1000,
            'MI' => 1000000,
            'GI' => 1000000000,
            'TI' => 1000000000000,
            'PI' => 1000000000000000,
        ],
        'nodes'            => [
            'https://node.tangle.works:443',
            'http://iota.bitfinex.com:80',
            'http://service.iotasupport.com:14265',
            'http://eugene.iota.community:14265',
            'http://eugene.iotasupport.com:14999',
            'http://eugeneoldisoft.iotasupport.com:14265',
            'http://node01.iotatoken.nl:14265',
            'http://node02.iotatoken.nl:14265',
            'http://node03.iotatoken.nl:15265',
            'http://node.deviceproof.org:14265',
            'http://mainnet.necropaz.com:14500',
            "http://node01.iotatoken.nl:14265",
        ]
    ]
];
