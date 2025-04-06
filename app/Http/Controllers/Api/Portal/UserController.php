<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @param string $company_name
     * @return JsonResponse
     */
    public function index(string $company_name): JsonResponse
    {
        try {
            Log::info('Fetching users list', ['company_name' => $company_name]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            $users = User::where('organ_id', $portal->id)
                ->select(['id', 'full_name', 'role_name', 'mobile', 'email', 'bio', 'created_at'])
                ->get();

            return $this->successResponse('Users retrieved successfully', $users);
        } catch (\Exception $e) {
            Log::error('Error fetching users', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to fetch users');
        }
    }

    /**
     * Store a newly created user.
     *
     * @param Request $request
     * @param string $company_name
     * @return JsonResponse
     */
    public function store(Request $request, string $company_name): JsonResponse
    {
        try {
            Log::info('Creating new user', [
                'company_name' => $company_name,
                'request_data' => $request->all()
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            $validator = $this->validateUserData($request);
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            if (!$this->checkUserLimit($portal)) {
                Log::warning('User limit reached', [
                    'portal_id' => $portal->id,
                    'current_count' => User::where('organ_id', $portal->id)->count(),
                    'max_users' => $portal->max_users
                ]);
                return $this->errorResponse('User limit reached for this portal', 403);
            }

            $user = $this->createUser($request, $portal);
            Log::info('User created successfully', [
                'user_id' => $user->id,
                'portal_id' => $portal->id
            ]);

            return $this->successResponse('User created successfully', $user, 201);
        } catch (QueryException $e) {
            Log::error('Database error while creating user', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return $this->errorResponse('Database error occurred while creating user');
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to create user');
        }
    }

    /**
     * Display the specified user.
     *
     * @param string $company_name
     * @param User $user
     * @return JsonResponse
     */
    public function show(string $company_name, User $user): JsonResponse
    {
        try {
            Log::info('Fetching user details', [
                'company_name' => $company_name,
                'user_id' => $user->id
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyUserBelongsToPortal($user, $portal)) {
                return $this->errorResponse('User does not belong to this portal', 403);
            }

            return $this->successResponse('User retrieved successfully', $user);
        } catch (\Exception $e) {
            Log::error('Error fetching user', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to fetch user');
        }
    }

    /**
     * Update the specified user.
     *
     * @param Request $request
     * @param string $company_name
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, string $company_name, User $user): JsonResponse
    {
        try {
            Log::info('Attempting to update user', [
                'company_name' => $company_name,
                'user_id' => $user->id
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyUserBelongsToPortal($user, $portal)) {
                return $this->errorResponse('User does not belong to this portal', 403);
            }

            return $this->errorResponse('User updates are not allowed. Please contact the admin for any changes.', 403);
        } catch (\Exception $e) {
            Log::error('Error in update method', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('An error occurred while processing your request');
        }
    }

    /**
     * Remove the specified user.
     *
     * @param string $company_name
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(string $company_name, User $user): JsonResponse
    {
        try {
            Log::info('Attempting to delete user', [
                'company_name' => $company_name,
                'user_id' => $user->id
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyUserBelongsToPortal($user, $portal)) {
                return $this->errorResponse('User does not belong to this portal', 403);
            }

            $user->delete();
            Log::info('User deleted successfully', ['user_id' => $user->id]);

            return $this->successResponse('User deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error in destroy method', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('An error occurred while processing your request');
        }
    }

    /**
     * Get portal by business name
     *
     * @param string $company_name
     * @return Portal|null
     */
    private function getPortal(string $company_name): ?Portal
    {
        return Portal::where('bussiness_name', $company_name)->first();
    }

    /**
     * Validate user data
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateUserData(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'role_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|regex:/^[0-9]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'bio' => 'nullable|string'
        ], [
            'full_name.required' => 'Full name is required',
            'full_name.max' => 'Full name must not exceed 255 characters',
            'role_name.required' => 'Role name is required',
            'role_name.max' => 'Role name must not exceed 255 characters',
            'mobile.required' => 'Mobile number is required',
            'mobile.regex' => 'Mobile number must contain only digits',
            'mobile.max' => 'Mobile number must not exceed 15 digits',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters long',
        ]);
    }

    /**
     * Check if portal has reached user limit
     *
     * @param Portal $portal
     * @return bool
     */
    private function checkUserLimit(Portal $portal): bool
    {
        $userCount = User::where('organ_id', $portal->id)->count();
        return $userCount < $portal->max_users;
    }

    /**
     * Create new user
     *
     * @param Request $request
     * @param Portal $portal
     * @return User
     * @throws \Exception
     */
    private function createUser(Request $request, Portal $portal): User
    {
        try {
            $userData = [
                'full_name' => $request->full_name,
                'role_name' => $request->role_name,
                'role_id' => 18, // Default role ID (role: employee)
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'bio' => $request->bio,
                'organ_id' => $portal->id,
                'created_at' => time(),
                'updated_at' => time()
            ];

            Log::info('Attempting to create user with data', ['user_data' => $userData]);

            $user = User::create($userData);
            Log::info('User created successfully', ['user_id' => $user->id]);

            return $user;
        } catch (QueryException $e) {
            Log::error('Database error in createUser', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error in createUser', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Verify user belongs to portal
     *
     * @param User $user
     * @param Portal $portal
     * @return bool
     */
    private function verifyUserBelongsToPortal(User $user, Portal $portal): bool
    {
        return $user->organ_id === $portal->id;
    }

    /**
     * Verify department belongs to portal
     *
     * @param int $department_id
     * @param Portal $portal
     * @return bool
     */
    private function verifyDepartmentBelongsToPortal(int $department_id, Portal $portal): bool
    {
        return \App\Models\Department::where('id', $department_id)
            ->where('portal_id', $portal->id)
            ->exists();
    }

    /**
     * Return success response
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    private function successResponse(string $message, $data = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    private function errorResponse(string $message, int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }

    /**
     * Return validation error response
     *
     * @param \Illuminate\Support\MessageBag $errors
     * @return JsonResponse
     */
    private function validationErrorResponse(\Illuminate\Support\MessageBag $errors): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'errors' => $errors
        ], 422);
    }
}
