# WordPress Theme Orchestration Workflow

Complete WordPress theme creation workflow with multi-phase orchestration for production-ready themes.

## Task Description

$ARGUMENTS

## Output Directory

**IMPORTANT**: All theme files MUST be saved in the `wp-content/themes/` directory relative to the project root.

- Create the theme directory at: `wp-content/themes/{theme-name}/`
- All theme files (style.css, index.php, functions.php, etc.) go inside this directory
- Do NOT save files in any other location (examples/, current directory, etc.)

## Input Sources

Read input files from the following locations (if they exist):

### Required Input
- **User Description**: Provided via $ARGUMENTS above

### Optional Input Files (plans/input/)
- `plans/input/theme-spec.txt` - Detailed theme specifications
- `plans/input/site-config.txt` - Site configuration settings
- `plans/input/custom-post-types.txt` - Custom Post Type definitions
- `plans/input/color-scheme.txt` - Color palette specifications
- `plans/input/typography.txt` - Typography settings

### Reference Screenshots (plans/input/screenshots/)
- `homepage.png` - Homepage design reference
- `page.png` - Page template design
- `single-post.png` - Single post design
- `archive.png` - Archive template design
- `product-list.png` - Product list design (WooCommerce)
- `single-product.png` - Single product design (WooCommerce)
- `custom-post.png` - Custom post type design

## Workflow Phases

Execute the following phases sequentially:

---

## Phase 0: Preliminary - Data Collection

### Step 0.1: Parse User Description

Analyze the user description provided in $ARGUMENTS to extract:
- Theme name suggestion
- Theme purpose/type (blog, business, portfolio, e-commerce, etc.)
- Key features requested
- Design style hints (modern, classic, minimal, etc.)

### Step 0.2: Read Input Files

Read and parse all available input files from `plans/input/`:

1. **theme-spec.txt** (if exists):
   - Extract theme metadata (name, author, description)
   - Identify required features
   - Note color scheme preferences
   - Note typography preferences
   - Identify layout preferences

2. **site-config.txt** (if exists):
   - Extract site title and tagline
   - Homepage settings
   - Navigation menu items
   - Widget area requirements
   - Social links

3. **custom-post-types.txt** (if exists):
   - Parse CPT definitions
   - Extract labels, supports, and settings

4. **color-scheme.txt** (if exists):
   - Extract primary, secondary, accent colors
   - Background and text colors

5. **typography.txt** (if exists):
   - Font family preferences
   - Font sizes and line heights

### Step 0.3: Analyze Screenshots

For each screenshot in `plans/input/screenshots/`:
1. Read the image file
2. Analyze visual design elements:
   - Color palette used
   - Typography styles
   - Layout structure
   - Component patterns
   - Spacing and proportions
3. Document findings for design phase

### Step 0.4: Determine WooCommerce Requirement

Check if WooCommerce support is needed:
- Look for `WooCommerce Support: yes` in theme-spec.txt
- Check for product-related screenshots
- Analyze user description for e-commerce keywords

---

## Phase 1: Configuration and Structure

### Step 1.1: Generate Theme Metadata

Create `style.css` with proper theme metadata:

```css
/*
Theme Name: {Theme Name}
Theme URI: {Theme URI}
Author: {Author Name}
Author URI: {Author URI}
Description: {Theme Description}
Version: 1.0.0
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 8.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: {theme-name}
Tags: {relevant-tags}
*/
```

### Step 1.2: Create Core Files Structure

Create the following directory structure:

```
wp-content/themes/{theme-name}/
├── style.css                 # Theme metadata and main styles
├── index.php                 # Required fallback template
├── functions.php             # Theme setup and functionality
├── screenshot.png            # Theme preview (generate or placeholder)
├── assets/
│   ├── css/
│   │   ├── style.css         # Main compiled styles
│   │   ├── responsive.css    # Responsive breakpoints
│   │   └── editor-style.css  # Editor styles
│   ├── js/
│   │   └── main.js           # Main JavaScript
│   └── images/
│       └── logo.png          # Default logo placeholder
├── inc/
│   ├── setup.php             # Theme setup functions
│   ├── customizer.php        # Customizer configuration
│   ├── widgets.php           # Widget registration
│   ├── template-tags.php     # Custom template functions
│   └── template-functions.php # Helper functions
├── languages/
│   └── {theme-name}.pot      # Translation template
└── template-parts/
    ├── content.php           # Default content partial
    ├── content-page.php      # Page content
    ├── content-single.php    # Single post content
    └── content-none.php      # No content found
```

