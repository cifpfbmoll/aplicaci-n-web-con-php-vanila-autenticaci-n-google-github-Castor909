# OAuth 2.0 Authentication with PHP

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-blue)](https://www.php.net)
[![OAuth 2.0](https://img.shields.io/badge/OAuth-2.0-orange)](https://oauth.net/2/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

A complete PHP web application implementing OAuth 2.0 authentication with **Google** and **GitHub**, along with traditional username/password authentication.

## 🚀 Quick Start

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
# Edit .env with your OAuth credentials
```

### 3. Initialize Database

```bash
php setup_database.php
```

### 4. Start Server

```bash
php -S localhost:8000
```

Open: **http://localhost:8000**

## ✨ Features

- ✅ **Google OAuth 2.0** - Login with Google accounts
- ✅ **GitHub OAuth 2.0** - Login with GitHub accounts  
- ✅ **Traditional Auth** - Username/password registration and login
- ✅ **Secure Sessions** - Session regeneration and secure cookies
- ✅ **Password Hashing** - Bcrypt encryption
- ✅ **SQLite Database** - Lightweight database with PDO
- ✅ **Modern UI** - Responsive and mobile-friendly design
- ✅ **Error Handling** - Comprehensive validation and error messages

## 📋 Requirements

- PHP 7.4 or higher
- SQLite3 (included with PHP)
- Composer
- Extensions: `pdo_sqlite`, `openssl`, `curl`, `json`

## 🔧 OAuth Configuration

### Google OAuth

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a project and enable Google+ API
3. Create OAuth 2.0 credentials
4. Add redirect URI: `http://localhost:8000/oauth/google_callback.php`
5. Copy Client ID and Client Secret to `.env`

### GitHub OAuth

1. Go to [GitHub Developer Settings](https://github.com/settings/developers)
2. Create a new OAuth App
3. Set callback URL: `http://localhost:8000/oauth/github_callback.php`
4. Copy Client ID and Client Secret to `.env`

## 📁 Project Structure

```
├── index.php                 # Login page
├── register.php              # Registration page
├── dashboard.php             # Protected dashboard
├── oauth/
│   ├── google_callback.php   # Google OAuth handler
│   └── github_callback.php   # GitHub OAuth handler
├── config.php                # Environment loader
├── oauth_config.php          # OAuth configuration
├── db_connection.php         # Database connection
├── database/                 # SQLite database
├── vendor/                   # Composer dependencies
└── .env                      # Environment variables
```

## 📖 Documentation

For complete documentation, see [DOCUMENTATION.md](DOCUMENTATION.md)

Topics covered:
- Detailed installation instructions
- OAuth 2.0 flow explanation
- Security features
- Database schema
- Troubleshooting guide
- API references

## 🔒 Security Features

- **Password Security**: Bcrypt hashing with automatic salt
- **SQL Injection Protection**: PDO prepared statements
- **XSS Protection**: Output escaping with `htmlspecialchars()`
- **CSRF Protection**: Session regeneration on login
- **Secure Sessions**: HTTPOnly and Secure cookies
- **Input Validation**: Server-side validation
- **OAuth Security**: State parameter, token validation

## 📸 Screenshots

### Login Page
![Login Screen](pantallazos/login.png)

### Dashboard
![Dashboard](pantallazos/dashboard.png)

## 🐛 Troubleshooting

### Configuration Error
```bash
cp .env.example .env
# Edit .env with your credentials
```

### Database Issues
```bash
mkdir -p database
chmod 755 database
php setup_database.php
```

### Redirect URI Mismatch
Ensure OAuth provider settings match exactly:
- Google: `http://localhost:8000/oauth/google_callback.php`
- GitHub: `http://localhost:8000/oauth/github_callback.php`

## 📚 Technologies Used

- **PHP 7.4+** - Server-side language
- **SQLite** - Database
- **PDO** - Database abstraction
- **Google API Client** - Google OAuth
- **Guzzle** - HTTP client for GitHub OAuth
- **OAuth 2.0** - Authentication protocol

## 🎯 Project Objectives

This project demonstrates:
- ✅ Complete OAuth 2.0 authentication flow
- ✅ Secure token and session management
- ✅ User profile information retrieval
- ✅ Environment variable configuration
- ✅ Error handling and validation
- ✅ Modern security practices

## 📝 Assignment Details

**Course**: Web Application Development with PHP  
**Topic**: OAuth 2.0 Authentication Implementation  
**Due Date**: October 15, 2025, 23:59  
**Requirements**: Fully functional OAuth authentication with documentation

## 👨‍💻 Development

### Running Tests
```bash
# Check PHP version
php -v

# Check required extensions
php -m | grep -E "pdo_sqlite|openssl|curl|json"

# Verify database
sqlite3 database/usuarios.db ".tables"
```

### Development Server
```bash
# Start server
php -S localhost:8000

# Alternative ports
php -S localhost:3000
```

## 🔄 Version History

- **v1.0.0** (2025-10-15) - Initial release
  - Google OAuth 2.0 implementation
  - GitHub OAuth 2.0 implementation
  - Traditional authentication
  - Complete documentation
  - Security features

## 📄 License

This project is open source and available under the MIT License.

## 🙏 Acknowledgments

- [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2)
- [GitHub OAuth Documentation](https://docs.github.com/en/developers/apps/building-oauth-apps)
- [PHP Manual](https://www.php.net/manual/)
- [OAuth 2.0 Specification](https://oauth.net/2/)

---

**Made with ❤️ for learning OAuth 2.0 authentication**

