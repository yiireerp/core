# Multi-Language Support (i18n)

## Overview
The application supports multiple languages through Laravel's built-in localization system.

## Supported Languages
- **English (en)** - Default
- **French (fr)**
- **Spanish (es)**

## How It Works

### Automatic Locale Detection
The `SetLocale` middleware automatically detects the user's preferred language in the following order:

1. **User Profile** - If authenticated, uses `language` field from user profile
2. **Accept-Language Header** - Standard HTTP header sent by browsers
3. **X-Locale Header** - Custom header for API clients
4. **Fallback** - Defaults to `en` if none above are available

### Setting User Language

#### During Registration
```json
POST /api/register
{
  "email": "user@example.com",
  "password": "password",
  "language": "fr"
}
```

#### Update User Profile
```json
PUT /api/profile
{
  "language": "es"
}
```

#### Via HTTP Headers
```bash
# Using Accept-Language header (browser default)
curl -H "Accept-Language: fr-FR,fr;q=0.9" http://localhost:8000/api/me

# Using X-Locale header (API clients)
curl -H "X-Locale: es" http://localhost:8000/api/me
```

## Using Translations in Code

### In Controllers
```php
// Simple translation
return response()->json([
    'message' => __('messages.auth.login_success')
]);

// Translation with parameters
return response()->json([
    'message' => __('messages.auth.switched_organization', ['name' => $org->name])
]);

// Checking if translation exists
if (trans()->has('messages.team.created')) {
    $message = __('messages.team.created');
}
```

### Examples by Feature

#### Authentication
```php
// Login success
__('messages.auth.login_success')
// Returns: "Login successful" (en), "Connexion réussie" (fr), "Inicio de sesión exitoso" (es)

// Invalid credentials
__('messages.auth.invalid_credentials')
// Returns: "Invalid credentials" (en), "Identifiants invalides" (fr), "Credenciales inválidas" (es)
```

#### Email Verification
```php
__('messages.verification.sent')
__('messages.verification.verified')
__('messages.verification.invalid_token')
```

#### Password Reset
```php
__('messages.password.reset_sent')
__('messages.password.reset_success')
```

#### Two-Factor Authentication
```php
__('messages.2fa.enabled')
__('messages.2fa.invalid_code')
__('messages.2fa.required')
```

#### Teams
```php
__('messages.team.created')
__('messages.team.member_added')
__('messages.team.access_denied')
```

#### Modules
```php
__('messages.module.enabled')
__('messages.module.access_denied')
__('messages.module.not_enabled')
```

### Validation Messages
```php
// Automatic validation messages
$request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
], [
    'email.required' => __('messages.validation.required', ['attribute' => 'email']),
    'email.email' => __('messages.validation.email'),
    'password.min' => __('messages.validation.min', ['attribute' => 'password', 'min' => 8]),
]);
```

## API Response Examples

### English (en)
```json
{
  "message": "Login successful",
  "access_token": "eyJ0eXAiOiJKV1QiLCJh..."
}
```

### French (fr)
```bash
curl -H "Accept-Language: fr" -X POST /api/login
```
```json
{
  "message": "Connexion réussie",
  "access_token": "eyJ0eXAiOiJKV1QiLCJh..."
}
```

### Spanish (es)
```bash
curl -H "X-Locale: es" -X POST /api/login
```
```json
{
  "message": "Inicio de sesión exitoso",
  "access_token": "eyJ0eXAiOiJKV1QiLCJh..."
}
```

## Adding New Languages

### 1. Create Language Directory
```bash
mkdir lang/de  # For German
mkdir lang/ar  # For Arabic
```

### 2. Copy Translation File
```bash
cp lang/en/messages.php lang/de/messages.php
```

### 3. Translate Messages
Edit `lang/de/messages.php` and translate all strings.

### 4. Update Supported Locales
In `app/Http/Middleware/SetLocale.php`:
```php
$supportedLocales = ['en', 'fr', 'es', 'de', 'ar'];
```

### 5. Update Config (Optional)
In `config/app.php`:
```php
'locale' => 'en',
'fallback_locale' => 'en',
'supported_locales' => ['en', 'fr', 'es', 'de', 'ar'],
```

## Translation File Structure

All translations are in `lang/{locale}/messages.php`:

```
lang/
├── en/
│   └── messages.php
├── fr/
│   └── messages.php
└── es/
    └── messages.php
```

### Message Categories
- `auth` - Authentication messages
- `verification` - Email verification
- `password` - Password reset
- `2fa` - Two-factor authentication
- `user` - User profile management
- `organization` - Organization operations
- `team` - Team management
- `role` - Roles and permissions
- `module` - Module access
- `billing` - Billing and subscriptions
- `validation` - Form validation
- `error` - Error messages
- `success` - Success messages

## Best Practices

### 1. Always Use Translation Keys
❌ **Bad:**
```php
return response()->json(['message' => 'Login successful']);
```

✅ **Good:**
```php
return response()->json(['message' => __('messages.auth.login_success')]);
```

### 2. Use Parameters for Dynamic Content
```php
__('messages.auth.switched_organization', ['name' => $organization->name])
```

### 3. Organize by Feature
Keep related translations together in the same category.

### 4. Provide Context in Keys
Use descriptive keys: `auth.login_success` instead of just `success`

### 5. Test All Languages
Ensure all translations are complete and accurate.

## Testing Translations

### Test with cURL
```bash
# English
curl -H "X-Locale: en" http://localhost:8000/api/me

# French
curl -H "X-Locale: fr" http://localhost:8000/api/me

# Spanish
curl -H "X-Locale: es" http://localhost:8000/api/me
```

### Test with Postman
Add a header to your requests:
- Key: `X-Locale`
- Value: `fr` or `es` or `en`

### Test User Preference
1. Login and get token
2. Update profile with preferred language:
```json
PUT /api/profile
{
  "language": "fr"
}
```
3. All subsequent requests will use French

## Current User Locale

Get the current locale in code:
```php
use Illuminate\Support\Facades\App;

$currentLocale = App::getLocale(); // Returns: 'en', 'fr', or 'es'
```

## Migration Guide

To convert existing hardcoded messages to use translations:

1. **Find hardcoded strings** in controllers
2. **Add translation key** to `lang/en/messages.php`
3. **Replace with `__()` helper**
4. **Translate to other languages**
5. **Test all languages**

### Example Migration
**Before:**
```php
return response()->json(['message' => 'Team created successfully']);
```

**After:**
```php
return response()->json(['message' => __('messages.team.created')]);
```

Then add to all language files:
- `lang/en/messages.php`: `'created' => 'Team created successfully'`
- `lang/fr/messages.php`: `'created' => 'Équipe créée avec succès'`
- `lang/es/messages.php`: `'created' => 'Equipo creado exitosamente'`
