name
wordpress-database

description
WordPress database patterns for wpdb operations, prepared statements, Options API, custom tables, meta operations, query optimization, and database security for production-grade WordPress database development.

origin
OpenCode WordPress

# WordPress Database

Production-grade WordPress database patterns for secure, performant WordPress database operations.

## When to Use

- Querying WordPress database
- Working with post meta
- Using Options API
- Creating custom database tables
- Optimizing database queries
- Implementing database migrations
- Managing database transactions
- Debugging database issues

## How It Works

- Use `$wpdb` for database operations
- Always use prepared statements
- Leverage Options API for settings
- Use metadata APIs for post/user/term meta
- Create custom tables when needed
- Optimize queries with proper indexes
- Follow WordPress database conventions
- Implement proper error handling

## Examples

### wpdb Basics

```php
<?php
/**
 * WPDB BASIC OPERATIONS
 */

global $wpdb;

// GET TABLE NAMES
$posts_table = $wpdb->posts;           // wp_posts
$postmeta_table = $wpdb->postmeta;     // wp_postmeta
$options_table = $wpdb->options;       // wp_options
$users_table = $wpdb->users;           // wp_users
$usermeta_table = $wpdb->usermeta;     // wp_usermeta
$comments_table = $wpdb->comments;     // wp_comments
$commentmeta_table = $wpdb->commentmeta; // wp_commentmeta
$terms_table = $wpdb->terms;           // wp_terms
$term_taxonomy = $wpdb->term_taxonomy; // wp_term_taxonomy
$term_relationships = $wpdb->term_relationships; // wp_term_relationships

// Custom table name
$custom_table = $wpdb->prefix . 'my_custom_table';

// DATABASE CHARACTER SET
$charset_collate = $wpdb->get_charset_collate();

// DATABASE ERRORS
$last_error = $wpdb->last_error;       // Last error message
$last_query = $wpdb->last_query;       // Last executed query

// ESCAPING
$escaped_string = $wpdb->_escape($string); // Escape string (deprecated, use prepare)
$escaped_like = $wpdb->esc_like($string);  // Escape for LIKE query
```

### Prepared Statements

```php
<?php
/**
 * PREPARED STATEMENTS (SQL INJECTION PREVENTION)
 */

global $wpdb;

// PLACEHERS:
// %d - integer
// %s - string
// %f - float

// SAFE: SELECT with prepare
$user_id = absint($user_id);
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_author = %d AND post_status = %s",
        $user_id,
        'publish'
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

// SAFE: LIKE query with esc_like
$search_term = sanitize_text_field($_GET['search']);
$search = '%' . $wpdb->esc_like($search_term) . '%';
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_title LIKE %s",
        $search
    )
);

// UNSAFE: Never concatenate variables directly
// ❌ NEVER DO THIS:
// $wpdb->query("SELECT * FROM {$wpdb->posts} WHERE post_author = $user_id"); // SQL INJECTION RISK!

// UNSAFE: Never trust user input
// ❌ NEVER DO THIS:
// $wpdb->query("SELECT * FROM {$wpdb->posts} WHERE post_title = '{$_GET['title']}'"); // SQL INJECTION RISK!
```

### SELECT Queries

```php
<?php
/**
 * SELECT QUERIES
 */

global $wpdb;

// GET_RESULTS - Returns array of objects (multiple rows)
$results = $wpdb->get_results(
    "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_status = 'publish'"
);

foreach ($results as $row) {
    echo $row->ID . ': ' . $row->post_title . '<br>';
}

// GET_ROW - Returns single row object
$post = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
        $post_id
    )
);

if ($post) {
    echo $post->post_title;
}

// GET_ROW with ARRAY_A (associative array)
$post = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
        $post_id
    ),
    ARRAY_A
);

if ($post) {
    echo $post['post_title'];
}

// GET_VAR - Returns single variable
$count = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish'"
);

echo "Total posts: $count";

// GET_COL - Returns column as array
$post_ids = $wpdb->get_col(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'"
);

foreach ($post_ids as $id) {
    echo $id . '<br>';
}

// COMPLEX QUERY WITH JOINS
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.ID, p.post_title, pm.meta_value as price
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s
        WHERE p.post_type = %s AND p.post_status = %s
        ORDER BY p.post_date DESC
        LIMIT %d",
        '_price',
        'product',
        'publish',
        10
    )
);

// GROUP BY AND AGGREGATES
$author_counts = $wpdb->get_results(
    "SELECT post_author, COUNT(*) as post_count
    FROM {$wpdb->posts}
    WHERE post_status = 'publish' AND post_type = 'post'
    GROUP BY post_author
    ORDER BY post_count DESC"
);
```

