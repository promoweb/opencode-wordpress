name
wordpress-plugin-development

description
WordPress plugin architecture, structure, hooks system, Settings API, meta boxes, shortcodes, custom post types, database operations, and admin integration patterns for production-grade plugins.

origin
OpenCode WordPress

# WordPress Plugin Development

Production-grade WordPress plugin patterns for scalable, maintainable plugin development.

## When to Use

- Building WordPress plugins from scratch
- Creating custom post types and taxonomies
- Implementing Settings API pages
- Adding meta boxes to posts
- Creating shortcodes
- Working with WordPress database
- Adding admin menu pages
- Handling AJAX requests
- Extending WordPress REST API

## How It Works

- Structure plugin around clear classes (main class -> includes -> classes)
- Use WordPress hooks for plugin integration (actions and filters)
- Organize functionality by concern (admin, public, settings, database)
- Follow WordPress Settings API for admin pages
- Use meta boxes for post metadata management
- Implement shortcodes with proper validation
- Database operations with wpdb and prepared statements
- AJAX via admin-ajax.php or REST API endpoints

## Examples

### Plugin Directory Structure

```
my-plugin/
├── my-plugin.php             # Main plugin file (bootstrap)
├── readme.txt                # Plugin readme for WP.org
├── uninstall.php             # Uninstall handler
├── assets/
│   ├── css/
│   │   ├── admin.css        # Admin styles
│   │   └── public.css       # Public styles
│   ├── js/
│   │   ├── admin.js         # Admin scripts
│   │   ├── public.js        # Public scripts
│   └── images/
│       ├── icon.png
│       └── banner-772x250.jpg
├── includes/
│   ├── class-my-plugin.php   # Main plugin class
│   ├── class-admin.php       # Admin functionality
│   ├── class-public.php      # Public functionality
│   ├── class-settings.php    # Settings API wrapper
│   ├── class-ajax.php        # AJAX handler
│   ├── class-post-type.php   # Custom post types
│   ├── class-meta-box.php    # Meta box handler
│   ├── class-shortcode.php   # Shortcode processor
│   ├── class-rest-api.php    # REST API endpoints
│   ├── class-database.php    # Database operations
│   ├── functions.php         # Helper functions
│   └── utils/
│       ├── sanitize.php      # Sanitization helpers
│       └── validate.php      # Validation helpers
├── templates/
│   ├── admin/
│   │   ├── settings-page.php
│   │   ├── meta-box.php
│   │   └── dashboard-widget.php
│   └── public/
│       ├── shortcode-output.php
│       ├── archive-custom.php
│       └── single-custom.php
├── languages/
│   └── my-plugin.pot         # Translation template
└── tests/
    ├── test-main.php         # Main plugin tests
    ├── test-ajax.php         # AJAX tests
    └── test-shortcode.php    # Shortcode tests
```

### Main Plugin File (my-plugin.php)

```php
<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI: https://example.com/my-plugin
 * Description: A production-grade WordPress plugin example
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-plugin
 * Domain Path: /languages
 *
 * @package My_Plugin
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

// Plugin constants
define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_FILE', __FILE__);
define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URI', plugin_dir_url(__FILE__));
define('MY_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader (if using classes)
spl_autoload_register(function ($class) {
    $prefix = 'My_Plugin\\';
    $base_dir = MY_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load main plugin class
require_once MY_PLUGIN_DIR . 'includes/class-my-plugin.php';

// Initialize plugin
function my_plugin_init(): void {
    $plugin = new My_Plugin();
    $plugin->run();
}

add_action('plugins_loaded', 'my_plugin_init', 10);

// Activation hook
register_activation_hook(__FILE__, function () {
    require_once MY_PLUGIN_DIR . 'includes/class-my-plugin.php';
    My_Plugin::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function () {
    require_once MY_PLUGIN_DIR . 'includes/class-my-plugin.php';
    My_Plugin::deactivate();
});
```

### Main Plugin Class (includes/class-my-plugin.php)

