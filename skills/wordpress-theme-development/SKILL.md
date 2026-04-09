name
wordpress-theme-development

description
WordPress theme architecture, hierarchy, templates, functions.php organization, hooks, Customizer API, widgets, and performance patterns for production-grade themes.

origin
OpenCode WordPress

# WordPress Theme Development

Production-grade WordPress theme patterns for scalable, maintainable theme development.

## When to Use

- Building WordPress themes from scratch
- Creating custom theme templates
- Implementing Customizer options
- Setting up widget areas
- Organizing functions.php
- Adding theme hooks and filters
- Optimizing theme performance

## How It Works

- Structure theme around clear hierarchy (templates -> partials -> functions)
- Use WordPress hooks for theme setup (wp_enqueue_scripts, after_setup_theme)
- Organize functions.php by concern (setup, assets, customizer, widgets)
- Follow WordPress Template Hierarchy for template selection
- Implement Customizer for user-configurable options
- Optimize assets with proper enqueuing and lazy loading

## Examples

### Theme Directory Structure

```
my-theme/
├── style.css                 # Theme metadata and main styles
├── index.php                 # Required fallback template
├── functions.php             # Theme setup and functionality
├── screenshot.png            # Theme preview image (1200x900)
├── assets/
│   ├── css/
│   │   ├── editor-style.css  # Editor styles
│   │   └── custom.css        # Additional styles
│   ├── js/
│   │   ├── theme.js          # Main theme JavaScript
│   │   └── navigation.js     # Navigation script
│   └── images/
│       ├── logo.png
│       └── icons/
├── inc/
│   ├── setup.php             # Theme setup functions
│   ├── customizer.php        # Customizer configuration
│   ├── widgets.php           # Widget registration
│   ├── template-tags.php     # Custom template functions
│   └── extras.php            # Additional functionality
├── templates/
│   ├── header.php
│   ├── footer.php
│   ├── sidebar.php
│   ├── content.php           # Default content partial
│   ├── content-single.php    # Single post content
│   ├── content-page.php      # Page content
│   ├── content-none.php      # No content found
│   ├── page.php              # Default page template
│   ├── single.php            # Single post template
│   ├── archive.php           # Archive template
│   ├── search.php            # Search results template
│   ├── 404.php               # 404 error template
│   └── template-parts/
│       ├── featured-image.php
│       ├── entry-meta.php
│       ├── comments.php
│       └── pagination.php
└── languages/
    └── my-theme.pot          # Translation template
```

### Theme Metadata (style.css)

```css
/*
Theme Name: My Theme
Theme URI: https://example.com/my-theme
Author: Your Name
Author URI: https://example.com
Description: A modern WordPress theme with Customizer support.
Version: 1.0.0
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 8.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: my-theme
Tags: one-column, custom-colors, custom-logo, custom-menu, featured-images
*/
```

### functions.php Organization

```php
<?php
/**
 * My Theme Functions and Definitions
 *
 * @package My_Theme
 * @version 1.0.0
 */

if (!defined('MY_THEME_VERSION')) {
    define('MY_THEME_VERSION', '1.0.0');
}

if (!defined('MY_THEME_DIR')) {
    define('MY_THEME_DIR', get_template_directory());
}

if (!defined('MY_THEME_URI')) {
    define('MY_THEME_URI', get_template_directory_uri());
}

/**
 * Theme Setup
 */
require_once MY_THEME_DIR . '/inc/setup.php';

/**
 * Customizer Configuration
 */
require_once MY_THEME_DIR . '/inc/customizer.php';

/**
 * Widget Registration
 */
require_once MY_THEME_DIR . '/inc/widgets.php';

/**
 * Custom Template Tags
 */
require_once MY_THEME_DIR . '/inc/template-tags.php';

/**
 * Additional Functionality
 */
require_once MY_THEME_DIR . '/inc/extras.php';
```

### Theme Setup (inc/setup.php)

