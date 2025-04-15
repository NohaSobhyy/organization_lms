<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display a listing of departments.
     *
     * @param Request $request
     * 
     */
    public function index(Request $request)
    {
        $portals = Portal::when($request->company_name, function ($query) use ($request) {
            $query->where('bussiness_name', $request->company_name);
        })->get();
        $portals->transform(function ($portal) {
            if ($portal->logo) {
                $portal->logo = asset('storage/' . $portal->logo);
            }
            return $portal;
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Portal updated successfully.',
            'data' => $portals,
        ]);
    }


    public function update(Request $request, $company_name, $id)
    {
        $portal = Portal::where('bussiness_name', $company_name)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'bussiness_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'password' => 'nullable|string|min:6',
            'phone' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'facebook' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string',
            'other_link' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'password') {
                $portal->password = bcrypt($value);
            } else {
                $portal->$key = $value;
            }
        }

        $portal->save();

        Log::info('Portal updated successfully', [
            'portal_id' => $portal->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Portal updated successfully.',
            'data' => $portal
        ]);
    }
}
