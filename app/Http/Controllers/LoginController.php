<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Userr;

class LoginController extends Controller
{
    public function index()
    {
        return view('login'); 
    }

   
    
}

