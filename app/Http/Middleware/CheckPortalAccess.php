<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Portal;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        try {
            // Get company name from route parameters
            $companyName = $request->route('company_name');

            if (!$companyName) {
                Log::warning('Company name not found in route parameters');
                return response()->json(['message' => 'Company name is required'], 400);
            }

            // Find the portal
            $portal = Portal::where('bussiness_name', $companyName)->first();

            if (!$portal) {
                Log::warning('Portal not found', ['company_name' => $companyName]);
                return response()->json(['message' => 'Portal not found'], 404);
            }

            // Get the authenticated user
            $user = auth('api')->user();
            
            if (!$user) {
                Log::warning('Unauthorized access attempt - No authenticated user');
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Check if this is a portal admin
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();
            
            if ($payload->get('is_portal_admin') === true) {
                // This is a portal admin
                $portalId = $payload->get('portal_id');
                
                if ($portalId !== $portal->id) {
                    Log::warning('Portal access denied - Different portal', [
                        'authenticated_portal_id' => $portalId,
                        'requested_portal_id' => $portal->id
                    ]);
                    return response()->json(['message' => 'You do not have access to this portal'], 403);
                }
                
                // Set the portal as the authenticated user
                $portalUser = Portal::find($portalId);
                Auth::setUser($portalUser);
                return $next($request);
            }

            // If not a portal admin, check user access
            if ($user->role_id === 18) {
                Log::warning('Access denied - Employee role not allowed', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Employees are not allowed to access this resource.'
                ], 403);
            }

            // Check user access
            $isSuperAdmin = $user->role_id === 2;
            $isTeamLeader = $user->role_id === 17;

            // Check if user belongs to the portal
            if ($user->organ_id !== $portal->id && !$isSuperAdmin) {
                Log::warning('User does not belong to portal', [
                    'user_id' => $user->id,
                    'portal_id' => $portal->id
                ]);
                return response()->json(['message' => 'You do not have access to this portal'], 403);
            }

            // Allow access for all other users
            Log::info('Access granted to portal', [
                'user_id' => $user->id,
                'portal_id' => $portal->id,
                'role_id' => $user->role_id
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error in CheckPortalAccess middleware', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
