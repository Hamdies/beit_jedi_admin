# 🚀 Quick Start Guide - Deploy to Hostinger

## Step 1: Prepare Your Git Repository

### Option A: Using GitHub (Recommended)

1. **Create a new repository on GitHub:**
   - Go to https://github.com/new
   - Repository name: `beit_jedi_backend`
   - Set as **Private**
   - Don't initialize with README (we already have files)

2. **Push your code to GitHub:**
   ```bash
   cd /Users/mac/beit_jedi_backend
   
   # Initialize git (if not already done)
   git init
   
   # Add all files
   git add .
   
   # Create .gitignore if needed
   echo "node_modules/
   /vendor/
   .env
   .env.backup
   .phpunit.result.cache
   Homestead.json
   Homestead.yaml
   npm-debug.log
   yarn-error.log
   /.idea
   /.vscode" > .gitignore
   
   # Commit
   git commit -m "Initial commit - Production ready with Arabic UI"
   
   # Add remote (replace YOUR_USERNAME with your GitHub username)
   git remote add origin https://github.com/YOUR_USERNAME/beit_jedi_backend.git
   
   # Push to GitHub
   git branch -M main
   git push -u origin main
   ```

### Option B: Using GitLab or Bitbucket
- Follow similar steps on GitLab.com or Bitbucket.org
- Use their respective URLs for remote origin

---

## Step 2: Set Up Git on Hostinger

1. **Login to Hostinger hPanel:**
   - Go to https://hpanel.hostinger.com
   - Login with your credentials

2. **Navigate to GIT section:**
   - In the left sidebar, find **Advanced** → **GIT**
   - You should see the screen from your screenshot

3. **Generate SSH Key:**
   - Click **"Generate SSH Key"** button
   - Copy the generated public SSH key

4. **Add SSH Key to GitHub:**
   - Go to GitHub.com → Settings → SSH and GPG keys
   - Click **"New SSH key"**
   - Title: `Hostinger Server`
   - Paste the SSH key from Hostinger
   - Click **"Add SSH key"**

5. **Create Repository in Hostinger:**
   - Back in Hostinger GIT section
   - Click **"Create a New Repository"**
   - Fill in:
     - **Repository**: `git@github.com:YOUR_USERNAME/beit_jedi_backend.git`
     - **Branch**: `main`
     - **Directory**: Leave empty or type `public_html`
   - Click **"Create"**

6. **Clone Repository:**
   - Hostinger will automatically clone your repository
   - Wait for the process to complete

---

## Step 3: Configure Database on Hostinger

1. **Create MySQL Database:**
   - In hPanel, go to **Databases** → **MySQL Databases**
   - Click **"Create New Database"**
   - Database name: `beitjedi_db` (or your choice)
   - Create a database user with a strong password
   - Grant all privileges to the user

2. **Note down credentials:**
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

---

## Step 4: Configure .env File

1. **Access File Manager:**
   - In hPanel, go to **Files** → **File Manager**
   - Navigate to your Laravel installation directory

2. **Create .env file:**
   - Copy `.env.production` to `.env`
   - Or upload the `.env` file via File Manager

3. **Update .env with your credentials:**
   ```env
   APP_NAME="Beit Jedi"
   APP_ENV=production
   APP_KEY=base64:YOUR_KEY_HERE
   APP_DEBUG=false
   APP_URL=https://beitjedi.albashgroup.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=beitjedi_db
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

---

## Step 5: Run Deployment Commands via SSH

1. **Access SSH:**
   - In hPanel, go to **Advanced** → **SSH Access**
   - Enable SSH if not already enabled
   - Note your SSH credentials

2. **Connect via SSH:**
   ```bash
   # On your Mac terminal
   ssh username@beitjedi.albashgroup.com
   # Or
   ssh username@server-ip-address
   ```

3. **Navigate to your Laravel directory:**
   ```bash
   cd domains/beitjedi.albashgroup.com
   # Or wherever your Laravel app is located
   ```

4. **Make deploy script executable:**
   ```bash
   chmod +x deploy.sh
   ```

5. **Run deployment script:**
   ```bash
   ./deploy.sh
   ```

   **OR run commands manually:**
   ```bash
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   
   # Generate app key
   php artisan key:generate --force
   
   # Run migrations
   php artisan migrate --force
   
   # Cache config
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Link storage
   php artisan storage:link
   
   # Set permissions
   chmod -R 755 storage bootstrap/cache
   ```

---

## Step 6: Configure Web Server (Important!)

### If Laravel is in subdirectory:

1. **Update public_html/.htaccess:**
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ domains/beitjedi.albashgroup.com/public/$1 [L]
   </IfModule>
   ```

