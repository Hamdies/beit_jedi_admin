# Hostinger Deployment Guide - Beit Jedi Backend

## Pre-Deployment Checklist

### 1. Environment Configuration
- [ ] Update `.env` for production
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database credentials
- [ ] Set correct `APP_URL`
- [ ] Update mail configuration
- [ ] Configure storage and cache drivers

### 2. Git Repository Setup (Hostinger)

#### Step 1: Create SSH Key in Hostinger
1. Go to Hostinger hPanel
2. Navigate to: Advanced → GIT
3. Click "Generate SSH Key"
4. Copy the generated SSH public key

#### Step 2: Add SSH Key to GitHub/GitLab/Bitbucket
1. Go to your Git provider (GitHub/GitLab/Bitbucket)
2. Add the SSH key to your account:
   - **GitHub**: Settings → SSH and GPG keys → New SSH key
   - **GitLab**: Preferences → SSH Keys
   - **Bitbucket**: Personal settings → SSH keys

#### Step 3: Create Repository in Hostinger
1. In Hostinger GIT section, click "Create a New Repository"
2. Fill in:
   - **Repository**: Your Git repository URL (SSH format)
     - GitHub: `git@github.com:username/repository.git`
     - GitLab: `git@gitlab.com:username/repository.git`
   - **Branch**: `main` or `master`
   - **Directory**: Leave empty (will deploy to public_html) or specify `public_html`

#### Step 4: Initial Git Setup (Local)
```bash
# Initialize git if not already done
cd /Users/mac/beit_jedi_backend
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - Production ready"

# Add remote (replace with your repository URL)
git remote add origin git@github.com:yourusername/beit_jedi_backend.git

# Push to remote
git push -u origin main
```

### 3. File Structure on Hostinger

Your Laravel app should be structured like this on Hostinger:
```
/home/username/
├── public_html/          # This is your document root
│   ├── index.php         # Laravel's public/index.php (moved here)
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── storage -> ../domains/yoursite.com/storage/app/public
├── domains/
│   └── yoursite.com/     # Your Laravel app root
│       ├── app/
│       ├── bootstrap/
│       ├── config/
│       ├── database/
│       ├── resources/
│       ├── routes/
│       ├── storage/
│       ├── vendor/
│       ├── .env
│       ├── artisan
│       └── composer.json
```

### 4. Post-Deployment Commands (Run via SSH)

```bash
# Connect via SSH to Hostinger
ssh username@yoursite.com

# Navigate to your Laravel root
cd domains/yoursite.com

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key (if not set)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache
```

### 5. Update .htaccess in public_html

Create/update `/public_html/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to Laravel's public folder
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L]
</IfModule>
```

OR if you moved index.php to public_html root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 6. Update index.php (if moved to public_html)

Update paths in `/public_html/index.php`:
```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Update these paths to point to your Laravel installation
require __DIR__.'/../domains/yoursite.com/vendor/autoload.php';

$app = require_once __DIR__.'/../domains/yoursite.com/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### 7. Production .env Configuration

```env
APP_NAME="Beit Jedi"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://beitjedi.albashgroup.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_production_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Update all other credentials for production
```

### 8. Security Checklist
- [ ] Set `APP_DEBUG=false`
- [ ] Remove any test/debug routes
- [ ] Ensure `.env` is not publicly accessible
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Enable HTTPS/SSL certificate
- [ ] Configure CORS properly
- [ ] Set up database backups

### 9. Testing After Deployment
1. Visit your domain: `https://beitjedi.albashgroup.com`
2. Test vendor panel login
3. Check database connections
4. Test file uploads
5. Verify all Arabic translations are working
6. Test order flow
7. Check all API endpoints

### 10. Continuous Deployment (Optional)

Set up automatic deployment on git push:
1. In Hostinger GIT section, enable "Auto Deploy"
2. Every time you push to your repository, it will auto-deploy
3. You may need to run post-deployment commands manually via SSH

## Quick Deployment Commands

```bash
# After pushing to Git, SSH into Hostinger and run:
cd domains/yoursite.com
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart  # if using queues
```

## Troubleshooting

### Issue: 500 Internal Server Error
- Check storage and bootstrap/cache permissions
- Check .env file exists and is configured
- Check error logs in storage/logs/laravel.log

### Issue: CSS/JS not loading
- Run `php artisan storage:link`
- Check asset paths in .env
- Clear browser cache

### Issue: Database connection failed
- Verify DB credentials in .env
- Check if database exists in Hostinger
- Verify DB user has proper permissions

### Issue: Session/Cache issues
- Run `php artisan cache:clear`
- Run `php artisan config:clear`
- Check storage permissions

## Support
- Hostinger Support: https://www.hostinger.com/support
- Laravel Deployment Docs: https://laravel.com/docs/deployment
