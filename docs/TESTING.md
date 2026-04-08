# Testing Guide

Complete guide to testing OpenCode WordPress components and examples.

## Overview

Testing ensures OpenCode WordPress components work correctly and follow standards. This guide covers:

- Skill testing
- Agent testing
- Hook testing
- Command testing
- Example testing
- Integration testing
- WordPress testing

## Prerequisites

### Required Tools

- **PHPUnit**: For PHP unit testing
- **Jest**: For JavaScript testing (hooks)
- **WP-CLI**: For WordPress testing
- **Node.js**: 14+ for hook testing
- **PHP**: 7.4+ for example testing

### Install Testing Tools

```bash
# Install PHPUnit globally
composer global require phpunit/phpunit

# Install Jest for hook tests
npm install --global jest

# Install WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp

# Verify installations
phpunit --version
jest --version
wp --version
```

## Skill Testing

### Manual Testing

Test skill invocation manually:

```bash
# Navigate to test WordPress project
cd test-wordpress-project

# Start OpenCode
opencode

# Invoke skill
opencode skill wordpress-theme-development

# Verify:
# - Skill loads correctly
# - Guidance is accurate
# - Examples are relevant
# - References are valid
# - No errors in output
```

### Skill Test Checklist

For each skill, verify:

- [ ] SKILL.md exists and is valid markdown
- [ ] Skill invokes correctly
- [ ] Guidance is WordPress-specific
- [ ] Examples are accurate
- [ ] Code examples follow WordPress standards
- [ ] References are valid URLs
- [ ] Version compatibility is documented
- [ ] No broken links
- [ ] No outdated information

### Skill Test Script

Create automated skill test:

```bash
#!/bin/bash
# test-skills.sh

SKILLS_DIR="skills"
ERRORS=0

for skill_dir in "$SKILLS_DIR"/*; do
    skill_name=$(basename "$skill_dir")
    skill_file="$skill_dir/SKILL.md"
    
    echo "Testing skill: $skill_name"
    
    # Check SKILL.md exists
    if [ ! -f "$skill_file" ]; then
        echo "ERROR: SKILL.md not found for $skill_name"
        ERRORS=$((ERRORS + 1))
        continue
    fi
    
    # Validate markdown
    if ! markdown-validator "$skill_file"; then
        echo "ERROR: Invalid markdown in $skill_name"
        ERRORS=$((ERRORS + 1))
    fi
    
    # Check required sections
    sections=("Overview" "Capabilities" "Instructions" "Examples" "References")
    for section in "${sections[@]}"; do
        if ! grep -q "## $section" "$skill_file"; then
            echo "ERROR: Missing section '$section' in $skill_name"
            ERRORS=$((ERRORS + 1))
        fi
    done
    
    # Test skill invocation (mock)
    if ! opencode-test skill "$skill_name"; then
        echo "ERROR: Skill invocation failed for $skill_name"
        ERRORS=$((ERRORS + 1))
    fi
done

echo "Skill tests completed with $ERRORS errors"
exit $ERRORS
```

## Agent Testing

### Agent Activation Test

Test agent activates correctly:

```bash
# Create test WordPress file
echo "<?php wp_enqueue_script('test', 'url'); ?>" > test-file.php

# Invoke agent
opencode agent wordpress-reviewer --file=test-file.php

# Verify agent:
# - Activates for WordPress files
# - Detects issues
# - Reports in correct format
# - Provides actionable suggestions
```

### Agent Test Cases

Create test cases for agents:

```php
// test-cases/theme-reviewer/bad-enqueue.php
<?php
// This should trigger warning
wp_enqueue_style('style', get_stylesheet_uri());
// Missing: version parameter, proper handle name
```

```php
// test-cases/plugin-reviewer/no-nonce.php
<?php
function save_data() {
    // This should trigger error
    update_option('my_option', $_POST['data']);
    // Missing: nonce verification, capability check, sanitization
}
```

```php
// test-cases/security-check/sql-injection.php
<?php
global $wpdb;
// This should trigger error
$results = $wpdb->get_results("SELECT * FROM table WHERE id = " . $_GET['id']);
// Missing: $wpdb->prepare()
```

