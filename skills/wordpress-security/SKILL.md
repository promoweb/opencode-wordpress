name
wordpress-security

description
WordPress security patterns for input sanitization, output escaping, nonce verification, CSRF/XSS/SQL injection prevention, capability checks, and secure coding practices for production-grade WordPress security.

origin
OpenCode WordPress

# WordPress Security

Production-grade WordPress security patterns for secure plugin/theme development.

## When to Use

- Building WordPress plugins or themes
- Handling user input (forms, AJAX, API)
- Outputting data to browser
- Processing database queries
- Creating admin pages
- Implementing authentication/authorization
- Preventing CSRF/XSS/SQL injection attacks
- Secure file uploads
- API endpoint security

## How It Works

- Validate all input data before processing
- Sanitize all input data before storage
- Escape all output data before display
- Verify nonces for forms and AJAX
- Check user capabilities before operations
- Use prepared statements for database queries
- Follow WordPress security best practices
- Never trust user input

## Examples

### Input Sanitization

#### Text Fields

```php
<?php
/**
 * Sanitize text input
 */

// Basic text field
$text = sanitize_text_field($_POST['text_field']);

// Textarea
$textarea = sanitize_textarea_field($_POST['textarea_field']);

// Title (removes HTML, converts entities)
$title = sanitize_title($_POST['title_field']);

// Email
$email = sanitize_email($_POST['email']);

// URL
$url = esc_url_raw($_POST['url_field']);

// Username
$username = sanitize_user($_POST['username']);

// Key (lowercase alphanumeric, dashes, underscores)
$key = sanitize_key($_POST['key_field']);

// File name
$filename = sanitize_file_name($_POST['filename']);

// HTML content (removes unsafe HTML)
$html = wp_kses_post($_POST['html_content']);

// Meta key (for custom fields)
$meta_key = sanitize_meta('_my_custom_key', $_POST['meta_key'], 'post');
```

#### Numbers

```php
<?php
/**
 * Sanitize numeric input
 */

// Integer
$int_value = absint($_POST['int_field']);

// Float
$float_value = floatval($_POST['float_field']);

// Positive number
$positive_number = abs(floatval($_POST['positive_field']));

// Integer within range
$limited = max(1, min(100, absint($_POST['limited_field'])));

// Hex color
$hex_color = sanitize_hex_color($_POST['hex_color']);

// Hex color no hash
$hex_color_no_hash = sanitize_hex_color_no_hash($_POST['hex_color_no_hash']);
```

#### Arrays

```php
<?php
/**
 * Sanitize array input
 */

// Simple array
$array_data = array_map('sanitize_text_field', $_POST['array_field']);

// Nested array
function sanitize_nested_array(array $data): array {
    $sanitized = [];
    
    foreach ($data as $key => $value) {
        $sanitized_key = sanitize_key($key);
        
        if (is_array($value)) {
            $sanitized[$sanitized_key] = sanitize_nested_array($value);
        } else {
            $sanitized[$sanitized_key] = sanitize_text_field($value);
        }
    }
    
    return $sanitized;
}

$sanitized_array = sanitize_nested_array($_POST['nested_array']);

// Specific structure validation
function validate_product_data(array $data): array {
    return [
        'name'     => sanitize_text_field($data['name'] ?? ''),
        'price'    => floatval($data['price'] ?? 0),
        'quantity' => absint($data['quantity'] ?? 1),
        'sku'      => sanitize_text_field($data['sku'] ?? ''),
        'active'   => isset($data['active']) ? (bool) $data['active'] : false,
    ];
}
```

### Output Escaping

#### HTML Context

```php
<?php
/**
 * Escape output for HTML context
 */

// Escape HTML entities
echo '<h1>' . esc_html($title) . '</h1>';

// Escape HTML attribute
echo '<div class="' . esc_attr($class_name) . '"></div>';

// Escape URL
echo '<a href="' . esc_url($url) . '">Link</a>';

// Escape URL with additional protocols allowed
echo '<a href="' . esc_url($url, ['http', 'https', 'mailto']) . '">Link</a>';

// Escape JavaScript string
echo '<script>var name = "' . esc_js($name) . '";</script>';

// Escape for textarea
echo '<textarea>' . esc_textarea($content) . '</textarea>';

// Escape XML
echo '<node>' . esc_xml($content) . '</node>';
```

