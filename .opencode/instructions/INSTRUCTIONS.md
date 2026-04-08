# OpenCode WordPress - Instructions

This configuration provides comprehensive WordPress development support through skills, agents, rules, and commands.

## Available Commands

### WordPress Development
- `/wp-theme` - Theme development workflow
- `/wp-plugin` - Plugin development workflow
- `/wp-review` - Comprehensive code review
- `/wp-build-fix` - Debug and error resolution
- `/wc-build` - WooCommerce development workflow

## Available Agents

### Code Review
- `wordpress-reviewer` - Full WordPress code review (security, performance, standards)
- `theme-reviewer` - Theme-specific review (hierarchy, Customizer, accessibility)
- `plugin-reviewer` - Plugin-specific review (structure, hooks, APIs)

### Debugging
- `wordpress-build-resolver` - Debug and fix WordPress errors

## Available Skills

### Core Skills
1. **wordpress-theme-development** - Theme hierarchy, templates, functions.php, Customizer, widgets
2. **wordpress-plugin-development** - Plugin structure, Settings API, CPT, meta boxes, shortcodes
3. **woocommerce-patterns** - Products, orders, cart, payment gateways, shipping
4. **wordpress-security** - Sanitization, escaping, nonces, capability checks

### Additional Skills
5. **wordpress-rest-api** - Endpoint registration, authentication, schemas
6. **wordpress-testing** - PHPUnit, WP_Mock, integration tests, TDD
7. **wordpress-hooks-filters** - Actions, filters, priorities, custom hooks
8. **wordpress-database** - wpdb, prepared statements, Options API, custom tables

## Rules

### WordPress-Specific Rules
- `coding-style.md` - WordPress Coding Standards (WPCS)
- `hooks.md` - Hook patterns and best practices
- `patterns.md` - Design patterns for WordPress
- `security.md` - Security best practices
- `testing.md` - Testing requirements and patterns
- `database.md` - Database operations and security

### Common Rules (Base)
- `coding-style.md` - Language-agnostic coding principles
- `testing.md` - General testing standards
- `security.md` - General security principles
- `patterns.md` - Common design patterns

## Hooks

Automated checks on file operations:
- `php-lint.js` - PHP syntax validation
- `wp-debug-check.js` - Debug code detection
- `security-check.js` - Security vulnerability scanning

## Quick Start

### Theme Development
```
/wp-theme "Create a responsive blog theme with Customizer options"
```

### Plugin Development
```
/wp-plugin "Create a contact form plugin with Settings API"
```

### Code Review
```
/wp-review path/to/plugin/or/theme
```

### WooCommerce Extension
```
/wc-build "Add custom checkout field"
```

### Fix Errors
```
/wp-build-fix "Fix PHP deprecation notices"
```

## Configuration

This configuration uses:
- **Primary Model**: Claude Sonnet 4.5
- **Small Model**: Claude Haiku 4.5
- **Default Agent**: build

## Best Practices

1. **Security First**: Always sanitize input, escape output, verify nonces
2. **Use WordPress APIs**: Leverage built-in functions and hooks
3. **Follow WPCS**: Adhere to WordPress Coding Standards
4. **Test Your Code**: Write unit tests with PHPUnit, aim for 70%+ coverage
5. **Document Code**: Use DocBlocks for all functions and classes
6. **Optimize Performance**: Cache expensive operations, optimize queries
7. **Internationalize**: Make all strings translatable

## Resources

- WordPress Developer Documentation: https://developer.wordpress.org/
- WordPress Coding Standards: https://developer.wordpress.org/coding-standards/
- WooCommerce Documentation: https://developer.woocommerce.com/
- WordPress Security Handbook: https://developer.wordpress.org/apis/security/

## Support

For issues or questions:
- Review the detailed skills documentation
- Check WordPress official documentation
- Use the specialized agents for context-specific help