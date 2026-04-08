# Contributing Guide

Guide for contributing to OpenCode WordPress repository.

## Overview

OpenCode WordPress is an open-source repository providing skills, agents, rules, commands, and hooks for WordPress development with OpenCode. We welcome contributions from the community.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Types of Contributions](#types-of-contributions)
- [Development Setup](#development-setup)
- [Contribution Process](#contribution-process)
- [Standards](#standards)
- [Testing](#testing)
- [Documentation](#documentation)
- [Pull Request Guidelines](#pull-request-guidelines)
- [Issue Guidelines](#issue-guidelines)

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all.

### Our Standards

- Be respectful and inclusive
- Welcome diverse viewpoints
- Accept constructive criticism
- Focus on what's best for the community
- Show empathy towards others

### Enforcement

Violations of the code of conduct may result in:
- Warning
- Temporary ban
- Permanent ban

Report violations to: conduct@example.com

## Getting Started

### Prerequisites

- Git 2.0+
- Node.js 14+ (for hooks)
- PHP 7.4+ (for examples)
- WordPress 5.8+ (for testing)
- OpenCode latest version

### Fork and Clone

```bash
# Fork repository on GitHub
# Clone your fork
git clone https://github.com/YOUR_USERNAME/opencode-wordpress.git

# Navigate to repository
cd opencode-wordpress

# Add upstream remote
git remote add upstream https://github.com/original/opencode-wordpress.git
```

### Install Dependencies

```bash
# Install hook dependencies
cd hooks
npm install

# Install example dependencies (if testing)
cd examples/theme-example
npm install # if package.json exists
```

## Types of Contributions

### Skills

Add new WordPress skills or improve existing:

- **New Skill**: Add skill for specific WordPress domain
- **Skill Enhancement**: Improve existing skill guidance
- **Skill Examples**: Add more examples to skills
- **Skill Updates**: Update for WordPress version changes

### Agents

Add or improve WordPress agents:

- **New Agent**: Create specialized agent
- **Agent Enhancement**: Improve agent review capabilities
- **Agent Tests**: Add agent test scenarios
- **Agent Documentation**: Improve agent documentation

### Rules

Add or improve rules:

- **New Rule**: Add WordPress-specific rule
- **Rule Enhancement**: Improve rule enforcement
- **Rule Updates**: Update for standards changes
- **Rule Tests**: Add rule test cases

### Commands

Add or improve commands:

- **New Command**: Create useful WordPress command
- **Command Enhancement**: Improve command functionality
- **Command Documentation**: Improve command docs
- **Command Aliases**: Add convenient aliases

### Hooks

Add or improve hooks:

- **New Hook**: Create validation hook
- **Hook Enhancement**: Improve hook validation
- **Hook Tests**: Add hook test suite
- **Hook Documentation**: Improve hook docs

### Examples

Add or improve examples:

- **New Example**: Create complete example project
- **Example Enhancement**: Improve existing example
- **Example Documentation**: Add README and docs
- **Example Tests**: Add tests for examples

### Documentation

Improve documentation:

- **New Documentation**: Create new documentation file
- **Documentation Enhancement**: Improve existing docs
- **Documentation Updates**: Update for changes
- **Translation**: Translate documentation

## Development Setup

### Repository Structure

```
opencode-wordpress/
├── .opencode/
│   ├── opencode.json          # Configuration
│   ├── commands/               # Commands (.md files)
│   ├── instructions/
│   │   └── INSTRUCTIONS.md
│   └── prompts/
│   └── agents/                 # Agent prompts (.txt)
├── agents/                     # Agent definitions (.md)
├── skills/                     # Skills (directories with SKILL.md)
├── rules/
│   ├── common/                 # Common rules
│   └ wordpress/               # WordPress rules
├── hooks/                      # Pre-commit hooks (.js)
├── examples/                   # Example projects
│   ├── theme-example/
│   ├── plugin-example/
│   └── woocommerce-example/
├── docs/                       # Documentation
├── README.md                   # Main README
├── CHANGELOG.md                # Version history
├── CONTRIBUTING.md             # This file
├── LICENSE                     # GPL v2
├── install.sh                  # Installation script
└── package.json                # NPM configuration
```

### Local Testing

Test your changes locally:

```bash
# Test skill
opencode skill your-new-skill

# Test agent
opencode agent your-new-agent

# Test command
/your-new-command

# Test hook
node hooks/your-new-hook.js test-file.php

# Test examples
cd examples/theme-example
# Run WordPress with theme
```

### Testing Environment

Set up testing environment:

```bash
# Create test WordPress installation
wp core download
wp config create --dbname=test --dbuser=root
wp db create
wp core install --url=localhost --title=Test --admin_user=admin

# Install test theme/plugin
wp theme activate examples/theme-example
wp plugin activate examples/plugin-example
```

## Contribution Process

### 1. Create Branch

```bash
# Update from upstream
git fetch upstream

# Create feature branch
git checkout -b feature/your-feature upstream/main
```

Branch naming:
- `feature/skill-name`: New skill
- `enhancement/skill-name`: Skill enhancement
- `agent/agent-name`: Agent contribution
- `rule/rule-name`: Rule contribution
- `command/command-name`: Command contribution
- `hook/hook-name`: Hook contribution
- `example/example-name`: Example contribution
- `docs/documentation-name`: Documentation contribution
- `fix/issue-number`: Bug fix
- `test/test-name`: Test contribution

### 2. Make Changes

Make your changes following standards below.

### 3. Commit Changes

```bash
# Stage changes
git add .

# Commit with proper message
git commit -m "Add skill for WordPress REST API authentication"
```

Commit message format:
```
<type>(<scope>): <subject>

<body>

<footer>
```

Types:
- `feat`: New feature
- `enhance`: Enhancement
- `fix`: Bug fix
- `docs`: Documentation
- `test`: Tests
- `refactor`: Refactoring
- `chore`: Maintenance

Examples:
```
feat(skill): Add WordPress REST API authentication skill

Add comprehensive skill for implementing WordPress REST API
authentication patterns including OAuth, JWT, and cookie auth.

Closes #123
```

### 4. Push and Create PR

```bash
# Push to your fork
git push origin feature/your-feature

# Create pull request on GitHub
# Use PR template
```

## Standards

### Skill Standards

SKILL.md structure:

```markdown
# Skill Name

Brief description of the skill.

## Overview

Detailed overview of what the skill provides.

## Capabilities

What the skill can help with:
- Capability 1
- Capability 2

## Instructions

Step-by-step instructions for using the skill.

## Examples

### Example 1: Title

Description of example.

```php
// Code example
```

## References

- WordPress documentation links
- Related skills
- External resources

## Notes

Important notes and warnings.

## Version

- WordPress version compatibility
- PHP version compatibility
```

### Agent Standards

Agent .md structure:

```markdown
# Agent Name

Agent purpose and behavior.

## Activation

When the agent activates:
- File patterns
- Context triggers

## Review Capabilities

What the agent reviews:
- Review area 1
- Review area 2

## Review Process

How the agent performs review:
1. Step 1
2. Step 2

## Output Format

How the agent reports findings:
- Format description
- Example output

## Configuration

Agent configuration options.

## Version

Compatibility information.
```

### Rule Standards

Rule .md structure:

```markdown
# Rule Name

Brief rule description.

## Purpose

Why this rule exists.

## Enforcement

How the rule is enforced.

## Requirements

What the rule requires:
- Requirement 1
- Requirement 2

## Examples

### Good Example

```php
// Correct code
```

### Bad Example

```php
// Incorrect code
```

## References

Related documentation.

## Severity

Rule severity: error, warning, info.
```

### Command Standards

Command .md structure:

```markdown
---
name: command-name
description: Command description
category: wordpress
---

# Command Name

Detailed command description.

## Usage

```bash
/command-name [options]
```

## Options

- `--option1`: Description
- `--option2`: Description

## Examples

### Example 1

```bash
/command-name --option1 value
```

## Related

- Related commands
- Related skills
```

### Hook Standards

Hook .js structure:

```javascript
/**
 * Hook Name
 * 
 * Purpose and validation behavior
 */

module.exports = {
    name: 'hook-name',
    description: 'Hook description',
    
    validate: async (files, config) => {
        // Validation logic
        const errors = [];
        
        for (const file of files) {
            // Check file
            if (hasIssue(file)) {
                errors.push({
                    file: file.path,
                    message: 'Issue description',
                    severity: 'error'
                });
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    config: {
        // Default configuration
        enabled: true,
        exclude: ['vendor/', 'node_modules/']
    }
};
```

### PHP Code Standards

Follow WordPress Coding Standards:

- Proper indentation
- Yoda conditions
- Spacing rules
- Naming conventions
- Documentation blocks

Example:

```php
/**
 * Function description
 *
 * @since 1.0.0
 *
 * @param string $param Parameter description.
 * @return string Return description.
 */
function my_function( $param ) {
    // Yoda condition
    if ( 'value' === $param ) {
        return 'result';
    }
    
    return '';
}
```

### Documentation Standards

- Clear and concise
- Proper formatting
- Complete examples
- Valid markdown
- No broken links

## Testing

### Skill Testing

Test skill invocation:

```bash
# Invoke skill
opencode skill your-skill

# Verify guidance is correct
# Check examples work
# Test with real WordPress project
```

### Agent Testing

Test agent review:

```bash
# Create test file with known issues
# Run agent
opencode agent your-agent

# Verify agent detects issues
# Check agent output format
```

### Hook Testing

Test hook validation:

```bash
# Create test file with known issues
node hooks/your-hook.js test-file.php

# Verify hook detects issues
# Check hook output
```

### Integration Testing

Test complete workflow:

```bash
# Create WordPress project
# Use skill for development
# Run agent for review
# Run hooks for validation
# Verify everything works together
```

## Documentation

### README Updates

Update README for significant changes:

- New skills
- New agents
- New commands
- New hooks
- Breaking changes

### CHANGELOG Updates

Update CHANGELOG.md:

```markdown
## [1.1.0] - 2025-01-15

### Added
- New WordPress REST API skill
- WooCommerce order handling agent

### Changed
- Enhanced theme development skill
- Improved PHP lint hook

### Fixed
- Security check hook false positives

### Deprecated
- Old command syntax

### Removed
- Deprecated features

### Security
- Security improvements
```

### Code Documentation

Add inline documentation:

```php
/**
 * @since 1.0.0
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
add_action( 'wp_enqueue_scripts', 'my_enqueue' );
```

## Pull Request Guidelines

### PR Template

```markdown
## Description

Brief description of changes.

## Type of Change

- [ ] New skill
- [ ] Skill enhancement
- [ ] New agent
- [ ] Agent enhancement
- [ ] New rule
- [ ] Rule enhancement
- [ ] New command
- [ ] Command enhancement
- [ ] New hook
- [ ] Hook enhancement
- [ ] New example
- [ ] Example enhancement
- [ ] Documentation
- [ ] Bug fix
- [ ] Test

## Testing

How was this tested?

- [ ] Local testing
- [ ] Integration testing
- [ ] WordPress testing

## Checklist

- [ ] Code follows standards
- [ ] Documentation updated
- [ ] Tests added/updated
- [ ] CHANGELOG updated
- [ ] No breaking changes
- [ ] Ready for review

## Related Issues

Closes #123
Related to #456
```

### PR Requirements

- Clear description
- Proper branch naming
- Standards compliance
- Tests included
- Documentation updated
- No merge conflicts
- All checks passing

## Issue Guidelines

### Issue Template

```markdown
## Issue Type

- [ ] Bug
- [ ] Feature request
- [ ] Enhancement
- [ ] Documentation
- [ ] Question

## Description

Detailed description.

## Expected Behavior

What should happen.

## Actual Behavior

What actually happens.

## Steps to Reproduce

1. Step 1
2. Step 2

## Environment

- OpenCode version:
- WordPress version:
- PHP version:
- Node version:

## Screenshots/Logs

If applicable.

## Possible Solution

If you have ideas.
```

### Bug Reports

Include:
- Clear description
- Steps to reproduce
- Expected behavior
- Actual behavior
- Environment details
- Screenshots/logs

### Feature Requests

Include:
- Clear description
- Use case
- Benefits
- Possible implementation

## Questions?

- **GitHub Discussions**: https://github.com/promoweb/opencode-wordpress/discussions
- **GitHub Issues**: For bug reports and feature requests
- **Email**: contribute@example.com

## License

By contributing, you agree your contributions will be licensed under GPL v2 or later.

## Thank You!

Thank you for contributing to OpenCode WordPress! Your contributions help improve WordPress development for everyone.