### Agent Test Script

```bash
#!/bin/bash
# test-agents.sh

AGENTS_DIR="agents"
TEST_CASES_DIR="test-cases"
ERRORS=0

for agent_file in "$AGENTS_DIR"/*.md; do
    agent_name=$(basename "$agent_file" .md)
    
    echo "Testing agent: $agent_name"
    
    # Check agent file exists
    if [ ! -f "$agent_file" ]; then
        echo "ERROR: Agent file not found"
        ERRORS=$((ERRORS + 1))
        continue
    fi
    
    # Test with test cases
    if [ -d "$TEST_CASES_DIR/$agent_name" ]; then
        for test_file in "$TEST_CASES_DIR/$agent_name"/*.php; do
            echo "Testing with: $test_file"
            
            # Run agent on test file
            result=$(opencode agent "$agent_name" --file="$test_file" 2>&1)
            
            # Check agent detected expected issues
            if [[ $result != *"ERROR"* ]] && [[ $result != *"WARNING"* ]]; then
                echo "ERROR: Agent did not detect issues in $test_file"
                ERRORS=$((ERRORS + 1))
            fi
        done
    fi
done

echo "Agent tests completed with $ERRORS errors"
exit $ERRORS
```

## Hook Testing

### Manual Hook Testing

Test hooks manually:

```bash
# Test PHP lint hook
node hooks/php-lint.js test-files/valid.php
# Should pass

node hooks/php-lint.js test-files/syntax-error.php
# Should fail with error

# Test WordPress debug check
node hooks/wp-debug-check.js test-files/no-debug.php
# Should pass

node hooks/wp-debug-check.js test-files/with-debug.php
# Should fail with var_dump detected

# Test security check
node hooks/security-check.js test-files/secure.php
# Should pass

node hooks/security-check.js test-files/sql-injection.php
# Should fail with SQL injection risk
```

### Hook Test Files

Create test files for hooks:

```php
// test-files/valid.php
<?php
/**
 * Valid PHP file
 */

function valid_function() {
    return 'valid';
}
```

```php
// test-files/syntax-error.php
<?php
function broken_function() {
    echo "missing semicolon"
    return // syntax error
}
```

```php
// test-files/with-debug.php
<?php
function debug_function() {
    var_dump($variable); // Should trigger error
    print_r($array); // Should trigger error
    error_log('test'); // Allowed
}
```

```php
// test-files/sql-injection.php
<?php
function insecure_query() {
    global $wpdb;
    $id = $_GET['id'];
    $results = $wpdb->get_results("SELECT * FROM table WHERE id = $id");
    // Should trigger error
}
```

### Jest Hook Tests

Create Jest tests for hooks:

