paths:
  "**/*.php"
  "**/composer.json"

# WordPress Coding Style

> This file extends [common/coding-style.md](../common/coding-style.md) with WordPress-specific coding standards.

## WordPress Coding Standards (WPCS)

Follow WordPress Coding Standards for all PHP code. Use PHP_CodeSniffer with WPCS rules.

### Installation

```bash
composer require --dev wp-coding-standards/wpcs
vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
vendor/bin/phpcs --standard=WordPress path/to/your/plugin
```

## Naming Conventions

### Variables and Functions

```php
<?php
// ✅ GOOD: lowercase with underscores (snake_case)
$post_id = get_the_ID();
$user_name = get_user_name();

function my_plugin_get_user_data( $user_id ) {
    return get_userdata( $user_id );
}

// ❌ BAD: camelCase
$postId = get_the_ID();
$userName = get_user_name();

function myPluginGetUserData( $userId ) {
    return get_userdata( $userId );
}
```

### Classes

```php
<?php
// ✅ GOOD: PascalCase for class names
class My_Plugin_Admin {
    // Class content
}

class My_Plugin_Settings_Page {
    // Class content
}

// ❌ BAD: snake_case for class names
class my_plugin_admin {
    // Class content
}
```

### Constants

```php
<?php
// ✅ GOOD: UPPERCASE with underscores
define( 'MY_PLUGIN_VERSION', '1.0.0' );
define( 'MY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// ❌ BAD: lowercase
define( 'my_plugin_version', '1.0.0' );
```

### File Names

```php
<?php
// ✅ GOOD: lowercase with hyphens
// File: class-my-plugin-admin.php
// File: my-plugin-functions.php

// ❌ BAD: mixed case or underscores
// File: class.MyPlugin.Admin.php
// File: my_plugin_functions.php
```

## Prefixing

**ALWAYS prefix** all functions, classes, variables, and database entries to avoid conflicts.

```php
<?php
// ✅ GOOD: Prefixed with plugin slug
function my_plugin_get_option( $key ) {
    return get_option( 'my_plugin_' . $key );
}

class My_Plugin_Admin {
    // Class content
}

$my_plugin_settings = get_option( 'my_plugin_settings' );

// ❌ BAD: No prefix (conflict risk)
function get_option( $key ) { // Overrides WordPress function!
    return get_option( $key );
}

class Admin { // Generic name, conflict risk
    // Class content
}
```

## Yoda Conditions

Use Yoda conditions (variable on right, value on left) to prevent accidental assignment.

```php
<?php
// ✅ GOOD: Yoda conditions
if ( true === $is_valid ) {
    // Do something
}

if ( 0 === $count ) {
    // Do something
}

if ( 'publish' === get_post_status() ) {
    // Do something
}

// ❌ BAD: Regular conditions (assignment risk)
if ( $is_valid = true ) { // Bug: assignment instead of comparison
    // Do something
}

if ( $count == 0 ) { // Less readable
    // Do something
}
```

## Brace Style

Use Allman style (opening brace on new line) for control structures.

```php
<?php
// ✅ GOOD: Allman style
if ( condition ) {
    // Do something
} else {
    // Do something else
}

foreach ( $items as $item ) {
    // Process item
}

// ❌ BAD: K&R style
if ( condition ) {
    // Do something
} else {
    // Do something else
}
```

## Spacing

### Arrays

```php
<?php
// ✅ GOOD: Spaces around array items
$array = [
    'key1' => 'value1',
    'key2' => 'value2',
];

$items = [ 'item1', 'item2', 'item3' ];

// ❌ BAD: No spaces
$array = ['key1'=>'value1','key2'=>'value2'];
```

### Functions

```php
<?php
// ✅ GOOD: Spaces around parentheses
function my_function( $param1, $param2 ) {
    return $param1 . $param2;
}

$result = my_function( 'value1', 'value2' );

// ❌ BAD: No spaces
function my_function($param1,$param2) {
    return $param1 . $param2;
}

$result = my_function('value1','value2');
```

### Operators

```php
<?php
// ✅ GOOD: Spaces around operators
$value = $a + $b;
$sum   = $x + $y;

// ❌ BAD: No spaces
$value=$a+$b;
```

## Indentation

Use tabs (not spaces) for indentation. Use spaces for alignment.

```php
<?php
// ✅ GOOD: Tabs for indentation
function my_function() {
	$var = 'value'; // Tab

	if ( condition ) {
		do_something(); // Tab
	}

	$items = [
		'key1' => 'value1', // Tab + spaces for alignment
		'key2' => 'value2',
	];
}

// ❌ BAD: Spaces for indentation
function my_function() {
    $var = 'value'; // Spaces (should be tab)
}
```

## Quotes

Use single quotes for strings unless double quotes are necessary.

```php
<?php
// ✅ GOOD: Single quotes for simple strings
$name = 'John Doe';
$path = '/path/to/file';

// ✅ GOOD: Double quotes for strings with variables or escape sequences
$message = "Hello, $name!";
$path   = "C:\\Users\\John\\Documents";

// ❌ BAD: Double quotes for simple strings
$name = "John Doe";
```

## Sanitization and Escaping