### INSERT, UPDATE, DELETE

```php
<?php
/**
 * INSERT, UPDATE, DELETE OPERATIONS
 */

global $wpdb;

// INSERT - Method 1: Using $wpdb->insert()
$inserted = $wpdb->insert(
    $wpdb->prefix . 'my_custom_table',
    [
        'user_id'    => absint($user_id),
        'data_key'   => sanitize_text_field($key),
        'data_value' => maybe_serialize($value),
        'created_at' => current_time('mysql'),
    ],
    ['%d', '%s', '%s', '%s'] // Format
);

if ($inserted) {
    $insert_id = $wpdb->insert_id;
    echo "Inserted with ID: $insert_id";
}

// INSERT - Method 2: Using prepare and query
$inserted = $wpdb->query(
    $wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}my_custom_table (user_id, data_key, data_value) VALUES (%d, %s, %s)",
        absint($user_id),
        sanitize_text_field($key),
        maybe_serialize($value)
    )
);

// UPDATE - Method 1: Using $wpdb->update()
$updated = $wpdb->update(
    $wpdb->prefix . 'my_custom_table',
    [
        'data_value' => maybe_serialize($new_value),
        'updated_at' => current_time('mysql'),
    ],
    ['id' => absint($id)], // WHERE clause
    ['%s', '%s'],          // Format for data
    ['%d']                 // Format for WHERE
);

if ($updated !== false) {
    echo "Updated $updated rows";
}

// UPDATE - Method 2: Using prepare and query
$updated = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->prefix}my_custom_table SET data_value = %s WHERE id = %d",
        maybe_serialize($new_value),
        absint($id)
    )
);

// DELETE - Method 1: Using $wpdb->delete()
$deleted = $wpdb->delete(
    $wpdb->prefix . 'my_custom_table',
    ['id' => absint($id)],
    ['%d']
);

if ($deleted) {
    echo "Deleted $deleted rows";
}

// DELETE - Method 2: Using prepare and query
$deleted = $wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}my_custom_table WHERE id = %d",
        absint($id)
    )
);

// DELETE multiple rows
$deleted = $wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}my_custom_table WHERE user_id = %d",
        absint($user_id)
    )
);

// REPLACE (insert or update)
$replaced = $wpdb->replace(
    $wpdb->prefix . 'my_custom_table',
    [
        'id'         => absint($id),
        'data_value' => maybe_serialize($value),
    ],
    ['%d', '%s']
);
```

### Options API

```php
<?php
/**
 * OPTIONS API - Store plugin settings
 */

// ADD OPTION (if not exists)
add_option('my_plugin_settings', [
    'enabled'   => true,
    'max_items' => 10,
    'api_key'   => '',
]);

// GET OPTION
$settings = get_option('my_plugin_settings', []);
$enabled = isset($settings['enabled']) ? $settings['enabled'] : false;

// UPDATE OPTION
$settings['max_items'] = 20;
update_option('my_plugin_settings', $settings);

// DELETE OPTION
delete_option('my_plugin_settings');

// CHECK IF OPTION EXISTS
if (get_option('my_plugin_settings') !== false) {
    // Option exists
}

// AUTLOAD OPTIONS (loaded on every page)
add_option('my_plugin_important', 'value', '', 'yes');    // Autoload yes
add_option('my_plugin_rarely_used', 'value', '', 'no');   // Autoload no

// OPTIONS WITH DEFAULT VALUES
$setting = get_option('my_plugin_setting', 'default_value');

// SERIALIZED VS INDIVIDUAL OPTIONS
// Option 1: Serialized array (recommended for multiple settings)
$settings = [
    'setting_1' => 'value_1',
    'setting_2' => 'value_2',
    'setting_3' => 'value_3',
];
update_option('my_plugin_settings', $settings);

// Option 2: Individual options (for very simple cases)
update_option('my_plugin_setting_1', 'value_1');
update_option('my_plugin_setting_2', 'value_2');

// OPTIONS API VS WPDB DIRECT
// ✅ GOOD: Use Options API for plugin settings
$settings = get_option('my_plugin_settings');

// ❌ BAD: Direct database access for options
// $settings = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'my_plugin_settings'");
```

