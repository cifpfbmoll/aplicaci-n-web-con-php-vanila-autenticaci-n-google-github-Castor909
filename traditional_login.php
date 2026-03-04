<?php
/**
 * Traditional Login Handler
 * Handles username/password authentication
 */

session_start();
require_once __DIR__ . '/db_connection.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$clave = $_POST['clave'] ?? '';

// Validation
if (empty($usuario) || empty($clave)) {
    $_SESSION['error'] = 'Please fill in all fields';
    header('Location: index.php');
    exit;
}

try {
    $db = getDbConnection();
    
    // Find user by username or email
    $stmt = $db->prepare('
        SELECT * FROM usuarios 
        WHERE (usuario = ? OR email = ?) AND password IS NOT NULL
    ');
    $stmt->execute([$usuario, $usuario]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['error'] = 'Invalid username or password';
        header('Location: index.php');
        exit;
    }
    
    // Verify password
    if (!password_verify($clave, $user['password'])) {
        $_SESSION['error'] = 'Invalid username or password';
        header('Location: index.php');
        exit;
    }
    
    // Update last login
    $stmt = $db->prepare('UPDATE usuarios SET last_login = CURRENT_TIMESTAMP WHERE id = ?');
    $stmt->execute([$user['id']]);
    
    // Set session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['name'] = $user['name'] ?? $user['usuario'];
    $_SESSION['avatar'] = $user['avatar'];
    $_SESSION['oauth_provider'] = null; // Traditional login
    
    header('Location: dashboard.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error occurred. Please try again.';
    header('Location: index.php');
    exit;
}