```php
<?php
/**
 * Theme Setup Functions
 *
 * @package My_Theme
 */

if (!function_exists('my_theme_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    function my_theme_setup(): void {
        // Make theme available for translation
        load_theme_textdomain('my-theme', MY_THEME_DIR . '/languages');

        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');

        // Let WordPress manage the document title
        add_theme_support('title-tag');

        // Enable support for Post Thumbnails on posts and pages
        add_theme_support('post-thumbnails');

        // Add custom image sizes
        add_image_size('my-theme-featured', 1200, 600, true);
        add_image_size('my-theme-thumbnail', 400, 300, true);
        add_image_size('my-theme-square', 200, 200, true);

        // Register navigation menus
        register_nav_menus([
            'primary'   => __('Primary Menu', 'my-theme'),
            'footer'    => __('Footer Menu', 'my-theme'),
            'social'    => __('Social Links Menu', 'my-theme'),
        ]);

        // Switch default core markup to output valid HTML5
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);

        // Enable custom logo support
        add_theme_support('custom-logo', [
            'height'      => 250,
            'width'       => 250,
            'flex-height' => true,
            'flex-width'  => true,
        ]);

        // Enable selective refresh for widgets in Customizer
        add_theme_support('customize-selective-refresh-widgets');

        // Add support for editor styles
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');

        // Add support for wide and full alignment
        add_theme_support('align-wide');

        // Add support for responsive embeds
        add_theme_support('responsive-embeds');

        // Add support for custom spacing
        add_theme_support('custom-spacing');

        // Set up the WordPress core custom background feature
        add_theme_support('custom-background', [
            'default-color' => 'ffffff',
        ]);
    }
endif;

add_action('after_setup_theme', 'my_theme_setup');

/**
 * Set the content width in pixels
 */
function my_theme_content_width(): void {
    $GLOBALS['content_width'] = apply_filters('my_theme_content_width', 1200);
}

add_action('after_setup_theme', 'my_theme_content_width', 0);
```

### Enqueue Scripts and Styles

```php
<?php
/**
 * Enqueue scripts and styles.
 */
function my_theme_scripts(): void {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'my-theme-style',
        get_stylesheet_uri(),
        [],
        MY_THEME_VERSION
    );

    // Enqueue custom CSS
    wp_enqueue_style(
        'my-theme-custom',
        MY_THEME_URI . '/assets/css/custom.css',
        ['my-theme-style'],
        MY_THEME_VERSION
    );

    // Enqueue navigation JavaScript
    wp_enqueue_script(
        'my-theme-navigation',
        MY_THEME_URI . '/assets/js/navigation.js',
        [],
        MY_THEME_VERSION,
        true
    );

    // Enqueue main theme JavaScript
    wp_enqueue_script(
        'my-theme-main',
        MY_THEME_URI . '/assets/js/theme.js',
        ['my-theme-navigation'],
        MY_THEME_VERSION,
        true
    );

    // Localize script for AJAX (if needed)
    wp_localize_script('my-theme-main', 'myThemeData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('my_theme_nonce'),
    ]);

    // Comment reply script (only on single posts with comments open)
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('wp_enqueue_scripts', 'my_theme_scripts');

/**
 * Enqueue block editor scripts and styles.
 */
function my_theme_editor_scripts(): void {
    wp_enqueue_script(
        'my-theme-editor',
        MY_THEME_URI . '/assets/js/editor.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
        MY_THEME_VERSION,
        true
    );
}

add_action('enqueue_block_editor_assets', 'my_theme_editor_scripts');
```

### Customizer Configuration (inc/customizer.php)

