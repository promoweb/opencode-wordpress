# WordPress Theme Orchestration Workflow

Complete guide for creating production-ready WordPress themes using the `/wp-theme-orchestrate` command.

## Overview

The Theme Orchestration workflow is a comprehensive 5-phase process (plus preliminary data collection) that creates complete, functional WordPress themes from specifications, screenshots, and user descriptions.

## Quick Start

```bash
# Basic usage with description only
/wp-theme-orchestrate "Create a modern business theme with dark mode support"

# With input files (place in plans/input/)
/wp-theme-orchestrate "Create a portfolio theme based on my specifications"
```

## Workflow Phases

### Phase 0: Preliminary - Data Collection

The preliminary phase collects all necessary input data:

#### User Description
Provided via command argument. Should include:
- Theme name suggestion
- Theme purpose (blog, business, portfolio, e-commerce)
- Key features requested
- Design style preferences

#### Input Files (Optional)
Place in `plans/input/` directory:

| File | Purpose |
|------|---------|
| `theme-spec.txt` | Theme metadata, features, colors, typography |
| `site-config.txt` | Site settings, navigation, widgets |
| `custom-post-types.txt` | Custom Post Type definitions |
| `color-scheme.txt` | Color palette specifications |
| `typography.txt` | Font settings and sizes |

#### Reference Screenshots (Optional)
Place in `plans/input/screenshots/`:

| Screenshot | Purpose |
|------------|---------|
| `homepage.png` | Homepage design reference |
| `page.png` | Page template design |
| `single-post.png` | Single post design |
| `archive.png` | Archive template design |
| `product-list.png` | WooCommerce product list |
| `single-product.png` | WooCommerce single product |
| `custom-post.png` | Custom post type design |

### Phase 1: Configuration and Structure

Creates the theme foundation:

1. **Theme Metadata** (`style.css`)
   - Theme name, author, description
   - Version, requires PHP, text domain
   - Tags and features

2. **Directory Structure**
   ```
   wp-content/themes/{theme-name}/
   ├── style.css
   ├── index.php
   ├── functions.php
   ├── screenshot.png
   ├── assets/
   │   ├── css/
   │   ├── js/
   │   └── images/
   ├── inc/
   │   ├── setup.php
   │   ├── customizer.php
   │   ├── widgets.php
   │   ├── template-tags.php
   │   └── template-functions.php
   ├── languages/
   └── template-parts/
   ```

3. **Theme Setup**
   - Theme supports (title-tag, post-thumbnails, custom-logo, html5)
   - Navigation menus registration
   - Custom image sizes
   - Content width definition

4. **Custom Post Types** (if specified)
   - CPT registration with proper labels
   - Taxonomy registration
   - REST API support

5. **Widget Areas**
   - Primary sidebar
   - Footer widget areas
   - Custom widget areas

### Phase 2: Design and Layout Development

Generates visual design and templates:

1. **CSS Custom Properties**
   - Colors extracted from screenshots
   - Typography settings
   - Spacing and layout variables
   - Border and shadow definitions

2. **Template Files**
   - `header.php` - Logo, navigation, mobile menu
   - `footer.php` - Widget areas, copyright, social
   - `front-page.php` - Homepage sections
   - `single.php` - Single post layout
   - `page.php` - Page layout
   - `archive.php` - Archive loop
   - `search.php` - Search results
   - `404.php` - Error page
   - `sidebar.php` - Sidebar widget area

3. **Responsive Design**
   - Mobile-first approach
   - Breakpoints: 576px, 768px, 992px, 1200px, 1400px
   - Fluid typography and spacing

4. **Template Parts**
   - Content partials
   - Entry meta
   - Featured image
   - Pagination
   - Comments

5. **Customizer Options**
   - Color options
   - Typography options
   - Layout options
   - Feature toggles

### Phase 3: Demo Content Implementation

Creates sample content to showcase theme:

1. **Homepage**
   - Hero section with CTA
   - Featured content grid
   - Latest posts section
   - Additional sections based on theme type

2. **Demo Page**
   - Typography showcase
   - Media elements
   - Layout components
   - Sidebar demonstration

3. **Demo Post**
   - Engaging content
   - Featured image
   - Categories and tags
   - Sample comments

4. **Taxonomies**
   - Sample categories
   - Sample tags

### Phase 4: Finalization and Testing

Ensures quality and completeness:

1. **Code Integrity**
   - PHP syntax validation
   - WordPress coding standards
   - Security audit
   - Performance review

2. **Placeholder Removal**
   - No TODO comments
   - No placeholder text
   - No dummy images
   - No debug code

