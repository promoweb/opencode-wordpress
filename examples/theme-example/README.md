# Opencode Theme Example

A modern, accessible WordPress theme demonstrating best practices for theme development.

## Features

- **Responsive Design**: Mobile-first approach with CSS Grid and Flexbox
- **Accessibility Ready**: WCAG 2.0 compliant with proper ARIA labels and keyboard navigation
- **Customizer Support**: Custom logo, menus, and widgets
- **Developer Friendly**: Clean code, well-documented, following WordPress coding standards
- **Gutenberg Compatible**: Full support for the block editor
- **Performance Optimized**: Minimal dependencies, optimized CSS/JS

## Theme Structure

```
opencode-theme-example/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js             # Theme JavaScript
├── inc/                        # Include files
│   ├── template-tags.php       # Custom template functions
│   └── template-functions.php   # Custom functionality
├── template-parts/             # Template partials
│   ├── content.php             # Post content
│   ├── content-page.php        # Page content
│   └── content-none.php        # No posts found
├── 404.php                     # 404 error template
├── footer.php                  # Footer template
├── functions.php               # Theme setup and functions
├── header.php                  # Header template
├── index.php                   # Main template
├── page.php                    # Single page template
├── sidebar.php                 # Sidebar template
├── single.php                  # Single post template
└── style.css                   # Theme metadata
```

## Installation

1. Upload the theme folder to `/wp-content/themes/`
2. Activate the theme in WordPress Dashboard → Appearance → Themes
3. Configure theme settings via Appearance → Customize

## Customization

### Theme Supports

The theme includes support for:

- `title-tag`: Document title management
- `post-thumbnails`: Featured images
- `custom-logo`: Custom logo upload
- `html5`: HTML5 markup for forms, comments, galleries
- `wp-block-styles`: Core block styles
- `align-wide`: Wide and full alignment for blocks
- `editor-styles`: Editor stylesheet

### Widget Areas

1. **Sidebar** (ID: `sidebar-1`): Main sidebar widget area
2. **Footer** (ID: `footer-1`): Footer widget area

### Navigation Menus

1. **Primary Menu** (`primary`): Main navigation menu
2. **Footer Menu** (`footer`): Footer navigation menu

### Hooks and Filters

```php
// Content width filter
add_filter( 'opencode_theme_content_width', function( $width ) {
    return 1200;
} );

// Enqueue additional scripts
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );
```

## Development

### Requirements

- Node.js 14+
- npm or yarn
- WordPress 5.8+

### Setup

```bash
# Navigate to theme directory
cd wp-content/themes/opencode-theme-example

# Install dependencies (if using build tools)
npm install

# Watch for changes
npm run watch

# Build for production
npm run build
```

### CSS Architecture

The theme uses CSS custom properties for theming:

```css
:root {
    --color-primary: #0073aa;
    --color-secondary: #23282d;
    --color-accent: #00a0d2;
    --color-text: #32373c;
    --color-background: #fff;
    --font-primary: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --max-width: 1200px;
}
```

### JavaScript

Theme JavaScript follows the revealing module pattern:

```javascript
(function() {
    'use strict';
    
    // Private functions
    function privateFunction() {
        // ...
    }
    
    // Public API
    window.OpencodeTheme = {
        init: function() {
            // Initialize
        }
    };
})();
```

## Child Theme

To create a child theme:

```php
<?php
// style.css
/*
 Theme Name:   Opencode Child
 Template:     opencode-theme-example
*/

// functions.php
<?php
add_action( 'wp_enqueue_scripts', 'child_theme_styles' );
function child_theme_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
```

## Coding Standards

This theme follows:

- WordPress Coding Standards for PHP
- WordPress HTML Coding Standards
- WordPress CSS Coding Standards
- WordPress JavaScript Coding Standards

## Browser Support

- Chrome (last 2 versions)
- Firefox (last 2 versions)
- Safari (last 2 versions)
- Edge (last 2 versions)
- IE 11 (graceful degradation)

## Security

- All data is properly escaped using WordPress functions
- Nonce verification for forms
- Proper capability checks
- No direct database queries

## Performance

- Minimal HTTP requests
- Optimized images with lazy loading
- No jQuery dependency
- CSS critical path optimized
- Asynchronous JavaScript loading

## Accessibility

- Skip link to main content
- Proper heading hierarchy
- ARIA labels for interactive elements
- Keyboard navigation support
- Focus management
- Screen reader text for icons
- Color contrast compliance

## Internationalization

The theme is translation-ready:

```php
// Load text domain
load_theme_textdomain( 'opencode-theme-example', get_template_directory() . '/languages' );

// Usage
esc_html__( 'Text', 'opencode-theme-example' );
esc_html_e( 'Text', 'opencode-theme-example' );
printf( esc_html__( 'Text with %s', 'opencode-theme-example' ), $variable );
```

## Testing

Test with:

- Theme Check plugin
- WordPress Theme Unit Test Data
- Debug Bar
- Query Monitor
- WAVE Accessibility Tool
- BrowserStack for cross-browser testing

## Credits

- Built with WordPress best practices
- Inspired by WordPress core themes
- CSS Grid and Flexbox layouts
- Accessibility guidelines from WCAG 2.0

## License

GPL v2 or later

## Changelog

### 1.0.0
- Initial release
- Responsive layout
- Accessibility features
- Widget areas
- Custom menu support
- Custom logo support
- Block editor support