### Post Meta Operations

```php
<?php
/**
 * POST META OPERATIONS
 */

$post_id = 123;

// ADD POST META
add_post_meta($post_id, '_my_custom_field', 'value', true); // Unique

// GET POST META
$value = get_post_meta($post_id, '_my_custom_field', true); // Single value
$values = get_post_meta($post_id, '_my_custom_field', false); // Multiple values

// UPDATE POST META
update_post_meta($post_id, '_my_custom_field', 'new_value');

// DELETE POST META
delete_post_meta($post_id, '_my_custom_field');
delete_post_meta($post_id, '_my_custom_field', 'specific_value'); // Delete only if matches value

// CHECK IF META EXISTS
if (metadata_exists('post', $post_id, '_my_custom_field')) {
    // Meta exists
}

// GET ALL META FOR POST
$all_meta = get_post_meta($post_id);
/*
[
    '_my_custom_field' => ['value'],
    '_another_field' => ['value1', 'value2'],
]
*/

// META QUERIES WITH WP_QUERY
$args = [
    'post_type'  => 'product',
    'meta_query' => [
        [
            'key'     => '_price',
            'value'   => 100,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        ],
        [
            'key'     => '_stock_status',
            'value'   => 'instock',
        ],
        'relation' => 'AND',
    ],
];

$query = new WP_Query($args);

// ADVANCED META QUERIES
$args = [
    'meta_query' => [
        'relation' => 'OR',
        [
            'key'     => '_price',
            'value'   => [50, 100],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ],
        [
            'relation' => 'AND',
            [
                'key'     => '_sale_price',
                'value'   => 0,
                'compare' => '>',
            ],
            [
                'key'     => '_sale_price',
                'value'   => 50,
                'compare' => '<',
            ],
        ],
    ],
];

// WPDB DIRECT META QUERIES (for performance)
global $wpdb;

$posts_with_meta = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.ID, p.post_title, pm.meta_value as price
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
        AND pm.meta_value > %d
        AND p.post_status = %s",
        '_price',
        100,
        'publish'
    )
);

// BULK META OPERATIONS
// Update multiple meta at once
$meta_data = [
    '_price'        => '99.99',
    '_sku'          => 'PRODUCT-123',
    '_stock'        => 50,
    '_stock_status' => 'instock',
];

foreach ($meta_data as $key => $value) {
    update_post_meta($post_id, $key, $value);
}
```

### User Meta & Term Meta

