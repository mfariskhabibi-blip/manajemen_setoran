<?php

namespace Config;

class Auth
{
    /**
     * Authentication configuration
     */
    
    // Password hashing algorithm
    public string $hashAlgorithm = PASSWORD_DEFAULT;
    
    // Session configuration
    public int $sessionTimeout = 3600; // 1 hour in seconds
    
    // Redirect URLs
    public string $loginRedirect = '/dashboard';
    public string $logoutRedirect = '/login';
    
    // User registration
    public bool $allowRegistration = true;
    
    // Remember me functionality
    public bool $enableRememberMe = true;
    public int $rememberMeDuration = 604800; // 1 week in seconds
    
    // Maximum login attempts
    public int $maxLoginAttempts = 5;
    public int $lockoutDuration = 900; // 15 minutes in seconds
    
    // Password requirements
    public int $minPasswordLength = 8;
    public bool $requireUppercase = true;
    public bool $requireLowercase = true;
    public bool $requireNumbers = true;
    public bool $requireSpecialChars = true;
    
    // Email verification
    public bool $requireEmailVerification = false;
    
    // User roles
    public array $roles = [
        'admin' => 'Administrator',
        'user'  => 'User'
    ];
    
    // Default role for new users
    public string $defaultRole = 'user';
}