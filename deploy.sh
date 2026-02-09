#!/bin/bash

echo "SimpleLicManager Production Deployment Script"
echo "=============================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Error: Composer is not installed!${NC}"
    exit 1
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Warning: .env file not found. Copying from .env.example${NC}"
    cp .env.example .env
    echo -e "${GREEN}.env file created. Please edit it with your production settings.${NC}"
    exit 0
fi

echo "Step 1: Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Dependencies installed${NC}"
else
    echo -e "${RED}✗ Failed to install dependencies${NC}"
    exit 1
fi

echo ""
echo "Step 2: Generating application key..."
php artisan key:generate --force
echo -e "${GREEN}✓ Application key generated${NC}"

echo ""
echo "Step 3: Running migrations..."
read -p "Do you want to run migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo -e "${GREEN}✓ Migrations completed${NC}"
fi

echo ""
echo "Step 4: Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

echo ""
echo "Step 5: Creating optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo -e "${GREEN}✓ Optimized caches created${NC}"

echo ""
echo "Step 6: Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo -e "${GREEN}=============================================="
echo "Deployment completed successfully!"
echo "==============================================${NC}"
echo ""
echo "Next steps for cPanel hosting:"
echo "1. Upload all files to your server"
echo "2. Move 'public' folder contents to 'public_html'"
echo "3. Update public_html/index.php paths"
echo "4. Configure your database in .env"
echo "5. Run migrations if not done: php artisan migrate --seed"
echo ""
