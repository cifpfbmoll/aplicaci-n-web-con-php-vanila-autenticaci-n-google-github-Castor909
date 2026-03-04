<?php
/**
 * GitHub OAuth 2.0 Callback Handler
 * Handles the redirect from GitHub after user authorization
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../oauth_config.php';
require_once __DIR__ . '/../db_connection.php';

// Check for errors from GitHub
if (isset($_GET['error'])) {
    $_SESSION['error'] = 'GitHub authentication was cancelled or failed: ' . htmlspecialchars($_GET['error_description'] ?? $_GET['error']);
    header('Location: ../index.php');
    exit;
}

// Verify we have an authorization code
if (!isset($_GET['code'])) {
    $_SESSION['error'] = 'No authorization code received from GitHub';
    header('Location: ../index.php');
    exit;
}

try {
    // Exchange authorization code for access token
    $tokenUrl = 'https://github.com/login/oauth/access_token';
    
    $client = new GuzzleHttp\Client();
    $response = $client->post($tokenUrl, [
        'form_params' => [
            'client_id' => GITHUB_CLIENT_ID,
            'client_secret' => GITHUB_CLIENT_SECRET,
            'code' => $_GET['code'],
            'redirect_uri' => GITHUB_REDIRECT_URI
        ],
        'headers' => [
            'Accept' => 'application/json'
        ]
    ]);
    
    $tokenData = json_decode($response->getBody(), true);
    
    if (isset($tokenData['error'])) {
        throw new Exception('Token error: ' . ($tokenData['error_description'] ?? $tokenData['error']));
    }
    
    if (!isset($tokenData['access_token'])) {
        throw new Exception('No access token received from GitHub');
    }
    
    $accessToken = $tokenData['access_token'];
    
    // Get user information from GitHub
    $userResponse = $client->get('https://api.github.com/user', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'PHP-OAuth-App'
        ]
    ]);
    
    $userData = json_decode($userResponse->getBody(), true);
    
    // Get user email (might require separate API call if not public)
    $email = $userData['email'];
    
    if (empty($email)) {
        // Try to get email from emails endpoint
        $emailResponse = $client->get('https://api.github.com/user/emails', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'PHP-OAuth-App'
            ]
        ]);
        
        $emails = json_decode($emailResponse->getBody(), true);
        
        // Get primary email
        foreach ($emails as $emailData) {
            if ($emailData['primary']) {
                $email = $emailData['email'];
                break;
            }
        }
        
        // If no primary, get first verified email
        if (empty($email)) {
            foreach ($emails as $emailData) {
                if ($emailData['verified']) {
                    $email = $emailData['email'];
                    break;
                }
            }
        }
    }
    
    if (empty($email)) {
        throw new Exception('Could not retrieve email from GitHub. Please make sure your email is verified and visible.');
    }
    
    $name = $userData['name'] ?? $userData['login'];
    $avatar = $userData['avatar_url'];
    $githubId = $userData['id'];
    $username = $userData['login'];
    
    // Store or update user in database
    $db = getDbConnection();
    
    // Check if user exists (by email or OAuth ID)
    $stmt = $db->prepare('
        SELECT * FROM usuarios 
        WHERE email = ? OR (oauth_provider = ? AND oauth_id = ?)
    ');
    $stmt->execute([$email, 'github', $githubId]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing user
        $stmt = $db->prepare('
            UPDATE usuarios 
            SET name = ?, avatar = ?, oauth_provider = ?, oauth_id = ?, last_login = CURRENT_TIMESTAMP
            WHERE id = ?
        ');
        $stmt->execute([$name, $avatar, 'github', $githubId, $user['id']]);
        $userId = $user['id'];
    } else {
        // Create new user
        $stmt = $db->prepare('
            INSERT INTO usuarios (usuario, email, name, avatar, oauth_provider, oauth_id, last_login)
            VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');
        $stmt->execute([$username, $email, $name, $avatar, 'github', $githubId]);
        $userId = $db->lastInsertId();
    }
    
    // Set session variables
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['usuario'] = $username;
    $_SESSION['name'] = $name;
    $_SESSION['avatar'] = $avatar;
    $_SESSION['oauth_provider'] = 'github';
    
    // Redirect to protected area
    header('Location: ../dashboard.php');
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Authentication error: ' . htmlspecialchars($e->getMessage());
    header('Location: ../index.php');
    exit;
}
