#!/bin/bash

# Replace these with your GitHub username and repository name
USERNAME=danielnoveno
REPO_NAME=reusemart_backend

cat <<EOL > README.md
# 🎯 Laravel + Filament Admin Starter

![Build Status](https://img.shields.io/github/workflow/status/$USERNAME/$REPO_NAME/CI)
![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)
![License](https://img.shields.io/github/license/$USERNAME/$REPO_NAME)

This is a starter project using **Laravel** and **Filament Admin**, equipped with:
- ✅ Automatic code formatting (Pint, Prettier)
- ✅ Static analysis (PHPStan)
- ✅ IDE helper for better autocomplete
- ✅ Structured commit messages with **Conventional Commits** using **Commitlint** and **Husky**

---

## 🚀 Project Setup

### 1. Clone the Repository
\`\`\`bash
git clone <repo-url>
cd <repo-folder>
\`\`\`

### 2. Install PHP Dependencies
\`\`\`bash
composer install
\`\`\`

### 3. Install Frontend Dependencies (Node.js)
\`\`\`bash
npm install
\`\`\`

### 4. Copy the \`.env.example\` and Generate the Application Key
\`\`\`bash
cp .env.example .env
php artisan key:generate
\`\`\`

### 5. Enable Husky for Commit Hooks
\`\`\`bash
npx husky install
\`\`\`

---

## 🧹 Code Formatting & Static Analysis

### 🔧 Format PHP Code
\`\`\`bash
composer lint
\`\`\`
Or manually run:
\`\`\`bash
./vendor/bin/pint
\`\`\`

### 🧠 Run PHPStan Static Analysis
\`\`\`bash
vendor/bin/phpstan analyse
\`\`\`

### 🎨 Format Blade and HTML Files (Optional)
\`\`\`bash
npx prettier --write resources/views
\`\`\`

---

## ✅ Commit with Structured Messages

Use **Conventional Commits** for structured commit messages that are automatically used for changelog generation and versioning.

Example:
\`\`\`bash
npx cz
\`\`\`

Commit message examples:
\`\`\`
feat: add OTP login feature
fix(transactions): correct transaction amount validation
\`\`\`

---

## 📦 Required Tools

| Tool        | Minimum Version |
|-------------|-----------------|
| PHP         | 8.1+            |
| Composer    | 2.x             |
| Node.js     | 16.x+           |
| npm         | 8.x+            |
| Git         | Any             |

---

## 🔁 Quick Setup (Optional)

To run all the setup steps at once, add the following script in your \`package.json\`:

\`\`\`json
"scripts": {
  "setup": "composer install && npm install && npx husky install"
}
\`\`\`

Run it with:
\`\`\`bash
npm run setup
\`\`\`

---

## ✍️ Credit

This starter project is customized for a **Laravel + Filament** environment with a focus on code quality, modern development workflows, and maintainability.

EOL

echo "README.md file generated successfully!"
