<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentCreated;
use App\Util\Iota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $response = [
            'status' => false,
            'errors' => []
        ];

        // If payment id is passed
        if ($paymentId = $request->get('id')) {

            // Get payment details
            $payment = auth()->user()->payments()->where('id', base64_decode($paymentId))->first();

            if ($payment) {

                // Set response data
                $response['data'] = [
                    'payment_id' => base64_encode($payment->id),
                    'invoice_id' => $payment->invoice_id,
                    'price_usd'  => $payment->price_usd,
                    'price_iota' => $payment->price_usd,
                    'ipn'        => $payment->ipn,
                    'address'    => $payment->address->address,
                    'custom'     => $payment->metadata,
                    'status'     => $payment->status,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at
                ];

                $response['status'] = 1;
            }

        }else {
            $payments = auth()->user()->payments;

            if ($payments) {
                foreach ($payments as $payment) {

                    // Set response data
                    $response['data'][] = [
                        'payment_id' => base64_encode($payment->id),
                        'invoice_id' => $payment->invoice_id,
                        'price_usd'  => $payment->price_usd,
                        'price_iota' => $payment->price_usd,
                        'ipn'        => $payment->ipn,
                        'address'    => $payment->address->address,
                        'custom'     => $payment->metadata,
                        'status'     => $payment->status,
                        'created_at' => $payment->created_at,
                        'updated_at' => $payment->updated_at
                    ];
                }

                $response['status'] = 1;
            }
        }

        return response()->json($response);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $response = [
            'status' => false,
            'errors' => []
        ];

        // Real id
        $invoiceId = $request->get('invoice_id');

        // Sender id
        $senderId = $request->get('sender_id');

        // Iota Address
        $address = (new Iota())->generateAddress(auth()->user()->iota_seed, auth()->user()->addresses()->count());

        // Price
        $price = $request->get('price');

        // Currency
        $currency = trim(strtoupper($request->get('currency')));

        // Price in USD
        $priceUsd = $request->get('price_usd');

        // Price in iota
        $priceIota = $request->get('price_iota');

        if ( ! $priceUsd && ! $priceIota && $price && $currency) {
            $priceUsd = (new Iota())->convertCurrency($price, $currency, 'USD');
        }

        // Current Price in iota if not passed by api
        $priceIota = $priceIota > 0 ? $priceIota : ($priceUsd ? (new Iota())->getPrice($priceUsd, 'I') : null);

        // IPN Url {This url is called when a payment is processed}
        $ipnUrl = $request->get('ipn');

        // IPN Code {This code is returned with ipn}
        $ipnVerifyCode = $request->get('ipn_verify_code');

        // Custom variables
        $customVariables = array_where($request->all(), function ($value, $key){
            return starts_with($key, 'custom_var_');
        });

        $customVariables = $customVariables ? $customVariables : [];

        // Save custom price and currency
        if ($price && $currency) {
            $customVariables = array_merge($customVariables, [
                'price'    => $price,
                'currency' => $currency,
            ]);
        }

        // Validation Rules
        $rules = array(
            'address'    => 'required',
            'price_iota' => 'required',
        );

        // Create a new validator instance.
        $validator = Validator::make([
            'address'    => $address,
            'price_iota' => $priceIota
        ], $rules, [
            'address.required'    => 'IOTA address could not be generated. Please try again later.',
            'price_iota.required' => "Price is required and cannot be empty."
        ]);

        // If validation fails
        if ($validator->fails()) {

            $errors = $validator->messages();
            $response['errors'] = $errors;

        }else {

            // Save Address
            $address = auth()->user()->addresses()->firstOrCreate([
                'address' => $address
            ]);


            // Create payment
            $payment = auth()->user()->payments()->create([
                'invoice_id'       => $invoiceId,
                'sender_id'        => $senderId > 0 ? $senderId : auth()->user()->id,
                'price_usd'        => $priceUsd,
                'price_iota'       => $priceIota,
                'ipn'              => $ipnUrl,
                'ipn_verify_code'  => $ipnVerifyCode ? $ipnVerifyCode : '',
                'address_id'       => $address->id,
                'transaction_hash' => '',
                'metadata'         => $customVariables ? $customVariables : [],
                'status'           => 0
            ]);


            if ($payment) {

                $metadata = $payment->metadata;
                // Set response data
                $response['data'] = [
                    'payment_id' => base64_encode($payment->id),
                    'invoice_id' => $payment->invoice_id,
                    'price_usd'  => $payment->price_usd,
                    'price_iota' => $payment->price_iota,
                    'ipn'        => $payment->ipn,
                    'address'    => $address->address,
                    'custom'     => $payment->metadata,
                    'status'     => $payment->status,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at
                ];

                $response['status'] = 1;

                // [Event]
                event(new PaymentCreated($payment, []));
            }
        }

        return response()->json($response);
    }
}
