<?php
/**
 * Database Connection Handler
 * Manages SQLite database connection with PDO
 */

function getDbConnection() {
    try {
        $dbPath = __DIR__ . '/database/usuarios.db';
        $dbDir = dirname($dbPath);
        
        // Create database directory if it doesn't exist
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        $db = new PDO("sqlite:$dbPath");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Enable foreign keys
        $db->exec('PRAGMA foreign_keys = ON');
        
        return $db;
    } catch (PDOException $e) {
        die("Database connection error: " . htmlspecialchars($e->getMessage()));
    }
}

// Alias for backward compatibility
function conectar() {
    return getDbConnection();
}
