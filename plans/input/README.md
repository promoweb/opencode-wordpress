# Theme Input Files

This directory contains input files for the WordPress Theme Orchestrator workflow.

## Directory Structure

```
plans/input/
├── theme-spec.txt          # Theme specifications (metadata, features, layout)
├── site-config.txt          # Site configuration (title, menus, widgets)
├── custom-post-types.txt    # Custom Post Type definitions
├── color-scheme.txt         # Color palette specifications
├── typography.txt           # Typography settings
└── screenshots/             # Reference design images
    ├── homepage.png         # Homepage design
    ├── page.png             # Page template design
    ├── single-post.png      # Single post design
    ├── archive.png          # Archive design
    ├── product-list.png     # Product list (WooCommerce)
    ├── single-product.png   # Single product (WooCommerce)
    └── custom-post.png      # Custom post type design
```

## File Formats

### theme-spec.txt

Contains theme metadata and feature requirements:

```txt
# Theme Specification

## Basic Information
Theme Name: My Theme
Theme URI: https://example.com/my-theme
Author: Your Name
Author URI: https://example.com
Description: A modern WordPress theme
Version: 1.0.0
Requires PHP: 8.0
Text Domain: my-theme

## Features
- [x] Custom Logo
- [x] Custom Header
- [ ] Custom Background
- [x] Post Thumbnails
- [x] Custom Menus
- [x] Widget Areas
- [ ] WooCommerce Support
- [ ] Dark Mode

## Color Scheme
Primary: #0073aa
Secondary: #23282d
Accent: #00a0d2
Background: #ffffff
Text: #333333

## Typography
Heading Font: Inter
Body Font: Open Sans
Base Font Size: 16px
Line Height: 1.6

## Layout
Content Width: 1200px
Sidebar Position: right
Header Style: standard
Footer Columns: 3

## Custom Post Types
[Define any custom post types needed]

## Additional Requirements
[Any specific requirements]
```

### site-config.txt

Contains site configuration settings:

```txt
# Site Configuration

## Site Information
Site Title: My Website
Tagline: Just another WordPress site
Site URL: https://example.com

## Homepage Settings
Show on front: page
Front page template: default

## Blog Settings
Posts per page: 10
Excerpt length: 55
Featured image size: large

## Navigation
Primary Menu Items:
- Home
- About
- Services
- Blog
- Contact

Footer Menu Items:
- Privacy Policy
- Terms of Service
- Contact

## Widget Areas
- Primary Sidebar
- Footer 1
- Footer 2
- Footer 3

## Social Links
Facebook: https://facebook.com/mywebsite
Twitter: https://twitter.com/mywebsite
Instagram: https://instagram.com/mywebsite
LinkedIn: https://linkedin.com/company/mywebsite
```

### custom-post-types.txt

Defines custom post types:

```txt
# Custom Post Types

## Portfolio
Post Type Key: portfolio
Name: Portfolio Items
Singular Name: Portfolio Item
Description: Portfolio and project showcase
Public: true
Has Archive: true
Supports:
- title
- editor
- thumbnail
- excerpt
- custom-fields
Categories: portfolio_category
Tags: portfolio_tag

## Testimonials
Post Type Key: testimonial
Name: Testimonials
Singular Name: Testimonial
Description: Customer testimonials
Public: true
Has Archive: false
Supports:
- title
- editor
- thumbnail
```

### color-scheme.txt

Defines the color palette:

```txt
# Color Scheme

## Primary Colors
Primary: #0073aa
Primary Dark: #005a87
Primary Light: #00a0d2

## Secondary Colors
Secondary: #23282d
Secondary Dark: #191e23
Secondary Light: #32373c

## Accent Colors
Accent: #00a0d2
Accent Dark: #0073aa
Accent Light: #33b3db

## Background Colors
Background: #ffffff
Background Alt: #f7f7f7
Background Dark: #eee

## Text Colors
Text: #333333
Text Light: #666666
Text Dark: #1e1e1e

## Border Colors
Border: #ddd
Border Light: #eee
Border Dark: #ccc

## Status Colors
Success: #46b450
Warning: #ffb900
Error: #dc3232
Info: #00a0d2
```

### typography.txt

Defines typography settings:

```txt
# Typography Settings

## Fonts
Heading Font: Inter, -apple-system, BlinkMacSystemFont, sans-serif
Body Font: Open Sans, -apple-system, BlinkMacSystemFont, sans-serif
Mono Font: Menlo, Monaco, Consolas, monospace

## Font Sizes
Base Size: 16px
Small: 14px
Large: 18px
H1: 48px
H2: 36px
H3: 28px
H4: 24px
H5: 20px
H6: 18px

## Line Heights
Body Line Height: 1.6
Heading Line Height: 1.2

## Font Weights
Regular: 400
Medium: 500
Bold: 700
Heading: 600

## Letter Spacing
Body: 0
Heading: -0.02em
Buttons: 0.05em
```

## Screenshots

Reference screenshots should be placed in the `screenshots/` subdirectory:

- **homepage.png**: Full homepage design (recommended: 1920x1080)
- **page.png**: Standard page template design
- **single-post.png**: Single blog post design
- **archive.png**: Archive/category page design
- **product-list.png**: WooCommerce product list (if applicable)
- **single-product.png**: WooCommerce single product (if applicable)
- **custom-post.png**: Custom post type display (if applicable)

### Screenshot Guidelines

1. Use high-resolution images (minimum 1200px width)
2. Include full page layouts (header to footer)
3. Show responsive versions if available
4. Include hover states and interactions if relevant
5. Use realistic content, not placeholder text

## Usage

1. Copy the template files from `plans/input-templates/` to this directory
2. Fill in your theme specifications
3. Add your reference screenshots
4. Run the orchestration command:
   ```bash
   /wp-theme-orchestrate "Create a theme based on my specifications"
   ```

## Notes

- All input files are optional; the orchestrator will use defaults for missing files
- Screenshots are analyzed for design extraction
- WooCommerce support is auto-detected from theme-spec.txt and screenshots
- Custom post types are only created if defined in custom-post-types.txt