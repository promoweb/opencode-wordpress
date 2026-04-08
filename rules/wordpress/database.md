paths:
  "**/*.php"

# WordPress Database

> WordPress-specific database patterns for secure, performant database operations.

## Database Security Rules

### ALWAYS Use Prepared Statements

```php
<?php
global $wpdb;

// ✅ GOOD: Prepared statement
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_author = %d AND post_status = %s",
        $user_id,
        'publish'
    )
);

// ❌ BAD: Direct variable insertion (SQL injection)
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts} WHERE post_author = $user_id"
);
```

### Prepared Statement Placeholders

- `%d` - Integer
- `%s` - String
- `%f` - Float
- `%i` - Identifier (table/column names, WordPress 6.2+)

```php
<?php
// Integer
$wpdb->prepare( "SELECT * FROM table WHERE id = %d", $id );

// String
$wpdb->prepare( "SELECT * FROM table WHERE name = %s", $name );

// Float
$wpdb->prepare( "SELECT * FROM table WHERE price <= %f", $price );

// Multiple placeholders
$wpdb->prepare(
    "SELECT * FROM table WHERE user_id = %d AND status = %s AND price <= %f",
    $user_id,
    $status,
    $price
);
```

## SELECT Queries

### Get Results (Multiple Rows)

```php
<?php
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_status = %s",
        'publish'
    )
);

foreach ( $results as $row ) {
    echo $row->ID . ': ' . $row->post_title;
}
```

### Get Row (Single Row)

```php
<?php
$post = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
        $post_id
    )
);

if ( $post ) {
    echo $post->post_title;
}

// Get as associative array
$post = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
        $post_id
    ),
    ARRAY_A
);

if ( $post ) {
    echo $post['post_title'];
}
```

### Get Var (Single Value)

```php
<?php
$count = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish'"
);

echo "Total posts: $count";
```

### Get Col (Column)

```php
<?php
$post_ids = $wpdb->get_col(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product'"
);

foreach ( $post_ids as $id ) {
    echo $id . '<br>';
}
```

## INSERT, UPDATE, DELETE

### Insert

```php
<?php
// ✅ GOOD: Using $wpdb->insert()
$inserted = $wpdb->insert(
    $wpdb->prefix . 'my_table',
    [
        'user_id'    => $user_id,
        'data_key'   => $key,
        'data_value' => $value,
    ],
    [ '%d', '%s', '%s' ] // Format
);

if ( $inserted ) {
    $insert_id = $wpdb->insert_id;
}

// ✅ GOOD: Using prepare and query
$inserted = $wpdb->query(
    $wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}my_table (user_id, data_key) VALUES (%d, %s)",
        $user_id,
        $key
    )
);
```

### Update

```php
<?php
// ✅ GOOD: Using $wpdb->update()
$updated = $wpdb->update(
    $wpdb->prefix . 'my_table',
    [ 'data_value' => $new_value ], // Data
    [ 'id' => $id ],                 // Where
    [ '%s' ],                        // Data format
    [ '%d' ]                         // Where format
);

// ✅ GOOD: Using prepare and query
$updated = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->prefix}my_table SET data_value = %s WHERE id = %d",
        $new_value,
        $id
    )
);
```

### Delete

```php
<?php
// ✅ GOOD: Using $wpdb->delete()
$deleted = $wpdb->delete(
    $wpdb->prefix . 'my_table',
    [ 'id' => $id ],
    [ '%d' ]
);

// ✅ GOOD: Using prepare and query
$deleted = $wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}my_table WHERE id = %d",
        $id
    )
);
```

## LIKE Queries

### Escape LIKE Values

```php
<?php
// ✅ GOOD: Escape LIKE value
$search = '%' . $wpdb->esc_like( $search_term ) . '%';

$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_title LIKE %s",
        $search
    )
);

// ❌ BAD: Unescaped LIKE (SQL injection)
$search = '%' . $search_term . '%';
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts} WHERE post_title LIKE '%$search%'"
);
```

## Options API

### Use Options API for Settings

```php
<?php
// ✅ GOOD: Use Options API
$settings = get_option( 'my_plugin_settings', [] );
update_option( 'my_plugin_settings', $settings );
delete_option( 'my_plugin_settings' );

// ❌ BAD: Direct database access for options
$settings = $wpdb->get_var(
    "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'my_plugin_settings'"
);
```

## Metadata API

### Use Metadata API for Post/User/Term Meta

```php
<?php
// ✅ GOOD: Use Metadata API
$value = get_post_meta( $post_id, '_my_meta', true );
update_post_meta( $post_id, '_my_meta', $value );
delete_post_meta( $post_id, '_my_meta' );

// ❌ BAD: Direct database access for meta
$value = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
        $post_id,
        '_my_meta'
    )
);
```

