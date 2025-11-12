# Documentation Index

Welcome to the Multi-Tenant Authorization Microservice documentation.

## Getting Started

### 📚 [Getting Started Guide](./GETTING_STARTED.md)
Complete setup and installation instructions, including:
- Installation steps
- Environment configuration
- Database setup
- UUID implementation details
- First-time setup guide

## Core Documentation

### 🔐 [Authentication Guide](./AUTHENTICATION.md)
JWT authentication and multi-tenant login system:
- Multi-tenant architecture
- JWT authentication flow
- Login with organization context
- Switch organization feature
- Security considerations
- Frontend implementation examples

### 📖 [API Reference](./API_REFERENCE.md)
Complete API endpoint documentation:
- All available endpoints
- Request/response examples
- Authentication requirements
- Error handling
- Rate limiting

### 👤 [User Management](./USER_MANAGEMENT.md)
User profile and management features:
- Enhanced user profile (20+ fields)
- Profile update endpoints
- Avatar upload/delete
- Password management
- Preferences management
- Frontend examples

### 🏢 [Multi-Tenancy Guide](./MULTI_TENANCY.md)
Tenant management and role-based access control:
- Tenant (organization) management
- Role and permission system
- Tenant-scoped RBAC
- User-tenant relationships
- Permission checking

## Quick Reference

### Common API Endpoints

```bash
# Authentication
POST   /api/login
POST   /api/register
POST   /api/logout
POST   /api/switch-organization

# User Profile
GET    /api/me
PUT    /api/profile
POST   /api/profile/avatar
PUT    /api/profile/password

# Tenants
GET    /api/tenants
POST   /api/tenants
GET    /api/tenants/{id}/context

# Roles & Permissions (tenant-scoped)
GET    /api/roles
POST   /api/roles
GET    /api/permissions
POST   /api/permissions
```

### Database Structure

```
users (20+ fields including profile data)
  └── tenant_user (many-to-many)
        └── tenants (UUID primary key)
              ├── roles (tenant-scoped)
              │     ├── role_user
              │     └── permission_role
              └── permissions (tenant-scoped)
                    └── permission_user
```

### Key Concepts

1. **Multi-Tenancy**: Users can belong to multiple organizations with different roles
2. **JWT Tokens**: Contain embedded permissions for current organization
3. **UUID Tenant IDs**: Secure, non-enumerable organization identifiers
4. **Soft Deletes**: Users and tenants are never permanently deleted
5. **Tenant Scoping**: All roles and permissions are scoped to specific tenants

## Documentation Files

| File | Description |
|------|-------------|
| `GETTING_STARTED.md` | Setup guide and UUID implementation |
| `AUTHENTICATION.md` | Multi-tenant auth and JWT documentation |
| `API_REFERENCE.md` | Complete API endpoint reference |
| `USER_MANAGEMENT.md` | User profile features and management |
| `MULTI_TENANCY.md` | Tenant and RBAC system documentation |

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
