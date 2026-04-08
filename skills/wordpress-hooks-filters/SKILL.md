name
wordpress-hooks-filters

description
WordPress hooks system patterns for actions, filters, priorities, callbacks, hook removal, debugging, and custom hook creation for production-grade WordPress development.

origin
OpenCode WordPress

# WordPress Hooks & Filters

Production-grade WordPress hooks system patterns for extensible, maintainable WordPress code.

## When to Use

- Hooking into WordPress core functionality
- Extending plugins and themes
- Modifying data before output
- Triggering custom actions
- Removing default WordPress behavior
- Creating custom hooks for extensions
- Debugging hook execution
- Managing hook priorities

## How It Works

- **Actions**: Do something (no return value)
- **Filters**: Modify data (return modified data)
- **Priorities**: Control execution order (lower = earlier)
- **Callbacks**: Functions/methods attached to hooks
- **Hook removal**: `remove_action`, `remove_filter`
- **Custom hooks**: `do_action`, `apply_filters`

## Examples

### Actions vs Filters

```php
<?php
/**
 * ACTIONS vs FILTERS
 */

// ACTION: Does something, no return value
// Use when you want to execute code at a specific point
add_action('wp_head', 'my_plugin_add_meta_tag');
function my_plugin_add_meta_tag(): void {
    echo '<meta name="my-plugin" content="active">';
}

add_action('save_post', 'my_plugin_log_post_save', 10, 2);
function my_plugin_log_post_save(int $post_id, WP_Post $post): void {
    error_log("Post {$post_id} saved: {$post->post_title}");
    // No return value needed
}

// FILTER: Modifies and returns data
// Use when you want to change data before it's used
add_filter('the_title', 'my_plugin_modify_title', 10, 2);
function my_plugin_modify_title(string $title, int $post_id): string {
    // MUST return modified value
    return $title . ' [' . $post_id . ']';
}

add_filter('the_content', 'my_plugin_add_signature');
function my_plugin_add_signature(string $content): string {
    // MUST return modified content
    return $content . '<p>Written by My Plugin</p>';
}

// DECISION MATRIX:
// Do I need to return a value? 
//   YES -> Use FILTER
//   NO  -> Use ACTION
```

### Hook Priorities

```php
<?php
/**
 * HOOK PRIORITIES
 */

// Priority: 1 (runs early)
add_action('init', 'my_plugin_early_init', 1);
function my_plugin_early_init(): void {
    // Runs before most other init hooks
}

// Priority: 10 (default, runs in the middle)
add_action('init', 'my_plugin_normal_init');
// Default priority is 10 if not specified

// Priority: 20 (runs late)
add_action('init', 'my_plugin_late_init', 20);
function my_plugin_late_init(): void {
    // Runs after most other init hooks
}

// Priority: 999 (runs very late)
add_action('wp_footer', 'my_plugin_footer_end', 999);
function my_plugin_footer_end(): void {
    // Runs near the end of wp_footer
}

// EXECUTION ORDER:
// 1. Priority 1   (my_plugin_early_init)
// 2. Priority 10  (my_plugin_normal_init)
// 3. Priority 20  (my_plugin_late_init)
// 4. Priority 999 (my_plugin_footer_end)

// Multiple hooks with same priority execute in registration order
add_action('init', 'first_callback', 10);   // Runs first
add_action('init', 'second_callback', 10);  // Runs second
add_action('init', 'third_callback', 10);   // Runs third
```

### Hook Callbacks

```php
<?php
/**
 * HOOK CALLBACKS
 */

// FUNCTION CALLBACK
add_action('wp_head', 'my_custom_function');

// STATIC METHOD CALLBACK
add_action('wp_head', ['MyClass', 'static_method']);
// or
add_action('wp_head', 'MyClass::static_method');

// OBJECT METHOD CALLBACK
$object = new MyClass();
add_action('wp_head', [$object, 'method']);

// CLOSURE/ANONYMOUS FUNCTION
add_action('wp_head', function () {
    echo '<meta name="generator" content="My Plugin">';
});

// NAMESPACED CLASS
add_action('wp_head', [\My\Plugin\ClassName::class, 'method']);

// EXAMPLES WITH PARAMETERS
// Action with 2 parameters
add_action('save_post', 'my_save_post_handler', 10, 2);
function my_save_post_handler(int $post_id, WP_Post $post): void {
    // Process both parameters
}

// Filter with 2 parameters
add_filter('post_link', 'my_modify_permalink', 10, 2);
function my_modify_permalink(string $url, WP_Post $post): string {
    // MUST return URL
    return $url;
}

// LARGE NUMBER OF PARAMETERS
add_action('transition_post_status', 'my_post_status_transition', 10, 3);
function my_post_status_transition(
    string $new_status, 
    string $old_status, 
    WP_Post $post
): void {
    // Handle all 3 parameters
}
```

