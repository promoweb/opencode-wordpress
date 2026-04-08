name
wordpress-testing

description
WordPress testing patterns with PHPUnit, WP_Mock, BrainMonkey, integration tests, test-driven development, code coverage, and CI/CD integration for production-grade WordPress testing.

origin
OpenCode WordPress

# WordPress Testing

Production-grade WordPress testing patterns for maintainable, reliable WordPress code.

## When to Use

- Writing unit tests for WordPress plugins/themes
- Testing custom post types and taxonomies
- Testing hooks and filters
- Testing database operations
- Testing REST API endpoints
- Implementing test-driven development (TDD)
- Setting up CI/CD for WordPress
- Measuring code coverage
- Mocking WordPress functions
- Integration testing

## How It Works

- Use PHPUnit as testing framework
- Mock WordPress functions with WP_Mock or BrainMonkey
- Test hooks with BrainMonkey
- Use WordPress test suite for integration tests
- Write unit tests for business logic
- Write integration tests for WordPress integration
- Aim for 70%+ code coverage
- Run tests automatically in CI/CD

## Examples

### Testing Directory Structure

```
my-plugin/
├── tests/
│   ├── bootstrap.php           # PHPUnit bootstrap
│   ├── phpunit.xml             # PHPUnit configuration
│   ├── unit/
│   │   ├── test-main.php
│   │   ├── test-shortcode.php
│   │   ├── test-ajax.php
│   │   └── test-settings.php
│   ├── integration/
│   │   ├── test-post-type.php
│   │   ├── test-meta-box.php
│   │   └── test-rest-api.php
│   └── factories/
│       ├── class-post-factory.php
│       └── class-user-factory.php
├── .github/
│   └── workflows/
│       └── tests.yml           # GitHub Actions CI
└── composer.json               # Dependencies
```

### PHPUnit Configuration (phpunit.xml)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
    bootstrap="tests/bootstrap.php"
    colors="true"
    verbose="true"
    cacheResultFile=".phpunit.cache/test-results"
    failOnRisky="true"
    failOnWarning="true"
    stopOnFailure="false"
>
    <testsuites>
        <testsuite name="unit">
            <directory suffix=".php">tests/unit</directory>
        </testsuite>
        <testsuite name="integration">
            <directory suffix=".php">tests/integration</directory>
        </testsuite>
    </testsuites>

    <coverage>
        <include>
            <directory suffix=".php">includes</directory>
        </include>
        <exclude>
            <directory suffix=".php">tests</directory>
        </exclude>
        <report>
            <clover outputFile="coverage/clover.xml"/>
            <html outputFileDirectory="coverage/html"/>
            <text outputFile="php://stdout" showUncoveredFiles="false"/>
        </report>
    </coverage>

    <php>
        <env name="WP_TESTS_SKIP_INSTALL" value="0"/>
        <env name="WP_TESTS_DIR" value="/tmp/wordpress-tests-lib"/>
        <env name="WP_CORE_DIR" value="/tmp/wordpress/"/>
    </php>
</phpunit>
```

### Bootstrap File (tests/bootstrap.php)

```php
<?php
/**
 * PHPUnit Bootstrap
 *
 * @package My_Plugin
 */

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define constants
define('MY_PLUGIN_DIR', dirname(__DIR__) . '/');
define('MY_PLUGIN_FILE', MY_PLUGIN_DIR . 'my-plugin.php');
define('MY_PLUGIN_VERSION', '1.0.0');

// WP_Mock setup (for unit tests)
if (!class_exists('WP_Mock')) {
    require_once MY_PLUGIN_DIR . 'vendor/autoload.php';
}

WP_Mock::bootstrap();

// Include plugin files (for testing)
require_once MY_PLUGIN_DIR . 'includes/class-main.php';
require_once MY_PLUGIN_DIR . 'includes/functions.php';
```

### composer.json Dependencies

```json
{
    "name": "yourname/my-plugin",
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "10up/wp_mock": "^0.4.2",
        "brain/monkey": "^2.6",
        "mockery/mockery": "^1.5",
        "yoast/phpunit-polyfills": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "My_Plugin\\": "includes/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "My_Plugin\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "test-coverage": "phpunit --coverage-html coverage/html"
    }
}
```

### Unit Test with WP_Mock

```php
<?php
/**
 * Unit tests for Main class
 *
 * @package My_Plugin\Tests\Unit
 */

