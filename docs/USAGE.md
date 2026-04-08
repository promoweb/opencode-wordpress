# Usage Guide

Complete guide to using OpenCode WordPress skills, agents, commands, and hooks.

## Quick Start

### First Time Setup

After installation, verify everything works:

```bash
# Check available skills
opencode skills list

# Test a skill
opencode skill wordpress-theme-development
```

### Basic Workflow

1. **Start OpenCode session** in your WordPress project directory
2. **Invoke a skill** for specific task
3. **Use commands** for quick actions
4. **Let agents review** your code
5. **Hooks validate** on commit

## Skills Usage

### List Available Skills

```bash
# List all skills
opencode skills list

# List WordPress skills only
opencode skills list --filter wordpress

# Show skill details
opencode skill wordpress-theme-development --info
```

### Invoke Skills

Skills provide structured guidance for WordPress development.

#### Theme Development

```bash
# Invoke theme development skill
opencode skill wordpress-theme-development

# Or use slash command
/wp-theme
```

Skill provides:
- Theme structure guidance
- Template hierarchy patterns
- Enqueue scripts/styles best practices
- Widget area setup
- Customizer integration

#### Plugin Development

```bash
# Invoke plugin development skill
opencode skill wordpress-plugin-development

# Or use slash command
/wp-plugin
```

Skill provides:
- Plugin structure guidance
- Hooks and filters patterns
- Settings API implementation
- Custom post type registration
- Security best practices

#### WooCommerce Patterns

```bash
# Invoke WooCommerce skill
opencode skill woocommerce-patterns

# Or use slash command
/wc-build
```

Skill provides:
- WooCommerce extension structure
- Custom product types
- Payment gateway integration
- Order handling patterns
- WooCommerce hooks reference

#### Security Best Practices

```bash
# Invoke security skill
opencode skill wordpress-security
```

Skill provides:
- Security audit checklist
- Escaping and sanitization rules
- Nonce verification patterns
- Capability check templates
- SQL injection prevention

#### REST API Development

```bash
# Invoke REST API skill
opencode skill wordpress-rest-api
```

Skill provides:
- REST API endpoint registration
- Authentication patterns
- Permission callbacks
- Response formatting
- Error handling

#### Testing

```bash
# Invoke testing skill
opencode skill wordpress-testing
```

Skill provides:
- PHPUnit setup guidance
- Test writing patterns
- Mock object usage
- Integration testing
- Codeception configuration

#### Hooks and Filters

```bash
# Invoke hooks skill
opencode skill wordpress-hooks-filters
```

Skill provides:
- Action vs filter usage
- Hook priority management
- Custom hook creation
- Hook removal patterns
- Debugging hooks

#### Database Operations

```bash
# Invoke database skill
opencode skill wordpress-database
```

Skill provides:
- $wpdb usage patterns
- Custom table creation
- Query optimization
- Transaction handling
- Cache integration

## Commands Usage

Commands provide quick actions via slash syntax.

### Theme Commands

```bash
# Create new theme
/wp-theme create my-theme

# Review theme
/wp-theme review

# Validate theme
/wp-theme validate

# Build theme assets
/wp-theme build
```

### Plugin Commands

```bash
# Create new plugin
/wp-plugin create my-plugin

# Review plugin
/wp-plugin review

# Validate plugin
/wp-plugin validate

# Build plugin
/wp-plugin build
```

### WooCommerce Commands

```bash
# Create WooCommerce extension
/wc-build create my-extension

# Review WooCommerce code
/wc-build review

# Validate WooCommerce integration
/wc-build validate
```

### Review Commands

```bash
# Full WordPress code review
/wp-review

# Review specific file
/wp-review path/to/file.php

# Review with fixes
/wp-review --fix
```

### Build Fix Commands

```bash
# Fix build issues
/wp-build-fix

# Fix specific issue type
/wp-build-fix --type=php-lint

# Auto-fix all issues
/wp-build-fix --auto
```

## Agents Usage

Agents automatically review and improve code.

### WordPress Reviewer Agent

Automatically activates when WordPress code is detected:

```php
// Agent reviews:
- Coding standards compliance
- Security vulnerabilities
- Performance issues
- Hook/filter usage
- Documentation quality
```

Usage:
```bash
# Agent activates automatically
# Or explicitly invoke:
opencode agent wordpress-reviewer
```

### Theme Reviewer Agent

Specialized for theme review:

```bash
# Invoke theme reviewer
opencode agent theme-reviewer

# Review specific theme
opencode agent theme-reviewer --theme=my-theme
```

Checks:
- Template hierarchy
- Required files presence
- Theme supports
- Widget registration
- Customizer implementation
- Accessibility compliance

### Plugin Reviewer Agent

Specialized for plugin review:

```bash
# Invoke plugin reviewer
opencode agent plugin-reviewer

# Review specific plugin
opencode agent plugin-reviewer --plugin=my-plugin
```

Checks:
- Plugin header compliance
- Activation/deactivation hooks
- Uninstall handling
- Settings API usage
- Custom post type registration
- Security practices

