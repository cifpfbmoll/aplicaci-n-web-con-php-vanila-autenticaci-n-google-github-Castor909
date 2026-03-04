# Screenshots Guide - What to Capture

## Required Screenshots for Project Submission

Create these screenshots and save them in the `pantallazos/` directory:

### 1. **login.png** - Main Login Page
- Navigate to: `http://localhost:8000`
- Capture: Full page showing:
  - Login form with username/password fields
  - "Continue with Google" button
  - "Continue with GitHub" button
  - Registration link

### 2. **google-oauth-consent.png** - Google Authorization Screen
- Click "Continue with Google"
- Capture: Google's authorization page showing:
  - Your app name requesting permissions
  - Email and profile scopes
  - Allow/Deny buttons

### 3. **github-oauth-consent.png** - GitHub Authorization Screen
- Click "Continue with GitHub"
- Capture: GitHub's authorization page showing:
  - Your app name
  - Requested permissions
  - Authorize button

### 4. **dashboard-google.png** - Dashboard After Google Login
- After authorizing with Google
- Capture: Dashboard showing:
  - Welcome message with user's name
  - Google avatar/profile picture
  - User information cards
  - "OAuth 2.0 - Google" badge
  - Profile details from Google

### 5. **dashboard-github.png** - Dashboard After GitHub Login
- Logout and login with GitHub
- Capture: Dashboard showing:
  - Welcome message with GitHub username
  - GitHub avatar
  - User information cards
  - "OAuth 2.0 - GitHub" badge
  - Profile details from GitHub

### 6. **register.png** - Registration Form
- Click "Don't have an account? Register here"
- Capture: Registration page showing:
  - Username field
  - Email field (optional)
  - Password fields
  - Password requirements
  - Create Account button

### 7. **traditional-login-success.png** - Traditional Login Success
- Register a new account
- Login with username/password
- Capture: Dashboard showing:
  - Welcome message
  - "Traditional Login" badge
  - Profile information
  - No OAuth provider

### 8. **logout.png** - Logout and Redirect
- Click logout button
- Capture: Login page with success message:
  - "You have been successfully logged out"
  - Back to login form

### 9. **oauth-config-example.png** - Environment Configuration (Optional)
- Open `.env.example` in editor
- Capture: Configuration template showing:
  - Google OAuth settings
  - GitHub OAuth settings
  - Application settings
  - Comments explaining each setting

### 10. **database-structure.png** - Database Schema (Optional)
- Run in terminal: `sqlite3 database/usuarios.db ".schema usuarios"`
- Capture: Terminal showing the table structure

## How to Take Screenshots

### Linux (Ubuntu/Debian):
```bash
# Using Gnome Screenshot
gnome-screenshot -f pantallazos/login.png

# Using Flameshot (more features)
flameshot gui
# Then save to pantallazos/
```

### Using Browser Developer Tools:
1. Open DevTools (F12)
2. Press `Ctrl+Shift+P` (or `Cmd+Shift+P` on Mac)
3. Type "screenshot"
4. Select "Capture full size screenshot"
5. Save to `pantallazos/` directory

### Quick Bash Script to Organize:
```bash
#!/bin/bash
# After taking screenshots, rename them:
cd pantallazos/
mv Screenshot*.png login.png
# etc.
```

## Screenshot Checklist

Before submission, verify you have:

- [ ] `login.png` - Login page with OAuth buttons
- [ ] `google-oauth-consent.png` - Google authorization
- [ ] `github-oauth-consent.png` - GitHub authorization
- [ ] `dashboard-google.png` - Dashboard with Google profile
- [ ] `dashboard-github.png` - Dashboard with GitHub profile
- [ ] `register.png` - Registration form
- [ ] `traditional-login-success.png` - Dashboard with traditional login
- [ ] `logout.png` - Logout confirmation

## Tips for Good Screenshots

1. **Use Full Browser Window**: Don't crop too much
2. **Show URL Bar**: Helps verify localhost
3. **Clear Browser**: No unnecessary tabs/extensions visible
4. **Good Lighting**: If taking photo of screen
5. **Readable Text**: Ensure text is not blurry
6. **Consistent Size**: Try to keep similar dimensions
7. **Hide Sensitive Info**: If any real OAuth credentials visible

## After Taking Screenshots

Update the README.md to reference them:
```markdown
### Screenshots

![Login Page](pantallazos/login.png)
*Login page with OAuth options*

![Dashboard](pantallazos/dashboard-google.png)
*User dashboard after Google authentication*
```

## Testing Preparation

Before taking screenshots:

1. **Configure OAuth** (use test accounts):
   - For testing, you can use:
   - Your personal Google account
   - Your GitHub account

2. **Clear Browser Cache**:
   - Ensure clean session
   - Test from fresh state

3. **Test Each Flow**:
   - ✓ Google OAuth login
   - ✓ GitHub OAuth login  
   - ✓ Traditional registration
   - ✓ Traditional login
   - ✓ Logout

4. **Verify Data**:
   - Check dashboard shows correct info
   - Verify avatars load
   - Confirm OAuth badges display

## Documentation in Submissions

Include these screenshots in your project documentation with descriptions:

```markdown
## Application Screenshots

### Authentication Flow

**Login Page**
![Login](pantallazos/login.png)
Users can authenticate using Google, GitHub, or traditional credentials.

**OAuth Authorization**
![Google OAuth](pantallazos/google-oauth-consent.png)
Google's secure authorization flow.

**User Dashboard**
![Dashboard](pantallazos/dashboard-google.png)
Personalized dashboard displaying user information retrieved via OAuth 2.0.
```

---

**Ready to take screenshots?**

1. Start the server: `php -S localhost:8000`
2. Open: `http://localhost:8000`
3. Follow the list above
4. Save each screenshot with the suggested filename

Good luck! 📸
