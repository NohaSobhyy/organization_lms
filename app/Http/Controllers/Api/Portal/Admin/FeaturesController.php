<?php

namespace App\Http\Controllers\Api\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\PortalFeature;
use App\Models\Api\PortalPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeaturesController extends Controller
{
    // Get all features
    public function index()
    {
        $features = PortalFeature::all();
        return response()->json($features);
    }

    // Get single feature
    public function show($id)
    {
        $feature = PortalFeature::find($id);

        if (!$feature) {
            return response()->json(['message' => 'Feature not found'], 404);
        }

        return response()->json($feature);
    }

    // Create feature
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feature = PortalFeature::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Feature created successfully',
            'data' => $feature
        ], 201);
    }

    // Update feature
    public function update(Request $request, $id)
    {
        $feature = PortalFeature::find($id);

        if (!$feature) {
            return response()->json(['message' => 'Feature not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'name_ar' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feature->update($request->only('name', 'name_ar', 'description'));

        return response()->json([
            'message' => 'Feature updated successfully',
            'data' => $feature
        ]);
    }

    // Delete feature
    public function destroy($id)
    {
        $feature = PortalFeature::find($id);

        if (!$feature) {
            return response()->json(['message' => 'Feature not found'], 404);
        }

        $feature->delete();

        return response()->json(['message' => 'Feature deleted successfully']);
    }
}
