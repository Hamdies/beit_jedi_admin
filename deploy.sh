#!/bin/bash

# Beit Jedi Backend - Deployment Script for Hostinger
# Run this script after SSH into your Hostinger server

echo "🚀 Starting Beit Jedi Backend Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
LARAVEL_ROOT="/home/username/domains/beitjedi.albashgroup.com"
PUBLIC_HTML="/home/username/public_html"

echo -e "${YELLOW}📁 Navigating to Laravel root...${NC}"
cd $LARAVEL_ROOT || exit

echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev

echo -e "${YELLOW}🔑 Generating application key (if needed)...${NC}"
php artisan key:generate --force

echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force

echo -e "${YELLOW}🌱 Seeding database (if needed)...${NC}"
# php artisan db:seed --force

echo -e "${YELLOW}🧹 Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo -e "${YELLOW}⚡ Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${YELLOW}🔗 Creating storage symlink...${NC}"
php artisan storage:link

echo -e "${YELLOW}🔒 Setting proper permissions...${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo -e "${YELLOW}📊 Restarting queue workers (if using queues)...${NC}"
# php artisan queue:restart

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${GREEN}🌐 Your application should now be live at: https://beitjedi.albashgroup.com${NC}"

echo -e "\n${YELLOW}📝 Post-deployment checklist:${NC}"
echo "1. ✓ Verify .env file is configured correctly"
echo "2. ✓ Test database connection"
echo "3. ✓ Check file uploads work"
echo "4. ✓ Test vendor panel login"
echo "5. ✓ Verify all Arabic translations display correctly"
echo "6. ✓ Test order creation flow"
echo "7. ✓ Check SSL certificate is active"

echo -e "\n${YELLOW}🔍 Useful commands:${NC}"
echo "View logs: tail -f storage/logs/laravel.log"
echo "Clear cache: php artisan cache:clear"
echo "Run migrations: php artisan migrate"
echo "Check routes: php artisan route:list"
