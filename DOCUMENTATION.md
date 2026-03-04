# OAuth 2.0 Authentication with PHP - Google & GitHub

A complete PHP web application implementing OAuth 2.0 authentication with Google and GitHub, along with traditional username/password authentication.

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![OAuth 2.0](https://img.shields.io/badge/OAuth-2.0-orange)

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Security Features](#-security-features)
- [Technical Implementation](#-technical-implementation)
- [Screenshots](#-screenshots)
- [Troubleshooting](#-troubleshooting)

## ✨ Features

- ✅ **Complete OAuth 2.0 Implementation**
  - Google OAuth 2.0 authentication
  - GitHub OAuth 2.0 authentication
  - Secure token handling and validation

- ✅ **Traditional Authentication**
  - Username/password registration and login
  - Password hashing with bcrypt
  - Secure session management

- ✅ **User Management**
  - SQLite database with PDO
  - User profile information storage
  - Avatar support from OAuth providers
  - Last login tracking

- ✅ **Security Features**
  - Environment variable configuration
  - SQL injection protection with prepared statements
  - CSRF protection with session regeneration
  - Password strength validation
  - Secure session handling

- ✅ **Modern UI**
  - Responsive design
  - Clean and intuitive interface
  - Error and success message display
  - Mobile-friendly

## 📦 Requirements

- **PHP 7.4 or higher**
- **SQLite3** (included with PHP)
- **Composer** (for dependency management)
- **Extensions:**
  - `pdo_sqlite`
  - `openssl`
  - `curl`
  - `json`

## 🚀 Installation

### 1. Clone the Repository

\`\`\`bash
git clone <repository-url>
cd <project-directory>
\`\`\`

### 2. Install Dependencies

\`\`\`bash
composer install
\`\`\`

This will install:
- `google/apiclient` - Google API Client Library
- `guzzlehttp/guzzle` - HTTP client for GitHub API

### 3. Create Environment Configuration

\`\`\`bash
cp .env.example .env
\`\`\`

### 4. Initialize Database

\`\`\`bash
php setup_database.php
\`\`\`

This creates the SQLite database and users table.

## ⚙️ Configuration

### Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the **Google+ API**
4. Go to **Credentials** → **Create Credentials** → **OAuth 2.0 Client ID**
5. Configure OAuth consent screen
6. Set application type to **Web application**
7. Add authorized redirect URI: \`http://localhost:8000/oauth/google_callback.php\`
8. Copy **Client ID** and **Client Secret**

### GitHub OAuth Setup

1. Go to [GitHub Developer Settings](https://github.com/settings/developers)
2. Click **New OAuth App**
3. Fill in the application details:
   - **Application name**: Your app name
   - **Homepage URL**: \`http://localhost:8000\`
   - **Authorization callback URL**: \`http://localhost:8000/oauth/github_callback.php\`
4. Click **Register application**
5. Copy **Client ID** and **Client Secret**

### Environment Variables

Edit the \`.env\` file with your credentials:

\`\`\`env
# Google OAuth Settings
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/oauth/google_callback.php

# GitHub OAuth Settings
GITHUB_CLIENT_ID=your_github_client_id_here
GITHUB_CLIENT_SECRET=your_github_client_secret_here
GITHUB_REDIRECT_URI=http://localhost:8000/oauth/github_callback.php

# Application Settings
APP_URL=http://localhost:8000
SESSION_SECURE=false
SESSION_HTTPONLY=true
\`\`\`

## 🎮 Usage

### Starting the Development Server

\`\`\`bash
php -S localhost:8000
\`\`\`

Then open your browser and navigate to: \`http://localhost:8000\`

### User Registration

1. Click **"Don't have an account? Register here"**
2. Fill in username, email (optional), and password
3. Click **"Create Account"**
4. You'll be redirected to the login page

### Login Options

#### Option 1: OAuth Login
- Click **"Continue with Google"** or **"Continue with GitHub"**
- Authorize the application
- You'll be redirected to the dashboard

#### Option 2: Traditional Login
- Enter your username/email and password
- Click **"Sign In"**
- You'll be redirected to the dashboard

## 📁 Project Structure

\`\`\`
project/
├── config.php                 # Environment loader
├── oauth_config.php          # OAuth configuration
├── db_connection.php         # Database connection
├── setup_database.php        # Database initialization
├── index.php                 # Login page
├── register.php              # Registration page
├── register_handler.php      # Registration processing
├── traditional_login.php     # Traditional login handler
├── dashboard.php             # Protected dashboard
├── logout.php                # Logout handler
├── oauth/
│   ├── google_callback.php   # Google OAuth callback
│   └── github_callback.php   # GitHub OAuth callback
├── database/
│   └── usuarios.db           # SQLite database (auto-created)
├── vendor/                   # Composer dependencies
├── .env                      # Environment variables (not in git)
├── .env.example              # Environment template
├── .gitignore               # Git ignore rules
├── composer.json            # Composer configuration
└── DOCUMENTATION.md         # This file
\`\`\`

## 🔒 Security Features

### Password Security
- **Bcrypt Hashing**: All passwords are hashed using PHP's \`password_hash()\` with bcrypt
- **Salt Generation**: Automatic unique salt generation per password
- **Cost Factor**: Default cost factor of 10 (2^10 iterations)

### Session Security
- **Session Regeneration**: ID regenerated on login to prevent session fixation
- **HTTPOnly Cookies**: Session cookies not accessible via JavaScript
- **Secure Flag**: Can be enabled for HTTPS connections

### SQL Injection Protection
- **Prepared Statements**: All database queries use PDO prepared statements
- **Parameter Binding**: No direct string concatenation in SQL queries

### Input Validation
- **Server-side Validation**: All inputs validated on the server
- **XSS Protection**: Output escaped with \`htmlspecialchars()\`
- **Email Validation**: Email format validation
- **Username Validation**: Regex pattern validation

### OAuth Security
- **State Parameter**: Prevents CSRF attacks in OAuth flow
- **Token Validation**: Access tokens validated before use
- **Secure Token Exchange**: Tokens exchanged over secure channels
- **No Token Storage**: OAuth tokens not stored in database

## 🔧 Technical Implementation

### OAuth 2.0 Flow

#### Google OAuth Flow

1. **Authorization Request**
   \`\`\`php
   $client = new Google_Client();
   $client->setClientId(GOOGLE_CLIENT_ID);
   $client->setClientSecret(GOOGLE_CLIENT_SECRET);
   $client->setRedirectUri(GOOGLE_REDIRECT_URI);
   $client->addScope('email');
   $client->addScope('profile');
   $authUrl = $client->createAuthUrl();
   \`\`\`

2. **User Authorization**
   - User clicks Google login button
   - Redirected to Google's authorization page
   - User grants permissions

3. **Callback & Token Exchange**
   \`\`\`php
   $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
   $client->setAccessToken($token);
   \`\`\`

4. **User Information Retrieval**
   \`\`\`php
   $oauth2 = new Google_Service_Oauth2($client);
   $userInfo = $oauth2->userinfo->get();
   \`\`\`

#### GitHub OAuth Flow

1. **Authorization Request**
   \`\`\`php
   $githubAuthUrl = 'https://github.com/login/oauth/authorize?' . http_build_query([
       'client_id' => GITHUB_CLIENT_ID,
       'redirect_uri' => GITHUB_REDIRECT_URI,
       'scope' => 'user:email read:user'
   ]);
   \`\`\`

2. **Token Exchange**
   \`\`\`php
   $response = $client->post('https://github.com/login/oauth/access_token', [
       'form_params' => [
           'client_id' => GITHUB_CLIENT_ID,
           'client_secret' => GITHUB_CLIENT_SECRET,
           'code' => $_GET['code']
       ]
   ]);
   \`\`\`

3. **API Requests**
   \`\`\`php
   $userResponse = $client->get('https://api.github.com/user', [
       'headers' => [
           'Authorization' => 'Bearer ' . $accessToken,
           'Accept' => 'application/vnd.github.v3+json'
       ]
   ]);
   \`\`\`

### Database Schema

\`\`\`sql
CREATE TABLE usuarios (
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
);
\`\`\`

#### Field Descriptions

- **id**: Unique user identifier (auto-increment)
- **usuario**: Username (required, unique)
- **password**: Hashed password (nullable for OAuth users)
- **email**: User's email address
- **name**: Display name
- **avatar**: Profile picture URL
- **oauth_provider**: OAuth provider (google/github)
- **oauth_id**: Provider's unique user ID
- **created_at**: Account creation timestamp
- **last_login**: Last login timestamp

### Session Management

\`\`\`php
// On successful login
session_regenerate_id(true);
$_SESSION['user_id'] = $userId;
$_SESSION['usuario'] = $username;
$_SESSION['name'] = $name;
$_SESSION['avatar'] = $avatar;
$_SESSION['oauth_provider'] = $provider;
\`\`\`

### Error Handling

\`\`\`php
try {
    // OAuth or database operation
} catch (Exception $e) {
    $_SESSION['error'] = 'User-friendly error message';
    header('Location: index.php');
    exit;
}
\`\`\`

## 📸 Screenshots

*Place screenshots in the \`screenshots/\` directory and reference them here:*

### Login Page
![Login Page](screenshots/login.png)

### OAuth Selection
![OAuth Buttons](screenshots/oauth-buttons.png)

### Dashboard
![Dashboard](screenshots/dashboard.png)

### Registration
![Registration](screenshots/register.png)

## 🐛 Troubleshooting

### Common Issues

#### 1. "Configuration Error: .env file not found"
**Solution**: Copy \`.env.example\` to \`.env\` and fill in your OAuth credentials

\`\`\`bash
cp .env.example .env
\`\`\`

#### 2. "Database connection error"
**Solution**: Ensure the \`database/\` directory exists and is writable

\`\`\`bash
mkdir -p database
chmod 755 database
php setup_database.php
\`\`\`

#### 3. "Redirect URI mismatch"
**Solution**: Ensure the redirect URIs in your OAuth provider settings exactly match the URIs in your \`.env\` file

**Google Console**: \`http://localhost:8000/oauth/google_callback.php\`
**GitHub Settings**: \`http://localhost:8000/oauth/github_callback.php\`

#### 4. "Could not retrieve email from GitHub"
**Solution**: Make sure your GitHub email is verified and set to public, or the application has permission to access private emails

#### 5. "Composer dependencies not found"
**Solution**: Install dependencies with Composer

\`\`\`bash
composer install
\`\`\`

### Debugging Tips

1. **Enable Error Display** (development only):
   \`\`\`php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   \`\`\`

2. **Check PHP Extensions**:
   \`\`\`bash
   php -m | grep -E "pdo_sqlite|openssl|curl|json"
   \`\`\`

3. **Verify Database**:
   \`\`\`bash
   sqlite3 database/usuarios.db ".tables"
   sqlite3 database/usuarios.db "SELECT * FROM usuarios;"
   \`\`\`

4. **Test OAuth Callbacks**:
   - Ensure your local server is running on the correct port
   - Check that the redirect URIs match exactly (including protocol and port)

## 📝 Development Notes

### Testing OAuth Locally

Since OAuth providers require valid redirect URIs, you need to:
1. Use \`localhost:8000\` (not 127.0.0.1)
2. Configure the exact same URI in OAuth provider settings
3. For production, update URIs to your domain with HTTPS

### Session Configuration

For production environments:
\`\`\`php
// In config.php or a session configuration file
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);      // HTTPS only
ini_set('session.cookie_samesite', 'Lax');
\`\`\`

### Database Backup

Regular backups recommended:
\`\`\`bash
cp database/usuarios.db database/usuarios_backup_$(date +%Y%m%d).db
\`\`\`

## 🎯 Best Practices Implemented

1. ✅ **Separation of Concerns**: OAuth callbacks in separate directory
2. ✅ **Environment Variables**: Sensitive data in `.env` file
3. ✅ **Error Handling**: Comprehensive try-catch blocks
4. ✅ **Input Validation**: Server-side validation for all inputs
5. ✅ **Output Escaping**: XSS protection with `htmlspecialchars()`
6. ✅ **Prepared Statements**: SQL injection protection
7. ✅ **Session Security**: Regeneration and secure cookies
8. ✅ **Password Security**: Bcrypt hashing
9. ✅ **Code Documentation**: Comprehensive comments
10. ✅ **User Feedback**: Clear error and success messages

## 🔄 Future Enhancements

Possible improvements:
- [ ] Password reset functionality
- [ ] Email verification
- [ ] Two-factor authentication (2FA)
- [ ] Account linking (connect multiple OAuth providers)
- [ ] Profile editing
- [ ] Remember me functionality
- [ ] Rate limiting for login attempts
- [ ] Audit logging
- [ ] Admin panel
- [ ] Multi-language support

## 📚 References

- [OAuth 2.0 Specification](https://oauth.net/2/)
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [GitHub OAuth Documentation](https://docs.github.com/en/developers/apps/building-oauth-apps)
- [PHP Password Hashing](https://www.php.net/manual/en/function.password-hash.php)
- [PDO Documentation](https://www.php.net/manual/en/book.pdo.php)

## 📄 License

This project is open source and available under the MIT License.

## 👨‍💻 Author

Developed as part of a PHP authentication project assignment.

---

**Last Updated**: March 2026
**PHP Version**: 7.4+
**Database**: SQLite3