#### Context-Specific Escaping

```php
<?php
/**
 * Escape output based on context
 */

// HTML body content
function render_html_content(string $content): void {
    echo '<div class="content">';
    echo esc_html($content);
    echo '</div>';
}

// HTML attribute
function render_attribute(string $attr, string $value): void {
    echo esc_attr($attr) . '="' . esc_attr($value) . '"';
}

// JavaScript inline
function render_js_variable(string $var_name, string $value): void {
    echo '<script>';
    echo 'var ' . esc_js($var_name) . ' = "' . esc_js($value) . '";';
    echo '</script>';
}

// JSON output
function render_json_response(array $data): void {
    wp_send_json_success($data);
}

// URL in href
function render_link(string $url, string $text): void {
    printf(
        '<a href="%s">%s</a>',
        esc_url($url),
        esc_html($text)
    );
}

// Complex HTML with allowed tags
function render_rich_content(string $content): void {
    // Allow specific tags
    $allowed_tags = [
        'a'      => ['href' => [], 'title' => []],
        'strong' => [],
        'em'     => [],
        'p'      => [],
        'br'     => [],
    ];
    
    echo wp_kses($content, $allowed_tags);
}

// Full HTML content (kses)
function render_full_html(string $content): void {
    echo wp_kses_post($content);
}
```

### Nonce Verification

#### Creating Nonces

```php
<?php
/**
 * Create nonces for security
 */

// Form nonce
$nonce = wp_create_nonce('my_plugin_form_action');

// Add nonce to form
function render_secure_form(): void {
    $nonce = wp_create_nonce('my_plugin_save_data');
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('my_plugin_save_data', 'my_plugin_nonce'); ?>
        
        <!-- Alternative: manual nonce field -->
        <input type="hidden" name="my_plugin_nonce" value="<?php echo esc_attr($nonce); ?>">
        
        <input type="text" name="my_field">
        <button type="submit">Submit</button>
    </form>
    <?php
}

// AJAX nonce (localize script)
function enqueue_ajax_script(): void {
    wp_enqueue_script('my-plugin-ajax', MY_PLUGIN_URI . 'assets/js/ajax.js');
    
    wp_localize_script('my-plugin-ajax', 'myPluginAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('my_plugin_ajax_nonce'),
    ]);
}

add_action('wp_enqueue_scripts', 'enqueue_ajax_script');

// Admin nonce for actions
function render_admin_action_link(int $post_id): void {
    $url = wp_nonce_url(
        admin_url('admin.php?action=my_plugin_action&post_id=' . $post_id),
        'my_plugin_action_' . $post_id
    );
    
    echo '<a href="' . esc_url($url) . '">' . esc_html__('Process', 'my-plugin') . '</a>';
}
```

#### Verifying Nonces

```php
<?php
/**
 * Verify nonces for security
 */

// Form submission verification
function handle_form_submission(): void {
    // Check if nonce is set
    if (!isset($_POST['my_plugin_nonce'])) {
        wp_die('Security check failed: nonce missing');
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['my_plugin_nonce'], 'my_plugin_save_data')) {
        wp_die('Security check failed: invalid nonce');
    }
    
    // Process form data
    $data = sanitize_text_field($_POST['my_field']);
    
    // ... save data
}

add_action('admin_init', 'handle_form_submission');

// Admin action verification
function handle_admin_action(): void {
    // Check nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'my_plugin_action_' . $_GET['post_id'])) {
        wp_die('Security check failed');
    }
    
    // Verify permissions
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied');
    }
    
    // Process action
    $post_id = absint($_GET['post_id']);
    // ... process
}

add_action('admin_action_my_plugin_action', 'handle_admin_action');

// AJAX request verification
function handle_ajax_request(): void {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_plugin_ajax_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }
    
    // Process AJAX request
    $data = sanitize_text_field($_POST['data']);
    
    wp_send_json_success(['result' => 'processed']);
}

add_action('wp_ajax_my_plugin_ajax', 'handle_ajax_request');
add_action('wp_ajax_nopriv_my_plugin_ajax', 'handle_ajax_public_request');

// REST API nonce verification
function verify_rest_nonce(\WP_REST_Request $request): bool {
    $nonce = $request->get_header('X-WP-Nonce');
    
    if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
        return false;
    }
    
    return true;
}
```

