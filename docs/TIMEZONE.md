# Timezone Support

## Overview
The application handles multiple timezones for global users and organizations. All dates are stored in UTC in the database and converted to the appropriate timezone for display.

## How It Works

### Automatic Timezone Detection
The `SetTimezone` middleware automatically sets the timezone in the following priority order:

1. **X-Timezone Header** - Custom header for API clients
2. **User's Timezone** - From authenticated user's profile
3. **Organization's Timezone** - From current organization settings
4. **Query Parameter** - `?timezone=America/New_York` (for testing)
5. **Fallback** - UTC if none above are available

### Database Storage
**CRITICAL:** All datetime fields in the database are stored in **UTC timezone**.

```sql
-- All timestamps stored in UTC
created_at: 2025-11-14 10:30:00 (UTC)
updated_at: 2025-11-14 15:45:00 (UTC)
```

### Display to Users
Dates are automatically converted to the user's timezone when returned in API responses.

## Configuration

### Users Table
The `timezone` field already exists in the users table:
```php
$table->string('timezone')->default('UTC');
```

### Organizations Table
After running migrations, organizations will have:
```php
$table->string('timezone')->default('UTC');
$table->string('country')->nullable();
$table->string('currency')->default('USD');
```

## Setting Timezone

### During Registration
```json
POST /api/register
{
  "email": "user@example.com",
  "password": "password",
  "timezone": "America/New_York"
}
```

### Update User Profile
```json
PUT /api/profile
{
  "timezone": "Europe/Paris"
}
```

### Create Organization with Timezone
```json
POST /api/organizations
{
  "name": "Acme Corp Europe",
  "slug": "acme-eu",
  "timezone": "Europe/London",
  "country": "GB",
  "currency": "GBP"
}
```

### Via HTTP Headers
```bash
# Using X-Timezone header
curl -H "X-Timezone: Asia/Tokyo" http://localhost:8000/api/me

# Using query parameter (for testing)
curl http://localhost:8000/api/me?timezone=Australia/Sydney
```

## Helper Functions

### to_user_timezone()
Convert any datetime to user's timezone:
```php
use Carbon\Carbon;

// Convert to user's timezone
$userTime = to_user_timezone('2025-11-14 10:30:00');
// Returns: "2025-11-14 05:30:00" (if user is in EST)

// Convert with custom timezone
$nyTime = to_user_timezone('2025-11-14 10:30:00', 'America/New_York');

// Custom format
$formatted = to_user_timezone('2025-11-14 10:30:00', null, 'M d, Y g:i A');
// Returns: "Nov 14, 2025 5:30 AM"
```

### to_utc()
Convert from any timezone to UTC (for database storage):
```php
// User input in their timezone -> convert to UTC for DB
$utcTime = to_utc($request->scheduled_at);

// From specific timezone
$utcTime = to_utc('2025-11-14 17:00:00', 'Europe/Paris');
```

### format_datetime()
Format datetime with presets:
```php
$datetime = '2025-11-14 10:30:00';

format_datetime($datetime, 'short');
// Returns: "2025-11-14 05:30"

format_datetime($datetime, 'medium');
// Returns: "Nov 14, 2025 05:30"

format_datetime($datetime, 'long');
// Returns: "November 14, 2025 5:30 AM"

format_datetime($datetime, 'full');
// Returns: "Thursday, November 14, 2025 5:30:00 AM EST"

format_datetime($datetime, 'date_only');
// Returns: "2025-11-14"

format_datetime($datetime, 'time_only');
// Returns: "05:30:00"

format_datetime($datetime, 'human');
// Returns: "5 hours ago"

// Custom format
format_datetime($datetime, 'Y-m-d h:i A');
// Returns: "2025-11-14 05:30 AM"
```

### current_timezone()
Get the current active timezone:
```php
$tz = current_timezone();
// Returns: "America/New_York"
```

### timezone_offset()
Get timezone offset:
```php
$offset = timezone_offset('America/New_York');
// Returns: "-05:00"

$offset = timezone_offset('Asia/Tokyo');
// Returns: "+09:00"
```