### Object-Oriented Hooks

```php
<?php
/**
 * OBJECT-ORIENTED HOOKS
 */

namespace My\Plugin;

class Plugin {
    /**
     * Initialize plugin hooks
     */
    public function __construct() {
        // Register hooks using object methods
        add_action('init', [$this, 'register_post_types']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('the_content', [$this, 'filter_content'], 10);
    }

    /**
     * Register post types
     */
    public function register_post_types(): void {
        register_post_type('my_custom', [
            'label'  => 'Custom Post Type',
            'public' => true,
        ]);
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts(): void {
        wp_enqueue_style(
            'my-plugin-style',
            MY_PLUGIN_URI . 'assets/css/style.css',
            [],
            MY_PLUGIN_VERSION
        );
    }

    /**
     * Filter content
     */
    public function filter_content(string $content): string {
        if (is_single() && is_main_query()) {
            $content .= '<div class="my-plugin-signature">Custom content</div>';
        }
        return $content;
    }
}

// SINGLETON PATTERN
class Singleton_Plugin {
    private static ?Singleton_Plugin $instance = null;

    public static function get_instance(): Singleton_Plugin {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'init']);
    }

    public function init(): void {
        // Initialization code
    }
}

// Initialize
Singleton_Plugin::get_instance();

// STATIC METHODS (no instantiation needed)
class Static_Plugin {
    public static function init(): void {
        add_action('wp_head', [self::class, 'add_meta_tag']);
        add_filter('the_title', [self::class, 'modify_title']);
    }

    public static function add_meta_tag(): void {
        echo '<meta name="my-plugin" content="active">';
    }

    public static function modify_title(string $title): string {
        return $title . ' ★';
    }
}

Static_Plugin::init();
```

### Removing Hooks

```php
<?php
/**
 * REMOVING HOOKS
 */

// REMOVE ACTION
// remove_action($tag, $callback, $priority)
remove_action('wp_head', 'wp_generator'); // Remove WordPress generator meta tag

// REMOVE FILTER
remove_filter('the_content', 'wpautop'); // Remove automatic paragraph tags

// IMPORTANT: Priority must match original hook priority
// Original: add_action('init', 'some_function', 20);
remove_action('init', 'some_function', 20); // ✅ Correct priority

remove_action('init', 'some_function', 10); // ❌ Won't work (wrong priority)

// REMOVE OBJECT METHOD
$object = new MyClass();
add_action('wp_head', [$object, 'method']);
remove_action('wp_head', [$object, 'method']); // Must use same object instance

// REMOVE STATIC METHOD
add_action('wp_head', ['MyClass', 'static_method']);
remove_action('wp_head', ['MyClass', 'static_method']);
// or
remove_action('wp_head', 'MyClass::static_method');

// REMOVE CLOSURE (VERY DIFFICULT)
$callback = function () {
    echo 'Test';
};
add_action('wp_head', $callback);

// To remove, you need the same closure instance
remove_action('wp_head', $callback); // Must have reference to same closure

// REMOVE ALL CALLBACKS FROM A HOOK
remove_all_actions('wp_head'); // Removes all actions from wp_head
remove_all_filters('the_content'); // Removes all filters from the_content

// CONDITIONAL REMOVAL
function my_plugin_remove_hooks(): void {
    // Only remove on specific pages
    if (is_page('special-page')) {
        remove_action('wp_head', 'wp_generator');
    }
}
add_action('wp', 'my_plugin_remove_hooks'); // Run after WordPress is loaded

// REMOVE HOOKS FROM ANOTHER PLUGIN
function my_plugin_remove_other_plugin_hooks(): void {
    // Remove specific hook from WooCommerce
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_action('init', 'my_plugin_remove_other_plugin_hooks', 20);
```

### Conditional Hooks

