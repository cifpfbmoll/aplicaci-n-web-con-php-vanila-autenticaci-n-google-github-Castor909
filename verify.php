#!/usr/bin/env php
<?php
/**
 * Project Verification Script
 * Checks that all components are properly configured
 */

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  OAuth 2.0 Project - Verification Script\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$errors = [];
$warnings = [];
$success = [];

// Check PHP version
echo "→ Checking PHP version... ";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "✓ " . PHP_VERSION . "\n";
    $success[] = "PHP version: " . PHP_VERSION;
} else {
    echo "✗\n";
    $errors[] = "PHP 7.4+ required. Current: " . PHP_VERSION;
}

// Check extensions
echo "→ Checking required extensions... ";
$required = ['pdo_sqlite', 'openssl', 'curl', 'json'];
$missing = [];
foreach ($required as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}
if (empty($missing)) {
    echo "✓\n";
    $success[] = "All required extensions loaded";
} else {
    echo "✗\n";
    $errors[] = "Missing extensions: " . implode(', ', $missing);
}

// Check vendor directory
echo "→ Checking Composer dependencies... ";
if (is_dir(__DIR__ . '/vendor')) {
    echo "✓\n";
    $success[] = "Composer dependencies installed";
} else {
    echo "✗\n";
    $errors[] = "Vendor directory not found. Run: composer install";
}

// Check .env file
echo "→ Checking environment configuration... ";
if (file_exists(__DIR__ . '/.env')) {
    echo "✓\n";
    $success[] = ".env file exists";
    
    // Check if configured
    $env_content = file_get_contents(__DIR__ . '/.env');
    if (strpos($env_content, 'your_google_client_id_here') !== false ||
        strpos($env_content, 'your_github_client_id_here') !== false) {
        $warnings[] = "OAuth credentials not configured in .env";
    } else {
        $success[] = "OAuth credentials appear to be configured";
    }
} else {
    echo "✗\n";
    $errors[] = ".env file not found. Run: cp .env.example .env";
}

// Check database
echo "→ Checking database... ";
if (file_exists(__DIR__ . '/database/usuarios.db')) {
    echo "✓\n";
    $success[] = "Database file exists";
    
    // Check schema
    try {
        $db = new PDO("sqlite:" . __DIR__ . "/database/usuarios.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='usuarios'");
        if ($result->fetch()) {
            echo "→ Checking database schema... ✓\n";
            $success[] = "Database schema is correct";
            
            // Check columns
            $columns = $db->query("PRAGMA table_info(usuarios)")->fetchAll(PDO::FETCH_COLUMN, 1);
            $required_columns = ['id', 'usuario', 'password', 'email', 'name', 'avatar', 'oauth_provider', 'oauth_id', 'created_at', 'last_login'];
            $missing_columns = array_diff($required_columns, $columns);
            
            if (empty($missing_columns)) {
                $success[] = "All database columns present";
            } else {
                $warnings[] = "Missing database columns: " . implode(', ', $missing_columns);
            }
        } else {
            echo "→ Checking database schema... ✗\n";
            $errors[] = "usuarios table not found. Run: php setup_database.php";
        }
    } catch (PDOException $e) {
        echo "→ Checking database schema... ✗\n";
        $errors[] = "Database error: " . $e->getMessage();
    }
} else {
    echo "✗\n";
    $errors[] = "Database not found. Run: php setup_database.php";
}

// Check required files
echo "→ Checking required files... ";
$required_files = [
    'index.php',
    'dashboard.php',
    'register.php',
    'register_handler.php',
    'traditional_login.php',
    'logout.php',
    'config.php',
    'oauth_config.php',
    'db_connection.php',
    'oauth/google_callback.php',
    'oauth/github_callback.php'
];

$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $missing_files[] = $file;
    }
}

if (empty($missing_files)) {
    echo "✓\n";
    $success[] = "All required files present";
} else {
    echo "✗\n";
    $errors[] = "Missing files: " . implode(', ', $missing_files);
}

// Check documentation
echo "→ Checking documentation... ";
$doc_files = [
    'README.md',
    'DOCUMENTATION.md',
    'GUIA_CONFIGURACION.md'
];

$missing_docs = [];
foreach ($doc_files as $doc) {
    if (!file_exists(__DIR__ . '/' . $doc)) {
        $missing_docs[] = $doc;
    }
}

if (empty($missing_docs)) {
    echo "✓\n";
    $success[] = "All documentation files present";
} else {
    echo "⚠\n";
    $warnings[] = "Missing documentation: " . implode(', ', $missing_docs);
}

// Check screenshots directory
echo "→ Checking screenshots directory... ";
if (is_dir(__DIR__ . '/pantallazos')) {
    echo "✓\n";
    $files = glob(__DIR__ . '/pantallazos/*.{png,jpg,jpeg}', GLOB_BRACE);
    if (count($files) > 0) {
        $success[] = "Screenshots directory exists with " . count($files) . " images";
    } else {
        $warnings[] = "Screenshots directory is empty";
    }
} else {
    echo "⚠\n";
    $warnings[] = "Screenshots directory not found";
}

// Results
echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  VERIFICATION RESULTS\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if (!empty($success)) {
    echo "✓ SUCCESS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "  • " . $msg . "\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠ WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "  • " . $msg . "\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "✗ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "  • " . $msg . "\n";
    }
    echo "\n";
}

// Final verdict
echo "═══════════════════════════════════════════════════════════\n";
if (empty($errors)) {
    echo "  STATUS: ✓ READY\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    if (!empty($warnings)) {
        echo "The project is ready to run with minor warnings.\n";
        echo "Address warnings before final submission.\n\n";
    } else {
        echo "The project is fully configured and ready!\n\n";
    }
    
    echo "To start the server:\n";
    echo "  php -S localhost:8000\n\n";
    
    echo "Then open: http://localhost:8000\n";
    
} else {
    echo "  STATUS: ✗ SETUP REQUIRED\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    echo "Please fix the errors above before running the application.\n";
    echo "Run: php setup.php for automated setup.\n";
}

echo "\n";
exit(empty($errors) ? 0 : 1);