```php
<?php
/**
 * Customizer Configuration
 *
 * @package My_Theme
 */

class My_Theme_Customizer {
    /**
     * Initialize customizer settings
     */
    public static function init(): void {
        add_action('customize_register', [self::class, 'register_settings']);
        add_action('customize_preview_init', [self::class, 'preview_js']);
    }

    /**
     * Register customizer settings and controls
     */
    public static function register_settings(WP_Customize_Manager $wp_customize): void {
        // Theme Options Section
        $wp_customize->add_section('my_theme_options', [
            'title'    => __('Theme Options', 'my-theme'),
            'priority' => 30,
        ]);

        // Primary Color Setting
        $wp_customize->add_setting('primary_color', [
            'default'           => '#0073aa',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ]);

        $wp_customize->add_control(new WP_Customize_Color_Control(
            $wp_customize,
            'primary_color',
            [
                'label'   => __('Primary Color', 'my-theme'),
                'section' => 'my_theme_options',
            ]
        ));

        // Show Featured Images Setting
        $wp_customize->add_setting('show_featured_images', [
            'default'           => true,
            'sanitize_callback' => 'my_theme_sanitize_checkbox',
            'transport'         => 'postMessage',
        ]);

        $wp_customize->add_control('show_featured_images', [
            'type'    => 'checkbox',
            'label'   => __('Show Featured Images', 'my-theme'),
            'section' => 'my_theme_options',
        ]);

        // Social Links Section
        $wp_customize->add_section('my_theme_social', [
            'title'    => __('Social Links', 'my-theme'),
            'priority' => 35,
        ]);

        // Social links settings (example for each platform)
        $social_links = ['facebook', 'twitter', 'instagram', 'linkedin'];

        foreach ($social_links as $social) {
            $wp_customize->add_setting('social_' . $social, [
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            ]);

            $wp_customize->add_control('social_' . $social, [
                'label'   => sprintf(__('%s URL', 'my-theme'), ucfirst($social)),
                'section' => 'my_theme_social',
                'type'    => 'url',
            ]);
        }
    }

    /**
     * Sanitize checkbox
     */
    public static function sanitize_checkbox(bool $checked): bool {
        return (bool) $checked;
    }

    /**
     * Enqueue preview JavaScript
     */
    public static function preview_js(): void {
        wp_enqueue_script(
            'my-theme-customize-preview',
            MY_THEME_URI . '/assets/js/customize-preview.js',
            ['customize-preview'],
            MY_THEME_VERSION,
            true
        );
    }
}

My_Theme_Customizer::init();
```

### Widget Registration (inc/widgets.php)

```php
<?php
/**
 * Widget Registration
 *
 * @package My_Theme
 */

class My_Theme_Widgets {
    /**
     * Initialize widget areas
     */
    public static function init(): void {
        add_action('widgets_init', [self::class, 'register_sidebars']);
    }

    /**
     * Register widget areas (sidebars)
     */
    public static function register_sidebars(): void {
        // Primary Sidebar
        register_sidebar([
            'name'          => __('Primary Sidebar', 'my-theme'),
            'id'            => 'sidebar-1',
            'description'   => __('Add widgets here to appear in your sidebar.', 'my-theme'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);

        // Footer Widget Area 1
        register_sidebar([
            'name'          => __('Footer Widget Area 1', 'my-theme'),
            'id'            => 'footer-1',
            'description'   => __('Add widgets here for footer column 1.', 'my-theme'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);

        // Footer Widget Area 2
        register_sidebar([
            'name'          => __('Footer Widget Area 2', 'my-theme'),
            'id'            => 'footer-2',
            'description'   => __('Add widgets here for footer column 2.', 'my-theme'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);

        // Footer Widget Area 3
        register_sidebar([
            'name'          => __('Footer Widget Area 3', 'my-theme'),
            'id'            => 'footer-3',
            'description'   => __('Add widgets here for footer column 3.', 'my-theme'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
    }
}

My_Theme_Widgets::init();
```

### Template Hierarchy Usage

WordPress template hierarchy determines which template file to use based on the content type:

```
┌─────────────────────────────────────────────────────────────┐
│ Template Hierarchy Flow                                      │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ Single Post:                                                 │
│   single-{post-type}-{slug}.php                             │
│   single-{post-type}.php                                     │
│   single.php                                                 │
│   singular.php                                               │
│   index.php                                                  │
│                                                              │
│ Page:                                                        │
│   page-{slug}.php                                            │
│   page-{id}.php                                              │
│   page.php                                                   │
│   singular.php                                               │
│   index.php                                                  │
│                                                              │
│ Category Archive:                                            │
│   category-{slug}.php                                        │
│   category-{id}.php                                          │
│   category.php                                               │
│   archive.php                                                │
│   index.php                                                  │
│                                                              │
│ Author Archive:                                              │
│   author-{nicename}.php                                      │
│   author-{id}.php                                            │
│   author.php                                                 │
│   archive.php                                                │
│   index.php                                                  │
│                                                              │
│ Date Archive:                                                │
│   date.php                                                   │
│   archive.php                                                │
│   index.php                                                  │
│                                                              │
│ Search Results:                                              │
│   search.php                                                 │
│   index.php                                                  │
│                                                              │
│ 404 Error:                                                   │
│   404.php                                                    │
│   index.php                                                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Template Part Usage

```php
<?php
// In single.php or archive.php