### OR Move public contents to public_html:

1. **Copy contents of `public/` to `public_html/`:**
   ```bash
   cp -r domains/beitjedi.albashgroup.com/public/* public_html/
   ```

2. **Update index.php paths:**
   Edit `public_html/index.php` and update paths:
   ```php
   require __DIR__.'/../domains/beitjedi.albashgroup.com/vendor/autoload.php';
   $app = require_once __DIR__.'/../domains/beitjedi.albashgroup.com/bootstrap/app.php';
   ```

---

## Step 7: Enable SSL Certificate

1. **In hPanel, go to Security → SSL:**
   - Select your domain
   - Install free Let's Encrypt SSL certificate
   - Force HTTPS redirect

---

## Step 8: Test Your Application

1. **Visit your domain:**
   - https://beitjedi.albashgroup.com

2. **Test vendor panel:**
   - https://beitjedi.albashgroup.com/restaurant-panel

3. **Check logs if issues:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## Step 9: Set Up Automatic Deployment (Optional)

1. **In Hostinger GIT section:**
   - Enable **"Auto Deploy"** for your repository
   - Now every `git push` will auto-deploy

2. **After each push, SSH and run:**
   ```bash
   cd domains/beitjedi.albashgroup.com
   ./deploy.sh
   ```

---

## Common Issues & Solutions

### Issue: 500 Internal Server Error
**Solution:**
```bash
# Check permissions
chmod -R 755 storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear

# Check .env exists
ls -la .env

# Check error logs
tail -f storage/logs/laravel.log
```

### Issue: Database connection failed
**Solution:**
- Verify DB credentials in `.env`
- Check database exists in Hostinger MySQL
- Verify user has correct privileges

### Issue: CSS/JS not loading
**Solution:**
```bash
# Link storage
php artisan storage:link

# Check APP_URL in .env
# Clear browser cache
```

### Issue: Composer not found
**Solution:**
```bash
# Use full path to composer
/usr/local/bin/composer install --optimize-autoloader --no-dev
```

---

## Important Security Notes

✅ **Do this:**
- Set `APP_DEBUG=false` in production
- Use strong database passwords
- Keep `.env` file secure (never commit to git)
- Enable SSL/HTTPS
- Regular backups of database
- Update dependencies regularly

❌ **Don't do this:**
- Never commit `.env` to git
- Don't use `APP_DEBUG=true` in production
- Don't use weak passwords
- Don't expose sensitive API keys

---

## Support Resources

- **Hostinger Support:** https://www.hostinger.com/support
- **Laravel Docs:** https://laravel.com/docs
- **Deployment Guide:** See `DEPLOYMENT_GUIDE.md` for detailed info

---

## Quick Commands Reference

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear all cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Optimize
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Run migrations
php artisan migrate --force

# Check Laravel version
php artisan --version

# List all routes
php artisan route:list

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## 🎉 Congratulations!

Your Beit Jedi Backend with beautiful Arabic UI should now be live!

**Next Steps:**
1. Create admin/vendor accounts
2. Test all features
3. Set up regular backups
4. Monitor error logs
5. Configure email settings
6. Set up payment gateways

**Need help?** Check the detailed `DEPLOYMENT_GUIDE.md` file.
