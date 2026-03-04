<?php
/**
 * OAuth Configuration
 * Google and GitHub OAuth 2.0 settings loaded from environment variables
 */

require_once __DIR__ . '/config.php';

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET'));
define('GOOGLE_REDIRECT_URI', env('GOOGLE_REDIRECT_URI'));

// GitHub OAuth Configuration
define('GITHUB_CLIENT_ID', env('GITHUB_CLIENT_ID'));
define('GITHUB_CLIENT_SECRET', env('GITHUB_CLIENT_SECRET'));
define('GITHUB_REDIRECT_URI', env('GITHUB_REDIRECT_URI'));

// Application Configuration
define('APP_URL', env('APP_URL', 'http://localhost:8000'));

// Validate that required configuration exists
function validateOAuthConfig() {
    $required = [
        'GOOGLE_CLIENT_ID',
        'GOOGLE_CLIENT_SECRET',
        'GOOGLE_REDIRECT_URI',
        'GITHUB_CLIENT_ID',
        'GITHUB_CLIENT_SECRET',
        'GITHUB_REDIRECT_URI'
    ];
    
    $missing = [];
    foreach ($required as $key) {
        if (empty(constant($key)) || strpos(constant($key), 'your_') === 0) {
            $missing[] = $key;
        }
    }
    
    if (!empty($missing)) {
        return [
            'valid' => false,
            'missing' => $missing,
            'message' => 'Please configure the following in your .env file: ' . implode(', ', $missing)
        ];
    }
    
    return ['valid' => true];
}

