<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressesController extends Controller
{
    /**
     * List user's all address
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $addresses = $user->addresses()->orderby('id', 'asc')->get();

        return view('payments.addresses', compact('addresses', 'user'));
    }
}