### Capability Checks

#### Checking Capabilities

```php
<?php
/**
 * Check user capabilities
 */

// Check if user can manage options (admin)
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page');
}

// Check if user can edit posts
if (!current_user_can('edit_posts')) {
    wp_die('Permission denied');
}

// Check if user can edit specific post
if (!current_user_can('edit_post', $post_id)) {
    wp_die('You cannot edit this post');
}

// Check if user can upload files
if (!current_user_can('upload_files')) {
    wp_die('Permission denied');
}

// Check if user can manage WooCommerce
if (!current_user_can('manage_woocommerce')) {
    wp_die('Permission denied');
}

// Multiple capabilities check (AND)
if (!current_user_can('manage_options') || !current_user_can('edit_posts')) {
    wp_die('Insufficient permissions');
}

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('You must be logged in');
}

// Get current user ID
$user_id = get_current_user_id();

if (!$user_id) {
    wp_die('User not logged in');
}
```

#### Custom Capabilities

```php
<?php
/**
 * Add custom capabilities
 */

// Add custom capability to administrator role
function add_custom_capabilities(): void {
    $admin = get_role('administrator');
    
    if ($admin) {
        $admin->add_cap('manage_my_plugin');
        $admin->add_cap('edit_my_custom_post');
        $admin->add_cap('delete_my_custom_post');
    }
    
    // Add to editor role
    $editor = get_role('editor');
    
    if ($editor) {
        $editor->add_cap('edit_my_custom_post');
    }
}

add_action('admin_init', 'add_custom_capabilities');

// Remove custom capability on plugin deactivation
function remove_custom_capabilities(): void {
    $admin = get_role('administrator');
    
    if ($admin) {
        $admin->remove_cap('manage_my_plugin');
        $admin->remove_cap('edit_my_custom_post');
        $admin->remove_cap('delete_my_custom_post');
    }
}

register_deactivation_hook(__FILE__, 'remove_custom_capabilities');

// Check custom capability
function check_my_plugin_capability(): bool {
    return current_user_can('manage_my_plugin');
}

// Use in admin pages
function my_plugin_admin_page(): void {
    if (!current_user_can('manage_my_plugin')) {
        wp_die('You do not have permission to access this page');
    }
    
    // Render admin page
}
```

### Database Security

#### Prepared Statements with wpdb

```php
<?php
/**
 * Secure database operations with prepared statements
 */

global $wpdb;

// SAFE: Prepared statement with %d (integer)
$user_id = absint($user_id);
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}my_table WHERE user_id = %d",
        $user_id
    )
);

// SAFE: Prepared statement with %s (string)
$name = sanitize_text_field($name);
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}my_table WHERE name = %s",
        $name
    )
);

// SAFE: Multiple placeholders
$post_id = absint($post_id);
$status = sanitize_text_field($status);
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}my_table WHERE post_id = %d AND status = %s",
        $post_id,
        $status
    )
);

// SAFE: LIKE query with escaping
$search = '%' . $wpdb->esc_like(sanitize_text_field($search_term)) . '%';
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}my_table WHERE name LIKE %s",
        $search
    )
);

// SAFE: INSERT with prepare
$inserted = $wpdb->query(
    $wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}my_table (user_id, data_key, data_value) VALUES (%d, %s, %s)",
        absint($user_id),
        sanitize_text_field($key),
        maybe_serialize($value)
    )
);

// SAFE: UPDATE with prepare
$updated = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->prefix}my_table SET data_value = %s WHERE id = %d",
        maybe_serialize($value),
        absint($id)
    )
);

// SAFE: DELETE with prepare
$deleted = $wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}my_table WHERE id = %d",
        absint($id)
    )
);

// UNSAFE: Never concatenate variables directly
// DO NOT DO THIS:
// $wpdb->query("SELECT * FROM {$wpdb->prefix}my_table WHERE user_id = $user_id"); // SQL INJECTION RISK!

// UNSAFE: Never trust user input in queries
// DO NOT DO THIS:
// $wpdb->query("SELECT * FROM {$wpdb->prefix}my_table WHERE name = '{$_GET['name']}'"); // SQL INJECTION RISK!
```

