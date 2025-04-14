<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use App\Models\Api\PortalBill;
use App\Models\Api\PortalPlan;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    public function store(Request $request, $business_name)
    {
        $portal = Portal::where('bussiness_name', $business_name)->first();

        if (!$portal) {
            return response()->json(['message' => 'Portal not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'bill' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store uploaded bill in public/store/bills
        $path = $request->file('bill')->store('bills', 'public');

        $portalBill = PortalBill::create([
            'name' => $validated['name'],
            'bill' => $path,
            'portal_id' => $portal->id,
        ]);

        return response()->json([
            'message' => 'Bill uploaded successfully.',
            'data' => $portalBill,
            'bill_url' => asset('storage/' . $portalBill->bill),
        ], 201);
    }

    public function getAllBillsAndFeatures()
    {
        try {
            $portalBills = PortalBill::all();
            $response = [];

            foreach ($portalBills as $bill) {
                $portal = Portal::find($bill->portal_id);

                if ($portal) {
                    $planId = $portal->plan_id;
                    $plan = PortalPlan::find($planId);

                    if ($plan) {
                        $features = $plan->features;  
                        $response[] = [
                            'bill' => $bill,
                            'plan' => $plan,
                            'features' => $features,
                        ];
                    }
                }
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
