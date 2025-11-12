<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'timezone' => $request->timezone ?? 'UTC',
            'language' => $request->language ?? 'en',
        ]);

        // Assign default 'user' role to new registrations
        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'tenant_id' => 'nullable|string', // Can be UUID or slug
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Update last login information
        $user->updateLastLogin($request->ip());

        // Get all organizations user belongs to
        $organizations = $user->getActiveTenants()->map(function ($tenant) use ($user) {
            $roles = $user->rolesInTenant($tenant)->get()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                ];
            });

            $permissions = $user->getAllPermissionsInTenant($tenant)->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                ];
            });

            return [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'domain' => $tenant->domain,
                'roles' => $roles,
                'permissions' => $permissions,
            ];
        });

        // Determine current organization
        $currentOrganization = null;
        if ($request->tenant_id) {
            // Find tenant by UUID or slug
            $tenant = Tenant::where('id', $request->tenant_id)
                ->orWhere('slug', $request->tenant_id)
                ->first();
            
            if ($tenant && $user->belongsToTenant($tenant)) {
                $currentOrganization = $organizations->firstWhere('id', $tenant->id);
            }
        }

        // If no tenant specified or invalid, use first organization
        if (!$currentOrganization && $organizations->isNotEmpty()) {
            $currentOrganization = $organizations->first();
        }

        // Create JWT token with custom claims
        $customClaims = [
            'tenant_id' => $currentOrganization['id'] ?? null,
            'roles' => $currentOrganization['roles'] ?? [],
            'permissions' => $currentOrganization['permissions'] ?? [],
        ];

        $token = JWTAuth::claims($customClaims)->fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60, // Convert minutes to seconds
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'timezone' => $user->timezone,
                'language' => $user->language,
            ],
            'current_organization' => $currentOrganization,
            'organizations' => $organizations,
        ]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to logout, please try again.',
            ], 500);
        }
    }

    /**
     * Switch to a different organization and get new JWT token
     */
    public function switchOrganization(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|string', // Can be UUID or slug
        ]);

        $user = $request->user();
        
        // Find tenant by UUID or slug
        $tenant = Tenant::where('id', $request->tenant_id)
            ->orWhere('slug', $request->tenant_id)
            ->first();

        if (!$tenant) {
            return response()->json([
                'error' => 'Organization not found.',
            ], 404);
        }

        if (!$user->belongsToTenant($tenant)) {
            return response()->json([
                'error' => 'You do not have access to this organization.',
            ], 403);
        }

        // Get roles and permissions for the new organization
        $roles = $user->rolesInTenant($tenant)->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ];
        });

        $permissions = $user->getAllPermissionsInTenant($tenant)->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
            ];
        });

        $currentOrganization = [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'domain' => $tenant->domain,
            'roles' => $roles,
            'permissions' => $permissions,
        ];

        // Invalidate old token and create new one with updated organization context
        JWTAuth::invalidate(JWTAuth::getToken());

        $customClaims = [
            'tenant_id' => $currentOrganization['id'],
            'roles' => $currentOrganization['roles'],
            'permissions' => $currentOrganization['permissions'],
        ];

        $token = JWTAuth::claims($customClaims)->fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'current_organization' => $currentOrganization,
            'message' => 'Successfully switched to ' . $tenant->name,
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        // Get JWT token payload to extract current organization
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $tenantId = $payload->get('tenant_id');
            
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
                
                if ($tenant && $user->belongsToTenant($tenant)) {
                    return response()->json([
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'current_organization' => [
                            'id' => $tenant->id,
                            'name' => $tenant->name,
                            'slug' => $tenant->slug,
                            'roles' => $payload->get('roles', []),
                            'permissions' => $payload->get('permissions', []),
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Token parsing failed, return basic user info
        }
        
        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'date_of_birth' => $user->date_of_birth,
            'gender' => $user->gender,
            'address_line1' => $user->address_line1,
            'address_line2' => $user->address_line2,
            'city' => $user->city,
            'state' => $user->state,
            'postal_code' => $user->postal_code,
            'country' => $user->country,
            'timezone' => $user->timezone,
            'language' => $user->language,
            'bio' => $user->bio,
            'is_active' => $user->is_active,
            'last_login_at' => $user->last_login_at,
        ]);
    }
}

