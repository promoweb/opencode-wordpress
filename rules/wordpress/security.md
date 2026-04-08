paths:
  "**/*.php"

# WordPress Security

> This file extends [common/security.md](../common/security.md) with WordPress-specific security practices.

## WordPress Security Principles

### Core Security Rules

1. **Sanitize all input** before processing or storing
2. **Escape all output** before displaying
3. **Verify nonces** for forms and AJAX
4. **Check capabilities** before operations
5. **Use prepared statements** for database queries
6. **Never trust user data**

## Input Sanitization

### Text Fields

```php
<?php
// ✅ GOOD: Sanitize text input
$title = sanitize_text_field( $_POST['title'] );
$description = sanitize_textarea_field( $_POST['description'] );
$name = sanitize_text_field( $_REQUEST['name'] );

// ❌ BAD: No sanitization
$title = $_POST['title'];
$description = $_POST['description'];
```

### Email and URLs

```php
<?php
// ✅ GOOD: Sanitize email and URL
$email = sanitize_email( $_POST['email'] );
$url = esc_url_raw( $_POST['url'] );

// ❌ BAD: No sanitization
$email = $_POST['email'];
$url = $_POST['url'];
```

### Numbers

```php
<?php
// ✅ GOOD: Sanitize numbers
$quantity = absint( $_POST['quantity'] );
$price = floatval( $_POST['price'] );
$id = intval( $_POST['id'] );

// ❌ BAD: No sanitization
$quantity = $_POST['quantity'];
$price = $_POST['price'];
```

### Arrays

```php
<?php
// ✅ GOOD: Sanitize array
$ids = array_map( 'absint', $_POST['ids'] );
$names = array_map( 'sanitize_text_field', $_POST['names'] );

// Recursive array sanitization
function sanitize_array( array $array ): array {
    foreach ( $array as $key => $value ) {
        if ( is_array( $value ) ) {
            $array[ $key ] = sanitize_array( $value );
        } else {
            $array[ sanitize_key( $key ) ] = sanitize_text_field( $value );
        }
    }
    return $array;
}
```

### HTML Content

```php
<?php
// ✅ GOOD: Allow safe HTML
$html = wp_kses_post( $_POST['content'] );

// ✅ GOOD: Allow specific tags
$allowed_tags = [
    'a'      => [ 'href' => [], 'title' => [] ],
    'strong' => [],
    'em'     => [],
    'p'      => [],
];
$html = wp_kses( $_POST['content'], $allowed_tags );

// ❌ BAD: No sanitization (XSS risk)
$html = $_POST['content'];
```

## Output Escaping

### HTML Context

```php
<?php
// ✅ GOOD: Escape for HTML
echo '<h1>' . esc_html( $title ) . '</h1>';
echo '<p>' . esc_html( $description ) . '</p>';

// ❌ BAD: No escaping (XSS risk)
echo '<h1>' . $title . '</h1>';
echo '<p>' . $description . '</p>';
```

### HTML Attributes

```php
<?php
// ✅ GOOD: Escape for attribute
echo '<div class="' . esc_attr( $class ) . '">';
echo '<input type="text" value="' . esc_attr( $value ) . '">';

// ❌ BAD: No escaping (XSS risk)
echo '<div class="' . $class . '">';
echo '<input type="text" value="' . $value . '">';
```

### URLs

```php
<?php
// ✅ GOOD: Escape URLs
echo '<a href="' . esc_url( $url ) . '">Link</a>';
echo '<img src="' . esc_url( $image_url ) . '">';

// ❌ BAD: No escaping (XSS risk)
echo '<a href="' . $url . '">Link</a>';
echo '<img src="' . $image_url . '">';
```

### JavaScript

```php
<?php
// ✅ GOOD: Escape for JavaScript
echo '<script>var name = "' . esc_js( $name ) . '";</script>';

// ✅ GOOD: Use wp_json_encode
echo '<script>var data = ' . wp_json_encode( $data ) . ';</script>';

// ❌ BAD: No escaping (XSS risk)
echo '<script>var name = "' . $name . '";</script>';
```

