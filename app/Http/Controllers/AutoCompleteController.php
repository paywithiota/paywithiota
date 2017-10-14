<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AutoCompleteController extends Controller {

    /*
     * Search User email to database
     * and show in autocomplete input
     */
    public function searchUserEmail(Request $request) {

        // Get requested term
        $query = $request->get('term','');

        // Find user match with search term
        $users = User::where('email','LIKE','%'.$query.'%')->get();

        $data=array();
        foreach ($users as $user) {
            $data[]=array('value'=>$user->email,'id'=>$user->id);
        }
        if(count($data)){

            return $data;
        }else{
            return ['value'=>'No Result Found','id'=>''];
        }
    }

}