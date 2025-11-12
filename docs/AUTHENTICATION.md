# Multi-Tenant Authorization Microservice Documentation

## Overview

This is a **multi-tenant authorization microservice** built with Laravel Sanctum. It allows users to be members of multiple companies/tenants with different roles and permissions in each tenant.

## Key Features

✅ **Multi-Tenancy** - Users can belong to multiple tenants/companies  
✅ **Tenant-Scoped Roles** - Different roles per tenant for the same user  
✅ **Tenant-Scoped Permissions** - Permissions are isolated by tenant  
✅ **Flexible Authorization** - Check permissions based on tenant context  
✅ **API Token Authentication** - Laravel Sanctum-based authentication  
✅ **Tenant Context Middleware** - Automatic tenant identification from headers/subdomain

## Architecture

### Database Structure

**Core Tables:**
- `tenants` - Companies/organizations (uses UUID primary key for security)
- `users` - User accounts (shared across tenants)
- `roles` - Roles scoped to tenants
- `permissions` - Permissions scoped to tenants

**Relationship Tables:**
- `tenant_user` - User membership in tenants
- `role_user` - User roles within specific tenants
- `permission_role` - Permissions assigned to roles
- `permission_user` - Direct user permissions

### Security: UUID-based Tenant IDs

**Why UUIDs?**
- ✅ **Prevents enumeration attacks** - Can't guess tenant IDs sequentially
- ✅ **Hides business metrics** - Number of tenants not exposed
- ✅ **Collision-free** - Safe for distributed systems and mergers
- ✅ **Publicly safe** - Can be exposed in URLs and APIs without risk

**Example Tenant ID:** `019a77ec-851a-7028-8f56-5f31232cdf72`

Instead of predictable IDs like `1, 2, 3`, your tenants have cryptographically random UUIDs that cannot be guessed or enumerated.

### Multi-Tenant Flow

```
User → Tenant A → Role: Admin → Permissions: All
  ↓
  └→ Tenant B → Role: User → Permissions: Limited
```

The same user can have:
- Admin role in Tenant A
- User role in Tenant B
- No access to Tenant C

## Quick Start

### 1. Database Setup

```bash
php artisan migrate:fresh
php artisan db:seed --class=MultiTenantSeeder
```

### 2. Demo Data

**Tenants:**
- Acme Corporation (slug: `acme`, ID: UUID)
- TechStart Inc (slug: `techstart`, ID: UUID)

> **Note:** Tenant IDs are UUIDs like `019a77ec-851a-7028-8f56-5f31232cdf72` for security

**Users:**
- `john@example.com` - Admin in Acme, User in TechStart
- `jane@example.com` - Moderator in both tenants
- `bob@example.com` - Admin in TechStart only
- Password: `password`

### 3. Start Server

```bash
php artisan serve
```

## API Usage

### Authentication

#### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "access_token": "...",
  "token_type": "Bearer",
  "user": {...},
  "tenants": [
    {"id": 1, "name": "Acme Corporation", "slug": "acme"},
    {"id": 2, "name": "TechStart Inc", "slug": "techstart"}
  ]
}
```

### Tenant Context

There are **3 ways** to specify the tenant context:

#### 1. Using Header (Recommended)
```bash
# You can use either the tenant slug or UUID
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer TOKEN" \
  -H "X-Tenant-ID: acme"

# Or with UUID
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer TOKEN" \
  -H "X-Tenant-ID: 019a77ec-851a-7028-8f56-5f31232cdf72"
```

#### 2. Using Query Parameter
```bash
# Using slug
curl -X GET "http://localhost:8000/api/roles?tenant_id=acme" \
  -H "Authorization: Bearer TOKEN"

# Or using UUID (more secure for public APIs)
curl -X GET "http://localhost:8000/api/roles?tenant_id=019a77ec-851a-7028-8f56-5f31232cdf72" \
  -H "Authorization: Bearer TOKEN"
```

#### 3. Using Subdomain
```bash
curl -X GET http://acme.localhost:8000/api/roles \
  -H "Authorization: Bearer TOKEN"
```

> **Security Tip:** When exposing tenant identifiers publicly (e.g., in URLs), use UUIDs instead of slugs for maximum security.

### Tenant Management

#### List User's Tenants
```bash
GET /api/tenants
Authorization: Bearer {token}
```

#### Create New Tenant
```bash
POST /api/tenants
Authorization: Bearer {token}

{
  "name": "New Company",
  "slug": "new-company",
  "domain": "newcompany.example.com",
  "description": "Description here"
}
```

#### Add User to Tenant
```bash
POST /api/tenants/{id}/add-user
Authorization: Bearer {token}