```php
<?php
/**
 * Main Plugin Class
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class My_Plugin {
    /**
     * Plugin version
     */
    const VERSION = MY_PLUGIN_VERSION;

    /**
     * Singleton instance
     */
    private static ?My_Plugin $instance = null;

    /**
     * Plugin components
     */
    private Admin $admin;
    private Public_Frontend $public;
    private Settings $settings;
    private AJAX $ajax;
    private Post_Type $post_type;
    private Meta_Box $meta_box;
    private Shortcode $shortcode;
    private REST_API $rest_api;
    private Database $database;

    /**
     * Get singleton instance
     */
    public static function get_instance(): My_Plugin {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies(): void {
        require_once MY_PLUGIN_DIR . 'includes/functions.php';
        
        $this->admin       = new Admin();
        $this->public      = new Public_Frontend();
        $this->settings    = new Settings();
        $this->ajax        = new AJAX();
        $this->post_type   = new Post_Type();
        $this->meta_box    = new Meta_Box();
        $this->shortcode   = new Shortcode();
        $this->rest_api    = new REST_API();
        $this->database    = new Database();
    }

    /**
     * Define plugin hooks
     */
    private function define_hooks(): void {
        // Admin hooks
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_scripts']);
        add_action('admin_menu', [$this->admin, 'add_menu']);
        add_filter('plugin_action_links_' . MY_PLUGIN_BASENAME, [$this->admin, 'add_action_links']);

        // Public hooks
        add_action('wp_enqueue_scripts', [$this->public, 'enqueue_scripts']);

        // Settings hooks
        add_action('admin_init', [$this->settings, 'register_settings']);

        // AJAX hooks
        add_action('wp_ajax_my_plugin_action', [$this->ajax, 'handle_action']);
        add_action('wp_ajax_nopriv_my_plugin_action', [$this->ajax, 'handle_public_action']);

        // Custom post type hooks
        add_action('init', [$this->post_type, 'register'], 0);

        // Meta box hooks
        add_action('add_meta_boxes', [$this->meta_box, 'add']);
        add_action('save_post', [$this->meta_box, 'save'], 10, 2);

        // Shortcode hooks
        add_shortcode('my_shortcode', [$this->shortcode, 'render']);

        // REST API hooks
        add_action('rest_api_init', [$this->rest_api, 'register_routes']);
    }

    /**
     * Run the plugin
     */
    public function run(): void {
        self::get_instance();
    }

    /**
     * Plugin activation
     */
    public static function activate(): void {
        // Check PHP version
        if (version_compare(PHP_VERSION, '8.0', '<')) {
            deactivate_plugins(MY_PLUGIN_BASENAME);
            wp_die(
                esc_html__('My Plugin requires PHP 8.0 or higher.', 'my-plugin'),
                'Plugin Activation Error',
                ['back_link' => true]
            );
        }

        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '6.0', '<')) {
            deactivate_plugins(MY_PLUGIN_BASENAME);
            wp_die(
                esc_html__('My Plugin requires WordPress 6.0 or higher.', 'my-plugin'),
                'Plugin Activation Error',
                ['back_link' => true]
            );
        }

        // Create database tables
        Database::create_tables();

        // Set default options
        self::set_default_options();

        // Clear rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    /**
     * Set default plugin options
     */
    private static function set_default_options(): void {
        if (!get_option('my_plugin_settings')) {
            add_option('my_plugin_settings', [
                'enabled'      => true,
                'debug_mode'   => false,
                'max_items'    => 10,
            ]);
        }
    }

    /**
     * Get plugin option
     */
    public static function get_option(string $key, $default = null) {
        $options = get_option('my_plugin_settings', []);
        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Update plugin option
     */
    public static function update_option(string $key, $value): bool {
        $options = get_option('my_plugin_settings', []);
        $options[$key] = $value;
        return update_option('my_plugin_settings', $options);
    }
}
```

### Settings API Implementation (includes/class-settings.php)

