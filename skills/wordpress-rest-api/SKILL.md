name
wordpress-rest-api

description
WordPress REST API patterns for endpoint registration, authentication, permissions, request handling, response formatting, schema definition, and API versioning for production-grade REST API development.

origin
OpenCode WordPress

# WordPress REST API Development

Production-grade WordPress REST API patterns for scalable, maintainable API development.

## When to Use

- Building REST API endpoints
- Creating custom API routes
- Implementing API authentication
- Handling API permissions
- Formatting API responses
- Defining API schemas
- Versioning REST APIs
- Integrating with external services
- Building headless WordPress applications
- Creating custom API authentication methods

## How It Works

- Register routes with `register_rest_route()`
- Use permission callbacks for authorization
- Implement request validation and sanitization
- Return proper REST responses
- Define schemas for documentation
- Follow REST principles (resources, methods, status codes)
- Handle errors consistently
- Implement authentication (Cookie, OAuth, JWT, Application Passwords)

## Examples

### REST API Directory Structure

```
my-plugin/
├── includes/
│   ├── rest-api/
│   │   ├── class-api.php           # Main API class
│   │   ├── class-products.php      # Products endpoint
│   │   ├── class-orders.php        # Orders endpoint
│   │   ├── class-users.php         # Users endpoint
│   │   ├── class-authentication.php # Custom authentication
│   │   └── schemas/
│   │       ├── product-schema.php
│   │       ├── order-schema.php
│   │       └── user-schema.php
├── tests/
│   └── rest-api/
│       ├── test-api.php
│       ├── test-products.php
│       └── test-authentication.php
```

### Basic REST API Class

```php
<?php
/**
 * Main REST API Class
 *
 * @package My_Plugin
 */

namespace My_Plugin\REST_API;

defined('ABSPATH') || exit;

class API {
    /**
     * API namespace
     */
    const NAMESPACE = 'my-plugin/v1';

    /**
     * Initialize REST API
     */
    public static function init(): void {
        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    /**
     * Register all REST API routes
     */
    public static function register_routes(): void {
        // Products endpoint
        register_rest_route(
            self::NAMESPACE,
            '/products',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [Products::class, 'get_items'],
                'permission_callback' => [Products::class, 'get_items_permissions_check'],
                'args'                => Products::get_collection_params(),
            ]
        );

        register_rest_route(
            self::NAMESPACE,
            '/products/(?P<id>\d+)',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [Products::class, 'get_item'],
                'permission_callback' => [Products::class, 'get_item_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function ($param) {
                            return is_numeric($param);
                        },
                        'sanitize_callback' => 'absint',
                    ],
                ],
            ]
        );

        register_rest_route(
            self::NAMESPACE,
            '/products',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [Products::class, 'create_item'],
                'permission_callback' => [Products::class, 'create_item_permissions_check'],
                'args'                => Products::get_endpoint_args_for_item_schema(\WP_REST_Server::CREATABLE),
            ]
        );

        register_rest_route(
            self::NAMESPACE,
            '/products/(?P<id>\d+)',
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [Products::class, 'update_item'],
                'permission_callback' => [Products::class, 'update_item_permissions_check'],
                'args'                => Products::get_endpoint_args_for_item_schema(\WP_REST_Server::EDITABLE),
            ]
        );

        register_rest_route(
            self::NAMESPACE,
            '/products/(?P<id>\d+)',
            [
                'methods'             => \WP_REST_Server::DELETABLE,
                'callback'            => [Products::class, 'delete_item'],
                'permission_callback' => [Products::class, 'delete_item_permissions_check'],
                'args'                => [
                    'id' => [
                        'required'          => true,
                        'validate_callback' => function ($param) {
                            return is_numeric($param);
                        },
                        'sanitize_callback' => 'absint',
                    ],
                    'force' => [
                        'required'          => false,
                        'default'           => false,
                        'sanitize_callback' => 'rest_sanitize_boolean',
                    ],
                ],
            ]
        );
    }
}

// Initialize
API::init();
```

### Complete Products Endpoint