{
  "user_id": 5
}
```

#### Get User's Context in Tenant
```bash
GET /api/tenants/{id}/context
Authorization: Bearer {token}
```

**Response:**
```json
{
  "tenant": {
    "id": "019a77ec-851a-7028-8f56-5f31232cdf72",
    "name": "Acme Corporation"
  },
  "roles": [
    {"id": 1, "name": "Administrator", "slug": "admin"}
  ],
  "permissions": [
    {"id": 1, "name": "View Users", "slug": "view-users"},
    ...
  ]
}
```

### Role Management (Tenant-Scoped)

#### List Roles in Current Tenant
```bash
GET /api/roles
Authorization: Bearer {token}
X-Tenant-ID: acme
```

#### Create Role in Tenant
```bash
POST /api/roles
Authorization: Bearer {token}
X-Tenant-ID: acme

{
  "name": "Editor",
  "slug": "editor",
  "description": "Content editor role"
}
```

#### Assign Role to User in Tenant
```bash
POST /api/roles/{id}/assign-user
Authorization: Bearer {token}
X-Tenant-ID: acme

{
  "user_id": 5
}
```

### Permission Management (Tenant-Scoped)

#### List Permissions in Current Tenant
```bash
GET /api/permissions
Authorization: Bearer {token}
X-Tenant-ID: acme
```

#### Create Permission in Tenant
```bash
POST /api/permissions
Authorization: Bearer {token}
X-Tenant-ID: acme

{
  "name": "Export Data",
  "slug": "export-data",
  "description": "Can export data"
}
```

## Programmatic Usage

### User Methods

```php
// Join a tenant
$user->joinTenant($tenant);

// Leave a tenant
$user->leaveTenant($tenant);

// Check membership
if ($user->belongsToTenant($tenant)) {
    // User is member of this tenant
}

// Assign role in specific tenant
$user->assignRoleInTenant('admin', $tenant);

// Remove role in specific tenant
$user->removeRoleInTenant('moderator', $tenant);

// Check role in specific tenant
if ($user->hasRoleInTenant('admin', $tenant)) {
    // User is admin in this tenant
}

// Check permission in specific tenant
if ($user->hasPermissionInTenant('edit-posts', $tenant)) {
    // User can edit posts in this tenant
}

// Get all permissions in tenant
$permissions = $user->getAllPermissionsInTenant($tenant);

// Get roles in tenant
$roles = $user->rolesInTenant($tenant)->get();

// Get active tenants
$tenants = $user->getActiveTenants();
```

### Tenant Methods

```php
// Add user to tenant
$tenant->addUser($user);

// Remove user from tenant
$tenant->removeUser($user);

// Check if user belongs to tenant
if ($tenant->hasUser($user)) {
    // User is a member
}
```

## Middleware Protection

### Require Tenant Context

```php
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    // These routes require a tenant context
});
```

### Require Role in Tenant

```php
Route::middleware(['auth:sanctum', 'tenant', 'role:admin'])->group(function () {
    // User must be admin in the current tenant
});
```

### Require Permission in Tenant

```php
Route::middleware(['auth:sanctum', 'tenant', 'permission:edit-posts'])->group(function () {
    // User must have edit-posts permission in current tenant
});
```

## Use Cases

### Example 1: SaaS Application

```
Company A (tenant_id: 1)
├── John (Admin) - Full access
├── Jane (User) - Limited access
└── Bob (Moderator) - Content access

Company B (tenant_id: 2)
├── John (User) - Limited access
└── Jane (Admin) - Full access
```

John has different permissions in Company A vs Company B.

### Example 2: Agency Platform

```
Client 1 (tenant_id: 1)
└── Agency Staff → Role: Account Manager

Client 2 (tenant_id: 2)
└── Same Staff → Role: Consultant
```

Agency staff have different roles for different clients.

### Example 3: Enterprise with Departments

```
HR Department (tenant_id: 1)
├── Alice (HR Manager)
└── Bob (HR Assistant)

IT Department (tenant_id: 2)
├── Alice (IT User)
└── Bob (IT Admin)
```

Same employees, different roles per department.

## Testing Examples

### Test 1: User in Multiple Tenants

```bash
# Login as John
TOKEN=$(curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}' \
  | jq -r '.access_token')

# Check roles in Acme (should be admin)
curl -X GET http://localhost:8000/api/tenants/1/context \
  -H "Authorization: Bearer $TOKEN"

# Check roles in TechStart (should be user)
curl -X GET http://localhost:8000/api/tenants/2/context \
  -H "Authorization: Bearer $TOKEN"
