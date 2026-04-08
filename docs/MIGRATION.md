# Migration Guide

Guide to migrate existing WordPress development workflow to OpenCode WordPress.

## Overview

OpenCode WordPress provides structured skills, agents, commands, and hooks for WordPress development. This guide helps migrate from:

- Manual WordPress development
- Other AI-assisted tools
- Existing OpenCode configurations
- Traditional WordPress workflows

## Migration Scenarios

### Scenario 1: Manual WordPress Development

Migrate from manual coding to OpenCode WordPress.

#### Before (Manual)

```
- No structured guidance
- Inconsistent patterns
- Manual code review
- No automated validation
- Ad-hoc security checks
```

#### After (OpenCode WordPress)

```
- Structured skills guidance
- Consistent patterns from skills
- Automated agent review
- Hook validation on commit
- Security enforcement via rules
```

#### Migration Steps

1. **Install OpenCode WordPress**

```bash
git clone https://github.com/yourusername/opencode-wordpress.git
cd opencode-wordpress
./install.sh
```

2. **Open Existing Project**

```bash
cd your-wordpress-project
opencode
```

3. **Review Current Code**

```bash
# Full review
/wp-review

# Theme review
opencode agent theme-reviewer

# Plugin review
opencode agent plugin-reviewer
```

4. **Apply Fixes**

```bash
# Auto-fix issues
/wp-build-fix --auto

# Manual fix with guidance
opencode skill wordpress-security
# Follow skill recommendations
```

5. **Enforce Standards**

```bash
# Commit with validation
git commit -m "Apply OpenCode WordPress standards"

# Hooks will validate
```

### Scenario 2: From Claude Code or Other AI Tools

Migrate from other AI-assisted development tools.

#### Differences

| Feature | Other AI Tools | OpenCode WordPress |
|---------|---------------|-------------------|
| Guidance | Generic advice | Specialized WordPress skills |
| Review | Manual prompts | Automated agents |
| Validation | None | Pre-commit hooks |
| Standards | Generic coding | WordPress-specific rules |
| Examples | Limited | Complete examples |

#### Migration Steps

1. **Install OpenCode WordPress** (same as above)

2. **Replace Generic Patterns with WordPress Patterns**

```bash
# Invoke WordPress skill
opencode skill wordpress-plugin-development

# Use WordPress-specific guidance instead of generic advice
```

3. **Use WordPress-Specific Agents**

```bash
# Instead of generic review
opencode agent wordpress-reviewer

# Theme-specific
opencode agent theme-reviewer

# Plugin-specific
opencode agent plugin-reviewer
```

4. **Enable WordPress Hooks**

```bash
# Hooks enforce WordPress standards
npm install ~/.config/opencode/hooks
```

5. **Follow WordPress Rules**

Rules automatically enforce WordPress coding standards, security, and patterns.

### Scenario 3: From Existing OpenCode Config

Migrate existing OpenCode configuration to include WordPress components.

#### Backup Existing Config

```bash
# Backup current configuration
cp -r ~/.config/opencode ~/.config/opencode.backup
```

#### Merge Configurations

Option A: Full merge (WordPress + existing)

```bash
# Install WordPress components
cd opencode-wordpress
./install.sh --merge
```

Option B: Partial merge (selective)

```bash
# Copy specific components
cp -r skills/* ~/.config/opencode/skills/
cp -r agents/* ~/.config/opencode/agents/
cp -r rules/wordpress ~/.config/opencode/rules/

# Keep existing commands/hooks or merge
```

#### Update Configuration

```json
// ~/.config/opencode/.opencode/opencode.json
{
  "existingConfig": "...",
  "skillsDir": "~/.config/opencode/skills",
  "agentsDir": "~/.config/opencode/agents",
  "rulesDir": "~/.config/opencode/rules",
  "commandsDir": "~/.config/opencode/.opencode/commands",
  "hooksDir": "~/.config/opencode/hooks",
  "wordpress": {
    "enabled": true
  }
}
```

#### Test Migration

```bash
# Test skills load
opencode skills list

# Test WordPress skill
opencode skill wordpress-theme-development

# Test agent
opencode agent wordpress-reviewer
```

### Scenario 4: Team Migration

Migrate entire team to OpenCode WordPress.

#### Team Setup

1. **Create Team Repository**

```bash
# Clone OpenCode WordPress
git clone https://github.com/yourusername/opencode-wordpress.git

# Add to team's tooling repository
cd team-tools
git submodule add https://github.com/yourusername/opencode-wordpress.git
```

2. **Create Team Installation Script**

```bash
# team-install.sh
#!/bin/bash

# Team-specific installation
cd opencode-wordpress
./install.sh --team-config=/path/to/team/config.json
```

3. **Document Team Workflow**

```markdown
# Team WordPress Development Workflow

1. All members install OpenCode WordPress
2. Use approved skills for development
3. Mandatory agent review before commit
4. Hooks validate all commits
5. Follow team rules configuration
```