```php
<?php
/**
 * Products REST API Endpoint
 *
 * @package My_Plugin
 */

namespace My_Plugin\REST_API;

defined('ABSPATH') || exit;

class Products extends \WP_REST_Controller {
    /**
     * Resource name
     */
    protected $resource_name = 'products';

    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'my-plugin/v1';
        $this->rest_base = 'products';
    }

    /**
     * Register routes
     */
    public function register_routes(): void {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ],
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema(true),
                ],
                'schema' => [$this, 'get_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args'                => [
                        'context' => $this->get_context_param(['default' => 'view']),
                    ],
                ],
                [
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema(false),
                ],
                [
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'delete_item'],
                    'permission_callback' => [$this, 'delete_item_permissions_check'],
                    'args'                => [
                        'force'    => [
                            'type'        => 'boolean',
                            'default'     => false,
                            'description' => __('Whether to bypass trash and force deletion.', 'my-plugin'),
                        ],
                    ],
                ],
                'schema' => [$this, 'get_item_schema'],
            ]
        );
    }

    /**
     * Get items (collection)
     */
    public function get_items(\WP_REST_Request $request): \WP_REST_Response {
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => $request->get_param('per_page'),
            'paged'          => $request->get_param('page'),
            'post_status'    => 'publish',
            'orderby'        => $request->get_param('orderby'),
            'order'          => $request->get_param('order'),
        ];

        // Search
        if (!empty($request->get_param('search'))) {
            $args['s'] = sanitize_text_field($request->get_param('search'));
        }

        // Category filter
        if (!empty($request->get_param('category'))) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($request->get_param('category')),
                ],
            ];
        }

        $query = new \WP_Query($args);

        $items = [];

        foreach ($query->posts as $post) {
            $data = $this->prepare_item_for_response($post, $request);
            $items[] = $this->prepare_response_for_collection($data);
        }

        $total_posts = $query->found_posts;
        $max_pages = $query->max_num_pages;

        $response = rest_ensure_response($items);

        // Add pagination headers
        $response->header('X-WP-Total', (int) $total_posts);
        $response->header('X-WP-TotalPages', (int) $max_pages);

        // Add pagination links
        $page = $request->get_param('page');
        $base = add_query_arg(
            array_merge(
                ['page' => '%d'],
                $request->get_query_params()
            ),
            rest_url(sprintf('%s/%s', $this->namespace, $this->rest_base))
        );

        if ($page > 1) {
            $response->link_header('prev', sprintf($base, $page - 1));
        }

        if ($page < $max_pages) {
            $response->link_header('next', sprintf($base, $page + 1));
        }

        return $response;
    }

    /**
     * Get single item
     */
    public function get_item(\WP_REST_Request $request): \WP_REST_Response {
        $id = (int) $request->get_param('id');
        $post = get_post($id);

        if (empty($post) || $post->post_type !== 'product') {
            return new \WP_Error(
                'rest_product_invalid_id',
                __('Invalid product ID.', 'my-plugin'),
                ['status' => 404]
            );
        }

        $data = $this->prepare_item_for_response($post, $request);

        return rest_ensure_response($data);
    }

    /**
     * Create item
     */
    public function create_item(\WP_REST_Request $request): \WP_REST_Response {
        if (!empty($request['id'])) {
            return new \WP_Error(
                'rest_product_exists',
                __('Cannot create existing product.', 'my-plugin'),
                ['status' => 400]
            );
        }

        $prepared_post = $this->prepare_item_for_database($request);

        $post_id = wp_insert_post($prepared_post, true);

        if (is_wp_error($post_id)) {
            if ('db_insert_error' === $post_id->get_error_code()) {
                $post_id->add_data(['status' => 500]);
            } else {
                $post_id->add_data(['status' => 400]);
            }
            return $post_id;
        }

        $post = get_post($post_id);

        // Handle meta fields
        $this->update_additional_fields_for_object($post, $request);

        // Handle taxonomies
        if (!empty($request['categories'])) {
            wp_set_object_terms($post_id, $request['categories'], 'product_category');
        }

        $response = $this->prepare_item_for_response($post, $request);
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $post_id)));

        return $response;
    }

    /**
     * Update item
     */
    public function update_item(\WP_REST_Request $request): \WP_REST_Response {
        $id = (int) $request->get_param('id');
        $post = get_post($id);

        if (empty($post) || $post->post_type !== 'product') {
            return new \WP_Error(
                'rest_product_invalid_id',
                __('Invalid product ID.', 'my-plugin'),
                ['status' => 404]
            );
        }

        $prepared_post = $this->prepare_item_for_database($request);

        $prepared_post['ID'] = $post->ID;

        $post_id = wp_update_post($prepared_post, true);

        if (is_wp_error($post_id)) {
            if ('db_update_error' === $post_id->get_error_code()) {
                $post_id->add_data(['status' => 500]);
            } else {
                $post_id->add_data(['status' => 400]);
            }
            return $post_id;
        }

        $post = get_post($post_id);

        // Handle meta fields
        $this->update_additional_fields_for_object($post, $request);

        $response = $this->prepare_item_for_response($post, $request);

        return rest_ensure_response($response);
    }

    /**
     * Delete item
     */
    public function delete_item(\WP_REST_Request $request): \WP_REST_Response {
        $id = (int) $request->get_param('id');
        $force = (bool) $request->get_param('force');

        $post = get_post($id);

        if (empty($post) || $post->post_type !== 'product') {
            return new \WP_Error(
                'rest_product_invalid_id',
                __('Invalid product ID.', 'my-plugin'),
                ['status' => 404]
            );
        }

        $supports_trash = (EMPTY_TRASH_DAYS > 0);

        // Already in trash
        if ('trash' === $post->post_status) {
            return new \WP_Error(
                'rest_product_already_trashed',
                __('The product has already been deleted.', 'my-plugin'),
                ['status' => 410]
            );
        }

        // Force delete or move to trash
        $request->set_param('context', 'edit');

        if ($force || !$supports_trash) {
            $previous = $this->prepare_item_for_response($post, $request);
            $result = wp_delete_post($id, true);
            $response = new \WP_REST_Response();
            $response->set_data(
                ['deleted' => true, 'previous' => $previous->get_data()]
            );
        } else {
            $result = wp_trash_post($id);
            $post = get_post($id);
            $response = $this->prepare_item_for_response($post, $request);
        }

        if (!$result) {
            return new \WP_Error(
                'rest_cannot_delete',
                __('The product cannot be deleted.', 'my-plugin'),
                ['status' => 500]
            );
        }

        return $response;
    }

    /**
     * Check get items permission
     */
    public function get_items_permissions_check(\WP_REST_Request $request): bool {
        return true; // Public endpoint
    }

    /**
     * Check get item permission
     */
    public function get_item_permissions_check(\WP_REST_Request $request): bool {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Check create item permission
     */
    public function create_item_permissions_check(\WP_REST_Request $request): bool {
        if (!is_user_logged_in()) {
            return false;
        }

        return current_user_can('edit_posts');
    }

    /**
     * Check update item permission
     */
    public function update_item_permissions_check(\WP_REST_Request $request): bool {
        $post = get_post((int) $request->get_param('id'));

        if (!$post) {
            return false;
        }

        return current_user_can('edit_post', $post->ID);
    }

    /**
     * Check delete item permission
     */
    public function delete_item_permissions_check(\WP_REST_Request $request): bool {
        $post = get_post((int) $request->get_param('id'));

        if (!$post) {
            return false;
        }

        return current_user_can('delete_post', $post->ID);
    }

    /**
     * Prepare item for response
     */
    public function prepare_item_for_response($post, \WP_REST_Request $request): \WP_REST_Response {
        $schema = $this->get_item_schema();

        $data = [
            'id'               => (int) $post->ID,
            'date'             => mysql_to_rfc3339($post->post_date),
            'date_gmt'         => mysql_to_rfc3339($post->post_date_gmt),
            'modified'         => mysql_to_rfc3339($post->post_modified),
            'modified_gmt'     => mysql_to_rfc3339($post->post_modified_gmt),
            'slug'             => $post->post_name,
            'status'           => $post->post_status,
            'type'             => $post->post_type,
            'link'             => get_permalink($post->ID),
            'title'            => [
                'rendered' => get_the_title($post->ID),
            ],
            'content'          => [
                'rendered' => apply_filters('the_content', $post->post_content),
                'raw'      => $post->post_content,
            ],
            'excerpt'          => [
                'rendered' => apply_filters('the_excerpt', $post->post_excerpt),
                'raw'      => $post->post_excerpt,
            ],
            'author'           => (int) $post->post_author,
            'featured_media'   => (int) get_post_thumbnail_id($post->ID),
            'price'            => get_post_meta($post->ID, '_price', true),
            'sku'              => get_post_meta($post->ID, '_sku', true),
            'stock_quantity'   => get_post_meta($post->ID, '_stock', true),
            'stock_status'     => get_post_meta($post->ID, '_stock_status', true),
        ];

        // Add categories if in schema
        $taxonomies = wp_list_pluck($schema['properties'], 'type', 'name');
        if (isset($taxonomies['categories'])) {
            $terms = get_the_terms($post->ID, 'product_category');
            $data['categories'] = $terms ? wp_list_pluck($terms, 'term_id') : [];
        }

        $context = !empty($request['context']) ? $request['context'] : 'view';
        $data = $this->filter_response_by_context($data, $context);

        $response = rest_ensure_response($data);
        $response->add_links($this->prepare_links($post));

        return $response;
    }

    /**
     * Prepare item for database
     */
    protected function prepare_item_for_database(\WP_REST_Request $request): array {
        $prepared_post = [
            'post_type' => 'product',
        ];

        // Post title
        if (isset($request['title'])) {
            if (is_string($request['title'])) {
                $prepared_post['post_title'] = sanitize_text_field($request['title']);
            } elseif (!empty($request['title']['raw'])) {
                $prepared_post['post_title'] = sanitize_text_field($request['title']['raw']);
            }
        }

        // Post content
        if (isset($request['content'])) {
            if (is_string($request['content'])) {
                $prepared_post['post_content'] = wp_kses_post($request['content']);
            } elseif (isset($request['content']['raw'])) {
                $prepared_post['post_content'] = wp_kses_post($request['content']['raw']);
            }
        }

        // Post excerpt
        if (isset($request['excerpt'])) {
            if (is_string($request['excerpt'])) {
                $prepared_post['post_excerpt'] = sanitize_textarea_field($request['excerpt']);
            } elseif (isset($request['excerpt']['raw'])) {
                $prepared_post['post_excerpt'] = sanitize_textarea_field($request['excerpt']['raw']);
            }
        }

        // Post status
        if (isset($request['status'])) {
            $status = sanitize_text_field($request['status']);
            if (in_array($status, ['draft', 'publish', 'pending', 'private'], true)) {
                $prepared_post['post_status'] = $status;
            }
        }

        // Post author
        if (isset($request['author'])) {
            $prepared_post['post_author'] = (int) $request['author'];
        }

        return $prepared_post;
    }

    /**
     * Prepare links for response
     */
    protected function prepare_links($post): array {
        $base = sprintf('%s/%s', $this->namespace, $this->rest_base);

        $links = [
            'self' => [
                'href' => rest_url(trailingslashit($base) . $post->ID),
            ],
            'collection' => [
                'href' => rest_url($base),
            ],
        ];

        return $links;
    }

    /**
     * Get item schema
     */
    public function get_item_schema(): array {
        if ($this->schema) {
            return $this->add_additional_fields_schema($this->schema);
        }

        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'product',
            'type'       => 'object',
            'properties' => [
                'id' => [
                    'description' => __('Unique identifier for the object.', 'my-plugin'),
                    'type'        => 'integer',
                    'context'     => ['view', 'edit', 'embed'],
                    'readonly'    => true,
                ],
                'date' => [
                    'description' => __("The date the object was published, in the site's timezone.", 'my-plugin'),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => ['view', 'edit', 'embed'],
                    'readonly'    => true,
                ],
                'date_gmt' => [
                    'description' => __('The date the object was published, as GMT.', 'my-plugin'),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'modified' => [
                    'description' => __("The date the object was last modified, in the site's timezone.", 'my-plugin'),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'modified_gmt' => [
                    'description' => __('The date the object was last modified, as GMT.', 'my-plugin'),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'slug' => [
                    'description' => __('An alphanumeric identifier for the object unique to its type.', 'my-plugin'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit', 'embed'],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_title',
                    ],
                ],
                'status' => [
                    'description' => __('A named status for the object.', 'my-plugin'),
                    'type'        => 'string',
                    'enum'        => ['publish', 'draft', 'pending', 'private'],
                    'context'     => ['view', 'edit'],
                ],
                'type' => [
                    'description' => __('Type of Post for the object.', 'my-plugin'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit', 'embed'],
                    'readonly'    => true,
                ],
                'link' => [
                    'description' => __('URL to the object.', 'my-plugin'),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => ['view', 'edit', 'embed'],
                    'readonly'    => true,
                ],
                'title' => [
                    'description' => __('The title for the object.', 'my-plugin'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit', 'embed'],
                    'properties'  => [
                        'raw' => [
                            'description' => __('Title for the object, as it exists in the database.', 'my-plugin'),
                            'type'        => 'string',
                            'context'     => ['edit'],
                        ],
                        'rendered' => [
                            'description' => __('HTML title for the object, transformed for display.', 'my-plugin'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit', 'embed'],
                            'readonly'    => true,
                        ],
                    ],
                ],
                'content' => [
                    'description' => __('The content for the object.', 'my-plugin'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit'],
                    'properties'  => [
                        'raw' => [
                            'description' => __('Content for the object, as it exists in the database.', 'my-plugin'),
                            'type'        => 'string',
                            'context'     => ['edit'],
                        ],
                        'rendered' => [
                            'description' => __('HTML content for the object, transformed for display.', 'my-plugin'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                    ],
                ],
                'excerpt' => [
                    'description' => __('The excerpt for the object.', 'my-plugin'),
                    'type'        => 'object',
                    'context'     => ['view', 'edit', 'embed'],
                    'properties'  => [
                        'raw' => [
                            'description' => __('Excerpt for the object, as it exists in the database.', 'my-plugin'),
                            'type'        => 'string',
                            'context'     => ['edit'],
                        ],
                        'rendered' => [
                            'description' => __('HTML excerpt for the object, transformed for display.', 'my-plugin'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit', 'embed'],
                            'readonly'    => true,
                        ],
                    ],
                ],
                'author' => [
                    'description' => __('The ID for the author of the object.', 'my-plugin'),
                    'type'        => 'integer',
                    'context'     => ['view', 'edit', 'embed'],
                ],
                'featured_media' => [
                    'description' => __('The ID of the featured media for the object.', 'my-plugin'),
                    'type'        => 'integer',
                    'context'     => ['view', 'edit', 'embed'],
                ],
                'price' => [
                    'description' => __('Product price.', 'my-plugin'),
                    'type'        => 'number',
                    'context'     => ['view', 'edit'],
                ],
                'sku' => [
                    'description' => __('Product SKU.', 'my-plugin'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                ],
                'stock_quantity' => [
                    'description' => __('Product stock quantity.', 'my-plugin'),
                    'type'        => 'integer',
                    'context'     => ['view', 'edit'],
                ],
                'stock_status' => [
                    'description' => __('Product stock status.', 'my-plugin'),
                    'type'        => 'string',
                    'enum'        => ['instock', 'outofstock', 'onbackorder'],
                    'context'     => ['view', 'edit'],
                ],
                'categories' => [
                    'description' => __('The terms assigned to the object in the product_category taxonomy.', 'my-plugin'),
                    'type'        => 'array',
                    'items'       => [
                        'type' => 'integer',
                    ],
                    'context'     => ['view', 'edit'],
                ],
            ],
        ];

        $this->schema = $schema;

        return $this->add_additional_fields_schema($this->schema);
    }

    /**
     * Get collection params
     */
    public function get_collection_params(): array {
        $query_params = parent::get_collection_params();

        $query_params['context']['default'] = 'view';

        $query_params['search'] = [
            'description' => __('Limit results to those matching a string.', 'my-plugin'),
            'type'        => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ];

        $query_params['category'] = [
            'description' => __('Limit result set to products assigned a specific category.', 'my-plugin'),
            'type'        => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ];

        return $query_params;
    }
}

// Register routes
add_action('rest_api_init', function () {
    $controller = new Products();
    $controller->register_routes();
});
```