```

### Test 2: Tenant-Scoped Permissions

```bash
# Try to access admin route in Acme (should work - John is admin)
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Tenant-ID: acme"

# Try to access admin route in TechStart (should fail - John is user)
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Tenant-ID: techstart"
```

### Test 3: Cross-Tenant Isolation

```bash
# Bob should not access Acme data (he's only in TechStart)
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer BOB_TOKEN" \
  -H "X-Tenant-ID: acme"
# Should return 403 Forbidden
```

## Best Practices

1. **Always Set Tenant Context** - Use headers or subdomains to specify tenant
2. **Scope All Queries** - Filter by tenant_id when querying roles/permissions
3. **Validate Tenant Access** - Check user belongs to tenant before operations
4. **Use Middleware** - Apply tenant middleware to all tenant-scoped routes
5. **Audit Logs** - Track which tenant context was used for each action

## Security Considerations

- ✅ **UUID Tenant IDs** - Cryptographically random, prevents enumeration attacks
- ✅ **Tenant Isolation** - Users can only access tenants they belong to
- ✅ **Scoped Authorization** - Roles and permissions are isolated per tenant
- ✅ **No Data Leakage** - Cross-tenant data access prevented by tenant_id scoping
- ✅ **Access Validation** - Middleware validates tenant membership before granting access
- ✅ **User-scoped Tokens** - API tokens are user-scoped, not tenant-scoped
- ✅ **Safe Public Exposure** - UUIDs can be safely exposed in URLs and APIs

### Why UUIDs Matter for Security

**Without UUIDs (using auto-increment):**
```bash
# Attacker can easily enumerate all tenants
GET /api/tenants/1
GET /api/tenants/2
GET /api/tenants/3
# ... reveals total number of customers
```

**With UUIDs:**
```bash
# Attacker cannot guess or enumerate
GET /api/tenants/019a77ec-851a-7028-8f56-5f31232cdf72
# Next tenant ID is unpredictable
GET /api/tenants/019a77ec-851b-7106-a6b9-a93e00b91358
```

## API Endpoints Summary

| Endpoint | Method | Description | Middleware |
|----------|--------|-------------|------------|
| `/api/login` | POST | User login | Public |
| `/api/tenants` | GET | List user's tenants | Auth |
| `/api/tenants` | POST | Create tenant | Auth |
| `/api/tenants/{id}` | GET | Get tenant details | Auth |
| `/api/tenants/{id}/context` | GET | Get user's roles/permissions in tenant | Auth |
| `/api/tenants/{id}/add-user` | POST | Add user to tenant | Auth |
| `/api/roles` | GET | List roles in tenant | Auth + Tenant + Admin |
| `/api/roles` | POST | Create role in tenant | Auth + Tenant + Admin |
| `/api/permissions` | GET | List permissions in tenant | Auth + Tenant + Admin |
| `/api/permissions` | POST | Create permission in tenant | Auth + Tenant + Admin |

## Troubleshooting

### "Tenant context is required"
- Make sure to include `X-Tenant-ID` header or `tenant_id` parameter
- Or use subdomain routing

### "You do not have access to this tenant"
- User is not a member of the specified tenant
- Add user to tenant first using `/api/tenants/{id}/add-user`

### "Unauthorized. Required role(s) in this tenant"
- User doesn't have the required role in this specific tenant
- Assign appropriate role using `/api/roles/{id}/assign-user`

## License

MIT License
# JWT Authentication with Multi-Tenant Support

## Overview

The authentication system now uses **JWT (JSON Web Tokens)** with embedded roles and permissions for the current organization. Each JWT token contains:

- User identification
- Current organization (tenant) context
- Roles in that organization
- Permissions in that organization

## Installation Complete

✅ JWT Auth package installed (`tymon/jwt-auth`)  
✅ JWT secret key generated  
✅ User model implements `JWTSubject`  
✅ Auth guard configured for JWT  
✅ AuthController updated with organization-aware login  

## API Endpoints

### 1. Login

**Endpoint:** `POST /api/login`

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password",
  "tenant_id": "acme"  // Optional: UUID or slug
}
```

**Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "current_organization": {
    "id": "019a77f4-54f3-72c3-beec-c8b1a59dbc23",
    "name": "Acme Corporation",
    "slug": "acme",
    "domain": "acme.example.com",
    "roles": [
      {
        "id": 1,
        "name": "Administrator",
        "slug": "admin"
      }
    ],
    "permissions": [
      {
        "id": 1,
        "name": "View Users",
        "slug": "view-users"
      },
      {
        "id": 2,
        "name": "Create Users",
        "slug": "create-users"
      },
      ...
    ]
  },
  "organizations": [
    {
      "id": "019a77f4-54f3-72c3-beec-c8b1a59dbc23",
      "name": "Acme Corporation",
      "slug": "acme",
      "domain": "acme.example.com",
      "roles": [...],
      "permissions": [...]
    },
    {
      "id": "019a77f4-54f4-7382-8f5e-e680fb75bb18",
      "name": "TechStart Inc",
      "slug": "techstart",
      "domain": "techstart.example.com",
      "roles": [...],
      "permissions": [...]
    }
  ]
}
```

**Key Features:**
- ✅ Returns JWT token with organization context embedded
- ✅ Lists ALL organizations user belongs to
- ✅ Each organization includes user's roles and permissions
- ✅ Auto-selects first organization if `tenant_id` not provided
- ✅ Token expires in 60 minutes (configurable in `config/jwt.php`)

### 2. Switch Organization

**Endpoint:** `POST /api/switch-organization`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request:**
```json
{
  "tenant_id": "techstart"  // UUID or slug
}
```

**Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",  // New token
  "token_type": "Bearer",
  "expires_in": 3600,
  "current_organization": {
    "id": "019a77f4-54f4-7382-8f5e-e680fb75bb18",
    "name": "TechStart Inc",
    "slug": "techstart",
    "domain": "techstart.example.com",
    "roles": [
      {
        "id": 7,
        "name": "User",
        "slug": "user"
      }
    ],
    "permissions": [
      {
        "id": 13,
        "name": "View Posts",
        "slug": "view-posts"
      },
      {
        "id": 14,
        "name": "Create Posts",
        "slug": "create-posts"
      }
    ]
  },
  "message": "Successfully switched to TechStart Inc"
}
```

**Key Features:**
- ✅ Invalidates old JWT token
- ✅ Generates new token with updated organization context
- ✅ Validates user has access to requested organization
- ✅ Returns new roles and permissions for the organization

### 3. Get Current User

**Endpoint:** `GET /api/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "current_organization": {
    "id": "019a77f4-54f3-72c3-beec-c8b1a59dbc23",
    "name": "Acme Corporation",
    "slug": "acme",
    "roles": [
      {
        "id": 1,
        "name": "Administrator",
        "slug": "admin"
      }
    ],
    "permissions": [
      {
        "id": 1,
        "name": "View Users",
        "slug": "view-users"
      },
      ...
    ]
  }
}
```

**Key Features:**
- ✅ Extracts organization context from JWT token
- ✅ Returns current organization roles and permissions
- ✅ No database queries needed (data in token)

### 4. Logout

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Successfully logged out"
}
```

**Key Features:**
- ✅ Invalidates JWT token (adds to blacklist)
- ✅ Token cannot be reused after logout

## JWT Token Payload

The JWT token contains the following claims:

```json
{
  "iss": "http://127.0.0.1:8000",  // Issuer
  "iat": 1699876543,                // Issued at
  "exp": 1699880143,                // Expires at
  "nbf": 1699876543,                // Not before
  "jti": "random-unique-id",        // JWT ID
  "sub": 1,                         // User ID
  "prv": "hash",                    // Provider hash
  "tenant_id": "019a77f4-54f3-72c3-beec-c8b1a59dbc23",
  "roles": [
    {
      "id": 1,
      "name": "Administrator",
      "slug": "admin"
    }
  ],
  "permissions": [
    {
      "id": 1,
      "name": "View Users",
      "slug": "view-users"
    },
    ...
  ]
}
```

## Authentication Flow

### Login with Organization

```mermaid
sequenceDiagram
    Client->>API: POST /api/login (email, password, tenant_id)
    API->>Database: Verify credentials
    API->>Database: Get user's organizations
    API->>Database: Get roles/permissions for each org
    API->>API: Generate JWT with org context
    API-->>Client: Return token + organizations list
```

### Switch Organization

```mermaid
sequenceDiagram
    Client->>API: POST /api/switch-organization (tenant_id)
    API->>API: Verify user access to tenant
    API->>Database: Get roles/permissions for new org
    API->>API: Invalidate old token
    API->>API: Generate new JWT with new org context
    API-->>Client: Return new token
```

### Access Protected Resource

```mermaid
sequenceDiagram
    Client->>API: GET /api/resource (Authorization: Bearer token)
    API->>API: Validate JWT signature
    API->>API: Extract org context from token
    API->>API: Check permissions in token
    API-->>Client: Return resource or 403
```

## Example Usage

### cURL Examples

**1. Login to specific organization:**
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password",
    "tenant_id": "acme"
  }'
```

