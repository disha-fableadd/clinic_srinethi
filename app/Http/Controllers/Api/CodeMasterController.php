<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CodeMaster;
use Illuminate\Http\Request;

class CodeMasterController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id');
        $query = CodeMaster::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'branch_id' => 'required'
        ]);

        $codeMaster = CodeMaster::create($request->all());

        return response()->json([
            'message' => 'Code created successfully!',
            'data' => $codeMaster
        ]);
    }

    public function show($id)
    {
        $codeMaster = CodeMaster::findOrFail($id);
        return response()->json($codeMaster);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $codeMaster = CodeMaster::findOrFail($id);
        $codeMaster->update($request->all());

        return response()->json([
            'message' => 'Code updated successfully!',
            'data' => $codeMaster
        ]);
    }

    public function destroy($id)
    {
        $codeMaster = CodeMaster::findOrFail($id);
        $codeMaster->delete();

        return response()->json([
            'message' => 'Code deleted successfully!'
        ]);
    }
}
