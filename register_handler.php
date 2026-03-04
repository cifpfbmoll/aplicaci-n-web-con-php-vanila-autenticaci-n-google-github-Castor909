<?php
/**
 * Registration Handler
 * Processes new user registration
 */

session_start();
require_once __DIR__ . '/db_connection.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$email = trim($_POST['email'] ?? '');
$clave = $_POST['clave'] ?? '';
$clave_confirm = $_POST['clave_confirm'] ?? '';

// Validation
$errors = [];

if (empty($usuario)) {
    $errors[] = 'Username is required';
}

if (strlen($usuario) < 3 || strlen($usuario) > 20) {
    $errors[] = 'Username must be 3-20 characters';
}

if (!preg_match('/^[A-Za-z0-9_-]+$/', $usuario)) {
    $errors[] = 'Username can only contain letters, numbers, underscore and hyphen';
}

if (empty($clave)) {
    $errors[] = 'Password is required';
}

if (strlen($clave) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

if ($clave !== $clave_confirm) {
    $errors[] = 'Passwords do not match';
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: register.php');
    exit;
}

try {
    $db = getDbConnection();
    
    // Check if username already exists
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Username already exists';
        header('Location: register.php');
        exit;
    }
    
    // Check if email already exists (if provided)
    if (!empty($email)) {
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Email already registered';
            header('Location: register.php');
            exit;
        }
    }
    
    // Hash password
    $hash = password_hash($clave, PASSWORD_BCRYPT);
    
    // Insert user
    $stmt = $db->prepare('
        INSERT INTO usuarios (usuario, email, password, name, last_login)
        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
    ');
    $emailValue = !empty($email) ? $email : null;
    $stmt->execute([$usuario, $emailValue, $hash, $usuario]);
    
    $_SESSION['success'] = 'Account created successfully! Please sign in.';
    header('Location: index.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Registration failed. Please try again.';
    header('Location: register.php');
    exit;
}
