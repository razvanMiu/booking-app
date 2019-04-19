<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
	public function __construct()
	{
		 $this->middleware('auth');
		 $this->middleware('is_admin')->except('destroy');
	}

    public function index()
    {
      return view('admin.index');
    }

    public function destroy()
    {
    	auth()->logout();

    	return redirect('/');
    }

}
