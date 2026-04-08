paths:
  "**/*.php"

# WordPress Design Patterns

> This file extends [common/patterns.md](../common/patterns.md) with WordPress-specific design patterns.

## Singleton Pattern

Use singleton for plugin/theme main class to ensure single instance.

```php
<?php
/**
 * Singleton pattern for WordPress plugin.
 */
class My_Plugin {
    private static ?My_Plugin $instance = null;

    public static function get_instance(): My_Plugin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Initialize hooks
        add_action( 'init', [ $this, 'init' ] );
    }

    private function __clone() {
        // Prevent cloning
    }

    public function __wakeup() {
        // Prevent unserialization
        throw new Exception( 'Cannot unserialize singleton' );
    }

    public function init(): void {
        // Initialization code
    }
}

// Usage
My_Plugin::get_instance();
```

## Factory Pattern

Use factory pattern for creating post types, taxonomies, or other WordPress objects.

```php
<?php
/**
 * Factory for creating custom post types.
 */
class Post_Type_Factory {
    private static array $post_types = [];

    public static function create( string $slug, array $args ): bool {
        if ( post_type_exists( $slug ) ) {
            return false;
        }

        $defaults = [
            'public'       => true,
            'has_archive'  => true,
            'show_in_rest' => true,
            'supports'     => [ 'title', 'editor', 'thumbnail' ],
        ];

        $args = wp_parse_args( $args, $defaults );
        
        register_post_type( $slug, $args );
        
        self::$post_types[ $slug ] = $args;
        
        return true;
    }

    public static function get_post_types(): array {
        return self::$post_types;
    }
}

// Usage
Post_Type_Factory::create( 'product', [
    'label'  => 'Products',
    'labels' => [
        'name' => 'Products',
    ],
] );
```

## Repository Pattern

Abstract data access layer for cleaner separation of concerns.

```php
<?php
/**
 * Repository interface for post data.
 */
interface Post_Repository_Interface {
    public function find( int $id ): ?WP_Post;
    public function find_all( array $args = [] ): array;
    public function save( array $data ): int;
    public function delete( int $id ): bool;
}

/**
 * WordPress post repository implementation.
 */
class WP_Post_Repository implements Post_Repository_Interface {
    private string $post_type;

    public function __construct( string $post_type = 'post' ) {
        $this->post_type = $post_type;
    }

    public function find( int $id ): ?WP_Post {
        $post = get_post( $id );
        return $post && $post->post_type === $this->post_type ? $post : null;
    }

    public function find_all( array $args = [] ): array {
        $defaults = [
            'post_type'      => $this->post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $args = wp_parse_args( $args, $defaults );
        
        return get_posts( $args );
    }

    public function save( array $data ): int {
        $defaults = [
            'post_type'   => $this->post_type,
            'post_status' => 'publish',
        ];

        $data = wp_parse_args( $data, $defaults );
        
        return wp_insert_post( $data );
    }

    public function delete( int $id ): bool {
        return wp_delete_post( $id, true ) !== false;
    }
}

// Usage
$repository = new WP_Post_Repository( 'product' );
$product = $repository->find( 123 );
$products = $repository->find_all( [ 'posts_per_page' => 10 ] );
```

## Service Container Pattern

Dependency injection container for managing services.

```php
<?php
/**
 * Simple service container.
 */
class Service_Container {
    private static ?Service_Container $instance = null;
    private array $services = [];
    private array $instances = [];

    public static function get_instance(): Service_Container {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register( string $name, callable $factory ): void {
        $this->services[ $name ] = $factory;
    }

    public function get( string $name ) {
        if ( isset( $this->instances[ $name ] ) ) {
            return $this->instances[ $name ];
        }

        if ( ! isset( $this->services[ $name ] ) ) {
            throw new Exception( "Service $name not registered" );
        }

        $this->instances[ $name ] = $this->services[ $name ]( $this );
        
        return $this->instances[ $name ];
    }
}

// Usage
$container = Service_Container::get_instance();

$container->register( 'repository', function( $container ) {
    return new WP_Post_Repository( 'product' );
} );

$container->register( 'service', function( $container ) {
    return new My_Service( $container->get( 'repository' ) );
} );

$service = $container->get( 'service' );
```

