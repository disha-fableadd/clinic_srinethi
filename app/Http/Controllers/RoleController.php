<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
class RoleController extends Controller
{
    public function index()
    {
        
    
        return view('role.index', );
    }
    
    
    public function create()
    {
        return view('role.create');
    }

}
