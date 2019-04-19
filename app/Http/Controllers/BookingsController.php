<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBooking;
use App\Booking;

class BookingsController extends Controller
{
	public function store(StoreBooking $request)
	{	
		$request->validated();

		if($request->user['_id'] != auth()->user()->_id) {
			// TODO:: Check if event overlap and is in correct format
			return response()->json([
				'error' => 'Something went wrong. Please refresh the page!'
			]);
		} 

		$booking = new Booking;
		$booking->description 	= $request->event['description'];
		$booking->user_id 		= $request->user['_id'];
		$booking->room_id 		= $request->room['_id'];
		$booking->title 		= $request->event['title'];
		$booking->start 		= $request->event['start'];
		$booking->dow  			= $request->event['dow'];
		$booking->end 			= $request->event['end'];
		$booking->status 		= 'available';


		if(count($booking->dow) > 0 ) {
			$booking->ranges = $request->event['ranges'];
		} else {
			$booking->ranges = [];
		}

		$booking->save();

		return response()->json([
			'bookingId' => $booking->_id,
		]);
	}

	public function update(Booking $booking, StoreBooking $request)
	{	
		$request->validated();

		if($request->user['_id'] == auth()->user()->_id) {
			return response()->json([
				'error' => 'Something went wrong. Please refresh the page!'
			]);
		} 

		$booking->description 	= $request->event['description'];
		$booking->ranges 		= $request->event['ranges'];
		$booking->status 		= $request->event['status'];
		$booking->title 		= $request->event['title'];
		$booking->start 		= $request->event['start'];
		$booking->dow 			= $request->event['dow'];
		$booking->end			= $request->event['end'];

		$booking->save();
	}

	public function delete(Booking $booking)
	{
		$booking->delete();
	}
}
