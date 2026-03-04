<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OAuth 2.0 Authentication</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 450px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .success {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button[type="submit"]:hover {
            background: #5568d3;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .requirements {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .requirements h3 {
            color: #667eea;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .requirements ul {
            list-style-position: inside;
            color: #666;
            font-size: 13px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Create Account</h1>
        <p class="subtitle">Register for a new account</p>
        
        <?php
        session_start();
        
        // Display messages
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        
        if (isset($_SESSION['success'])) {
            echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard.php');
            exit;
        }
        ?>
        
        <form method="POST" action="register_handler.php">
            <div class="form-group">
                <label for="usuario">Username *</label>
                <input type="text" id="usuario" name="usuario" required 
                       pattern="[A-Za-z0-9_-]{3,20}" 
                       title="3-20 characters: letters, numbers, underscore, hyphen"
                       autocomplete="username">
                <div class="hint">3-20 characters: letters, numbers, underscore, hyphen</div>
            </div>
            
            <div class="form-group">
                <label for="email">Email (optional)</label>
                <input type="email" id="email" name="email" autocomplete="email">
                <div class="hint">For account recovery and notifications</div>
            </div>
            
            <div class="form-group">
                <label for="clave">Password *</label>
                <input type="password" id="clave" name="clave" required 
                       minlength="6" autocomplete="new-password">
                <div class="hint">Minimum 6 characters</div>
            </div>
            
            <div class="form-group">
                <label for="clave_confirm">Confirm Password *</label>
                <input type="password" id="clave_confirm" name="clave_confirm" required 
                       minlength="6" autocomplete="new-password">
            </div>
            
            <button type="submit">Create Account</button>
        </form>
        
        <div class="links">
            <a href="index.php">Already have an account? Sign in</a>
        </div>
        
        <div class="requirements">
            <h3>Password Requirements</h3>
            <ul>
                <li>Minimum 6 characters</li>
                <li>Encrypted with bcrypt algorithm</li>
                <li>Never stored in plain text</li>
            </ul>
        </div>
    </div>
</body>
</html>