```php
<?php
/**
 * CONDITIONAL HOOKS
 */

// HOOK ONLY ON ADMIN PAGES
add_action('admin_init', function () {
    // This only runs in admin
    add_action('admin_notices', 'my_admin_notice');
});

// HOOK ONLY ON FRONTEND
add_action('template_redirect', function () {
    // This only runs on frontend
    if (is_single()) {
        add_filter('the_content', 'my_single_content_filter');
    }
});

// HOOK ONLY ON SPECIFIC PAGE
add_action('wp', function () {
    if (is_page('contact')) {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('contact-form');
        });
    }
});

// HOOK ONLY FOR LOGGED-IN USERS
add_action('init', function () {
    if (is_user_logged_in()) {
        add_filter('the_title', 'my_logged_in_title');
    }
});

// HOOK ONLY FOR SPECIFIC USER ROLE
add_action('init', function () {
    if (current_user_can('manage_options')) {
        add_action('admin_bar_menu', 'my_admin_bar_item', 100);
    }
});

// HOOK ONLY ON SPECIFIC POST TYPE
add_action('wp', function () {
    if (is_singular('product')) {
        add_filter('the_content', 'my_product_content');
    }
});

// HOOK CONDITIONALLY BASED ON URL
add_action('wp', function () {
    $current_url = home_url(add_query_arg([], $GLOBALS['wp']->request));
    
    if (strpos($current_url, '/special/') !== false) {
        add_action('wp_head', 'my_special_page_meta');
    }
});

// HOOK BASED ON SHORTCODE PRESENCE
add_action('wp', function () {
    global $post;
    
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'my_shortcode')) {
        wp_enqueue_script('my-shortcode-script');
    }
});
```

### Custom Hooks

```php
<?php
/**
 * CREATING CUSTOM HOOKS
 */

// CREATE CUSTOM ACTION
// do_action($tag, ...$args);

// Simple custom action
do_action('my_plugin_before_content');

// Custom action with parameters
do_action('my_plugin_save_data', $data, $post_id);

// Trigger custom action
function my_plugin_process_data(array $data, int $post_id): void {
    // Process data
    $processed_data = my_plugin_transform($data);
    
    // Trigger action for extensions
    do_action('my_plugin_data_processed', $processed_data, $post_id);
}

// Other plugins can hook into your custom action
add_action('my_plugin_data_processed', function (array $data, int $post_id) {
    // Extension: Log the data
    error_log("Data processed for post $post_id");
}, 10, 2);

// CREATE CUSTOM FILTER
// apply_filters($tag, $value, ...$args);

// Simple custom filter
$title = apply_filters('my_plugin_title', $title);

// Custom filter with parameters
$price = apply_filters('my_plugin_product_price', $price, $product_id, $currency);

// Apply custom filter
function my_plugin_calculate_price(float $base_price, int $product_id): float {
    $price = $base_price;
    
    // Apply discounts
    $price = apply_filters('my_plugin_discount', $price, $product_id);
    
    // Apply taxes
    $price = apply_filters('my_plugin_tax', $price, $product_id);
    
    // Final price filter
    $price = apply_filters('my_plugin_final_price', $price, $product_id);
    
    return $price;
}

// Other plugins can hook into your custom filter
add_filter('my_plugin_final_price', function (float $price, int $product_id): float {
    // Apply custom markup
    return $price * 1.1; // 10% markup
}, 10, 2);

// NAMED HOOKS WITH DOCUMENTATION
/**
 * Fires after product is added to cart.
 *
 * @since 1.0.0
 *
 * @param int   $product_id Product ID.
 * @param int   $quantity   Quantity added.
 * @param float $price      Product price.
 */
do_action('my_plugin_add_to_cart', $product_id, $quantity, $price);

/**
 * Filters the product price.
 *
 * @since 1.0.0
 *
 * @param float $price      Product price.
 * @param int   $product_id Product ID.
 */
$price = apply_filters('my_plugin_product_price', $price, $product_id);
```

### Hook Debugging