// Load content partial
get_template_part('templates/content', get_post_type());

// Load specific content partial for single posts
get_template_part('templates/content', 'single');

// Load featured image partial
get_template_part('templates/template-parts/featured-image');

// Load entry meta partial
get_template_part('templates/template-parts/entry-meta');

// Load pagination partial
get_template_part('templates/template-parts/pagination');
```

### Custom Template Tags (inc/template-tags.php)

```php
<?php
/**
 * Custom Template Tags
 *
 * @package My_Theme
 */

if (!function_exists('my_theme_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function my_theme_posted_on(): void {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        printf(
            '<span class="posted-on">%s</span>',
            $time_string
        );
    }
endif;

if (!function_exists('my_theme_posted_by')) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function my_theme_posted_by(): void {
        printf(
            '<span class="byline"><span class="author vcard"><a class="url fn n" href="%s">%s</a></span></span>',
            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
            esc_html(get_the_author())
        );
    }
endif;

if (!function_exists('my_theme_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function my_theme_entry_footer(): void {
        // Hide category and tag text for pages
        if ('post' === get_post_type()) {
            // Categories
            $categories_list = get_the_category_list(', ');
            if ($categories_list) {
                printf('<span class="cat-links">%s</span>', $categories_list);
            }

            // Tags
            $tags_list = get_the_tag_list('', ', ');
            if ($tags_list) {
                printf('<span class="tags-links">%s</span>', $tags_list);
            }
        }

        // Comments
        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'my-theme'),
                        ['span' => ['class' => []]]
                    ),
                    wp_kses_post(get_the_title())
                )
            );
            echo '</span>';
        }

        // Edit link
        edit_post_link(
            sprintf(
                wp_kses(
                    __('Edit<span class="screen-reader-text"> %s</span>', 'my-theme'),
                    ['span' => ['class' => []]]
                ),
                wp_kses_post(get_the_title())
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;
```

### Performance Optimization

```php
<?php
/**
 * Performance Optimization
 */

// Lazy load images (native WordPress support)
add_filter('wp_lazy_loading_enabled', '__return_true');

// Preload critical assets
function my_theme_preload_assets(): void {
    // Preload main stylesheet
    echo '<link rel="preload" href="' . esc_url(get_stylesheet_uri()) . '" as="style">';
    
    // Preload fonts if needed
    // echo '<link rel="preload" href="' . esc_url(MY_THEME_URI . '/assets/fonts/main.woff2') . '" as="font" type="font/woff2" crossorigin>';
}

add_action('wp_head', 'my_theme_preload_assets', 1);

// Remove unnecessary emoji scripts
function my_theme_disable_emojis(): void {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}

add_action('init', 'my_theme_disable_emojis');

// Disable WordPress embeds
function my_theme_disable_embeds(): void {
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
}

add_action('init', 'my_theme_disable_embeds');

// Limit post revisions to reduce database size
define('WP_POST_REVISIONS', 3);
```

## Theme Best Practices

### Security

- Escape all output: `esc_html()`, `esc_attr()`, `esc_url()`
- Sanitize inputs: `sanitize_text_field()`, `sanitize_email()`
- Use nonces for forms: `wp_nonce_field()`, `wp_verify_nonce()`
- Check capabilities: `current_user_can()`

### Accessibility

- Provide meaningful alt text for images
- Use semantic HTML elements
- Ensure sufficient color contrast
- Make navigation keyboard-friendly
- Use ARIA labels where needed

### Responsive Design

- Use responsive CSS (media queries)
- Implement mobile-first approach
- Test on various devices and screen sizes
- Use responsive images (`wp_get_attachment_image()`)

### Maintainability

- Organize code by concern (inc/ directory)
- Use consistent naming conventions
- Document functions with DocBlocks
- Keep functions.php thin (use require_once)
- Use template parts for reusable components

### Performance

- Optimize images before upload
- Use appropriate image sizes
- Lazy load images and videos
- Minimize HTTP requests
- Cache expensive operations
- Use transients for temporary data

## Theme Orchestration Workflow

The orchestration workflow enables automated theme generation from user specifications and design references.

### Workflow Phases

```
┌─────────────────────────────────────────────────────────────────────────┐
│ Theme Orchestration Workflow                                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│ Phase 0: Preliminary                                                     │
│   ├── Collect project description from user                              │
│   ├── Import .txt files (theme-spec, site-config, custom-post-types)    │
│   └── Analyze screenshot images for design extraction                    │
│                                                                          │
│ Phase 1: Configuration & Structure                                       │
│   ├── Create theme directory structure                                   │
│   ├── Generate style.css with metadata                                   │
│   ├── Set up functions.php with required includes                        │
│   └── Register custom post types (if specified)                          │
│                                                                          │
│ Phase 2: Design & Layout Development                                     │
│   ├── Generate header.php and footer.php                                 │
│   ├── Create index.php and singular.php templates                        │
│   ├── Build archive and single templates                                 │
│   ├── Implement responsive CSS with media queries                         │
│   └── Create template parts for reusable components                      │
│                                                                          │
│ Phase 3: Demo Content Implementation                                      │
│   ├── Create homepage template with sections                             │
│   ├── Generate sample page content                                       │
│   ├── Create sample post with featured image                             │
│   └── Set up navigation menus and widgets                                │
│                                                                          │
│ Phase 4: Finalization & Testing                                          │
│   ├── Validate code integrity                                            │
│   ├── Remove all placeholder content                                     │
│   ├── Test theme functionality                                           │
│   └── Generate installation instructions                                  │
│                                                                          │
│ Phase 5: WooCommerce Integration (Optional)                              │
│   ├── Add WooCommerce theme support                                      │
│   ├── Create WooCommerce templates                                       │
│   ├── Generate sample products                                           │
│   └── Test shop functionality                                            │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

### Screenshot Analysis Patterns

When analyzing design screenshots, extract the following information:

```php
/**
 * Design Extraction from Screenshots
 * 
 * Analyze reference images to extract:
 * 
 * 1. Color Palette
 *    - Primary color (main brand color)
 *    - Secondary color (accent color)
 *    - Background colors (light/dark variants)
 *    - Text colors (headings, body, muted)
 *    - Border and divider colors
 * 
 * 2. Typography
 *    - Heading font family
 *    - Body font family
 *    - Font sizes (h1-h6, body, small)
 *    - Font weights (regular, medium, bold)
 *    - Line heights and letter spacing
 * 
 * 3. Layout Structure
 *    - Header layout (logo position, navigation style)
 *    - Content width and sidebar position
 *    - Footer columns and widget areas
 *    - Spacing patterns (margins, padding)
 * 
 * 4. Component Styles
 *    - Button styles (primary, secondary, outline)
 *    - Card styles (shadows, borders, radius)
 *    - Form input styles
 *    - Navigation menu styles
 * 
 * 5. Responsive Breakpoints
 *    - Mobile layout changes
 *    - Tablet adjustments
 *    - Desktop variations
 */
```

### Demo Content Generation Patterns

```php
<?php
/**
 * Demo Content Generation
 * 
 * Patterns for creating realistic demo content.
 */

/**
 * Homepage Sections
 */
function theme_generate_homepage_sections(): array {
    return [
        'hero' => [
            'type' => 'hero',
            'title' => 'Welcome to Our Website',
            'subtitle' => 'Discover amazing features and services',
            'button_text' => 'Get Started',
            'button_link' => '#',
            'background' => 'hero-bg.jpg',
        ],
        'features' => [
            'type' => 'features',
            'title' => 'Our Features',
            'columns' => 3,
            'items' => [
                ['icon' => 'dashicons-admin-users', 'title' => 'Feature One', 'description' => 'Description here'],
                ['icon' => 'dashicons-admin-settings', 'title' => 'Feature Two', 'description' => 'Description here'],
                ['icon' => 'dashicons-admin-appearance', 'title' => 'Feature Three', 'description' => 'Description here'],
            ],
        ],
        'cta' => [
            'type' => 'call-to-action',
            'title' => 'Ready to Get Started?',
            'button_text' => 'Contact Us',
            'button_link' => '/contact',
        ],
    ];
}

/**
 * Sample Page Content
 */
function theme_generate_sample_page(): array {
    return [
        'title' => 'About Us',
        'slug' => 'about',
        'content' => '
            <!-- wp:heading {"level":2} -->
            <h2>Our Story</h2>
            <!-- /wp:heading -->
            
            <!-- wp:paragraph -->
            <p>This is a sample page demonstrating the theme capabilities. Replace this content with your actual page content.</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:columns -->
            <div class="wp-block-columns">
                <!-- wp:column -->
                <div class="wp-block-column">
                    <!-- wp:heading {"level":3} -->
                    <h3>Our Mission</h3>
                    <!-- /wp:heading -->
                    <!-- wp:paragraph -->
                    <p>Mission statement goes here.</p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:column -->
                
                <!-- wp:column -->
                <div class="wp-block-column">
                    <!-- wp:heading {"level":3} -->
                    <h3>Our Vision</h3>
                    <!-- /wp:heading -->
                    <!-- wp:paragraph -->
                    <p>Vision statement goes here.</p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->
        ',
    ];
}

/**
 * Sample Post Content
 */
function theme_generate_sample_post(): array {
    return [
        'title' => 'Hello World! Welcome to Our Site',
        'slug' => 'hello-world',
        'content' => '
            <!-- wp:paragraph -->
            <p>This is your first post. It demonstrates the theme blog capabilities including:</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:list -->
            <ul>
                <li>Featured image display</li>
                <li>Post meta information</li>
                <li>Content formatting</li>
                <li>Comments integration</li>
            </ul>
            <!-- /wp:list -->
            
            <!-- wp:quote -->
            <blockquote class="wp-block-quote"><p>A great theme makes content shine.</p></blockquote>
            <!-- /wp:quote -->
        ',
        'categories' => ['News'],
        'tags' => ['welcome', 'first-post'],
        'featured_image' => 'sample-featured.jpg',
    ];
}
```

### Custom Post Type Registration Pattern

```php
<?php
/**
 * Custom Post Type Registration
 * 
 * Pattern for registering CPTs from user specifications.
 */

class Theme_Custom_Post_Types {
    
    private static $instance = null;
    private $post_types = [];
    
    public static function get_instance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function register(string $slug, array $args): void {
        $this->post_types[$slug] = $args;
    }
    
    public function init(): void {
        add_action('init', [$this, 'register_post_types']);
    }
    
    public function register_post_types(): void {
        foreach ($this->post_types as $slug => $args) {
            $defaults = [
                'labels' => [
                    'name' => $args['plural_name'] ?? ucfirst($slug),
                    'singular_name' => $args['singular_name'] ?? ucfirst($slug),
                    'add_new' => sprintf('Add New %s', $args['singular_name'] ?? ucfirst($slug)),
                    'add_new_item' => sprintf('Add New %s', $args['singular_name'] ?? ucfirst($slug)),
                    'edit_item' => sprintf('Edit %s', $args['singular_name'] ?? ucfirst($slug)),
                    'view_item' => sprintf('View %s', $args['singular_name'] ?? ucfirst($slug)),
                    'search_items' => sprintf('Search %s', $args['plural_name'] ?? ucfirst($slug)),
                ],
                'public' => true,
                'has_archive' => true,
                'publicly_queryable' => true,
                'query_var' => true,
                'rewrite' => ['slug' => $slug],
                'capability_type' => 'post',
                'hierarchical' => false,
                'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
                'menu_position' => 5,
                'menu_icon' => $args['menu_icon'] ?? 'dashicons-admin-post',
                'show_in_rest' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_in_admin_bar' => true,
            ];
            
            register_post_type($slug, wp_parse_args($args, $defaults));
        }
    }
}

// Usage from specification file:
// $cpt = Theme_Custom_Post_Types::get_instance();
// $cpt->register('portfolio', [
//     'singular_name' => 'Portfolio Item',
//     'plural_name' => 'Portfolio',
//     'menu_icon' => 'dashicons-portfolio',
//     'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
// ]);
// $cpt->init();
```

### WooCommerce Theme Support Pattern

```php
<?php
/**
 * WooCommerce Theme Support
 * 
 * Add WooCommerce support to theme.
 */

function theme_woocommerce_support(): void {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}

add_action('after_setup_theme', 'theme_woocommerce_support');

/**
 * WooCommerce Template Hooks
 */
function theme_woocommerce_setup(): void {
    // Remove default WooCommerce wrappers
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    
    // Add theme wrappers
    add_action('woocommerce_before_main_content', 'theme_woocommerce_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'theme_woocommerce_wrapper_end', 10);
}

add_action('wp', 'theme_woocommerce_setup');

function theme_woocommerce_wrapper_start(): void {
    echo '<main id="primary" class="site-main woocommerce-site-main">';
    echo '<div class="container">';
}

function theme_woocommerce_wrapper_end(): void {
    echo '</div>';
    echo '</main>';
}

/**
 * WooCommerce Archive Columns
 */
function theme_woocommerce_loop_columns(): int {
    return 3; // 3 products per row
}

add_filter('loop_shop_columns', 'theme_woocommerce_loop_columns');

/**
 * WooCommerce Related Products Columns
 */
function theme_related_products_args(array $args): array {
    $args['columns'] = 3;
    $args['posts_per_page'] = 3;
    return $args;
}

add_filter('woocommerce_related_products_columns', 'theme_related_products_args');
add_filter('woocommerce_output_related_products_args', 'theme_related_products_args');
```

### Theme Directory Generation Pattern

```
theme-name/
├── style.css                    # Theme metadata + base styles
├── index.php                    # Required fallback template
├── functions.php                # Theme setup and includes
├── screenshot.png               # Theme preview (1200x900)
├── assets/
│   ├── css/
│   │   ├── base.css            # Reset/normalize
│   │   ├── components.css      # Buttons, cards, forms
│   │   ├── layout.css          # Grid, containers
│   │   ├── responsive.css      # Media queries
│   │   └── woocommerce.css     # WooCommerce styles (if needed)
│   ├── js/
│   │   ├── main.js             # Main theme JavaScript
│   │   └── navigation.js       # Mobile navigation
│   └── images/
│       └── logo.png
├── inc/
│   ├── setup.php               # Theme setup functions
│   ├── customizer.php          # Customizer settings
│   ├── widgets.php             # Widget registration
│   ├── template-tags.php       # Custom template functions
│   ├── cpt.php                 # Custom post types
│   └── woocommerce.php         # WooCommerce integration (if needed)
├── template-parts/
│   ├── content.php             # Default content partial
│   ├── content-page.php        # Page content partial
│   ├── content-single.php      # Single post partial
│   ├── content-none.php       # No content found
│   ├── header/
│   │   ├── header-main.php     # Main header
│   │   └── header-mobile.php   # Mobile header
│   ├── footer/
│   │   └── footer-main.php     # Main footer
│   └── components/
│       ├── hero.php            # Hero section
│       ├── features.php        # Features section
│       ├── cta.php             # Call to action
│       └── testimonials.php    # Testimonials section
├── templates/
│   ├── homepage.php            # Homepage template
│   ├── full-width.php          # Full width template
│   └── contact.php             # Contact page template
├── woocommerce/                # WooCommerce templates (if needed)
│   ├── archive-product.php
│   ├── single-product.php
│   └── content-product.php
├── header.php
├── footer.php
├── sidebar.php
├── singular.php
├── single.php
├── page.php
├── archive.php
├── search.php
├── 404.php
└── languages/
    └── theme-name.pot          # Translation template
```

## Reference

- WordPress Theme Developer Handbook: https://developer.wordpress.org/themes/
- WordPress Template Hierarchy: https://developer.wordpress.org/themes/basics/template-hierarchy/
- WordPress Customizer API: https://developer.wordpress.org/themes/customize-api/
- WordPress Coding Standards: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/
- WooCommerce Theme Developer Handbook: https://developer.woocommerce.com/themes/

**Remember**: Great themes are organized, secure, accessible, and performant. Follow the hierarchy, use template parts, and keep functions modular.