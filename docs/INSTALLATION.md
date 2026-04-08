# Installation Guide

Complete guide to install and configure OpenCode WordPress repository.

## Overview

OpenCode WordPress is a comprehensive repository containing:
- 8 specialized skills for WordPress development
- 6 WordPress-specific rules
- 4 specialized agents
- 5 custom commands
- 3 pre-commit hooks
- Complete examples for themes, plugins, and WooCommerce

## Requirements

### System Requirements

- **Operating System**: Linux, macOS, Windows (with Git Bash or WSL)
- **Git**: Version 2.0 or higher
- **OpenCode**: Latest version installed
- **WordPress**: 5.8+ (for examples)
- **PHP**: 7.4+ (for testing examples)
- **Node.js**: 14+ (for hooks)

### OpenCode Requirements

Ensure OpenCode is properly installed:

```bash
# Check OpenCode installation
opencode --version

# Verify configuration
opencode config list
```

## Installation Methods

### Method 1: Quick Install (Recommended)

Use the automated installation script:

```bash
# Clone repository
git clone https://github.com/promoweb/opencode-wordpress.git

# Navigate to repository
cd opencode-wordpress

# Run installation script
chmod +x install.sh
./install.sh
```

The script will:
1. Detect your OpenCode configuration directory
2. Copy all skills, agents, rules, commands, and hooks
3. Update OpenCode configuration
4. Verify installation

### Method 2: Manual Install

Step-by-step manual installation:

#### Step 1: Clone Repository

```bash
git clone https://github.com/promoweb/opencode-wordpress.git
cd opencode-wordpress
```

#### Step 2: Locate OpenCode Directory

Find your OpenCode configuration directory:

```bash
# Default locations:
# Linux/macOS: ~/.config/opencode/ or ~/.opencode/
# Windows: %APPDATA%\opencode\ or C:\Users\Username\.opencode\

# Check current location
opencode config path
```

#### Step 3: Copy Skills

```bash
# Create skills directory if not exists
mkdir -p ~/.config/opencode/skills

# Copy WordPress skills
cp -r skills/* ~/.config/opencode/skills/
```

Skills copied:
- wordpress-theme-development
- wordpress-plugin-development
- woocommerce-patterns
- wordpress-security
- wordpress-rest-api
- wordpress-testing
- wordpress-hooks-filters
- wordpress-database

#### Step 4: Copy Agents

```bash
# Create agents directory if not exists
mkdir -p ~/.config/opencode/agents

# Copy WordPress agents
cp -r agents/* ~/.config/opencode/agents/
```

Agents copied:
- wordpress-reviewer
- wordpress-build-resolver
- theme-reviewer
- plugin-reviewer

#### Step 5: Copy Rules

```bash
# Create rules directories
mkdir -p ~/.config/opencode/rules/common
mkdir -p ~/.config/opencode/rules/wordpress

# Copy rules
cp -r rules/common/* ~/.config/opencode/rules/common/
cp -r rules/wordpress/* ~/.config/opencode/rules/wordpress/
```

Rules copied:
- Common: git-workflow, code-style, documentation, security
- WordPress: coding-style, hooks, patterns, security, testing, database

#### Step 6: Copy Commands

```bash
# Create commands directory
mkdir -p ~/.config/opencode/.opencode/commands

# Copy commands
cp -r .opencode/commands/* ~/.config/opencode/.opencode/commands/
```

Commands copied:
- wp-theme
- wp-plugin
- wp-review
- wp-build-fix
- wc-build

#### Step 7: Copy Hooks

```bash
# Create hooks directory
mkdir -p ~/.config/opencode/hooks

# Copy hooks
cp -r hooks/* ~/.config/opencode/hooks/

# Install hook dependencies if package.json exists
cd ~/.config/opencode/hooks
npm install
```

Hooks copied:
- php-lint.js
- wp-debug-check.js
- security-check.js

#### Step 8: Update Configuration

Update OpenCode configuration file:

```bash
# Backup existing config
cp ~/.config/opencode/.opencode/opencode.json ~/.config/opencode/.opencode/opencode.json.backup

# Merge configurations
# Open opencode.json and add:
{
  "skillsDir": "~/.config/opencode/skills",
  "agentsDir": "~/.config/opencode/agents",
  "rulesDir": "~/.config/opencode/rules",
  "commandsDir": "~/.config/opencode/.opencode/commands",
  "hooksDir": "~/.config/opencode/hooks"
}
```