```php
<?php
/**
 * USER META OPERATIONS
 */

$user_id = 1;

// ADD USER META
add_user_meta($user_id, 'my_plugin_preference', 'dark_mode', true);

// GET USER META
$preference = get_user_meta($user_id, 'my_plugin_preference', true);

// UPDATE USER META
update_user_meta($user_id, 'my_plugin_preference', 'light_mode');

// DELETE USER META
delete_user_meta($user_id, 'my_plugin_preference');

// GET ALL META FOR USER
$all_meta = get_user_meta($user_id);

// CHECK IF USER META EXISTS
if (metadata_exists('user', $user_id, 'my_plugin_preference')) {
    // Meta exists
}

/**
 * TERM META OPERATIONS
 */

$term_id = 5;

// ADD TERM META
add_term_meta($term_id, 'my_plugin_term_color', '#ff0000', true);

// GET TERM META
$color = get_term_meta($term_id, 'my_plugin_term_color', true);

// UPDATE TERM META
update_term_meta($term_id, 'my_plugin_term_color', '#00ff00');

// DELETE TERM META
delete_term_meta($term_id, 'my_plugin_term_color');

/**
 * COMMENT META OPERATIONS
 */

$comment_id = 100;

// ADD COMMENT META
add_comment_meta($comment_id, 'my_plugin_rating', 5, true);

// GET COMMENT META
$rating = get_comment_meta($comment_id, 'my_plugin_rating', true);

// UPDATE COMMENT META
update_comment_meta($comment_id, 'my_plugin_rating', 4);

// DELETE COMMENT META
delete_comment_meta($comment_id, 'my_plugin_rating');
```

### Custom Database Tables

```php
<?php
/**
 * CUSTOM DATABASE TABLES
 */

class Custom_Table {
    const TABLE_NAME = 'my_custom_data';

    /**
     * Create custom table
     */
    public static function create_table(): void {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            data_key varchar(100) NOT NULL,
            data_value longtext NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY data_key (data_key),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Store table version
        add_option('my_plugin_table_version', '1.0.0');
    }

    /**
     * Upgrade table schema
     */
    public static function upgrade_table(): void {
        $current_version = get_option('my_plugin_table_version', '0.0.0');

        if (version_compare($current_version, '1.1.0', '<')) {
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;

            // Add new column
            $wpdb->query(
                "ALTER TABLE $table_name ADD COLUMN priority int(11) DEFAULT 0 AFTER status"
            );

            update_option('my_plugin_table_version', '1.1.0');
        }
    }

    /**
     * Drop table on uninstall
     */
    public static function drop_table(): void {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        delete_option('my_plugin_table_version');
    }

    /**
     * Insert data
     */
    public static function insert_data(array $data): int {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $inserted = $wpdb->insert(
            $table_name,
            [
                'post_id'    => absint($data['post_id']),
                'user_id'    => absint($data['user_id']),
                'data_key'   => sanitize_text_field($data['data_key']),
                'data_value' => maybe_serialize($data['data_value']),
                'status'     => sanitize_text_field($data['status'] ?? 'active'),
            ],
            ['%d', '%d', '%s', '%s', '%s']
        );

        return $inserted ? $wpdb->insert_id : 0;
    }

    /**
     * Get data by ID
     */
    public static function get_data(int $id): ?array {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

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

        return null;
    }

    /**
     * Get data by post_id
     */
    public static function get_by_post(int $post_id): array {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

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
        }

        return $results ?: [];
    }

    /**
     * Update data
     */
    public static function update_data(int $id, array $data): bool {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $update_data = [];
        $format = [];

        if (isset($data['data_value'])) {
            $update_data['data_value'] = maybe_serialize($data['data_value']);
            $format[] = '%s';
        }

        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
            $format[] = '%s';
        }

        if (empty($update_data)) {
            return false;
        }

        $updated = $wpdb->update(
            $table_name,
            $update_data,
            ['id' => $id],
            $format,
            ['%d']
        );

        return $updated !== false;
    }

    /**
     * Delete data
     */
    public static function delete_data(int $id): bool {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $deleted = $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );

        return $deleted !== false;
    }
}

// Create table on plugin activation
register_activation_hook(__FILE__, [Custom_Table::class, 'create_table']);

// Drop table on plugin uninstall
register_uninstall_hook(__FILE__, [Custom_Table::class, 'drop_table']);
```

### Transients (Temporary Data)