```php
<?php
/**
 * Settings API Wrapper
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class Settings {
    /**
     * Settings page slug
     */
    const PAGE_SLUG = 'my-plugin-settings';

    /**
     * Settings option name
     */
    const OPTION_NAME = 'my_plugin_settings';

    /**
     * Register settings and fields
     */
    public function register_settings(): void {
        register_setting(
            self::OPTION_NAME,
            self::OPTION_NAME,
            [
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default'            => $this->get_defaults(),
            ]
        );

        // General Settings Section
        add_settings_section(
            'my_plugin_general',
            __('General Settings', 'my-plugin'),
            [$this, 'section_general_callback'],
            self::PAGE_SLUG
        );

        // Enabled Field
        add_settings_field(
            'enabled',
            __('Enable Plugin', 'my-plugin'),
            [$this, 'field_checkbox_callback'],
            self::PAGE_SLUG,
            'my_plugin_general',
            [
                'id'          => 'enabled',
                'description' => __('Enable plugin functionality.', 'my-plugin'),
            ]
        );

        // Debug Mode Field
        add_settings_field(
            'debug_mode',
            __('Debug Mode', 'my-plugin'),
            [$this, 'field_checkbox_callback'],
            self::PAGE_SLUG,
            'my_plugin_general',
            [
                'id'          => 'debug_mode',
                'description' => __('Enable debug logging.', 'my-plugin'),
            ]
        );

        // Max Items Field
        add_settings_field(
            'max_items',
            __('Maximum Items', 'my-plugin'),
            [$this, 'field_number_callback'],
            self::PAGE_SLUG,
            'my_plugin_general',
            [
                'id'          => 'max_items',
                'description' => __('Maximum number of items to display.', 'my-plugin'),
                'min'         => 1,
                'max'         => 100,
                'step'        => 1,
            ]
        );

        // API Key Field
        add_settings_section(
            'my_plugin_api',
            __('API Settings', 'my-plugin'),
            [$this, 'section_api_callback'],
            self::PAGE_SLUG
        );

        add_settings_field(
            'api_key',
            __('API Key', 'my-plugin'),
            [$this, 'field_text_callback'],
            self::PAGE_SLUG,
            'my_plugin_api',
            [
                'id'          => 'api_key',
                'description' => __('Enter your API key.', 'my-plugin'),
                'type'        => 'password',
            ]
        );
    }

    /**
     * Sanitize settings input
     */
    public function sanitize_settings(array $input): array {
        $sanitized = [];
        $defaults = $this->get_defaults();

        // Sanitize enabled
        $sanitized['enabled'] = isset($input['enabled']) ? (bool) $input['enabled'] : $defaults['enabled'];

        // Sanitize debug_mode
        $sanitized['debug_mode'] = isset($input['debug_mode']) ? (bool) $input['debug_mode'] : $defaults['debug_mode'];

        // Sanitize max_items
        $sanitized['max_items'] = isset($input['max_items']) 
            ? absint($input['max_items']) 
            : $defaults['max_items'];
        
        // Validate range
        if ($sanitized['max_items'] < 1 || $sanitized['max_items'] > 100) {
            add_settings_error(
                self::OPTION_NAME,
                'max_items_range',
                __('Maximum items must be between 1 and 100.', 'my-plugin'),
                'error'
            );
            $sanitized['max_items'] = $defaults['max_items'];
        }

        // Sanitize api_key
        $sanitized['api_key'] = isset($input['api_key']) 
            ? sanitize_text_field($input['api_key']) 
            : '';

        return $sanitized;
    }

    /**
     * Get default settings
     */
    private function get_defaults(): array {
        return [
            'enabled'      => true,
            'debug_mode'   => false,
            'max_items'    => 10,
            'api_key'      => '',
        ];
    }

    /**
     * Section: General Settings
     */
    public function section_general_callback(): void {
        echo '<p>' . esc_html__('Configure general plugin settings.', 'my-plugin') . '</p>';
    }

    /**
     * Section: API Settings
     */
    public function section_api_callback(): void {
        echo '<p>' . esc_html__('Configure API integration settings.', 'my-plugin') . '</p>';
    }

    /**
     * Field: Checkbox
     */
    public function field_checkbox_callback(array $args): void {
        $options = get_option(self::OPTION_NAME, $this->get_defaults());
        $id = $args['id'];
        $checked = isset($options[$id]) && $options[$id];

        printf(
            '<input type="checkbox" id="%s" name="%s[%s]" value="1" %s>',
            esc_attr($id),
            esc_attr(self::OPTION_NAME),
            esc_attr($id),
            checked($checked, true, false)
        );

        if (isset($args['description'])) {
            printf(
                '<p class="description">%s</p>',
                esc_html($args['description'])
            );
        }
    }

    /**
     * Field: Number
     */
    public function field_number_callback(array $args): void {
        $options = get_option(self::OPTION_NAME, $this->get_defaults());
        $id = $args['id'];
        $value = isset($options[$id]) ? $options[$id] : 0;
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $step = isset($args['step']) ? $args['step'] : 1;

        printf(
            '<input type="number" id="%s" name="%s[%s]" value="%d" min="%d" max="%d" step="%d">',
            esc_attr($id),
            esc_attr(self::OPTION_NAME),
            esc_attr($id),
            esc_attr($value),
            esc_attr($min),
            esc_attr($max),
            esc_attr($step)
        );

        if (isset($args['description'])) {
            printf(
                '<p class="description">%s</p>',
                esc_html($args['description'])
            );
        }
    }

    /**
     * Field: Text
     */
    public function field_text_callback(array $args): void {
        $options = get_option(self::OPTION_NAME, $this->get_defaults());
        $id = $args['id'];
        $value = isset($options[$id]) ? $options[$id] : '';
        $type = isset($args['type']) ? $args['type'] : 'text';

        printf(
            '<input type="%s" id="%s" name="%s[%s]" value="%s" class="regular-text">',
            esc_attr($type),
            esc_attr($id),
            esc_attr(self::OPTION_NAME),
            esc_attr($id),
            esc_attr($value)
        );

        if (isset($args['description'])) {
            printf(
                '<p class="description">%s</p>',
                esc_html($args['description'])
            );
        }
    }
}
```

