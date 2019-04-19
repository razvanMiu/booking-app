<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;

class RoomsController extends Controller
{
    public function fetch($room)
    {	
    	// dd(Room::with('bookings')->find($room));
		return Room::with('bookings')->find($room);
    }
}
