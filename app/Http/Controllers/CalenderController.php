<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalenderController extends Controller
{
    public function index()
    {
        return view('calender.index'); 
    }
    public function create()
    {
        return view('calender.create'); 
    }
}