### Custom Authentication Methods

```php
<?php
/**
 * Custom REST API Authentication
 */

namespace My_Plugin\REST_API;

class Authentication {
    /**
     * Initialize authentication
     */
    public static function init(): void {
        add_filter('determine_current_user', [self::class, 'authenticate'], 20);
        add_filter('rest_authentication_errors', [self::class, 'rest_authentication_errors']);
    }

    /**
     * Authenticate via custom header
     */
    public static function authenticate($user_id): int {
        // Don't authenticate twice
        if (!empty($user_id)) {
            return $user_id;
        }

        // Check for custom auth header
        $auth_header = isset($_SERVER['HTTP_X_MY_PLUGIN_AUTH']) 
            ? sanitize_text_field($_SERVER['HTTP_X_MY_PLUGIN_AUTH']) 
            : '';

        if (empty($auth_header)) {
            return $user_id;
        }

        // Validate token
        $token_data = self::validate_token($auth_header);

        if ($token_data && isset($token_data['user_id'])) {
            return (int) $token_data['user_id'];
        }

        return $user_id;
    }

    /**
     * Validate custom auth token
     */
    private static function validate_token(string $token): ?array {
        // Option 1: Validate API Key
        $api_key = get_option('my_plugin_api_key');
        
        if ($token === $api_key) {
            // Return admin user for API key
            $admins = get_users(['role' => 'administrator', 'number' => 1]);
            
            if (!empty($admins)) {
                return ['user_id' => $admins[0]->ID];
            }
        }

        // Option 2: Validate JWT-like token
        // Token format: base64(json_data).signature
        $parts = explode('.', $token);
        
        if (count($parts) !== 2) {
            return null;
        }

        $data_json = base64_decode($parts[0]);
        $signature = $parts[1];

        // Verify signature
        $expected_signature = hash_hmac(
            'sha256',
            $data_json,
            wp_salt('auth')
        );

        if (!hash_equals($expected_signature, $signature)) {
            return null;
        }

        // Decode data
        $data = json_decode($data_json, true);

        if (!$data || !isset($data['user_id']) || !isset($data['exp'])) {
            return null;
        }

        // Check expiration
        if ($data['exp'] < time()) {
            return null;
        }

        return $data;
    }

    /**
     * Generate auth token
     */
    public static function generate_token(int $user_id, int $expiration = 3600): string {
        $data = [
            'user_id' => $user_id,
            'exp'     => time() + $expiration,
            'iat'     => time(),
        ];

        $data_json = json_encode($data);
        $signature = hash_hmac('sha256', $data_json, wp_salt('auth'));

        return base64_encode($data_json) . '.' . $signature;
    }

    /**
     * REST authentication errors
     */
    public static function rest_authentication_errors($result) {
        // Return existing errors
        if (is_wp_error($result)) {
            return $result;
        }

        // No errors
        return $result;
    }

    /**
     * Require authentication for endpoint
     */
    public static function require_authentication(): bool {
        // Check if user is authenticated
        if (!is_user_logged_in()) {
            return new \WP_Error(
                'rest_unauthorized',
                __('Authentication required.', 'my-plugin'),
                ['status' => 401]
            );
        }

        return true;
    }
}

// Initialize
Authentication::init();
```