Or use the provided config:

```bash
cp .opencode/opencode.json ~/.config/opencode/.opencode/opencode.json
```

### Method 3: NPM Install (Coming Soon)

```bash
# Future: npm package
npm install -g opencode-wordpress
```

## Post-Installation

### Verify Installation

Check all components are installed:

```bash
# List installed skills
opencode skills list

# List installed agents
opencode agents list

# List installed commands
opencode commands list

# Check configuration
opencode config validate
```

### Test Installation

Test a skill:

```bash
# Test theme development skill
opencode skill wordpress-theme-development

# Test a command
/wp-theme create my-theme
```

### Configure Hooks

Hooks run automatically, but verify they're working:

```bash
# Navigate to hooks directory
cd ~/.config/opencode/hooks

# Test PHP lint hook
node php-lint.js test-file.php

# Check hook permissions
ls -la *.js
```

## Configuration Options

### Custom Configuration

Customize OpenCode WordPress behavior:

```json
{
  "wordpress": {
    "version": "6.0",
    "phpVersion": "8.0",
    "codingStandards": "WordPress",
    "securityLevel": "strict",
    "enableTesting": true,
    "enableHooks": true,
    "autoFix": true
  }
}
```

### Environment Variables

Set environment variables for advanced configuration:

```bash
# WordPress version
export WP_VERSION=6.0

# PHP version
export PHP_VERSION=8.0

# Enable debug mode
export OPENCODE_WP_DEBUG=true

# Hook timeout
export OPENCODE_HOOK_TIMEOUT=5000
```

## Directory Structure After Installation

```
~/.config/opencode/
├── .opencode/
│   ├── opencode.json          # Configuration
│   ├── commands/               # Custom commands
│   │   ├── wp-theme.md
│   │   ├── wp-plugin.md
│   │   ├── wp-review.md
│   │   ├── wp-build-fix.md
│   │   └── wc-build.md
│   ├── instructions/
│   │   └── INSTRUCTIONS.md
│   └── prompts/
│   └── agents/
│       ├── wordpress-reviewer.txt
│       ├── wordpress-build-resolver.txt
│       ├── theme-reviewer.txt
│       └── plugin-reviewer.txt
├── skills/                     # WordPress skills
│   ├── wordpress-theme-development/
│   │   └── SKILL.md
│   ├── wordpress-plugin-development/
│   │   └ SKILL.md
│   ├── woocommerce-patterns/
│   │   └ SKILL.md
│   ├── wordpress-security/
│   │   └ SKILL.md
│   ├── wordpress-rest-api/
│   │   └ SKILL.md
│   ├── wordpress-testing/
│   │   └ SKILL.md
│   ├── wordpress-hooks-filters/
│   │   └ SKILL.md
│   └── wordpress-database/
│   │   └ SKILL.md
├── agents/                     # WordPress agents
│   ├── wordpress-reviewer.md
│   ├── wordpress-build-resolver.md
│   ├── theme-reviewer.md
│   └── plugin-reviewer.md
├── rules/                      # Development rules
│   ├── common/
│   │   ├── git-workflow.md
│   │   ├── code-style.md
│   │   ├── documentation.md
│   │   └── security.md
│   └ wordpress/
│   │   ├── coding-style.md
│   │   ├── hooks.md
│   │   ├── patterns.md
│   │   ├── security.md
│   │   ├── testing.md
│   │   └ database.md
└── hooks/                      # Pre-commit hooks
    ├── php-lint.js
    ├── wp-debug-check.js
    ├── security-check.js
    └ package.json
```

## Updating

### Update Repository

```bash
# Navigate to repository
cd opencode-wordpress

# Pull latest changes
git pull origin main

# Re-run installation
./install.sh
```

### Update Individual Components

```bash
# Update skills only
cp -r skills/* ~/.config/opencode/skills/

# Update agents only
cp -r agents/* ~/.config/opencode/agents/

# Update rules only
cp -r rules/* ~/.config/opencode/rules/
```

## Uninstallation

### Complete Uninstall

