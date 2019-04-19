<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Booking;
use App\User;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function __construct()
    {
             $this->middleware('auth');
             $this->middleware('is_admin');
    }

    public function show()
    {
      return view('admin.history.index');
    }

    public function fetch()
    {       
      $bookings = Booking::all();
      foreach ($bookings as $booking) 
      {
        // $date = new Carbon($booking->endDate['date'], 'Europe/Bucharest');
        
        if(count($booking->dow) == 0 || is_null($booking->dow)) {
          $date = new Carbon($booking->endDate, 'Europe/Bucharest');  
          if($date->isPast() && $booking->status != 'finished')
          {
            $booking->status = 'finished';
            $booking->save();
          }
        }
      }
      
      return response()->json([
          'history' => Booking::with(['user', 'room'])->get()
      ]);        
    }

    public function update()
    {
        $booking = Booking::find(request('booking'))->first();
        $booking->status = request('status');
        $booking->save();
    }
}
