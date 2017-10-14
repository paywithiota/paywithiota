<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

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
        $query = trim($request->get('term', ''));

        if ($query) {

            // Find user match with search term
            $users = User::where('email', 'LIKE', '%' . $query . '%')->get();

            foreach ($users as $user) {
                $response['data'][] = [
                    'value' => $user->email
                ];
            }
        }

        return response()->json($response);
    }

}