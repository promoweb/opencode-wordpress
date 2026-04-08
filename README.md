# OpenCode WordPress

OpenCode skills, agents, rules, and commands for professional WordPress development (themes, plugins, WooCommerce).

## About

A comprehensive toolkit for WordPress developers using OpenCode. This repository provides specialized skills, agents, rules, and commands designed to streamline WordPress development workflows. Whether you're building themes, plugins, or WooCommerce extensions, this toolkit helps you write cleaner, more secure, and more maintainable code following WordPress coding standards and best practices.

**Author**: Emilio Petrozzi - [mrtux.it](https://www.mrtux.it)

## Overview

This repository provides a comprehensive OpenCode configuration for WordPress development:

- **8 Skills**: WordPress theme, plugin, WooCommerce, security, REST API, testing, hooks/filters, database
- **4 Agents**: WordPress reviewer, build resolver, theme reviewer, plugin reviewer
- **6 Rules**: WordPress coding standards, hooks, patterns, security, testing, database
- **5 Commands**: `/wp-theme`, `/wp-plugin`, `/wp-review`, `/wp-build-fix`, `/wc-build`
- **3 Hooks**: PHP lint, WP debug check, security check

## Installation

### Prerequisites

- OpenCode CLI installed
- WordPress development environment

### Quick Install

```bash
./install.sh
```

### Manual Install

1. Copy `.opencode/` to your project root
2. Copy `skills/` to your project
3. Copy `rules/` to your project
4. Configure your OpenCode environment

## Structure

```
opencode-wordpress/
├── .opencode/
│   ├── opencode.json          # Main configuration
│   ├── commands/              # Slash commands
│   ├── instructions/          # Base instructions
│   ├── prompts/agents/        # Agent prompts
│   ├── plugins/               # Custom tools
│   └── tools/                 # Custom tool implementations
├── skills/
│   ├── wordpress-theme-development/
│   ├── wordpress-plugin-development/
│   ├── woocommerce-patterns/
│   ├── wordpress-security/
│   ├── wordpress-rest-api/
│   ├── wordpress-testing/
│   ├── wordpress-hooks-filters/
│   └── wordpress-database/
├── rules/
│   ├── wordpress/             # WordPress-specific rules
│   └── common/                # Base rules (language-agnostic)
├── agents/
│   ├── wordpress-reviewer.md
│   ├── wordpress-build-resolver.md
│   ├── theme-reviewer.md
│   └── plugin-reviewer.md
├── hooks/
│   ├── php-lint.js
│   ├── wp-debug-check.js
│   └── security-check.js
├── examples/
│   ├── theme-example/
│   ├── plugin-example/
│   └── woocommerce-example/
└── docs/
    ├── README.md
    ├── INSTALLATION.md
    ├── USAGE.md
    └── MIGRATION.md
```

## Skills

### Core Skills

1. **wordpress-theme-development**: Theme hierarchy, templates, functions.php, hooks, Customizer, widgets
2. **wordpress-plugin-development**: Plugin structure, hooks, Settings API, meta boxes, shortcodes, CPT
3. **woocommerce-patterns**: WooCommerce hooks, CRUD, orders, cart, payments, REST API
4. **wordpress-security**: Sanitization, escaping, nonces, CSRF, XSS, SQL injection prevention

### Additional Skills

5. **wordpress-rest-api**: Endpoint registration, authentication, permissions, responses, schema
6. **wordpress-testing**: PHPUnit, WP_Mock, BrainMonkey, integration tests, CI integration
7. **wordpress-hooks-filters**: Actions vs filters, priorities, OOP callbacks, debugging
8. **wordpress-database**: wpdb, Options API, custom tables, meta operations, query optimization

## Agents

- **wordpress-reviewer**: Comprehensive WordPress code review (WPCS, hooks, security, performance)
- **wordpress-build-resolver**: Build/debug error resolution (PHP errors, deprecation notices, conflicts)
- **theme-reviewer**: Theme-specific review (hierarchy, templates, Customizer, accessibility)
- **plugin-reviewer**: Plugin-specific review (structure, hooks, Settings API, database)

## Commands

- `/wp-theme`: Start theme development workflow
- `/wp-plugin`: Start plugin development workflow
- `/wp-review`: Comprehensive WordPress code review
- `/wp-build-fix`: Fix WordPress build/debug errors
- `/wc-build`: WooCommerce-specific development/review

## Rules

WordPress-specific rules extend common rules:

- **coding-style**: WordPress Coding Standards (WPCS), naming conventions, file organization
- **hooks**: Action vs filter conventions, priorities, naming with prefixes
- **patterns**: Singleton, Factory, Repository patterns adapted for WordPress
- **security**: Sanitization, escaping, nonce, capability checks, SQL safety
- **testing**: PHPUnit configuration, coverage requirements, mock patterns
- **database**: wpdb usage, prepare statements, Options API, query optimization

## Usage Examples

### Theme Development

```bash
opencode /wp-theme "Create a responsive blog theme with Customizer options"
```

### Plugin Development

```bash
opencode /wp-plugin "Create a contact form plugin with Settings API"
```

### WooCommerce Extension

```bash
opencode /wc-build "Add custom checkout field for WooCommerce"
```

### Code Review

```bash
opencode /wp-review "Review my-theme/functions.php for security issues"
```

### Fix Build Errors

```bash
opencode /wp-build-fix "Fix PHP deprecation notices in my-plugin"
```

## Documentation

- [INSTALLATION.md](docs/INSTALLATION.md) - Detailed installation guide
- [USAGE.md](docs/USAGE.md) - Usage examples and best practices
- [MIGRATION.md](docs/MIGRATION.md) - Migration from manual WordPress development

## Contributing

Contributions welcome! Please read the contributing guidelines.

## License

MIT License

## Credits

Inspired by and adapted from [everything-claude-code](https://github.com/affaan-m/everything-claude-code) for WordPress-specific development.