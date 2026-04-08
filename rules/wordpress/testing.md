paths:
  "**/tests/**/*.php"
  "**/phpunit.xml"
  "**/composer.json"

# WordPress Testing

> This file extends [common/testing.md](../common/testing.md) with WordPress-specific testing practices.

## Testing Setup

### PHPUnit Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="tests/bootstrap.php"
    colors="true"
    verbose="true"
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
    </coverage>
</phpunit>
```

### Composer Dependencies

```json
{
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "10up/wp_mock": "^0.4.2",
        "brain/monkey": "^2.6",
        "yoast/phpunit-polyfills": "^1.0"
    },
    "scripts": {
        "test": "phpunit",
        "test-coverage": "phpunit --coverage-html coverage"
    }
}
```

### Bootstrap File

```php
<?php
// tests/bootstrap.php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// WP_Mock setup
WP_Mock::bootstrap();

// Define constants
define( 'MY_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'MY_PLUGIN_FILE', MY_PLUGIN_DIR . 'my-plugin.php' );
define( 'MY_PLUGIN_VERSION', '1.0.0' );

// Include plugin files
require_once MY_PLUGIN_DIR . 'includes/class-main.php';
```

## Unit Tests with WP_Mock

### Basic Unit Test

```php
<?php
namespace My_Plugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WP_Mock;

class MainTest extends TestCase {
    public function setUp(): void {
        WP_Mock::setUp();
        parent::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_init(): void {
        WP_Mock::expectActionAdded( 'init', 'my_function', 10 );
        
        my_plugin_init();
        
        $this->assertConditionsMet();
    }

    public function test_get_option(): void {
        WP_Mock::userFunction( 'get_option', [
            'args'   => [ 'my_plugin_setting', [] ],
            'return' => [ 'enabled' => true ],
        ] );
        
        $result = my_plugin_get_setting();
        
        $this->assertEquals( [ 'enabled' => true ], $result );
    }
}
```

### Testing Filters

```php
<?php
public function test_filter_applied(): void {
    $content = 'Test content';
    
    WP_Mock::expectFilter( 'the_content', $content );
    
    apply_filters( 'the_content', $content );
    
    $this->assertConditionsMet();
}

public function test_filter_result(): void {
    WP_Mock::onFilter( 'the_content' )
        ->with( 'original' )
        ->reply( 'modified' );
    
    $result = apply_filters( 'the_content', 'original' );
    
    $this->assertEquals( 'modified', $result );
}
```

## Integration Tests with WordPress Test Suite

### Setting Up WordPress Test Environment

```bash
# Install WordPress test library
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

### Basic Integration Test

```php
<?php
namespace My_Plugin\Tests\Integration;

use WP_UnitTestCase;

class PostTypeTest extends WP_UnitTestCase {
    public function test_post_type_registered(): void {
        register_post_type( 'my_custom', [
            'public' => true,
            'label'  => 'My Custom Post',
        ] );
        
        $this->assertTrue( post_type_exists( 'my_custom' ) );
    }

    public function test_create_post(): void {
        $post_id = $this->factory->post->create( [
            'post_type'    => 'my_custom',
            'post_title'   => 'Test Post',
            'post_status'  => 'publish',
        ] );
        
        $this->assertIsInt( $post_id );
        $this->assertGreaterThan( 0, $post_id );
        
        $post = get_post( $post_id );
        $this->assertEquals( 'Test Post', $post->post_title );
    }

    public function test_post_meta(): void {
        $post_id = $this->factory->post->create();
        
        update_post_meta( $post_id, '_my_meta', 'value' );
        
        $this->assertEquals( 'value', get_post_meta( $post_id, '_my_meta', true ) );
    }
}
```

## Testing Best Practices

### Test Naming

```php
<?php
// ✅ GOOD: Descriptive test names
public function test_save_post_updates_meta(): void {}
public function test_invalid_nonce_rejects_request(): void {}
public function test_empty_title_returns_error(): void {}

// ❌ BAD: Vague test names
public function test_save(): void {}
public function test_meta(): void {}
public function test_error(): void {}
```

### Test Organization

```php
<?php
class My_Class_Test extends WP_UnitTestCase {
    // Setup/Teardown
    public function setUp(): void {}
    public function tearDown(): void {}
    
    // Happy path tests
    public function test_valid_input_returns_success(): void {}
    public function test_valid_input_saves_data(): void {}
    
    // Edge cases
    public function test_empty_input_returns_error(): void {}
    public function test_invalid_input_returns_error(): void {}
    
    // Error scenarios
    public function test_database_error_throws_exception(): void {}
}
```

### Data Providers

```php
<?php
/**
 * @dataProvider additionProvider
 */
public function test_addition( $a, $b, $expected ): void {
    $this->assertEquals( $expected, $a + $b );
}

public function additionProvider(): array {
    return [
        'positive numbers' => [ 2, 3, 5 ],
        'negative numbers' => [ -2, -3, -5 ],
        'zero values'      => [ 0, 0, 0 ],
        'mixed values'     => [ -1, 1, 0 ],
    ];
}
```

## Test Coverage Requirements

### Minimum Coverage

- **Target**: 70%+ code coverage
- **Critical paths**: 100% coverage (authentication, payments, data processing)
- **New code**: 80%+ coverage

### Coverage Commands

```bash
# Run tests with coverage
composer test-coverage

# Generate HTML coverage report
phpunit --coverage-html coverage/html
```

## Testing Specific Components

### Testing Hooks

```php
<?php
public function test_action_added(): void {
    WP_Mock::expectActionAdded( 'init', [ My_Class::class, 'method' ], 10 );
    
    My_Class::init();
    
    $this->assertConditionsMet();
}

public function test_filter_added(): void {
    WP_Mock::expectFilterAdded( 'the_content', 'my_filter', 10, 1 );
    
    my_plugin_init();
    
    $this->assertConditionsMet();
}
```

### Testing Shortcodes

```php
<?php
public function test_shortcode_output(): void {
    WP_Mock::userFunction( 'shortcode_atts', [
        'return' => [ 'id' => 123 ],
    ] );
    
    $output = my_shortcode_function( [ 'id' => 123 ] );
    
    $this->assertStringContainsString( 'expected content', $output );
}
```

### Testing AJAX

```php
<?php
public function test_ajax_success(): void {
    WP_Mock::userFunction( 'wp_verify_nonce', [
        'return' => true,
    ] );
    
    WP_Mock::userFunction( 'current_user_can', [
        'return' => true,
    ] );
    
    WP_Mock::userFunction( 'wp_send_json_success', [
        'times' => 1,
    ] );
    
    $_POST['nonce'] = 'valid_nonce';
    my_ajax_handler();
}
```

### Testing REST API

```php
<?php
public function test_rest_endpoint(): void {
    $request = new WP_REST_Request( 'GET', '/my-plugin/v1/data' );
    $request->set_param( 'id', 123 );
    
    $response = my_rest_handler( $request );
    
    $this->assertEquals( 200, $response->get_status() );
    $this->assertArrayHasKey( 'data', $response->get_data() );
}
```

## Continuous Integration

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php-version: [8.0, 8.1, 8.2]
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          coverage: xdebug
      
      - name: Install dependencies
        run: composer install
      
      - name: Run tests
        run: composer test
```

## Testing Checklist

- [ ] All tests pass
- [ ] Code coverage >= 70%
- [ ] Unit tests cover business logic
- [ ] Integration tests cover WordPress integration
- [ ] Edge cases tested
- [ ] Error scenarios tested
- [ ] Tests are independent
- [ ] Tests are fast (< 1s each)
- [ ] Tests are deterministic
- [ ] Tests run in CI/CD

## Reference

- PHPUnit: https://phpunit.de/
- WP_Mock: https://github.com/10up/wp_mock
- BrainMonkey: https://brain-wp.github.io/BrainMonkey/
- WordPress Testing: https://make.wordpress.org/core/handbook/testing/

**Remember**: Write tests first (TDD), aim for 70%+ coverage, test edge cases, and run tests in CI/CD!