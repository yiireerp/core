<?php

return [
    // Authentication
    'auth' => [
        'login_success' => 'Login successful',
        'logout_success' => 'Logged out successfully',
        'register_success' => 'Registration successful',
        'invalid_credentials' => 'Invalid credentials',
        'unauthorized' => 'Unauthorized',
        'token_expired' => 'Token has expired',
        'token_invalid' => 'Invalid token',
        'email_not_verified' => 'Email address not verified',
        'organization_required' => 'Organization is required',
        'organization_not_found' => 'Organization not found',
        'switched_organization' => 'Switched to organization: :name',
    ],

    // Email Verification
    'verification' => [
        'sent' => 'Verification email sent',
        'already_verified' => 'Email already verified',
        'verified' => 'Email verified successfully',
        'invalid_token' => 'Invalid verification token',
        'expired' => 'Verification link has expired',
    ],

    // Password Reset
    'password' => [
        'reset_sent' => 'Password reset link sent to your email',
        'reset_success' => 'Password reset successfully',
        'invalid_token' => 'Invalid password reset token',
        'same_password' => 'New password must be different from current password',
    ],

    // Two-Factor Authentication
    '2fa' => [
        'enabled' => '2FA enabled successfully',
        'disabled' => '2FA disabled successfully',
        'confirmed' => '2FA setup confirmed',
        'invalid_code' => 'Invalid 2FA code',
        'recovery_codes_generated' => 'New recovery codes generated',
        'required' => '2FA verification required',
    ],

    // User Management
    'user' => [
        'profile_updated' => 'Profile updated successfully',
        'avatar_uploaded' => 'Avatar uploaded successfully',
        'avatar_deleted' => 'Avatar deleted successfully',
        'password_changed' => 'Password changed successfully',
        'preferences_updated' => 'Preferences updated successfully',
        'not_found' => 'User not found',
    ],

    // Organizations
    'organization' => [
        'created' => 'Organization created successfully',
        'updated' => 'Organization updated successfully',
        'deleted' => 'Organization deleted successfully',
        'not_found' => 'Organization not found',
        'access_denied' => 'Access denied to this organization',
        'user_added' => 'User added to organization',
        'user_removed' => 'User removed from organization',
        'user_limit_reached' => 'User limit reached for this organization',
        'suspended' => 'This organization has been suspended',
    ],

    // Teams
    'team' => [
        'created' => 'Team created successfully',
        'updated' => 'Team updated successfully',
        'deleted' => 'Team deleted successfully',
        'not_found' => 'Team not found',
        'member_added' => 'Member added to team',
        'member_updated' => 'Member role updated',
        'member_removed' => 'Member removed from team',
        'access_denied' => 'You do not have access to this team',
        'leader_required' => 'Team leader permission required',
        'modules_assigned' => 'Modules assigned to team',
    ],

    // Roles & Permissions
    'role' => [
        'created' => 'Role created successfully',
        'updated' => 'Role updated successfully',
        'deleted' => 'Role deleted successfully',
        'not_found' => 'Role not found',
        'assigned' => 'Role assigned to user',
        'permission_denied' => 'Permission denied',
    ],

    'permission' => [
        'created' => 'Permission created successfully',
        'not_found' => 'Permission not found',
    ],

    // Modules
    'module' => [
        'enabled' => 'Module enabled successfully',
        'disabled' => 'Module disabled successfully',
        'updated' => 'Module settings updated',
        'not_found' => 'Module not found',
        'access_denied' => 'You do not have access to this module',
        'expired' => 'Module access has expired',
        'not_enabled' => 'This module is not enabled for your organization',
    ],

    // Billing
    'billing' => [
        'subscription_updated' => 'Subscription updated successfully',
        'limit_updated' => 'User limit updated successfully',
        'organization_suspended' => 'Organization suspended',
        'organization_reactivated' => 'Organization reactivated',
    ],

    // Validation
    'validation' => [
        'required' => 'The :attribute field is required',
        'email' => 'Please provide a valid email address',
        'min' => 'The :attribute must be at least :min characters',
        'max' => 'The :attribute must not exceed :max characters',
        'unique' => 'This :attribute is already taken',
        'confirmed' => 'The :attribute confirmation does not match',
    ],

    // Errors
    'error' => [
        'server_error' => 'Internal server error',
        'not_found' => 'Resource not found',
        'validation_failed' => 'Validation failed',
        'rate_limit_exceeded' => 'Too many requests. Please try again later',
    ],

    // Success
    'success' => [
        'operation_completed' => 'Operation completed successfully',
    ],
];