namespace My_Plugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WP_Mock;
use My_Plugin\Main;

class MainTest extends TestCase {
    /**
     * Setup before each test
     */
    public function setUp(): void {
        WP_Mock::setUp();
        parent::setUp();
    }

    /**
     * Cleanup after each test
     */
    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    /**
     * Test plugin initialization
     */
    public function test_init(): void {
        // Expect actions to be added
        WP_Mock::expectActionAdded('plugins_loaded', [Main::class, 'init'], 10);
        WP_Mock::expectActionAdded('admin_enqueue_scripts', [Main::class, 'enqueue_admin_scripts'], 10);
        WP_Mock::expectFilterAdded('the_content', [Main::class, 'filter_content'], 10);

        // Initialize plugin
        Main::init();

        // Assert actions were added
        $this->assertConditionsMet();
    }

    /**
     * Test filter_content method
     */
    public function test_filter_content(): void {
        $content = '<p>Original content</p>';

        // Mock WordPress function
        WP_Mock::userFunction('is_single', [
            'return' => true,
        ]);

        WP_Mock::userFunction('is_main_query', [
            'return' => true,
        ]);

        // Call method
        $filtered = Main::filter_content($content);

        // Assert content was modified
        $this->assertStringContainsString('Original content', $filtered);
        $this->assertStringContainsString('<div class="my-plugin-wrapper">', $filtered);
    }

    /**
     * Test filter_content does not modify on non-single pages
     */
    public function test_filter_content_does_not_modify_on_non_single(): void {
        $content = '<p>Original content</p>';

        // Mock WordPress function
        WP_Mock::userFunction('is_single', [
            'return' => false,
        ]);

        // Call method
        $filtered = Main::filter_content($content);

        // Assert content was not modified
        $this->assertEquals($content, $filtered);
    }

    /**
     * Test get_option wrapper
     */
    public function test_get_option(): void {
        $option_name = 'my_plugin_settings';
        $option_value = [
            'enabled' => true,
            'max_items' => 10,
        ];

        // Mock get_option
        WP_Mock::userFunction('get_option', [
            'args'   => [$option_name, []],
            'return' => $option_value,
            'times'  => 1,
        ]);

        // Call method
        $result = Main::get_settings();

        // Assert result
        $this->assertEquals($option_value, $result);
        $this->assertTrue($result['enabled']);
        $this->assertEquals(10, $result['max_items']);
    }

    /**
     * Test update_option wrapper
     */
    public function test_update_option(): void {
        $option_name = 'my_plugin_settings';
        $option_value = [
            'enabled' => false,
            'max_items' => 5,
        ];

        // Mock update_option
        WP_Mock::userFunction('update_option', [
            'args'   => [$option_name, $option_value],
            'return' => true,
            'times'  => 1,
        ]);

        // Call method
        $result = Main::update_settings($option_value);

        // Assert result
        $this->assertTrue($result);
    }

    /**
     * Test plugin activation
     */
    public function test_activate(): void {
        // Mock WordPress functions
        WP_Mock::userFunction('get_option', [
            'args'   => ['my_plugin_settings', false],
            'return' => false,
        ]);

        WP_Mock::userFunction('add_option', [
            'args'   => ['my_plugin_settings', WP_Mock\Functions::type('array')],
            'return' => true,
        ]);

        WP_Mock::userFunction('flush_rewrite_rules', [
            'times' => 1,
        ]);

        // Call activation
        Main::activate();

        // Assert conditions met
        $this->assertConditionsMet();
    }
}
```

### Unit Test with BrainMonkey

```php
<?php
/**
 * Unit tests using BrainMonkey
 *
 * @package My_Plugin\Tests\Unit
 */

namespace My_Plugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use My_Plugin\Shortcode;