```php
<?php
/**
 * TRANSIENTS API - Temporary data storage with expiration
 */

// SET TRANSIENT (expires in 1 hour)
set_transient('my_plugin_cached_data', $data, HOUR_IN_SECONDS);

// GET TRANSIENT
$cached_data = get_transient('my_plugin_cached_data');

if (false === $cached_data) {
    // Transient doesn't exist or expired, regenerate
    $cached_data = expensive_operation();
    set_transient('my_plugin_cached_data', $cached_data, HOUR_IN_SECONDS);
}

// DELETE TRANSIENT
delete_transient('my_plugin_cached_data');

// TRANSIENT WITH MULTIPLE DATA
$cache_key = 'my_plugin_posts_' . md5(json_encode($args));
$posts = get_transient($cache_key);

if (false === $posts) {
    $posts = get_posts($args);
    set_transient($cache_key, $posts, HOUR_IN_SECONDS);
}

// SITE TRANSIENT (network-wide in multisite)
set_site_transient('my_plugin_network_data', $data, HOUR_IN_SECONDS);
$network_data = get_site_transient('my_plugin_network_data');
delete_site_transient('my_plugin_network_data');

// COMMON EXPIRATION CONSTANTS
HOUR_IN_SECONDS      // 3600
DAY_IN_SECONDS       // 86400
WEEK_IN_SECONDS      // 604800
MONTH_IN_SECONDS     // 2592000
YEAR_IN_SECONDS      // 31536000

// CACHE EXPENSIVE OPERATIONS
function get_expensive_data(): array {
    $cache_key = 'my_plugin_expensive_data';
    $cached = get_transient($cache_key);
    
    if (false !== $cached) {
        return $cached;
    }
    
    // Expensive operation
    global $wpdb;
    $data = $wpdb->get_results(
        "SELECT * FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'"
    );
    
    set_transient($cache_key, $data, HOUR_IN_SECONDS);
    
    return $data;
}
```

### Database Optimization

```php
<?php
/**
 * DATABASE OPTIMIZATION
 */

// 1. USE PROPER INDEXES
// When creating custom tables, add indexes on frequently queried columns
CREATE TABLE {$wpdb->prefix}my_table (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    post_id bigint(20) NOT NULL,
    status varchar(20),
    created_at datetime,
    PRIMARY KEY (id),
    KEY user_id (user_id),      // Index for user queries
    KEY post_id (post_id),      // Index for post queries
    KEY status (status),        // Index for status filtering
    KEY created (created_at)    // Index for ordering
);

// 2. SELECT ONLY NEEDED COLUMNS
// ✅ GOOD
$results = $wpdb->get_results(
    "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type = 'post'"
);

// ❌ BAD
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts} WHERE post_type = 'post'"
);

// 3. USE LIMIT
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts} WHERE post_type = 'post' LIMIT 100"
);

// 4. AVOID N+1 QUERIES
// ❌ BAD: N+1 query
$posts = get_posts(['post_type' => 'product']);
foreach ($posts as $post) {
    $price = get_post_meta($post->ID, '_price', true); // N queries
}

// ✅ GOOD: Batch query
$posts = get_posts(['post_type' => 'product']);
$post_ids = wp_list_pluck($posts, 'ID');
$prices = get_post_meta_by_ids($post_ids, '_price');

// 5. USE WP_CACHE
$cache_key = 'my_plugin_posts';
$posts = wp_cache_get($cache_key, 'my_plugin');

if (false === $posts) {
    $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts}");
    wp_cache_set($cache_key, $posts, 'my_plugin', HOUR_IN_SECONDS);
}

// 6. OPTIMIZE META QUERIES
// Use meta query index
$args = [
    'post_type' => 'product',
    'meta_query' => [
        'price_clause' => [
            'key'     => '_price',
            'value'   => 100,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        ],
    ],
    'orderby' => 'price_clause', // Use indexed meta query
    'order'   => 'ASC',
];

// 7. USE TRANSIENTS FOR CACHING
function get_cached_posts(): array {
    $cache_key = 'my_cached_posts';
    $cached = get_transient($cache_key);
    
    if (false !== $cached) {
        return $cached;
    }
    
    $posts = get_posts(['post_type' => 'product', 'numberposts' => -1]);
    set_transient($cache_key, $posts, HOUR_IN_SECONDS);
    
    return $posts;
}

// 8. CLEAR CACHE ON UPDATE
add_action('save_post', function ($post_id) {
    delete_transient('my_cached_posts');
});

// 9. BATCH OPERATIONS
// ✅ GOOD: Batch insert
$values = [];
foreach ($data as $item) {
    $values[] = $wpdb->prepare(
        '(%d, %s, %s)',
        absint($item['id']),
        sanitize_text_field($item['key']),
        maybe_serialize($item['value'])
    );
}

$wpdb->query(
    "INSERT INTO {$wpdb->prefix}my_table (id, key, value) VALUES " . implode(',', $values)
);

// 10. EXPLAIN SLOW QUERIES
$wpdb->query("EXPLAIN SELECT * FROM {$wpdb->posts} WHERE post_type = 'product'");
```