```javascript
// hooks/__tests__/php-lint.test.js
const phpLint = require('../php-lint.js');
const fs = require('fs');

describe('PHP Lint Hook', () => {
    test('should pass valid PHP file', async () => {
        const files = [
            { path: 'test-files/valid.php', content: fs.readFileSync('test-files/valid.php', 'utf8') }
        ];
        
        const result = await phpLint.validate(files, {});
        expect(result.valid).toBe(true);
        expect(result.errors).toHaveLength(0);
    });
    
    test('should fail PHP file with syntax error', async () => {
        const files = [
            { path: 'test-files/syntax-error.php', content: fs.readFileSync('test-files/syntax-error.php', 'utf8') }
        ];
        
        const result = await phpLint.validate(files, {});
        expect(result.valid).toBe(false);
        expect(result.errors.length).toBeGreaterThan(0);
    });
    
    test('should skip excluded files', async () => {
        const files = [
            { path: 'vendor/package/file.php', content: 'invalid' }
        ];
        
        const config = { exclude: ['vendor/'] };
        const result = await phpLint.validate(files, config);
        expect(result.valid).toBe(true);
    });
});

// hooks/__tests__/wp-debug-check.test.js
const wpDebugCheck = require('../wp-debug-check.js');

describe('WordPress Debug Check Hook', () => {
    test('should detect var_dump', async () => {
        const files = [
            { path: 'test.php', content: 'var_dump($var);' }
        ];
        
        const result = await wpDebugCheck.validate(files, {});
        expect(result.valid).toBe(false);
        expect(result.errors[0].message).toContain('var_dump');
    });
    
    test('should detect print_r', async () => {
        const files = [
            { path: 'test.php', content: 'print_r($array);' }
        ];
        
        const result = await wpDebugCheck.validate(files, {});
        expect(result.valid).toBe(false);
        expect(result.errors[0].message).toContain('print_r');
    });
    
    test('should allow error_log', async () => {
        const files = [
            { path: 'test.php', content: 'error_log("message");' }
        ];
        
        const result = await wpDebugCheck.validate(files, {});
        expect(result.valid).toBe(true);
    });
});

// hooks/__tests__/security-check.test.js
const securityCheck = require('../security-check.js');

describe('Security Check Hook', () => {
    test('should detect SQL injection', async () => {
        const files = [
            { path: 'test.php', content: '$wpdb->get_results("SELECT * FROM table WHERE id = $id");' }
        ];
        
        const result = await securityCheck.validate(files, {});
        expect(result.valid).toBe(false);
        expect(result.errors[0].message).toContain('SQL injection');
    });
    
    test('should detect missing nonce verification', async () => {
        const files = [
            { path: 'test.php', content: 'update_option("key", $_POST["value"]);' }
        ];
        
        const result = await securityCheck.validate(files, {});
        expect(result.valid).toBe(false);
        expect(result.errors[0].message).toContain('nonce');
    });
    
    test('should pass secure code', async () => {
        const files = [
            { 
                path: 'test.php', 
                content: `
                    if (!wp_verify_nonce($_POST['nonce'], 'action')) wp_die();
                    if (!current_user_can('manage_options')) wp_die();
                    $value = sanitize_text_field($_POST['value']);
                    update_option('key', $value);
                `
            }
        ];
        
        const result = await securityCheck.validate(files, {});
        expect(result.valid).toBe(true);
    });
});
```

Run Jest tests:

```bash
cd hooks
npm test
```

## Command Testing

### Command Invocation Test

Test commands work correctly:

```bash
# Test theme command
/wp-theme create test-theme
# Verify theme created

/wp-theme validate test-theme
# Verify validation runs

# Test plugin command
/wp-plugin create test-plugin
# Verify plugin created

/wp-plugin validate test-plugin
# Verify validation runs

# Test review command
/wp-review path/to/file.php
# Verify review executes
```

### Command Test Script

```bash
#!/bin/bash
# test-commands.sh

COMMANDS_DIR=".opencode/commands"
ERRORS=0

for command_file in "$COMMANDS_DIR"/*.md; do
    command_name=$(basename "$command_file" .md)
    
    echo "Testing command: $command_name"
    
    # Check command file exists
    if [ ! -f "$command_file" ]; then
        echo "ERROR: Command file not found"
        ERRORS=$((ERRORS + 1))
        continue
    fi
    
    # Validate markdown
    if ! markdown-validator "$command_file"; then
        echo "ERROR: Invalid markdown"
        ERRORS=$((ERRORS + 1))
    fi
    
    # Check frontmatter
    if ! grep -q "^---" "$command_file"; then
        echo "ERROR: Missing frontmatter"
        ERRORS=$((ERRORS + 1))
    fi
    
    # Test command (mock)
    if ! opencode-test command "$command_name"; then
        echo "ERROR: Command invocation failed"
        ERRORS=$((ERRORS + 1))
    fi
done

echo "Command tests completed with $ERRORS errors"
exit $ERRORS
```

## Example Testing

### Theme Example Testing

Test theme example works:

```bash
# Navigate to theme example
cd examples/theme-example

# Check theme structure
ls -la
# Required files: style.css, index.php, functions.php

# Validate theme
wp theme validate opencode-theme-example

# Theme Check plugin
wp plugin install theme-check --activate
wp theme-check opencode-theme-example

# Test theme functionality
wp theme activate opencode-theme-example
# Visit site, verify templates work
```

### Plugin Example Testing

Test plugin example:

