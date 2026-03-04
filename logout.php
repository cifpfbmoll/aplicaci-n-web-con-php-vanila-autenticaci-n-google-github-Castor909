<?php
/**
 * Logout Handler
 * Destroys the session and redirects to login page
 */

session_start();

// Clear all session variables
$_SESSION = [];

// Delete the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Set success message for next session
session_start();
$_SESSION['success'] = 'You have been successfully logged out';

// Redirect to login page
header('Location: index.php');
exit;
