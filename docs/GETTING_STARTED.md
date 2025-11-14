# Setup Guide - Roles and Permissions

## Quick Start

### 1. Database is Ready
All migrations have been run. The following tables exist:
- `roles`
- `permissions`
- `role_user`
- `permission_role`
- `permission_user`

### 2. Default Data is Seeded
Run this command if you need to reseed:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

This creates:
- 3 Roles: admin, moderator, user
- 14 Permissions: Various user, post, and role management permissions

### 3. Start the Server
```bash
php artisan serve
```

### 4. Create Your First Admin User

**Option A: Via API then Tinker**
```bash
# 1. Register via API
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# 2. Make them admin via Tinker
php artisan tinker
>>> $user = User::where('email', 'admin@example.com')->first();
>>> $user->assignRole('admin');
>>> exit
```

**Option B: Via Tinker Only**
```bash
php artisan tinker
>>> $user = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password123')]);
>>> $user->assignRole('admin');
>>> exit
```

### 5. Test the System

**Login as Admin:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

Save the `access_token` from the response.

**Get All Roles (Admin only):**
```bash
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Get Your User Info with Roles:**
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## What's Implemented

✅ **Database Tables** - All role and permission tables created
✅ **Models** - Role, Permission models with relationships
✅ **User Trait** - HasRolesAndPermissions trait added to User model
✅ **Middleware** - RoleMiddleware and PermissionMiddleware registered
✅ **Controllers** - Full CRUD for roles, permissions, and user management
✅ **API Routes** - All endpoints configured with proper middleware
✅ **Seeders** - Default roles and permissions seeded
✅ **Auto-assignment** - New users automatically get 'user' role

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php (updated with role assignment)
│   │       ├── RoleController.php (new)
│   │       ├── PermissionController.php (new)
│   │       └── UserController.php (new)
│   └── Middleware/
│       ├── RoleMiddleware.php (new)
│       └── PermissionMiddleware.php (new)
├── Models/
│   ├── User.php (updated)
│   ├── Role.php (new)
│   └── Permission.php (new)
└── Traits/
    └── HasRolesAndPermissions.php (new)

database/
├── migrations/
│   ├── 2025_11_12_111436_create_roles_table.php
│   ├── 2025_11_12_111440_create_permissions_table.php
│   ├── 2025_11_12_111445_create_role_user_table.php
│   ├── 2025_11_12_111449_create_permission_role_table.php
│   └── 2025_11_12_111454_create_permission_user_table.php
└── seeders/
    └── RolePermissionSeeder.php (new)

routes/
└── api.php (updated with role/permission routes)

bootstrap/
└── app.php (updated with middleware aliases)

Documentation:
├── ROLES_AND_PERMISSIONS.md (comprehensive guide)
└── QUICK_REFERENCE.md (quick reference)
```

## Next Steps

1. **Create Admin User** (see step 4 above)
2. **Test Authentication** with different roles
3. **Customize Permissions** based on your app's needs
4. **Add Role Checks** to your controllers/views
5. **Create Custom Roles** for your specific use cases

## Common Commands

```bash
# View all routes
php artisan route:list

# View API routes only
php artisan route:list --path=api

# Reseed roles and permissions
php artisan db:seed --class=RolePermissionSeeder

# Open Tinker for database operations
php artisan tinker

# Clear all caches
php artisan optimize:clear
```

## Support

For detailed documentation, see:
- `ROLES_AND_PERMISSIONS.md` - Full documentation
- `QUICK_REFERENCE.md` - Quick reference guide
- `AUTH_API_DOCUMENTATION.md` - Authentication API docs
# UUID Implementation for Organization IDs

## Summary

Organization IDs are now **UUIDs** instead of auto-incrementing integers for enhanced security and privacy.

## Changes Made

### 1. Organization Model
- Added `HasUuids` trait
- Set `$incrementing = false`
- Set `$keyType = 'string'`

### 2. Database Migrations

**Organizations Table:**
```php
$table->uuid('id')->primary();  // Instead of $table->id()
```

**Foreign Keys:**
```php
// All organization_id foreign keys updated to:
$table->uuid('organization_id')->nullable();
$table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
```

**Affected Tables:**
- `organization_user` - Many-to-many user-organization relationship
- `roles` - Roles scoped to organizations
- `permissions` - Permissions scoped to organizations
- `role_user` - User role assignments per organization

## Example UUIDs

```
Acme Corporation:  019a77ec-851a-7028-8f56-5f31232cdf72
TechStart Inc:     019a77ec-851b-7106-a6b9-a93e00b91358
```

## Security Benefits

