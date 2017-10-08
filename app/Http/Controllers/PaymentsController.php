<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        $addresses = auth()->user()->addresses()->orderby('id', 'asc')->get();

        return view('payments.addresses', compact('addresses'));
    }
}