```bash
# Navigate to plugin example
cd examples/plugin-example

# Check plugin structure
ls -la
# Required file: my-plugin.php

# Validate plugin
wp plugin validate opencode-plugin-example

# Test plugin activation
wp plugin activate opencode-plugin-example

# Test plugin functionality
wp opencode-plugin test-command

# PHPUnit tests (if provided)
phpunit
```

### WooCommerce Example Testing

Test WooCommerce example:

```bash
# Ensure WooCommerce active
wp plugin install woocommerce --activate

# Navigate to WooCommerce example
cd examples/woocommerce-example

# Activate extension
wp plugin activate opencode-wc-extension

# Test WooCommerce integration
wc --extension=opencode-wc-extension test

# Create test product
wc product create --type=opencode_custom --name="Test Product"

# Test custom product type
wc product list --type=opencode_custom

# PHPUnit tests
phpunit
```

## Integration Testing

### Full Workflow Test

Test complete workflow:

```bash
# 1. Create test WordPress installation
wp core download
wp config create --dbname=test --dbuser=root
wp db create
wp core install --url=test.local --title=Test

# 2. Start OpenCode in test installation
cd test-wordpress
opencode

# 3. Invoke skill
opencode skill wordpress-theme-development

# 4. Create theme following skill guidance
opencode: "Create theme following skill guidance"

# 5. Use command for validation
/wp-theme validate

# 6. Let agent review
opencode agent theme-reviewer

# 7. Fix issues
/wp-build-fix

# 8. Commit with hooks
git add .
git commit -m "Test workflow"

# 9. Verify hooks ran
git log --show-notes=hooks

# 10. Verify everything works
wp theme list
wp theme activate test-theme
```

### CI/CD Integration Test

Test in CI pipeline:

```yaml
# .github/workflows/test.yml
name: OpenCode WordPress Tests

on: [push, pull_request]

jobs:
  test-skills:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Test Skills
        run: ./test-skills.sh
  
  test-agents:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Test Agents
        run: ./test-agents.sh
  
  test-hooks:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: actions/setup-node@v2
      - name: Install dependencies
        run: cd hooks && npm install
      - name: Test Hooks
        run: cd hooks && npm test
  
  test-examples:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:5.7
      wordpress:
        image: wordpress:latest
    steps:
      - uses: actions/checkout@v2
      - name: Test Theme Example
        run: wp theme validate examples/theme-example
      - name: Test Plugin Example
        run: wp plugin validate examples/plugin-example
```

## WordPress Testing

### WordPress Test Environment

Set up WordPress test environment:

```bash
# Install WordPress test library
bash <(curl -s https://raw.githubusercontent.com/kurpass/wp-test-lib/master/install.sh)

# Or use wp-cli scaffold
wp scaffold plugin-tests examples/plugin-example

# Run WordPress tests
phpunit -c wordpress-tests/phpunit.xml
```

### WordPress Unit Tests

Create WordPress unit tests:

```php
// examples/plugin-example/tests/test-main.php
<?php

class Plugin_Test extends WP_UnitTestCase {

    public function test_plugin_activated() {
        $this->assertTrue(class_exists('OpenCode_Plugin_Example\Plugin'));
    }

    public function test_custom_post_type_registered() {
        $this->assertTrue(post_type_exists('opencode_item'));
    }

    public function test_option_saved() {
        $value = 'test_value';
        update_option('opencode_plugin_test', $value);
        $this->assertEquals($value, get_option('opencode_plugin_test'));
    }

    public function test_shortcode_registered() {
        global $shortcode_tags;
        $this->assertArrayHasKey('opencode_shortcode', $shortcode_tags);
    }
}
```

Run WordPress unit tests:

```bash
cd examples/plugin-example
phpunit
```

### Theme Unit Tests

Create theme unit tests:

```php
// examples/theme-example/tests/test-theme.php
<?php

class Theme_Test extends WP_UnitTestCase {

    public function test_theme_activated() {
        $this->assertEquals('opencode-theme-example', get_stylesheet());
    }

    public function test_theme_supports_title_tag() {
        $this->assertTrue(current_theme_supports('title-tag'));
    }

    public function test_theme_supports_post_thumbnails() {
        $this->assertTrue(current_theme_supports('post-thumbnails'));
    }

    public function test_nav_menu_registered() {
        $locations = get_nav_menu_locations();
        $this->assertArrayHasKey('primary', $locations);
    }

    public function test_sidebar_registered() {
        $this->assertTrue(is_active_sidebar('sidebar-1'));
    }
}
```