### Build Resolver Agent

Resolves build and compilation issues:

```bash
# Invoke build resolver
opencode agent wordpress-build-resolver

# Resolve specific error
opencode agent wordpress-build-resolver --error="syntax error"
```

Resolves:
- PHP syntax errors
- Composer dependency issues
- npm build errors
- Asset compilation failures
- WordPress coding standard violations

## Rules Enforcement

Rules are automatically enforced during development.

### Common Rules

#### Git Workflow

Enforces:
- Branch naming conventions
- Commit message format
- Merge strategies
- Pull request guidelines

#### Code Style

Enforces:
- PSR-12 formatting
- WordPress coding standards
- Documentation comments
- Code organization

#### Documentation

Enforces:
- README requirements
- Inline documentation
- Function/method documentation
- License headers

#### Security

Enforces:
- No hardcoded secrets
- Proper escaping
- Input validation
- Output sanitization

### WordPress Rules

#### WordPress Coding Style

Enforces WordPress Coding Standards:
- Yoda conditions
- Spacing rules
- Naming conventions
- Braces placement

#### Hooks Usage

Enforces:
- Proper hook usage
- Action vs filter distinction
- Hook naming
- Priority management

#### WordPress Patterns

Enforces:
- Template hierarchy
- Loop patterns
- Template tag usage
- Conditional tags

#### WordPress Security

Enforces:
- Nonce verification
- Capability checks
- Data escaping
- SQL preparation

#### WordPress Testing

Enforces:
- Test coverage requirements
- PHPUnit configuration
- Mock usage
- Test naming

#### WordPress Database

Enforces:
- $wpdb usage
- Query preparation
- Table creation
- Transaction handling

## Hooks Usage

Pre-commit hooks validate code before commit.

### PHP Lint Hook

Validates PHP syntax:

```bash
# Automatic on commit
# Manual test:
node ~/.config/opencode/hooks/php-lint.js file.php
```

Checks:
- Syntax validity
- Parse errors
- Deprecated functions
- PHP version compatibility

### WordPress Debug Check

Checks for debug code:

```bash
# Automatic on commit
# Manual test:
node ~/.config/opencode/hooks/wp-debug-check.js file.php
```

Prevents:
- var_dump() in code
- print_r() in code
- debug output
- Console logs in PHP

### Security Check

Validates security practices:

```bash
# Automatic on commit
# Manual test:
node ~/.config/opencode/hooks/security-check.js file.php
```

Checks:
- SQL injection risks
- XSS vulnerabilities
- CSRF protection
- Authentication bypass

### Hook Configuration

Customize hook behavior:

```javascript
// hooks/config.json
{
  "php-lint": {
    "enabled": true,
    "phpVersion": "8.0",
    "exclude": ["vendor/", "node_modules/"]
  },
  "wp-debug-check": {
    "enabled": true,
    "strict": true,
    "allowed": ["debug-log"]
  },
  "security-check": {
    "enabled": true,
    "level": "strict",
    "ignore": ["tests/"]
  }
}
```

### Bypass Hooks

Temporarily bypass hooks:

```bash
# Bypass all hooks
git commit --no-verify

# Bypass specific hook
SKIP_PHP_LINT=true git commit
```

## Integration Examples

### Theme Development Workflow

```bash
# 1. Start OpenCode in theme directory
cd wp-content/themes/my-theme
opencode

# 2. Invoke theme skill
/wp-theme

# 3. Create theme structure
opencode: "Create theme structure following skill guidance"

# 4. Use commands for quick actions
/wp-theme validate

# 5. Let agent review code
opencode agent theme-reviewer

# 6. Commit with hook validation
git commit -m "Add theme functionality"
```

### Plugin Development Workflow

```bash
# 1. Start OpenCode in plugin directory
cd wp-content/plugins/my-plugin
opencode

# 2. Invoke plugin skill
/wp-plugin

# 3. Create plugin skeleton
opencode: "Create plugin following WordPress plugin patterns"

# 4. Validate plugin structure
/wp-plugin validate

# 5. Review with agent
opencode agent plugin-reviewer

# 6. Fix any issues
/wp-build-fix

# 7. Commit safely
git commit -m "Initial plugin structure"
```

### WooCommerce Extension Workflow

```bash
# 1. Start in WooCommerce extension directory
cd wp-content/plugins/my-wc-extension
opencode

# 2. Invoke WooCommerce skill
/wc-build

# 3. Create extension structure
opencode: "Create WooCommerce extension with custom product type"

# 4. Validate WooCommerce integration
/wc-build validate

# 5. Review WooCommerce code
opencode agent plugin-reviewer --woocommerce

# 6. Commit with validation
git commit -m "Add custom product type"
```

### Code Review Workflow

```bash
# 1. Open existing WordPress project
cd wp-content/themes/client-theme
opencode

# 2. Full code review
/wp-review

# 3. Apply fixes
/wp-review --fix

# 4. Specific agent review
opencode agent theme-reviewer

# 5. Fix build issues
/wp-build-fix --auto

# 6. Validate fixes
/wp-theme validate

# 7. Commit improvements
git commit -m "Apply code review fixes"
```

