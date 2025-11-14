# Documentation Index

> **Complete documentation for Yiire Multi-Organization ERP Core Microservice**

Welcome to the comprehensive documentation for the authentication and authorization microservice. All documentation has been consolidated into single-source guides for each feature area.

---

## 🚀 Getting Started

### [📚 Getting Started Guide](./GETTING_STARTED.md)
Complete setup and installation instructions:
- Installation steps & requirements
- Environment configuration
- Database setup & migrations
- UUID implementation details
- First-time setup guide
- Docker deployment

---

## 🔐 Core Features

### [� JWT Authentication](./JWT.md) **← COMPREHENSIVE GUIDE**
Complete JWT token implementation, validation, and microservice integration:
- **JWT Token Structure** - Enhanced claims with 11+ fields
- **Module Validation** - Zero-query module access validation
- **Subscription Validation** - JWT-based subscription status checking
- **Owner Validation** - Fast owner status verification
- **Middleware Reference** - 6 ready-to-use middleware
- **Microservice Integration** - Node.js, Python, Go examples
- **Performance Optimization** - 10-50x faster than DB validation
- **Security Best Practices** - Token lifecycle, secrets, HTTPS

**Key Topics:**
- Organization context in JWT
- Team slugs for access control
- Module slugs for feature validation
- Subscription status enforcement
- User limits and billing integration

---

### [🛡️ Security Features](./SECURITY.md) **← COMPREHENSIVE GUIDE**
Complete security implementation guide:
- **Email Verification** - Automated verification flow with secure tokens
- **Password Reset** - Secure forgot password with anti-enumeration
- **Two-Factor Authentication (2FA)** - TOTP with QR codes & recovery codes
- **Rate Limiting** - Multi-tier protection against abuse
- **JWT Security** - Token lifecycle, blacklisting, refresh strategy
- **Best Practices** - HTTPS, secrets rotation, input validation
- **Security Checklist** - Pre-launch and maintenance tasks

**API Endpoints Covered:**
- `/api/email/verify` - Email verification
- `/api/password/reset` - Password reset
- `/api/2fa/enable` - 2FA setup
- All security-related middleware

---

### [👥 Team Management](./TEAMS.md) **← COMPREHENSIVE GUIDE**
Organization-scoped team collaboration with RBAC:
- **Team Structure** - Hierarchical teams with parent-child relationships
- **Team Roles** - Leader, Member, Viewer permissions
- **API Reference** - 11 endpoints for complete team management
- **JWT Integration** - Team slugs in tokens for zero-query validation
- **Middleware** - `team.access` for route-level protection
- **Code Examples** - User, Team, Organization model methods
- **Best Practices** - Team naming, hierarchy depth, size recommendations

**Use Cases:**
- Department organization (Sales, Dev, Support)
- Project-based teams with sub-teams
- Cross-functional collaboration
- Team-scoped module access

---

### [🔐 Authentication & Authorization](./AUTHENTICATION.md)
Multi-organization authentication and JWT flow:
- Multi-organization architecture
- Login with organization context
- Switch organization feature
- Role-based access control (RBAC)
- Organization-scoped permissions
- Frontend implementation examples

---

### [👤 User Management](./USER_MANAGEMENT.md)
Enhanced user profiles and management:
- 20+ profile fields (personal, professional, preferences)
- Avatar upload/delete
- Password management
- Email preferences
- Notification settings
- Profile update endpoints

---

### [🏢 Multi-Organization](./MULTI_ORGANIZATION.md)
Organization management and tenancy:
- Organization creation & management
- User-organization relationships
- Organization settings
- Subscription management
- Usage tracking
- Billing integration

---

### [🎭 Roles & Permissions](./GLOBAL_ROLES_PERMISSIONS.md)
System-wide and organization-scoped RBAC:
- Global vs organization-scoped roles
- Super admin privileges
- Permission inheritance
- Role assignment
- Security considerations
- Best practices

---

### [📦 Modules](./MODULE_ACCESS.md)
Modular ERP architecture with 68+ modules:
- Module enable/disable per organization
- Module-based pricing
- Module access validation (JWT & DB)
- Module configuration
- Module expiration
- Module limits & settings

---

### [💳 Billing & Subscriptions](./BILLING_INTEGRATION.md)
Comprehensive billing integration:
- User-based pricing
- Module-based pricing
- Subscription status management
- Usage tracking APIs
- Limit enforcement middleware
- Webhooks & events

---

## 📖 Reference

### [📡 API Reference](./API_REFERENCE.md)
Complete endpoint documentation:
- All available endpoints grouped by feature
- Request/response examples
- Authentication requirements
- Error codes & handling
- Rate limiting details
- Pagination standards

---

## 🚀 Quick Reference

### Common API Endpoints

```bash
# Authentication
POST   /api/login              # Login with organization
POST   /api/register           # Register new user
POST   /api/logout             # Logout and blacklist token
POST   /api/refresh            # Refresh JWT token
POST   /api/organizations/{id}/switch  # Switch organization

# Security
POST   /api/email/verify       # Verify email address
POST   /api/password/reset     # Reset password
POST   /api/2fa/enable         # Enable 2FA
POST   /api/2fa/verify         # Verify 2FA code

# User Profile
GET    /api/me                 # Get current user
PUT    /api/profile            # Update profile
POST   /api/profile/avatar     # Upload avatar
PUT    /api/profile/password   # Change password

# Teams
GET    /api/teams              # List all teams
GET    /api/teams/my           # Get my teams
POST   /api/teams              # Create team
POST   /api/teams/{id}/members # Add team member

# Organizations
GET    /api/organizations      # List my organizations
POST   /api/organizations      # Create organization
GET    /api/organizations/{id}/modules  # List modules

# Billing
GET    /api/billing/organizations/{id}/usage  # Get usage data
PUT    /api/billing/organizations/{id}/subscription  # Update subscription
```

