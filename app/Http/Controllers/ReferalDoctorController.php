<?php

namespace App\Http\Controllers;

use App\Models\ReferalDoctor;
use Illuminate\Http\Request;

class ReferalDoctorController extends Controller
{
    public function index()
    {
        return view('referaldoctor.index'); // make sure this file exists
    }

    public function create()
    {
        return view('referaldoctor.create');
    }
}
