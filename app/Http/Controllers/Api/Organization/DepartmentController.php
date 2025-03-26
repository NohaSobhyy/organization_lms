<?php

namespace App\Http\Controllers\Api\Organization;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Portal;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return response()->json(Department::all());
    }

    public function store(Request $request, $company_name)
    {
        $portal = Portal::where('business_name', $company_name)->first();
        if (!$portal) {
            return response()->json(['error' => 'Portal not found'], 404);
        }
        
        $department = Department::create([
            'name' => $request->name,
            'portal_id' => $portal->id,
        ]);
        
        return response()->json($department, 201);
    }

    public function update(Request $request, Department $Department)
    {
        return response()->json(['error' => 'Unauthorized. Please contact the admin.'], 403);
    }

    public function destroy(Department $Department)
    {
        return response()->json(['error' => 'Unauthorized. Please contact the admin.'], 403);
    }

    public function addUserToDepartment(Request $request, Department $department)
    {
        $portal = Portal::find($department->portal_id);
        if (!$portal) {
            return response()->json(['error' => 'Portal not found'], 404);
        }

        $currentUserCount = $department->users()->count();

        if ($currentUserCount >= $portal->max_users) {
            return response()->json(['error' => 'Department user limit reached'], 400);
        }

        $department->users()->attach($request->user_id);
        return response()->json(['message' => 'User added successfully','current_users' => $currentUserCount]);
    }
}