### Custom Post Type Registration (includes/class-post-type.php)

```php
<?php
/**
 * Custom Post Type Handler
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class Post_Type {
    /**
     * Post type slug
     */
    const SLUG = 'my_custom';

    /**
     * Register custom post type
     */
    public function register(): void {
        $labels = [
            'name'                  => _x('Custom Items', 'Post Type General Name', 'my-plugin'),
            'singular_name'         => _x('Custom Item', 'Post Type Singular Name', 'my-plugin'),
            'menu_name'             => __('Custom Items', 'my-plugin'),
            'name_admin_bar'        => __('Custom Item', 'my-plugin'),
            'archives'              => __('Item Archives', 'my-plugin'),
            'attributes'            => __('Item Attributes', 'my-plugin'),
            'parent_item_colon'     => __('Parent Item:', 'my-plugin'),
            'all_items'             => __('All Items', 'my-plugin'),
            'add_new_item'          => __('Add New Item', 'my-plugin'),
            'add_new'               => __('Add New', 'my-plugin'),
            'new_item'              => __('New Item', 'my-plugin'),
            'edit_item'             => __('Edit Item', 'my-plugin'),
            'update_item'           => __('Update Item', 'my-plugin'),
            'view_item'             => __('View Item', 'my-plugin'),
            'view_items'            => __('View Items', 'my-plugin'),
            'search_items'          => __('Search Item', 'my-plugin'),
            'not_found'             => __('Not found', 'my-plugin'),
            'not_found_in_trash'    => __('Not found in Trash', 'my-plugin'),
            'featured_image'        => __('Featured Image', 'my-plugin'),
            'set_featured_image'    => __('Set featured image', 'my-plugin'),
            'remove_featured_image' => __('Remove featured image', 'my-plugin'),
            'use_featured_image'    => __('Use as featured image', 'my-plugin'),
            'insert_into_item'      => __('Insert into item', 'my-plugin'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'my-plugin'),
            'items_list'            => __('Items list', 'my-plugin'),
            'items_list_navigation' => __('Items list navigation', 'my-plugin'),
            'filter_items_list'     => __('Filter items list', 'my-plugin'),
        ];

        $args = [
            'label'                 => __('Custom Item', 'my-plugin'),
            'description'           => __('Custom post type for my plugin', 'my-plugin'),
            'labels'                => $labels,
            'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'taxonomies'            => ['category', 'post_tag'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-generic',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => self::SLUG,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];

        register_post_type(self::SLUG, $args);

        // Register custom taxonomy if needed
        $this->register_taxonomy();
    }

    /**
     * Register custom taxonomy
     */
    private function register_taxonomy(): void {
        $labels = [
            'name'              => _x('Custom Categories', 'Taxonomy General Name', 'my-plugin'),
            'singular_name'     => _x('Custom Category', 'Taxonomy Singular Name', 'my-plugin'),
            'menu_name'         => __('Categories', 'my-plugin'),
            'all_items'         => __('All Categories', 'my-plugin'),
            'parent_item'       => __('Parent Category', 'my-plugin'),
            'parent_item_colon' => __('Parent Category:', 'my-plugin'),
            'new_item_name'     => __('New Category Name', 'my-plugin'),
            'add_new_item'      => __('Add New Category', 'my-plugin'),
            'edit_item'         => __('Edit Category', 'my-plugin'),
            'update_item'       => __('Update Category', 'my-plugin'),
            'view_item'         => __('View Category', 'my-plugin'),
            'separate_items_with_commas' => __('Separate categories with commas', 'my-plugin'),
            'add_or_remove_items'        => __('Add or remove categories', 'my-plugin'),
            'choose_from_most_used'      => __('Choose from the most used', 'my-plugin'),
            'popular_items'              => __('Popular Categories', 'my-plugin'),
            'search_items'               => __('Search Categories', 'my-plugin'),
            'not_found'                   => __('Not Found', 'my-plugin'),
            'no_terms'                    => __('No categories', 'my-plugin'),
            'items_list'                  => __('Categories list', 'my-plugin'),
            'items_list_navigation'       => __('Categories list navigation', 'my-plugin'),
        ];

        $args = [
            'labels'             => $labels,
            'hierarchical'       => true,
            'public'             => true,
            'show_ui'            => true,
            'show_admin_column'  => true,
            'show_in_nav_menus'  => true,
            'show_tagcloud'      => true,
            'show_in_rest'       => true,
            'rest_base'          => 'my_custom_category',
        ];

        register_taxonomy('my_custom_category', [self::SLUG], $args);
    }
}
```