### is_valid_timezone()
Validate timezone string:
```php
if (is_valid_timezone($request->timezone)) {
    // Safe to use
}
```

### convert_timezone()
Convert between any two timezones:
```php
$result = convert_timezone(
    '2025-11-14 10:30:00',
    'America/New_York',    // From
    'Europe/London'        // To
);
// Returns Carbon instance in London time
```

### get_popular_timezones()
Get list of common timezones for dropdowns:
```php
$timezones = get_popular_timezones();
// Returns:
// [
//     'UTC' => 'UTC (Coordinated Universal Time)',
//     'America/New_York' => 'Eastern Time (US & Canada)',
//     'Europe/London' => 'London (GMT)',
//     ...
// ]
```

## Usage in Controllers

### Storing User Input
Always convert to UTC before saving to database:
```php
// User submits: "2025-12-25 18:00" in their timezone
public function store(Request $request)
{
    $event = Event::create([
        'name' => $request->name,
        'starts_at' => to_utc($request->starts_at), // Converts to UTC
        'ends_at' => to_utc($request->ends_at),
    ]);
    
    return response()->json($event);
}
```

### Returning Data
Laravel automatically serializes Carbon dates to ISO 8601 with timezone:
```php
public function show(Event $event)
{
    return response()->json([
        'event' => $event,
        // timestamps automatically converted to user's timezone
    ]);
}
```

### Manual Conversion
```php
public function index()
{
    $events = Event::all()->map(function ($event) {
        return [
            'id' => $event->id,
            'name' => $event->name,
            'starts_at' => to_user_timezone($event->starts_at),
            'starts_at_formatted' => format_datetime($event->starts_at, 'long'),
            'starts_at_human' => format_datetime($event->starts_at, 'human'),
        ];
    });
    
    return response()->json($events);
}
```

## API Response Examples

### User in New York (EST)
```bash
curl -H "X-Timezone: America/New_York" /api/events/1
```
```json
{
  "id": 1,
  "name": "Team Meeting",
  "starts_at": "2025-11-14T10:30:00-05:00",
  "created_at": "2025-11-13T15:20:00-05:00"
}
```

### User in Tokyo (JST)
```bash
curl -H "X-Timezone: Asia/Tokyo" /api/events/1
```
```json
{
  "id": 1,
  "name": "Team Meeting",
  "starts_at": "2025-11-15T00:30:00+09:00",
  "created_at": "2025-11-14T05:20:00+09:00"
}
```

### User in London (GMT)
```bash
curl -H "X-Timezone: Europe/London" /api/events/1
```
```json
{
  "id": 1,
  "name": "Team Meeting",
  "starts_at": "2025-11-14T15:30:00+00:00",
  "created_at": "2025-11-13T20:20:00+00:00"
}
```

## Common Timezone Identifiers

### Americas
- `America/New_York` - Eastern Time (US)
- `America/Chicago` - Central Time (US)
- `America/Denver` - Mountain Time (US)
- `America/Los_Angeles` - Pacific Time (US)
- `America/Toronto` - Toronto
- `America/Vancouver` - Vancouver
- `America/Mexico_City` - Mexico City
- `America/Sao_Paulo` - São Paulo, Brazil

### Europe
- `Europe/London` - London, UK (GMT)
- `Europe/Paris` - Paris, Brussels, Amsterdam
- `Europe/Berlin` - Berlin, Rome, Stockholm
- `Europe/Madrid` - Madrid, Spain
- `Europe/Istanbul` - Istanbul, Turkey
- `Europe/Moscow` - Moscow, Russia

### Asia
- `Asia/Dubai` - Dubai, UAE
- `Asia/Kolkata` - Mumbai, India
- `Asia/Bangkok` - Bangkok, Thailand
- `Asia/Singapore` - Singapore
- `Asia/Hong_Kong` - Hong Kong, China
- `Asia/Tokyo` - Tokyo, Japan
- `Asia/Seoul` - Seoul, South Korea

