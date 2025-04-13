<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\Portal;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ];

        validateParam($request->all(), $rules);

        return $this->attemptLogin($request);
    }

    public function username()
    {
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

        if (empty($this->username)) {
            $this->username = 'mobile';
            if (preg_match($email_regex, request('username', null))) {
                $this->username = 'email';
            }
        }
        return $this->username;
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ];

        // Try to authenticate as a user first
        if ($token = auth('api')->attempt($credentials)) {
            return $this->afterLogged($request, $token);
        }

        // If user authentication fails, try portal authentication
        $portal = Portal::where('email', $request->email)->first();
        
        if ($portal && Hash::check($request->password, $portal->password)) {
            // Generate a JWT token for the portal
            $token = JWTAuth::fromUser($portal);
            
            // Set the portal as the authenticated user
            auth('api')->setUser($portal);
            
            return $this->afterLogged($request, $token, false, true);
        }

        return apiResponse2(0, 'invalid', "invalid email or password");
    }

    public function afterLogged(Request $request, $token, $verify = false, $isPortal = false)
    {
        $user = auth('api')->user();

        if ($isPortal) {
            $data['token'] = $token;
            $data['portal'] = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "status" => $user->status,
                "business_name" => $user->bussiness_name
            ];

            return apiResponse2(1, 'login', "portal login successfully", $data);
        }

        if ($user->ban) {
            $time = time();
            $endBan = $user->ban_end_at;
            if (!empty($endBan) and $endBan > $time) {
                auth('api')->logout();
                return apiResponse2(0, 'banned_account', "your account has been banned");
            } elseif (!empty($endBan) and $endBan < $time) {
                $user->update([
                    'ban' => false,
                    'ban_start_at' => null,
                    'ban_end_at' => null,
                ]);
            }
        }

        if ($user->status != User::$active and !$verify) {
            // auth('api')->logout();
            auth('api')->logout();
            return apiResponse2(0, 'inactive_account', trans('auth.inactive_account'));
            //  dd(apiAuth());
            // $verificationController = new VerificationController();
            // $checkConfirmed = $verificationController->checkConfirmed($user, 'email', $request->input('email'));

            // if ($checkConfirmed['status'] == 'send') {

            //     return apiResponse2(0, 'not_verified', "can't login before verify your acount");

            // } elseif ($checkConfirmed['status'] == 'verified') {
            //     $user->update([
            //         'status' => User::$active,
            //     ]);
            // }
        } elseif ($verify) {
            $user->update([
                'status' => User::$active,
            ]);
        }

        if ($user->status != User::$active) {
            \auth('api')->logout();
            return apiResponse2(0, 'inactive_account', trans('auth.inactive_account'));
        }

        $profile_completion = [];
        $data['token'] = $token;
        $businessName = optional($user->portal)->bussiness_name;
        $data['user'] = [
            "id" => $user->id,
            "full_name" => $user->full_name,
            "role_name" => $user->role_name,
            "user_code" => $user->user_code,
            "mobile" => $user->mobile,
            "email" => $user->email,
            "status" => $user->status,
            "as_student" => $user->student,
            "business_name" => $businessName
        ];
        if (!$user->full_name) {
            $profile_completion[] = 'full_name';
            $data['profile_completion'] = $profile_completion;
        }

        return apiResponse2(1, 'login', "user login successfully", $data);
    }

    public function logout()
    {
        auth('api')->logout();
        if (!apiAuth()) {
            return apiResponse2(1, 'logout', trans('auth.logout'));
        }
        return apiResponse2(0, 'failed', trans('auth.logout.failed'));
    }
}
