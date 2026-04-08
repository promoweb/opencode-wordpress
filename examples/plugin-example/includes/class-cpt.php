<?php
/**
 * Custom Post Type registration
 *
 * @package Opencode_Plugin_Example
 */

namespace OpenCode_Plugin_Example;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Post_Type {

    /**
     * Transient cache key prefix.
     *
     * @since 1.0.0
     * @var string
     */
    private const CACHE_PREFIX = 'opencode_item_';

    /**
     * Cache expiration time in seconds (1 hour).
     *
     * @since 1.0.0
     * @var int
     */
    private const CACHE_EXPIRATION = 3600;

    /**
     * Initialize custom post type functionality.
     *
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
        add_filter( 'manage_opencode_item_posts_columns', array( $this, 'set_custom_columns' ) );
        add_action( 'manage_opencode_item_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
        add_filter( 'manage_edit-opencode_item_sortable_columns', array( $this, 'set_sortable_columns' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_opencode_item', array( $this, 'save_meta_box' ) );
        add_action( 'delete_post', array( $this, 'clear_item_cache' ) );
        add_action( 'wp_trash_post', array( $this, 'clear_item_cache' ) );
        add_action( 'untrash_post', array( $this, 'clear_item_cache' ) );
    }

    /**
     * Register the custom post type.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_post_type(): void {
        $labels = array(
            'name'                  => _x( 'OpenCode Items', 'Post Type General Name', 'opencode-plugin-example' ),
            'singular_name'         => _x( 'OpenCode Item', 'Post Type Singular Name', 'opencode-plugin-example' ),
            'menu_name'             => __( 'OpenCode Items', 'opencode-plugin-example' ),
            'name_admin_bar'        => __( 'OpenCode Item', 'opencode-plugin-example' ),
            'archives'              => __( 'Item Archives', 'opencode-plugin-example' ),
            'attributes'            => __( 'Item Attributes', 'opencode-plugin-example' ),
            'parent_item_colon'     => __( 'Parent Item:', 'opencode-plugin-example' ),
            'all_items'             => __( 'All Items', 'opencode-plugin-example' ),
            'add_new_item'          => __( 'Add New Item', 'opencode-plugin-example' ),
            'add_new'               => __( 'Add New', 'opencode-plugin-example' ),
            'new_item'              => __( 'New Item', 'opencode-plugin-example' ),
            'edit_item'             => __( 'Edit Item', 'opencode-plugin-example' ),
            'update_item'           => __( 'Update Item', 'opencode-plugin-example' ),
            'view_item'             => __( 'View Item', 'opencode-plugin-example' ),
            'view_items'            => __( 'View Items', 'opencode-plugin-example' ),
            'search_items'          => __( 'Search Item', 'opencode-plugin-example' ),
            'not_found'             => __( 'Not found', 'opencode-plugin-example' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'opencode-plugin-example' ),
            'featured_image'        => __( 'Featured Image', 'opencode-plugin-example' ),
            'set_featured_image'    => __( 'Set featured image', 'opencode-plugin-example' ),
            'remove_featured_image' => __( 'Remove featured image', 'opencode-plugin-example' ),
            'use_featured_image'    => __( 'Use as featured image', 'opencode-plugin-example' ),
            'insert_into_item'      => __( 'Insert into item', 'opencode-plugin-example' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'opencode-plugin-example' ),
            'items_list'            => __( 'Items list', 'opencode-plugin-example' ),
            'items_list_navigation' => __( 'Items list navigation', 'opencode-plugin-example' ),
            'filter_items_list'     => __( 'Filter items list', 'opencode-plugin-example' ),
        );

        $args = array(
            'label'               => __( 'OpenCode Item', 'opencode-plugin-example' ),
            'description'         => __( 'OpenCode Item Description', 'opencode-plugin-example' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'taxonomies'          => array( 'opencode_category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-admin-generic',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        );

        register_post_type( 'opencode_item', $args );
    }

    /**
     * Register the custom taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_taxonomy(): void {
        $labels = array(
            'name'                       => _x( 'Categories', 'Taxonomy General Name', 'opencode-plugin-example' ),
            'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'opencode-plugin-example' ),
            'menu_name'                  => __( 'Categories', 'opencode-plugin-example' ),
            'all_items'                  => __( 'All Categories', 'opencode-plugin-example' ),
            'parent_item'                => __( 'Parent Category', 'opencode-plugin-example' ),
            'parent_item_colon'          => __( 'Parent Category:', 'opencode-plugin-example' ),
            'new_item_name'              => __( 'New Category Name', 'opencode-plugin-example' ),
            'add_new_item'               => __( 'Add New Category', 'opencode-plugin-example' ),
            'edit_item'                  => __( 'Edit Category', 'opencode-plugin-example' ),
            'update_item'                => __( 'Update Category', 'opencode-plugin-example' ),
            'view_item'                  => __( 'View Category', 'opencode-plugin-example' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'opencode-plugin-example' ),
            'add_or_remove_items'        => __( 'Add or remove categories', 'opencode-plugin-example' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'opencode-plugin-example' ),
            'popular_items'              => __( 'Popular Categories', 'opencode-plugin-example' ),
            'search_items'               => __( 'Search Categories', 'opencode-plugin-example' ),
            'not_found'                  => __( 'Not Found', 'opencode-plugin-example' ),
            'no_terms'                   => __( 'No categories', 'opencode-plugin-example' ),
            'items_list'                 => __( 'Categories list', 'opencode-plugin-example' ),
            'items_list_navigation'      => __( 'Categories list navigation', 'opencode-plugin-example' ),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'show_in_rest'      => true,
        );

        register_taxonomy( 'opencode_category', array( 'opencode_item' ), $args );
    }

    /**
     * Set custom admin columns.
     *
     * @since 1.0.0
     * @param array<string> $columns Existing columns.
     * @return array<string> Modified columns.
     */
    public function set_custom_columns( array $columns ): array {
        $new_columns = array(
            'cb'        => $columns['cb'],
            'title'     => __( 'Title', 'opencode-plugin-example' ),
            'taxonomy-opencode_category' => __( 'Category', 'opencode-plugin-example' ),
            'custom_value' => __( 'Custom Value', 'opencode-plugin-example' ),
            'date'      => $columns['date'],
        );

        return $new_columns;
    }

