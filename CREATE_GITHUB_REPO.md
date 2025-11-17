# 🎯 Create GitHub Repository - Step by Step

## Option 1: Create via GitHub Website (Easiest)

### Step 1: Go to GitHub
1. Open your browser and go to: **https://github.com/new**
2. Login if you're not already logged in

### Step 2: Fill Repository Details
- **Repository name**: `beit_jedi_backend`
- **Description**: `Beit Jedi Backend - Food delivery platform with Arabic UI`
- **Visibility**: Choose **Private** (recommended for production code)
- **DO NOT** check "Initialize this repository with:"
  - ❌ Don't add README
  - ❌ Don't add .gitignore
  
  (We already have these files locally)

### Step 3: Click "Create repository"

### Step 4: Copy the SSH URL
After creating, GitHub will show you a page with commands.
Copy the SSH URL that looks like:
```
git@github.com:YOUR_USERNAME/beit_jedi_backend.git
```

### Step 5: Push Your Code
Open Terminal and run these commands:

```bash
cd /Users/mac/beit_jedi_backend

# Add the remote repository
git remote add origin git@github.com:YOUR_USERNAME/beit_jedi_backend.git

# Rename branch to main (if needed)
git branch -M main

# Push your code
git push -u origin main
```

---

## Option 2: Create via GitHub CLI (Advanced)

If you have GitHub CLI installed:

```bash
cd /Users/mac/beit_jedi_backend

# Login to GitHub CLI
gh auth login

# Create repository
gh repo create beit_jedi_backend --private --source=. --remote=origin

# Push code
git push -u origin main
```

---

## Verify SSH Keys (If Push Fails)

If you get "Permission denied" error, you need to set up SSH keys:

### Check if you have SSH keys:
```bash
ls -la ~/.ssh
```

### If no SSH keys exist, create them:
```bash
ssh-keygen -t ed25519 -C "your_email@example.com"
# Press Enter to accept default location
# Enter a passphrase (optional but recommended)
```

### Add SSH key to ssh-agent:
```bash
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519
```

### Copy your public key:
```bash
cat ~/.ssh/id_ed25519.pub
# Copy the entire output
```

### Add to GitHub:
1. Go to: https://github.com/settings/keys
2. Click "New SSH key"
3. Title: `Mac - Beit Jedi`
4. Paste your public key
5. Click "Add SSH key"

### Test connection:
```bash
ssh -T git@github.com
# Should say: "Hi username! You've successfully authenticated..."
```

---

## After Successful Push

Once you've pushed your code, you can:

1. **View your repository**: `https://github.com/YOUR_USERNAME/beit_jedi_backend`

2. **Set up Hostinger Git**:
   - Follow the instructions in `QUICK_START_HOSTINGER.md`
   - Use this repository URL in Hostinger GIT section

3. **Future updates**:
   ```bash
   # Make changes to your code
   git add .
   git commit -m "Description of changes"
   git push origin main
   ```

---

## Troubleshooting

### Error: "remote origin already exists"
```bash
git remote remove origin
git remote add origin git@github.com:YOUR_USERNAME/beit_jedi_backend.git
```

### Error: "Permission denied (publickey)"
- Follow the SSH key setup steps above
- Make sure you're using the SSH URL (git@github.com:...) not HTTPS

### Error: "Updates were rejected"
```bash
git pull origin main --rebase
git push origin main
```

---

## Next Steps After GitHub Setup

1. ✅ Code is on GitHub
2. 📋 Follow `QUICK_START_HOSTINGER.md` to deploy
3. 🔗 Connect Hostinger to your GitHub repository
4. 🚀 Deploy to production!

---

## Quick Reference

```bash
# Check remote
git remote -v

# Check status
git status

# View commit history
git log --oneline

# Create new branch
git checkout -b feature-name

# Switch branches
git checkout main

# Pull latest changes
git pull origin main

# Push changes
git push origin main
```