#### Safe CRUD Operations

```php
<?php
/**
 * Safe database CRUD operations
 */

class Safe_Database {
    /**
     * Get single row by ID
     */
    public static function get_row(int $id): ?array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'my_custom_table';
        
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
        
        return $row ?: null;
    }

    /**
     * Get multiple rows with conditions
     */
    public static function get_rows(array $args = []): array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'my_custom_table';
        
        $defaults = [
            'status'     => 'active',
            'limit'      => 10,
            'offset'     => 0,
            'orderby'    => 'created_at',
            'order'      => 'DESC',
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        // Whitelist orderby
        $allowed_orderby = ['id', 'created_at', 'updated_at', 'status'];
        $orderby = in_array($args['orderby'], $allowed_orderby, true) 
            ? $args['orderby'] 
            : 'created_at';
        
        // Whitelist order
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        // Sanitize status
        $status = sanitize_text_field($args['status']);
        $limit = absint($args['limit']);
        $offset = absint($args['offset']);
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = %s ORDER BY $orderby $order LIMIT %d OFFSET %d",
                $status,
                $limit,
                $offset
            ),
            ARRAY_A
        );
        
        return $results ?: [];
    }

    /**
     * Insert new row
     */
    public static function insert(array $data): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'my_custom_table';
        
        // Sanitize all data
        $sanitized_data = [
            'user_id'    => absint($data['user_id']),
            'post_id'    => absint($data['post_id']),
            'data_key'   => sanitize_text_field($data['data_key']),
            'data_value' => maybe_serialize($data['data_value']),
            'status'     => sanitize_text_field($data['status'] ?? 'active'),
        ];
        
        $format = ['%d', '%d', '%s', '%s', '%s'];
        
        $inserted = $wpdb->insert($table_name, $sanitized_data, $format);
        
        return $inserted ? $wpdb->insert_id : 0;
    }

    /**
     * Update row
     */
    public static function update(int $id, array $data): bool {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'my_custom_table';
        
        // Sanitize update data
        $sanitized_data = [];
        $format = [];
        
        if (isset($data['data_value'])) {
            $sanitized_data['data_value'] = maybe_serialize($data['data_value']);
            $format[] = '%s';
        }
        
        if (isset($data['status'])) {
            $sanitized_data['status'] = sanitize_text_field($data['status']);
            $format[] = '%s';
        }
        
        if (empty($sanitized_data)) {
            return false;
        }
        
        $updated = $wpdb->update(
            $table_name,
            $sanitized_data,
            ['id' => $id],
            $format,
            ['%d']
        );
        
        return $updated !== false;
    }

    /**
     * Delete row
     */
    public static function delete(int $id): bool {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'my_custom_table';
        
        $deleted = $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );
        
        return $deleted !== false;
    }
}
```

### CSRF Protection

#### Form CSRF Protection

