paths:
  "**/*"

# Common Coding Style

Language-agnostic coding principles for all projects.

## Core Principles

### Readability First

- Code should be self-documenting
- Use descriptive names over comments
- Keep functions small and focused (<50 lines)
- Avoid deep nesting (>4 levels)

### Immutability

- Prefer immutable data structures
- Avoid side effects in functions
- Return new values instead of modifying inputs
- Use pure functions when possible

### Explicit Over Implicit

- Explicit is better than implicit
- Avoid magic numbers and strings
- Use constants for fixed values
- Document assumptions

### Composition

- Small, composable functions
- Avoid monolithic functions
- Prefer composition over inheritance
- Single responsibility principle

## Naming Conventions

- **Variables**: descriptive, noun-based
- **Functions**: verb-based, action-oriented
- **Classes**: noun-based, PascalCase
- **Constants**: UPPER_SNAKE_CASE
- **Files**: match primary export/functionality

## Code Organization

- Group related functions together
- Separate concerns by file/module
- Public API at top, private at bottom
- Import statements grouped and sorted

## Error Handling

- Validate inputs early
- Use explicit error types
- Provide meaningful error messages
- Handle errors at appropriate level
- Never silently swallow errors

## Comments

- Explain **why**, not **what**
- Avoid redundant comments
- Use TODO/FIXME for incomplete work
- Document complex algorithms
- Keep comments up-to-date with code

## Testing Requirements

- Write tests first (TDD when appropriate)
- Minimum 70% test coverage
- Test edge cases and error scenarios
- Use descriptive test names
- One assertion per test (when practical)

## Performance

- Measure before optimizing
- Avoid premature optimization
- Use appropriate data structures
- Cache expensive operations
- Batch operations when possible

## Security

- Validate all inputs
- Sanitize all outputs
- Never trust user data
- Use secure defaults
- Keep secrets in environment variables
- Audit dependencies regularly

## Refactoring

- Refactor incrementally
- Maintain behavior during refactoring
- Test before and after refactoring
- Use version control effectively
- Document architectural decisions

## Language-Specific Extensions

**Note**: Language-specific rules in respective directories extend these common principles with language-idiomatic patterns.

See:
- `rules/wordpress/` for WordPress-specific coding standards
- `rules/php/` for PHP-specific coding standards
- Other language directories as needed