---

## 🏗️ Architecture

### Database Structure

```
users (20+ fields)
  └── organization_user (many-to-many with roles)
        └── organizations (UUID primary key)
              ├── teams (hierarchical, org-scoped)
              │     └── team_user (with roles: leader/member/viewer)
              ├── modules (68+ available)
              │     └── module_organization (enabled modules)
              ├── roles (organization-scoped RBAC)
              │     ├── role_user
              │     └── permission_role
              └── permissions (organization-scoped)
                    └── permission_user
```

---

## 📊 Enhanced JWT Token Structure

```json
{
  "sub": 1,
  "organization_id": "uuid",
  "organization_slug": "acme-corp",
  "is_owner": false,
  "subscription_status": "active",
  "max_users": 50,
  "roles": ["admin", "manager"],
  "permissions": ["users.create", "users.edit"],
  "modules": ["accounting", "crm", "inventory"],
  "teams": ["sales", "dev", "support"],
  "user_modules": ["dash", "crm"]
}
```

---

## 🔧 Middleware Reference

| Middleware | Alias | Purpose |
|------------|-------|---------|
| Authenticate | `auth:api` | JWT authentication |
| CheckSubscriptionStatus | `subscription.active` | Block suspended orgs |
| CheckUserLimit | `user.limit` | Enforce user count limits |
| CheckOwnerAccess | `owner.only` | Owner-only routes |
| CheckModuleAccessFromJWT | `module.jwt:slug` | Fast module validation |
| CheckModuleAccess | `module.access:slug` | DB module validation |
| CheckTeamAccess | `team.access:slug` | Team membership validation |

---

## 📚 Additional Resources

### Implementation Guides (Archived)
Legacy implementation summaries have been consolidated into the main guides above:
- `JWT_MODULE_IMPLEMENTATION.md` → Merged into **JWT.md**
- `JWT_ENHANCEMENTS_SUMMARY.md` → Merged into **JWT.md**
- `TEAM_IMPLEMENTATION_SUMMARY.md` → Merged into **TEAMS.md**
- `BILLING_IMPLEMENTATION.md` → Merged into **BILLING_INTEGRATION.md**

### Quick References (Archived)
Quick reference cards have been integrated into comprehensive guides:
- `JWT_ENHANCEMENTS_QUICKREF.md` → See **JWT.md**
- `TEAM_MANAGEMENT_QUICKREF.md` → See **TEAMS.md**
- `GLOBAL_ROLES_QUICKREF.md` → See **ROLES_PERMISSIONS.md**
- `MODULE_ACCESS_QUICKREF.md` → See **MODULE_ACCESS.md**

---

## 🎯 Documentation Standards

All consolidated documentation follows these standards:
- ✅ **Single Source of Truth** - One comprehensive guide per feature
- ✅ **Complete Coverage** - Database, API, middleware, examples, testing
- ✅ **Code Examples** - PHP, JavaScript, Python, Go where applicable
- ✅ **Best Practices** - Security, performance, architecture guidance
- ✅ **Troubleshooting** - Common issues and solutions
- ✅ **Version Tracking** - Last updated dates and version numbers

---

## 🆘 Getting Help

- **📧 Email:** support@yiire.com
- **🐛 Issues:** https://github.com/yiire-erp/auth/issues
- **💬 Discussions:** https://github.com/yiire-erp/auth/discussions
- **📖 Docs:** https://docs.yiire.com

---

**Last Updated:** November 13, 2025  
**Documentation Version:** 2.0.0  
**Status:** ✅ Consolidated & Production Ready

### Key Concepts

1. **Multi-Tenancy**: Users can belong to multiple organizations with different roles
2. **JWT Tokens**: Contain embedded permissions for current organization
3. **UUID Organization IDs**: Secure, non-enumerable organization identifiers
4. **Soft Deletes**: Users and organizations are never permanently deleted
5. **Organization Scoping**: All roles and permissions are scoped to specific organizations

## Documentation Files

| File | Description |
|------|-------------|
| `GETTING_STARTED.md` | Setup guide and UUID implementation |
| `AUTHENTICATION.md` | Multi-organization auth and JWT documentation |
| `API_REFERENCE.md` | Complete API endpoint reference |
| `USER_MANAGEMENT.md` | User profile features and management |
| `MULTI_ORGANIZATION.md` | Organization and RBAC system documentation |
| `GLOBAL_ROLES_PERMISSIONS.md` | Global roles/permissions and super admin features |

## Need Help?

- Check the specific guide for your topic
- Review API examples in each documentation file
- See code examples in `USER_MANAGEMENT.md` for frontend integration
- Review `AUTHENTICATION.md` for JWT implementation details

## Version Information

- **Documentation Version:** 1.0.0
- **Last Updated:** November 12, 2025
- **Laravel Version:** 12.x
- **PHP Version:** 8.2+
- **JWT Package:** tymon/jwt-auth 2.2+