```php
<?php
/**
 * Complete CSRF protection for forms
 */

// Render secure form
function render_my_plugin_form(): void {
    // Check capabilities first
    if (!current_user_can('edit_posts')) {
        echo '<p>' . esc_html__('Permission denied', 'my-plugin') . '</p>';
        return;
    }
    
    $nonce_action = 'my_plugin_form_submit';
    $nonce_name = 'my_plugin_form_nonce';
    ?>
    <form method="post" action="" id="my-plugin-form">
        <?php wp_nonce_field($nonce_action, $nonce_name); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="field_name"><?php esc_html_e('Field Name', 'my-plugin'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           name="field_name" 
                           id="field_name" 
                           value="" 
                           class="regular-text">
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" name="submit" class="button button-primary">
                <?php esc_html_e('Submit', 'my-plugin'); ?>
            </button>
        </p>
    </form>
    <?php
}

// Process form submission
function process_my_plugin_form(): void {
    // Check if form was submitted
    if (!isset($_POST['submit'])) {
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['my_plugin_form_nonce']) || 
        !wp_verify_nonce($_POST['my_plugin_form_nonce'], 'my_plugin_form_submit')) {
        wp_die(
            '<h1>' . esc_html__('Security Error', 'my-plugin') . '</h1>' .
            '<p>' . esc_html__('Security token expired. Please try again.', 'my-plugin') . '</p>',
            esc_html__('Security Error', 'my-plugin'),
            ['response' => 403]
        );
    }
    
    // Check capabilities
    if (!current_user_can('edit_posts')) {
        wp_die(
            '<h1>' . esc_html__('Permission Denied', 'my-plugin') . '</h1>',
            esc_html__('Permission Denied', 'my-plugin'),
            ['response' => 403]
        );
    }
    
    // Sanitize input
    $field_name = sanitize_text_field($_POST['field_name']);
    
    // Validate input
    if (empty($field_name)) {
        wp_die(
            '<h1>' . esc_html__('Validation Error', 'my-plugin') . '</h1>' .
            '<p>' . esc_html__('Field name is required.', 'my-plugin') . '</p>'
        );
    }
    
    // Process data
    // ... save to database
    
    // Redirect with success message
    wp_safe_redirect(add_query_arg('success', '1', wp_get_referer()));
    exit;
}

add_action('admin_init', 'process_my_plugin_form');
```

#### AJAX CSRF Protection

```php
<?php
/**
 * Complete CSRF protection for AJAX
 */

// Enqueue script with nonce
function my_plugin_enqueue_ajax_script(): void {
    wp_enqueue_script(
        'my-plugin-ajax',
        MY_PLUGIN_URI . 'assets/js/ajax.js',
        ['jquery'],
        MY_PLUGIN_VERSION,
        true
    );
    
    // Pass nonce to JavaScript
    wp_localize_script('my-plugin-ajax', 'myPluginAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('my_plugin_ajax_action'),
    ]);
}

add_action('wp_enqueue_scripts', 'my_plugin_enqueue_ajax_script');

// JavaScript AJAX request (assets/js/ajax.js)
/*
jQuery(document).ready(function($) {
    $('#my-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'my_plugin_ajax_action',
            nonce: myPluginAjax.nonce,
            field1: $('#field1').val(),
            field2: $('#field2').val()
        };
        
        $.ajax({
            url: myPluginAjax.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.data.message);
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    });
});
*/

// AJAX handler
function my_plugin_ajax_handler(): void {
    // Verify nonce
    if (!isset($_POST['nonce']) || 
        !wp_verify_nonce($_POST['nonce'], 'my_plugin_ajax_action')) {
        wp_send_json_error([
            'message' => __('Security token expired. Please refresh and try again.', 'my-plugin')
        ]);
    }
    
    // Check capabilities (if required)
    if (!current_user_can('edit_posts')) {
        wp_send_json_error([
            'message' => __('Permission denied.', 'my-plugin')
        ]);
    }
    
    // Sanitize input
    $field1 = sanitize_text_field($_POST['field1'] ?? '');
    $field2 = sanitize_text_field($_POST['field2'] ?? '');
    
    // Validate input
    if (empty($field1)) {
        wp_send_json_error([
            'message' => __('Field 1 is required.', 'my-plugin')
        ]);
    }
    
    // Process data
    $result = process_data($field1, $field2);
    
    // Return success
    wp_send_json_success([
        'message' => __('Data processed successfully.', 'my-plugin'),
        'result'  => $result,
    ]);
}

add_action('wp_ajax_my_plugin_ajax_action', 'my_plugin_ajax_handler');
add_action('wp_ajax_nopriv_my_plugin_ajax_action', 'my_plugin_public_ajax_handler');
```

