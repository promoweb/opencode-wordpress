<?php
/**
 * Theme functions and definitions
 *
 * @package Opencode_Theme_Example
 * @version 1.0.0
 */

if (!defined('OPENCODE_THEME_VERSION')) {
    define('OPENCODE_THEME_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
if (!function_exists('opencode_theme_setup')) :
    function opencode_theme_setup() {
        // Make theme available for translation
        load_theme_textdomain('opencode-theme-example', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');

        // Let WordPress manage the document title
        add_theme_support('title-tag');

        // Enable support for Post Thumbnails
        add_theme_support('post-thumbnails');

        // Add custom image sizes
        add_image_size('opencode-featured', 1200, 600, true);
        add_image_size('opencode-thumbnail', 400, 300, true);
        add_image_size('opencode-square', 300, 300, true);

        // Custom logo support
        add_theme_support('custom-logo', array(
            'height'      => 250,
            'width'       => 250,
            'flex-height' => true,
            'flex-width'  => true,
        ));

        // HTML5 support
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ));

        // Editor style support
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');

        // Wide and full alignment support
        add_theme_support('align-wide');

        // Block styles support
        add_theme_support('wp-block-styles');

        // Responsive embeds support
        add_theme_support('responsive-embeds');

        // Custom spacing support
        add_theme_support('custom-spacing');

        // Selective refresh for widgets
        add_theme_support('customize-selective-refresh-widgets');

        // Register navigation menus
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'opencode-theme-example'),
            'footer'  => __('Footer Menu', 'opencode-theme-example'),
            'social'  => __('Social Links Menu', 'opencode-theme-example'),
        ));

        // Set up the WordPress core custom background feature
        add_theme_support('custom-background', array(
            'default-color' => 'ffffff',
        ));
    }
endif;
add_action('after_setup_theme', 'opencode_theme_setup');

/**
 * Set the content width in pixels
 */
function opencode_theme_content_width() {
    $GLOBALS['content_width'] = apply_filters('opencode_theme_content_width', 1200);
}
add_action('after_setup_theme', 'opencode_theme_content_width', 0);

/**
 * Register widget areas
 */
function opencode_theme_widgets_init() {
    // Primary Sidebar
    register_sidebar(array(
        'name'          => __('Primary Sidebar', 'opencode-theme-example'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'opencode-theme-example'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Footer Widget Area 1
    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'opencode-theme-example'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here for footer column 1.', 'opencode-theme-example'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Footer Widget Area 2
    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'opencode-theme-example'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets here for footer column 2.', 'opencode-theme-example'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Footer Widget Area 3
    register_sidebar(array(
        'name'          => __('Footer Widget Area 3', 'opencode-theme-example'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets here for footer column 3.', 'opencode-theme-example'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'opencode_theme_widgets_init');

/**
 * Enqueue scripts and styles
 */
function opencode_theme_scripts() {
    // Main stylesheet
    wp_enqueue_style('opencode-theme-style', get_stylesheet_uri(), array(), OPENCODE_THEME_VERSION);

    // Custom CSS
    wp_enqueue_style('opencode-theme-main', get_template_directory_uri() . '/assets/css/style.css', array('opencode-theme-style'), OPENCODE_THEME_VERSION);

    // Responsive CSS
    wp_enqueue_style('opencode-theme-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array('opencode-theme-main'), OPENCODE_THEME_VERSION);

    // Main JavaScript
    wp_enqueue_script('opencode-theme-main', get_template_directory_uri() . '/assets/js/main.js', array(), OPENCODE_THEME_VERSION, true);

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'opencode_theme_scripts');

/**
 * Implement the Custom Header feature.
 */
// require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Performance optimizations
 */

// Add preload for critical assets
function opencode_theme_preload_assets() {
    // Preload main stylesheet
    echo '<link rel="preload" href="' . esc_url(get_stylesheet_uri()) . '" as="style">';
}
add_action('wp_head', 'opencode_theme_preload_assets', 1);

// Enable native lazy loading
add_filter('wp_lazy_loading_enabled', '__return_true');

// Disable emoji scripts
function opencode_theme_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'opencode_theme_disable_emojis');

// Remove query strings from static resources
function opencode_theme_remove_script_version($src) {
    $parts = explode('?', $src);
    return $parts[0];
}
// Uncomment to remove query strings (not recommended for development)
// add_filter('script_loader_src', 'opencode_theme_remove_script_version', 15, 1);
// add_filter('style_loader_src', 'opencode_theme_remove_script_version', 15, 1);

// Limit post revisions
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

/**
 * Modify excerpt length
 */
function opencode_theme_excerpt_length($length) {
    return absint(get_theme_mod('home_excerpt_length', 55));
}
add_filter('excerpt_length', 'opencode_theme_excerpt_length');


