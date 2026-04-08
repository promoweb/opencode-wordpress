# WordPress Build & Debug Fix

Resolve WordPress build errors, runtime errors, and debugging issues efficiently.

## Task

$ARGUMENTS

## Error Diagnosis

First, identify the error type and context:

### 1. Enable Debugging

```php
// Add to wp-config.php if not already present
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
```

Check error logs:
- `wp-content/debug.log`
- Server error log (`/var/log/php_errors.log` or similar)

### 2. Error Type Classification

**Fatal Errors**:
- White screen of death (WSOD)
- Parse errors
- Undefined functions/classes
- Memory exhausted

**Warnings**:
- Undefined variables
- Missing arguments
- Deprecated functions

**Notices**:
- Undefined indexes
- Undefined properties
- Deprecated features

## Common Error Patterns

### White Screen of Death (WSOD)

**Symptoms**: Blank page, no output

**Diagnostic Steps**:
1. Check `debug.log` for errors
2. Increase memory limit
3. Check for PHP syntax errors
4. Disable plugins to find conflict
5. Switch to default theme

**Common Fixes**:
```php
// Increase memory limit in wp-config.php
define( 'WP_MEMORY_LIMIT', '256M' );

// Fix syntax errors
// ❌ Wrong: Missing closing parenthesis
function my_function( {
    // code
}

// ✅ Fixed
function my_function() {
    // code
}

// Fix undefined function
// ❌ Wrong: Function doesn't exist or not loaded
$result = my_custom_function();

// ✅ Fixed: Check function exists
if ( function_exists( 'my_custom_function' ) ) {
    $result = my_custom_function();
}
```

### PHP Fatal Errors

**Call to undefined function**:
```php
// ❌ Error: Call to undefined function get_post_id()
$id = get_post_id();

// ✅ Fix: Use correct function
$id = get_the_ID();
```

**Class not found**:
```php
// ❌ Error: Class 'My_Class' not found
$object = new My_Class();

// ✅ Fix: Ensure class is loaded
require_once MY_PLUGIN_DIR . 'includes/class-my-class.php';
$object = new My_Class();
```

**Cannot redeclare function**:
```php
// ❌ Error: Cannot redeclare my_function()
function my_function() {
    // code
}

// ✅ Fix: Use function_exists wrapper
if ( ! function_exists( 'my_function' ) ) {
    function my_function() {
        // code
    }
}
```

**Memory exhausted**:
```php
// ❌ Error: Allowed memory size exhausted
// Processing large dataset

// ✅ Fix: Process in batches
$offset = 0;
$batch_size = 100;

while ( true ) {
    $posts = get_posts( [
        'posts_per_page' => $batch_size,
        'offset' => $offset,
    ] );
    
    if ( empty( $posts ) ) {
        break;
    }
    
    foreach ( $posts as $post ) {
        // Process post
    }
    
    $offset += $batch_size;
    wp_cache_flush(); // Clear cache
}
```

### WordPress Deprecation Notices

**create_function() deprecated (PHP 7.2+)**:
```php
// ❌ Deprecated
add_action( 'init', create_function( '', 'echo "Hello";' ) );

// ✅ Fixed
add_action( 'init', function() {
    echo "Hello";
} );
```

**each() deprecated (PHP 7.2+)**:
```php
// ❌ Deprecated
while ( list( $key, $value ) = each( $array ) ) {
    // process
}

// ✅ Fixed
foreach ( $array as $key => $value ) {
    // process
}
```

**wp_make_content_images_responsive() deprecated (WP 5.5+)**:
```php
// ❌ Deprecated
$content = wp_make_content_images_responsive( $content );

// ✅ Fixed
$content = wp_filter_content_tags( $content );
```

### Database Errors

**Table doesn't exist**:
```php
// ❌ Error: Table 'wp_my_table' doesn't exist
$results = $wpdb->get_results( "SELECT * FROM wp_my_table" );

// ✅ Fix: Check table exists or create it
$table_name = $wpdb->prefix . 'my_table';

if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
    // Table doesn't exist, create it
    $sql = "CREATE TABLE $table_name ( ... )";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
```