### XSS Prevention

#### Output Escaping for XSS Prevention

```php
<?php
/**
 * XSS prevention through proper escaping
 */

// BAD: Unescaped output (XSS vulnerability)
// echo $user_input; // NEVER DO THIS!

// GOOD: Escape for HTML context
echo '<p>' . esc_html($user_input) . '</p>';

// GOOD: Escape for attribute context
echo '<div class="' . esc_attr($class_name) . '">';

// GOOD: Escape for URL context
echo '<a href="' . esc_url($url) . '">';

// GOOD: Escape for JavaScript context
echo '<script>var name = "' . esc_js($name) . '";</script>';

// GOOD: Allow specific HTML tags
$allowed_html = [
    'a'      => ['href' => [], 'title' => []],
    'strong' => [],
    'em'     => [],
    'p'      => [],
];
echo wp_kses($user_input, $allowed_html);

// GOOD: Use wp_kses_post for rich content
echo wp_kses_post($post_content);

// GOOD: Escape JSON output
echo '<script>var data = ' . wp_json_encode($data, JSON_UNESCAPED_UNICODE) . ';</script>';

// GOOD: Use wp_kses for custom allowed tags
function render_custom_html(string $html): void {
    $allowed = [
        'div'    => ['class' => [], 'id' => []],
        'span'   => ['class' => []],
        'a'      => ['href' => [], 'target' => [], 'rel' => []],
        'img'    => ['src' => [], 'alt' => [], 'width' => [], 'height' => []],
        'p'      => [],
        'strong' => [],
        'em'     => [],
        'ul'     => [],
        'ol'     => [],
        'li'     => [],
    ];
    
    echo wp_kses($html, $allowed);
}

// GOOD: Escape in template files
?>
<div class="my-plugin-wrapper">
    <h2><?php echo esc_html($title); ?></h2>
    <p class="description"><?php echo esc_html($description); ?></p>
    <a href="<?php echo esc_url($link_url); ?>" class="link">
        <?php echo esc_html($link_text); ?>
    </a>
    <img src="<?php echo esc_url($image_url); ?>" 
         alt="<?php echo esc_attr($image_alt); ?>">
</div>
<?php
```

#### XSS Prevention in JavaScript

```php
<?php
/**
 * XSS prevention for JavaScript context
 */

// BAD: Unescaped output in JavaScript (XSS vulnerability)
// echo "<script>var name = '$user_input';</script>"; // NEVER DO THIS!

// GOOD: Use wp_json_encode
echo '<script>var name = ' . wp_json_encode($user_input) . ';</script>';

// GOOD: Use esc_js for inline JavaScript
echo '<script>var name = "' . esc_js($user_input) . '";</script>';

// GOOD: Pass data via wp_localize_script
function my_plugin_enqueue_scripts(): void {
    wp_enqueue_script('my-plugin-js', MY_PLUGIN_URI . 'assets/js/main.js');
    
    wp_localize_script('my-plugin-js', 'myPluginData', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('my_plugin_nonce'),
        'userId'    => get_current_user_id(),
        'userName'  => wp_get_current_user()->display_name,
        'strings'   => [
            'confirm' => __('Are you sure?', 'my-plugin'),
            'error'   => __('An error occurred', 'my-plugin'),
        ],
    ]);
}

add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');

// JavaScript file (assets/js/main.js)
/*
console.log(myPluginData.userName); // Safe, properly encoded by WordPress
console.log(myPluginData.strings.confirm);
*/
```

### Secure File Uploads