### Step 1.3: Implement Theme Setup

Create `functions.php` with:

1. **Constants Definition**:
   ```php
   define('{THEME_NAME}_VERSION', '1.0.0');
   define('{THEME_NAME}_DIR', get_template_directory());
   define('{THEME_NAME}_URI', get_template_directory_uri());
   ```

2. **Theme Setup Function** (hooked to `after_setup_theme`):
   - Load text domain
   - Add theme supports (title-tag, post-thumbnails, custom-logo, html5, etc.)
   - Register navigation menus
   - Add custom image sizes
   - Set content width

3. **Include Files**:
   - Require all inc/ files

### Step 1.4: Register Custom Post Types

If CPT definitions exist in input:

1. Create `inc/custom-post-types.php`
2. Register each CPT with proper:
   - Labels
   - Supports array
   - Public settings
   - Rewrite rules
   - Show in REST (for Gutenberg)

### Step 1.5: Create Widget Areas

Register widget areas based on site-config.txt:

1. Primary Sidebar
2. Footer Widget Areas (based on footer columns setting)
3. Any custom widget areas specified

---

## Phase 2: Design and Layout Development

### Step 2.1: Generate CSS Custom Properties

Based on screenshot analysis and color-scheme.txt:

```css
:root {
  /* Colors */
  --color-primary: {extracted-primary};
  --color-secondary: {extracted-secondary};
  --color-accent: {extracted-accent};
  --color-background: {extracted-background};
  --color-text: {extracted-text};
  --color-text-light: {extracted-text-light};
  --color-border: {extracted-border};
  
  /* Typography */
  --font-heading: '{extracted-heading-font}', sans-serif;
  --font-body: '{extracted-body-font}', sans-serif;
  --font-size-base: {extracted-base-size};
  --font-size-small: {extracted-small-size};
  --font-size-large: {extracted-large-size};
  --line-height: {extracted-line-height};
  
  /* Spacing */
  --spacing-unit: 8px;
  --spacing-xs: 4px;
  --spacing-sm: 8px;
  --spacing-md: 16px;
  --spacing-lg: 24px;
  --spacing-xl: 32px;
  
  /* Layout */
  --content-width: {extracted-content-width};
  --sidebar-width: 300px;
  --header-height: {extracted-header-height};
  
  /* Borders */
  --border-radius: {extracted-radius};
  --border-width: 1px;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.15);
}
```

### Step 2.2: Generate Template Files

Create all required template files based on screenshot analysis:

#### Header Template (header.php)
- Site logo/branding
- Primary navigation
- Mobile menu toggle
- Skip to content link

#### Footer Template (footer.php)
- Footer widget areas (columns based on config)
- Footer navigation
- Copyright information
- Social links

#### Homepage Template (front-page.php)
- Hero section (if applicable)
- Featured content sections
- Latest posts section
- Call to action sections

#### Single Post Template (single.php)
- Article structure matching screenshot
- Featured image display
- Meta information (date, author, categories)
- Content area
- Comments section

#### Page Template (page.php)
- Page title
- Content area
- Sidebar (if configured)

#### Archive Template (archive.php)
- Archive header (title, description)
- Posts loop
- Pagination

#### Search Template (search.php)
- Search form
- Results display
- No results message

#### 404 Template (404.php)
- Error message
- Search form
- Helpful links

### Step 2.3: Implement Responsive Design

Create `assets/css/responsive.css` with mobile-first breakpoints:

```css
/* Mobile First - Base styles for 0-576px */

/* Small devices (landscape phones, 576px and up) */
@media (min-width: 576px) {
  /* sm breakpoint styles */
}

/* Medium devices (tablets, 768px and up) */
@media (min-width: 768px) {
  /* md breakpoint styles */
}

/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) {
  /* lg breakpoint styles */
}

/* Extra large devices (large desktops, 1200px and up) */
@media (min-width: 1200px) {
  /* xl breakpoint styles */
}

/* Extra extra large devices (1400px and up) */
@media (min-width: 1400px) {
  /* xxl breakpoint styles */
}
```

