#!/usr/bin/env php
<?php
/**
 * Quick Setup Script
 * Automates the initial setup process
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║         OAuth 2.0 Authentication - Quick Setup            ║\n";
echo "║              Google & GitHub Integration                  ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check PHP version
echo "→ Checking PHP version... ";
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    echo "❌\n";
    echo "   ERROR: PHP 7.4 or higher is required. Current version: " . PHP_VERSION . "\n";
    exit(1);
}
echo "✓ " . PHP_VERSION . "\n";

// Check required extensions
echo "→ Checking PHP extensions... ";
$required_extensions = ['pdo_sqlite', 'openssl', 'curl', 'json'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "❌\n";
    echo "   ERROR: Missing extensions: " . implode(', ', $missing_extensions) . "\n";
    exit(1);
}
echo "✓\n";

// Check if Composer is installed
echo "→ Checking Composer... ";
exec('composer --version 2>&1', $output, $return_var);
if ($return_var !== 0) {
    echo "⚠️  Not found\n";
    echo "   Please install Composer: https://getcomposer.org/\n";
    echo "   Then run: composer install\n";
} else {
    echo "✓\n";
    
    // Check if vendor directory exists
    if (!is_dir(__DIR__ . '/vendor')) {
        echo "→ Installing dependencies... ";
        exec('composer install 2>&1', $output, $return_var);
        if ($return_var === 0) {
            echo "✓\n";
        } else {
            echo "❌\n";
            echo "   ERROR: Failed to install dependencies\n";
            exit(1);
        }
    } else {
        echo "→ Dependencies already installed ✓\n";
    }
}

// Check if .env exists
echo "→ Checking environment configuration... ";
if (!file_exists(__DIR__ . '/.env')) {
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
        echo "✓ Created .env file\n";
        echo "\n";
        echo "   ⚠️  IMPORTANT: Edit .env file with your OAuth credentials:\n";
        echo "   • Google Client ID and Secret (from Google Cloud Console)\n";
        echo "   • GitHub Client ID and Secret (from GitHub Developer Settings)\n";
        echo "\n";
    } else {
        echo "❌\n";
        echo "   ERROR: .env.example not found\n";
        exit(1);
    }
} else {
    echo "✓\n";
}

// Create database directory
echo "→ Creating database directory... ";
$db_dir = __DIR__ . '/database';
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0755, true);
    echo "✓\n";
} else {
    echo "✓ Already exists\n";
}

// Create screenshots directory
echo "→ Creating screenshots directory... ";
$screenshots_dir = __DIR__ . '/pantallazos';
if (!is_dir($screenshots_dir)) {
    mkdir($screenshots_dir, 0755, true);
    echo "✓\n";
} else {
    echo "✓ Already exists\n";
}

// Initialize database
echo "→ Initializing database... ";
try {
    $db = new PDO("sqlite:" . $db_dir . "/usuarios.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $db->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario TEXT UNIQUE NOT NULL,
        password TEXT,
        email TEXT,
        name TEXT,
        avatar TEXT,
        oauth_provider TEXT,
        oauth_id TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        last_login DATETIME,
        UNIQUE(oauth_provider, oauth_id)
    )");
    
    echo "✓\n";
} catch (PDOException $e) {
    echo "❌\n";
    echo "   ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║                  Setup Completed! ✓                       ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";

// Read .env to check if credentials are configured
$env_content = file_get_contents(__DIR__ . '/.env');
$needs_config = (
    strpos($env_content, 'your_google_client_id_here') !== false ||
    strpos($env_content, 'your_github_client_id_here') !== false
);

if ($needs_config) {
    echo "⚠️  NEXT STEPS:\n";
    echo "\n";
    echo "1. Configure OAuth credentials in .env file:\n";
    echo "   nano .env  (or use your preferred editor)\n";
    echo "\n";
    echo "2. Get Google OAuth credentials:\n";
    echo "   https://console.cloud.google.com/\n";
    echo "   → Create OAuth 2.0 Client ID\n";
    echo "   → Redirect URI: http://localhost:8000/oauth/google_callback.php\n";
    echo "\n";
    echo "3. Get GitHub OAuth credentials:\n";
    echo "   https://github.com/settings/developers\n";
    echo "   → New OAuth App\n";
    echo "   → Callback URL: http://localhost:8000/oauth/github_callback.php\n";
    echo "\n";
} else {
    echo "✓ OAuth credentials are configured!\n";
    echo "\n";
}

echo "4. Start the development server:\n";
echo "   php -S localhost:8000\n";
echo "\n";
echo "5. Open in browser:\n";
echo "   http://localhost:8000\n";
echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "For complete documentation, see: DOCUMENTATION.md\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";