### Context-Specific Escaping

```php
<?php
// HTML body
echo esc_html( $text );

// HTML attribute
echo '<div class="' . esc_attr( $class ) . '">';

// URL in href
echo '<a href="' . esc_url( $url ) . '">';

// JavaScript inline
echo '<script>var val = "' . esc_js( $value ) . '";</script>';

// JSON
wp_send_json( $data );
```

## Nonce Verification

### Creating Nonces

```php
<?php
// Form nonce
$nonce = wp_create_nonce( 'my_plugin_action' );
wp_nonce_field( 'my_plugin_action', 'my_plugin_nonce' );

// URL nonce
$url = wp_nonce_url( admin_url( 'admin.php?action=my_action' ), 'my_action' );

// AJAX nonce (localize script)
wp_localize_script( 'my-script', 'myPlugin', [
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce'   => wp_create_nonce( 'my_plugin_ajax' ),
] );
```

### Verifying Nonces

```php
<?php
// ✅ GOOD: Verify nonce in form handler
if ( ! isset( $_POST['my_plugin_nonce'] ) ||
     ! wp_verify_nonce( $_POST['my_plugin_nonce'], 'my_plugin_action' ) ) {
    wp_die( 'Security check failed' );
}

// ✅ GOOD: Verify nonce in AJAX handler
if ( ! isset( $_POST['nonce'] ) ||
     ! wp_verify_nonce( $_POST['nonce'], 'my_plugin_ajax' ) ) {
    wp_send_json_error( [ 'message' => 'Security check failed' ] );
}

// ✅ GOOD: Verify nonce in admin action
if ( ! isset( $_GET['_wpnonce'] ) ||
     ! wp_verify_nonce( $_GET['_wpnonce'], 'my_action' ) ) {
    wp_die( 'Security check failed' );
}

// ❌ BAD: No nonce verification
$data = $_POST['data'];
```

## Capability Checks

### Checking Capabilities

```php
<?php
// ✅ GOOD: Check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have permission to access this page' );
}

// ✅ GOOD: Check post-specific capability
if ( ! current_user_can( 'edit_post', $post_id ) ) {
    wp_die( 'You cannot edit this post' );
}

// ✅ GOOD: Check if user is logged in
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

// ❌ BAD: No capability check
update_option( 'setting', $_POST['value'] );
```

### Custom Capabilities

```php
<?php
// Add custom capability
$role = get_role( 'administrator' );
if ( $role ) {
    $role->add_cap( 'manage_my_plugin' );
}

// Check custom capability
if ( ! current_user_can( 'manage_my_plugin' ) ) {
    wp_die( 'Permission denied' );
}
```

## Database Security

### Prepared Statements

```php
<?php
global $wpdb;

// ✅ GOOD: Use prepared statements
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_author = %d AND post_status = %s",
        $user_id,
        'publish'
    )
);

// ✅ GOOD: INSERT with prepare
$wpdb->query(
    $wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}my_table (user_id, data) VALUES (%d, %s)",
        $user_id,
        $data
    )
);

// ✅ GOOD: UPDATE with prepare
$wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->prefix}my_table SET data = %s WHERE id = %d",
        $data,
        $id
    )
);

// ❌ BAD: Direct variable insertion (SQL injection)
$wpdb->query( "SELECT * FROM {$wpdb->posts} WHERE post_author = $user_id" );
```

### Safe Database Operations

```php
<?php
// ✅ GOOD: Use wpdb->insert, update, delete
$wpdb->insert(
    $wpdb->prefix . 'my_table',
    [ 'user_id' => $user_id, 'data' => $data ],
    [ '%d', '%s' ]
);

$wpdb->update(
    $wpdb->prefix . 'my_table',
    [ 'data' => $new_data ],
    [ 'id' => $id ],
    [ '%s' ],
    [ '%d' ]
);

$wpdb->delete(
    $wpdb->prefix . 'my_table',
    [ 'id' => $id ],
    [ '%d' ]
);
```

## File Upload Security

### Secure File Uploads

