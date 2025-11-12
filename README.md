# Yiire Auth - Multi-Tenant Authorization Microservice# Multi-Tenant Authorization Microservice# Yiire - Laravel Sanctum Auth with Roles & Permissions



[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)

[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)A production-ready Laravel-based multi-tenant authorization microservice with JWT authentication, role-based access control (RBAC), and comprehensive user management.A complete Laravel application with Laravel Sanctum authentication and a comprehensive Role-Based Access Control (RBAC) system.

[![Docker](https://img.shields.io/badge/Docker-Supported-blue.svg)](https://docker.com)



A production-ready Laravel-based multi-tenant authorization microservice with JWT authentication, role-based access control (RBAC), and comprehensive user management.

## Features## Features

## ✨ Features



- 🏢 **Multi-Tenancy** - Users can belong to multiple organizations with different roles

- 🔐 **JWT Authentication** - Secure token-based authentication with embedded permissions✅ **Multi-Tenancy** - Users can belong to multiple organizations with different roles  ✅ **Laravel Sanctum Authentication**

- 👥 **RBAC System** - Flexible role and permission management scoped per tenant

- 🔑 **UUID Tenant IDs** - Secure, non-enumerable tenant identifiers✅ **JWT Authentication** - Secure token-based authentication with embedded permissions  - User registration with email/password

- 👤 **Enhanced User Profiles** - 20+ profile fields with avatar upload support

- 🗑️ **Soft Deletes** - Safe data retention✅ **RBAC System** - Flexible role and permission management scoped per tenant  - Token-based authentication

- 🚀 **API-First Design** - RESTful API with complete documentation

- 🐳 **Docker Support** - Ready-to-deploy containers✅ **UUID Tenant IDs** - Secure, non-enumerable tenant identifiers  - Secure login/logout

- 📊 **Comprehensive Seeding** - Demo data for quick development

✅ **Enhanced User Profiles** - Comprehensive user data with preferences  - Token revocation

## 📋 Table of Contents

✅ **Soft Deletes** - Safe data retention  

- [Quick Start](#-quick-start)

- [Installation](#-installation)✅ **API-First Design** - RESTful API with complete documentation  ✅ **Role-Based Access Control (RBAC)**

- [Docker Deployment](#-docker-deployment)

- [API Documentation](#-api-documentation)- 3 default roles: Admin, Moderator, User

- [Architecture](#-architecture)

- [Demo Credentials](#-demo-credentials)## Quick Start- 14 default permissions for user, post, and role management

- [Contributing](#-contributing)

- [License](#-license)- Assign permissions to roles



## 🚀 Quick Start### Installation- Direct user permissions (overrides)



### Local Development- Permission inheritance from roles



```bash```bash

# Clone the repository

git clone https://github.com/yiire-erp/auth.git# Install dependencies✅ **RESTful API**

cd auth

composer install- Complete authentication endpoints

# Install dependencies

composer install- Role management CRUD



# Setup environment# Copy environment file- Permission management CRUD

cp .env.example .env

php artisan key:generatecp .env.example .env- User management endpoints

php artisan jwt:secret



# Run migrations and seed demo data

php artisan migrate:fresh --seed --seeder=MultiTenantSeeder# Generate application key✅ **Security**



# Start development serverphp artisan key:generate- Password hashing with bcrypt

php artisan serve

```- Middleware-based route protection



Visit `http://localhost:8000` and use the [demo credentials](#-demo-credentials) to login.# Generate JWT secret- Role and permission checking



### Docker Deploymentphp artisan jwt:secret- Secure database relationships



```bash

# Clone the repository

git clone https://github.com/yiire-erp/auth.git# Run migrations and seed demo data## Quick Start

cd auth

php artisan migrate:fresh --seed --seeder=MultiTenantSeeder

# Copy environment file

cp .env.example .env### 1. Install Dependencies



# Build and start containers# Start development server```bash

docker-compose up -d

php artisan servecomposer install

# Run migrations inside container

docker-compose exec app php artisan migrate:fresh --seed --seeder=MultiTenantSeeder``````



# Generate keys inside container

docker-compose exec app php artisan key:generate

docker-compose exec app php artisan jwt:secret### Demo Credentials### 2. Environment Setup

```

```bash

Application will be available at `http://localhost:8000`

```cp .env.example .env

## 📦 Installation

Email: john@example.comphp artisan key:generate

### Requirements

Password: password```

- PHP 8.2 or higher

- ComposerOrganizations: Acme Corporation (Admin), TechStart Inc (User)

- MySQL 8.0+ / PostgreSQL 16+ / SQLite

- Redis (optional, for caching and queues)Configure your database in `.env`:

- Node.js and npm (for frontend assets)

Email: jane@example.com  ```

### Step-by-Step Setup

Password: passwordDB_CONNECTION=mysql

1. **Install PHP Dependencies**

   ```bashOrganizations: Acme Corporation (Moderator), TechStart Inc (Moderator)DB_HOST=127.0.0.1

   composer install

   ```DB_PORT=3306



2. **Environment Configuration**Email: bob@example.comDB_DATABASE=yiire

   ```bash

   cp .env.example .envPassword: passwordDB_USERNAME=root

   ```

   Organizations: TechStart Inc (Admin)DB_PASSWORD=

   Update `.env` with your database credentials:

   ```env``````

   DB_CONNECTION=mysql

   DB_HOST=127.0.0.1

   DB_PORT=3306

   DB_DATABASE=yiire_auth## Documentation### 3. Run Migrations & Seeders

   DB_USERNAME=your_username

   DB_PASSWORD=your_password```bash

   ```

📖 **[Complete Documentation](./docs/)** - All guides and referencesphp artisan migrate

3. **Generate Application Keys**

   ```bashphp artisan db:seed --class=RolePermissionSeeder

   php artisan key:generate

   php artisan jwt:secret### Quick Links```

   ```



4. **Run Database Migrations**

   ```bash- **[Getting Started Guide](./docs/GETTING_STARTED.md)** - Installation and setup### 4. Start Development Server

   php artisan migrate

   ```- **[Authentication Guide](./docs/AUTHENTICATION.md)** - JWT auth and multi-tenant login```bash



5. **Seed Demo Data (Optional)**- **[API Reference](./docs/API_REFERENCE.md)** - Complete endpoint documentationphp artisan serve

   ```bash

   php artisan db:seed --class=MultiTenantSeeder- **[User Management](./docs/USER_MANAGEMENT.md)** - Profile management features```

   ```

- **[Multi-Tenancy Guide](./docs/MULTI_TENANCY.md)** - Tenant and RBAC management

6. **Install Frontend Dependencies (Optional)**

   ```bash### 5. Create Your First Admin

   npm install

   npm run build## Example API Usage```bash

   ```

php artisan tinker

7. **Start Development Server**

   ```bash### Login>>> $user = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => Hash::make('password')]);

   php artisan serve

   ``````bash>>> $user->assignRole('admin');



## 🐳 Docker Deploymentcurl -X POST http://localhost:8000/api/login \>>> exit



### Using Docker Compose  -H "Content-Type: application/json" \```



The project includes a complete Docker setup with MySQL, Redis, and Nginx.  -d '{



**Start all services:**    "email": "john@example.com",## API Endpoints

```bash

docker-compose up -d    "password": "password",

```

    "tenant_id": "acme"### Public Endpoints

**View logs:**

```bash  }'- `POST /api/register` - Register new user

docker-compose logs -f app

``````- `POST /api/login` - Login user



**Stop services:**

```bash

docker-compose down### Response### Protected Endpoints

```

```json- `GET /api/user` - Get authenticated user

**Rebuild containers:**

```bash{- `GET /api/me` - Get user with roles & permissions

docker-compose build --no-cache

docker-compose up -d  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",- `POST /api/logout` - Logout user

```

  "token_type": "Bearer",

### Services Included

  "expires_in": 3600,### Admin Endpoints

| Service | Port | Description |

|---------|------|-------------|  "current_organization": {- `GET /api/users` - List all users

| **app** | 8000 | Laravel application with Nginx + PHP-FPM |

| **db** | 3306 | MySQL 8.0 database |    "id": "019a77f4-54f3-72c3-beec-c8b1a59dbc23",- `GET /api/roles` - Manage roles

| **redis** | 6379 | Redis for caching and queues |

| **postgres** | 5432 | PostgreSQL (optional, use profile) |    "name": "Acme Corporation",- `GET /api/permissions` - Manage permissions



### Optional Services    "roles": [{"id": 1, "name": "Administrator", "slug": "admin"}],- And more... (see full docs)



**Start with PostgreSQL instead of MySQL:**    "permissions": [...]

```bash

docker-compose --profile postgres up -d  },## Documentation

```

  "organizations": [...]

**Start with queue worker:**

```bash}📖 **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Detailed setup instructions

docker-compose --profile queue up -d

``````



**Start with scheduler:**📖 **[ROLES_AND_PERMISSIONS.md](ROLES_AND_PERMISSIONS.md)** - Complete RBAC documentation

```bash

docker-compose --profile scheduler up -d## Architecture

```

📖 **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick reference guide

### Production Deployment

```

For production, use the optimized Dockerfile:

Users ──┬── Tenant A (Admin) ──┬── Roles ──┬── Permissions📖 **[AUTH_API_DOCUMENTATION.md](AUTH_API_DOCUMENTATION.md)** - API documentation

```bash

# Build production image        │                      │           └── manage-users

docker build -t yiire/auth:latest .

        │                      └── Users## Usage Examples

# Run container

docker run -d \        │

  --name yiire-auth \

  -p 80:80 \        └── Tenant B (User) ───┬── Roles ──┬── Permissions### Check User Roles

  -e APP_ENV=production \

  -e APP_DEBUG=false \                               │           └── view-posts```php

  -e DB_HOST=your-db-host \

  -e DB_DATABASE=your-db-name \                               └── Usersif ($user->hasRole('admin')) {

  -e DB_USERNAME=your-db-user \

  -e DB_PASSWORD=your-db-password \```    // User is admin

  yiire/auth:latest

```}



## 📚 API Documentation## Technology Stack```



### Authentication Endpoints



| Method | Endpoint | Description |- **Laravel 12.x** - PHP Framework### Check Permissions

|--------|----------|-------------|

| POST | `/api/register` | Register new user |- **JWT Auth** - Token-based authentication```php

| POST | `/api/login` | Login with email/password |

| POST | `/api/logout` | Logout and invalidate token |- **SQLite/MySQL/PostgreSQL** - Databaseif ($user->hasPermission('edit-posts')) {

| GET | `/api/user` | Get current authenticated user |

| POST | `/api/switch-organization` | Switch to different organization |- **PHP 8.2+** - Programming Language    // User can edit posts



### User Profile Endpoints}



| Method | Endpoint | Description |## Key Features```

|--------|----------|-------------|

| GET | `/api/me` | Get complete user profile |

| PUT | `/api/profile` | Update profile information |

| POST | `/api/profile/avatar` | Upload avatar image |### Multi-Tenant Support### Protect Routes

| DELETE | `/api/profile/avatar` | Delete avatar |

| PUT | `/api/profile/password` | Change password |- Users belong to multiple organizations```php

| PUT | `/api/profile/preferences` | Update user preferences |

- Different roles per organizationRoute::middleware(['auth:sanctum', 'role:admin'])->group(function () {

### Tenant Management

- Isolated data per tenant    // Admin only routes

| Method | Endpoint | Description |

|--------|----------|-------------|});

| GET | `/api/tenants` | List user's organizations |

| POST | `/api/tenants` | Create new tenant |### Security```

| GET | `/api/tenants/{id}` | Get tenant details |

| PUT | `/api/tenants/{id}` | Update tenant |- JWT tokens with embedded permissions

| POST | `/api/tenants/{id}/add-user` | Add user to tenant |

| POST | `/api/tenants/{id}/remove-user` | Remove user from tenant |- UUID-based tenant IDs (non-enumerable)### API Testing



### Roles & Permissions (Admin Only)- Password hashing with bcrypt```bash



All role and permission endpoints require admin privileges and tenant context via `X-Tenant-ID` header.- Soft deletes for data retention# Register



**Complete API documentation:** [docs/API_REFERENCE.md](docs/API_REFERENCE.md)- Last login trackingcurl -X POST http://localhost:8000/api/register \



**Postman Collection:** [postman_collection.json](postman_collection.json)  -H "Content-Type: application/json" \



## 🏗️ Architecture### User Management  -d '{"name": "John", "email": "john@test.com", "password": "password123", "password_confirmation": "password123"}'



### Multi-Tenant Model- 20+ profile fields



```- Avatar upload support# Login

Users ──┬── Tenant A (Admin) ──┬── Roles ──┬── Permissions

        │                      │           └── manage-users- Timezone and language preferencescurl -X POST http://localhost:8000/api/login \

        │                      └── Users

        │- Custom JSON preferences  -H "Content-Type: application/json" \

        └── Tenant B (User) ───┬── Roles ──┬── Permissions

                               │           └── view-posts- Password change functionality  -d '{"email": "john@test.com", "password": "password123"}'

                               └── Users

``````



### Database Schema## License



- **users** - User accounts with 20+ profile fields## Default Roles

- **tenants** - Organizations with UUID primary keys

- **roles** - Tenant-scoped rolesMIT License

- **permissions** - Tenant-scoped permissions

- **tenant_user** - User-tenant relationships- **Admin** - Full system access (all permissions)

- **role_user** - User-role assignments (per tenant)

- **permission_role** - Role-permission assignments---- **Moderator** - Content moderation access

- **permission_user** - Direct user permissions

- **User** - Basic access (assigned to new registrations)

### JWT Token Structure

**Version:** 1.0.0  

```json

{**Last Updated:** November 12, 2025  ## Default Permissions

  "sub": 1,

  "tenant_id": "019a77f4-54f3-72c3-beec-c8b1a59dbc23",**Laravel:** 12.x | **PHP:** 8.2+

  "roles": ["admin"],

  "permissions": ["manage-users", "manage-roles", "..."],**User Management:** view-users, create-users, edit-users, delete-users

  "exp": 1699876543

}**Post Management:** view-posts, create-posts, edit-posts, delete-posts

```

**Role Management:** view-roles, create-roles, edit-roles, delete-roles, assign-roles, assign-permissions

## 🔐 Demo Credentials

## Technology Stack

After seeding the database, you can use these credentials:

- Laravel 12.x

| Email | Password | Tenants | Role |- Laravel Sanctum 4.x

|-------|----------|---------|------|- PHP 8.2+

| john@example.com | password | Acme (Admin), TechStart (User) | Admin/User |- MySQL/SQLite

| jane@example.com | password | Acme (Moderator), TechStart (Moderator) | Moderator |- RESTful API

| bob@example.com | password | TechStart (Admin) | Admin |

## License

### Example Login Request

MIT License

```bash

curl -X POST http://localhost:8000/api/login \---

  -H "Content-Type: application/json" \

  -d '{<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

    "email": "john@example.com",

    "password": "password",
    "tenant_id": "acme"
  }'
```

## 📖 Documentation

- **[Getting Started Guide](docs/GETTING_STARTED.md)** - Installation and setup
- **[Authentication Guide](docs/AUTHENTICATION.md)** - JWT auth and multi-tenant login
- **[API Reference](docs/API_REFERENCE.md)** - Complete endpoint documentation
- **[User Management](docs/USER_MANAGEMENT.md)** - Profile management features
- **[Multi-Tenancy Guide](docs/MULTI_TENANCY.md)** - Tenant and RBAC management
- **[Postman Collection](postman_collection.json)** - Ready-to-use API requests

## 🛠️ Technology Stack

- **Framework:** Laravel 12.x
- **Authentication:** Laravel Sanctum 4.2 + JWT Auth 2.2
- **Database:** MySQL 8.0 / PostgreSQL 16 / SQLite
- **Cache/Queue:** Redis 7
- **PHP:** 8.2+
- **Server:** Nginx + PHP-FPM
- **Container:** Docker + Docker Compose

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
```

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- JWT authentication by [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- Inspired by modern multi-tenant SaaS architectures

## 📧 Support

- **Issues:** [GitHub Issues](https://github.com/yiire-erp/auth/issues)
- **Discussions:** [GitHub Discussions](https://github.com/yiire-erp/auth/discussions)
- **Email:** team@yiire.com

---

<p align="center">Made with ❤️ by the Yiire Team</p>
<p align="center">
  <a href="https://github.com/yiire-erp/auth">⭐ Star us on GitHub</a>
</p>
