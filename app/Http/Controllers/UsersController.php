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
            'updateLastKeyIndex'
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

        return view("users.account", compact("user"));
    }

    /**
     * Update last key index
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLastKeyIndex(Request $request)
    {
        $response = [
            'status' => 0
        ];

        $seed = $request->get("seed");
        $address = $request->get("address");
        $lastKeyIndex = $request->get("key_index");

        if ($seed && $address) {
            $user = User::whereIotaSeed($seed)->first();

            if ($user) {
                $user->update([
                    "last_key_index" => $lastKeyIndex
                ]);

                $user->addresses()->firstOrCreate([
                    'address'   => $address,
                    'key_index' => $lastKeyIndex,
                ]);

                $response['status'] = 1;
            }
        }

        return response()->json($response);

    }

    /**
     * Get user last key index
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastKeyIndex(Request $request)
    {
        $response = [
            'status' => 0
        ];

        $seed = $request->get("seed");

        if ($seed) {
            $user = User::whereIotaSeed($seed)->select('last_key_index')->first();

            if ($user) {
                $response['status'] = 1;
                $response['key_index'] = $user->last_key_index;
            }
        }

        return response()->json($response);
    }

}