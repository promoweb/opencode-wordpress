# OpenCode Plugin Example

A comprehensive WordPress plugin demonstrating modern development practices, coding standards, and best patterns.

## Features

- **Custom Post Types**: Full implementation with custom columns and meta boxes
- **Custom Taxonomies**: Hierarchical taxonomy for organizing content
- **Settings API**: Complete implementation with sanitization and validation
- **Admin Pages**: Custom admin menu with dashboard integration
- **Security**: Nonce verification, capability checks, proper escaping
- **Performance**: Optimized queries, caching support
- **Internationalization**: Full i18n support with text domains
- **Uninstall Routine**: Clean uninstallation with data removal

## Plugin Structure

```
opencode-plugin-example/
├── includes/
│   ├── class-plugin.php      # Main plugin class
│   ├── class-admin.php       # Admin functionality
│   ├── class-settings.php    # Settings API implementation
│   └── class-cpt.php         # Custom Post Type registration
├── admin/
│   ├── css/
│   │   └── admin.css         # Admin stylesheet
│   ├── js/
│   │   └── admin.js          # Admin JavaScript
│   └── views/
│       ├── admin-page.php    # Main admin page template
│       └── settings-page.php # Settings page template
├── assets/
│   ├── css/
│   │   └── frontend.css      # Frontend stylesheet
│   └── js/
│       └── frontend.js       # Frontend JavaScript
├── languages/                 # Translation files
├── my-plugin.php             # Main plugin file
├── uninstall.php             # Uninstall handler
└── README.md                 # This file
```

## Installation

### Manual Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin in WordPress Dashboard → Plugins
3. Configure settings in Settings → OpenCode Plugin

### Composer Installation

```bash
composer require opencode/plugin-example
```

## Configuration

### Basic Settings

Navigate to **Settings → OpenCode Plugin** to configure:

- **Enable Feature**: Toggle the main plugin functionality
- **API Key**: Enter your API key for external services
- **Cache Duration**: Set cache duration for API responses

### Advanced Configuration

```php
// Disable plugin features conditionally
add_filter( 'opencode_plugin_enabled', '__return_false' );

// Customize cache duration
add_filter( 'opencode_plugin_cache_duration', function( $duration ) {
    return HOUR_IN_SECONDS * 2;
} );

// Add custom settings field
add_action( 'opencode_plugin_settings_fields', function( $fields ) {
    $fields['custom_field'] = array(
        'label' => 'Custom Field',
        'type'  => 'text',
    );
    return $fields;
} );
```

## Usage

### Custom Post Type

The plugin registers a custom post type `opencode_item`:

```php
// Query custom post type
$args = array(
    'post_type'      => 'opencode_item',
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) :
    while ( $query->have_posts() ) : $query->the_post();
        // Display content
        the_title();
        the_content();
    endwhile;
    wp_reset_postdata();
endif;
```

### Get Plugin Settings

```php
// Get individual setting
$api_key = \OpenCode_Plugin_Example\Settings::get_option( 'api_key' );

// Get setting with default value
$cache_duration = \OpenCode_Plugin_Example\Settings::get_option( 'cache_duration', '3600' );

// Check if feature is enabled
$enabled = \OpenCode_Plugin_Example\Settings::get_option( 'enable_feature', false );
```

### Custom Meta Data

```php
// Get custom post meta
$value = get_post_meta( $post_id, '_opencode_item_value', true );

// Update custom post meta
update_post_meta( $post_id, '_opencode_item_value', 'new value' );

// Delete custom post meta
delete_post_meta( $post_id, '_opencode_item_value' );
```

## Hooks

### Actions

```php
// Fires after plugin initialization
do_action( 'opencode_plugin_init' );

// Fires before settings are saved
do_action( 'opencode_plugin_before_save_settings', $settings );

// Fires after settings are saved
do_action( 'opencode_plugin_after_save_settings', $settings );
```

### Filters

```php
// Filter settings before saving
$settings = apply_filters( 'opencode_plugin_save_settings', $settings );

// Filter custom post type arguments
$args = apply_filters( 'opencode_plugin_cpt_args', $args );

// Filter custom taxonomy arguments
$args = apply_filters( 'opencode_plugin_taxonomy_args', $args );

// Filter plugin capabilities
$capabilities = apply_filters( 'opencode_plugin_capabilities', $capabilities );
```

