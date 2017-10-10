<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Util\Iota;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $except = [
            'payNow',
            'buy',
            'product'
        ];

        // Login
        if ( ! $request->user() && ! $request->get('login')) {
            $except[] = 'pay';
        }

        $this->middleware('auth')->except($except);
    }

    /**
     * Show payments
     *
     * @return View
     */
    public function index(Request $request)
    {
        $payments = auth()->user()->payments()->orderby('id', 'desc')->get();

        return view('payments.index', compact('payments'));
    }

    /**
     * Addresses
     *
     * @return View
     */
    public function addresses(Request $request)
    {
        $user = auth()->user();
        $addresses = $user->addresses()->orderby('id', 'asc')->get();

        return view('payments.addresses', compact('addresses', 'user'));
    }

    /**
     * Pay with IOTA
     *
     * @param Request $request
     */
    public function pay(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $returnUrl = $request->get('return_url');
        $user = auth()->user();

        if ($paymentId) {
            $payment = Payment::whereId(base64_decode($paymentId))->whereStatus(0)->first();

            if ($payment) {
                return view("payments.pay", compact('payment', 'returnUrl', 'user'));
            }
        }

        return redirect(route("Home"));
    }

    /**
     * Pay with IOTA
     *
     * @param Request $request
     */
    public function payNow(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $returnUrl = $request->get('return_url');

        if ($paymentId) {
            $payment = Payment::whereId(base64_decode($paymentId))->whereStatus(0)->first();

            if ($payment) {
                return view("payments.pay", compact('payment', 'returnUrl'));
            }
        }

        return redirect(route("Home"));
    }

    /**
     * Product
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function product()
    {
        return view('payments.product');
    }

    /**
     * Buy with IOTA
     *
     * @param Request $request
     */
    public function buy(Request $request)
    {
        if (strtolower($request->method()) == 'post') {
            $payment = (new Iota())->call([
                'METHOD' => 'POST',
                'URL'    => route("Api.Payments.Create", ['api_token' => $request->get('api_token')]),
                'DATA'   => [
                    'invoice_id'      => $request->get('invoice_id'),
                    'price_usd'       => $request->get('price_usd'),
                    'price_iota'      => $request->get('price_iota'),
                    'ipn'             => $request->get('ipn'),
                    'ipn_verify_code' => $request->get('ipn_verify_code'),
                    'metadata'        => [
                        'custom_var_time' => time()
                    ]
                ]
            ]);print_r($payment);die;

            // Todo address not created
            if ($payment && isset($payment->status) && $payment->status == 1 && $payment->data->payment_id) {
                return redirect(route("Payments.Pay", [
                    'return_url' => $request->get('return_url'),
                    'payment_id' => $payment->data->payment_id,
                ]));
            }else {
                return redirect(route("Home"));
            }
        }else {
            return view("payments.buy");
        }
    }
}