## Performance Testing

### Skill Performance

Test skill performance:

```bash
# Time skill invocation
time opencode skill wordpress-theme-development

# Should be < 5 seconds
```

### Agent Performance

Test agent performance:

```bash
# Time agent review on large file
time opencode agent wordpress-reviewer --file=large-plugin.php

# Should be < 10 seconds for 100KB file
```

### Hook Performance

Test hook performance:

```bash
# Time hook validation on 100 files
time node hooks/php-lint.js $(find . -name "*.php" | head -100)

# Should be < 30 seconds
```

## Test Coverage

### Coverage Requirements

Minimum test coverage:

- **Skills**: All skills tested
- **Agents**: All agents tested
- **Hooks**: 80% code coverage
- **Examples**: Core functionality tested

### Coverage Reports

Generate coverage reports:

```bash
# Jest coverage
cd hooks
npm test -- --coverage

# PHPUnit coverage
cd examples/plugin-example
phpunit --coverage-html coverage/

# View reports
open hooks/coverage/lcov-report/index.html
open examples/plugin-example/coverage/index.html
```

## Continuous Testing

### Pre-commit Testing

Automated testing before commit:

```bash
# .git/hooks/pre-commit
#!/bin/bash

# Run all tests
./test-skills.sh
./test-agents.sh
cd hooks && npm test

# If any test fails, abort commit
```

### Automated Testing

Automated test pipeline:

```bash
# test-all.sh
#!/bin/bash

echo "Running all tests..."

# Skills
./test-skills.sh || exit 1

# Agents
./test-agents.sh || exit 1

# Commands
./test-commands.sh || exit 1

# Hooks
cd hooks && npm test || exit 1

# Examples
cd examples/plugin-example && phpunit || exit 1
cd examples/theme-example && phpunit || exit 1

echo "All tests passed!"
```

Run all tests:

```bash
chmod +x test-all.sh
./test-all.sh
```

## Test Maintenance

### Update Tests

Update tests when:

- Adding new skills
- Adding new agents
- Adding new hooks
- Adding new examples
- WordPress version changes
- OpenCode version changes

### Test Review

Review tests regularly:

- Weekly: Check all tests pass
- Monthly: Review test coverage
- Quarterly: Update test cases
- Annually: Full test audit

## Troubleshooting Tests

### Skill Test Fails

```bash
# Check SKILL.md
cat skills/skill-name/SKILL.md

# Validate markdown
markdown-validator skills/skill-name/SKILL.md

# Check skill invocation
opencode skill skill-name --debug
```

### Agent Test Fails

```bash
# Check agent file
cat agents/agent-name.md

# Check test cases
ls test-cases/agent-name/

# Run agent manually
opencode agent agent-name --file=test-file.php --debug
```

### Hook Test Fails

```bash
# Check hook file
cat hooks/hook-name.js

# Run hook manually
node hooks/hook-name.js test-file.php

# Check Node.js version
node --version

# Reinstall dependencies
cd hooks && rm -rf node_modules && npm install
```

### PHPUnit Fails

```bash
# Check WordPress test library
ls wordpress-tests/

# Check phpunit.xml
cat phpunit.xml

# Run with debug
phpunit --debug

# Check PHP version
php --version
```

## Best Practices

### Test First

Write tests before or alongside code:

- Test-driven development
- Examples need tests
- Hooks need tests
- Agents need test cases

### Keep Tests Updated

Update tests when:

- Code changes
- WordPress updates
- Standards change
- Bugs found

### Comprehensive Testing

Test all aspects:

- Functionality
- Security
- Performance
- Compatibility
- Edge cases

### Document Tests

Document test requirements:

- How to run tests
- What tests cover
- How to fix test failures
- Test maintenance schedule

## Support

For testing questions:
- **GitHub Issues**: https://github.com/yourusername/opencode-wordpress/issues
- **Testing Discussions**: https://github.com/yourusername/opencode-wordpress/discussions/categories/testing

## License

GPL v2 or later