## Custom Tables

### Creating Custom Tables

```php
<?php
function create_custom_table(): void {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'my_custom_table';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        data_key varchar(100) NOT NULL,
        data_value longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

register_activation_hook( __FILE__, 'create_custom_table' );
```

### Dropping Custom Tables

```php
<?php
function drop_custom_table(): void {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'my_custom_table';
    
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

register_uninstall_hook( __FILE__, 'drop_custom_table' );
```

## Transients

### Use Transients for Caching

```php
<?php
// Set transient (expires in 1 hour)
set_transient( 'my_plugin_cache', $data, HOUR_IN_SECONDS );

// Get transient
$cached = get_transient( 'my_plugin_cache' );

if ( false === $cached ) {
    // Not cached, fetch and cache
    $cached = expensive_query();
    set_transient( 'my_plugin_cache', $cached, HOUR_IN_SECONDS );
}

// Delete transient
delete_transient( 'my_plugin_cache' );
```

## Database Performance

### Create Proper Indexes

```sql
CREATE TABLE {$wpdb->prefix}my_table (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    post_id bigint(20) NOT NULL,
    status varchar(20),
    created_at datetime,
    PRIMARY KEY (id),
    KEY user_id (user_id),      -- Index for user queries
    KEY post_id (post_id),      -- Index for post queries
    KEY status (status),        -- Index for status filtering
    KEY created (created_at)    -- Index for ordering
);
```

### Select Only Needed Columns

```php
<?php
// ✅ GOOD: Select specific columns
$results = $wpdb->get_results(
    "SELECT ID, post_title, post_status FROM {$wpdb->posts}"
);

// ❌ BAD: Select all columns
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts}"
);
```

### Use LIMIT

```php
<?php
// ✅ GOOD: Limit results
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts} LIMIT 10"
);
```

### Avoid N+1 Queries

```php
<?php
// ❌ BAD: N+1 query
$posts = get_posts( [ 'post_type' => 'product' ] );
foreach ( $posts as $post ) {
    $price = get_post_meta( $post->ID, '_price', true ); // N queries
}

// ✅ GOOD: Batch query
$posts = get_posts( [ 'post_type' => 'product' ] );
$post_ids = wp_list_pluck( $posts, 'ID' );

// Prime the cache
update_meta_cache( 'post', $post_ids );

// Now get_post_meta will use cache
foreach ( $posts as $post ) {
    $price = get_post_meta( $post->ID, '_price', true );
}
```

## Database Debugging

### Enable Error Display (Development Only)

```php
<?php
$wpdb->show_errors();
$wpdb->print_error();

// Get last error
$error = $wpdb->last_error;

// Get last query
$query = $wpdb->last_query;
```

### Log Queries (Development Only)

```php
<?php
define( 'SAVEQUERIES', true );

// Get all queries
$queries = $wpdb->queries;

foreach ( $queries as $query ) {
    error_log( $query[0] . ' - ' . $query[1] . 's' );
}
```

## Database Best Practices

### Security

- ✅ Always use prepared statements
- ✅ Sanitize input before database operations
- ✅ Use WordPress APIs when possible
- ❌ Never trust user input
- ❌ Never concatenate variables in SQL

### Performance

- ✅ Create proper indexes
- ✅ Select only needed columns
- ✅ Use LIMIT for large result sets
- ✅ Cache expensive queries
- ✅ Use transients for temporary data
- ❌ Avoid N+1 queries
- ❌ Avoid SELECT *

### Maintainability

- ✅ Use WordPress APIs (Options, Metadata)
- ✅ Document custom table schemas
- ✅ Use dbDelta for table creation
- ✅ Clean up on plugin uninstall
- ✅ Handle errors gracefully

## Database Checklist

- [ ] All queries use prepared statements
- [ ] Input is sanitized before database operations
- [ ] WordPress APIs used for options and meta
- [ ] Custom tables have proper indexes
- [ ] Queries are optimized for performance
- [ ] Transients used for caching
- [ ] Errors are handled gracefully
- [ ] Custom tables are cleaned up on uninstall

## Reference

- WordPress wpdb Class: https://developer.wordpress.org/reference/classes/wpdb/
- WordPress Options API: https://developer.wordpress.org/apis/options/
- WordPress Metadata API: https://developer.wordpress.org/apis/metadata/
- dbDelta Function: https://developer.wordpress.org/reference/functions/dbdelta/

**Remember**: Always use prepared statements for security, leverage WordPress APIs, create proper indexes for performance, and cache expensive queries!