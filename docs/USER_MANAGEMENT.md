# Enhanced User Profile Documentation

## Overview

The user profile has been enhanced with comprehensive personal and professional information fields, supporting a complete user management system.

## User Profile Fields

### Basic Information
- `id` - Unique identifier (auto-increment)
- `first_name` - User's first name (required)
- `last_name` - User's last name (required)
- `email` - Email address (required, unique)
- `password` - Hashed password (required)
- `email_verified_at` - Email verification timestamp

### Contact Information
- `phone` - Phone number (optional)
- `address_line1` - Primary address (optional)
- `address_line2` - Secondary address (optional)
- `city` - City (optional)
- `state` - State/Province (optional)
- `postal_code` - ZIP/Postal code (optional)
- `country` - Country (optional)

### Personal Information
- `date_of_birth` - Date of birth (optional)
- `gender` - Gender (`male`, `female`, `other`, `prefer_not_to_say`)
- `avatar` - Profile picture path (optional)
- `bio` - Personal biography (max 1000 chars, optional)

### Preferences
- `timezone` - User's timezone (default: `UTC`)
- `language` - Preferred language (default: `en`)
- `preferences` - JSON object for custom user preferences

### System Fields
- `is_active` - Account status (default: `true`)
- `last_login_at` - Last login timestamp
- `last_login_ip` - Last login IP address
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp (null if active)

## Computed Attributes

### `full_name`
Returns the user's full name.
```php
$user->full_name; // "John Doe"
```

### `initials`
Returns the user's initials.
```php
$user->initials; // "JD"
```

## API Endpoints

### 1. Get Current User Profile

**Endpoint:** `GET /api/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "full_name": "John Doe",
  "initials": "JD",
  "email": "john@example.com",
  "phone": "+1-555-0101",
  "avatar": "http://localhost/storage/avatars/user1.jpg",
  "date_of_birth": "1990-05-15",
  "gender": "male",
  "address_line1": "123 Main St",
  "address_line2": "Apt 4B",
  "city": "New York",
  "state": "NY",
  "postal_code": "10001",
  "country": "USA",
  "timezone": "America/New_York",
  "language": "en",
  "bio": "Software Engineer with 10+ years of experience",
  "preferences": {
    "theme": "dark",
    "notifications": true
  },
  "is_active": true,
  "last_login_at": "2025-11-12T10:30:00.000000Z",
  "created_at": "2025-01-01T00:00:00.000000Z"
}
```

### 2. Update User Profile

**Endpoint:** `PUT /api/profile`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+1-555-0101",
  "date_of_birth": "1990-05-15",
  "gender": "male",
  "address_line1": "123 Main St",
  "address_line2": "Apt 4B",
  "city": "New York",
  "state": "NY",
  "postal_code": "10001",
  "country": "USA",
  "timezone": "America/New_York",
  "language": "en",
  "bio": "Software Engineer with 10+ years of experience"
}
```

**Response:**
```json
{
  "message": "Profile updated successfully",
  "user": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "full_name": "John Doe",
    "email": "john@example.com"
  }
}
```

**Validation Rules:**
- `first_name` - string, max 255 characters
- `last_name` - string, max 255 characters
- `email` - valid email, unique
- `phone` - string, max 20 characters
- `date_of_birth` - valid date, must be in the past
- `gender` - one of: `male`, `female`, `other`, `prefer_not_to_say`
- `bio` - string, max 1000 characters
- All address fields - string, max 255 characters

### 3. Upload Avatar

**Endpoint:** `POST /api/profile/avatar`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```
avatar: [image file]
```

**Validation:**
- File must be an image (jpeg, png, jpg, gif)
- Maximum file size: 2MB

**Response:**
```json
{
  "message": "Avatar uploaded successfully",
  "avatar": "http://localhost/storage/avatars/xyz123.jpg"
}
```

**Notes:**
- Old avatar is automatically deleted when uploading a new one
- Avatars are stored in `storage/app/public/avatars/`

### 4. Delete Avatar

**Endpoint:** `DELETE /api/profile/avatar`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Avatar deleted successfully"
}
```

### 5. Change Password

**Endpoint:** `PUT /api/profile/password`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "new_password": "newpassword456",
  "new_password_confirmation": "newpassword456"
}
```

**Validation:**
- `current_password` - required, must match current password
- `new_password` - required, min 8 characters, must be confirmed

**Response:**
```json
{
  "message": "Password changed successfully"
}
```

**Error Response (incorrect current password):**
```json
{
  "error": "Current password is incorrect"
}
```

### 6. Update Preferences

**Endpoint:** `PUT /api/profile/preferences`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "preferences": {
    "theme": "dark",
    "notifications": true,
    "email_digest": "weekly",
    "language": "en"
  }
}
```

**Response:**
```json
{
  "message": "Preferences updated successfully",
  "preferences": {
    "theme": "dark",
    "notifications": true,
    "email_digest": "weekly",
    "language": "en"
  }
}
```

**Notes:**
- Preferences are merged with existing preferences
- Can store any JSON-serializable data

### 7. Register New User

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1-555-0101",
  "date_of_birth": "1990-05-15",
  "gender": "male",
  "timezone": "America/New_York",
  "language": "en"
}
```

**Response:**
```json
{
  "access_token": "token...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "+1-555-0101"
  }
}
```

## Usage Examples

### cURL Examples

**Get profile:**
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Update profile:**
```bash
curl -X PUT http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "city": "New York",
    "bio": "Software Engineer"
  }'
