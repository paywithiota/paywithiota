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
    public function findByEmail(Request $request)
    {
        $response = [
            'data' => []
        ];

        // Get requested term
        $query = trim(str_replace('%', '', $request->get('term', '')));

        if ($query) {

            // Find user match with search term
            $users = User::where('email', 'LIKE', '%' . $query . '%')->where('!=', auth()->user()->id)->get();

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

        return view("users.account", compact("user"));
    }

}