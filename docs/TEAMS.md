# Team Management - Complete Guide

> **Comprehensive team collaboration and access control documentation**

## Table of Contents

1. [Overview](#overview)
2. [Database Structure](#database-structure)
3. [Team Roles & Permissions](#team-roles--permissions)
4. [API Reference](#api-reference)
5. [Hierarchical Teams](#hierarchical-teams)
6. [JWT Integration](#jwt-integration)
7. [Middleware & Access Control](#middleware--access-control)
8. [Code Examples](#code-examples)
9. [Best Practices](#best-practices)
10. [Testing](#testing)

---

## Overview

Team Management is a **core feature** providing organization-scoped team collaboration with role-based access control. Teams enable logical grouping of users for module access, project collaboration, and permission management.

### Key Features

✅ **Organization-Scoped** - Teams belong to organizations with strict isolation  
✅ **Hierarchical Structure** - Parent-child team relationships with unlimited depth  
✅ **Role-Based Access** - Leader, Member, and Viewer roles  
✅ **JWT Integration** - Zero-query team validation via JWT tokens  
✅ **Module Assignment** - Optional team-level module access control  
✅ **Middleware Protection** - Route-level team access enforcement  
✅ **Soft Deletes** - Recover deleted teams  
✅ **Audit Trail** - Track who created teams and invited members  

---

## Database Structure

### Tables

#### 1. `teams` Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `organization_id` | uuid | Foreign key to organizations |
| `parent_team_id` | bigint (nullable) | Parent team for hierarchies |
| `name` | string(100) | Team display name |
| `slug` | string(100) | URL-friendly identifier (unique per org) |
| `description` | text (nullable) | Team description |
| `avatar` | string (nullable) | Team avatar/logo URL |
| `color` | string(7) (nullable) | Hex color code (#RRGGBB) |
| `created_by` | bigint | User who created the team |
| `is_active` | boolean | Active status (default: true) |
| `metadata` | json (nullable) | Additional custom data |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Last update timestamp |
| `deleted_at` | timestamp (nullable) | Soft delete timestamp |

**Indexes:**
- `organization_id, slug` (unique)
- `organization_id, is_active`
- `parent_team_id`

---

#### 2. `team_user` Pivot Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `team_id` | bigint | Foreign key to teams |
| `user_id` | bigint | Foreign key to users |
| `role` | enum | leader, member, viewer |
| `invited_by` | bigint (nullable) | User who added this member |
| `joined_at` | timestamp | When user joined |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

**Indexes:**
- `team_id, user_id` (unique)
- `user_id`

---

#### 3. `module_team` Pivot Table (Optional)

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `team_id` | bigint | Foreign key to teams |
| `module_id` | bigint | Foreign key to modules |
| `is_active` | boolean | Active status |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Last update |

**Indexes:**
- `team_id, module_id` (unique)

---

## Team Roles & Permissions

### Role Hierarchy

| Role | Access Level | Description |
|------|-------------|-------------|
| **Leader** | High | Manage team, add/remove members, assign roles |
| **Member** | Standard | Collaborate, create/edit own content |
| **Viewer** | Read-only | View team content without editing |

---

### Permission Matrix

| Action | Leader | Member | Viewer |
|--------|:------:|:------:|:------:|
| **Team Management** ||||
| Edit team settings | ✅ | ❌ | ❌ |
| Delete team | ✅ | ❌ | ❌ |
| Archive/restore team | ✅ | ❌ | ❌ |
| **Member Management** ||||
| Add members | ✅ | ❌ | ❌ |
| Remove members | ✅ | ❌ | ❌ |
| Update member roles | ✅ | ❌ | ❌ |
| **Content Access** ||||
| View team content | ✅ | ✅ | ✅ |
| Create content | ✅ | ✅ | ❌ |
| Edit own content | ✅ | ✅ | ❌ |
| Edit others' content | ✅ | ❌ | ❌ |
| Delete content | ✅ | ❌ | ❌ |
| **Module Access** ||||
| Assign modules to team | ✅ | ❌ | ❌ |
| Access assigned modules | ✅ | ✅ | ✅* |

*\* Viewer can access if team has module enabled*

---

### Common Team Structures

#### **Startup Team** (5-10 people)
```
Leader (1)     - Founder/CEO
├── Member (5-7) - Developers, designers, marketers
└── Viewer (1)   - Investor/advisor
```

#### **Sales Team**
```
Leader (1)     - Sales Director
├── Member (10+) - Sales representatives
└── Viewer (2)   - Finance analyst, CEO
```

#### **Development Team** (with hierarchy)
```
Development Team (Leader: Engineering Manager)
├── Frontend Team (Leader: Frontend Lead)
│   └── Members: Frontend developers
├── Backend Team (Leader: Backend Lead)
│   └── Members: Backend developers
└── DevOps Team (Leader: DevOps Lead)
    └── Members: Infrastructure engineers
```

---

## API Reference

### Authentication

All endpoints require:
```http
Authorization: Bearer {jwt_token}
X-Organization-ID: {organization_uuid}
```

---

### Endpoints

#### 1. List All Teams

```http
GET /api/teams
```

**Query Parameters:**
- `include_inactive` (boolean) - Include archived teams
- `parent_id` (int) - Filter by parent team

**Response (200):**
```json
{
  "teams": [
    {
      "id": 1,
      "name": "Sales Team",
      "slug": "sales",
      "description": "Sales and customer acquisition",
      "color": "#10B981",
      "parent_team_id": null,
      "users_count": 5,
      "created_by": 1,
      "is_active": true,
      "created_at": "2025-11-13T10:00:00Z",
      "updated_at": "2025-11-13T10:00:00Z"
    }
  ],
  "total": 8
}
```

---

#### 2. Get My Teams

```http
GET /api/teams/my
```

**Response (200):**
```json
{
  "teams": [
    {
      "id": 1,
      "name": "Sales Team",
      "slug": "sales",
      "role": "leader",
      "joined_at": "2025-11-01T09:00:00Z"
    },
    {
      "id": 3,
      "name": "Marketing Team",
      "slug": "marketing",
      "role": "member",
      "joined_at": "2025-11-05T14:30:00Z"
    }
  ],
  "total": 2
}
```

---

#### 3. Get Team Details

```http
GET /api/teams/{id}
```

**Response (200):**
```json
{
  "team": {
    "id": 1,
    "name": "Sales Team",
    "slug": "sales",
    "description": "Sales and customer acquisition team",
    "color": "#10B981",
    "parent_team_id": null,
    "is_active": true,
    "created_by": 1,
    "members": [
      {
        "id": 2,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "leader",
        "joined_at": "2025-11-01T09:00:00Z"
      },
      {
        "id": 5,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "role": "member",
        "joined_at": "2025-11-05T10:00:00Z"
      }
    ],
    "members_count": 2,
    "sub_teams": [],
    "modules": [
      {
        "id": 3,
        "name": "CRM",
        "slug": "crm"
      }
    ]
  }
}
```

---

#### 4. Create Team

```http
POST /api/teams
Content-Type: application/json

{
  "name": "Product Team",
  "slug": "product",
  "description": "Product management and strategy",
  "color": "#3B82F6",
  "parent_team_id": null
}
```

**Validation Rules:**
- `name` - required, string, max:100
- `slug` - required, string, max:100, unique per organization, alphanumeric + dashes
- `description` - nullable, string
- `color` - nullable, regex:/^#[0-9A-F]{6}$/i
- `parent_team_id` - nullable, exists:teams,id (must be in same organization)

**Response (201):**
```json
{
  "team": {
    "id": 9,
    "name": "Product Team",
    "slug": "product",
    "description": "Product management and strategy",
    "color": "#3B82F6",
    "parent_team_id": null,
    "created_by": 1,
    "is_active": true,
    "created_at": "2025-11-13T15:30:00Z"
  },
  "message": "Team created successfully"
}
```

**Error (422):**
```json
{
  "message": "Validation failed",
  "errors": {
    "slug": ["The slug has already been taken in this organization."]
  }
}
```

---

#### 5. Update Team

```http
PUT /api/teams/{id}
Content-Type: application/json

{
  "name": "Product & Strategy Team",
  "description": "Product management, strategy, and roadmap planning",
  "color": "#6366F1"
}
```

**Response (200):**
```json
{
  "team": {
    "id": 9,
    "name": "Product & Strategy Team",
    "slug": "product",
    "description": "Product management, strategy, and roadmap planning",
    "color": "#6366F1",
    "updated_at": "2025-11-13T16:00:00Z"
  },
  "message": "Team updated successfully"
}
```

---

#### 6. Delete Team

```http
DELETE /api/teams/{id}
```

**Notes:**
- Soft delete by default
- Removes all team memberships
- Sub-teams become root teams (parent_team_id set to null)

**Response (200):**
```json
{
  "message": "Team deleted successfully"
}
```

**Error (403):**
```json
{
  "success": false,
  "message": "Only team leaders can delete this team"
}
```

---

#### 7. List Team Members

```http
GET /api/teams/{id}/members
```

**Query Parameters:**
- `role` (string) - Filter by role: leader, member, viewer

**Response (200):**
```json
{
  "members": [
    {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com",
      "avatar": "https://...",
      "role": "leader",
      "invited_by": null,
      "joined_at": "2025-11-01T09:00:00Z"
    },
    {
      "id": 5,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "avatar": null,
      "role": "member",
      "invited_by": 2,
      "joined_at": "2025-11-05T10:00:00Z"
    }
  ],
  "total": 2,
  "by_role": {
    "leader": 1,
    "member": 1,
    "viewer": 0
  }
}
```

---

#### 8. Add Team Member

```http
POST /api/teams/{id}/members
Content-Type: application/json

{
  "user_id": 7,
  "role": "member"
}
```

**Validation:**
- `user_id` - required, exists in organization
- `role` - required, in:leader,member,viewer

**Response (200):**
```json
{
  "member": {
    "id": 7,
    "name": "Bob Wilson",
    "email": "bob@example.com",
    "role": "member",
    "joined_at": "2025-11-13T16:30:00Z"
  },
  "message": "Member added successfully"
}
```

**Error (409):**
```json
{
  "success": false,
  "message": "User is already a member of this team"
}
```

---

#### 9. Update Member Role

```http
PUT /api/teams/{team_id}/members/{user_id}
Content-Type: application/json

{
  "role": "leader"
}
```

**Response (200):**
```json
{
  "member": {
    "id": 7,
    "name": "Bob Wilson",
    "role": "leader"
  },
  "message": "Member role updated successfully"
}
```

---

#### 10. Remove Team Member

```http
DELETE /api/teams/{team_id}/members/{user_id}
```

**Response (200):**
```json
{
  "message": "Member removed successfully"
}
```

**Error (400):**
```json
{
  "success": false,
  "message": "Cannot remove the last leader from the team"
}
```

---

#### 11. Assign Modules to Team

```http
POST /api/teams/{id}/modules
Content-Type: application/json

{
  "module_ids": [1, 3, 5]
}
```

**Response (200):**
```json
{
  "modules": [
    {
      "id": 1,
      "name": "CRM",
      "slug": "crm"
    },
    {
      "id": 3,
      "name": "Accounting",
      "slug": "accounting"
    }
  ],
  "message": "Modules assigned successfully"
}
```

---

## Hierarchical Teams

### Creating Parent-Child Relationships

```http
POST /api/teams
Content-Type: application/json

{
  "name": "Frontend Team",
  "slug": "frontend",
  "description": "Frontend development sub-team",
  "parent_team_id": 2,
  "color": "#6366F1"
}
```

**Example Structure:**
```
Development Team (ID: 2)
├── Frontend Team (ID: 10, parent_team_id: 2)
├── Backend Team (ID: 11, parent_team_id: 2)
└── DevOps Team (ID: 12, parent_team_id: 2)
```

---

### Accessing Parent/Child Teams

```php
// Get parent team
$team = Team::find(10); // Frontend Team
$parent = $team->parentTeam; // Development Team

// Get sub-teams
$devTeam = Team::find(2);
$subTeams = $devTeam->subTeams; // [Frontend, Backend, DevOps]

// Get root teams (no parent)
$rootTeams = Team::whereNull('parent_team_id')
    ->where('organization_id', $orgId)
    ->get();
```

---

### Use Cases for Hierarchies

1. **Department → Sub-departments**
   - Engineering → Frontend, Backend, QA
   - Sales → Enterprise, SMB, Partnerships

2. **Project → Feature Teams**
   - Project Alpha → Auth Team, UI Team, API Team

3. **Geographic → Regional**
   - Global Sales → APAC, EMEA, Americas

---

## JWT Integration

### Team Slugs in JWT Token

Teams are embedded in JWT tokens as an array of slugs for zero-query validation.

**JWT Payload:**
```json
{
  "sub": 1,
  "organization_id": "uuid",
  "roles": ["admin"],
  "permissions": ["users.create"],
  "modules": ["crm", "sales"],
  "teams": ["sales", "dev", "support"]
}
```

---

### Updated on Every Login/Refresh

Teams are automatically included when:

1. **Login** - `POST /api/login`
2. **Token Refresh** - `POST /api/refresh`
3. **Switch Organization** - `POST /api/organizations/{id}/switch`

**Implementation in `AuthController`:**
```php
// Get user's team slugs in current organization
$teamSlugs = $user->teams()
    ->where('organization_id', $currentOrganization['id'])
    ->pluck('slug')
    ->toArray();

$customClaims = [
    'organization_id' => $currentOrganization['id'],
    'roles' => $rolesSlugs,
    'permissions' => $permissionsSlugs,
    'modules' => $moduleSlugs,
    'teams' => $teamSlugs,  // Added here
];

$token = JWTAuth::customClaims($customClaims)->fromUser($user);
```

---

### Token Size Impact

**Example with 3 teams:**
```json
"teams": ["sales", "dev", "support"]
```
**Size:** ~35 bytes

**Example with 10 teams:**
```json
"teams": ["sales", "dev", "support", "marketing", "hr", "finance", "ops", "product", "legal", "admin"]
```
**Size:** ~110 bytes

**Recommendation:** Keep team slugs short (3-15 characters) to minimize JWT size.

---

## Middleware & Access Control

### `CheckTeamAccess` Middleware

**File:** `app/Http/Middleware/CheckTeamAccess.php`  
**Alias:** `team.access`

**Features:**
- ✅ Validates team access from JWT token
- ✅ Zero database queries
- ✅ Supports single/multiple/any team patterns
- ✅ Clear error messages

---

### Usage Patterns

#### 1. Single Team Required

```php
Route::middleware(['auth:api', 'team.access:sales'])
    ->get('/sales-dashboard', [SalesController::class, 'dashboard']);
```

**Behavior:**
- User must be in "sales" team
- 403 if not in team

---

#### 2. Multiple Teams (OR logic)

```php
Route::middleware(['auth:api', 'team.access:sales,marketing'])
    ->get('/customer-reports', [ReportController::class, 'customers']);
```

**Behavior:**
- User must be in "sales" OR "marketing" team
- 403 if not in any of the specified teams

---

#### 3. Any Team (Just Check Membership)

```php
Route::middleware(['auth:api', 'team.access'])
    ->get('/team-features', [FeatureController::class, 'index']);
```

**Behavior:**
- User must be in at least one team
- 403 if not in any team

---

### Error Responses

**No Teams (403):**
```json
{
  "success": false,
  "message": "You are not a member of any team in this organization."
}
```

**Specific Team Required (403):**
```json
{
  "success": false,
  "message": "Access denied. You must be a member of the 'sales' team to access this resource.",
  "required_team": "sales",
  "your_teams": ["dev", "support"]
}
```

**Multiple Teams Required (403):**
```json
{
  "success": false,
  "message": "Access denied. You must be a member of one of these teams: sales, marketing",
  "required_teams": ["sales", "marketing"],
  "your_teams": ["dev", "support"]
}
```

---

## Code Examples

### Model Methods

#### User Model

```php
use App\Models\User;

$user = User::find(1);

// Get all teams
$teams = $user->teams;

// Get teams in specific organization
$orgTeams = $user->teamsInOrganization($organizationId);

// Get teams where user is leader
$leadingTeams = $user->leadingTeams;

// Check if user belongs to team
if ($user->belongsToTeam($team)) {
    // User is in this team
}

// Check if user is leader
if ($user->isTeamLeader($team)) {
    // User is a leader of this team
}

// Get user's role in team
$role = $user->getTeamRole($team); // 'leader', 'member', or 'viewer'
```

---

#### Team Model

```php
use App\Models\Team;

$team = Team::find(1);

// Get all members
$members = $team->users;

// Get only leaders
$leaders = $team->leaders;

// Get only regular members
$members = $team->members;

// Get only viewers
$viewers = $team->viewers;

// Check if user is member
if ($team->hasMember($user)) {
    // User is in this team
}

// Check if user is leader
if ($team->hasLeader($user)) {
    // User is a leader
}

// Add member
$team->addMember($user, 'member');

// Remove member
$team->removeMember($user);

// Update role
$team->updateMemberRole($user, 'leader');

// Get member count
$count = $team->getMemberCount();

// Get sub-teams (children)
$subTeams = $team->subTeams;

// Get parent team
$parent = $team->parentTeam;
```

---

#### Organization Model

```php
use App\Models\Organization;

$org = Organization::find('uuid');

// Get all teams
$teams = $org->teams;

// Get only active teams
$activeTeams = $org->activeTeams;

// Get root teams (no parent)
$rootTeams = $org->rootTeams;
```

---

### Controller Examples

#### Protecting Controller Method

```php
use Illuminate\Http\Request;
use App\Models\Team;

class SalesController extends Controller
{
    public function dashboard(Request $request)
    {
        // Middleware already validated team access
        // Get team from JWT
        $teamsSlugs = auth()->payload()->get('teams', []);
        
        if (!in_array('sales', $teamsSlugs)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }
        
        $salesTeam = Team::where('slug', 'sales')
            ->where('organization_id', $request->header('X-Organization-ID'))
            ->first();
        
        $stats = [
            'total_members' => $salesTeam->getMemberCount(),
            'deals' => Deal::where('team_id', $salesTeam->id)->count(),
            // ... more stats
        ];
        
        return response()->json(['dashboard' => $stats]);
    }
}
```

---

#### Team-Scoped Query

```php
public function getTeamProjects(Request $request, Team $team)
{
    // Verify user is in this team
    $user = auth()->user();
    
    if (!$user->belongsToTeam($team)) {
        return response()->json([
            'success' => false,
            'message' => 'You are not a member of this team'
        ], 403);
    }
    
    // Get projects for this team
    $projects = Project::where('team_id', $team->id)
        ->with(['tasks', 'members'])
        ->get();
    
    return response()->json(['projects' => $projects]);
}
```

---

### Frontend Integration

```javascript
// Decode JWT to get teams
function getUserTeams(token) {
  const payload = parseJwt(token);
  return payload.teams || []; // ['sales', 'dev', 'support']
}

// Check if user is in team
function isInTeam(token, teamSlug) {
  const teams = getUserTeams(token);
  return teams.includes(teamSlug);
}

// Show/hide UI based on team membership
const teams = getUserTeams(localStorage.getItem('jwt_token'));

if (teams.includes('sales')) {
  document.getElementById('sales-dashboard').style.display = 'block';
}

if (teams.includes('dev') || teams.includes('devops')) {
  document.getElementById('deployment-button').style.display = 'block';
}

// Display user's teams
const teamBadges = teams.map(team => 
  `<span class="badge">${team}</span>`
).join('');
document.getElementById('user-teams').innerHTML = teamBadges;
```

---

## Best Practices

### 1. Team Naming Conventions

**✅ Good:**
- `sales` - Short, clear
- `dev` - Common abbreviation
- `support-tier1` - Descriptive with context

**❌ Avoid:**
- `the-best-sales-team-ever` - Too long
- `Team 1` - Not descriptive
- `sales_TEAM` - Inconsistent casing

---

### 2. Slug Standards

- Lowercase only
- Use hyphens for spaces
- 3-20 characters recommended
- Alphanumeric + hyphens only
- Unique per organization

**Examples:**
```
sales → ✅
dev-frontend → ✅
hr-recruiting → ✅
SALES → ❌ (use lowercase)
dev_team → ❌ (use hyphens)
super-long-team-name-that-is-too-descriptive → ❌ (too long)
```

---

### 3. Role Assignment Guidelines

**Leaders:**
- 1-3 per team recommended
- Must have authority and responsibility
- Can manage team completely

**Members:**
- Default role for collaborators
- Majority of team members
- Standard access for work

**Viewers:**
- Use sparingly (stakeholders, clients)
- Read-only access
- Good for transparency

---

### 4. Hierarchy Best Practices

**When to use hierarchies:**
- Large organizations (>50 people)
- Clear reporting structures
- Departmental organization
- Project-based teams

**When NOT to use:**
- Small teams (<20 people)
- Flat organizations
- Cross-functional collaboration

**Depth recommendations:**
- Max 3-4 levels deep
- Avoid complex nesting
- Keep it simple

---

### 5. Team Size Recommendations

| Team Size | Structure | Roles |
|-----------|-----------|-------|
| 2-5 people | Flat | 1 leader, rest members |
| 6-15 people | Flat | 1-2 leaders, rest members |
| 16-30 people | Hierarchical | Parent team + 2-3 sub-teams |
| 30+ people | Hierarchical | Department + multiple sub-teams |

---

### 6. Security Considerations

**✅ Do:**
- Always validate team membership server-side
- Use JWT middleware for route protection
- Verify leader status before admin actions
- Log team membership changes
- Audit team deletions

**❌ Don't:**
- Trust client-side team checks
- Allow members to assign leader roles
- Permit last leader removal
- Share teams across organizations
- Expose team data without auth

---

## Testing

### Unit Tests

```php
use Tests\TestCase;
use App\Models\{User, Team, Organization};

class TeamTest extends TestCase
{
    public function test_user_can_create_team()
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);

        $response = $this->actingAs($user)
            ->withHeader('X-Organization-ID', $org->id)
            ->postJson('/api/teams', [
                'name' => 'Test Team',
                'slug' => 'test-team',
                'description' => 'A test team'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('team.name', 'Test Team');

        $this->assertDatabaseHas('teams', [
            'slug' => 'test-team',
            'organization_id' => $org->id
        ]);
    }

    public function test_leader_can_add_member()
    {
        $leader = User::factory()->create();
        $member = User::factory()->create();
        $org = Organization::factory()->create();
        $team = Team::factory()->create(['organization_id' => $org->id]);
        
        $team->addMember($leader, 'leader');
        $org->users()->attach([$leader->id, $member->id]);

        $response = $this->actingAs($leader)
            ->withHeader('X-Organization-ID', $org->id)
            ->postJson("/api/teams/{$team->id}/members", [
                'user_id' => $member->id,
                'role' => 'member'
            ]);

        $response->assertStatus(200);

        $this->assertTrue($team->hasMember($member));
        $this->assertEquals('member', $member->getTeamRole($team));
    }

    public function test_member_cannot_remove_members()
    {
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        $org = Organization::factory()->create();
        $team = Team::factory()->create(['organization_id' => $org->id]);
        
        $team->addMember($member1, 'member');
        $team->addMember($member2, 'member');

        $response = $this->actingAs($member1)
            ->withHeader('X-Organization-ID', $org->id)
            ->deleteJson("/api/teams/{$team->id}/members/{$member2->id}");

        $response->assertStatus(403);
    }

    public function test_jwt_contains_team_slugs()
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);
        
        $team1 = Team::factory()->create(['slug' => 'sales', 'organization_id' => $org->id]);
        $team2 = Team::factory()->create(['slug' => 'dev', 'organization_id' => $org->id]);
        
        $team1->addMember($user, 'member');
        $team2->addMember($user, 'leader');

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'organization_id' => $org->id
        ]);

        $token = $response->json('access_token');
        $payload = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();
        $teams = $payload->get('teams');

        $this->assertIsArray($teams);
        $this->assertContains('sales', $teams);
        $this->assertContains('dev', $teams);
    }

    public function test_middleware_blocks_non_team_members()
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);
        
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->withHeader('X-Organization-ID', $org->id)
            ->getJson('/api/sales-dashboard'); // Requires 'sales' team

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_cannot_remove_last_leader()
    {
        $leader = User::factory()->create();
        $org = Organization::factory()->create();
        $team = Team::factory()->create(['organization_id' => $org->id]);
        
        $team->addMember($leader, 'leader');

        $response = $this->actingAs($leader)
            ->withHeader('X-Organization-ID', $org->id)
            ->deleteJson("/api/teams/{$team->id}/members/{$leader->id}");

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_hierarchical_teams()
    {
        $org = Organization::factory()->create();
        $parent = Team::factory()->create(['organization_id' => $org->id]);
        $child = Team::factory()->create([
            'organization_id' => $org->id,
            'parent_team_id' => $parent->id
        ]);

        $this->assertEquals($parent->id, $child->parent_team_id);
        $this->assertTrue($parent->subTeams->contains($child));
        $this->assertEquals($parent->id, $child->parentTeam->id);
    }
}
```

---

### Integration Tests

```bash
# Run all team tests
php artisan test --filter=TeamTest

# Test team creation flow
php artisan test --filter=test_user_can_create_team

# Test middleware
php artisan test --filter=test_middleware_blocks_non_team_members

# Test JWT integration
php artisan test --filter=test_jwt_contains_team_slugs
```

---

### Manual Testing

```bash
# 1. Create team
curl -X POST http://localhost/api/teams \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Organization-ID: $ORG_ID" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "QA Team",
    "slug": "qa",
    "description": "Quality Assurance",
    "color": "#F59E0B"
  }'

# 2. Add member
curl -X POST http://localhost/api/teams/1/members \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Organization-ID: $ORG_ID" \
  -d '{"user_id": 5, "role": "member"}'

# 3. List my teams
curl -X GET http://localhost/api/teams/my \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Organization-ID: $ORG_ID"

# 4. Test middleware protection
curl -X GET http://localhost/api/sales-dashboard \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Organization-ID: $ORG_ID"
# Should return 403 if user not in sales team
```

---

## Database Seeding

The `TeamSeeder` creates 8 default teams per organization:

```php
php artisan db:seed --class=TeamSeeder
```

**Seeded Teams:**

| Team | Slug | Color | Hierarchy |
|------|------|-------|-----------|
| Sales Team | `sales` | #10B981 (Green) | Root |
| Development Team | `dev` | #3B82F6 (Blue) | Root |
| ├─ Frontend Team | `frontend` | #6366F1 (Indigo) | Child of Dev |
| └─ Backend Team | `backend` | #8B5CF6 (Purple) | Child of Dev |
| Support Team | `support` | #F59E0B (Amber) | Root |
| Marketing Team | `marketing` | #EC4899 (Pink) | Root |
| HR Team | `hr` | #EF4444 (Red) | Root |
| Finance Team | `finance` | #14B8A6 (Teal) | Root |

---

## Migration Reference

```bash
# Run team migrations
php artisan migrate

# Rollback team tables
php artisan migrate:rollback --step=1

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

**Migration File:**
`database/migrations/2025_11_13_100001_create_teams_table.php`

---

## Troubleshooting

### "Team slug already exists"

**Cause:** Duplicate slug in same organization

**Solution:**
```bash
# Check existing teams
SELECT id, name, slug, organization_id FROM teams WHERE organization_id = 'uuid';

# Use different slug
"slug": "sales-emea"  # Instead of just "sales"
```

---

### "Cannot remove last leader"

**Cause:** Attempting to remove the only leader from a team

**Solution:**
```bash
# Add another leader first
POST /api/teams/{id}/members/{user_id}
{"role": "leader"}

# Then remove the original leader
DELETE /api/teams/{id}/members/{old_leader_id}
```

---

### Team not appearing in JWT

**Cause:** Token issued before team membership

**Solution:**
```bash
# Refresh token
POST /api/refresh
Authorization: Bearer {old_token}

# Or re-login
POST /api/login
```

---

### Middleware blocking despite team membership

**Cause:** 
1. Token not refreshed after joining team
2. Slug mismatch

**Solutions:**
```bash
# 1. Decode JWT to verify teams
echo $TOKEN | cut -d'.' -f2 | base64 -d | jq '.teams'

# 2. Refresh token
POST /api/refresh

# 3. Verify team slug in database
SELECT slug FROM teams WHERE id = X;
```

---

## Summary

### Quick Reference

| Task | Endpoint / Method |
|------|-------------------|
| **List teams** | `GET /api/teams` |
| **My teams** | `GET /api/teams/my` |
| **Create team** | `POST /api/teams` |
| **Update team** | `PUT /api/teams/{id}` |
| **Delete team** | `DELETE /api/teams/{id}` |
| **Add member** | `POST /api/teams/{id}/members` |
| **Remove member** | `DELETE /api/teams/{id}/members/{user_id}` |
| **Update role** | `PUT /api/teams/{id}/members/{user_id}` |
| **Protect route** | `Route::middleware(['team.access:sales'])` |
| **Check membership** | `$user->belongsToTeam($team)` |
| **Check leader** | `$user->isTeamLeader($team)` |
| **Get JWT teams** | `auth()->payload()->get('teams')` |

---

**Last Updated:** November 13, 2025  
**Version:** 1.0.0  
**Status:** ✅ Production Ready

---

For more documentation:
- [JWT Documentation](/docs/JWT.md)
- [Security Features](/docs/SECURITY.md)
- [Roles & Permissions](/docs/ROLES_PERMISSIONS.md)
- [API Reference](/docs/API_REFERENCE.md)
