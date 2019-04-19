<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function fetch()
    {
		$user = Auth::user();

        foreach($user->bookings as $booking) {

            if(count($booking->dow) == 0 || $booking->dow == null) {
               $date = new Carbon($booking->end, 'Europe/Bucharest');
            
                if($date->isPast() && $booking->status != 'finished') {
                    $booking->status = 'finished';
                    $booking->save();
                } 
            }
        }

		return response()->json([
			'_id' 		=> $user->_id,
			'name' 		=> $user->name,
			'email' 	=> $user->email,
			'type'  	=> $user->type,
			'bookings'	=> $user->bookings,
		]);
    }

    public function fetchUser(User $user) 
    {
    		return $user;
    }
}