### Step 2.4: Create Template Parts

Generate reusable template parts:

- `template-parts/content.php` - Default content display
- `template-parts/content-page.php` - Page content
- `template-parts/content-single.php` - Single post content
- `template-parts/content-none.php` - No content message
- `template-parts/entry-meta.php` - Post meta information
- `template-parts/featured-image.php` - Featured image display
- `template-parts/pagination.php` - Pagination links
- `template-parts/comments.php` - Comments section

### Step 2.5: Implement Customizer Options

Create `inc/customizer.php` with:

1. **Color Options**:
   - Primary color
   - Secondary color
   - Accent color
   - Background color

2. **Typography Options**:
   - Heading font
   - Body font
   - Base font size

3. **Layout Options**:
   - Sidebar position
   - Content width
   - Footer columns

4. **Feature Options**:
   - Show/hide featured images
   - Show/hide author info
   - Show/hide related posts

---

## Phase 3: Demo Content Implementation

### Step 3.1: Create Homepage

Create a complete homepage with:

1. **Hero Section**:
   - Compelling headline
   - Subheadline/description
   - Call to action button
   - Background image or gradient

2. **Featured Content**:
   - 3-4 featured items
   - Icons or images
   - Brief descriptions

3. **Latest Posts Section**:
   - 3-6 recent posts
   - Featured images
   - Excerpts
   - Read more links

4. **Additional Sections** (based on theme type):
   - Testimonials (business theme)
   - Portfolio items (portfolio theme)
   - Services overview
   - Contact information

### Step 3.2: Create Demo Page

Create a sample page demonstrating:

1. **Typography**:
   - All heading levels (h1-h6)
   - Paragraphs
   - Lists (ordered and unordered)
   - Blockquotes

2. **Media**:
   - Images with captions
   - Image gallery
   - Embedded video placeholder

3. **Layout Elements**:
   - Columns
   - Buttons
   - Cards

4. **Sidebar** (if applicable):
   - Widget examples
   - Search form
   - Recent posts widget

### Step 3.3: Create Demo Post

Create a sample blog post with:

1. **Content**:
   - Engaging title
   - Well-structured content (800-1200 words)
   - Multiple paragraphs
   - Images throughout

2. **Meta**:
   - Featured image
   - Categories (2-3)
   - Tags (3-5)
   - Author attribution

3. **Comments**:
   - 2-3 sample comments
   - Various comment types

### Step 3.4: Create Demo Categories and Tags

Create sample taxonomy data:
- 3-5 categories with descriptions
- 5-10 tags

---

## Phase 4: Finalization and Testing

### Step 4.1: Code Integrity Check

Verify all generated code:

1. **PHP Validation**:
   - No syntax errors
   - Proper escaping (esc_html, esc_attr, esc_url)
   - Input sanitization
   - Nonce verification where needed

2. **WordPress Standards**:
   - Proper hook usage
   - Correct function prefixes
   - Text domain consistency
   - DocBlocks for all functions

3. **Security Audit**:
   - No hardcoded credentials
   - Proper capability checks
   - Safe database queries
   - XSS prevention

### Step 4.2: Remove Placeholders

Ensure no placeholder content remains:

1. Replace all `TODO` comments with actual code
2. Remove placeholder text (Lorem ipsum, etc.)
3. Replace dummy image references with proper placeholder images or actual images
4. Remove any debug code (var_dump, print_r, console.log)
5. Remove any test data that shouldn't be in production

### Step 4.3: Create Documentation

Generate theme documentation:

1. **README.md**:
   - Theme description
   - Installation instructions
   - Configuration guide
   - Customizer options
   - Widget areas
   - Template hierarchy
   - Credits and license

2. **CHANGELOG.md**:
   - Initial version entry

3. **Screenshot**:
   - Generate or use homepage screenshot as screenshot.png (1200x900)

### Step 4.4: Final Testing Checklist

Verify the following:

- [ ] Theme activates without errors
- [ ] All templates render correctly
- [ ] Customizer options save and display properly
- [ ] Widget areas accept widgets
- [ ] Navigation menus display correctly
- [ ] Responsive design works at all breakpoints
- [ ] No PHP errors in debug mode
- [ ] Passes Theme Check plugin (if available)
- [ ] Accessibility: Skip link present
- [ ] Accessibility: Proper heading hierarchy
- [ ] Performance: No unnecessary queries
- [ ] All strings are translatable