class ShortcodeTest extends TestCase {
    /**
     * Setup before each test
     */
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Cleanup after each test
     */
    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Test shortcode registration
     */
    public function test_shortcode_is_registered(): void {
        // Expect add_shortcode to be called
        Actions\expectDone('init');
        
        // Register shortcode
        Shortcode::register();

        // Assert shortcode was registered
        $this->assertTrue(has_shortcode('my_shortcode'));
    }

    /**
     * Test shortcode rendering
     */
    public function test_shortcode_render(): void {
        // Mock WordPress functions
        Functions\when('get_posts')->justReturn([
            (object) [
                'ID'         => 1,
                'post_title' => 'Test Post 1',
                'post_content' => 'Content 1',
            ],
            (object) [
                'ID'         => 2,
                'post_title' => 'Test Post 2',
                'post_content' => 'Content 2',
            ],
        ]);

        Functions\when('get_permalink')->alias(function ($id) {
            return "https://example.com/post/$id";
        });

        Functions\when('esc_url')->alias(function ($url) {
            return filter_var($url, FILTER_SANITIZE_URL);
        });

        Functions\when('esc_html')->alias(function ($text) {
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        });

        // Render shortcode
        $output = Shortcode::render(['count' => 2]);

        // Assert output contains expected content
        $this->assertStringContainsString('Test Post 1', $output);
        $this->assertStringContainsString('Test Post 2', $output);
        $this->assertStringContainsString('my-shortcode-wrapper', $output);
    }

    /**
     * Test shortcode with empty results
     */
    public function test_shortcode_render_empty(): void {
        // Mock empty result
        Functions\when('get_posts')->justReturn([]);

        // Render shortcode
        $output = Shortcode::render(['count' => 5]);

        // Assert no results message
        $this->assertStringContainsString('No items found', $output);
    }

    /**
     * Test shortcode attributes
     */
    public function test_shortcode_attributes_are_parsed(): void {
        // Mock shortcode_atts
        Functions\expect('shortcode_atts')
            ->once()
            ->with([
                'count'   => 5,
                'orderby' => 'date',
                'order'   => 'DESC',
            ], ['count' => 10, 'orderby' => 'title'], 'my_shortcode')
            ->andReturnFirstArg();

        Functions\when('get_posts')->justReturn([]);

        // Render with attributes
        Shortcode::render(['count' => 10, 'orderby' => 'title']);
    }

    /**
     * Test hook registration
     */
    public function test_hooks_are_added(): void {
        // Expect actions
        Actions\expectAdded('wp_enqueue_scripts')->once();
        Actions\expectAdded('init')->once();

        // Expect filters
        Filters\expectAdded('the_content')->once();

        // Initialize
        Shortcode::init();

        // All assertions passed
        $this->assertTrue(true);
    }

    /**
     * Test filter application
     */
    public function test_filter_is_applied(): void {
        // Mock content
        $content = 'Test content';

        // Expect filter to be applied
        Filters\expectApplied('my_plugin_shortcode_content')
            ->once()
            ->with($content);

        Functions\when('get_posts')->justReturn([]);

        // Render shortcode
        Shortcode::render([], $content);
    }
}
```

### Integration Test with WordPress Test Suite

```php
<?php
/**
 * Integration tests for Post Type
 *
 * @package My_Plugin\Tests\Integration
 */

namespace My_Plugin\Tests\Integration;

use WP_UnitTestCase;
use My_Plugin\Post_Type;

class PostTypeTest extends WP_UnitTestCase {
    /**
     * Test custom post type registration
     */
    public function test_post_type_is_registered(): void {
        // Register post type
        Post_Type::register();

        // Get registered post types
        $post_types = get_post_types(['_builtin' => false], 'names');

        // Assert post type is registered
        $this->assertArrayHasKey('my_custom', $post_types);
    }

