paths:
  "**/*"

# Common Security Principles

Language-agnostic security standards for all projects.

## Security Philosophy

### Defense in Depth

- Multiple layers of security
- Never rely on single security measure
- Validate at multiple points
- Fail securely on errors

### Least Privilege

- Grant minimum necessary permissions
- Avoid over-privileged accounts
- Restrict access by default
- Audit permission usage

### Secure Defaults

- Default configurations should be secure
- Security settings opt-out, not opt-in
- Never expose sensitive data by default
- Disable unnecessary features

## Input Validation

### Validate Early

- Validate at entry points
- Check input before processing
- Reject invalid input immediately
- Use whitelist validation over blacklist

### Validation Rules

- Type checking (string, number, array, etc.)
- Length/range validation
- Format validation (email, URL, UUID, etc.)
- Business rule validation
- Context-specific validation

### Validation Implementation

- Centralized validation logic
- Reusable validation functions
- Validation error messages
- Validation logging (for debugging)

## Output Escaping

### Context-Aware Escaping

- Escape for appropriate context
- HTML context: escape HTML entities
- JavaScript context: escape for JS
- URL context: encode URLs
- SQL context: use prepared statements

### Escaping Rules

- Escape all user-generated output
- Escape system-generated output (when needed)
- Use framework/library escaping functions
- Never rely on input validation alone
- Double-check escaping in templates

## Authentication

### Password Security

- Strong password requirements
- Hash passwords with bcrypt/argon2
- Never store plaintext passwords
- Use password hashing libraries
- Implement password reset securely

### Session Security

- Secure session tokens
- Session expiration
- Session regeneration
- Prevent session fixation
- Secure cookie settings

### Authentication Checks

- Verify authentication on every request
- Use centralized auth middleware
- Handle auth failures gracefully
- Log auth failures (for monitoring)

## Authorization

### Permission Checks

- Check permissions before operations
- Use role-based access control (RBAC)
- Define clear permission levels
- Audit permission usage

### Authorization Implementation

- Centralized authorization logic
- Role/permission definitions
- Permission check functions
- Authorization logging

## Data Protection

### Sensitive Data

- Never log sensitive data
- Encrypt sensitive data at rest
- Encrypt sensitive data in transit
- Mask sensitive data in displays
- Use secure storage for secrets

### Data Retention

- Define data retention policies
- Implement data deletion
- Secure data backup
- Audit data access

## Error Handling

### Secure Error Messages

- Never expose internal details
- User-friendly error messages
- Log detailed errors internally
- Separate user/system error messages

### Error Logging

- Log errors securely
- Never log sensitive data
- Include relevant context
- Monitor error patterns

## Secret Management

### Secrets Storage

- Never hardcode secrets
- Use environment variables
- Use secret management tools
- Rotate secrets regularly
- Audit secret access

### Secrets in Code

- No secrets in source code
- No secrets in version control
- No secrets in logs
- No secrets in error messages
- Use secret injection at runtime

## Security Testing

### Security Test Types

- Input validation tests
- Authentication tests
- Authorization tests
- Injection tests (SQL, XSS, etc.)
- CSRF tests

### Security Test Coverage

- Test all security controls
- Test edge cases
- Test failure scenarios
- Regular security audits
- Penetration testing (periodic)

## Security Monitoring

### Monitoring Points

- Authentication failures
- Authorization violations
- Input validation failures
- Unusual access patterns
- Security errors

### Incident Response

- Define incident response plan
- Monitor security alerts
- Quick response procedures
- Post-incident review

## Security Headers

- Content-Security-Policy (CSP)
- X-Frame-Options
- X-Content-Type-Options
- Strict-Transport-Security (HSTS)
- X-XSS-Protection

## Dependency Security

- Audit dependencies regularly
- Use dependency scanning tools
- Update dependencies promptly
- Remove unused dependencies
- Pin dependency versions

## Language-Specific Extensions

**Note**: Language-specific security rules extend these common principles with framework-specific security guidance.

See:
- `rules/wordpress/security.md` for WordPress-specific security (sanitization, escaping, nonces)
- Other language directories as needed