## Action/Filter Registration Pattern

Organized hook registration for cleaner code.

```php
<?php
/**
 * Hook registrar for organized hook management.
 */
class Hook_Registrar {
    public static function register(): void {
        self::register_actions();
        self::register_filters();
    }

    private static function register_actions(): void {
        add_action( 'init', [ self::class, 'register_post_types' ] );
        add_action( 'wp_enqueue_scripts', [ self::class, 'enqueue_scripts' ] );
        add_action( 'save_post', [ self::class, 'save_post' ], 10, 2 );
    }

    private static function register_filters(): void {
        add_filter( 'the_content', [ self::class, 'filter_content' ] );
        add_filter( 'the_title', [ self::class, 'filter_title' ] );
    }

    public static function register_post_types(): void {
        // Register CPTs
    }

    public static function enqueue_scripts(): void {
        // Enqueue scripts
    }

    public static function save_post( int $post_id, WP_Post $post ): void {
        // Save post logic
    }

    public static function filter_content( string $content ): string {
        return $content;
    }

    public static function filter_title( string $title ): string {
        return $title;
    }
}

// Usage
Hook_Registrar::register();
```

## Options Abstraction Pattern

Abstract plugin options for cleaner API.

```php
<?php
/**
 * Options abstraction layer.
 */
class Plugin_Options {
    private string $option_name;
    private array $defaults;
    private ?array $options = null;

    public function __construct( string $option_name, array $defaults = [] ) {
        $this->option_name = $option_name;
        $this->defaults = $defaults;
    }

    public function get( string $key = null, $default = null ) {
        if ( null === $this->options ) {
            $this->options = get_option( $this->option_name, $this->defaults );
        }

        if ( null === $key ) {
            return $this->options;
        }

        return $this->options[ $key ] ?? $default;
    }

    public function set( string $key, $value ): bool {
        if ( null === $this->options ) {
            $this->options = get_option( $this->option_name, $this->defaults );
        }

        $this->options[ $key ] = $value;
        
        return update_option( $this->option_name, $this->options );
    }

    public function delete( string $key ): bool {
        if ( null === $this->options ) {
            $this->options = get_option( $this->option_name, $this->defaults );
        }

        if ( ! isset( $this->options[ $key ] ) ) {
            return false;
        }

        unset( $this->options[ $key ] );
        
        return update_option( $this->option_name, $this->options );
    }

    public function reset(): bool {
        $this->options = $this->defaults;
        return update_option( $this->option_name, $this->defaults );
    }
}

// Usage
$options = new Plugin_Options( 'my_plugin_settings', [
    'enabled'   => true,
    'max_items' => 10,
] );

$enabled = $options->get( 'enabled' );
$options->set( 'max_items', 20 );
```

## Template Hierarchy Pattern

Custom template hierarchy for themes/plugins.

```php
<?php
/**
 * Template loader for custom templates.
 */
class Template_Loader {
    private string $template_dir;
    private string $default_template_dir;

    public function __construct( string $template_dir, string $default_template_dir = '' ) {
        $this->template_dir = $template_dir;
        $this->default_template_dir = $default_template_dir ?: $template_dir;
    }

    public function load( string $template_name, array $data = [] ): void {
        $template = $this->locate_template( $template_name );

        if ( ! $template ) {
            return;
        }

        // Extract data for template use
        if ( ! empty( $data ) ) {
            extract( $data, EXTR_SKIP );
        }

        include $template;
    }

    public function get( string $template_name, array $data = [] ): string {
        ob_start();
        $this->load( $template_name, $data );
        return ob_get_clean();
    }

    private function locate_template( string $template_name ): ?string {
        // Check theme first
        $theme_template = locate_template( [ $template_name ] );
        
        if ( $theme_template ) {
            return $theme_template;
        }

        // Check plugin template directory
        $plugin_template = trailingslashit( $this->template_dir ) . $template_name;
        
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }

        // Check default template
        $default_template = trailingslashit( $this->default_template_dir ) . $template_name;
        
        if ( file_exists( $default_template ) ) {
            return $default_template;
        }

        return null;
    }
}

// Usage
$loader = new Template_Loader(
    MY_PLUGIN_DIR . 'templates',
    MY_PLUGIN_DIR . 'templates/default'
);

$loader->load( 'single-product.php', [ 'product' => $product ] );
```

