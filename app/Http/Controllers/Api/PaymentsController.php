<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentCreated;
use App\Util\Iota;
use Illuminate\Http\Request;
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

        // Iota Address
        $address = (new Iota())->generateAddress(auth()->user()->iota_seed, auth()->user()->payments()->count());

        // Price in USD
        $priceUsd = $request->get('price_usd');

        // Price in iota
        $priceIota = $request->get('price_iota');

        // Current Price in iota if not passed by api
        $priceIota = $priceIota > 0 ? $priceIota : (new Iota())->getPrice($priceUsd, 'IOTA');

        // IPN Url {This url is called when a payment is processed}
        $ipnUrl = $request->get('ipn');

        // IPN Code {This code is returned with ipn}
        $ipnVerifyCode = $request->get('ipn_verify_code');

        // Custom variables
        $customVariables = array_where($request->all(), function ($value, $key){
            return starts_with($key, 'custom_var_');
        });

        if ($address) {

            // Save Address
            $address = auth()->user()->addresses()->create([
                'address' => $address
            ]);

            // Create payment
            $payment = auth()->user()->payments()->create([
                'invoice_id'       => $invoiceId,
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
        }else {
            $response['errors'][] = "IOTA Address couldn't be generated.";
        }

        return response()->json($response);
    }
}
