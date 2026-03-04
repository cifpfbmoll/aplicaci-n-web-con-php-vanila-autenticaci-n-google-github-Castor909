<?php
/**
 * User Dashboard - Protected Page
 * Displays user information after successful authentication
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/db_connection.php';

// Get user details from database
try {
    $db = getDbConnection();
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // User not found, logout
        session_destroy();
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die('Database error');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OAuth 2.0 Authentication</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h2 {
            color: #667eea;
            font-size: 20px;
        }
        
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .navbar .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            overflow: hidden;
        }
        
        .navbar .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .navbar .name {
            color: #333;
            font-weight: 500;
        }
        
        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 22px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .profile-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        
        .profile-item label {
            display: block;
            color: #666;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .profile-item .value {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-primary {
            background: #cce5ff;
            color: #004085;
        }
        
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .welcome-section h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card .icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .stat-card .value {
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }
        
        .info-message {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .info-message p {
            color: #0c5460;
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <h2>🔐 OAuth 2.0 Dashboard</h2>
            <div class="user-info">
                <div class="avatar">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['name'] ?? $user['usuario'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <span class="name"><?php echo htmlspecialchars($user['name'] ?? $user['usuario']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </nav>
        
        <div class="welcome-section">
            <h1>👋 Welcome, <?php echo htmlspecialchars($user['name'] ?? $user['usuario']); ?>!</h1>
            <p>You have successfully authenticated using 
                <?php 
                if ($user['oauth_provider']) {
                    echo ucfirst($user['oauth_provider']) . ' OAuth 2.0';
                } else {
                    echo 'traditional username/password';
                }
                ?>
            </p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">👤</div>
                <div class="label">Account Type</div>
                <div class="value">
                    <?php echo $user['oauth_provider'] ? ucfirst($user['oauth_provider']) : 'Local'; ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📅</div>
                <div class="label">Member Since</div>
                <div class="value">
                    <?php echo date('M Y', strtotime($user['created_at'])); ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="icon">🕐</div>
                <div class="label">Last Login</div>
                <div class="value">
                    <?php echo date('M d, Y', strtotime($user['last_login'])); ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3>📋 Profile Information</h3>
            <div class="profile-grid">
                <div class="profile-item">
                    <label>User ID</label>
                    <div class="value">#<?php echo htmlspecialchars($user['id']); ?></div>
                </div>
                
                <div class="profile-item">
                    <label>Username</label>
                    <div class="value"><?php echo htmlspecialchars($user['usuario']); ?></div>
                </div>
                
                <?php if ($user['email']): ?>
                <div class="profile-item">
                    <label>Email</label>
                    <div class="value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($user['name']): ?>
                <div class="profile-item">
                    <label>Full Name</label>
                    <div class="value"><?php echo htmlspecialchars($user['name']); ?></div>
                </div>
                <?php endif; ?>
                
                <div class="profile-item">
                    <label>Authentication Method</label>
                    <div class="value">
                        <?php if ($user['oauth_provider']): ?>
                            <span class="badge badge-primary">
                                OAuth 2.0 - <?php echo ucfirst($user['oauth_provider']); ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-secondary">
                                Traditional Login
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($user['oauth_id']): ?>
                <div class="profile-item">
                    <label>OAuth ID</label>
                    <div class="value"><?php echo htmlspecialchars($user['oauth_id']); ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="info-message">
                <p><strong>ℹ️ Security Information:</strong></p>
                <p>✓ Your session is encrypted and secure</p>
                <p>✓ Password stored using bcrypt hashing (if applicable)</p>
                <p>✓ OAuth tokens are not stored in our database</p>
                <p>✓ Your data is protected following industry best practices</p>
            </div>
        </div>
    </div>
</body>
</html>