### REST API Client Example

```php
<?php
/**
 * REST API Client for consuming WordPress REST APIs
 */

namespace My_Plugin;

class REST_API_Client {
    /**
     * API base URL
     */
    private string $api_url;

    /**
     * API credentials
     */
    private string $username;
    private string $password;
    private string $api_key;

    /**
     * Constructor
     */
    public function __construct(string $api_url, string $username = '', string $password = '', string $api_key = '') {
        $this->api_url = trailingslashit($api_url) . 'wp-json/';
        $this->username = $username;
        $this->password = $password;
        $this->api_key = $api_key;
    }

    /**
     * GET request
     */
    public function get(string $endpoint, array $args = []): array {
        $url = $this->api_url . $endpoint;
        
        if (!empty($args)) {
            $url = add_query_arg($args, $url);
        }

        $response = wp_remote_get($url, [
            'headers' => $this->get_headers(),
            'timeout' => 30,
        ]);

        return $this->handle_response($response);
    }

    /**
     * POST request
     */
    public function post(string $endpoint, array $data = []): array {
        $url = $this->api_url . $endpoint;

        $response = wp_remote_post($url, [
            'headers' => $this->get_headers(),
            'body'    => json_encode($data),
            'timeout' => 30,
        ]);

        return $this->handle_response($response);
    }

    /**
     * PUT request
     */
    public function put(string $endpoint, array $data = []): array {
        $url = $this->api_url . $endpoint;

        $response = wp_remote_request($url, [
            'method'  => 'PUT',
            'headers' => $this->get_headers(),
            'body'    => json_encode($data),
            'timeout' => 30,
        ]);

        return $this->handle_response($response);
    }

    /**
     * DELETE request
     */
    public function delete(string $endpoint, array $args = []): array {
        $url = $this->api_url . $endpoint;
        
        if (!empty($args)) {
            $url = add_query_arg($args, $url);
        }

        $response = wp_remote_request($url, [
            'method'  => 'DELETE',
            'headers' => $this->get_headers(),
            'timeout' => 30,
        ]);

        return $this->handle_response($response);
    }

    /**
     * Get request headers
     */
    private function get_headers(): array {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        // Basic Auth
        if ($this->username && $this->password) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->username . ':' . $this->password);
        }

        // API Key
        if ($this->api_key) {
            $headers['X-API-Key'] = $this->api_key;
        }

        return $headers;
    }

    /**
     * Handle API response
     */
    private function handle_response($response): array {
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        $body = wp_remote_retrieve_body($response);
        $code = wp_remote_retrieve_response_code($response);

        $data = json_decode($body, true);

        if ($code >= 200 && $code < 300) {
            return [
                'success' => true,
                'data'    => $data,
                'code'    => $code,
            ];
        }

        return [
            'success' => false,
            'error'   => $data['message'] ?? 'Unknown error',
            'code'    => $code,
            'data'    => $data,
        ];
    }
}

// Usage example
/*
$client = new REST_API_Client(
    'https://example.com',
    'username',
    'password',
    'api_key'
);

// Get products
$result = $client->get('my-plugin/v1/products', ['per_page' => 10]);

// Create product
$result = $client->post('my-plugin/v1/products', [
    'title'   => 'New Product',
    'content' => 'Product description',
    'status'  => 'publish',
    'price'   => 19.99,
]);

// Update product
$result = $client->put('my-plugin/v1/products/123', [
    'price' => 24.99,
]);

// Delete product
$result = $client->delete('my-plugin/v1/products/123', ['force' => true]);
*/
```