4. **Train Team Members**

- Workshop on OpenCode WordPress usage
- Skill invocation practices
- Agent interpretation
- Hook understanding
- Rules compliance

#### Team Configuration

```json
{
  "team": {
    "name": "Development Team",
    "standards": "WordPress",
    "strictness": "high"
  },
  "wordpress": {
    "version": "6.0",
    "phpVersion": "8.0",
    "codingStandards": "WordPress-Strict",
    "securityLevel": "strict",
    "enableTesting": true,
    "minTestCoverage": 80
  },
  "hooks": {
    "php-lint": true,
    "wp-debug-check": true,
    "security-check": true,
    "preventBypass": true
  }
}
```

#### Team Enforcement

- Configure hooks to prevent bypassing
- Mandatory agent review in CI/CD
- Team rules in centralized config
- Regular skill updates via submodule

## Component Migration

### Skills Migration

#### Replace Generic Skills

If you have generic skills, replace with WordPress-specific:

```bash
# Remove generic skill
rm ~/.config/opencode/skills/generic-web-development

# Add WordPress skill
cp skills/wordpress-theme-development ~/.config/opencode/skills/
```

#### Skill Priority

WordPress skills should have higher priority:

```json
{
  "skillPriority": [
    "wordpress-theme-development",
    "wordpress-plugin-development",
    "woocommerce-patterns",
    "wordpress-security",
    "wordpress-rest-api",
    "wordpress-testing",
    "wordpress-hooks-filters",
    "wordpress-database"
  ]
}
```

### Agents Migration

#### Replace Generic Agents

```bash
# Remove generic reviewer
rm ~/.config/opencode/agents/general-reviewer

# Add WordPress reviewers
cp agents/wordpress-reviewer.md ~/.config/opencode/agents/
cp agents/theme-reviewer.md ~/.config/opencode/agents/
cp agents/plugin-reviewer.md ~/.config/opencode/agents/
```

#### Agent Activation

WordPress agents should activate for WordPress code:

```json
{
  "agentActivation": {
    "wordpress-reviewer": {
      "triggers": ["*.php", "wp-content/**/*"],
      "priority": 10
    },
    "theme-reviewer": {
      "triggers": ["wp-content/themes/**/*"],
      "priority": 11
    },
    "plugin-reviewer": {
      "triggers": ["wp-content/plugins/**/*"],
      "priority": 11
    }
  }
}
```

### Rules Migration

#### Add WordPress Rules

```bash
# Keep common rules
# Add WordPress rules
mkdir -p ~/.config/opencode/rules/wordpress
cp rules/wordpress/* ~/.config/opencode/rules/wordpress/
```

#### Rules Priority

WordPress rules apply after common rules:

```json
{
  "rulePriority": [
    "common/git-workflow",
    "common/code-style",
    "common/documentation",
    "common/security",
    "wordpress/coding-style",
    "wordpress/hooks",
    "wordpress/patterns",
    "wordpress/security",
    "wordpress/testing",
    "wordpress/database"
  ]
}
```

### Commands Migration

#### Add WordPress Commands

```bash
# Add WordPress commands to existing
cp .opencode/commands/wp-*.md ~/.config/opencode/.opencode/commands/
cp .opencode/commands/wc-*.md ~/.config/opencode/.opencode/commands/
```

#### Command Aliases

Create aliases for WordPress commands:

```bash
# Add to .bashrc/.zshrc
alias wp-theme='/wp-theme'
alias wp-plugin='/wp-plugin'
alias wp-review='/wp-review'
alias wp-build='/wp-build-fix'
alias wc='/wc-build'
```

### Hooks Migration

#### Add WordPress Hooks

```bash
# Add WordPress hooks
mkdir -p ~/.config/opencode/hooks
cp hooks/php-lint.js ~/.config/opencode/hooks/
cp hooks/wp-debug-check.js ~/.config/opencode/hooks/
cp hooks/security-check.js ~/.config/opencode/hooks/

# Install dependencies
cd ~/.config/opencode/hooks
npm install
```

#### Hook Priority

WordPress hooks run before generic hooks:

```json
{
  "hookPriority": [
    "php-lint.js",
    "wp-debug-check.js",
    "security-check.js",
    "other-hooks..."
  ]
}
```

## Code Migration Patterns

### Theme Code Migration

#### Old Pattern (Manual)

```php
// Inconsistent enqueue
wp_enqueue_style('style', get_stylesheet_uri());
wp_enqueue_script('script', get_template_directory_uri() . '/js/script.js');
```

#### New Pattern (OpenCode WordPress)

```php
// Follow skill guidance
function my_theme_scripts() {
    wp_enqueue_style(
        'my-theme-style',
        get_template_directory_uri() . '/assets/css/style.css',
        array(),
        MY_THEME_VERSION
    );
    
    wp_enqueue_script(
        'my-theme-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array('jquery'),
        MY_THEME_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');
```

