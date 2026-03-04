<?php
/**
 * Google OAuth 2.0 Callback Handler
 * Handles the redirect from Google after user authorization
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../oauth_config.php';
require_once __DIR__ . '/../db_connection.php';

// Check for errors from Google
if (isset($_GET['error'])) {
    $_SESSION['error'] = 'Google authentication was cancelled or failed: ' . htmlspecialchars($_GET['error']);
    header('Location: ../index.php');
    exit;
}

// Verify we have an authorization code
if (!isset($_GET['code'])) {
    $_SESSION['error'] = 'No authorization code received from Google';
    header('Location: ../index.php');
    exit;
}

try {
    // Configure Google Client
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope('email');
    $client->addScope('profile');
    
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error'])) {
        throw new Exception('Token error: ' . $token['error_description'] ?? $token['error']);
    }
    
    $client->setAccessToken($token);
    
    // Get user information from Google
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();
    
    $email = $userInfo->email;
    $name = $userInfo->name;
    $avatar = $userInfo->picture;
    $googleId = $userInfo->id;
    
    if (empty($email)) {
        throw new Exception('Could not retrieve email from Google');
    }
    
    // Store or update user in database
    $db = getDbConnection();
    
    // Check if user exists (by email or OAuth ID)
    $stmt = $db->prepare('
        SELECT * FROM usuarios 
        WHERE email = ? OR (oauth_provider = ? AND oauth_id = ?)
    ');
    $stmt->execute([$email, 'google', $googleId]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing user
        $stmt = $db->prepare('
            UPDATE usuarios 
            SET name = ?, avatar = ?, oauth_provider = ?, oauth_id = ?, last_login = CURRENT_TIMESTAMP
            WHERE id = ?
        ');
        $stmt->execute([$name, $avatar, 'google', $googleId, $user['id']]);
        $userId = $user['id'];
    } else {
        // Create new user
        $stmt = $db->prepare('
            INSERT INTO usuarios (usuario, email, name, avatar, oauth_provider, oauth_id, last_login)
            VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');
        $stmt->execute([$email, $email, $name, $avatar, 'google', $googleId]);
        $userId = $db->lastInsertId();
    }
    
    // Set session variables
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['usuario'] = $email;
    $_SESSION['name'] = $name;
    $_SESSION['avatar'] = $avatar;
    $_SESSION['oauth_provider'] = 'google';
    
    // Redirect to protected area
    header('Location: ../dashboard.php');
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Authentication error: ' . htmlspecialchars($e->getMessage());
    header('Location: ../index.php');
    exit;
}
