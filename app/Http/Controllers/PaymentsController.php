<?php

namespace App\Http\Controllers;

use App\Payment;
use App\User;
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
            $payment = Payment::whereId(base64_decode($paymentId))->first();

            if ($payment) {

                if ($payment->status == 1) {
                    return $returnUrl ? redirect($returnUrl) : redirect(route("Home"));
                }

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
     * Show a payment
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function show(Request $request, $payment)
    {
        $payment = auth()->user()->payments()->whereId(base64_decode($payment))->first();

        if ($payment) {
            return view('payments.show', compact('payment'));
        }else {
            return redirect(route("Payments"));
        }
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
            ]);


            if ($payment && isset($payment->status) && $payment->status == 1 && $payment->data->payment_id) {
                return redirect(route("Payments.Pay", [
                    'return_url' => $request->get('return_url'),
                    'payment_id' => $payment->data->payment_id,
                ]));
            }else {
                flash("Payment was not created due to some error.", "error");

                return redirect(route("Home"));
            }
        }else {
            return view("payments.buy");
        }
    }

    /**
     * Deposit Balance
     *
     * @param Request $request
     */
    public function deposit(Request $request)
    {
        $amount = intval($request->get('amount'));
        $unit = $request->get('unit');

        if ($amount > 0) {

            $priceIota = $amount;

            if ($unit == 'MIOTA') {
                $priceIota = $amount * 1000000;
            }else if ($unit == 'GIOTA') {
                $priceIota = $amount * 1000000000;
            }else if ($unit == 'PIOTA') {
                $priceIota = $amount * 1000000000000;
            }

            $payment = (new Iota())->call([
                'METHOD' => 'POST',
                'URL'    => route("Api.Payments.Create", ['api_token' => auth()->user()->token()]),
                'DATA'   => [
                    'invoice_id'      => '',
                    'price_usd'       => '',
                    'price_iota'      => $priceIota,
                    'ipn'             => '',
                    'ipn_verify_code' => '',
                    'custom'          => [
                        'type' => 'CUSTOM_DEPOSIT'
                    ]
                ]
            ]);

            if ($payment && isset($payment->status) && $payment->status == 1 && $payment->data->payment_id) {
                return redirect(route("Payments.Pay", [
                    'return_url' => route("Payments.Show", ['payment_id' => $payment->data->payment_id]),
                    'payment_id' => $payment->data->payment_id,
                ]));
            }else {
                flash("Payment creation request was not accepted due to some error.", "error");

                return redirect(route("Home"));
            }
        }else {
            flash("You cannot create an empty request.", "error");

            return redirect(route("Payments"));
        }

    }

    /**
     * Show deposit form
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function showDepositForm(Request $request)
    {
        return view("payments.deposit");
    }

    /**
     * Show transfer form
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function showTransferForm(Request $request)
    {
        return view("payments.transfer");
    }

    /**
     * Transfer Balance
     *
     * @param Request $request
     */
    public function transfer(Request $request)
    {
        $amount = intval($request->get('amount'));
        $unit = $request->get('unit');
        $userId = $request->get('user_id');
        $user = $userId ? User::whereId($userId)->first() : null;

        if ($amount > 0 && $user) {


            $priceIota = $amount;

            if ($unit == 'MIOTA') {
                $priceIota = $amount * 1000000;
            }else if ($unit == 'GIOTA') {
                $priceIota = $amount * 1000000000;
            }else if ($unit == 'PIOTA') {
                $priceIota = $amount * 1000000000000;
            }

            $payment = (new Iota())->call([
                'METHOD' => 'POST',
                'URL'    => route("Api.Payments.Create", ['api_token' => $user->token()]),
                'DATA'   => [
                    'invoice_id'      => '',
                    'price_usd'       => '',
                    'price_iota'      => $priceIota,
                    'ipn'             => '',
                    'ipn_verify_code' => '',
                    'custom'          => [
                        'type'           => 'TRANSFER',
                        'transferred_by' => auth()->user()->email,
                    ]
                ]
            ]);

            if ($payment && isset($payment->status) && $payment->status == 1 && $payment->data->payment_id) {
                return redirect(route("Payments.Pay", [
                    'return_url' => route("Payments.Show", ['payment_id' => $payment->data->payment_id]),
                    'payment_id' => $payment->data->payment_id,
                ]));
            }else {
                flash("Transfer request was not accepted due to some error.", "error");

                return redirect(route("Home"));
            }
        }else {
            flash("You cannot create an empty transfer.", "error");

            return redirect(route("Payments"));
        }

    }
}