    /**
     * Test post type labels
     */
    public function test_post_type_labels(): void {
        // Register post type
        Post_Type::register();

        // Get post type object
        $post_type_obj = get_post_type_object('my_custom');

        // Assert labels
        $this->assertEquals('Custom Items', $post_type_obj->labels->name);
        $this->assertEquals('Custom Item', $post_type_obj->labels->singular_name);
        $this->assertTrue($post_type_obj->public);
        $this->assertTrue($post_type_obj->has_archive);
    }

    /**
     * Test creating custom post
     */
    public function test_create_custom_post(): void {
        // Register post type
        Post_Type::register();

        // Create post
        $post_id = $this->factory->post->create([
            'post_type'    => 'my_custom',
            'post_title'   => 'Test Custom Post',
            'post_content' => 'Test content',
            'post_status'  => 'publish',
        ]);

        // Assert post was created
        $this->assertIsInt($post_id);
        $this->assertGreaterThan(0, $post_id);

        // Get post
        $post = get_post($post_id);

        // Assert post data
        $this->assertEquals('my_custom', $post->post_type);
        $this->assertEquals('Test Custom Post', $post->post_title);
        $this->assertEquals('publish', $post->post_status);
    }

    /**
     * Test custom post meta
     */
    public function test_custom_post_meta(): void {
        // Register post type
        Post_Type::register();

        // Create post
        $post_id = $this->factory->post->create([
            'post_type'    => 'my_custom',
            'post_title'   => 'Test Post',
            'post_status'  => 'publish',
        ]);

        // Add meta
        update_post_meta($post_id, '_my_custom_field', 'test_value');

        // Get meta
        $meta_value = get_post_meta($post_id, '_my_custom_field', true);

        // Assert meta
        $this->assertEquals('test_value', $meta_value);
    }

    /**
     * Test custom taxonomy
     */
    public function test_custom_taxonomy(): void {
        // Register post type and taxonomy
        Post_Type::register();

        // Create post
        $post_id = $this->factory->post->create([
            'post_type'    => 'my_custom',
            'post_status'  => 'publish',
        ]);

        // Create term
        $term_id = $this->factory->term->create([
            'taxonomy' => 'my_custom_category',
            'name'     => 'Test Category',
        ]);

        // Assign term to post
        wp_set_object_terms($post_id, [$term_id], 'my_custom_category');

        // Get terms
        $terms = wp_get_object_terms($post_id, 'my_custom_category');

        // Assert term was assigned
        $this->assertCount(1, $terms);
        $this->assertEquals('Test Category', $terms[0]->name);
    }

    /**
     * Test post deletion
     */
    public function test_delete_custom_post(): void {
        // Register post type
        Post_Type::register();

        // Create post
        $post_id = $this->factory->post->create([
            'post_type'    => 'my_custom',
            'post_status'  => 'publish',
        ]);

        // Delete post
        wp_delete_post($post_id, true);

        // Try to get deleted post
        $post = get_post($post_id);

        // Assert post was deleted
        $this->assertNull($post);
    }
}
```

### REST API Test

```php
<?php
/**
 * REST API Integration Tests
 *
 * @package My_Plugin\Tests\Integration
 */

namespace My_Plugin\Tests\Integration;

use WP_UnitTestCase;
use WP_REST_Request;
use My_Plugin\REST_API\Products;

class RESTAPITest extends WP_UnitTestCase {
    /**
     * Test REST server
     */
    protected $server;

    /**
     * Setup
     */
    public function setUp(): void {
        parent::setUp();

        // Create REST server
        global $wp_rest_server;
        $this->server = $wp_rest_server = new \WP_REST_Server();
        do_action('rest_api_init');

        // Register routes
        $controller = new Products();
        $controller->register_routes();
    }

    /**
     * Test GET /products endpoint
     */
    public function test_get_products(): void {
        // Create test posts
        $post_ids = $this->factory->post->create_many(5, [
            'post_type'   => 'product',
            'post_status' => 'publish',
        ]);

        // Create request
        $request = new WP_REST_Request('GET', '/my-plugin/v1/products');
        $request->set_param('per_page', 10);

        // Execute request
        $response = $this->server->dispatch($request);
        $data = $response->get_data();

        // Assert response
        $this->assertEquals(200, $response->get_status());
        $this->assertCount(5, $data);
    }