### Meta Box Implementation (includes/class-meta-box.php)

```php
<?php
/**
 * Meta Box Handler
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class Meta_Box {
    /**
     * Meta box ID
     */
    const META_BOX_ID = 'my_plugin_meta_box';

    /**
     * Meta key prefix
     */
    const META_PREFIX = '_my_plugin_';

    /**
     * Add meta box to post types
     */
    public function add(): void {
        add_meta_box(
            self::META_BOX_ID,
            __('My Plugin Settings', 'my-plugin'),
            [$this, 'render'],
            [Post_Type::SLUG, 'post'],
            'normal',
            'high'
        );
    }

    /**
     * Render meta box content
     */
    public function render(WP_Post $post): void {
        // Add nonce for security
        wp_nonce_field(
            self::META_BOX_ID . '_nonce_action',
            self::META_BOX_ID . '_nonce'
        );

        // Get current meta values
        $meta_data = $this->get_meta_data($post->ID);

        // Render template
        include MY_PLUGIN_DIR . 'templates/admin/meta-box.php';
    }

    /**
     * Save meta box data
     */
    public function save(int $post_id, WP_Post $post): void {
        // Verify nonce
        if (!isset($_POST[self::META_BOX_ID . '_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(
            $_POST[self::META_BOX_ID . '_nonce'],
            self::META_BOX_ID . '_nonce_action'
        )) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check post type
        if (!in_array($post->post_type, [Post_Type::SLUG, 'post'], true)) {
            return;
        }

        // Sanitize and save meta data
        $this->save_meta_data($post_id);
    }

    /**
     * Get meta data for post
     */
    private function get_meta_data(int $post_id): array {
        return [
            'custom_field_1' => get_post_meta($post_id, self::META_PREFIX . 'custom_field_1', true),
            'custom_field_2' => get_post_meta($post_id, self::META_PREFIX . 'custom_field_2', true),
            'custom_checkbox' => get_post_meta($post_id, self::META_PREFIX . 'custom_checkbox', true),
            'custom_select' => get_post_meta($post_id, self::META_PREFIX . 'custom_select', true),
        ];
    }

    /**
     * Save sanitized meta data
     */
    private function save_meta_data(int $post_id): void {
        // Custom Field 1 (text)
        if (isset($_POST[self::META_PREFIX . 'custom_field_1'])) {
            $value = sanitize_text_field($_POST[self::META_PREFIX . 'custom_field_1']);
            update_post_meta($post_id, self::META_PREFIX . 'custom_field_1', $value);
        } else {
            delete_post_meta($post_id, self::META_PREFIX . 'custom_field_1');
        }

        // Custom Field 2 (textarea)
        if (isset($_POST[self::META_PREFIX . 'custom_field_2'])) {
            $value = sanitize_textarea_field($_POST[self::META_PREFIX . 'custom_field_2']);
            update_post_meta($post_id, self::META_PREFIX . 'custom_field_2', $value);
        } else {
            delete_post_meta($post_id, self::META_PREFIX . 'custom_field_2');
        }

        // Custom Checkbox
        $value = isset($_POST[self::META_PREFIX . 'custom_checkbox']) ? 'yes' : 'no';
        update_post_meta($post_id, self::META_PREFIX . 'custom_checkbox', $value);

        // Custom Select
        if (isset($_POST[self::META_PREFIX . 'custom_select'])) {
            $allowed_values = ['option_1', 'option_2', 'option_3'];
            $value = sanitize_text_field($_POST[self::META_PREFIX . 'custom_select']);
            
            if (in_array($value, $allowed_values, true)) {
                update_post_meta($post_id, self::META_PREFIX . 'custom_select', $value);
            }
        }
    }
}
```

