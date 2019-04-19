<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmInvitation;
use App\Room;
use App\Booking;

class MailsController extends Controller
{

	public function show(Booking $booking)
	{	
		$email 	  = 'razvan@yahoo.com';	
		$room     = Room::find($booking->room_id);
		$start    = new \Carbon\Carbon($booking->startDate['date']); 
		$end      = new \Carbon\Carbon($booking->endDate['date']); 

		return view('emails.confirm', [
			'email'		=> $email,
			'booking' 	=> $booking,
			'room' 		=> $room,
			'start' 	=> $start,
			'end'		=> $end,
		]);
	}

  	public function invitation(Booking $booking, Request $request)
  	{		
		// foreach($request->emails as $email) {
		// 	Mail::to($email)->send(new ConfirmInvitation($booking, $email, $request->dateRange, $request->duration, $request->eventOP, $request->room));
		// }
		$email = 'razvan@yahoo.com';
		Mail::to($request->emails)->send(new ConfirmInvitation($booking, $email, $request->dateRange, $request->duration, $request->eventOP, $request->room));
	}

	public function confirm(Booking $booking, Request $request)
	{	
		$booking->push('participants', [
			'name' 	=> $request->name, 
			'email' 	=> $request->email,
			'phone' 	=> $request->number
		]);
	}
}
