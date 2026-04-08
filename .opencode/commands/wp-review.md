# WordPress Code Review

Perform a comprehensive code review of WordPress theme or plugin code.

## Task

$ARGUMENTS

## Review Scope

This command performs a thorough review covering:

### 1. Security Review 🚨

**Critical Security Checks**:
- [ ] Input sanitization (all `$_GET`, `$_POST`, `$_REQUEST`)
- [ ] Output escaping (`esc_html()`, `esc_attr()`, `esc_url()`)
- [ ] Nonce verification for forms and AJAX
- [ ] Capability checks for privileged operations
- [ ] SQL injection prevention (prepared statements)
- [ ] File upload validation
- [ ] CSRF protection
- [ ] XSS prevention
- [ ] No hardcoded credentials

**Red Flags**:
- Unescaped output: `echo $var`
- Unprepared queries: `$wpdb->query( "WHERE id = $id" )`
- Missing nonce: Form processing without `wp_verify_nonce()`
- Missing capability check: Admin functions without `current_user_can()`
- `eval()`, `exec()`, `system()` usage

### 2. WordPress Coding Standards

**Naming Conventions**:
- [ ] Functions/variables: `snake_case`
- [ ] Classes: `PascalCase`
- [ ] Constants: `UPPER_SNAKE_CASE`
- [ ] Files: `lowercase-with-hyphens.php`
- [ ] All identifiers prefixed

**Formatting**:
- [ ] Yoda conditions
- [ ] Allman brace style
- [ ] Proper spacing
- [ ] Tabs for indentation
- [ ] Single quotes for strings (when possible)

**Documentation**:
- [ ] DocBlocks for functions/classes
- [ ] Inline comments where needed
- [ ] README documentation

### 3. Performance Review

**Database Performance**:
- [ ] No N+1 queries
- [ ] Prepared statements
- [ ] Proper indexes
- [ ] Select specific columns (not `SELECT *`)
- [ ] Use `LIMIT` for large result sets
- [ ] Cache expensive queries with transients

**General Performance**:
- [ ] Avoid expensive operations in frequently called hooks
- [ ] Proper hook priorities
- [ ] Minimal autoloaded options
- [ ] Conditional script/style loading
- [ ] Image optimization

### 4. Hooks and Filters

**Correct Usage**:
- [ ] Actions for operations (no return)
- [ ] Filters for data modification (must return)
- [ ] Proper priorities
- [ ] Accepted args specified
- [ ] Return values in filters
- [ ] Prefixed hook names

**Common Issues**:
- Filter not returning value
- Wrong hook type (action vs filter)
- Missing accepted args parameter
- Nested hooks causing infinite loops

### 5. Database Operations

**Security and Efficiency**:
- [ ] All queries use `$wpdb->prepare()`
- [ ] Table names use `$wpdb->prefix`
- [ ] Data sanitized before DB operations
- [ ] Error handling implemented
- [ ] WordPress APIs used when appropriate (Options, Metadata)

### 6. REST API (if applicable)

**Best Practices**:
- [ ] `permission_callback` implemented
- [ ] Arguments sanitized and validated
- [ ] Proper HTTP methods
- [ ] Correct status codes
- [ ] Error handling

### 7. AJAX Handlers (if applicable)

**Security**:
- [ ] Nonce verification
- [ ] Capability checks
- [ ] Input sanitization
- [ ] `wp_send_json_*` responses
- [ ] Both authenticated and non-authenticated handlers

### 8. Internationalization

**Translation Readiness**:
- [ ] All strings internationalized
- [ ] Text domain matches plugin/theme
- [ ] Proper i18n functions (`__()`, `_e()`, etc.)
- [ ] Translation files present
- [ ] Translator comments for context

### 9. Error Handling

**Robustness**:
- [ ] Proper error checking
- [ ] WP_Error usage
- [ ] Graceful failure handling
- [ ] Error logging (not displaying in production)
- [ ] User-friendly error messages

### 10. Best Practices

**WordPress Standards**:
- [ ] Use WordPress APIs
- [ ] No plugin territory in themes
- [ ] No theme territory in plugins
- [ ] Follow WordPress philosophy
- [ ] Backward compatibility
- [ ] PHP version compatibility

## Review Format

```markdown
# Code Review Report

## Summary
[Brief overall assessment]

## Critical Issues 🚨 (MUST FIX)
1. **[File:Line]**: [Issue]
   - Severity: Critical
   - Issue: [What's wrong]
   - Impact: [Security/Performance/Functionality]
   - Fix: [How to fix]

## Security Issues
1. **[File:Line]**: [Issue]
   - Severity: Critical/High/Medium/Low
   - Fix: [How to fix]

## Performance Issues
1. **[File:Line]**: [Issue]
   - Severity: High/Medium/Low
   - Fix: [How to fix]

## Coding Standards
1. **[File:Line]**: [Issue]
   - Fix: [How to fix]

## Best Practices
1. **[File:Line]**: [Recommendation]

## Positive Findings
[What's done well]

## Recommendations
[Improvement suggestions]

## Code Quality Score
**Grade**: A/B/C/D/F
**Explanation**: [Reasoning]
```

## Severity Levels

- **Critical**: Security vulnerabilities, data loss risk, breaking bugs
- **High**: Significant performance issues, major best practice violations
- **Medium**: Coding standard violations, minor performance issues
- **Low**: Style improvements, minor documentation issues

## Tools to Use

- `read`: Read file contents
- `grep`: Search for patterns
- `glob`: Find files

## Output

Provide:
1. Executive summary
2. Critical security issues (if any)
3. Performance concerns
4. Coding standards violations
5. Best practice recommendations
6. Overall code quality score with explanation

## Quick Checks

Run these quick checks automatically:

```bash
# Check for unescaped output
grep -r "echo \$" --include="*.php"

# Check for unprepared queries
grep -r "\$wpdb->query.*\$" --include="*.php"

# Check for missing nonce verification in POST handlers
grep -r "if.*isset.*_POST" --include="*.php"

# Check for hardcoded credentials
grep -rE "(password|secret|api_key|token).*=.*['\"]" --include="*.php"
```

## Notes

- Focus on critical issues first
- Be constructive in feedback
- Provide actionable recommendations
- Explain why something is an issue
- Reference WordPress documentation

Use `wordpress-reviewer` agent for comprehensive review or specialized agents (`theme-reviewer`, `plugin-reviewer`) for specific contexts.