```php
<?php
/**
 * DEBUGGING HOOKS
 */

// CHECK IF ACTION HAS BEEN DONE
if (did_action('wp_loaded')) {
    // wp_loaded action has fired
}

// CHECK IF FILTER HAS BEEN APPLIED
if (has_filter('the_content')) {
    // the_content has filters attached
}

// CHECK IF SPECIFIC CALLBACK IS HOOKED
if (has_action('wp_head', 'wp_generator')) {
    // wp_generator is hooked to wp_head
}

// COUNT HOOKS
$priority = has_filter('the_content', 'wpautop');
if (false !== $priority) {
    // wpautop is hooked to the_content with priority $priority
}

// DEBUG: LIST ALL HOOKS
function debug_list_hooks(): void {
    global $wp_filter;
    
    echo '<pre>';
    foreach ($wp_filter as $tag => $hook) {
        echo "Hook: $tag\n";
        foreach ($hook->callbacks as $priority => $callbacks) {
            echo "  Priority: $priority\n";
            foreach ($callbacks as $callback) {
                $function = $callback['function'];
                if (is_array($function)) {
                    if (is_object($function[0])) {
                        $name = get_class($function[0]) . '->' . $function[1];
                    } else {
                        $name = $function[0] . '::' . $function[1];
                    }
                } else {
                    $name = $function;
                }
                echo "    - $name\n";
            }
        }
    }
    echo '</pre>';
}
// Call only for debugging
// add_action('wp_footer', 'debug_list_hooks');

// DEBUG: LOG HOOK EXECUTION
function debug_log_hooks(): void {
    global $wp_actions;
    
    foreach ($wp_actions as $action => $count) {
        error_log("Action: $action, Count: $count");
    }
}
// add_action('shutdown', 'debug_log_hooks');

// DEBUG: WRAP FILTER FOR LOGGING
function debug_wrap_filter(string $tag): void {
    global $wp_filter;
    
    if (!isset($wp_filter[$tag])) {
        return;
    }
    
    $hooks = $wp_filter[$tag]->callbacks;
    
    foreach ($hooks as $priority => $callbacks) {
        foreach ($callbacks as $index => $callback) {
            $original_function = $callback['function'];
            
            $callback['function'] = function (...$args) use ($original_function, $tag, $priority) {
                error_log("Filter: $tag, Priority: $priority");
                $result = call_user_func_array($original_function, $args);
                error_log("Result: " . print_r($result, true));
                return $result;
            };
            
            $hooks[$priority][$index] = $callback;
        }
    }
    
    $wp_filter[$tag]->callbacks = $hooks;
}
```

### Common WordPress Hooks Reference

```php
<?php
/**
 * COMMON WORDPRESS HOOKS REFERENCE
 */

// INITIALIZATION HOOKS
add_action('plugins_loaded', 'plugin_init');          // After plugins loaded
add_action('init', 'register_post_types');            // After WordPress init
add_action('wp_loaded', 'after_wp_loaded');           // After WordPress fully loaded
add_action('after_setup_theme', 'theme_setup');       // After theme setup

// FRONTEND HOOKS
add_action('wp_head', 'add_meta_tags');               // Inside <head>
add_action('wp_body_open', 'after_body_tag');         // After <body>
add_action('wp_footer', 'add_scripts');               // Before </body>
add_action('wp_enqueue_scripts', 'enqueue_assets');   // Enqueue scripts/styles

// CONTENT FILTERS
add_filter('the_content', 'modify_content');          // Post content
add_filter('the_title', 'modify_title');              // Post title
add_filter('the_excerpt', 'modify_excerpt');          // Post excerpt
add_filter('the_permalink', 'modify_permalink');      // Permalink URL

// POST HOOKS
add_action('save_post', 'on_save_post', 10, 2);       // Post saved
add_action('transition_post_status', 'on_status_change', 10, 3); // Status changed
add_action('wp_insert_post', 'after_insert', 10, 3);  // After insert
add_action('delete_post', 'on_delete_post');          // Post deleted

// ADMIN HOOKS
add_action('admin_menu', 'add_menu_pages');           // Add admin menus
add_action('admin_init', 'admin_init');               // Admin initialization
add_action('admin_notices', 'show_admin_notice');     // Admin notices
add_action('admin_enqueue_scripts', 'admin_scripts'); // Admin scripts

// LOGIN/AUTH HOOKS
add_action('wp_login', 'on_user_login', 10, 2);       // User logged in
add_action('wp_logout', 'on_user_logout');            // User logged out
add_action('wp_login_failed', 'on_login_failed');     // Login failed

// USER HOOKS
add_action('user_register', 'on_user_register', 10, 1); // User registered
add_action('profile_update', 'on_profile_update', 10, 2); // Profile updated
add_action('delete_user', 'on_delete_user');          // User deleted

// COMMENT HOOKS
add_action('comment_post', 'on_comment_post', 10, 3); // Comment posted
add_action('transition_comment_status', 'on_comment_status', 10, 3);
add_filter('pre_comment_approved', 'moderate_comment', 10, 2);

// WIDGET HOOKS
add_action('widgets_init', 'register_widgets');       // Register widgets
add_action('dynamic_sidebar', 'before_widget');       // Before widget display

// HTTP API HOOKS
add_action('http_api_curl', 'log_curl_request');      // Before HTTP request
add_filter('http_request_args', 'modify_http_args');  // Modify HTTP args

// REST API HOOKS
add_action('rest_api_init', 'register_routes');       // Register REST routes
add_filter('rest_prepare_post', 'modify_rest_response', 10, 3);

// CRON HOOKS
add_action('my_scheduled_event', 'run_scheduled_task');
// wp_schedule_event(time(), 'hourly', 'my_scheduled_event');
```

