# Changelog

All notable changes to OpenCode WordPress will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Comprehensive testing guide (docs/TESTING.md)
- Migration guide for existing workflows (docs/MIGRATION.md)
- Contributing guidelines (docs/CONTRIBUTING.md)

### Changed
- Enhanced documentation structure
- Improved installation process

## [1.0.0] - 2025-04-07

### Added

#### Skills (8 complete skills)
- **wordpress-theme-development**: Complete theme development guidance
  - Theme structure and hierarchy
  - Enqueue scripts/styles best practices
  - Widget areas and customizer integration
  - Template patterns and accessibility
  
- **wordpress-plugin-development**: Complete plugin development guidance
  - Plugin structure and architecture
  - Hooks and filters patterns
  - Settings API implementation
  - Custom post types and taxonomies
  - Security best practices
  
- **woocommerce-patterns**: WooCommerce development patterns
  - Extension structure
  - Custom product types
  - Payment gateway integration
  - Order handling patterns
  - WooCommerce hooks reference
  
- **wordpress-security**: Security best practices
  - Security audit checklist
  - Escaping and sanitization rules
  - Nonce verification patterns
  - Capability check templates
  - SQL injection prevention
  
- **wordpress-rest-api**: REST API development
  - Endpoint registration
  - Authentication patterns
  - Permission callbacks
  - Response formatting
  - Error handling
  
- **wordpress-testing**: Testing guidance
  - PHPUnit setup
  - Test writing patterns
  - Mock object usage
  - Integration testing
  
- **wordpress-hooks-filters**: Hooks and filters patterns
  - Action vs filter usage
  - Hook priority management
  - Custom hook creation
  - Hook removal patterns
  
- **wordpress-database**: Database operations
  - $wpdb usage patterns
  - Custom table creation
  - Query optimization
  - Transaction handling

#### Agents (4 specialized agents)
- **wordpress-reviewer**: General WordPress code review
  - Coding standards compliance
  - Security vulnerabilities
  - Performance issues
  - Hook/filter usage
  - Documentation quality
  
- **wordpress-build-resolver**: Build issue resolver
  - PHP syntax errors
  - Composer dependency issues
  - npm build errors
  - WordPress coding standard violations
  
- **theme-reviewer**: Specialized theme review
  - Template hierarchy validation
  - Required files presence
  - Theme supports
  - Widget registration
  - Customizer implementation
  - Accessibility compliance
  
- **plugin-reviewer**: Specialized plugin review
  - Plugin header compliance
  - Activation/deactivation hooks
  - Uninstall handling
  - Settings API usage
  - Custom post type registration
  - Security practices

#### Rules (10 complete rules)
- **Common Rules (4)**:
  - git-workflow: Git conventions and workflow
  - code-style: General coding standards
  - documentation: Documentation requirements
  - security: General security practices
  
- **WordPress Rules (6)**:
  - coding-style: WordPress Coding Standards
  - hooks: Hook and filter usage rules
  - patterns: WordPress pattern enforcement
  - security: WordPress-specific security rules
  - testing: Testing requirements
  - database: Database operation rules

#### Commands (5 WordPress commands)
- **wp-theme**: Theme development command
  - `/wp-theme create [name]`: Create new theme
  - `/wp-theme review`: Review theme
  - `/wp-theme validate`: Validate theme
  - `/wp-theme build`: Build theme assets
  
- **wp-plugin**: Plugin development command
  - `/wp-plugin create [name]`: Create new plugin
  - `/wp-plugin review`: Review plugin
  - `/wp-plugin validate`: Validate plugin
  - `/wp-plugin build`: Build plugin
  
- **wp-review**: WordPress code review command
  - `/wp-review`: Full WordPress code review
  - `/wp-review [path]`: Review specific file
  - `/wp-review --fix`: Apply fixes
  
- **wp-build-fix**: Build issue fix command
  - `/wp-build-fix`: Fix build issues
  - `/wp-build-fix --type=[type]`: Fix specific type
  - `/wp-build-fix --auto`: Auto-fix all
  
- **wc-build**: WooCommerce development command
  - `/wc-build create [name]`: Create WooCommerce extension
  - `/wc-build review`: Review WooCommerce code
  - `/wc-build validate`: Validate WooCommerce integration

#### Hooks (3 pre-commit hooks)
- **php-lint.js**: PHP syntax validation
  - Validates PHP syntax
  - Detects parse errors
  - Checks deprecated functions
  - PHP version compatibility
  
- **wp-debug-check.js**: Debug code detection
  - Prevents var_dump() in code
  - Prevents print_r() in code
  - Detects debug output
  - Checks console logs
  
- **security-check.js**: Security validation
  - SQL injection detection
  - XSS vulnerability checks
  - CSRF protection validation
  - Authentication bypass prevention

