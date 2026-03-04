#!/bin/bash

# Quick Test Script
# Tests the main functionality of the OAuth application

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  OAuth 2.0 Application - Quick Test"
echo "═══════════════════════════════════════════════════════════"
echo ""

cd "$(dirname "$0")"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: Check if server is running
echo -n "→ Checking if server is running... "
if curl -s http://localhost:8000 > /dev/null 2>&1; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗${NC}"
    echo ""
    echo "Server is not running. Start it with:"
    echo "  php -S localhost:8000"
    echo ""
    exit 1
fi

# Test 2: Check index page
echo -n "→ Testing index page (login)... "
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/index.php)
if [ "$RESPONSE" = "200" ]; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗ (HTTP $RESPONSE)${NC}"
fi

# Test 3: Check register page
echo -n "→ Testing register page... "
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/register.php)
if [ "$RESPONSE" = "200" ]; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗ (HTTP $RESPONSE)${NC}"
fi

# Test 4: Check database
echo -n "→ Testing database connection... "
if php -r "
try {
    \$db = new PDO('sqlite:database/usuarios.db');
    \$db->query('SELECT COUNT(*) FROM usuarios');
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗${NC}"
fi

# Test 5: Check OAuth configuration
echo -n "→ Checking OAuth configuration... "
if [ -f .env ]; then
    if grep -q "your_google_client_id_here" .env || grep -q "your_github_client_id_here" .env; then
        echo -e "${YELLOW}⚠ Not configured${NC}"
    else
        echo -e "${GREEN}✓${NC}"
    fi
else
    echo -e "${RED}✗ .env missing${NC}"
fi

# Test 6: Count users in database
echo -n "→ Checking database users... "
USER_COUNT=$(sqlite3 database/usuarios.db "SELECT COUNT(*) FROM usuarios;" 2>/dev/null)
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ ($USER_COUNT users)${NC}"
else
    echo -e "${RED}✗${NC}"
fi

# Test 7: Check if Composer dependencies exist
echo -n "→ Checking Composer dependencies... "
if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗${NC}"
fi

# Test 8: Check documentation files
echo -n "→ Checking documentation... "
DOC_COUNT=0
[ -f "README.md" ] && ((DOC_COUNT++))
[ -f "DOCUMENTATION.md" ] && ((DOC_COUNT++))
[ -f "GUIA_CONFIGURACION.md" ] && ((DOC_COUNT++))

if [ $DOC_COUNT -eq 3 ]; then
    echo -e "${GREEN}✓ All present${NC}"
else
    echo -e "${YELLOW}⚠ $DOC_COUNT/3 present${NC}"
fi

# Test 9: Check screenshots
echo -n "→ Checking screenshots... "
SCREENSHOT_COUNT=$(ls pantallazos/*.{png,jpg,jpeg} 2>/dev/null | wc -l)
if [ $SCREENSHOT_COUNT -gt 0 ]; then
    echo -e "${GREEN}✓ ($SCREENSHOT_COUNT images)${NC}"
else
    echo -e "${YELLOW}⚠ No screenshots yet${NC}"
fi

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  Test Summary"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Test OAuth endpoints (if configured)
if ! grep -q "your_google_client_id_here" .env 2>/dev/null; then
    echo "OAuth appears to be configured. Test these URLs:"
    echo ""
    echo "  • Login:      http://localhost:8000"
    echo "  • Register:   http://localhost:8000/register.php"
    echo "  • Dashboard:  http://localhost:8000/dashboard.php"
    echo ""
    echo "Try logging in with:"
    echo "  1. Google OAuth (click 'Continue with Google')"
    echo "  2. GitHub OAuth (click 'Continue with GitHub')"
    echo "  3. Traditional login (username/password)"
else
    echo -e "${YELLOW}⚠ OAuth not configured yet!${NC}"
    echo ""
    echo "To configure OAuth:"
    echo "  1. Edit .env file with your credentials"
    echo "  2. Get Google credentials: https://console.cloud.google.com/"
    echo "  3. Get GitHub credentials: https://github.com/settings/developers"
fi

echo ""
echo "═══════════════════════════════════════════════════════════"
echo ""

# Interactive menu
echo "Would you like to:"
echo "  1. Open application in browser"
echo "  2. View database content"
echo "  3. Check error logs"
echo "  4. Exit"
echo ""
read -p "Enter choice (1-4): " choice

case $choice in
    1)
        echo "Opening browser..."
        if command -v xdg-open > /dev/null; then
            xdg-open http://localhost:8000
        elif command -v open > /dev/null; then
            open http://localhost:8000
        else
            echo "Please open: http://localhost:8000"
        fi
        ;;
    2)
        echo ""
        echo "Database content:"
        echo "═══════════════════════════════════════════════════════════"
        sqlite3 database/usuarios.db <<EOF
.headers on
.mode column
SELECT id, usuario, email, name, oauth_provider, created_at FROM usuarios;
EOF
        ;;
    3)
        echo ""
        echo "Recent PHP errors (if any):"
        echo "═══════════════════════════════════════════════════════════"
        if [ -f "/var/log/php_errors.log" ]; then
            tail -n 20 /var/log/php_errors.log
        else
            echo "No error log found (this is good!)"
        fi
        ;;
    4)
        echo "Goodbye!"
        exit 0
        ;;
    *)
        echo "Invalid choice"
        ;;
esac

echo ""
