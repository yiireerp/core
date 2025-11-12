# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Token refresh mechanism
- Email verification flow
- Two-factor authentication (2FA)
- Admin dashboard API endpoints
- Rate limiting middleware
- Audit logging system
- Comprehensive test suite
- API versioning support

## [1.0.0] - 2025-11-12

### Added
- Initial release of Yiire Auth microservice
- Multi-tenant authorization architecture with UUID tenant IDs
- JWT authentication with tymon/jwt-auth
- Complete RBAC (Role-Based Access Control) system
- Tenant-scoped roles and permissions
- User can belong to multiple tenants with different roles
- Enhanced user profile management (20+ fields)
  - Personal information (name, email, phone, date of birth, gender)
  - Address fields (line1, line2, city, state, postal code, country)
  - User preferences (JSON storage)
  - Avatar upload functionality
  - Timezone and language settings
  - User bio and profile customization
- Authentication endpoints
  - User registration with comprehensive profile fields
  - Login with organization selection
  - Organization switching with JWT token refresh
  - Logout with token invalidation
- User profile endpoints
  - Get complete user profile
  - Update profile information
  - Avatar upload/delete (max 2MB, jpeg/png/jpg/gif)
  - Password change with validation
  - Preferences management (JSON merge)
- Tenant/Organization management
  - List user's organizations
  - Create/update tenants
  - Add/remove users from tenants
  - Get user context in tenant (roles & permissions)
- Role management (tenant-scoped)
  - Create/list/view roles
  - Assign/remove roles to users
  - Assign permissions to roles
- Permission management (tenant-scoped)
  - Create/list/view permissions
  - Assign permissions directly to users
- Comprehensive seeder with demo data
  - 2 demo tenants (Acme Corporation, TechStart Inc)
  - 3 demo users with complete profiles
  - 11 permissions per tenant
  - 3 roles per tenant (admin, moderator, user)
- Middleware
  - RoleMiddleware for role-based route protection
  - PermissionMiddleware for permission-based route protection
  - SetTenantContext for automatic tenant context detection
- JWT features
  - Custom claims for tenant_id, roles, and permissions
  - 60-minute token TTL (configurable)
  - Token blacklist on logout
- Database migrations
  - Enhanced users table with 20+ fields and soft deletes
  - Tenants table with UUID primary keys
  - Roles and permissions tables (tenant-scoped)
  - Pivot tables for many-to-many relationships
- Documentation
  - Comprehensive README with quick start guide
  - Setup and installation guide
  - Multi-tenant authentication guide
  - JWT authentication documentation
  - API reference with all endpoints
  - User management guide
  - RBAC and multi-tenancy guide
  - Postman collection with 30+ API requests
- Docker support
  - Multi-stage Dockerfile for optimized builds
  - docker-compose.yml with MySQL, PostgreSQL, and Redis
  - Nginx and PHP-FPM configuration
  - Supervisor for process management
  - Health check endpoints
  - Support for both development and production
- GitHub Actions CI/CD
  - Automated testing workflow
  - Docker image build and push to GHCR
  - Code style checking with Laravel Pint
  - Multi-PHP version testing (8.2, 8.3)
- Open source preparation
  - MIT License
  - Contributing guidelines
  - Proper composer.json for Packagist publishing
  - Comprehensive .gitignore
  - .dockerignore for optimized builds

### Security
- UUID tenant IDs to prevent enumeration attacks
- Password hashing with bcrypt
- JWT token-based authentication
- Token blacklist for logout
- Middleware protection for sensitive routes
- CORS configuration
- Security headers in Nginx
- Environment-based configuration

### Dependencies
- Laravel 12.x
- PHP 8.2+
- Laravel Sanctum 4.2
- tymon/jwt-auth 2.2
- MySQL 8.0 / PostgreSQL 16 / SQLite
- Redis 7 (for caching and queues)

---

## Release Notes

### Version 1.0.0 - Initial Release

This is the first stable release of Yiire Auth, a production-ready multi-tenant authorization microservice. The system provides:

**Core Features:**
- Multi-tenant architecture where users can belong to multiple organizations
- JWT-based authentication with embedded permissions
- Complete RBAC system with tenant-scoped roles and permissions
- Comprehensive user profile management
- RESTful API with 30+ endpoints

**Deployment Options:**
- Docker containers with full orchestration
- Traditional server deployment
- Cloud-ready with horizontal scaling support

**Developer Experience:**
- Well-documented API
- Postman collection for quick testing
- Seeded demo data for development
- Comprehensive documentation
- Open source with contribution guidelines

For installation instructions, see [README.md](README.md).
For API documentation, see [docs/API_REFERENCE.md](docs/API_REFERENCE.md).

[Unreleased]: https://github.com/yiire-erp/auth/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/yiire-erp/auth/releases/tag/v1.0.0
