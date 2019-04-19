<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
      $this->validate(request(), [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed'
      ]);

      $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => bcrypt(request('password'))
      ]);

      auth()->login($user);
    }
}
