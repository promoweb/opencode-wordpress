# WordPress Plugin Development Workflow

Start a comprehensive WordPress plugin development workflow with security, performance, and best practices.

## Task

$ARGUMENTS

## Output Directory

**IMPORTANT**: All plugin files MUST be saved in the `wp-content/plugins/` directory relative to the WordPress project root.

- Create the plugin directory at: `wp-content/plugins/{plugin-name}/`
- All plugin files (main PHP file, includes/, admin/, etc.) go inside this directory
- Do NOT save files in any other location (examples/, current directory, etc.)

## Process

Follow these steps for plugin development:

### 1. Plugin Structure Setup

Create or validate plugin structure:
- Main plugin file with proper header
- `uninstall.php` or uninstall hook
- Organized directory structure:
  - `includes/` for classes and functions
  - `admin/` for admin functionality
  - `public/` for public functionality
  - `languages/` for translations
  - `tests/` for test files

### 2. Plugin Header

Verify plugin header:
- Plugin Name (required)
- Version
- License (GPL compatible)
- Text Domain
- Requires at least
- Requires PHP

### 3. Plugin Initialization

Set up proper initialization:
- `ABSPATH` check at top
- Constants definition with plugin prefix
- Initialization hook (`plugins_loaded`)
- Activation/deactivation hooks
- Proper class instantiation

### 4. Hooks Registration

Register WordPress hooks:
- Actions for operations
- Filters for data modification
- Appropriate priorities
- Accepted args specified
- Organized callback registration

### 5. Settings API Implementation

If plugin has settings:
- `register_setting()` with sanitization callback
- `add_settings_section()`
- `add_settings_field()`
- Settings page HTML
- Proper option storage

### 6. Custom Post Types (if needed)

Register custom post types:
- `init` hook
- Proper labels array
- `supports` array
- `show_in_rest` for Gutenberg
- Capability type
- Rewrite rules flush on activation

### 7. Meta Boxes (if needed)

Add meta boxes:
- `add_meta_box()` call
- Render callback
- Save callback with:
  - Nonce verification
  - Autosave check
  - Permission check
  - Data sanitization

### 8. Shortcodes (if needed)

Register shortcodes:
- `add_shortcode()` registration
- Attribute validation with `shortcode_atts()`
- Attribute sanitization
- Return output (not echo)
- Use output buffering

### 9. AJAX Handlers

If plugin uses AJAX:
- `wp_ajax_*` hooks for authenticated users
- `wp_ajax_nopriv_*` hooks for public
- Nonce verification
- Capability checks
- Input sanitization
- `wp_send_json_*` responses

### 10. REST API Endpoints (if needed)

Register REST API:
- `rest_api_init` hook
- `register_rest_route()`
- Permission callback
- Argument sanitization/validation
- Proper response format
- Error handling

### 11. Database Operations

If plugin uses database:
- Prepared statements for all queries
- `$wpdb->prefix` for custom tables
- Data sanitization before storage
- Error handling
- `dbDelta()` for table creation
- Cleanup in uninstall

### 12. Security Implementation

Ensure security:
- Input sanitization (`sanitize_text_field()`, etc.)
- Output escaping (`esc_html()`, `esc_attr()`, `esc_url()`)
- Nonce verification for forms
- Capability checks
- Prepared statements for DB queries
- File upload validation (if applicable)

### 13. Internationalization

Prepare for translation:
- All strings use `__()`, `_e()`, etc.
- Text domain matches folder name
- Translation files generated
- `load_plugin_textdomain()` called

### 14. Performance Optimization

Optimize performance:
- Efficient database queries
- Transient caching
- Conditional script/style loading
- Proper hook priorities
- Avoid expensive operations in loops

### 15. Uninstall/Cleanup

Implement uninstall routine:
- Delete options
- Remove custom post type posts
- Drop custom tables
- Clear scheduled events
- Delete transients

### 16. Testing Setup

Prepare testing:
- PHPUnit configuration
- Unit tests for logic
- Integration tests for WP integration
- Test coverage > 70%

### 17. Documentation

Create documentation:
- DocBlocks for all functions/classes
- README with usage instructions
- Installation guide
- Changelog
- License file

## Output

Provide:
1. Plugin structure assessment
2. Security analysis
3. Performance recommendations
4. Best practices compliance
5. Missing elements
6. Overall plugin quality score (A-F)

## Files to Create/Review

- Main plugin file
- `uninstall.php`
- `includes/class-*.php` files
- Admin and public classes
- `readme.txt` (for WP.org)
- `README.md`
- `LICENSE`
- Test files

## Plugin Territory Guidelines

Ensure plugin doesn't:
- Add SEO meta tags (use SEO plugins)
- Handle forms unnecessarily (use form plugins)
- Add analytics (use analytics plugins)
- Create content that belongs in theme

Plugin should:
- Add functionality portable across themes
- Be theme-independent
- Follow WordPress APIs
- Respect user's existing setup

## Notes

- Follow WordPress Plugin Developer Handbook
- Ensure GPL-compatible license
- Test with WordPress debugging enabled
- Use Query Monitor for performance testing
- Test on multiple WordPress versions
- Verify PHP compatibility

Use the `plugin-reviewer` agent for detailed plugin review after implementation.