    /**
     * Test GET /products/{id} endpoint
     */
    public function test_get_single_product(): void {
        // Create test post
        $post_id = $this->factory->post->create([
            'post_type'    => 'product',
            'post_title'   => 'Test Product',
            'post_content' => 'Test content',
            'post_status'  => 'publish',
        ]);

        // Add meta
        update_post_meta($post_id, '_price', 19.99);

        // Create request
        $request = new WP_REST_Request('GET', '/my-plugin/v1/products/' . $post_id);

        // Execute request
        $response = $this->server->dispatch($request);
        $data = $response->get_data();

        // Assert response
        $this->assertEquals(200, $response->get_status());
        $this->assertEquals($post_id, $data['id']);
        $this->assertEquals('Test Product', $data['title']['rendered']);
        $this->assertEquals(19.99, $data['price']);
    }

    /**
     * Test POST /products endpoint (create)
     */
    public function test_create_product(): void {
        // Create admin user
        $user_id = $this->factory->user->create([
            'role' => 'administrator',
        ]);

        wp_set_current_user($user_id);

        // Create request
        $request = new WP_REST_Request('POST', '/my-plugin/v1/products');
        $request->set_body_params([
            'title'   => 'New Product',
            'content' => 'Product description',
            'status'  => 'publish',
        ]);

        // Execute request
        $response = $this->server->dispatch($request);
        $data = $response->get_data();

        // Assert response
        $this->assertEquals(201, $response->get_status());
        $this->assertArrayHasKey('id', $data);
        $this->assertGreaterThan(0, $data['id']);

        // Verify post was created
        $post = get_post($data['id']);
        $this->assertEquals('New Product', $post->post_title);
        $this->assertEquals('Product description', $post->post_content);
    }

    /**
     * Test PUT /products/{id} endpoint (update)
     */
    public function test_update_product(): void {
        // Create test post
        $post_id = $this->factory->post->create([
            'post_type'    => 'product',
            'post_title'   => 'Original Title',
            'post_status'  => 'publish',
        ]);

        // Create admin user
        $user_id = $this->factory->user->create([
            'role' => 'administrator',
        ]);

        wp_set_current_user($user_id);

        // Create request
        $request = new WP_REST_Request('PUT', '/my-plugin/v1/products/' . $post_id);
        $request->set_body_params([
            'title' => 'Updated Title',
        ]);

        // Execute request
        $response = $this->server->dispatch($request);
        $data = $response->get_data();

        // Assert response
        $this->assertEquals(200, $response->get_status());
        $this->assertEquals('Updated Title', $data['title']['rendered']);

        // Verify post was updated
        $post = get_post($post_id);
        $this->assertEquals('Updated Title', $post->post_title);
    }

    /**
     * Test DELETE /products/{id} endpoint
     */
    public function test_delete_product(): void {
        // Create test post
        $post_id = $this->factory->post->create([
            'post_type'    => 'product',
            'post_status'  => 'publish',
        ]);

        // Create admin user
        $user_id = $this->factory->user->create([
            'role' => 'administrator',
        ]);

        wp_set_current_user($user_id);

        // Create request
        $request = new WP_REST_Request('DELETE', '/my-plugin/v1/products/' . $post_id);
        $request->set_param('force', true);

        // Execute request
        $response = $this->server->dispatch($request);
        $data = $response->get_data();

        // Assert response
        $this->assertEquals(200, $response->get_status());
        $this->assertTrue($data['deleted']);

        // Verify post was deleted
        $post = get_post($post_id);
        $this->assertNull($post);
    }

    /**
     * Test unauthorized request
     */
    public function test_unauthorized_create(): void {
        // Create request without authentication
        $request = new WP_REST_Request('POST', '/my-plugin/v1/products');
        $request->set_body_params([
            'title' => 'New Product',
        ]);

        // Execute request
        $response = $this->server->dispatch($request);

        // Assert unauthorized
        $this->assertEquals(401, $response->get_status());
    }
}
```

### AJAX Test

```php
<?php
/**
 * AJAX Handler Tests
 *
 * @package My_Plugin\Tests\Unit
 */