### Pacific
- `Australia/Sydney` - Sydney, Melbourne
- `Australia/Perth` - Perth
- `Pacific/Auckland` - Auckland, New Zealand

### Africa
- `Africa/Cairo` - Cairo, Egypt
- `Africa/Johannesburg` - Johannesburg, South Africa
- `Africa/Lagos` - Lagos, Nigeria
- `Africa/Nairobi` - Nairobi, Kenya

## Best Practices

### 1. Always Store in UTC
✅ **Good:**
```php
Event::create([
    'starts_at' => to_utc($request->starts_at),
]);
```

❌ **Bad:**
```php
Event::create([
    'starts_at' => $request->starts_at, // Unknown timezone!
]);
```

### 2. Convert on Display
Let the middleware handle conversion automatically, or use helpers:
```php
// Automatic (recommended)
return response()->json($event);

// Manual conversion
return response()->json([
    'starts_at' => format_datetime($event->starts_at, 'long'),
]);
```

### 3. Validate Timezones
```php
$request->validate([
    'timezone' => ['required', 'string', function ($attribute, $value, $fail) {
        if (!is_valid_timezone($value)) {
            $fail('The timezone is invalid.');
        }
    }],
]);
```

### 4. Use ISO 8601 Format
Always return dates in ISO 8601 format with timezone:
```
2025-11-14T10:30:00-05:00  ✅
2025-11-14 10:30:00        ❌
```

### 5. Document Timezone Expectations
In API documentation, clearly state:
- Input: User's local time
- Storage: UTC
- Output: User's timezone (based on profile/header)

## Testing Timezones

### Test with Different Timezones
```bash
# Test as New York user
curl -H "X-Timezone: America/New_York" /api/me

# Test as Tokyo user
curl -H "X-Timezone: Asia/Tokyo" /api/me

# Test as London user
curl -H "X-Timezone: Europe/London" /api/me
```

### Test with Postman
Add header to requests:
- Key: `X-Timezone`
- Value: `Europe/Paris` (or any valid timezone)

### Test Timezone Conversion
```php
// In tinker or tests
php artisan tinker

>>> to_user_timezone('2025-11-14 10:30:00', 'America/New_York')
=> "2025-11-14 05:30:00"

>>> to_utc('2025-11-14 10:30:00', 'America/New_York')
=> Carbon instance in UTC

>>> format_datetime('2025-11-14 10:30:00', 'full', 'Asia/Tokyo')
=> "Thursday, November 14, 2025 7:30:00 PM JST"
```

## Troubleshooting

### Dates Not Converting
**Problem:** Dates showing in UTC instead of user's timezone

**Solution:** Ensure middleware is registered:
```php
// bootstrap/app.php
$middleware->api(prepend: [
    \App\Http\Middleware\SetTimezone::class,
]);
```

### Invalid Timezone Error
**Problem:** "Invalid timezone" error

**Solution:** Use `is_valid_timezone()` to validate before using:
```php
if (!is_valid_timezone($timezone)) {
    return response()->json(['error' => 'Invalid timezone'], 400);
}
```

### Organization Timezone Not Applied
**Problem:** Organization timezone not being used

**Solution:** Ensure `SetOrganizationContext` middleware runs before `SetTimezone`:
```php
// In routes or controller
Route::middleware(['organization', 'timezone'])->group(function () {
    // Routes here
});
```

## Migration Guide

Run the migration to add timezone to organizations:
```bash
php artisan migrate
```

Then update the Organization model if needed:
```php
protected $fillable = [
    'name', 'slug', 'timezone', 'country', 'currency',
    // ... other fields
];
```

## Summary

- ✅ **Store in UTC** - All dates in database
- ✅ **Display in User TZ** - Automatic conversion via middleware
- ✅ **Use Helpers** - `to_utc()`, `to_user_timezone()`, `format_datetime()`
- ✅ **ISO 8601 Format** - Always include timezone in responses
- ✅ **Validate Input** - Check timezone validity
- ✅ **Test Thoroughly** - Test with multiple timezones
