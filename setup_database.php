<?php
/**
 * Database Initialization
 * Creates the users table with OAuth support
 */

require_once __DIR__ . '/db_connection.php';

try {
    $db = getDbConnection();
    
    // Create users table with OAuth fields
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
    
    echo "✓ Database and users table created successfully.\n";
    echo "✓ Table supports both traditional and OAuth authentication.\n";
    echo "✓ Ready to use!\n";
    
} catch (PDOException $e) {
    die("Error creating database: " . htmlspecialchars($e->getMessage()));
}