namespace My_Plugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WP_Mock;
use Brain\Monkey\Functions;
use My_Plugin\AJAX;

class AjaxTest extends TestCase {
    /**
     * Setup
     */
    protected function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
    }

    /**
     * Teardown
     */
    protected function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    /**
     * Test successful AJAX handler
     */
    public function test_ajax_handler_success(): void {
        // Mock nonce verification
        Functions\expect('wp_verify_nonce')
            ->once()
            ->with('valid_nonce', 'my_plugin_ajax_nonce')
            ->andReturn(true);

        // Mock user capability check
        Functions\expect('current_user_can')
            ->once()
            ->with('edit_posts')
            ->andReturn(true);

        // Mock input
        $_POST = [
            'nonce'   => 'valid_nonce',
            'action'  => 'my_plugin_ajax',
            'field_1' => 'value_1',
        ];

        Functions\when('sanitize_text_field')->returnArg();

        // Mock success response
        Functions\expect('wp_send_json_success')
            ->once()
            ->with(WP_Mock\Functions::type('array'));

        // Call handler
        AJAX::handle_request();
    }

    /**
     * Test AJAX handler with invalid nonce
     */
    public function test_ajax_handler_invalid_nonce(): void {
        // Mock nonce verification failure
        Functions\expect('wp_verify_nonce')
            ->once()
            ->andReturn(false);

        // Mock error response
        Functions\expect('wp_send_json_error')
            ->once()
            ->with(WP_Mock\Functions::type('array'));

        $_POST = [
            'nonce' => 'invalid_nonce',
        ];

        AJAX::handle_request();
    }

    /**
     * Test AJAX handler with unauthorized user
     */
    public function test_ajax_handler_unauthorized(): void {
        // Mock nonce verification
        Functions\expect('wp_verify_nonce')
            ->once()
            ->andReturn(true);

        // Mock capability check failure
        Functions\expect('current_user_can')
            ->once()
            ->andReturn(false);

        // Mock error response
        Functions\expect('wp_send_json_error')
            ->once()
            ->with(WP_Mock\Functions::type('array'));

        $_POST = [
            'nonce' => 'valid_nonce',
        ];

        AJAX::handle_request();
    }
}
```

### Test-Driven Development Example

```php
<?php
/**
 * TDD Example: Calculator Class
 * 
 * Step 1: Write test first (RED)
 * Step 2: Implement minimal code (GREEN)
 * Step 3: Refactor (REFACTOR)
 */

namespace My_Plugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use My_Plugin\Calculator;

class CalculatorTest extends TestCase {
    /**
     * @test
     */
    public function it_can_add_two_numbers(): void {
        $calculator = new Calculator();

        $result = $calculator->add(5, 3);

        $this->assertEquals(8, $result);
    }

    /**
     * @test
     */
    public function it_can_subtract_two_numbers(): void {
        $calculator = new Calculator();

        $result = $calculator->subtract(10, 4);

        $this->assertEquals(6, $result);
    }

