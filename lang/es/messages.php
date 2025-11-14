<?php

return [
    // Authentication
    'auth' => [
        'login_success' => 'Inicio de sesión exitoso',
        'logout_success' => 'Sesión cerrada exitosamente',
        'register_success' => 'Registro exitoso',
        'invalid_credentials' => 'Credenciales inválidas',
        'unauthorized' => 'No autorizado',
        'token_expired' => 'El token ha expirado',
        'token_invalid' => 'Token inválido',
        'email_not_verified' => 'Correo electrónico no verificado',
        'organization_required' => 'Se requiere organización',
        'organization_not_found' => 'Organización no encontrada',
        'switched_organization' => 'Cambiado a la organización: :name',
    ],

    // Email Verification
    'verification' => [
        'sent' => 'Correo de verificación enviado',
        'already_verified' => 'Correo ya verificado',
        'verified' => 'Correo verificado exitosamente',
        'invalid_token' => 'Token de verificación inválido',
        'expired' => 'El enlace de verificación ha expirado',
    ],

    // Password Reset
    'password' => [
        'reset_sent' => 'Enlace de restablecimiento de contraseña enviado a su correo',
        'reset_success' => 'Contraseña restablecida exitosamente',
        'invalid_token' => 'Token de restablecimiento inválido',
        'same_password' => 'La nueva contraseña debe ser diferente de la actual',
    ],

    // Two-Factor Authentication
    '2fa' => [
        'enabled' => '2FA habilitado exitosamente',
        'disabled' => '2FA deshabilitado exitosamente',
        'confirmed' => 'Configuración 2FA confirmada',
        'invalid_code' => 'Código 2FA inválido',
        'recovery_codes_generated' => 'Nuevos códigos de recuperación generados',
        'required' => 'Verificación 2FA requerida',
    ],

    // User Management
    'user' => [
        'profile_updated' => 'Perfil actualizado exitosamente',
        'avatar_uploaded' => 'Avatar subido exitosamente',
        'avatar_deleted' => 'Avatar eliminado exitosamente',
        'password_changed' => 'Contraseña cambiada exitosamente',
        'preferences_updated' => 'Preferencias actualizadas exitosamente',
        'not_found' => 'Usuario no encontrado',
    ],

    // Organizations
    'organization' => [
        'created' => 'Organización creada exitosamente',
        'updated' => 'Organización actualizada exitosamente',
        'deleted' => 'Organización eliminada exitosamente',
        'not_found' => 'Organización no encontrada',
        'access_denied' => 'Acceso denegado a esta organización',
        'user_added' => 'Usuario agregado a la organización',
        'user_removed' => 'Usuario eliminado de la organización',
        'user_limit_reached' => 'Límite de usuarios alcanzado para esta organización',
        'suspended' => 'Esta organización ha sido suspendida',
    ],

    // Teams
    'team' => [
        'created' => 'Equipo creado exitosamente',
        'updated' => 'Equipo actualizado exitosamente',
        'deleted' => 'Equipo eliminado exitosamente',
        'not_found' => 'Equipo no encontrado',
        'member_added' => 'Miembro agregado al equipo',
        'member_updated' => 'Rol del miembro actualizado',
        'member_removed' => 'Miembro eliminado del equipo',
        'access_denied' => 'No tiene acceso a este equipo',
        'leader_required' => 'Se requiere permiso de líder de equipo',
        'modules_assigned' => 'Módulos asignados al equipo',
    ],

    // Roles & Permissions
    'role' => [
        'created' => 'Rol creado exitosamente',
        'updated' => 'Rol actualizado exitosamente',
        'deleted' => 'Rol eliminado exitosamente',
        'not_found' => 'Rol no encontrado',
        'assigned' => 'Rol asignado al usuario',
        'permission_denied' => 'Permiso denegado',
    ],

    'permission' => [
        'created' => 'Permiso creado exitosamente',
        'not_found' => 'Permiso no encontrado',
    ],

    // Modules
    'module' => [
        'enabled' => 'Módulo habilitado exitosamente',
        'disabled' => 'Módulo deshabilitado exitosamente',
        'updated' => 'Configuración del módulo actualizada',
        'not_found' => 'Módulo no encontrado',
        'access_denied' => 'No tiene acceso a este módulo',
        'expired' => 'El acceso al módulo ha expirado',
        'not_enabled' => 'Este módulo no está habilitado para su organización',
    ],

    // Billing
    'billing' => [
        'subscription_updated' => 'Suscripción actualizada exitosamente',
        'limit_updated' => 'Límite de usuarios actualizado exitosamente',
        'organization_suspended' => 'Organización suspendida',
        'organization_reactivated' => 'Organización reactivada',
    ],

    // Validation
    'validation' => [
        'required' => 'El campo :attribute es requerido',
        'email' => 'Por favor proporcione una dirección de correo válida',
        'min' => 'El :attribute debe tener al menos :min caracteres',
        'max' => 'El :attribute no debe exceder :max caracteres',
        'unique' => 'Este :attribute ya está en uso',
        'confirmed' => 'La confirmación del :attribute no coincide',
    ],

    // Errors
    'error' => [
        'server_error' => 'Error interno del servidor',
        'not_found' => 'Recurso no encontrado',
        'validation_failed' => 'Falló la validación',
        'rate_limit_exceeded' => 'Demasiadas solicitudes. Por favor intente más tarde',
    ],

    // Success
    'success' => [
        'operation_completed' => 'Operación completada exitosamente',
    ],
];