Migration steps:
1. Invoke skill: `opencode skill wordpress-theme-development`
2. Review enqueue patterns in skill
3. Update theme functions.php
4. Run validation: `/wp-theme validate`

### Plugin Code Migration

#### Old Pattern (Manual)

```php
// Missing security
function my_plugin_save_data() {
    global $wpdb;
    $wpdb->insert('my_table', array(
        'data' => $_POST['data']
    ));
}
```

#### New Pattern (OpenCode WordPress)

```php
// Security enforced by rules
function my_plugin_save_data() {
    // Verify nonce
    if (!isset($_POST['my_plugin_nonce']) || 
        !wp_verify_nonce($_POST['my_plugin_nonce'], 'my_plugin_action')) {
        wp_die('Security check failed');
    }
    
    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Sanitize input
    $data = sanitize_text_field($_POST['data']);
    
    // Prepared query
    global $wpdb;
    $wpdb->insert(
        'my_table',
        array('data' => $data),
        array('%s')
    );
}
```

Migration steps:
1. Invoke skill: `opencode skill wordpress-plugin-development`
2. Invoke security skill: `opencode skill wordpress-security`
3. Review security patterns
4. Update plugin code
5. Run security check: `node security-check.js plugin-file.php`

### WooCommerce Code Migration

#### Old Pattern (Manual)

```php
// Direct WooCommerce manipulation
add_action('woocommerce_before_calculate_totals', function($cart) {
    foreach ($cart->get_cart() as $item) {
        $item['data']->set_price(10);
    }
});
```

#### New Pattern (OpenCode WordPress)

```php
// Proper WooCommerce integration
add_action('woocommerce_before_calculate_totals', 'my_wc_custom_price');

function my_wc_custom_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        
        // Check product type
        if ($product->get_type() === 'my_custom_type') {
            // Get custom price from meta
            $custom_price = get_post_meta(
                $product->get_id(),
                '_custom_price',
                true
            );
            
            if ($custom_price) {
                $product->set_price($custom_price);
            }
        }
    }
}
```

Migration steps:
1. Invoke skill: `opencode skill woocommerce-patterns`
2. Review WooCommerce hook patterns
3. Update WooCommerce integration
4. Run validation: `/wc-build validate`

## Testing Migration

### Validate Migration

After migration, validate all components work:

```bash
# Test skills
opencode skills list --all

# Test agents
opencode agents list --all

# Test commands
opencode commands list --all

# Test hooks
cd ~/.config/opencode/hooks
npm test

# Test rules
opencode rules validate
```

### Review Migrated Code

```bash
# Full review
/wp-review --all

# Theme review
opencode agent theme-reviewer

# Plugin review
opencode agent plugin-reviewer

# WooCommerce review
/wc-build review
```

### Fix Migration Issues

```bash
# Auto-fix
/wp-build-fix --auto

# Manual fix with skill
opencode skill wordpress-security
# Follow guidance
```

## Common Migration Issues

### Skills Not Loading

```bash
# Check SKILL.md files
find ~/.config/opencode/skills -name "SKILL.md"

# Check skill directory in config
opencode config get skillsDir

# Restart OpenCode
opencode restart
```

### Agents Not Activating

```bash
# Check agent triggers
opencode config get agentActivation

# Invoke explicitly
opencode agent wordpress-reviewer

# Check WordPress file detection
opencode detect wordpress
```

### Rules Conflicts

```bash
# Check rule priority
opencode config get rulePriority

# Disable conflicting rule
opencode rules disable generic-security

# Enable WordPress rule
opencode rules enable wordpress/security
```

### Hooks Failures

```bash
# Check Node.js version
node --version

# Reinstall dependencies
cd ~/.config/opencode/hooks && npm install

# Test hook manually
node php-lint.js test.php
```

## Best Practices

### Migration Checklist

- [ ] Backup existing configuration
- [ ] Install OpenCode WordPress
- [ ] Verify all components installed
- [ ] Test skills invocation
- [ ] Test agents activation
- [ ] Test commands execution
- [ ] Test hooks validation
- [ ] Review existing WordPress code
- [ ] Apply OpenCode WordPress patterns
- [ ] Fix issues with skills guidance
- [ ] Validate all code
- [ ] Commit with hook validation

### Incremental Migration

Don't migrate everything at once:

1. **Week 1**: Install and configure
2. **Week 2**: Migrate one theme
3. **Week 3**: Migrate one plugin
4. **Week 4**: Migrate WooCommerce code
5. **Week 5**: Team adoption
6. **Week 6**: Full workflow integration

### Team Communication

- Document migration process
- Train team on new workflow
- Provide support during transition
- Regular feedback collection
- Iterate based on team needs

## Support

For migration issues:
- **GitHub Issues**: https://github.com/yourusername/opencode-wordpress/issues
- **Migration Discussions**: https://github.com/yourusername/opencode-wordpress/discussions/categories/migration
- **Documentation**: [docs/](.) folder

## License

GPL v2 or later