## AJAX Handlers

The plugin includes built-in AJAX handlers:

```javascript
// Dismiss admin notice
jQuery.ajax({
    url: opencodePluginAdmin.ajaxUrl,
    type: 'POST',
    data: {
        action: 'opencode_dismiss_notice',
        notice_id: 'my-notice',
        nonce: opencodePluginAdmin.nonce
    },
    success: function(response) {
        console.log(response);
    }
});

// Toggle setting
jQuery.ajax({
    url: opencodePluginAdmin.ajaxUrl,
    type: 'POST',
    data: {
        action: 'opencode_toggle_setting',
        setting_key: 'enable_feature',
        setting_value: 1,
        nonce: opencodePluginAdmin.nonce
    }
});
```

## Security

### Nonce Verification

Always verify nonces for forms and AJAX requests:

```php
// Verify nonce in form submission
if ( ! isset( $_POST['my_nonce'] ) || ! wp_verify_nonce( $_POST['my_nonce'], 'my_action' ) ) {
    wp_die( 'Security check failed' );
}

// Verify nonce in AJAX
check_ajax_referer( 'my_ajax_action', 'nonce' );
```

### Capability Checks

Always check user capabilities:

```php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have permission to access this page' );
}
```

### Data Escaping

Always escape output:

```php
// Escape HTML
echo esc_html( $text );

// Escape attributes
echo esc_attr( $attr );

// Escape URLs
echo esc_url( $url );

// Escape for translation
echo esc_html__( 'Text', 'textdomain' );
```

## Performance

### Transients

Use transients for caching:

```php
// Get cached data
$data = get_transient( 'opencode_plugin_cache_key' );

if ( false === $data ) {
    // Fetch data
    $data = expensive_operation();
    
    // Cache for 1 hour
    set_transient( 'opencode_plugin_cache_key', $data, HOUR_IN_SECONDS );
}
```

### Optimized Queries

```php
// Use fields parameter to fetch only IDs
$posts = get_posts( array(
    'post_type'      => 'opencode_item',
    'posts_per_page' => -1,
    'fields'         => 'ids', // Only fetch post IDs
) );

// Use no_found_rows for pagination-free queries
$posts = get_posts( array(
    'post_type'      => 'opencode_item',
    'no_found_rows'  => true, // Skip counting total rows
) );
```

## Testing

### Unit Testing

```bash
# Run PHPUnit tests
phpunit

# Run specific test file
phpunit tests/test-main.php

# Run with code coverage
phpunit --coverage-html coverage/
```

### Integration Testing

```php
// Test custom post type registration
public function test_custom_post_type_registered() {
    $this->assertTrue( post_type_exists( 'opencode_item' ) );
}

// Test settings
public function test_get_option() {
    $value = \OpenCode_Plugin_Example\Settings::get_option( 'api_key' );
    $this->assertNotEmpty( $value );
}
```

## Debugging

### Debug Mode

Enable debug mode in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

### Query Monitor

Use Query Monitor plugin to debug:

- Database queries
- HTTP requests
- Hooks and actions
- Conditionals
- PHP errors

## Internationalization

### Load Text Domain

The plugin loads translations automatically from `/languages/` directory.

### Translation Functions

```php
// Basic translation
__( 'Text', 'opencode-plugin-example' );

// Escaped translation
esc_html__( 'Text', 'opencode-plugin-example' );

// Translation with context
_x( 'Text', 'Context', 'opencode-plugin-example' );

// Plural translation
printf( _n( 'One item', '%s items', $count, 'opencode-plugin-example' ), number_format_i18n( $count ) );
```

## Uninstalling

The plugin cleans up all data on uninstall:

- Deletes all plugin options
- Removes custom post type posts
- Clears scheduled hooks
- Drops custom database tables

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

## License

GPL v2 or later

## Credits

Developed by OpenCode

## Changelog

### 1.0.0
- Initial release
- Custom Post Type with taxonomy
- Settings API implementation
- Admin pages and meta boxes
- Security and performance optimizations
- Internationalization support