### Shortcode Implementation (includes/class-shortcode.php)

```php
<?php
/**
 * Shortcode Handler
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class Shortcode {
    /**
     * Render shortcode output
     */
    public function render($atts = [], ?string $content = null): string {
        // Parse shortcode attributes with defaults
        $atts = shortcode_atts([
            'id'      => 0,
            'title'   => '',
            'count'   => 5,
            'orderby' => 'date',
            'order'   => 'DESC',
            'class'   => 'my-shortcode',
        ], $atts, 'my_shortcode');

        // Validate and sanitize attributes
        $atts['id'] = absint($atts['id']);
        $atts['title'] = sanitize_text_field($atts['title']);
        $atts['count'] = absint($atts['count']);
        $atts['count'] = max(1, min($atts['count'], 100)); // Limit between 1-100
        
        $allowed_orderby = ['date', 'title', 'ID', 'author', 'modified'];
        $atts['orderby'] = in_array($atts['orderby'], $allowed_orderby, true) 
            ? $atts['orderby'] 
            : 'date';
        
        $atts['order'] = strtoupper($atts['order']);
        $atts['order'] = in_array($atts['order'], ['ASC', 'DESC'], true) 
            ? $atts['order'] 
            : 'DESC';
        
        $atts['class'] = sanitize_html_class($atts['class']);

        // Build query arguments
        $query_args = [
            'post_type'      => Post_Type::SLUG,
            'posts_per_page' => $atts['count'],
            'orderby'        => $atts['orderby'],
            'order'          => $atts['order'],
            'post_status'    => 'publish',
        ];

        if ($atts['id'] > 0) {
            $query_args['p'] = $atts['id'];
        }

        // Execute query
        $query = new \WP_Query($query_args);

        // Build output
        ob_start();

        if ($query->have_posts()) {
            echo '<div class="' . esc_attr($atts['class']) . '">';
            
            if (!empty($atts['title'])) {
                echo '<h3 class="shortcode-title">' . esc_html($atts['title']) . '</h3>';
            }

            while ($query->have_posts()) {
                $query->the_post();
                include MY_PLUGIN_DIR . 'templates/public/shortcode-output.php';
            }

            echo '</div>';
        } else {
            echo '<p class="shortcode-no-results">' . 
                esc_html__('No items found.', 'my-plugin') . 
                '</p>';
        }

        wp_reset_postdata();

        return ob_get_clean();
    }
}
```