```bash
# Remove all WordPress components
rm -rf ~/.config/opencode/skills/wordpress-*
rm -rf ~/.config/opencode/agents/wordpress-*
rm -rf ~/.config/opencode/rules/wordpress
rm -rf ~/.config/opencode/.opencode/commands/wp-*
rm -rf ~/.config/opencode/.opencode/commands/wc-*
rm -rf ~/.config/opencode/hooks/php-lint.js
rm -rf ~/.config/opencode/hooks/wp-debug-check.js
rm -rf ~/.config/opencode/hooks/security-check.js

# Restore backup config
cp ~/.config/opencode/.opencode/opencode.json.backup ~/.config/opencode/.opencode/opencode.json
```

### Partial Uninstall

Remove specific components:

```bash
# Remove specific skill
rm -rf ~/.config/opencode/skills/wordpress-theme-development

# Remove specific agent
rm -rf ~/.config/opencode/agents/wordpress-reviewer

# Remove specific command
rm ~/.config/opencode/.opencode/commands/wp-theme.md
```

## Troubleshooting

### Common Issues

#### Skills Not Loading

```bash
# Check skills directory
ls -la ~/.config/opencode/skills/

# Verify SKILL.md files
find ~/.config/opencode/skills -name "SKILL.md"

# Check config path
opencode config get skillsDir
```

#### Commands Not Found

```bash
# Verify commands directory
ls -la ~/.config/opencode/.opencode/commands/

# Check file permissions
chmod 644 ~/.config/opencode/.opencode/commands/*.md

# Restart OpenCode
opencode restart
```

#### Hooks Not Running

```bash
# Check Node.js installation
node --version

# Install dependencies
cd ~/.config/opencode/hooks
npm install

# Test hook manually
node php-lint.js your-file.php
```

#### Permission Denied

```bash
# Fix permissions
chmod -R 755 ~/.config/opencode/skills
chmod -R 755 ~/.config/opencode/agents
chmod -R 755 ~/.config/opencode/rules
chmod -R 755 ~/.config/opencode/hooks
chmod 644 ~/.config/opencode/.opencode/commands/*.md
```

#### Configuration Invalid: Bad File Reference

If you see an error like:

```
Configuration is invalid: bad file reference: "{file:.opencode/prompts/agents/wordpress-reviewer.txt}"
/home/user/.opencode/.opencode/prompts/agents/wordpress-reviewer.txt does not exist
```

This means the `opencode.json` file contains paths with the `.opencode/` prefix that create a double path after installation.

**Quick Fix:**

```bash
# Fix the paths in the installed opencode.json
sed -i 's|{file:\.opencode/|{file:|g' ~/.opencode/opencode.json

# Verify the fix
grep "{file:" ~/.opencode/opencode.json
```

**Expected output (paths without `.opencode/` prefix):**

```
"prompt": "{file:prompts/agents/wordpress-reviewer.txt}"
"template": "{file:commands/wp-theme.md}"
```

**Verify files exist:**

```bash
# Check that referenced files exist
ls -la ~/.opencode/prompts/agents/
ls -la ~/.opencode/commands/
```

### Debug Mode

Enable debug mode for troubleshooting:

```bash
# Enable verbose logging
export OPENCODE_DEBUG=true

# Check logs
tail -f ~/.config/opencode/logs/opencode.log
```

### Reset Configuration

Reset to default configuration:

```bash
# Reset OpenCode config
opencode config reset

# Reinstall OpenCode WordPress
./install.sh
```

## Platform-Specific Notes

### Linux

```bash
# Install dependencies
sudo apt-get install nodejs npm php-cli

# Set permissions
sudo chown -R $USER:$USER ~/.config/opencode
```

### macOS

```bash
# Install dependencies via Homebrew
brew install node php

# Set permissions
chmod -R 755 ~/.config/opencode
```

### Windows

```powershell
# Install dependencies
# Node.js: https://nodejs.org/
# PHP: https://windows.php.net/download/

# Run in PowerShell
Set-ExecutionPolicy Bypass -Scope Process
.\install.sh
```

## Next Steps

After installation:

1. **Read Usage Guide**: [USAGE.md](USAGE.md)
2. **Review Examples**: [examples/](../examples/)
3. **Configure Hooks**: Customize hooks for your workflow
4. **Start Developing**: Use skills and commands for WordPress development

## Support

For installation issues:

- **GitHub Issues**: https://github.com/promoweb/opencode-wordpress/issues
- **Documentation**: [docs/](../docs/)
- **Community**: Join discussions on GitHub

## License

GPL v2 or later