```

**Upload avatar:**
```bash
curl -X POST http://localhost:8000/api/profile/avatar \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "avatar=@/path/to/image.jpg"
```

**Change password:**
```bash
curl -X PUT http://localhost:8000/api/profile/password \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "oldpassword",
    "new_password": "newpassword",
    "new_password_confirmation": "newpassword"
  }'
```

### JavaScript/Fetch Examples

```javascript
// Get current user profile
const getProfile = async () => {
  const response = await fetch('http://localhost:8000/api/me', {
    headers: {
      'Authorization': `Bearer ${token}`,
    }
  });
  const profile = await response.json();
  return profile;
};

// Update profile
const updateProfile = async (data) => {
  const response = await fetch('http://localhost:8000/api/profile', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data)
  });
  return await response.json();
};

// Upload avatar
const uploadAvatar = async (file) => {
  const formData = new FormData();
  formData.append('avatar', file);
  
  const response = await fetch('http://localhost:8000/api/profile/avatar', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
    },
    body: formData
  });
  return await response.json();
};

// Change password
const changePassword = async (currentPassword, newPassword) => {
  const response = await fetch('http://localhost:8000/api/profile/password', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: newPassword
    })
  });
  return await response.json();
};
```

## Frontend Implementation

### Profile Form Component (React)

```jsx
const ProfileForm = () => {
  const [profile, setProfile] = useState({});
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchProfile();
  }, []);

  const fetchProfile = async () => {
    const data = await getProfile();
    setProfile(data);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      await updateProfile(profile);
      alert('Profile updated successfully');
    } catch (error) {
      alert('Error updating profile');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        value={profile.first_name || ''}
        onChange={(e) => setProfile({...profile, first_name: e.target.value})}
        placeholder="First Name"
      />
      <input
        value={profile.last_name || ''}
        onChange={(e) => setProfile({...profile, last_name: e.target.value})}
        placeholder="Last Name"
      />
      <input
        value={profile.email || ''}
        onChange={(e) => setProfile({...profile, email: e.target.value})}
        placeholder="Email"
        type="email"
      />
      <input
        value={profile.phone || ''}
        onChange={(e) => setProfile({...profile, phone: e.target.value})}
        placeholder="Phone"
      />
      <textarea
        value={profile.bio || ''}
        onChange={(e) => setProfile({...profile, bio: e.target.value})}
        placeholder="Bio"
        maxLength={1000}
      />
      <button type="submit" disabled={loading}>
        {loading ? 'Saving...' : 'Save Profile'}
      </button>
    </form>
  );
};
```

### Avatar Upload Component

```jsx
const AvatarUpload = () => {
  const [avatar, setAvatar] = useState(null);
  const [preview, setPreview] = useState(null);

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    setAvatar(file);
    setPreview(URL.createObjectURL(file));
  };

  const handleUpload = async () => {
    if (!avatar) return;
    
    const result = await uploadAvatar(avatar);
    alert(result.message);
  };

  return (
    <div>
      {preview && <img src={preview} alt="Preview" width={100} />}
      <input type="file" accept="image/*" onChange={handleFileChange} />
      <button onClick={handleUpload}>Upload Avatar</button>
    </div>
  );
};
```

## Database Schema

```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,
  avatar VARCHAR(255) NULL,
  date_of_birth DATE NULL,
  gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL,
  address_line1 VARCHAR(255) NULL,
  address_line2 VARCHAR(255) NULL,
  city VARCHAR(100) NULL,
  state VARCHAR(100) NULL,
  postal_code VARCHAR(20) NULL,
  country VARCHAR(100) NULL,
  timezone VARCHAR(50) DEFAULT 'UTC',
  language VARCHAR(10) DEFAULT 'en',
  bio TEXT NULL,
  preferences JSON NULL,
  is_active BOOLEAN DEFAULT TRUE,
  last_login_at TIMESTAMP NULL,
  last_login_ip VARCHAR(45) NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
);
```

## Seeded Test Data

Three users are created with sample data:

**1. John Doe (john@example.com)**
- City: New York, NY, USA
- Bio: Software Engineer with 10+ years of experience
- Timezone: America/New_York

**2. Jane Smith (jane@example.com)**
- City: San Francisco, CA, USA
- Bio: Product Manager passionate about user experience
- Timezone: America/Los_Angeles

**3. Bob Wilson (bob@example.com)**
- City: Austin, TX, USA
- Bio: Tech entrepreneur and startup founder
- Timezone: America/Chicago

All passwords: `password`

## Security Considerations

✅ **Password Hashing** - All passwords hashed with bcrypt  
✅ **Email Verification** - Support for email verification timestamp  
✅ **Soft Deletes** - Users are soft-deleted, not permanently removed  
✅ **Last Login Tracking** - Track login attempts and IP addresses  
✅ **Avatar Storage** - Files stored securely in storage/app/public  
✅ **File Validation** - Strict validation on avatar uploads (type, size)  
✅ **Current Password Required** - Must provide current password to change it  

## Best Practices

1. **Always validate input** - Use Laravel's built-in validation
2. **Sanitize file uploads** - Validate file types and sizes
3. **Use soft deletes** - Never permanently delete user accounts
4. **Track user activity** - Log login times and IP addresses
5. **Respect user privacy** - Mark sensitive fields as optional
6. **Support internationalization** - Store timezone and language preferences
7. **Allow profile customization** - Use JSON preferences field for flexibility
