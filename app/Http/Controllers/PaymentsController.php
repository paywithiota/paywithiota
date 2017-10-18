<?php

namespace App\Http\Controllers;

use App\Address;
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
            'product',
            'updateMetadata'
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

        $payments = Payment::whereUserId(auth()->user()->id)
                           ->orWhere('sender_id', auth()->user()->id)
                           ->orderby('id', 'desc')->get();

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

                // Addresses
                $totalAddresses = $user ? Address::whereUserId($user->id)->count() : 49;

                return view("payments.pay", compact('payment', 'returnUrl', 'user', 'totalAddresses'));
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
        $user = auth()->user();

        if ($paymentId) {
            $payment = Payment::whereId(base64_decode($paymentId))->whereStatus(0)->first();

            if ($payment) {

                // Addresses
                $totalAddresses = $user ? Address::whereUserId($user->id)->count() : 49;

                return view("payments.pay", compact('payment', 'returnUrl', 'user', 'totalAddresses'));
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
        try{
            \Artisan::call("iota:payments:check", [
                "paymentId" => base64_decode($payment)
            ]);

        }catch (\Exception $e){
        }


        $payment = Payment::whereId(base64_decode($payment))->where(function ($query){
            return $query->whereUserId(auth()->user()->id)
                         ->orWhere('sender_id', auth()->user()->id);
        })->first();

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
                flash("Payment was not created due to some error.", "danger");

                return redirect(route("Home"));
            }
        }else {
            return view("payments.buy");
        }
    }

    /**
     * Update payment metadata
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMetadata(Request $request)
    {
        $response = [
            'status' => 0
        ];

        $paymentId = $request->get("payment_id");

        $payment = Payment::whereId(base64_decode($paymentId))->first();

        if ($payment && $payment->status == 0) {
            $paymentMetadata = $payment->metadata;

            if ( ! isset($paymentMetadata['transaction'])) {
                $paymentMetadata['transaction'] = $request->all();

                $payment->update([
                    'metadata' => $paymentMetadata
                ]);

                $response['status'] = 1;
            }
        }

        return response()->json($response);
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

            $priceIota = (new Iota())->convertToUnit($amount, $unit, 'I');

            $payment = (new Iota())->call([
                'METHOD' => 'POST',
                'URL'    => route("Api.Payments.Create", ['api_token' => auth()->user()->token()]),
                'DATA'   => [
                    'invoice_id'      => "DEPOSIT-" . time(),
                    'price_usd'       => '',
                    'price_iota'      => $priceIota,
                    'ipn'             => '',
                    'ipn_verify_code' => '',
                    'custom_var_type' => 'DEPOSIT'
                ]
            ]);

            if ($payment && isset($payment->status) && $payment->status == 1 && $payment->data->payment_id) {
                return redirect(route("Payments.Pay", [
                    'return_url' => route("Payments.Show", ['payment_id' => $payment->data->payment_id]),
                    'payment_id' => $payment->data->payment_id,
                ]));
            }else {
                flash("Payment creation request was not accepted due to some error.", "danger");

                return redirect(route("Home"));
            }
        }else {
            flash("You cannot create an empty request.", "danger");

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
        $amount = floatval($request->get('amount'));
        $unit = $request->get('unit');
        $userEmail = $request->get('email');
        $user = $userEmail ? User::whereEmail($userEmail)->first() : null;

        // If amount is less than 0
        if ($amount < 0) {
            flash("Please enter an amount to start transfer", "danger");

            return redirect()->back()->withInput($request->all());
        }

        /**
         * If user not found
         */
        if ( ! $user) {
            flash("Please enter a valid user email address to start transfer", "danger");

            return redirect()->back()->withInput($request->all());
        }

        $priceIota = (new Iota())->convertToUnit($amount, $unit, 'I');

        $payment = (new Iota())->call([
            'METHOD' => 'POST',
            'URL'    => route("Api.Payments.Create", ['api_token' => $user->token()]),
            'DATA'   => [
                'invoice_id'        => 'TRANSFER-' . time(),
                'price_usd'         => '',
                'price_iota'        => $priceIota,
                'ipn'               => '',
                'ipn_verify_code'   => '',
                'custom_var_type'   => 'TRANSFER',
                'custom_var_sender' => auth()->user()->email,
                'sender_id'         => auth()->user()->id,
            ]
        ]);

        if ($payment && isset($payment->status) && $payment->status == 1 && $payment->data->payment_id) {

            // Add sender id
           /* Payment::whereId(base64_decode($payment->data->payment_id))->update([
                'sender_id' => auth()->user()->id
            ]);*/

            return redirect(route("Payments.Pay", [
                'return_url' => route("Payments.Show", ['payment_id' => $payment->data->payment_id]),
                'payment_id' => $payment->data->payment_id,
            ]));
        }else {
            flash("Transfer request was not accepted due to some error.", "danger");

            return redirect()->back()->withInput($request->all());
        }
    }
}