#### Examples (3 complete examples)
- **theme-example**: Complete WordPress theme
  - 12 files with full structure
  - Responsive layout
  - Accessibility features
  - Widget areas
  - Custom menu support
  - Custom logo support
  - Block editor support
  - Complete documentation (README.md)
  
- **plugin-example**: Complete WordPress plugin
  - 9 files with full structure
  - Custom Post Type implementation
  - Custom Taxonomy registration
  - Settings API implementation
  - Admin pages and meta boxes
  - Security and performance optimizations
  - Internationalization support
  - Uninstall routine
  - Complete documentation (README.md)
  
- **woocommerce-example**: Complete WooCommerce extension
  - 6 files with full structure
  - Custom Product Type implementation
  - Custom Payment Gateway integration
  - Order handling with custom meta
  - WooCommerce admin integration
  - Email template customization
  - My account customization
  - Custom fees support
  - Complete documentation (README.md)

#### Documentation (6 complete guides)
- **INSTALLATION.md**: Installation guide
  - System requirements
  - Installation methods (Quick, Manual, NPM)
  - Post-installation verification
  - Configuration options
  - Directory structure
  - Updating procedures
  - Uninstallation steps
  - Troubleshooting common issues
  - Platform-specific notes
  
- **USAGE.md**: Usage guide
  - Quick start
  - Skills invocation
  - Commands usage
  - Agents usage
  - Rules enforcement
  - Hooks usage
  - Integration examples
  - Advanced usage
  - Best practices
  - Tips and tricks
  
- **MIGRATION.md**: Migration guide
  - Migration scenarios
  - From manual development
  - From other AI tools
  - From existing OpenCode config
  - Team migration
  - Component migration
  - Code migration patterns
  - Testing migration
  - Common migration issues
  
- **CONTRIBUTING.md**: Contributing guide
  - Code of conduct
  - Getting started
  - Types of contributions
  - Development setup
  - Contribution process
  - Standards
  - Testing
  - Pull request guidelines
  - Issue guidelines
  
- **TESTING.md**: Testing guide
  - Testing prerequisites
  - Skill testing
  - Agent testing
  - Hook testing
  - Command testing
  - Example testing
  - Integration testing
  - WordPress testing
  - Performance testing
  - Test coverage
  - Continuous testing
  
- **README.md**: Main repository README
  - Overview and features
  - Quick start
  - Installation
  - Usage examples
  - Documentation links
  - Contributing information
  - License information

#### Configuration
- **opencode.json**: Complete OpenCode configuration
  - Skills directory configuration
  - Agents directory configuration
  - Rules directory configuration
  - Commands directory configuration
  - Hooks directory configuration
  - WordPress-specific settings
  
- **INSTRUCTIONS.md**: Main instructions
  - Skill invocation instructions
  - Agent activation rules
  - Command usage guidelines
  - Hook execution procedures
  - WordPress-specific workflows
  
- **Agent prompt files (4)**: Specialized agent prompts
  - wordpress-reviewer.txt
  - wordpress-build-resolver.txt
  - theme-reviewer.txt
  - plugin-reviewer.txt

### Infrastructure
- **install.sh**: Automated installation script
  - Detects OpenCode configuration directory
  - Copies all components
  - Updates configuration
  - Verifies installation
  - Cross-platform support
  
- **package.json**: NPM configuration for hooks
  - Hook dependencies
  - Test configuration
  - Script definitions

### Documentation Quality
- All documentation follows markdown best practices
- Complete code examples for all patterns
- WordPress-specific guidance
- Security-first approach
- Accessibility compliance
- Internationalization ready

## [0.9.0] - 2025-04-01 (Beta Release)

### Added
- Initial beta version
- Core skills (theme, plugin, WooCommerce)
- Basic agents
- Initial rules set
- Proof of concept examples
- Basic documentation

### Changed
- Refactored from everything-claude-code patterns
- WordPress-specific adaptations
- Security enhancements

### Fixed
- Initial bugs and issues

## Future Releases

## [1.1.0] - Planned

### Added
- WordPress CLI integration skill
- WordPress multisite patterns skill
- WordPress performance optimization skill
- WordPress SEO patterns skill
- WordPress caching patterns skill
- Additional agent: performance-reviewer
- Additional agent: seo-reviewer
- Additional hooks for code quality
- Additional examples: WordPress multisite setup
- Integration tests suite
- Automated update mechanism

### Changed
- Enhanced skill examples
- Improved agent detection algorithms
- Faster hook validation
- Better error reporting

### Deprecated
- Old command syntax (will be removed in 2.0)

## [1.2.0] - Planned

### Added
- WordPress child theme patterns skill
- WordPress backup and migration skill
- WordPress debugging patterns skill
- WordPress REST API authentication skill
- WordPress custom field patterns skill
- WordPress shortcode patterns skill
- Additional agent: child-theme-reviewer
- Additional agent: rest-api-reviewer
- WooCommerce payment gateway examples
- Integration with WP-CLI
- Docker WordPress development environment

