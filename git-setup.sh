#!/bin/bash

# Beit Jedi Backend - Git Repository Setup Script
# This script will initialize git and prepare for first push

echo "🚀 Setting up Git repository for Beit Jedi Backend..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo -e "${RED}❌ Git is not installed. Please install Git first.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Git is installed${NC}"

# Navigate to project directory
cd /Users/mac/beit_jedi_backend || exit

# Check if already a git repository
if [ -d .git ]; then
    echo -e "${YELLOW}⚠️  Git repository already exists${NC}"
    echo -e "${BLUE}Current remote:${NC}"
    git remote -v
else
    echo -e "${YELLOW}📦 Initializing Git repository...${NC}"
    git init
    echo -e "${GREEN}✅ Git repository initialized${NC}"
fi

# Create .gitignore if it doesn't exist
if [ ! -f .gitignore ]; then
    echo -e "${YELLOW}📝 Creating .gitignore file...${NC}"
    cat > .gitignore << 'EOF'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
.DS_Store
Thumbs.db
*.log
*.swp
*.swo
*~
.project
.settings
.buildpath
EOF
    echo -e "${GREEN}✅ .gitignore created${NC}"
else
    echo -e "${GREEN}✅ .gitignore already exists${NC}"
fi

# Add all files
echo -e "${YELLOW}📦 Adding files to git...${NC}"
git add .

# Check git status
echo -e "${BLUE}📊 Git status:${NC}"
git status --short

# Create initial commit
echo -e "${YELLOW}💾 Creating initial commit...${NC}"
if git diff-index --quiet HEAD --; then
    echo -e "${YELLOW}⚠️  No changes to commit${NC}"
else
    git commit -m "Initial commit - Production ready with Arabic UI redesign

- Redesigned vendor panel to modern Arabic-only UI
- Implemented navy blue and yellow brand colors
- Applied Cairo font throughout
- Localized all pages: dashboard, orders, foods, categories, reviews, chat, reports
- Hidden unnecessary menu items
- Optimized for RTL layout
- Ready for production deployment on Hostinger"
    echo -e "${GREEN}✅ Initial commit created${NC}"
fi

echo ""
echo -e "${GREEN}✅ Git setup complete!${NC}"
echo ""
echo -e "${BLUE}📋 Next steps:${NC}"
echo -e "1. Create a repository on GitHub/GitLab/Bitbucket"
echo -e "2. Copy the SSH or HTTPS URL of your repository"
echo -e "3. Run one of these commands:"
echo ""
echo -e "${YELLOW}For GitHub:${NC}"
echo -e "   git remote add origin git@github.com:YOUR_USERNAME/beit_jedi_backend.git"
echo -e "   git branch -M main"
echo -e "   git push -u origin main"
echo ""
echo -e "${YELLOW}For GitLab:${NC}"
echo -e "   git remote add origin git@gitlab.com:YOUR_USERNAME/beit_jedi_backend.git"
echo -e "   git branch -M main"
echo -e "   git push -u origin main"
echo ""
echo -e "${YELLOW}For Bitbucket:${NC}"
echo -e "   git remote add origin git@bitbucket.org:YOUR_USERNAME/beit_jedi_backend.git"
echo -e "   git branch -M main"
echo -e "   git push -u origin main"
echo ""
echo -e "${BLUE}💡 Tip: Make sure you have SSH keys set up with your Git provider${NC}"
echo -e "   GitHub SSH setup: https://docs.github.com/en/authentication/connecting-to-github-with-ssh"
