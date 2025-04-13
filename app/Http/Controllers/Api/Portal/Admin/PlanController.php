<?php

namespace App\Http\Controllers\Api\Portal\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Api\PortalPlan;
use App\Models\Portal;

class PlanController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'portal_id' => ['required', 'exists:portals,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ], [
            'name.required' => 'Plan name is required',
            'name_ar.required' => 'Arabic Plan name is required',
            'type.in' => 'Plan type must be either Basic, Pro, or Enterprise',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $plan = new PortalPlan();
        $plan->name = $request->name;
        $plan->name_ar = $request->name_ar;
        $plan->description = $request->description;
        $plan->type = 'Enterprise';
        $plan->start_date = $request->start_date;
        $plan->end_date = $request->end_date;
        $plan->save();

        $portal = Portal::find($request->portal_id);
        $portal->plan_id = $plan->id;
        $portal->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Plan created successfully',
            'data' => $plan
        ], 201);
    }

    public function assignFeatures(Request $request)
    {
        $request->validate([
            'portal_id' => 'required|exists:portal_plans,id',
            'features' => 'required|array',
            'features.*' => 'exists:portal_features,id',
        ]);

        $plan = PortalPlan::find($request->portal_id);
        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $plan->features()->sync($request->features);

        return response()->json([
            'message' => 'Features assigned successfully',
            'data' => $plan->features
        ]);
    }
}
