<?php

namespace App\Http\Controllers;

use App\User;
use App\Util\Iota;
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

        return view('addresses.index', compact('addresses', 'user'));
    }


    /**
     * Create new address
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        // Index
        $addressIndex = $request->get('index') ? $request->get('index') : $user->addresses()->count();

        // Iota Address
        $address = (new Iota())->generateAddress($user->iota_seed, $addressIndex);

        if ($address) {
            if ($user->addresses()->whereAddress($address)->count() > 0) {
                $addressIndex += 1;

                return redirect(route("Addresses.Create", ["index" => $addressIndex]));
            }
        }

        // Save Address
        if ($address) {

            $address = auth()->user()->addresses()->create([
                'address' => $address
            ]);

            flash("A new address " . $address->address . " has been created successfully.", "success");

            return redirect(route("Addresses"));
        }else {
            flash("Address was not created due to some error.", "danger");

            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @param         $address
     */
    public function show(Request $request, $address)
    {
        $address = auth()->user()->addresses()->whereId($address)->first();

        if ($address) {
            return view('addresses.show', compact('address'));
        }else {
            return redirect(route("Addresses"));
        }
    }
}