### Changed
- Updated for WordPress 6.5
- Updated for PHP 8.2
- Enhanced WooCommerce patterns
- Improved documentation

## [2.0.0] - Future Major Release

### Added
- AI-powered code generation
- WordPress design patterns library
- WordPress architecture patterns
- WordPress microservices patterns
- WordPress API-first patterns
- WordPress cloud integration
- WordPress automation patterns
- WordPress DevOps patterns

### Changed
- Complete rewrite for modern WordPress
- New skill format
- New agent architecture
- New hook system
- Performance optimizations

### Removed
- Deprecated command syntax
- Legacy patterns
- Outdated WordPress practices

## Version History Summary

| Version | Date | Type | Major Changes |
|---------|------|------|---------------|
| 1.0.0 | 2025-04-07 | Release | Complete WordPress development toolkit |
| 0.9.0 | 2025-04-01 | Beta | Initial beta release |
| Unreleased | - | Development | Testing and documentation enhancements |

## Upgrade Guide

### From 0.9.0 to 1.0.0

1. Remove old installation
```bash
rm -rf ~/.config/opencode/skills/wordpress-*
rm -rf ~/.config/opencode/agents/wordpress-*
```

2. Install new version
```bash
git clone https://github.com/yourusername/opencode-wordpress.git
cd opencode-wordpress
./install.sh
```

3. Update configuration
```bash
# Backup existing config
cp ~/.config/opencode/.opencode/opencode.json ~/.config/opencode/.opencode/opencode.json.bak

# Use new configuration
cp .opencode/opencode.json ~/.config/opencode/.opencode/opencode.json
```

4. Verify installation
```bash
opencode skills list
opencode agents list
opencode commands list
```

### From Manual Workflow to 1.0.0

See [MIGRATION.md](docs/MIGRATION.md) for complete migration guide.

## Breaking Changes

### Version 1.0.0

No breaking changes from beta.

### Version 2.0.0 (Planned)

- New skill format will require migration
- Old command syntax will be removed
- Legacy patterns will be deprecated

## Deprecation Notices

### Deprecated in 1.0.0

None.

### Deprecated in 1.1.0 (Planned)

- Old command syntax: Will be removed in 2.0.0
- Legacy patterns: Will be removed in 2.0.0

## Security Fixes

### Version 1.0.0

- Enhanced security hook validation
- Improved nonce verification patterns
- Better SQL injection detection
- XSS vulnerability checks
- CSRF protection validation

## Performance Improvements

### Version 1.0.0

- Optimized skill loading
- Faster agent review
- Improved hook performance
- Better caching strategies
- Reduced memory usage

## Known Issues

### Version 1.0.0

- Hooks may timeout on very large files (>1MB)
- Agent review may be slow on complex plugins
- Skills may need WordPress version updates

### Solutions

- For large files: Split into smaller files
- For complex plugins: Review module by module
- For WordPress updates: Update skill documentation

## Support Policy

### Supported Versions

- **Current (1.0.x)**: Full support, updates, bug fixes
- **Beta (0.9.0)**: No support, upgrade recommended
- **Future**: Development preview, may change

### WordPress Compatibility

- **WordPress 5.8 - 6.4**: Fully supported
- **WordPress 5.0 - 5.7**: Partial support
- **WordPress 4.x**: Legacy support only

### PHP Compatibility

- **PHP 7.4 - 8.2**: Fully supported
- **PHP 7.3**: Partial support
- **PHP 7.2 and below**: No support

### OpenCode Compatibility

- **OpenCode Latest**: Fully supported
- **OpenCode Previous**: Partial support

## Release Schedule

### Regular Releases

- **Major (x.0.0)**: Every 6-12 months
- **Minor (x.y.0)**: Every 2-3 months
- **Patch (x.y.z)**: As needed for bugs

### Release Criteria

- All tests passing
- Documentation updated
- CHANGELOG updated
- No breaking changes (for patches/minors)
- Upgrade guide provided (for majors)

## Contribution Recognition

### Contributors

Thanks to all contributors who made this possible:
- OpenCode team for the AI platform
- WordPress community for patterns and best practices
- Everyone who tested and provided feedback

### How to Contribute

See [CONTRIBUTING.md](docs/CONTRIBUTING.md) for contribution guidelines.

## License

GPL v2 or later. See [LICENSE](LICENSE) file for details.

## Contact

- **GitHub**: https://github.com/yourusername/opencode-wordpress
- **Issues**: https://github.com/yourusername/opencode-wordpress/issues
- **Discussions**: https://github.com/yourusername/opencode-wordpress/discussions
- **Email**: contact@example.com

---

**Note**: This changelog follows [Keep a Changelog](https://keepachangelog.com/) format.
For version comparison, see [SemVer](https://semver.org/).