paths:
  "**/*"

# Common Testing Principles

Language-agnostic testing standards for all projects.

## Testing Philosophy

### Test Pyramid

- **Unit Tests**: 70% - Fast, isolated, numerous
- **Integration Tests**: 20% - Component interactions
- **E2E Tests**: 10% - Critical user flows

### Test-Driven Development

- Write tests before code (when appropriate)
- Tests specify behavior, not implementation
- Refactor with confidence using tests
- Use tests as documentation

## Test Coverage

- **Minimum**: 70% coverage
- **Target**: 80%+ coverage
- **Critical paths**: 100% coverage
- Measure coverage regularly
- Track coverage trends over time

## Test Quality

### Test Names

- Descriptive and specific
- State expected behavior
- Format: `test_<function>_<scenario>_<expected>`
- Example: `test_createUser_validInput_returnsUser`

### Test Structure

- **Setup**: Prepare test data and state
- **Execute**: Run the code under test
- **Verify**: Check results match expectations
- **Cleanup**: Reset state (if needed)

### Test Independence

- Tests should not depend on each other
- Each test should be runnable alone
- Avoid shared mutable state
- Use test fixtures appropriately

## Test Types

### Unit Tests

- Test single functions/methods
- Mock external dependencies
- Fast execution (<1s total)
- No I/O operations
- No database/network calls

### Integration Tests

- Test component interactions
- Use real dependencies (when practical)
- Test database/network operations
- Longer execution time acceptable
- Test error scenarios

### E2E Tests

- Test critical user flows
- Use real environment
- Simulate user interactions
- Test across layers
- Focus on high-value scenarios

## Test Data

- Use factories for test data
- Avoid hardcoded test data
- Keep test data minimal
- Use realistic data scenarios
- Test edge cases explicitly

## Mocking Guidelines

- Mock external services
- Mock slow operations
- Use dependency injection for mocking
- Avoid over-mocking
- Mock behavior, not implementation

## Test Maintenance

- Refactor tests with code
- Keep tests readable
- Remove obsolete tests
- Update tests for new requirements
- Monitor test execution time

## Continuous Integration

- Run tests on every commit
- Fail fast on test failures
- Run tests in parallel (when possible)
- Use test result caching
- Generate test reports

## Test Performance

- Fast test suite (<10s for unit tests)
- Parallelize tests where possible
- Use test isolation for speed
- Avoid slow operations in unit tests
- Monitor test execution trends

## Test Documentation

- Tests serve as documentation
- Document test purpose in name
- Use test fixtures as examples
- Keep test documentation current
- Explain complex test scenarios

## Language-Specific Extensions

**Note**: Language-specific testing rules extend these common principles with framework-specific guidance.

See:
- `rules/wordpress/testing.md` for WordPress testing with PHPUnit
- Other language directories as needed