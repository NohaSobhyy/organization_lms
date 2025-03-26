<?php

namespace App\Http\Controllers\Api\Organization;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request, $company_name)
    {
        $portal = Portal::where('business_name', $company_name)->first();
        if (!$portal) {
            return response()->json(['error' => 'Portal not found'], 404);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'role_name' => $request->role_name,
            'role_id' => '1',
            'organ_id' => $portal->id,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'bio' => $request->bio,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        return response()->json(['error' => 'Unauthorized. Please contact the admin.'], 403);
    }

    public function destroy(User $user)
    {
        return response()->json(['error' => 'Unauthorized. Please contact the admin.'], 403);
    }
}
