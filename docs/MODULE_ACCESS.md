# Module Access Control - Hybrid Approach

## Overview

This system implements a **hybrid module access control** approach that combines team-based and role-based access control for granular, scalable permission management.

### Access Hierarchy

```
Organization Subscription (Billing Level)
    ↓
User's Accessible Modules (Hybrid Calculation)
    ↓
├─ Team-Based Access (Primary)
│  └─ Modules assigned to user's team(s)
│
└─ Role-Based Access (Fallback)
   └─ Modules assigned to user's role(s)
```

## How It Works

### 1. Organization-Level Modules
- Organizations subscribe to modules (billing level)
- Only enabled modules are available to users
- Stored in `organization_module` table

### 2. User-Level Access (Hybrid)
When a user logs in, their accessible modules are calculated using:

**Priority Order:**
1. **Super Admins** → All organization modules
2. **Organization Owners/Admins** → All organization modules  
3. **Team-Based Access** → Modules assigned to user's team(s)
4. **Role-Based Access** → Modules assigned to user's role(s)
5. **Default** → All organization modules (if no restrictions)

### 3. JWT Token Integration
The accessible modules are added to the JWT token as `user_modules[]`:

```json
{
  "sub": 1,
  "organization_id": "uuid-here",
  "roles": ["user-admin"],
  "permissions": ["users.create", "users.update"],
  "modules": ["dash", "crm", "inv", "hr"],
  "teams": ["sales-team"],
  "user_modules": ["dash", "crm", "inv"]
}
```

### 4. Zero-Query Validation
The `CheckModuleAccessFromJWT` middleware validates module access without database queries:

```php
Route::middleware(['auth:api', 'module.access:crm'])
    ->get('/api/crm/contacts', [ContactController::class, 'index']);
```

## Use Cases

### Use Case 1: Department-Based Access
**Scenario:** Sales team should only access CRM and Sales modules

**Solution:**
```php
// Assign modules to Sales team
$salesTeam->modules()->sync([
    $crmModule->id,
    $salesModule->id,
]);

// Users in sales team automatically get these modules
// JWT will contain: "user_modules": ["crm", "sale"]
```

### Use Case 2: Role-Based Restrictions
**Scenario:** All "Viewers" should only see Dashboard and Reports

**Solution:**
```php
// Assign modules to Viewer role
$viewerRole->giveModuleAccess($dashboardModule);
$viewerRole->giveModuleAccess($reportsModule);

// All users with Viewer role get these modules
// JWT will contain: "user_modules": ["dash", "rpt"]
```

### Use Case 3: Hybrid Access
**Scenario:** User is in Finance team (Accounting modules) AND has Manager role (all management modules)

**Solution:**
```php
// User gets UNION of both:
// - Finance team modules: acc, inv, payroll
// - Manager role modules: hr, acc, sale, purch, payroll
// Final: acc, inv, payroll, hr, sale, purch
```

## Database Schema

### `role_module` Table
```sql
CREATE TABLE role_module (
    id BIGINT PRIMARY KEY,
    role_id BIGINT,              -- Foreign key to roles
    module_id BIGINT,            -- Foreign key to modules
    organization_id UUID,        -- Which organization this applies to
    has_access BOOLEAN,          -- True = granted, False = denied
    granted_by BIGINT,           -- User ID who granted access
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(role_id, module_id, organization_id)
);
```

### `module_team` Table (Already Exists)
```sql
CREATE TABLE module_team (
    id BIGINT PRIMARY KEY,
    team_id BIGINT,              -- Foreign key to teams
    module_id BIGINT,            -- Foreign key to modules
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(team_id, module_id)
);
```

## API Endpoints

### Role-Module Management

#### Get Modules for Role
```http
GET /api/roles/{roleId}/modules
Headers:
  X-Organization-ID: {orgId}
  Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "role": {
      "id": 1,
      "name": "User Admin",
      "slug": "user-admin"
    },
    "modules": [
      {
        "id": 1,
        "name": "Dashboard",
        "slug": "dash",
        "category": "Core"
      },
      {
        "id": 2,
        "name": "CRM",
        "slug": "crm",
        "category": "Sales"
      }
    ],
    "module_count": 2
  }
}
```

#### Assign Modules to Role
```http
POST /api/roles/{roleId}/modules
Headers:
  X-Organization-ID: {orgId}
  Authorization: Bearer {token}
Body:
{
  "module_ids": [1, 2, 3]
}
```

#### Add Single Module to Role
```http
POST /api/roles/{roleId}/modules/{moduleId}
Headers:
  X-Organization-ID: {orgId}
  Authorization: Bearer {token}
```

#### Remove Module from Role
```http
DELETE /api/roles/{roleId}/modules/{moduleId}
Headers:
  X-Organization-ID: {orgId}
  Authorization: Bearer {token}
```

