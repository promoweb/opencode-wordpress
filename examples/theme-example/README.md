# OpenCode Theme Example

A modern, accessible, responsive WordPress blog theme demonstrating best practices for WordPress theme development.

## Description

OpenCode Theme Example is a production-ready WordPress theme built following all WordPress Theme Developer Handbook guidelines. It features responsive design, accessibility compliance, customizer support, Gutenberg compatibility, and performance optimization.

## Features

### 🎨 Design & Layout
- **Responsive Design**: Mobile-first approach with fluid layouts
- **CSS Grid & Flexbox**: Modern layout techniques
- **Customizable Colors**: Primary, accent, and link colors via Customizer
- **Sidebar Position**: Left, right, or no sidebar options
- **Footer Widgets**: Up to 3 footer widget areas
- **Custom Logo Support**: Upload your logo via Customizer

### ⚡ Performance
- **Native Lazy Loading**: WordPress 5.5+ lazy loading support
- **Preload Critical Assets**: Faster initial page load
- **Optimized Assets**: Minified CSS/JS with proper versioning
- **Disabled Emoji Scripts**: Reduces unnecessary HTTP requests
- **Limited Post Revisions**: Prevents database bloat

### ♿ Accessibility
- **Skip to Content Link**: Keyboard navigation support
- **Proper Heading Hierarchy**: SEO and screen reader friendly
- **ARIA Labels**: Enhanced screen reader support
- **Focus Styles**: Visible focus indicators
- **Color Contrast**: WCAG 2.1 AA compliant

### 🎯 WordPress Standards
- **Template Hierarchy**: Proper use of WordPress template hierarchy
- **Template Parts**: Modular, reusable template components
- **Theme Supports**: All recommended theme supports enabled
- **Customizer API**: Live preview with postMessage transport
- **Translation Ready**: Full i18n support with text domain

## Theme Structure

```
opencode-theme-example/
├── assets/
│   ├── css/
│   │   ├── style.css           # Main styles
│   │   ├── responsive.css      # Responsive enhancements
│   │   └── editor-style.css    # Gutenberg editor styles
│   └── js/
│       ├── main.js             # Main JavaScript
│       └── customizer-preview.js
├── inc/
│   ├── customizer.php          # Customizer settings
│   ├── template-functions.php  # Custom functions
│   └── template-tags.php       # Template tags
├── languages/                  # Translation files
├── template-parts/
│   ├── content.php             # Post content
│   ├── content-page.php        # Page content
│   ├── content-none.php        # No content found
│   └── content-search.php      # Search results
├── 404.php
├── archive.php
├── author.php
├── footer.php
├── functions.php
├── header.php
├── index.php
├── page.php
├── search.php
├── sidebar.php
├── single.php
├── singular.php
├── style.css                  # Theme metadata
└── screenshot.png             # Theme preview (1200x900)
```

## Installation

### Requirements
- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Methods

#### From WordPress Dashboard
1. Go to **Appearance > Themes > Add New**
2. Click **Upload Theme**
3. Choose the theme zip file
4. Click **Install Now**
5. Activate the theme

#### Manual Installation
1. Download the theme
2. Extract to `/wp-content/themes/opencode-theme-example/`
3. Activate via **Appearance > Themes**

## Customization

### Customizer Options

Navigate to **Appearance > Customize** to access:

#### Theme Options
- **Sidebar Position**: Left, Right, or None
- **Home Posts Per Page**: Number of posts on homepage
- **Archive Posts Per Page**: Number of posts on archive pages
- **Excerpt Length**: Word count for excerpts
- **Show Author Info**: Display author information on posts
- **Show Post Date**: Display publication dates

#### Theme Colors
- **Primary Color**: Main theme color
- **Accent Color**: Highlight color
- **Link Color**: Hyperlink color

### Widget Areas
- **Primary Sidebar**: Main sidebar widgets
- **Footer Widget Area 1**: Footer column 1
- **Footer Widget Area 2**: Footer column 2
- **Footer Widget Area 3**: Footer column 3

### Navigation Menus
- **Primary Menu**: Main navigation
- **Footer Menu**: Footer navigation
- **Social Links Menu**: Social media links

### Custom Image Sizes
The theme registers three custom image sizes:
- `opencode-featured`: 1200x600 (featured images)
- `opencode-thumbnail`: 400x300 (post thumbnails)
- `opencode-square`: 300x300 (square images)

## Developer Notes

### Hooks & Filters

#### Actions
- `opencode_theme_setup`: Theme initialization
- `opencode_theme_widgets_init`: Widget registration
- `opencode_theme_scripts`: Script/style enqueueing

#### Filters
- `opencode_theme_content_width`: Modify content width
- `opencode_theme_excerpt_length`: Customize excerpt length
- `opencode_theme_excerpt_more`: Customize excerpt "read more" text
- `body_class`: Add custom body classes

### Child Theme Support

All functions are pluggable (wrapped in `if (!function_exists())`), making the theme fully compatible with child themes.

Example child theme `functions.php`:
```php
<?php
// Override parent theme function
function opencode_theme_excerpt_length($length) {
    return 30; // Custom excerpt length
}
```

### Template Hierarchy

The theme follows WordPress Template Hierarchy:
- `singular.php` for single posts/pages
- `archive.php` for archive pages
- `author.php` for author archives
- `search.php` for search results
- `404.php` for 404 errors

### Performance Optimizations

1. **Lazy Loading**: Native lazy loading enabled
2. **Emoji Removal**: Emoji scripts/styles removed
3. **Asset Preloading**: Critical CSS preloaded
4. **Limited Revisions**: Post revisions limited to 3
5. **Minified Assets**: CSS/JS properly minified

### Accessibility Features

1. **Skip Links**: Skip to content link
2. **ARIA Labels**: Proper ARIA attributes
3. **Focus Management**: Visible focus indicators
4. **Screen Reader Text**: `.screen-reader-text` class
5. **Keyboard Navigation**: Full keyboard support
6. **Color Contrast**: WCAG 2.1 AA compliant

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)
- IE 11 (degraded experience)

## Credits

- **WordPress**: [wordpress.org](https://wordpress.org/)
- **Normalize.css**: [necolas.github.io/normalize.css/](https://necolas.github.io/normalize.css/)
- **Font Awesome**: Optional for icons

## License

OpenCode Theme Example is licensed under the GNU General Public License v2.0 or later.

## Changelog

### 1.0.0
- Initial release
- Responsive design with CSS Grid
- Customizer integration
- Accessibility compliance
- Performance optimizations
- WooCommerce basic support
- Full i18n support

## Support

For support, please open an issue on GitHub or contact the theme author.

## Contributing

Contributions are welcome! Please read the contributing guidelines before submitting a pull request.

---

Built with ❤️ by OpenCode following WordPress best practices.