### REST API Best Practices

```php
<?php
/**
 * REST API Best Practices
 */

// 1. Always use permission callbacks
register_rest_route('my-plugin/v1', '/sensitive-data', [
    'methods'  => 'GET',
    'callback' => 'get_sensitive_data',
    'permission_callback' => function () {
        return current_user_can('manage_options');
    },
]);

// 2. Validate and sanitize all input
register_rest_route('my-plugin/v1', '/products', [
    'methods'  => 'POST',
    'callback' => 'create_product',
    'args'     => [
        'title' => [
            'required'          => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => function ($value) {
                return strlen($value) > 0 && strlen($value) <= 200;
            },
        ],
        'price' => [
            'required'          => true,
            'type'              => 'number',
            'sanitize_callback' => 'floatval',
            'validate_callback' => function ($value) {
                return $value > 0;
            },
        ],
    ],
]);

// 3. Return proper status codes
function create_product(\WP_REST_Request $request): \WP_REST_Response {
    $title = $request->get_param('title');
    $price = $request->get_param('price');

    // Create product
    $product_id = create_my_product($title, $price);

    if (is_wp_error($product_id)) {
        return new \WP_REST_Response([
            'success' => false,
            'error'   => $product_id->get_error_message(),
        ], 400);
    }

    return new \WP_REST_Response([
        'success' => true,
        'id'      => $product_id,
    ], 201);
}

// 4. Use proper HTTP methods
// GET    - Retrieve resources (safe, idempotent)
// POST   - Create resources
// PUT    - Replace resources (idempotent)
// PATCH  - Partially update resources
// DELETE - Remove resources (idempotent)

// 5. Implement pagination
function get_products(\WP_REST_Request $request): \WP_REST_Response {
    $page = (int) $request->get_param('page');
    $per_page = (int) $request->get_param('per_page');

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    ];

    $query = new \WP_Query($args);

    $response = new \WP_REST_Response($query->posts);
    $response->header('X-WP-Total', $query->found_posts);
    $response->header('X-WP-TotalPages', $query->max_num_pages);

    return $response;
}

// 6. Use caching when appropriate
function get_cached_products(\WP_REST_Request $request): \WP_REST_Response {
    $cache_key = 'my_plugin_products_' . md5(json_encode($request->get_params()));
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return rest_ensure_response($cached);
    }

    $products = get_my_products();
    set_transient($cache_key, $products, HOUR_IN_SECONDS);

    return rest_ensure_response($products);
}

// 7. Handle errors consistently
function handle_api_error(string $code, string $message, int $status = 400): \WP_REST_Response {
    return new \WP_REST_Response([
        'success' => false,
        'error'   => [
            'code'    => $code,
            'message' => $message,
        ],
    ], $status);
}

// 8. Version your API
register_rest_route('my-plugin/v1', '/products', [...]); // Version 1
register_rest_route('my-plugin/v2', '/products', [...]); // Version 2

// 9. Document with schemas
function get_product_schema(): array {
    return [
        '$schema'    => 'http://json-schema.org/draft-04/schema#',
        'title'      => 'product',
        'type'       => 'object',
        'properties' => [
            'id' => [
                'type'        => 'integer',
                'description' => 'Unique identifier',
            ],
            // ... more properties
        ],
    ];
}

// 10. Rate limiting
function check_rate_limit(): bool {
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'rate_limit_' . md5($ip);
    $requests = get_transient($transient_key);

    if ($requests === false) {
        set_transient($transient_key, 1, MINUTE_IN_SECONDS);
        return true;
    }

    if ($requests >= 60) {
        return new \WP_Error(
            'rate_limit_exceeded',
            'Too many requests',
            ['status' => 429]
        );
    }

    set_transient($transient_key, $requests + 1, MINUTE_IN_SECONDS);
    return true;
}
```