## Advanced Usage

### Custom Skill Invocation

Invoke skills with parameters:

```bash
# Theme skill with specific template
opencode skill wordpress-theme-development --template=single

# Plugin skill with post type
opencode skill wordpress-plugin-development --cpt=product

# WooCommerce skill with gateway
opencode skill woocommerce-patterns --gateway=stripe
```

### Agent Configuration

Configure agent behavior:

```json
{
  "agents": {
    "wordpress-reviewer": {
      "strict": true,
      "autoFix": false,
      "reportFormat": "markdown"
    },
    "theme-reviewer": {
      "checkAccessibility": true,
      "checkPerformance": true
    }
  }
}
```

### Batch Operations

Process multiple files:

```bash
# Review all theme files
/wp-review themes/**/*.php

# Validate all plugins
/wp-plugin validate plugins/*/

# Fix all build issues
/wp-build-fix --recursive
```

### CI/CD Integration

Use in CI pipelines:

```yaml
# .gitlab-ci.yml
wordpress-review:
  script:
    - opencode skills list
    - opencode agent wordpress-reviewer
    - node hooks/php-lint.js
    - node hooks/security-check.js
  only:
    - merge_requests
```

### VS Code Integration

Integrate with VS Code:

```json
// .vscode/settings.json
{
  "opencode.skills": [
    "wordpress-theme-development",
    "wordpress-plugin-development"
  ],
  "opencode.autoReview": true,
  "opencode.hooks": true
}
```

## Best Practices

### Skill Usage

1. **Invoke skill first** before starting development
2. **Follow skill guidance** for structure and patterns
3. **Use skill examples** as templates
4. **Review skill updates** regularly

### Command Usage

1. **Use commands for quick actions**
2. **Validate before commit**
3. **Fix issues promptly**
4. **Don't bypass validation**

### Agent Usage

1. **Let agents review automatically**
2. **Address agent findings**
3. **Run agents before commit**
4. **Use specialized agents for specific code types**

### Hook Usage

1. **Never disable hooks permanently**
2. **Fix hook errors immediately**
3. **Test hooks manually occasionally**
4. **Update hook configuration as needed**

### Rules Compliance

1. **Understand rule requirements**
2. **Follow rules consistently**
3. **Don't suppress rule warnings**
4. **Update rules for team standards**

## Troubleshooting

### Skills Not Loading

```bash
# Check skill directory
ls ~/.config/opencode/skills/

# Verify SKILL.md exists
find ~/.config/opencode/skills -name "SKILL.md"

# Check configuration
opencode config get skillsDir
```

### Commands Not Working

```bash
# Verify command files
ls ~/.config/opencode/.opencode/commands/

# Check command syntax
cat ~/.config/opencode/.opencode/commands/wp-theme.md

# Restart OpenCode
opencode restart
```

### Agents Not Activating

```bash
# Check agent files
ls ~/.config/opencode/agents/

# Invoke agent explicitly
opencode agent wordpress-reviewer

# Check agent config
opencode config get agentsDir
```

### Hooks Failing

```bash
# Check Node.js version
node --version

# Install dependencies
cd ~/.config/opencode/hooks && npm install

# Test hook manually
node php-lint.js test.php

# Check hook logs
cat ~/.config/opencode/hooks/error.log
```

## Tips and Tricks

### Quick Actions

```bash
# Quick theme creation
/wp-theme create --scaffold

# Quick plugin creation
/wp-plugin create --boilerplate

# Quick review
/wp-review --quick
```

### Keyboard Shortcuts

- `Ctrl+Space`: Invoke skill menu
- `Ctrl+R`: Run review
- `Ctrl+B`: Build/compile
- `Ctrl+H`: Show hooks status

### Aliases

Create aliases for common operations:

```bash
# Add to .bashrc or .zshrc
alias wpt='opencode skill wordpress-theme-development'
alias wpp='opencode skill wordpress-plugin-development'
alias wc='opencode skill woocommerce-patterns'
alias wr='/wp-review'
alias wf='/wp-build-fix'
```

### Templates

Use templates from examples:

```bash
# Copy theme example
cp -r examples/theme-example wp-content/themes/my-theme

# Copy plugin example
cp -r examples/plugin-example wp-content/plugins/my-plugin

# Copy WooCommerce example
cp -r examples/woocommerce-example wp-content/plugins/my-wc-ext
```

## Next Steps

1. **Explore Examples**: [../examples/](../examples/)
2. **Customize Rules**: Edit rules to match your team standards
3. **Extend Hooks**: Add custom hooks for specific validations
4. **Create Custom Skills**: Add skills for your specific workflows

## Support

For usage questions:
- **GitHub Issues**: https://github.com/promoweb/opencode-wordpress/issues
- **Discussions**: https://github.com/promoweb/opencode-wordpress/discussions
- **Documentation**: All docs in [docs/](.) folder

## License

GPL v2 or later