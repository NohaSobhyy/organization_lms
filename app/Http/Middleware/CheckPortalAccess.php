<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Portal;

class CheckPortalAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the company name from the route parameters
        $companyName = $request->route('company_name');

        if (!$companyName) {
            Log::error('No company name found in route parameters');
            return response()->json(['error' => 'Invalid portal access'], 403);
        }

        if (!Auth::check()) {
            Log::error('User not authenticated');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();


        if ($user->role_id == 18) {
            Log::error('Access denied for role_id 18'); // Role 18 is employee
            return response()->json(['error' => 'Access denied for this role'], 403);
        }

        // Find portal by business name
        $portal = Portal::where('bussiness_name', $companyName)->first();

        if (!$portal) {
            Log::error('Portal not found for business name: ' . $companyName);
            return response()->json(['error' => 'Portal not found'], 404);
        }

        // Special handling for updateUserRole route to allow only Super Admin and Organizer
        if ($request->is('*/departments/*/users/role')) {
            if (!in_array($user->role_id, [2, 19])) {
                Log::error('Access denied for updateUserRole route. Role_id: ' . $user->role_id);
                return response()->json(['error' => 'Access denied for this action'], 403);
            }

            if ($user->role_id == 19 && $user->organ_id != $portal->id) {
                Log::error('Portal admin trying to access different portal');
                return response()->json(['error' => 'Access denied to this portal'], 403);
            }

            $request->merge(['portal' => $portal]);
            return $next($request);
        }

        // Regular route handling
        // Check if user has allowed role (2, 17, or 19) (Super Admin, Team Leader, Portal Admin)
        if (!in_array($user->role_id, [2, 17, 19])) {
            Log::error('Access denied for role_id: ' . $user->role_id);
            return response()->json(['error' => 'Access denied for this role'], 403);
        }

        // For super admin (role_id 2), allow access to any portal
        if ($user->role_id == 2) {
            $request->merge(['portal' => $portal]);
            return $next($request);
        }

        if ($user->role_id == 19 && $user->organ_id != $portal->id) {
            Log::error('Portal admin trying to access different portal');
            return response()->json(['error' => 'Access denied to this portal'], 403);
        }

        if ($user->role_id == 17 && $user->organ_id != $portal->id) {
            Log::error('Team leader trying to access different organization portal');
            return response()->json(['error' => 'Access denied to this portal'], 403);
        }

        $request->merge(['portal' => $portal]);

        return $next($request);
    }
}