## REST API Best Practices Summary

### Design Principles

- Use nouns for resources (not verbs)
- Use proper HTTP methods (GET, POST, PUT, PATCH, DELETE)
- Return appropriate status codes (200, 201, 400, 401, 403, 404, 500)
- Version your API (e.g., `/v1/products`)
- Use pagination for collections
- Filter and sort collections with query parameters

### Security

- Always use permission callbacks
- Validate and sanitize all input
- Implement authentication (OAuth, JWT, API Keys)
- Use HTTPS in production
- Rate limit requests
- Escape output in responses

### Performance

- Implement caching for read-heavy endpoints
- Use transients for temporary data
- Optimize database queries
- Implement pagination
- Compress responses (gzip)

### Documentation

- Define schemas for all endpoints
- Document all parameters
- Provide example requests/responses
- Use OpenAPI/Swagger specification
- Keep documentation up-to-date

## Reference

- WordPress REST API Handbook: https://developer.wordpress.org/rest-api/
- REST API Reference: https://developer.wordpress.org/rest-api/reference/
- REST API Extending: https://developer.wordpress.org/rest-api/extending-the-rest-api/
- REST API Authentication: https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
- REST API Schema: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
- HTTP Status Codes: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
- RESTful API Design: https://restfulapi.net/

**Remember**: Great REST APIs are secure, well-documented, performant, and follow REST principles. Use proper HTTP methods, return correct status codes, validate input, and version your API.