```php
<?php
/**
 * Secure file upload handling
 */

class Secure_File_Upload {
    /**
     * Allowed file types
     */
    const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
    ];

    /**
     * Max file size (5MB)
     */
    const MAX_FILE_SIZE = 5242880;

    /**
     * Upload directory
     */
    const UPLOAD_DIR = 'my-plugin-uploads';

    /**
     * Handle file upload
     */
    public static function handle_upload(array $file): array {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return [
                'success' => false,
                'error'   => 'No file uploaded',
            ];
        }

        // Verify nonce (should be checked before calling this)
        // wp_verify_nonce($_POST['upload_nonce'], 'file_upload_action');

        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return [
                'success' => false,
                'error'   => 'File size exceeds maximum allowed',
            ];
        }

        // Check MIME type
        $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
        
        if (!in_array($file_type['type'], self::ALLOWED_MIME_TYPES, true)) {
            return [
                'success' => false,
                'error'   => 'Invalid file type',
            ];
        }

        // Additional MIME type verification
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($real_mime, self::ALLOWED_MIME_TYPES, true)) {
            return [
                'success' => false,
                'error'   => 'File type mismatch detected',
            ];
        }

        // Sanitize filename
        $filename = sanitize_file_name($file['name']);
        
        // Generate unique filename
        $filename = wp_unique_filename(self::get_upload_dir(), $filename);

        // Move file to secure location
        $upload_dir = self::get_upload_dir();
        
        if (!file_exists($upload_dir)) {
            wp_mkdir_p($upload_dir);
        }

        $destination = $upload_dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'error'   => 'Failed to move uploaded file',
            ];
        }

        // Set proper permissions
        chmod($destination, 0644);

        // For images, strip metadata (prevent malicious code in EXIF)
        if (strpos($real_mime, 'image/') === 0) {
            self::strip_image_metadata($destination);
        }

        return [
            'success'  => true,
            'filename' => $filename,
            'path'     => $destination,
            'url'      => self::get_upload_url() . '/' . $filename,
            'mime'     => $real_mime,
        ];
    }

    /**
     * Get upload directory
     */
    private static function get_upload_dir(): string {
        $wp_upload_dir = wp_upload_dir();
        return $wp_upload_dir['basedir'] . '/' . self::UPLOAD_DIR;
    }

    /**
     * Get upload URL
     */
    private static function get_upload_url(): string {
        $wp_upload_dir = wp_upload_dir();
        return $wp_upload_dir['baseurl'] . '/' . self::UPLOAD_DIR;
    }

    /**
     * Strip image metadata
     */
    private static function strip_image_metadata(string $file_path): void {
        $editor = wp_get_image_editor($file_path);
        
        if (!is_wp_error($editor)) {
            $editor->strip_meta();
            $editor->save($file_path);
        }
    }
}
```

### Secure API Endpoints