3. **Documentation**
   - README.md
   - CHANGELOG.md
   - screenshot.png

4. **Testing**
   - Theme activation
   - Template rendering
   - Customizer functionality
   - Responsive design
   - Accessibility check

### Phase 5: WooCommerce Integration (Optional)

Adds e-commerce support when requested:

1. **WooCommerce Support**
   - Theme support declaration
   - Product gallery features

2. **WooCommerce Templates**
   - Shop/archive product
   - Single product
   - Cart and checkout
   - My account

3. **WooCommerce Styles**
   - Product grid styling
   - Single product layout
   - Responsive adjustments

4. **Demo Products**
   - Product categories
   - Sample products with images
   - Variable products (if applicable)

5. **WooCommerce Testing**
   - Shop page display
   - Product gallery
   - Cart/checkout flow

## Input File Templates

Templates are available in `plans/input-templates/`. Copy to `plans/input/` and fill in your specifications.

### theme-spec.txt

```txt
# Theme Specification

## Basic Information
Theme Name: My Theme
Author: Your Name
Description: A modern WordPress theme
Version: 1.0.0
Text Domain: my-theme

## Features
- [x] Custom Logo
- [x] Post Thumbnails
- [x] Custom Menus
- [x] Widget Areas
- [ ] WooCommerce Support

## Color Scheme
Primary: #0073aa
Secondary: #23282d
Accent: #00a0d2

## Typography
Heading Font: Inter
Body Font: Open Sans
Base Font Size: 16px

## Layout
Content Width: 1200px
Sidebar Position: right
Footer Columns: 3
```

### site-config.txt

```txt
# Site Configuration

## Site Information
Site Title: My Website
Tagline: Just another WordPress site

## Navigation
Primary Menu Items:
- Home
- About
- Services
- Blog
- Contact

## Widget Areas
- Primary Sidebar
- Footer 1
- Footer 2
- Footer 3
```

### custom-post-types.txt

```txt
# Custom Post Types

## Portfolio
Post Type Key: portfolio
Name: Portfolio Items
Public: true
Has Archive: true
Supports:
- title
- editor
- thumbnail
```

## Output Location

All generated themes are saved to:

```
wp-content/themes/{theme-name}/
```

## Installation

After theme generation:

1. Copy theme folder to WordPress `wp-content/themes/`
2. Go to Appearance > Themes in WordPress Admin
3. Click "Add New" and select the theme
4. Click "Activate"
5. Configure options in Appearance > Customize

## Best Practices

### Security
- All output properly escaped
- All input sanitized
- Nonces for form submissions
- Capability checks

### Performance
- Minimal database queries
- Proper asset enqueuing
- Lazy loading images
- No unnecessary HTTP requests

### Accessibility
- Skip to content link
- Proper heading hierarchy
- ARIA labels
- Keyboard navigation
- Color contrast 4.5:1 minimum

### Internationalization
- All strings translatable
- Proper text domain
- Translation files included

## Troubleshooting

### Theme Not Activating
- Check PHP version compatibility
- Verify all required files exist
- Check for syntax errors in functions.php

### Missing Styles
- Verify CSS files are enqueued
- Check file paths in functions.php
- Clear browser cache

### Customizer Not Saving
- Check sanitization callbacks
- Verify setting IDs are unique
- Check for JavaScript errors

### WooCommerce Issues
- Verify WooCommerce is installed
- Check theme support declaration
- Verify template overrides

## Examples

### Business Theme

```bash
/wp-theme-orchestrate "Create a professional business theme with hero section, services grid, testimonials, and contact form"
```

### Portfolio Theme

```bash
/wp-theme-orchestrate "Create a portfolio theme with project showcase, filterable gallery, and about page"
```

### E-commerce Theme

```bash
/wp-theme-orchestrate "Create a WooCommerce theme with product grid, single product page, and cart integration"
```

### Blog Theme

```bash
/wp-theme-orchestrate "Create a minimalist blog theme with featured posts, sidebar, and reading progress indicator"
```

## Reference

- [WordPress Theme Developer Handbook](https://developer.wordpress.org/themes/)
- [WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)
- [WordPress Customizer API](https://developer.wordpress.org/themes/customize-api/)
- [WooCommerce Theme Developer Guide](https://developer.woocommerce.com/)

## Support

For issues or questions:
- Review the detailed plan in `plans/wordpress-theme-orchestrator-upgrade.md`
- Check input file templates in `plans/input-templates/`
- Use the `theme-reviewer` agent for quality review