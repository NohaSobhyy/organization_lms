<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Portal;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Models\Role;
class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $departments = Department::with(['users'])
                ->when($request->company_name, function ($query) use ($request) {
                    $query->whereHas('portal', function ($q) use ($request) {
                        $q->where('bussiness_name', $request->company_name);
                    });
                })
                ->get();

            if ($departments->isEmpty()) {
                return $this->successResponse('No departments found', [], 200);
            }

            return $this->successResponse('Departments retrieved successfully', $departments);
        } catch (\Exception $e) {
            Log::error('Failed to fetch departments', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to fetch departments');
        }
    }

    /**
     * Store a newly created department.
     *
     * @param Request $request
     * @param string $company_name
     * @return JsonResponse
     */
    public function store(Request $request, string $company_name): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'name_ar' => ['required', 'string', 'max:255']
            ], [
                'name.required' => 'Department name is required',
                'name_ar.required' => 'Arabic department name is required'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            $department = Department::create([
                'name' => $request->name,
                'name_ar' => $request->name_ar,
                'portal_id' => $portal->id,
            ]);

            Log::info('Department created successfully', [
                'department_id' => $department->id,
                'portal_id' => $portal->id
            ]);

            return $this->successResponse('Department created successfully', $department, 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to create department', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to create department');
        }
    }

    /**
     * Update the specified department.
     *
     * @param Request $request
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function update(Request $request, string $company_name, Department $department): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'max:255'],
                'name_ar' => ['sometimes', 'string', 'max:255']
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            if (!$request->has('name') && !$request->has('name_ar')) {
                return $this->errorResponse('No fields to update provided', 400);
            }

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyDepartmentBelongsToPortal($department, $portal)) {
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            $updateData = [];
            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }
            if ($request->has('name_ar')) {
                $updateData['name_ar'] = $request->name_ar;
            }

            $department->update($updateData);

            Log::info('Department updated successfully', [
                'department_id' => $department->id,
                'updates' => $updateData
            ]);

            return $this->successResponse('Department updated successfully', $department);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to update department', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to update department');
        }
    }

    /**
     * Remove the specified department.
     *
     * @param Request $request
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function destroy(Request $request, string $company_name, Department $department): JsonResponse
    {
        try {
            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyDepartmentBelongsToPortal($department, $portal)) {
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            $userCount = $department->users()->count();
            if ($userCount > 0) {
                return $this->errorResponse('Cannot delete department with active users', 400);
            }

            $department->delete();

            Log::info('Department deleted successfully', [
                'department_id' => $department->id
            ]);

            return $this->successResponse('Department deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete department', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to delete department');
        }
    }

    /**
     * Add a user to the department.
     *
     * @param Request $request
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function addUserToDepartment(Request $request, string $company_name, Department $department): JsonResponse
    {
        try {
            Log::info('Adding user to department', [
                'company_name' => $company_name,
                'department_id' => $department->id,
                'request_data' => $request->all()
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyDepartmentBelongsToPortal($department, $portal)) {
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'is_team_leader' => ['sometimes', 'boolean']
            ], [
                'user_id.required' => 'User ID is required',
                'user_id.integer' => 'User ID must be an integer',
                'user_id.exists' => 'The selected user does not exist',
                'is_team_leader.boolean' => 'Team leader flag must be true or false'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = User::findOrFail($request->user_id);
            if (!$this->verifyUserBelongsToPortal($user, $portal)) {
                return $this->errorResponse('User does not belong to this portal', 403);
            }

            if ($department->users()->where('user_id', $user->id)->exists()) {
                return $this->errorResponse('User already belongs to this department', 400);
            }

            // Check if user is being set as team leader
            $isTeamLeader = $request->boolean('is_team_leader', false);

            if ($isTeamLeader) {
                // Set role_id to 17 for team leader
                $user->role_id = 17;
                $user->save();
            }

            $department->users()->attach($user->id);

            Log::info('User added to department successfully', [
                'user_id' => $user->id,
                'department_id' => $department->id,
                'is_team_leader' => $isTeamLeader
            ]);

            return $this->successResponse('User added to department successfully', [
                'current_users' => $department->users()->count(),
                'is_team_leader' => $isTeamLeader
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to add user to department', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to add user to department');
        }
    }

    /**
     * Remove a user from the department.
     *
     * @param Request $request
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function removeUserFromDepartment(Request $request, string $company_name, Department $department): JsonResponse
    {
        try {
            Log::info('Removing user from department', [
                'company_name' => $company_name,
                'department_id' => $department->id,
                'request_data' => $request->all()
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyDepartmentBelongsToPortal($department, $portal)) {
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => ['required', 'integer', 'exists:users,id']
            ], [
                'user_id.required' => 'User ID is required',
                'user_id.integer' => 'User ID must be an integer',
                'user_id.exists' => 'The selected user does not exist'
            ]);
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = User::findOrFail($request->user_id);
            if (!$this->verifyUserBelongsToPortal($user, $portal)) {
                return $this->errorResponse('User does not belong to this portal', 403);
            }

            if (!$department->users()->where('user_id', $user->id)->exists()) {
                return $this->errorResponse('User does not belong to this department', 404);
            }

            $department->users()->detach($user->id);

            Log::info('User removed from department successfully', [
                'user_id' => $user->id,
                'department_id' => $department->id
            ]);

            return $this->successResponse('User removed from department successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to remove user from department', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to remove user from department');
        }
    }

    /**
     * Get all users in a department.
     *
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function getDepartmentUsers(string $company_name, Department $department): JsonResponse
    {
        try {
            Log::info('Fetching department users', [
                'company_name' => $company_name,
                'department_id' => $department->id
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyDepartmentBelongsToPortal($department, $portal)) {
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            $users = $department->users()
                ->select([
                    'users.id',
                    'users.full_name',
                    'users.role_name',
                    'users.mobile',
                    'users.email',
                    'users.bio',
                    'users.created_at'
                ])
                ->get();

            return $this->successResponse('Department users retrieved successfully', $users);
        } catch (\Exception $e) {
            Log::error('Failed to fetch department users', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to fetch department users');
        }
    }

    /**
     * Update user role in department.
     *
     * @param Request $request
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function updateUserRole(Request $request, string $company_name, Department $department): JsonResponse
    {
        try {
            Log::info('Updating user role in department', [
                'company_name' => $company_name,
                'department_id' => $department->id,
                'request_data' => $request->all()
            ]);

            $portal = $this->getPortal($company_name);
            if (!$portal) {
                return $this->errorResponse('Portal not found', 404);
            }

            if (!$this->verifyDepartmentBelongsToPortal($department, $portal)) {
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'is_team_leader' => ['required', 'boolean']
            ], [
                'user_id.required' => 'User ID is required',
                'user_id.integer' => 'User ID must be an integer',
                'user_id.exists' => 'The selected user does not exist',
                'is_team_leader.required' => 'Team leader flag is required',
                'is_team_leader.boolean' => 'Team leader flag must be true or false'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = User::findOrFail($request->user_id);
            if (!$this->verifyUserBelongsToPortal($user, $portal)) {
                return $this->errorResponse('User does not belong to this portal', 403);
            }

            if (!$department->users()->where('user_id', $user->id)->exists()) {
                return $this->errorResponse('User does not belong to this department', 404);
            }

            // Update user role based on team leader flag
            if ($request->boolean('is_team_leader')) {
                // Set role_id 17 for team leader
                $user->role_id = 17;
            } else {
                // Set role_id 18 for employee
                $user->role_id = 18;
            }
            $user->save();

            $role = Role::find($user->role_id);
            $user->role_name = $role ? $role->name : 'employee';
            $user->save();

            Log::info('User role updated in department', [
                'user_id' => $user->id,
                'department_id' => $department->id,
                'new_role_id' => $user->role_id,
                'new_role_name' => $user->role_name
            ]);

            return $this->successResponse('User role updated successfully', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'role_name' => $user->role_name,
                'is_team_leader' => $request->boolean('is_team_leader')
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found', 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to update user role', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Failed to update user role');
        }
    }

    /**
     * Display the specified department.
     *
     * @param string $company_name
     * @param Department $department
     * @return JsonResponse
     */
    public function show(string $company_name, Department $department): JsonResponse
    {
        try {
            Log::info('Fetching department details', [
                'company_name' => $company_name,
                'department_id' => $department->id
            ]);

            // Get portal first
            $portal = Portal::where('bussiness_name', $company_name)->first();
            if (!$portal) {
                Log::error('Portal not found', ['company_name' => $company_name]);
                return $this->errorResponse('Portal not found', 404);
            }

            // Verify department belongs to portal
            if ($department->portal_id !== $portal->id) {
                Log::error('Department does not belong to portal', [
                    'department_id' => $department->id,
                    'portal_id' => $portal->id,
                    'department_portal_id' => $department->portal_id
                ]);
                return $this->errorResponse('Department does not belong to this portal', 403);
            }

            // Load department with all related data
            $department->load([
                'users' => function ($query) {
                    $query->select([
                        'users.id',
                        'users.full_name',
                        'users.role_name',
                        'users.role_id',
                        'users.mobile',
                        'users.email',
                        'users.bio',
                        'users.created_at'
                    ]);
                },
                'portal' => function ($query) {
                    $query->select([
                        'portals.id',
                        'portals.bussiness_name',
                        'portals.email',
                        'portals.created_at'
                    ]);
                }
            ]);

            // Get team leaders in this department
            $teamLeaders = $department->users()
                ->where('role_id', 17)
                ->select([
                    'users.id',
                    'users.full_name',
                    'users.email',
                    'users.mobile'
                ])
                ->get();

            $totalUsers = $department->users()->count();

            // Get employees count (role_id = 18)
            $employeesCount = $department->users()
                ->where('role_id', 18)
                ->count();

            // Prepare response data
            $responseData = [
                'department' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'name_ar' => $department->name_ar,
                    'created_at' => $department->created_at,
                    'updated_at' => $department->updated_at,
                    'total_users' => $totalUsers,
                    'employees_count' => $employeesCount,
                    'team_leaders_count' => $teamLeaders->count(),
                    'portal' => $department->portal,
                ],
                'team_leaders' => $teamLeaders,
                'users' => $department->users
            ];

            Log::info('Department details retrieved successfully', [
                'department_id' => $department->id,
                'total_users' => $totalUsers,
                'team_leaders_count' => $teamLeaders->count()
            ]);

            return $this->successResponse('Department details retrieved successfully', $responseData);
        } catch (\Exception $e) {
            Log::error('Failed to fetch department details', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'company_name' => $company_name,
                'department_id' => $department->id ?? null
            ]);
            return $this->errorResponse('Failed to fetch department details: ' . $e->getMessage());
        }
    }

    /**
     * Verify department belongs to portal
     *
     * @param Department $department
     * @param Portal $portal
     * @return bool
     */
    private function verifyDepartmentBelongsToPortal(Department $department, Portal $portal): bool
    {
        return $department->portal_id === $portal->id;
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
     * @param \Illuminate\Support\MessageBag|array $errors
     * @return JsonResponse
     */
    private function validationErrorResponse($errors): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'errors' => $errors
        ], 422);
    }

    /**
     * Get portal by business name.
     *
     * @param string $company_name
     * @return Portal|null
     */
    private function getPortal(string $company_name): ?Portal
    {
        return Portal::where('bussiness_name', $company_name)->first();
    }
}
