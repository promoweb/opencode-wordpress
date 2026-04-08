paths:
  "**/*.php"

# WordPress Hooks

> This file extends [common/hooks.md](../common/hooks.md) with WordPress-specific hook patterns.

## Action vs Filter Decision Matrix

| Scenario | Hook Type | Returns Value? |
|----------|-----------|----------------|
| Execute code at specific point | Action | No |
| Modify data before use | Filter | Yes |
| Log or track events | Action | No |
| Change output | Filter | Yes |
| Send emails/notifications | Action | No |
| Transform content | Filter | Yes |
| Add/remove data | Action | No |
| Validate/format data | Filter | Yes |

## Hook Naming Convention

**Pattern**: `{prefix}_{context}_{action}`

```php
<?php
// ✅ GOOD: Clear, prefixed hook names
do_action( 'my_plugin_before_save', $data );
do_action( 'my_plugin_after_save', $post_id, $data );
apply_filters( 'my_plugin_product_price', $price, $product_id );

// ❌ BAD: Generic or unprefixed hook names
do_action( 'before_save', $data );
do_action( 'save', $post_id );
apply_filters( 'price', $price );
```

## Hook Registration

### Function Callbacks

```php
<?php
// ✅ GOOD: Clear callback registration
add_action( 'wp_head', 'my_plugin_add_meta_tag', 10 );
add_filter( 'the_title', 'my_plugin_modify_title', 10, 2 );

// ✅ GOOD: Document priority and accepted args
/**
 * Add custom meta tag to head.
 *
 * @priority 10
 */
add_action( 'wp_head', 'my_plugin_add_meta_tag' );

// ❌ BAD: No priority specified (defaults to 10, but unclear)
add_action( 'wp_head', 'my_plugin_add_meta_tag' );
```

### Object-Oriented Callbacks

```php
<?php
// ✅ GOOD: Singleton pattern
class My_Plugin {
    private static ?My_Plugin $instance = null;

    public static function get_instance(): My_Plugin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', [ $this, 'init' ] );
        add_filter( 'the_content', [ $this, 'filter_content' ] );
    }

    public function init(): void {
        // Initialization
    }

    public function filter_content( string $content ): string {
        return $content;
    }
}

// ✅ GOOD: Static method callbacks
add_action( 'init', [ My_Plugin::class, 'init' ] );
add_action( 'init', 'My_Plugin::init' ); // Alternative syntax

// ❌ BAD: Instantiating object for each hook
add_action( 'init', [ new My_Plugin(), 'init' ] ); // New instance every time!
```

## Priority Management

```php
<?php
// EARLY EXECUTION (priority 1-5)
add_action( 'init', 'my_plugin_early_setup', 1 );
add_action( 'wp_head', 'my_plugin_critical_meta', 1 );

// NORMAL EXECUTION (priority 10 - default)
add_action( 'init', 'my_plugin_normal_setup' ); // Default: 10
add_action( 'wp_head', 'my_plugin_styles' );

// LATE EXECUTION (priority 20+)
add_action( 'init', 'my_plugin_late_setup', 20 );
add_action( 'wp_footer', 'my_plugin_analytics', 99 );

// EXECUTION ORDER FOR SAME HOOK:
// Priority 1  -> Early setup
// Priority 10 -> Normal setup (runs in registration order)
// Priority 20 -> Late setup
```

## Hook Removal

### Removing Actions

```php
<?php
// ✅ GOOD: Remove with matching priority
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

// ✅ GOOD: Remove after hook is registered
add_action( 'wp_loaded', function() {
    remove_action( 'wp_head', 'wp_generator' );
} );

// ❌ BAD: Wrong priority (won't work)
remove_action( 'wp_head', 'some_function', 20 ); // Original was 10
```

### Removing Filters

```php
<?php
// ✅ GOOD: Remove filter with matching priority
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_content', 'wptexturize', 10 );

// ✅ GOOD: Conditional removal
add_action( 'wp', function() {
    if ( is_page( 'special-page' ) ) {
        remove_filter( 'the_content', 'wpautop' );
    }
} );
```

### Removing Object Method Hooks

```php
<?php
// Remove static method
remove_action( 'init', [ My_Class::class, 'method' ] );

// Remove object method (need same instance)
global $my_plugin_instance;
remove_action( 'init', [ $my_plugin_instance, 'method' ] );
```

## Conditional Hooks

```php
<?php
// ✅ GOOD: Hook only on specific conditions
add_action( 'wp', function() {
    if ( is_single() && is_main_query() ) {
        add_filter( 'the_content', 'my_plugin_single_content' );
    }
} );

// ✅ GOOD: Hook only in admin
add_action( 'admin_init', function() {
    add_action( 'admin_notices', 'my_plugin_admin_notice' );
} );

// ✅ GOOD: Hook only on frontend
add_action( 'template_redirect', function() {
    if ( ! is_admin() ) {
        add_action( 'wp_footer', 'my_plugin_frontend_script' );
    }
} );

// ❌ BAD: Conditional logic inside callback (wasteful)
add_filter( 'the_content', function( $content ) {
    if ( ! is_single() ) {
        return $content; // Always executes, even when not needed
    }
    // Process
    return $modified_content;
} );
```

## Custom Hooks

### Creating Custom Actions

```php
<?php
/**
 * Fires before product is processed.
 *
 * @since 1.0.0
 *
 * @param array $data Product data.
 * @param int   $id   Product ID.
 */
do_action( 'my_plugin_before_process_product', $data, $id );

/**
 * Fires after product is processed.
 *
 * @since 1.0.0
 *
 * @param int   $id   Product ID.
 * @param array $data Processed product data.
 */
do_action( 'my_plugin_after_process_product', $id, $data );
```

