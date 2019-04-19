<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Building;
use App\Floor;

class BuildingsController extends Controller
{
    public function show(Building $building)
    {
    	return view('booking.show', compact('building'));
    }

    public function fetch($building)
    {   
    	return [ 
            'building' => Building::with(['floors', 'floors.rooms'])->find($building),
        ];
    }
}