    /**
     * @test
     */
    public function it_can_multiply_two_numbers(): void {
        $calculator = new Calculator();

        $result = $calculator->multiply(6, 7);

        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function it_can_divide_two_numbers(): void {
        $calculator = new Calculator();

        $result = $calculator->divide(20, 5);

        $this->assertEquals(4, $result);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_division_by_zero(): void {
        $calculator = new Calculator();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero');

        $calculator->divide(10, 0);
    }

    /**
     * @test
     * @dataProvider additionDataProvider
     */
    public function it_correctly_adds_multiple_values($a, $b, $expected): void {
        $calculator = new Calculator();

        $result = $calculator->add($a, $b);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for addition
     */
    public function additionDataProvider(): array {
        return [
            'positive numbers'   => [5, 3, 8],
            'negative numbers'   => [-5, -3, -8],
            'mixed numbers'      => [-5, 3, -2],
            'zero values'        => [0, 0, 0],
            'float numbers'      => [5.5, 3.5, 9.0],
        ];
    }
}

// Implementation (after writing tests)
// includes/class-calculator.php
<?php
namespace My_Plugin;

class Calculator {
    public function add(float $a, float $b): float {
        return $a + $b;
    }

    public function subtract(float $a, float $b): float {
        return $a - $b;
    }

    public function multiply(float $a, float $b): float {
        return $a * $b;
    }

    public function divide(float $a, float $b): float {
        if ($b === 0.0) {
            throw new \InvalidArgumentException('Division by zero');
        }
        return $a / $b;
    }
}
```

### GitHub Actions CI Configuration

```yaml
# .github/workflows/tests.yml
name: Tests

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php-version: [8.0, 8.1, 8.2]
        wordpress-version: [6.0, 6.2, 6.4]

    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: mbstring, xml, mysql, mysqli
          coverage: xdebug

      - name: Install Composer dependencies
        run: composer install --no-progress --no-suggest

      - name: Setup WordPress test environment
        run: |
          bash bin/install-wp-tests.sh wordpress_test root root localhost latest

      - name: Run unit tests
        run: composer test

      - name: Run integration tests
        run: composer test -- --testsuite integration

      - name: Upload coverage reports
        uses: codecov/codecov-action@v3
        with:
          file: ./coverage/clover.xml
          flags: unittests
          name: codecov-umbrella
```

### Code Coverage Badge

```php
<?php
/**
 * Generate code coverage badge
 */

// Run: composer test-coverage

// Coverage threshold
define('MINIMUM_COVERAGE', 70);

// Read coverage report
$report_file = 'coverage/clover.xml';

if (!file_exists($report_file)) {
    echo "Coverage report not found. Run tests first.\n";
    exit(1);
}

$xml = simplexml_load_file($report_file);

// Calculate coverage
$metrics = $xml->xpath('//metrics');
$total_elements = 0;
$checked_elements = 0;

foreach ($metrics as $metric) {
    $total_elements += (int) $metric['elements'];
    $checked_elements += (int) $metric['coveredelements'];
}

$coverage = ($total_elements > 0) 
    ? round(($checked_elements / $total_elements) * 100, 2) 
    : 0;

echo "Code coverage: $coverage%\n";

// Check if coverage meets minimum
if ($coverage >= MINIMUM_COVERAGE) {
    echo "✅ Coverage meets minimum threshold ($MINIMUM_COVERAGE%)\n";
    exit(0);
} else {
    echo "❌ Coverage below minimum threshold ($MINIMUM_COVERAGE%)\n";
    exit(1);
}
```

## Testing Best Practices

### Test Organization

- Separate unit tests from integration tests
- Use descriptive test names (test_method_scenario_expected)
- One test class per class
- One test method per scenario
- Use data providers for multiple test cases

### Test Quality

- Test edge cases and error scenarios
- Use mocks for external dependencies
- Keep tests independent
- Avoid testing implementation details
- Test behavior, not implementation

### Coverage

- Aim for 70%+ code coverage
- Focus on critical paths
- Don't chase 100% coverage
- Coverage != quality
- Review uncovered code

### TDD Cycle

1. **Red**: Write failing test
2. **Green**: Write minimal code to pass
3. **Refactor**: Improve code quality
4. Repeat

### CI/CD

- Run tests on every commit
- Run tests before merging
- Fail fast on test failures
- Generate coverage reports
- Monitor test execution time

## Reference

- PHPUnit Documentation: https://phpunit.de/documentation.html
- WP_Mock: https://github.com/10up/wp_mock
- BrainMonkey: https://brain-wp.github.io/BrainMonkey/
- WordPress Testing Handbook: https://make.wordpress.org/core/handbook/testing/
- WordPress Test Suite: https://develop.svn.wordpress.org/tags/6.4/tests/phpunit/includes/
- PHPUnit Best Practices: https://phpunit.de/manual/6.5/en/writing-tests-for-phpunit.html

**Remember**: Great tests are reliable, fast, and independent. Write tests first (TDD), aim for 70%+ coverage, test edge cases, and run tests automatically in CI/CD.