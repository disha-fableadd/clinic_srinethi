<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
  public function index()
    {
        return view('machine.index'); 
    }
    public function create()
    {
        return view('machine.create'); 
    }


    public function getByBranch($branchId)
    {
        $machine = Machine::where('branch_id', $branchId)
            ->select('id', 'name')
            ->get();

        return response()->json($machine);
    }
}