### AJAX Handler (includes/class-ajax.php)

```php
<?php
/**
 * AJAX Handler
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class AJAX {
    /**
     * Handle authenticated AJAX action
     */
    public function handle_action(): void {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_plugin_nonce')) {
            wp_send_json_error([
                'message' => __('Security check failed.', 'my-plugin'),
            ]);
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Permission denied.', 'my-plugin'),
            ]);
        }

        // Get and validate data
        $action = isset($_POST['action_type']) 
            ? sanitize_text_field($_POST['action_type']) 
            : '';

        $data = isset($_POST['data']) 
            ? $this->sanitize_data($_POST['data']) 
            : [];

        // Process action
        $result = $this->process_action($action, $data);

        if ($result) {
            wp_send_json_success([
                'message' => __('Action completed successfully.', 'my-plugin'),
                'data'    => $result,
            ]);
        } else {
            wp_send_json_error([
                'message' => __('Action failed.', 'my-plugin'),
            ]);
        }
    }

    /**
     * Handle public AJAX action (no authentication required)
     */
    public function handle_public_action(): void {
        // Verify nonce (optional for public actions)
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_plugin_public_nonce')) {
            wp_send_json_error([
                'message' => __('Security check failed.', 'my-plugin'),
            ]);
        }

        // Process public action
        $data = isset($_POST['data']) 
            ? $this->sanitize_data($_POST['data']) 
            : [];

        $result = $this->process_public_action($data);

        wp_send_json_success([
            'message' => __('Request processed.', 'my-plugin'),
            'data'    => $result,
        ]);
    }

    /**
     * Sanitize AJAX data
     */
    private function sanitize_data(array $data): array {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[sanitize_key($key)] = $this->sanitize_data($value);
            } elseif (is_string($value)) {
                $sanitized[sanitize_key($key)] = sanitize_text_field($value);
            } elseif (is_numeric($value)) {
                $sanitized[sanitize_key($key)] = absint($value);
            } else {
                $sanitized[sanitize_key($key)] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }

    /**
     * Process authenticated action
     */
    private function process_action(string $action, array $data): array {
        switch ($action) {
            case 'save_data':
                return Database::save_custom_data($data);

            case 'get_data':
                return Database::get_custom_data($data['id']);

            case 'delete_data':
                return ['deleted' => Database::delete_custom_data($data['id'])];

            default:
                return [];
        }
    }

    /**
     * Process public action
     */
    private function process_public_action(array $data): array {
        // Example: submit form, search, etc.
        return [
            'received' => $data,
            'timestamp' => current_time('mysql'),
        ];
    }
}
```

### Database Operations (includes/class-database.php)