    /**
     * Render custom column content.
     *
     * @since 1.0.0
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     * @return void
     */
    public function render_custom_columns( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'custom_value':
                $value = get_post_meta( $post_id, '_opencode_item_value', true );
                echo esc_html( $value ? $value : '—' );
                break;
        }
    }

    /**
     * Set sortable columns.
     *
     * @since 1.0.0
     * @param array<string> $columns Existing sortable columns.
     * @return array<string> Modified sortable columns.
     */
    public function set_sortable_columns( array $columns ): array {
        $columns['custom_value'] = 'custom_value';
        return $columns;
    }

    /**
     * Add meta boxes.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_meta_boxes(): void {
        add_meta_box(
            'opencode_item_details',
            __( 'Item Details', 'opencode-plugin-example' ),
            array( $this, 'render_meta_box' ),
            'opencode_item',
            'normal',
            'high'
        );
    }

    /**
     * Render meta box.
     *
     * @since 1.0.0
     * @param \WP_Post $post Post object.
     * @return void
     */
    public function render_meta_box( \WP_Post $post ): void {
        wp_nonce_field( 'opencode_item_meta_box', 'opencode_item_meta_box_nonce' );

        $value = get_post_meta( $post->ID, '_opencode_item_value', true );

        printf(
            '<p><label for="opencode_item_value"><strong>%s</strong></label><br /><input type="text" id="opencode_item_value" name="opencode_item_value" value="%s" class="widefat" /></p>',
            esc_html__( 'Custom Value:', 'opencode-plugin-example' ),
            esc_attr( $value )
        );
    }

    /**
     * Save meta box data.
     *
     * @since 1.0.0
     * @param int $post_id Post ID.
     * @return void
     */
    public function save_meta_box( int $post_id ): void {
        // Check nonce with proper unslashing for PHP 8.1+ compatibility
        $nonce = isset( $_POST['opencode_item_meta_box_nonce'] ) 
            ? wp_unslash( $_POST['opencode_item_meta_box_nonce'] ) 
            : '';

        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'opencode_item_meta_box' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check post type to avoid running on other post types
        if ( get_post_type( $post_id ) !== 'opencode_item' ) {
            return;
        }

        if ( isset( $_POST['opencode_item_value'] ) ) {
            update_post_meta( $post_id, '_opencode_item_value', sanitize_text_field( wp_unslash( $_POST['opencode_item_value'] ) ) );
        }

        // Clear cache for this item
        $this->clear_item_cache( $post_id );
    }

    /**
     * Get cached item data.
     *
     * @since 1.0.0
     * @param int $post_id Post ID.
     * @return array<string, mixed>|false Cached data or false if not found.
     */
    public function get_cached_item( int $post_id ): array|false {
        $cache_key = self::CACHE_PREFIX . $post_id;
        return get_transient( $cache_key );
    }

    /**
     * Set cached item data.
     *
     * @since 1.0.0
     * @param int                   $post_id Post ID.
     * @param array<string, mixed>  $data    Data to cache.
     * @return bool True if set successfully.
     */
    public function set_cached_item( int $post_id, array $data ): bool {
        $cache_key = self::CACHE_PREFIX . $post_id;
        return set_transient( $cache_key, $data, self::CACHE_EXPIRATION );
    }

    /**
     * Clear cached item data.
     *
     * @since 1.0.0
     * @param int $post_id Post ID.
     * @return void
     */
    public function clear_item_cache( int $post_id ): void {
        // Only clear cache for opencode_item post type
        if ( get_post_type( $post_id ) !== 'opencode_item' ) {
            return;
        }

        $cache_key = self::CACHE_PREFIX . $post_id;
        delete_transient( $cache_key );

        // Also clear any list caches
        delete_transient( self::CACHE_PREFIX . 'list' );
        delete_transient( self::CACHE_PREFIX . 'count' );
    }

    /**
     * Get cached items count.
     *
     * @since 1.0.0
     * @return int Number of published items.
     */
    public function get_items_count(): int {
        $cache_key = self::CACHE_PREFIX . 'count';
        $count = get_transient( $cache_key );

        if ( false === $count ) {
            $count_obj = wp_count_posts( 'opencode_item' );
            $count = $count_obj ? (int) $count_obj->publish : 0;
            set_transient( $cache_key, $count, self::CACHE_EXPIRATION );
        }

        return $count;
    }

    /**
     * Get cached items list.
     *
     * @since 1.0.0
     * @param array<string, mixed> $args Query arguments.
     * @return array<int> Array of post IDs.
     */
    public function get_cached_items_list( array $args = array() ): array {
        $defaults = array(
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );
        $cache_key = self::CACHE_PREFIX . 'list_' . md5( wp_json_encode( $args ) );
        $ids = get_transient( $cache_key );

        if ( false === $ids ) {
            $query = new \WP_Query(
                array_merge(
                    $args,
                    array(
                        'post_type'      => 'opencode_item',
                        'post_status'    => 'publish',
                        'fields'         => 'ids',
                        'no_found_rows'  => true,
                    )
                )
            );

            $ids = $query->posts;
            set_transient( $cache_key, $ids, self::CACHE_EXPIRATION );
        }

        return $ids;
    }
}