## Shortcode Registration Pattern

Organized shortcode management.

```php
<?php
/**
 * Shortcode registrar.
 */
class Shortcode_Registrar {
    private static array $shortcodes = [];

    public static function register(): void {
        $shortcodes = [
            'my_shortcode'      => 'render_my_shortcode',
            'another_shortcode' => 'render_another_shortcode',
        ];

        foreach ( $shortcodes as $tag => $callback ) {
            add_shortcode( $tag, [ self::class, $callback ] );
            self::$shortcodes[ $tag ] = $callback;
        }
    }

    public static function render_my_shortcode( array $atts, ?string $content = null ): string {
        $atts = shortcode_atts( [
            'id'    => 0,
            'class' => '',
        ], $atts, 'my_shortcode' );

        // Render shortcode
        ob_start();
        ?>
        <div class="my-shortcode <?php echo esc_attr( $atts['class'] ); ?>">
            <?php echo do_shortcode( $content ); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function render_another_shortcode( array $atts ): string {
        // Render shortcode
        return '';
    }
}

// Usage
Shortcode_Registrar::register();
```

## Meta Box Pattern

Organized meta box registration.

```php
<?php
/**
 * Meta box registrar.
 */
class Meta_Box_Registrar {
    public static function register(): void {
        add_action( 'add_meta_boxes', [ self::class, 'add_meta_boxes' ] );
        add_action( 'save_post', [ self::class, 'save_meta_boxes' ], 10, 2 );
    }

    public static function add_meta_boxes(): void {
        add_meta_box(
            'my_plugin_meta_box',
            __( 'My Plugin Settings', 'my-plugin' ),
            [ self::class, 'render_meta_box' ],
            [ 'post', 'page' ],
            'normal',
            'high'
        );
    }

    public static function render_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'my_plugin_meta_box', 'my_plugin_meta_box_nonce' );

        $value = get_post_meta( $post->ID, '_my_plugin_field', true );
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="my_plugin_field">
                        <?php esc_html_e( 'Field Label', 'my-plugin' ); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           id="my_plugin_field"
                           name="my_plugin_field"
                           value="<?php echo esc_attr( $value ); ?>"
                           class="regular-text">
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_meta_boxes( int $post_id, WP_Post $post ): void {
        // Verify nonce
        if ( ! isset( $_POST['my_plugin_meta_box_nonce'] ) ||
             ! wp_verify_nonce( $_POST['my_plugin_meta_box_nonce'], 'my_plugin_meta_box' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save meta
        if ( isset( $_POST['my_plugin_field'] ) ) {
            update_post_meta(
                $post_id,
                '_my_plugin_field',
                sanitize_text_field( $_POST['my_plugin_field'] )
            );
        }
    }
}

// Usage
Meta_Box_Registrar::register();
```

## Reference

- WordPress Plugin Developer Handbook: https://developer.wordpress.org/plugins/
- WordPress Theme Developer Handbook: https://developer.wordpress.org/themes/
- PHP Design Patterns: https://designpatternsphp.readthedocs.io/

**Remember**: Use patterns judiciously, adapt them to WordPress context, keep code testable, and follow WordPress conventions!