**Unknown column**:
```php
// ❌ Error: Unknown column 'nonexistent' in 'field list'
$wpdb->query( "SELECT nonexistent FROM {$wpdb->posts}" );

// ✅ Fix: Use correct column name
$wpdb->query( "SELECT post_title FROM {$wpdb->posts}" );
```

### Plugin Conflicts

**Diagnostic Steps**:
1. Disable all plugins
2. Enable one by one
3. Test after each enable
4. Identify conflicting plugin

**Fix Pattern**:
```php
// ❌ Conflict: Same hook priority
add_action( 'init', 'plugin_a_function', 10 );
add_action( 'init', 'plugin_b_function', 10 );

// ✅ Fix: Different priorities
add_action( 'init', 'plugin_a_function', 10 );
add_action( 'init', 'plugin_b_function', 20 );
```

### REST API Errors

**404 Not Found**:
```php
// ❌ Error: REST endpoint returns 404

// ✅ Fix: Flush rewrite rules
register_activation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );
```

**401 Unauthorized**:
```php
// ❌ Error: 401 Unauthorized

// ✅ Fix: Check authentication
register_rest_route( 'my-plugin/v1', '/endpoint', [
    'methods' => 'GET',
    'callback' => 'my_handler',
    'permission_callback' => function() {
        return current_user_can( 'read' );
    },
] );
```

### AJAX Errors

**Returns 0 or -1**:
```php
// ❌ Error: AJAX returns 0 or -1

// ✅ Fix: Ensure proper setup
// JavaScript
$.ajax({
    url: myPlugin.ajaxUrl,
    type: 'POST',
    data: {
        action: 'my_action',
        nonce: myPlugin.nonce,
    },
});

// PHP
add_action( 'wp_ajax_my_action', 'my_ajax_handler' );
add_action( 'wp_ajax_nopriv_my_action', 'my_public_ajax_handler' );
```

## Debugging Tools

### Query Monitor

Install Query Monitor plugin for:
- Database query analysis
- Hook debugging
- HTTP request tracking
- Performance profiling

### Manual Debugging

```php
// Log to debug.log
error_log( print_r( $variable, true ) );

// Var dump and die
var_dump( $variable );
wp_die();

// Backtrace
wp_debug_backtrace_summary();

// Check if function/class exists
if ( function_exists( 'my_function' ) ) {
    // Function exists
}

if ( class_exists( 'My_Class' ) ) {
    // Class exists
}
```

## Error Resolution Process

### Step 1: Reproduce
- Document steps to reproduce
- Note exact error message
- Identify context (admin, frontend, AJAX, REST API)

### Step 2: Diagnose
- Enable debugging
- Check error logs
- Review error message
- Check file and line number

### Step 3: Identify Cause
- Read error message carefully
- Review recent changes
- Check related code
- Use var_dump/error_log

### Step 4: Apply Fix
- Make minimal necessary change
- Add error handling if missing
- Test the fix
- Document the solution

### Step 5: Prevent Recurrence
- Add validation/sanitization
- Implement error handling
- Add tests if applicable
- Update documentation

## Output Format

```markdown
# Error Resolution Report

## Error Description
[Original error message]

## Root Cause
[What caused the error]

## Solution
[Step-by-step fix applied]

## Files Modified
1. [File path]: [Change description]

## Prevention
[How to prevent this error in future]

## Testing
[How to verify fix works]
```

## Quick Fixes Reference

- **Missing global**: Add `global $wpdb;` in function
- **Wrong hook timing**: Use correct hook (`init` vs `plugins_loaded`)
- **Permission denied**: Check file permissions or use WordPress Filesystem API
- **Class not found**: Autoload or require class file
- **Undefined variable**: Initialize variable or use `isset()` check

## Notes

- Always backup before making changes
- Test fixes in development environment first
- Add proper error handling
- Document fixes for future reference
- Consider adding automated tests

Use `wordpress-build-resolver` agent for complex debugging scenarios.