**2. Login without specifying organization (uses first):**
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'
```

**3. Switch to different organization:**
```bash
TOKEN="your-jwt-token-here"

curl -X POST http://127.0.0.1:8000/api/switch-organization \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tenant_id": "techstart"
  }'
```

**4. Get current user info:**
```bash
curl -X GET http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer $TOKEN"
```

### JavaScript/Fetch Example

```javascript
// Login
const loginResponse = await fetch('http://127.0.0.1:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'password',
    tenant_id: 'acme'  // Optional
  })
});

const loginData = await loginResponse.json();
const token = loginData.access_token;
const organizations = loginData.organizations;

// Store token
localStorage.setItem('jwt_token', token);
localStorage.setItem('current_org', JSON.stringify(loginData.current_organization));
localStorage.setItem('organizations', JSON.stringify(organizations));

// Switch organization
const switchResponse = await fetch('http://127.0.0.1:8000/api/switch-organization', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    tenant_id: 'techstart'
  })
});

const switchData = await switchResponse.json();
const newToken = switchData.access_token;

// Update stored token
localStorage.setItem('jwt_token', newToken);
localStorage.setItem('current_org', JSON.stringify(switchData.current_organization));
```

## Frontend Implementation Tips

### 1. Store Organizations on Login

```javascript
// After login, store all organizations
const { organizations, current_organization, access_token } = loginResponse;

// Store in state management (Redux, Vuex, etc.)
store.dispatch('auth/setToken', access_token);
store.dispatch('auth/setOrganizations', organizations);
store.dispatch('auth/setCurrentOrganization', current_organization);
```

### 2. Organization Switcher Component

```javascript
// Render organization switcher dropdown
const OrganizationSwitcher = () => {
  const organizations = useSelector(state => state.auth.organizations);
  const currentOrg = useSelector(state => state.auth.currentOrganization);
  
  const handleSwitch = async (orgId) => {
    const response = await api.post('/switch-organization', {
      tenant_id: orgId
    });
    
    // Update token and current org
    store.dispatch('auth/setToken', response.access_token);
    store.dispatch('auth/setCurrentOrganization', response.current_organization);
  };
  
  return (
    <Dropdown>
      <DropdownButton>{currentOrg.name}</DropdownButton>
      <DropdownMenu>
        {organizations.map(org => (
          <DropdownItem 
            key={org.id}
            onClick={() => handleSwitch(org.id)}
            active={org.id === currentOrg.id}
          >
            {org.name}
            <Badge>{org.roles.map(r => r.name).join(', ')}</Badge>
          </DropdownItem>
        ))}
      </DropdownMenu>
    </Dropdown>
  );
};
```

### 3. Check Permissions Client-Side

```javascript
// Extract permissions from current organization
const hasPermission = (permission) => {
  const currentOrg = store.getState().auth.currentOrganization;
  return currentOrg.permissions.some(p => p.slug === permission);
};

// Use in components
{hasPermission('create-users') && (
  <Button>Create User</Button>
)}
```

## Configuration

### JWT TTL (Time To Live)

Edit `config/jwt.php`:

```php
'ttl' => env('JWT_TTL', 60),  // Minutes
```

### JWT Refresh

```php
'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),  // 2 weeks
```

### JWT Secret

The secret is stored in `.env`:
```
JWT_SECRET=your-secret-key-here
```

## Security Considerations

✅ **Token Expiration** - Tokens expire after 60 minutes (default)  
✅ **Token Blacklist** - Logged out tokens are blacklisted  
✅ **Organization Validation** - System verifies user access to organization  
✅ **Embedded Permissions** - No DB queries needed for permission checks  
✅ **HTTPS Required** - Use HTTPS in production to protect tokens  

## Migration from Sanctum

If you need to support both JWT and Sanctum:

```php
// In routes/api.php
Route::middleware(['auth:api,sanctum'])->group(function () {
    // Routes support both JWT and Sanctum tokens
});
```

## Troubleshooting

### "Token has expired"
- Token TTL is 60 minutes by default
- Implement token refresh mechanism
- Or prompt user to re-login

### "Token could not be parsed from the request"
- Ensure header format: `Authorization: Bearer {token}`
- Token must be sent with every request

### "This action is unauthorized"
- User doesn't have required permission in current organization
- Check organization context is correct
- Verify permissions are in JWT token payload

## Next Steps

1. ✅ **Implement token refresh** - Allow extending session without re-login
2. ✅ **Add permission middleware** - Check JWT permissions for routes
3. ✅ **Create organization picker UI** - Frontend component to switch orgs
4. ✅ **Add audit logging** - Track which org context was used for actions
