<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return view('branch.index'); 
    }

    public function create()
    {
        return view('branch.create'); 
    }
}