---

## Phase 5: WooCommerce Integration (Optional)

**Execute this phase ONLY if WooCommerce support is detected.**

### Step 5.1: Add WooCommerce Support

In `functions.php`, add:

```php
/**
 * Add WooCommerce support
 */
function {theme_name}_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', '{theme_name}_woocommerce_support' );
```

### Step 5.2: Create WooCommerce Templates

Create WooCommerce template overrides:

```
woocommerce/
├── archive-product.php       # Shop/archive page
├── single-product.php        # Single product page
├── content-product.php       # Product loop item
├── content-single-product.php # Single product content
├── product-searchform.php    # Product search form
├── cart/
│   └── cart.php              # Cart page
│   └── cart-empty.php        # Empty cart
├── checkout/
│   └── form-checkout.php     # Checkout form
├── myaccount/
│   └── my-account.php        # Account page
└── global/
    └── wrapper-start.php     # Content wrapper start
    └── wrapper-end.php       # Content wrapper end
```

### Step 5.3: Add WooCommerce Styles

Create `assets/css/woocommerce.css`:

```css
/* WooCommerce Product Grid */
.woocommerce ul.products li.product {
    /* Styling based on product-list.png */
}

/* Single Product */
.woocommerce div.product {
    /* Styling based on single-product.png */
}

/* Product Gallery */
.woocommerce-product-gallery {
    /* Gallery styling */
}

/* Cart and Checkout */
.woocommerce-cart,
.woocommerce-checkout {
    /* Cart/checkout styling */
}

/* Responsive WooCommerce */
@media (max-width: 768px) {
    .woocommerce ul.products li.product {
        width: 100%;
    }
}
```

### Step 5.4: Create Demo Products

If WooCommerce is active, create:

1. **Product Categories**:
   - 3-5 categories with descriptions

2. **Demo Products** (3-5 products):
   - Product title and description
   - Product images
   - Regular and sale prices
   - Categories and tags
   - Short description
   - If applicable: variable product with variations

3. **Shop Page Configuration**:
   - Set shop page
   - Configure columns (typically 3-4)
   - Configure rows

### Step 5.5: WooCommerce Testing

Verify WooCommerce functionality:

- [ ] Shop page displays correctly
- [ ] Product grid matches design
- [ ] Single product page renders properly
- [ ] Product gallery functions (zoom, lightbox, slider)
- [ ] Add to cart works
- [ ] Cart page displays correctly
- [ ] Checkout flow works
- [ ] My Account pages display
- [ ] Responsive product grid
- [ ] WooCommerce widgets function

---

## Output Summary

After completing all phases, provide:

1. **Theme Location**: `wp-content/themes/{theme-name}/`
2. **Files Created**: List of all generated files
3. **Features Implemented**: Summary of theme features
4. **Demo Content**: Summary of created demo content
5. **Installation Instructions**: Step-by-step activation guide
6. **Customizer Options**: List of configurable options
7. **Known Issues**: Any limitations or notes

## Installation Instructions

Provide clear installation steps:

```markdown
## Installation

1. Copy the `{theme-name}` folder to `wp-content/themes/`
2. In WordPress Admin, go to Appearance > Themes
3. Click "Add New" and select the theme
4. Click "Activate"
5. Configure theme options in Appearance > Customize
6. Set up navigation menus in Appearance > Menus
7. Add widgets to widget areas in Appearance > Widgets

## Demo Content

The theme includes demo content:
- Homepage: [Page title]
- Sample Page: [Page title]
- Sample Post: [Post title]
- Categories: [List]
- Tags: [List]

## WooCommerce (if applicable)

1. Install and activate WooCommerce plugin
2. Configure WooCommerce settings
3. Demo products are available in the shop
```

## Notes

- Follow WordPress Theme Developer Handbook guidelines
- Ensure GPL-compatible license
- Use proper text domain for all translatable strings
- Implement accessibility best practices (WCAG 2.1 AA)
- Optimize for performance (minimal queries, lazy loading)
- Test with Theme Check plugin before release
- Use Theme Unit Test data for comprehensive testing

---

**Use the `theme-orchestrator` agent for detailed theme generation.**
**Use the `theme-reviewer` agent for final quality review.**