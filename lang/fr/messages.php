<?php

return [
    // Authentication
    'auth' => [
        'login_success' => 'Connexion réussie',
        'logout_success' => 'Déconnexion réussie',
        'register_success' => 'Inscription réussie',
        'invalid_credentials' => 'Identifiants invalides',
        'unauthorized' => 'Non autorisé',
        'token_expired' => 'Le jeton a expiré',
        'token_invalid' => 'Jeton invalide',
        'email_not_verified' => 'Adresse e-mail non vérifiée',
        'organization_required' => 'Organisation requise',
        'organization_not_found' => 'Organisation introuvable',
        'switched_organization' => 'Basculé vers l\'organisation: :name',
    ],

    // Email Verification
    'verification' => [
        'sent' => 'E-mail de vérification envoyé',
        'already_verified' => 'E-mail déjà vérifié',
        'verified' => 'E-mail vérifié avec succès',
        'invalid_token' => 'Jeton de vérification invalide',
        'expired' => 'Le lien de vérification a expiré',
    ],

    // Password Reset
    'password' => [
        'reset_sent' => 'Lien de réinitialisation du mot de passe envoyé à votre e-mail',
        'reset_success' => 'Mot de passe réinitialisé avec succès',
        'invalid_token' => 'Jeton de réinitialisation invalide',
        'same_password' => 'Le nouveau mot de passe doit être différent de l\'ancien',
    ],

    // Two-Factor Authentication
    '2fa' => [
        'enabled' => '2FA activé avec succès',
        'disabled' => '2FA désactivé avec succès',
        'confirmed' => 'Configuration 2FA confirmée',
        'invalid_code' => 'Code 2FA invalide',
        'recovery_codes_generated' => 'Nouveaux codes de récupération générés',
        'required' => 'Vérification 2FA requise',
    ],

    // User Management
    'user' => [
        'profile_updated' => 'Profil mis à jour avec succès',
        'avatar_uploaded' => 'Avatar téléchargé avec succès',
        'avatar_deleted' => 'Avatar supprimé avec succès',
        'password_changed' => 'Mot de passe modifié avec succès',
        'preferences_updated' => 'Préférences mises à jour avec succès',
        'not_found' => 'Utilisateur introuvable',
    ],

    // Organizations
    'organization' => [
        'created' => 'Organisation créée avec succès',
        'updated' => 'Organisation mise à jour avec succès',
        'deleted' => 'Organisation supprimée avec succès',
        'not_found' => 'Organisation introuvable',
        'access_denied' => 'Accès refusé à cette organisation',
        'user_added' => 'Utilisateur ajouté à l\'organisation',
        'user_removed' => 'Utilisateur retiré de l\'organisation',
        'user_limit_reached' => 'Limite d\'utilisateurs atteinte pour cette organisation',
        'suspended' => 'Cette organisation a été suspendue',
    ],

    // Teams
    'team' => [
        'created' => 'Équipe créée avec succès',
        'updated' => 'Équipe mise à jour avec succès',
        'deleted' => 'Équipe supprimée avec succès',
        'not_found' => 'Équipe introuvable',
        'member_added' => 'Membre ajouté à l\'équipe',
        'member_updated' => 'Rôle du membre mis à jour',
        'member_removed' => 'Membre retiré de l\'équipe',
        'access_denied' => 'Vous n\'avez pas accès à cette équipe',
        'leader_required' => 'Permission de chef d\'équipe requise',
        'modules_assigned' => 'Modules assignés à l\'équipe',
    ],

    // Roles & Permissions
    'role' => [
        'created' => 'Rôle créé avec succès',
        'updated' => 'Rôle mis à jour avec succès',
        'deleted' => 'Rôle supprimé avec succès',
        'not_found' => 'Rôle introuvable',
        'assigned' => 'Rôle assigné à l\'utilisateur',
        'permission_denied' => 'Permission refusée',
    ],

    'permission' => [
        'created' => 'Permission créée avec succès',
        'not_found' => 'Permission introuvable',
    ],

    // Modules
    'module' => [
        'enabled' => 'Module activé avec succès',
        'disabled' => 'Module désactivé avec succès',
        'updated' => 'Paramètres du module mis à jour',
        'not_found' => 'Module introuvable',
        'access_denied' => 'Vous n\'avez pas accès à ce module',
        'expired' => 'L\'accès au module a expiré',
        'not_enabled' => 'Ce module n\'est pas activé pour votre organisation',
    ],

    // Billing
    'billing' => [
        'subscription_updated' => 'Abonnement mis à jour avec succès',
        'limit_updated' => 'Limite d\'utilisateurs mise à jour avec succès',
        'organization_suspended' => 'Organisation suspendue',
        'organization_reactivated' => 'Organisation réactivée',
    ],

    // Validation
    'validation' => [
        'required' => 'Le champ :attribute est requis',
        'email' => 'Veuillez fournir une adresse e-mail valide',
        'min' => 'Le :attribute doit contenir au moins :min caractères',
        'max' => 'Le :attribute ne doit pas dépasser :max caractères',
        'unique' => 'Ce :attribute est déjà utilisé',
        'confirmed' => 'La confirmation du :attribute ne correspond pas',
    ],

    // Errors
    'error' => [
        'server_error' => 'Erreur interne du serveur',
        'not_found' => 'Ressource introuvable',
        'validation_failed' => 'Échec de la validation',
        'rate_limit_exceeded' => 'Trop de requêtes. Veuillez réessayer plus tard',
    ],

    // Success
    'success' => [
        'operation_completed' => 'Opération terminée avec succès',
    ],
];
