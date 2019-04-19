<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth')->except(['authentication']);
	}

	public function index()
	{
		return view('booking.index');
	}

	public function authentication()
	{
		return view('authentication.index');
	}

	public function bookings()
	{
		return view('booking.bookings');
	}

	public function test()
	{
		return view('test.index');
	}
}
