<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade

class GreetController extends Controller
{
  
    
    public function dontGreet()
    {
        $my_var = 'Hi bro';
        return response()->json(['message' => $my_var]);
    }

    public function insert()
    {
        DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            
        ]);
        return response()->json(['message' => "done"]);
    }

  
}