### Database Debugging

```php
<?php
/**
 * DATABASE DEBUGGING
 */

global $wpdb;

// ENABLE ERROR DISPLAY (only in development)
$wpdb->show_errors();

// GET LAST ERROR
$error = $wpdb->last_error;

// GET LAST QUERY
$query = $wpdb->last_query;

// LOG ALL QUERIES (for debugging)
define('SAVEQUERIES', true);

// Get all queries
$queries = $wpdb->queries;
foreach ($queries as $query) {
    echo $query[0] . ' - ' . $query[1] . 's<br>';
}

// PROFILE QUERIES
function profile_query($query) {
    error_log('Query: ' . $query);
    return $query;
}
add_filter('query', 'profile_query');

// CHECK TABLE EXISTS
function table_exists(string $table_name): bool {
    global $wpdb;
    
    $result = $wpdb->get_var(
        $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->prefix . $table_name
        )
    );
    
    return $result === $wpdb->prefix . $table_name;
}

// GET TABLE SIZE
function get_table_size(string $table_name): int {
    global $wpdb;
    
    $size = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT SUM(data_length + index_length) 
            FROM information_schema.TABLES 
            WHERE table_schema = %s AND table_name = %s",
            DB_NAME,
            $wpdb->prefix . $table_name
        )
    );
    
    return (int) $size;
}

// CHECK DATABASE CONNECTION
function check_db_connection(): bool {
    global $wpdb;
    
    $result = $wpdb->query("SELECT 1");
    
    return $result !== false;
}

// REPAIR TABLE
function repair_table(string $table_name): bool {
    global $wpdb;
    
    $result = $wpdb->query(
        $wpdb->prepare(
            "REPAIR TABLE %s",
            $wpdb->prefix . $table_name
        )
    );
    
    return $result !== false;
}

// OPTIMIZE TABLE
function optimize_table(string $table_name): bool {
    global $wpdb;
    
    $result = $wpdb->query(
        $wpdb->prepare(
            "OPTIMIZE TABLE %s",
            $wpdb->prefix . $table_name
        )
    );
    
    return $result !== false;
}
```

## Database Best Practices

### Security

- Always use prepared statements
- Never trust user input
- Sanitize all data before storage
- Use proper data types
- Escape LIKE queries

### Performance

- Create proper indexes
- Select only needed columns
- Use LIMIT for large result sets
- Avoid N+1 queries
- Use caching (transients, wp_cache)

### Maintainability

- Use WordPress APIs when possible
- Document custom table schemas
- Handle errors gracefully
- Implement migrations for schema changes
- Clean up on plugin uninstall

### Debugging

- Enable WP_DEBUG in development
- Log slow queries
- Use Query Monitor plugin
- Check for failed queries
- Profile database operations

## Reference

- WordPress wpdb Class: https://developer.wordpress.org/reference/classes/wpdb/
- WordPress Options API: https://developer.wordpress.org/apis/options/
- WordPress Metadata API: https://developer.wordpress.org/apis/metadata/
- Database Description: https://developer.wordpress.org/reference/classes/wpdb/
- dbDelta Function: https://developer.wordpress.org/reference/functions/dbdelta/
- Query Monitor Plugin: https://wordpress.org/plugins/query-monitor/

**Remember**: Always use prepared statements for security, leverage WordPress APIs for standard operations, create proper indexes for performance, and cache expensive queries!