**ALWAYS sanitize input and escape output** (see security rule).

```php
<?php
// ✅ GOOD: Sanitize input
$title = sanitize_text_field( $_POST['title'] );
$email = sanitize_email( $_POST['email'] );

// ✅ GOOD: Escape output
echo esc_html( $title );
echo '<a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';

// ❌ BAD: No sanitization or escaping
$title = $_POST['title'];
echo $title;
```

## PHP Tags

Use full PHP tags. Always close PHP tags in files that contain only PHP.

```php
<?php
// ✅ GOOD: Full PHP tag
<?php
class My_Plugin {
    // Class content
}

// ✅ GOOD: Omit closing tag in PHP-only files
<?php
function my_function() {
    return 'value';
}
// No closing tag

// ❌ BAD: Short PHP tag
<?
echo 'value';
?>
```

## DocBlocks

Document all functions, classes, and methods with DocBlocks.

```php
<?php
/**
 * Get user data by user ID.
 *
 * @since 1.0.0
 *
 * @param int $user_id User ID.
 * @return object|false User data object or false on failure.
 */
function my_plugin_get_user_data( $user_id ) {
    return get_userdata( $user_id );
}

/**
 * Plugin admin class.
 *
 * @since 1.0.0
 */
class My_Plugin_Admin {
    /**
     * Initialize admin functionality.
     *
     * @since 1.0.0
     */
    public function init() {
        // Initialize
    }
}
```

## Internationalization

Always use internationalization functions for translatable strings.

```php
<?php
// ✅ GOOD: Internationalized strings
$text = __( 'Hello World', 'my-plugin' );
_e( 'Welcome to my plugin', 'my-plugin' );
printf(
    /* translators: %s: Name of the person */
    __( 'Hello, %s!', 'my-plugin' ),
    $name
);

// ❌ BAD: Hardcoded strings
$text = 'Hello World';
echo 'Welcome to my plugin';
```

## File Organization

Organize plugin/theme files logically.

```
my-plugin/
├── my-plugin.php          # Main plugin file
├── includes/              # Include files
│   ├── class-admin.php    # Admin class
│   ├── class-public.php   # Public class
│   └── functions.php      # Helper functions
├── templates/             # Template files
├── assets/                # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── images/
└── languages/             # Translation files
```

## Error Handling

```php
<?php
// ✅ GOOD: Check for errors
$result = some_function();
if ( is_wp_error( $result ) ) {
    wp_die( $result->get_error_message() );
}

// ✅ GOOD: Return WP_Error for custom errors
function my_function() {
    if ( ! $condition ) {
        return new WP_Error( 'invalid_data', 'Invalid data provided.' );
    }
    
    return $data;
}

// ❌ BAD: Suppress errors
$result = @some_function();
```

## Database Queries

Always use prepared statements (see database rule).

```php
<?php
// ✅ GOOD: Prepared statement
$wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_author = %d",
    $user_id
);

// ❌ BAD: Direct variable insertion (SQL injection risk)
$wpdb->query( "SELECT * FROM {$wpdb->posts} WHERE post_author = $user_id" );
```

## Hooks

Use proper hook naming and callbacks (see hooks rule).

```php
<?php
// ✅ GOOD: Prefixed hook names
add_action( 'my_plugin_init', 'my_plugin_init_callback' );
apply_filters( 'my_plugin_title', $title );

// ❌ BAD: Generic hook names
add_action( 'init', 'my_callback' ); // Could conflict
apply_filters( 'title', $title ); // Too generic
```

## Class Autoloading

Use Composer autoloader or custom autoloader for classes.

```php
<?php
// composer.json
{
    "autoload": {
        "psr-4": {
            "My_Plugin\\": "includes/"
        }
    }
}

// Usage
use My_Plugin\Admin;
$admin = new Admin();
```

## Require vs Include

Use `require_once` for essential files. Use `include_once` for optional files.

```php
<?php
// ✅ GOOD: Essential files
require_once MY_PLUGIN_DIR . 'includes/functions.php';
require_once MY_PLUGIN_DIR . 'includes/class-admin.php';

// ✅ GOOD: Optional files (won't break if missing)
if ( file_exists( MY_PLUGIN_DIR . 'includes/custom.php' ) ) {
    include_once MY_PLUGIN_DIR . 'includes/custom.php';
}
```

## Type Declarations

Use PHP 7.4+ type declarations for better code clarity.

```php
<?php
// ✅ GOOD: Type declarations
function get_user_name( int $user_id ): string {
    $user = get_userdata( $user_id );
    return $user ? $user->display_name : '';
}

class My_Class {
    private string $name;
    private int $count = 0;

    public function __construct( string $name ) {
        $this->name = $name;
    }

    public function get_name(): string {
        return $this->name;
    }
}
```

## Reference

- WordPress Coding Standards: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/
- PHP_CodeSniffer: https://github.com/squizlabs/PHP_CodeSniffer
- WPCS: https://github.com/WordPress/WordPress-Coding-Standards
- PHP The Right Way: https://phptherightway.com/

**Remember**: Follow WordPress Coding Standards, always prefix your code, sanitize input, escape output, and document your functions!