```php
<?php
// ✅ GOOD: Validate file uploads
function handle_file_upload( array $file ): array {
    // Check file type
    $allowed_types = [ 'image/jpeg', 'image/png', 'image/gif' ];
    $file_type = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
    
    if ( ! in_array( $file_type['type'], $allowed_types, true ) ) {
        return [ 'error' => 'Invalid file type' ];
    }
    
    // Check file size (max 5MB)
    $max_size = 5 * 1024 * 1024;
    if ( $file['size'] > $max_size ) {
        return [ 'error' => 'File too large' ];
    }
    
    // Use WordPress media handling
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    
    $upload = wp_handle_upload( $file, [ 'test_form' => false ] );
    
    if ( isset( $upload['error'] ) ) {
        return [ 'error' => $upload['error'] ];
    }
    
    return $upload;
}

// ❌ BAD: No validation
move_uploaded_file( $_FILES['file']['tmp_name'], $destination );
```

## AJAX Security

### Secure AJAX Handler

```php
<?php
// ✅ GOOD: Secure AJAX handler
add_action( 'wp_ajax_my_action', function() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) ||
         ! wp_verify_nonce( $_POST['nonce'], 'my_plugin_ajax' ) ) {
        wp_send_json_error( [ 'message' => 'Security check failed' ] );
    }
    
    // Check capabilities
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied' ] );
    }
    
    // Sanitize input
    $data = sanitize_text_field( $_POST['data'] );
    
    // Process
    $result = process_data( $data );
    
    wp_send_json_success( [ 'result' => $result ] );
} );
```

## REST API Security

### Secure REST Endpoint

```php
<?php
// ✅ GOOD: Secure REST endpoint
register_rest_route( 'my-plugin/v1', '/data', [
    'methods'  => 'POST',
    'callback' => function( WP_REST_Request $request ) {
        // Data is already sanitized by WordPress
        $data = $request->get_param( 'data' );
        
        // Process
        return rest_ensure_response( [ 'success' => true ] );
    },
    'permission_callback' => function() {
        return current_user_can( 'edit_posts' );
    },
    'args' => [
        'data' => [
            'required'          => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ],
    ],
] );
```

## Common Vulnerabilities

### XSS Prevention

```php
<?php
// ✅ GOOD: Escape all output
echo esc_html( $user_input );
echo '<a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';

// ❌ BAD: Unescaped output (XSS)
echo $user_input;
```

### CSRF Prevention

```php
<?php
// ✅ GOOD: Use nonces
if ( ! wp_verify_nonce( $_POST['nonce'], 'action' ) ) {
    wp_die( 'Security check failed' );
}

// ❌ BAD: No nonce (CSRF vulnerable)
update_option( 'setting', $_POST['value'] );
```

### SQL Injection Prevention

```php
<?php
// ✅ GOOD: Prepared statement
$wpdb->prepare( "SELECT * FROM table WHERE id = %d", $id );

// ❌ BAD: Direct variable (SQL injection)
$wpdb->query( "SELECT * FROM table WHERE id = $id" );
```

## Security Checklist

- [ ] Sanitize all `$_GET`, `$_POST`, `$_REQUEST` data
- [ ] Escape all output with appropriate function
- [ ] Verify nonces for all forms and AJAX
- [ ] Check user capabilities before operations
- [ ] Use prepared statements for database queries
- [ ] Validate file uploads (type, size)
- [ ] Use WordPress media library for file handling
- [ ] Set `WP_DEBUG` to `false` in production
- [ ] Keep WordPress and plugins updated
- [ ] Use SSL/HTTPS
- [ ] Implement rate limiting for login
- [ ] Use strong passwords
- [ ] Limit login attempts
- [ ] Remove unnecessary meta information

## Reference

- WordPress Security Handbook: https://developer.wordpress.org/apis/security/
- WordPress Data Validation: https://developer.wordpress.org/apis/data/data-validation/
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- WordPress Security Guide: https://wordpress.org/documentation/article/hardening-wordpress/

**Remember**: Always sanitize input, escape output, verify nonces, check capabilities, and use prepared statements!