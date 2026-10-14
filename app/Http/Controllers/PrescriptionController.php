<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index()
    {
        return view('prescription.index'); 
    }
    public function create()
    {
        return view('prescription.create'); 
    }
}