### 1. Prevents Enumeration Attacks
**Before (Auto-increment):**
```bash
# Attacker can easily discover all organizations
curl /api/organizations/1
curl /api/organizations/2
curl /api/organizations/3
# Reveals: "You have at least 3 customers"
```

**After (UUID):**
```bash
# Impossible to guess next organization ID
curl /api/organizations/019a77ec-851a-7028-8f56-5f31232cdf72
curl /api/organizations/019a77ec-851b-7106-a6b9-a93e00b91358
# Cannot determine total number of organizations
```

### 2. Hides Business Metrics
- Auto-increment IDs reveal growth rate (ID 1000 → ID 1500 = 500 new organizations)
- UUIDs keep your business metrics private

### 3. Safe for Public Exposure
- Can be used in URLs without security concerns
- Can be shared in API documentation
- Can be logged without exposing sensitive patterns

### 4. Distributed System Ready
- Generate IDs without database coordination
- No conflicts when merging databases
- Perfect for microservices architecture

## API Usage

### Using UUID in Requests

```bash
# Get organization by UUID
GET /api/organizations/019a77ec-851a-7028-8f56-5f31232cdf72

# Set organization context with UUID
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer TOKEN" \
  -H "X-Organization-ID: 019a77ec-851a-7028-8f56-5f31232cdf72"

# Or use the slug (still supported for convenience)
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer TOKEN" \
  -H "X-Organization-ID: acme"
```

### Login Response

```json
{
  "access_token": "...",
  "user": {...},
  "organizations": [
    {
      "id": "019a77ec-851a-7028-8f56-5f31232cdf72",
      "name": "Acme Corporation",
      "slug": "acme"
    },
    {
      "id": "019a77ec-851b-7106-a6b9-a93e00b91358",
      "name": "TechStart Inc",
      "slug": "techstart"
    }
  ]
}
```

## Code Examples

### Creating a Organization (UUID auto-generated)

```php
$organization = Organization::create([
    'name' => 'New Company',
    'slug' => 'new-company',
    // No need to specify 'id' - UUID is auto-generated
]);

echo $organization->id; 
// Output: 019a77ec-8520-7abc-def0-123456789abc
```

### Finding by UUID

```php
// Find by UUID
$organization = Organization::find('019a77ec-851a-7028-8f56-5f31232cdf72');

// Or find by slug (if you need it)
$organization = Organization::where('slug', 'acme')->first();
```

### Relationships with UUIDs

```php
// All relationships work transparently
$role = Role::create([
    'organization_id' => '019a77ec-851a-7028-8f56-5f31232cdf72',
    'name' => 'Manager',
    'slug' => 'manager',
]);

// Eloquent relationships handle UUIDs automatically
$organization = Organization::find('019a77ec-851a-7028-8f56-5f31232cdf72');
$roles = $organization->roles; // Works perfectly
```

## Migration Strategy

If you already have data with integer IDs, you would need to:

1. Create a new UUID column
2. Generate UUIDs for existing records
3. Update all foreign keys
4. Drop old integer columns
5. Rename UUID columns to 'id'

**For new projects (like this one):** ✅ Already implemented from the start!

## Performance Considerations

### UUID vs Auto-increment

**Storage:**
- UUID: 36 characters (or 16 bytes as binary)
- Integer: 4-8 bytes
- **Impact:** Minimal for most applications

**Indexing:**
- UUIDs are slightly slower for indexing than integers
- **Impact:** Negligible for organization tables (low write volume)

**Best Practices:**
- Use UUIDs for organizations, users, and other publicly-exposed entities
- Use integers for high-volume internal tables if needed
- Laravel's `HasUuids` trait uses ordered UUIDs (ULID-style) for better performance

## Testing

Verify UUID implementation:

```bash
# Check organization UUIDs in database
php artisan tinker
>>> Organization::all()->pluck('id', 'name')
=> Illuminate\Support\Collection {
     "Acme Corporation": "019a77ec-851a-7028-8f56-5f31232cdf72",
     "TechStart Inc": "019a77ec-851b-7106-a6b9-a93e00b91358",
   }

# Test API with UUID
curl -X GET http://localhost:8000/api/organizations/019a77ec-851a-7028-8f56-5f31232cdf72 \
  -H "Authorization: Bearer TOKEN"
```

## Conclusion

✅ **Implemented:** All organization IDs are now UUIDs  
✅ **Secure:** Cannot be enumerated or guessed  
✅ **Production-ready:** Safe for public APIs  
✅ **Future-proof:** Ready for distributed systems  

Your multi-organization authorization microservice is now **production-grade** with enterprise-level security!
