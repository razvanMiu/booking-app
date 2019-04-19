<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function login()
    {
      if(! auth()->attempt(request(['email', 'password'])))
      {
          return response()->json([
            'authentication' => false,
          ]);
      }

      return response()->json([
        'authentication' => true,
      ]);
    }
}