### Creating Custom Filters

```php
<?php
/**
 * Filters the product price.
 *
 * @since 1.0.0
 *
 * @param float $price      Product price.
 * @param int   $product_id Product ID.
 */
$price = apply_filters( 'my_plugin_product_price', $price, $product_id );

/**
 * Filters the notification message.
 *
 * @since 1.0.0
 *
 * @param string $message Notification message.
 * @param int    $user_id User ID.
 */
$message = apply_filters( 'my_plugin_notification_message', $message, $user_id );
```

## Hook Debugging

### Check if Hook Exists

```php
<?php
// Check if action exists
if ( has_action( 'wp_head', 'wp_generator' ) ) {
    // wp_generator is hooked
}

// Check if filter exists
if ( has_filter( 'the_content', 'wpautop' ) ) {
    // wpautop is hooked
}

// Get hook priority
$priority = has_filter( 'the_content', 'wpautop' );
if ( false !== $priority ) {
    // wpautop is hooked with priority $priority
}
```

### Check if Hook Fired

```php
<?php
// Check if action fired
if ( did_action( 'init' ) ) {
    // init action already fired
}

// Use in conditional logic
if ( 0 === did_action( 'my_plugin_init' ) ) {
    // Hook hasn't fired yet
    do_action( 'my_plugin_init' );
}
```

## Common WordPress Hooks

### Initialization Hooks

```php
<?php
// After plugins loaded (before themes)
add_action( 'plugins_loaded', 'my_plugin_load' );

// After theme setup
add_action( 'after_setup_theme', 'my_theme_setup' );

// After WordPress init (custom post types, taxonomies)
add_action( 'init', 'my_plugin_register_cpt' );

// After WordPress fully loaded
add_action( 'wp_loaded', 'my_plugin_after_load' );
```

### Frontend Hooks

```php
<?php
// Enqueue scripts/styles
add_action( 'wp_enqueue_scripts', 'my_plugin_scripts' );

// Add to head
add_action( 'wp_head', 'my_plugin_meta' );

// Before closing body tag
add_action( 'wp_footer', 'my_plugin_footer' );

// Filter content
add_filter( 'the_content', 'my_plugin_content' );
add_filter( 'the_title', 'my_plugin_title' );
```

### Admin Hooks

```php
<?php
// Admin initialization
add_action( 'admin_init', 'my_plugin_admin_init' );

// Add admin menu
add_action( 'admin_menu', 'my_plugin_menu' );

// Admin notices
add_action( 'admin_notices', 'my_plugin_notice' );

// Enqueue admin scripts
add_action( 'admin_enqueue_scripts', 'my_plugin_admin_scripts' );
```

### Post Hooks

```php
<?php
// Save post
add_action( 'save_post', 'my_plugin_save', 10, 2 );

// Post status transition
add_action( 'transition_post_status', 'my_plugin_status_change', 10, 3 );

// Delete post
add_action( 'delete_post', 'my_plugin_delete' );
```

## Hook Best Practices

### 1. Always Return in Filters

```php
<?php
// ✅ GOOD: Always return value
add_filter( 'the_content', function( $content ) {
    if ( ! is_single() ) {
        return $content; // Return original if condition not met
    }
    return $content . '<p>Signature</p>';
} );

// ❌ BAD: Not returning value
add_filter( 'the_content', function( $content ) {
    if ( ! is_single() ) {
        return; // Missing return value!
    }
    return $content . '<p>Signature</p>';
} );
```

### 2. Specify Accepted Args

```php
<?php
// ✅ GOOD: Specify number of accepted args
add_action( 'save_post', 'my_save_function', 10, 2 );
function my_save_function( $post_id, $post ) {
    // Use both parameters
}

// ❌ BAD: Not using available parameters
add_action( 'save_post', 'my_save_function' );
function my_save_function( $post_id ) {
    // Missing $post parameter
}
```

### 3. Remove Hooks on Deactivation

```php
<?php
register_deactivation_hook( __FILE__, function() {
    remove_action( 'init', 'my_plugin_init' );
    remove_filter( 'the_content', 'my_plugin_content' );
    
    // Clear scheduled events
    wp_clear_scheduled_hook( 'my_plugin_daily_event' );
} );
```

### 4. Document Hook Purpose

```php
<?php
/**
 * Fires after order is completed.
 *
 * This action allows plugins to perform additional tasks
 * after an order transitions to completed status.
 *
 * @since 1.0.0
 *
 * @param int   $order_id Order ID.
 * @param array $data     Order data array.
 */
do_action( 'my_plugin_order_completed', $order_id, $data );
```

### 5. Avoid Nested Hooks

```php
<?php
// ❌ BAD: Nested hook (infinite loop risk)
add_action( 'save_post', function( $post_id ) {
    wp_update_post( [ 'ID' => $post_id, 'post_title' => 'Modified' ] );
    // save_post fires again!
} );

// ✅ GOOD: Remove hook before update
add_action( 'save_post', function( $post_id ) {
    remove_action( 'save_post', __FUNCTION__ );
    wp_update_post( [ 'ID' => $post_id, 'post_title' => 'Modified' ] );
    add_action( 'save_post', __FUNCTION__ );
} );
```

## Reference

- WordPress Plugin API: https://developer.wordpress.org/plugins/hooks/
- WordPress Hook Reference: https://developer.wordpress.org/hooks/
- WordPress Codex: https://codex.wordpress.org/Plugin_API

**Remember**: Use actions for operations, filters for data modification, always prefix hook names, specify accepted args, and document your custom hooks!