#### Get Available Modules
```http
GET /api/organizations/{orgId}/available-modules
Headers:
  Authorization: Bearer {token}
```

## Code Examples

### Check User's Module Access (in Controller)
```php
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $orgId = $request->header('X-Organization-ID');
        
        // Check if user can access inventory module
        if (!$user->canAccessModule('inv', $orgId)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to the Inventory module'
            ], 403);
        }
        
        // User has access, proceed...
        return Product::all();
    }
}
```

### Get User's Accessible Modules
```php
$user = Auth::user();
$orgId = $request->header('X-Organization-ID');

// Get all modules user can access
$modules = $user->getAccessibleModules($orgId);
// Returns: ['dash', 'crm', 'inv', 'hr']

// Get modules from teams only
$teamModules = $user->getModulesFromTeams($orgId);

// Get modules from roles only
$roleModules = $user->getModulesFromRoles($orgId);
```

### Assign Modules to Role
```php
use App\Models\Role;
use App\Models\Module;

$role = Role::find($roleId);

// Assign single module
$module = Module::where('slug', 'crm')->first();
$role->giveModuleAccess($module, auth()->id());

// Assign multiple modules
$moduleIds = [1, 2, 3, 4];
$role->syncModules($moduleIds, auth()->id());

// Remove module access
$role->revokeModuleAccess($module);

// Check if role has module access
if ($role->hasModuleAccess('crm')) {
    // Role has CRM access
}
```

### Assign Modules to Team
```php
use App\Models\Team;

$team = Team::find($teamId);

// Assign modules to team (already implemented)
$team->modules()->sync([1, 2, 3]);

// Get team's modules
$modules = $team->modules;
```

## Role-Module Access Matrix (Default Seeding)

| Role | Modules | Count |
|------|---------|-------|
| **Client** | Dashboard, Reports, Calendar | 3 |
| **User** | Client modules + CRM, Inventory, Projects, Documents, Chat | 8 |
| **User Admin** | User modules + HR, Accounting, Sales, Purchasing, Payroll | 13 |
| **Administrator** | All enabled modules | All |
| **Super Administrator** | All enabled modules | All |

## Migration Guide

### Fresh Installation
1. Run migrations: `php artisan migrate`
2. Seed modules: `php artisan db:seed --class=ModuleSeeder`
3. Create organizations and enable modules
4. Seed role-module access: `php artisan db:seed --class=RoleModuleSeeder`

### Existing Installation
1. Run new migration: `php artisan migrate`
2. Assign modules to roles via API or seeder
3. JWT tokens will automatically include `user_modules` on next login

## Performance Considerations

### Zero Database Queries
Once user logs in, all module access checks are JWT-based:
- ✅ No database queries for module access validation
- ✅ Stateless authentication
- ✅ Horizontal scalability

### When to Refresh JWT
User needs new JWT token when:
- Modules are added/removed from their role
- Modules are added/removed from their team
- They are added/removed from a team
- Organization enables/disables modules

**Solution:**
- User can call `POST /api/refresh` to get updated token
- Or `POST /api/switch-organization` to force refresh

## Security Best Practices

1. **Always validate organization context**
   ```php
   $organizationId = $request->header('X-Organization-ID');
   if (!$user->organizations()->where('id', $organizationId)->exists()) {
       abort(403);
   }
   ```

2. **Use middleware for module protection**
   ```php
   Route::middleware(['auth:api', 'module.access:crm'])
   ```

3. **Check role before assigning modules**
   ```php
   if (!$user->hasRoleInOrganization(['owner', 'admin'], $orgId)) {
       abort(403);
   }
   ```

4. **Verify module is enabled for organization**
   ```php
   $isEnabled = DB::table('organization_module')
       ->where('organization_id', $orgId)
       ->where('module_id', $moduleId)
       ->where('is_enabled', true)
       ->exists();
   ```

## Troubleshooting

### User can't access module after assignment
**Solution:** User needs to refresh their JWT token:
```http
POST /api/refresh
Authorization: Bearer {old_token}
```

### Module shows in org but user can't access
**Check:**
1. Is user in a team with that module?
2. Does user's role have that module?
3. If neither, user gets no access (hybrid restriction)

**Fix:** Either assign module to user's role or user's team

### All users getting all modules
**Cause:** No role-module or team-module restrictions exist

**Fix:** Run `php artisan db:seed --class=RoleModuleSeeder` to set up default restrictions

## Related Documentation

- [Team Management](./TEAM_MANAGEMENT.md) - Team-based access control
- [RBAC](./GLOBAL_ROLES_PERMISSIONS.md) - Role-based access control
- [Multi-Organization](./MULTI_ORGANIZATION.md) - Organization management
- [Authentication](./AUTHENTICATION.md) - JWT implementation

---

**Last Updated:** November 13, 2025  
**Version:** 1.0.0
