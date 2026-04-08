# WordPress Theme Development Workflow

Start a comprehensive WordPress theme development workflow with validation and best practices.

## Task

$ARGUMENTS

## Output Directory

**IMPORTANT**: All theme files MUST be saved in the `wp-content/themes/` directory relative to the WordPress project root.

- Create the theme directory at: `wp-content/themes/{theme-name}/`
- All theme files (style.css, index.php, functions.php, etc.) go inside this directory
- Do NOT save files in any other location (examples/, current directory, etc.)

## Process

Follow these steps for theme development:

### 1. Theme Structure Analysis

Analyze or create theme structure:
- Verify required files (`style.css`, `index.php`)
- Check theme metadata in `style.css`
- Validate `screenshot.png` (1200x900 recommended)
- Review `functions.php` organization

### 2. Theme Setup Validation

Ensure proper theme setup:
- `after_setup_theme` hook usage
- Theme supports (`title-tag`, `post-thumbnails`, `custom-logo`, etc.)
- Navigation menus registration
- Content width definition
- Translation loading

### 3. Template Hierarchy Check

Verify template hierarchy:
- `index.php` exists (required)
- Proper template files (`header.php`, `footer.php`, `sidebar.php`)
- Single post/page templates
- Archive templates
- Template parts usage (`get_template_part()`)

### 4. Scripts and Styles

Review script/style enqueuing:
- `wp_enqueue_scripts` hook
- Proper dependencies
- Version numbers
- Scripts in footer when appropriate
- No hardcoded `<link>` or `<script>` tags

### 5. Customizer Implementation

Check Customizer setup:
- Settings registration
- Sanitization callbacks
- Selective refresh
- Live preview (postMessage)
- Default values

### 6. Widget Areas

Verify widget registration:
- `widgets_init` hook
- Unique widget area IDs
- Descriptions provided
- Proper HTML wrappers

### 7. Accessibility Check

Ensure accessibility compliance:
- Skip to content link
- Proper heading hierarchy
- Image alt attributes
- Color contrast (4.5:1 minimum)
- Keyboard navigation
- Focus styles
- ARIA labels

### 8. Performance Optimization

Review performance:
- Image optimization
- CSS/JS minification
- Lazy loading
- Minimal HTTP requests
- Transient usage for caching

### 9. Internationalization

Check i18n readiness:
- All strings use `__()`, `_e()`, etc.
- Text domain matches theme folder
- Translation files present
- `load_theme_textdomain()` called

### 10. Child Theme Compatibility

Ensure child theme support:
- Functions are pluggable (`function_exists()` wrapper)
- Proper use of `get_template_directory_uri()` vs `get_stylesheet_directory_uri()`
- Template parts overridable

### 11. Security Review

Check security practices:
- Output escaping (`esc_html()`, `esc_attr()`, `esc_url()`)
- Input sanitization
- No hardcoded credentials
- Proper capability checks

### 12. Documentation

Ensure proper documentation:
- DocBlocks for functions
- README with installation instructions
- Changelog if applicable

## Output

Provide:
1. Theme structure assessment
2. Missing required elements
3. Best practice recommendations
4. Accessibility compliance report
5. Performance optimization suggestions
6. Security issues (if any)
7. Overall theme quality score (A-F)

## Files to Review

- `style.css`
- `index.php`
- `functions.php`
- All PHP template files
- JavaScript files
- CSS files
- `readme.txt` (if for WP.org)

## Notes

- Follow WordPress Theme Developer Handbook
- Ensure GPL-compatible license
- Check Theme Review Guidelines if submitting to WP.org
- Test with Theme Check plugin
- Verify with Theme Unit Test data

Use the `theme-reviewer` agent for detailed theme review after implementation.