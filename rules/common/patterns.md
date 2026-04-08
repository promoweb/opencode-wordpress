paths:
  "**/*"

# Common Design Patterns

Language-agnostic design patterns for all projects.

## Pattern Philosophy

### Use Patterns Judiciously

- Patterns are tools, not goals
- Don't over-engineer with patterns
- Choose patterns based on needs
- Adapt patterns to context

### Pattern Benefits

- Reusable solutions
- Common vocabulary
- Proven approaches
- Maintainable code

## Architectural Patterns

### Layered Architecture

- **Presentation Layer**: UI/User interface
- **Application Layer**: Business logic orchestration
- **Domain Layer**: Core business rules
- **Infrastructure Layer**: External services, data access

**Benefits**:
- Separation of concerns
- Independent layer testing
- Clear boundaries
- Easy to understand

### Repository Pattern

- Abstract data access
- Interface-based repositories
- Isolate persistence logic
- Enable testing without database

**Structure**:
```
Repository Interface -> Repository Implementation -> Data Source
```

**Benefits**:
- Decoupled data access
- Easy to mock for tests
- Switchable data sources
- Centralized query logic

### Service Layer Pattern

- Business logic in services
- Orchestrate repository calls
- Transaction management
- Application-level validation

**Structure**:
```
Controller -> Service -> Repository -> Database
```

**Benefits**:
- Thin controllers
- Reusable business logic
- Transaction boundaries
- Clear responsibility

### Factory Pattern

- Object creation encapsulation
- Interface-based factories
- Centralized creation logic
- Hide complex instantiation

**Benefits**:
- Flexible object creation
- Easy to change creation logic
- Consistent object initialization
- Reduce coupling

## Structural Patterns

### Singleton Pattern

- Single instance per application
- Global access point
- Controlled instantiation
- Use sparingly

**When to Use**:
- Configuration objects
- Logging services
- Cache managers

**Caution**:
- Often overused
- Can make testing difficult
- Hidden dependencies

### Adapter Pattern

- Wrap incompatible interfaces
- Convert interface to expected format
- Isolate third-party code
- Enable interface compatibility

**Benefits**:
- Decouple from external code
- Easy to swap implementations
- Testable adapters
- Clean interfaces

### Decorator Pattern

- Add behavior dynamically
- Wrap objects with additional functionality
- Transparent to clients
- Composable enhancements

**Benefits**:
- Flexible behavior addition
- Avoid class explosion
- Runtime behavior changes
- Single responsibility

### Composite Pattern

- Tree structure of objects
- Treat individual and group uniformly
- Recursive composition
- Hierarchical representation

**Benefits**:
- Uniform tree operations
- Easy to add new components
- Recursive algorithms
- Clear hierarchy

## Behavioral Patterns

### Strategy Pattern

- Encapsulate algorithms
- Interchangeable algorithms
- Client selects strategy
- Eliminate conditional logic

**Benefits**:
- Flexible algorithm selection
- Easy to add new strategies
- Eliminate large conditionals
- Testable strategies

### Observer Pattern

- Subscribe to events
- Event notification system
- Decouple publisher/subscriber
- One-to-many dependency

**Benefits**:
- Loose coupling
- Dynamic subscriptions
- Broadcast communication
- Event-driven architecture

### Command Pattern

- Encapsulate requests as objects
- Parameterize clients with requests
- Queue/schedule operations
- Undo/redo operations

**Benefits**:
- Decoupled request handling
- Queueable operations
- Undo/redo capability
- Transaction batching

### Template Method Pattern

- Define algorithm skeleton
- Subclasses implement steps
- Reuse common algorithm structure
- Customize individual steps

**Benefits**:
- Code reuse
- Controlled customization
- Clear algorithm structure
- Consistent workflow

## Dependency Injection

### Constructor Injection

- Inject dependencies via constructor
- Required dependencies explicit
- Immutable dependencies
- Testable with injected mocks

**Benefits**:
- Explicit dependencies
- Testable objects
- Clear initialization
- Type-safe injection

### Setter Injection

- Inject dependencies via setters
- Optional dependencies
- Change dependencies after creation
- Flexible configuration

**Benefits**:
- Optional dependencies
- Runtime dependency changes
- Flexible configuration

### Interface Injection

- Dependencies implement interfaces
- Decoupled from implementations
- Swappable implementations
- Mockable dependencies

**Benefits**:
- Complete decoupling
- Flexible implementations
- Testable interfaces

## Anti-Patterns to Avoid

### Spaghetti Code

- No clear structure
- Mixed responsibilities
- Deeply nested logic
- Hard to understand

**Fix**: Use layered architecture, clear separation of concerns

### Copy-Paste Programming

- Duplicate code
- Inconsistent behavior
- Hard to maintain
- Bug duplication

**Fix**: Extract common logic into functions/classes

### God Object

- One object does everything
- Multiple responsibilities
- Hard to test
- Hard to understand

**Fix**: Split into focused objects, single responsibility

### Magic Numbers/Strings

- Unexplained constants
- Hard to understand
- Hard to change
- Error-prone

**Fix**: Use named constants, configuration

### Premature Optimization

- Optimize without measuring
- Complex code for theoretical speed
- Maintainability sacrificed
- Wrong optimization targets

**Fix**: Measure first, optimize proven bottlenecks

## Pattern Selection Guide

### Choose Patterns Based On:

1. **Problem**: What problem are you solving?
2. **Context**: What is your project context?
3. **Complexity**: Is complexity worth the pattern?
4. **Team**: Does team understand the pattern?
5. **Testing**: Does pattern improve testability?

### Pattern Decision Process:

1. Identify the problem
2. Consider multiple patterns
3. Evaluate trade-offs
4. Start simple, add patterns when needed
5. Refactor to patterns when pain points appear

## Language-Specific Extensions

**Note**: Language-specific pattern rules extend these common patterns with language-idiomatic implementations.

See:
- `rules/wordpress/patterns.md` for WordPress-specific patterns (Singleton, Factory adapted for WP)
- Other language directories as needed