```php
<?php
/**
 * Database Handler
 *
 * @package My_Plugin
 */

namespace My_Plugin;

defined('ABSPATH') || exit;

class Database {
    /**
     * Custom table name
     */
    const TABLE_NAME = 'my_plugin_custom';

    /**
     * Create custom database tables
     */
    public static function create_tables(): void {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            data_key varchar(100) NOT NULL,
            data_value longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY data_key (data_key)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Add version option to track schema changes
        add_option('my_plugin_db_version', '1.0.0');
    }

    /**
     * Save custom data
     */
    public static function save_custom_data(array $data): array {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        // Validate required fields
        if (empty($data['post_id']) || empty($data['data_key'])) {
            return ['error' => 'Missing required fields'];
        }

        // Prepare data
        $insert_data = [
            'post_id'    => absint($data['post_id']),
            'user_id'    => get_current_user_id(),
            'data_key'   => sanitize_text_field($data['data_key']),
            'data_value' => maybe_serialize($data['data_value']),
        ];

        // Insert using prepared statement (safe)
        $result = $wpdb->insert(
            $table_name,
            $insert_data,
            ['%d', '%d', '%s', '%s']
        );

        if ($result === false) {
            return ['error' => 'Database insert failed'];
        }

        return [
            'success' => true,
            'id'      => $wpdb->insert_id,
        ];
    }

    /**
     * Get custom data by ID
     */
    public static function get_custom_data(int $id): array {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        // Safe query with prepare
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        if ($row) {
            $row['data_value'] = maybe_unserialize($row['data_value']);
            return $row;
        }

        return [];
    }

    /**
     * Get all custom data by post_id
     */
    public static function get_post_data(int $post_id): array {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        // Safe query with prepare
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE post_id = %d ORDER BY created_at DESC",
                $post_id
            ),
            ARRAY_A
        );

        if ($results) {
            foreach ($results as &$row) {
                $row['data_value'] = maybe_unserialize($row['data_value']);
            }
            return $results;
        }

        return [];
    }

    /**
     * Delete custom data
     */
    public static function delete_custom_data(int $id): bool {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        // Safe delete with prepare
        $result = $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Delete all data on plugin uninstall
     */
    public static function drop_tables(): void {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        delete_option('my_plugin_db_version');
    }
}
```

### Uninstall Handler (uninstall.php)

```php
<?php
/**
 * My Plugin Uninstall Handler
 *
 * This file is executed when the plugin is deleted (not deactivated).
 * 
 * @package My_Plugin
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('my_plugin_settings');
delete_option('my_plugin_db_version');

// Delete all custom post type posts
$posts = get_posts([
    'post_type'   => 'my_custom',
    'numberposts' => -1,
    'post_status' => 'any',
]);

foreach ($posts as $post) {
    wp_delete_post($post->ID, true); // Force delete (skip trash)
}

// Drop custom database tables
require_once plugin_dir_path(__FILE__) . 'includes/class-database.php';
My_Plugin\Database::drop_tables();

// Clear any cached data
wp_cache_flush();
```

## Plugin Best Practices

### Security

- Always verify nonces for forms and AJAX
- Check user capabilities before operations
- Escape all output (esc_html, esc_attr, esc_url)
- Sanitize all input (sanitize_text_field, etc.)
- Use prepared statements for database queries
- Never trust user input

### Performance

- Load scripts/styles only when needed (conditional enqueue)
- Use transients for caching expensive operations
- Optimize database queries (proper indexes)
- Avoid expensive operations in loops
- Use wp_cache when appropriate

### Compatibility

- Check WordPress and PHP version requirements
- Use WordPress APIs instead of direct SQL/file operations
- Test with major WP versions before release
- Follow WordPress coding standards
- Use internationalization functions (__(), _e())

### Maintainability

- Use classes and namespaces
- Separate concerns (admin vs public)
- Document code with DocBlocks
- Use consistent naming conventions
- Organize files logically

### User Experience

- Provide clear error messages
- Make settings intuitive
- Use proper admin notices
- Include helpful documentation
- Test with real users

## Reference

- WordPress Plugin Developer Handbook: https://developer.wordpress.org/plugins/
- WordPress Settings API: https://developer.wordpress.org/plugins/settings/
- WordPress Custom Post Types: https://developer.wordpress.org/plugins/post-types/
- WordPress AJAX: https://developer.wordpress.org/plugins/ajax/
- WordPress Database: https://developer.wordpress.org/plugins/database/

**Remember**: Great plugins are secure, performant, and maintainable. Use WordPress APIs, follow coding standards, and always sanitize/escape data.