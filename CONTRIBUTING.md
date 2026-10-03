# Contributing to Barbería Premium

Thanks for your interest in contributing! This document outlines how to set up a development environment and the guidelines for contributing to this project.

## Development Setup

### Prerequisites
- PHP 7.4+ (with `mysqli`, `json` extensions)
- MySQL 5.7+ / MariaDB 10.2+
- Apache or PHP built-in server

### Get the Code
```bash
git clone https://github.com/your-username/barberia-premium.git
cd barberia-premium
```

### Local Environment
1. Create the database:
   ```sql
   CREATE DATABASE barberia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Import the schema:
   ```bash
   mysql -u root -p barberia < database/init.sql
   ```
3. Configure your database credentials in `public/includes/config.php`:
   ```php
   $servername = "localhost";
   $username   = "root";
   $password   = "";
   $dbname     = "barberia";
   ```
4. Start a local PHP server:
   ```bash
   php -S localhost:8000 -t public/
   ```
5. Visit `http://localhost:8000/index.php` and `http://localhost:8000/../admin/login.php`

## Coding Standards

### PHP
- Use **PSR-12** formatting style (4-space indentation, opening braces on their own line for classes/functions).
- Always use **prepared statements** (`mysqli::prepare`) for database queries. Never interpolate user input into SQL strings.
- Use **password_hash()** / **password_verify()** for all password storage. Never store plain-text passwords.
- Sanitize all user-facing output with `htmlspecialchars()`.
- Use early returns and guard clauses for readability.
- Keep files focused — one primary responsibility per script.

### CSS
- Follow the existing dark-theme ("Nocturno Urbano") conventions.
- Use semantic class names that reflect the component they style.
- Avoid inline `<style>` blocks in new code; extend `style.css` instead.

### JavaScript
- Use vanilla JS unless jQuery is already loaded on the page.
- Keep inline scripts in the same file for now (legacy pattern); aim to externalize in future refactors.
- Always handle errors in AJAX/fetch calls and return JSON responses with a consistent structure: `{ success: true/false, ... }`.

## How to Contribute

1. **Fork** the repository on GitHub.
2. **Create a branch** for your feature or fix:
   ```bash
   git checkout -b feat/add-dark-mode-toggle
   ```
3. **Make your changes** following the coding standards above.
4. **Test** your changes locally on a PHP + MySQL environment.
5. **Commit** with a clear, descriptive message:
   ```
   Add dark mode toggle to admin dashboard
   
   - Persist user preference in localStorage
   - Add CSS variables for theme switching
   ```
6. **Push** to your fork and open a **Pull Request**.

### Pull Request Guidelines
- Include a clear description of what the PR does and why.
- Reference any relevant issues (e.g., `Fixes #42`).
- Keep PRs focused — one feature or fix per PR.
- Ensure all existing functionality still works after your changes.

## Reporting Issues

When reporting a bug, please include:
- PHP version and MySQL version
- Steps to reproduce
- Expected vs. actual behavior
- Any relevant error messages or screenshots

Use the GitHub issue templates when available.

## Security

If you discover a security vulnerability, please report it privately by emailing the maintainers. Do not open a public issue for security problems.

## Code of Conduct

Be respectful of other contributors. Harassment, discrimination, or unprofessional behavior of any kind will not be tolerated.
