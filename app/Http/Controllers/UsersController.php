<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $except = [

        ];

        $this->middleware('auth')->except($except);
    }

    /**
     * Search user by email address
     *
     * @param Request $request
     *
     * @return array
     */
    public function searchUserEmail(Request $request)
    {
        $response = [
            'data' => []
        ];

        // Get requested term
        $searchKeyword = trim(str_replace('%', '', $request->get('term', '')));

        if ($searchKeyword) {

            $searchKeyword = str_replace(' ', '%', $searchKeyword);

            // Find user match with search term
            $users = User::where('id', '!=', auth()->user()->id)->where(function ($query) use ($searchKeyword){
                return $query->where('email', 'LIKE', '%' . $searchKeyword . '%')
                             ->orWhere('name', 'LIKE', '%' . $searchKeyword . '%');
            })->groupBy('id')->select(['id', 'email'])->limit(6)->get();

            foreach ($users as $user) {
                $response['data'][] = [
                    'value' => $user->email
                ];
            }
        }

        return response()->json($response);
    }

    /**
     * Get User account data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAccountData()
    {
        $user = auth()->user();

        // Addresses
        $totalAddresses = $user->addresses()->count();
        return view("users.account", compact("user", "totalAddresses"));
    }

}