### Hook Best Practices

```php
<?php
/**
 * HOOK BEST PRACTICES
 */

// 1. USE PREFIXES FOR CUSTOM HOOKS
do_action('my_plugin_before_content');           // ✅ Good
do_action('before_content');                      // ❌ Bad (too generic)

// 2. DOCUMENT YOUR HOOKS
/**
 * Fires after product is purchased.
 *
 * @since 1.0.0
 *
 * @param int   $order_id   Order ID.
 * @param array $order_data Order data array.
 */
do_action('my_plugin_order_completed', $order_id, $order_data);

// 3. ACCEPT MULTIPLE PARAMETERS WHEN NEEDED
// Specify the number of accepted parameters
add_action('save_post', 'my_save_function', 10, 2);
function my_save_function(int $post_id, WP_Post $post): void {
    // Use both parameters
}

// 4. RETURN VALUES IN FILTERS
add_filter('the_title', 'my_modify_title');
function my_modify_title(string $title): string {
    // ALWAYS return, even if not modifying
    return $title;
}

// 5. CHECK IF HOOK EXISTS
if (has_action('my_custom_hook')) {
    do_action('my_custom_hook');
}

// 6. AVOID NESTED HOOKS (can cause infinite loops)
// ❌ Bad
add_action('save_post', function ($post_id) {
    wp_update_post(['ID' => $post_id, 'post_title' => 'Modified']); // Triggers save_post again!
});

// ✅ Good
add_action('save_post', function ($post_id) {
    // Remove hook before update
    remove_action('save_post', __FUNCTION__);
    wp_update_post(['ID' => $post_id, 'post_title' => 'Modified']);
    // Re-add hook
    add_action('save_post', __FUNCTION__);
});

// 7. USE LATE PRIORITIES FOR DEPENDENCIES
add_action('wp_enqueue_scripts', 'my_plugin_enqueue', 20); // Run after other plugins

// 8. CLEAN UP ON DEACTIVATION
register_deactivation_hook(__FILE__, function () {
    // Remove scheduled events
    wp_clear_scheduled_hook('my_plugin_daily_event');
    
    // Remove options
    delete_option('my_plugin_settings');
});

// 9. CONDITIONAL HOOKS IN A SINGLETON
class My_Plugin {
    public function __construct() {
        add_action('wp', [$this, 'conditional_hooks']);
    }

    public function conditional_hooks(): void {
        if (is_single()) {
            add_filter('the_content', [$this, 'filter_single_content']);
        }
    }
}

// 10. PERFORMANCE: USE HOOKS INSTEAD OF DIRECT QUERIES
// ✅ Good: Use hooks for caching
add_filter('get_the_terms', function ($terms, $post_id, $taxonomy) {
    $cache_key = "terms_{$post_id}_{$taxonomy}";
    $cached = wp_cache_get($cache_key);
    
    if (false !== $cached) {
        return $cached;
    }
    
    wp_cache_set($cache_key, $terms, '', HOUR_IN_SECONDS);
    return $terms;
}, 10, 3);
```

## Hooks Best Practices Summary

### Action vs Filter Decision

- **Use ACTION**: When performing an operation (no return value needed)
- **Use FILTER**: When modifying data (must return value)

### Priority Guidelines

- Default priority: **10**
- Early execution: **1-5**
- Late execution: **20-999**
- Same priority = registration order

### Naming Conventions

- Prefix custom hooks: `my_plugin_hook_name`
- Use descriptive names: `before_content`, `after_save`
- Follow WordPress naming patterns

### Performance Tips

- Remove hooks when not needed
- Use conditional hooks
- Avoid expensive operations in frequently called hooks
- Cache results when possible

### Debugging Tools

- `has_action()`, `has_filter()`
- `did_action()`
- Query Monitor plugin
- Debug bar plugins

## Reference

- WordPress Plugin API: https://developer.wordpress.org/plugins/hooks/
- WordPress Hook Reference: https://developer.wordpress.org/hooks/
- WordPress Codex Hooks: https://codex.wordpress.org/Plugin_API
- Query Monitor: https://wordpress.org/plugins/query-monitor/
- WordPress Hook Database: https://adambrown.info/p/wp_hooks

**Remember**: Hooks are the foundation of WordPress extensibility. Use actions for operations, filters for data modification, always return values in filters, and document your custom hooks!