```php
<?php
/**
 * Secure REST API endpoint implementation
 */

class Secure_REST_API {
    /**
     * Register REST API routes
     */
    public function register_routes(): void {
        register_rest_route('my-plugin/v1', '/data', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this, 'get_data'],
            'permission_callback' => [$this, 'check_permission'],
            'args'     => $this->get_endpoint_args(),
        ]);

        register_rest_route('my-plugin/v1', '/data', [
            'methods'  => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'create_data'],
            'permission_callback' => [$this, 'check_write_permission'],
            'args'     => $this->get_create_args(),
        ]);
    }

    /**
     * Permission check for read operations
     */
    public function check_permission(): bool {
        // Option 1: Check if user is logged in
        if (!is_user_logged_in()) {
            return false;
        }

        // Option 2: Check specific capability
        return current_user_can('read');
    }

    /**
     * Permission check for write operations
     */
    public function check_write_permission(): bool {
        return current_user_can('edit_posts');
    }

    /**
     * Get endpoint arguments
     */
    private function get_endpoint_args(): array {
        return [
            'id' => [
                'required'          => false,
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'validate_callback' => function ($param) {
                    return $param > 0;
                },
            ],
            'status' => [
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function ($param) {
                    return in_array($param, ['active', 'inactive'], true);
                },
            ],
        ];
    }

    /**
     * Get create endpoint arguments
     */
    private function get_create_args(): array {
        return [
            'title' => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'content' => [
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'wp_kses_post',
            ],
            'status' => [
                'required'          => false,
                'type'              => 'string',
                'default'           => 'draft',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function ($param) {
                    return in_array($param, ['draft', 'publish', 'private'], true);
                },
            ],
        ];
    }

    /**
     * Get data endpoint
     */
    public function get_data(\WP_REST_Request $request): \WP_REST_Response {
        // Arguments are already sanitized and validated
        
        $id = $request->get_param('id');
        $status = $request->get_param('status');

        // Fetch data
        $data = $this->fetch_data($id, $status);

        // Return response
        return rest_ensure_response([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Create data endpoint
     */
    public function create_data(\WP_REST_Request $request): \WP_REST_Response {
        // Arguments are already sanitized and validated
        
        $title = $request->get_param('title');
        $content = $request->get_param('content');
        $status = $request->get_param('status');

        // Create data
        $result = $this->create_record($title, $content, $status);

        if (is_wp_error($result)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => $result->get_error_message(),
            ], 400);
        }

        return rest_ensure_response([
            'success' => true,
            'id'      => $result,
            'message' => 'Data created successfully',
        ], 201);
    }

    /**
     * Fetch data from database
     */
    private function fetch_data(?int $id, ?string $status): array {
        // Safe database query with prepared statements
        global $wpdb;

        $table_name = $wpdb->prefix . 'my_custom_table';

        $query = "SELECT * FROM $table_name WHERE 1=1";
        $args = [];

        if ($id) {
            $query .= " AND id = %d";
            $args[] = $id;
        }

        if ($status) {
            $query .= " AND status = %s";
            $args[] = $status;
        }

        if (!empty($args)) {
            $query = $wpdb->prepare($query, $args);
        }

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Create record
     */
    private function create_record(string $title, string $content, string $status): int {
        global $wpdb;

        $table_name = $wpdb->prefix . 'my_custom_table';

        $inserted = $wpdb->insert(
            $table_name,
            [
                'title'     => $title,
                'content'   => $content,
                'status'    => $status,
                'author_id' => get_current_user_id(),
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%d', '%s']
        );

        return $inserted ? $wpdb->insert_id : 0;
    }
}

// Register REST API routes
add_action('rest_api_init', function () {
    $api = new Secure_REST_API();
    $api->register_routes();
});
```

## Security Best Practices

### Input Validation

- Validate all user input before processing
- Use appropriate sanitization functions
- Whitelist allowed values when possible
- Validate on both client and server side

### Output Escaping

- Escape all output based on context
- Use esc_html(), esc_attr(), esc_url()
- Use wp_kses() for allowed HTML
- Never trust stored data for output

### Nonce Verification

- Always verify nonces for forms
- Use nonces for AJAX requests
- Create unique nonce actions
- Handle expired nonces gracefully

### Capability Checks

- Check capabilities before operations
- Use appropriate capability levels
- Create custom capabilities when needed
- Check for specific object capabilities

### Database Security

- Always use prepared statements
- Sanitize data before database operations
- Use wpdb->prepare() for all queries
- Never concatenate user input in queries

### File Uploads

- Validate file types strictly
- Check MIME types server-side
- Limit file size
- Store uploads securely
- Strip metadata from images

### Authentication

- Use WordPress authentication system
- Never store plaintext passwords
- Use secure password hashing
- Implement rate limiting for login

## Reference

- WordPress Security Handbook: https://developer.wordpress.org/apis/security/
- WordPress Data Validation: https://developer.wordpress.org/apis/data/data-validation/
- WordPress Sanitization: https://developer.wordpress.org/apis/data/data-validation/#sanitization
- WordPress Escaping: https://developer.wordpress.org/apis/security/escaping/
- WordPress Nonces: https://developer.wordpress.org/apis/security/nonces/
- WordPress Capabilities: https://developer.wordpress.org/apis/handbook/capabilities/
- OWASP Top 10: https://owasp.org/www-project-top-ten/

**Remember**: Security is not optional. Always sanitize input, escape output, verify nonces, check capabilities, and use prepared statements. Never trust user data!