/**
 * WordPress Security Check Hook
 * 
 * Checks for common WordPress security issues in code.
 * Profile: strict
 */

module.exports = {
  name: 'security-check',
  
  config: {
    profile: 'strict',
    severity: {
      critical: 1,    // Block on critical issues
      high: 2,        // Warn on high issues
      medium: 3,      // Info on medium issues
      low: 4          // Log low issues
    }
  },
  
  // Security check patterns
  checks: {
    // Critical security issues
    critical: [
      {
        pattern: /eval\s*\(/,
        message: 'eval() is dangerous and should never be used',
        fix: 'Remove eval() and use safer alternatives'
      },
      {
        pattern: /exec\s*\(|system\s*\(|passthru\s*\(|shell_exec\s*\(/,
        message: 'System command execution detected - major security risk',
        fix: 'Use WordPress APIs instead of direct system calls'
      },
      {
        pattern: /\$\{?\$/,
        message: 'Variable variable detected - potential code injection risk',
        fix: 'Avoid variable variables, use arrays instead'
      },
      {
        pattern: /mysql_query\s*\(|mysql_connect\s*\(/,
        message: 'Deprecated mysql_* functions - use $wpdb or PDO',
        fix: 'Replace with wpdb or mysqli/PDO'
      }
    ],
    
    // High severity issues
    high: [
      {
        pattern: /echo\s+\$_(GET|POST|REQUEST|COOKIE)\[/,
        message: 'Unescaped direct output of user input (XSS risk)',
        fix: 'Use esc_html(), esc_attr(), or esc_url()'
      },
      {
        pattern: /echo\s+\$[a-zA-Z_]+\s*;/,
        message: 'Potential late escaping violation - variable output without escaping',
        fix: 'Use esc_html(), esc_attr(), esc_url(), or wp_kses() at point of output'
      },
      {
        pattern: /printf\s*\(\s*["'][^"']*%[sd][^"']*["']\s*,\s*\$[a-zA-Z_]+\s*\)\s*;/,
        message: 'Potential late escaping - printf with unescaped variable',
        fix: 'Escape variables before passing to printf() or use esc_html() in format string'
      },
      {
        pattern: /print\s+\$[a-zA-Z_]+\s*;/,
        message: 'Potential late escaping violation - print with unescaped variable',
        fix: 'Use esc_html(), esc_attr(), or appropriate escaping function'
      },
      {
        pattern: /\$_(GET|POST|REQUEST)\[[^\]]+\]\s*(?!.*sanitize)/,
        message: 'User input not sanitized (injection risk)',
        fix: 'Use sanitize_text_field() or appropriate sanitization function'
      },
      {
        pattern: /\$wpdb->query\s*\(\s*["']SELECT.*?\$/,
        message: 'Potential SQL injection - unprepared query with variable',
        fix: 'Use $wpdb->prepare() for all queries'
      },
      {
        pattern: /\$wpdb->get_var\s*\(\s*["'].*?\$|\$wpdb->get_row\s*\(\s*["'].*?\$|\$wpdb->get_results\s*\(\s*["'].*?\$/,
        message: 'Potential SQL injection - unprepared query method with variable',
        fix: 'Use $wpdb->prepare() for all queries'
      },
      {
        pattern: /password\s*=\s*['"][^'"]+['"]|api_key\s*=\s*['"][^'"]+['"]|secret\s*=\s*['"][^'"]+['"]/,
        message: 'Hardcoded credentials detected',
        fix: 'Use environment variables or wp-config.php constants'
      },
      {
        pattern: /update_option\s*\(\s*[^,]+,\s*\$_(GET|POST|REQUEST)\[/,
        message: 'Direct storage of user input without sanitization',
        fix: 'Sanitize input before storing with update_option()'
      },
      {
        pattern: /update_post_meta\s*\([^,]+,\s*[^,]+,\s*\$_(GET|POST|REQUEST)\[/,
        message: 'Direct storage of user input in post meta without sanitization',
        fix: 'Sanitize input before storing with update_post_meta()'
      }
    ],
    
    // Medium severity issues
    medium: [
      {
        pattern: /extract\s*\(/,
        message: 'extract() can overwrite variables - security risk',
        fix: 'Avoid extract(), use explicit variable assignment'
      },
      {
        pattern: /include\s*\(\s*\$|include_once\s*\(\s*\$|require\s*\(\s*\$|require_once\s*\(\s*\$/,
        message: 'Dynamic include/require with variable (LFI risk)',
        fix: 'Validate and sanitize file paths, use allowed list'
      },
      {
        pattern: /file_get_contents\s*\(\s*\$_/,
        message: 'Reading file from user input (LFI risk)',
        fix: 'Validate and sanitize file paths'
      },
      {
        pattern: /wp_redirect\s*\(\s*\$_/,
        message: 'Redirect with user input (open redirect risk)',
        fix: 'Validate URL with wp_validate_redirect()'
      }
    ],
    
    // Low severity issues
    low: [
      {
        pattern: /@\$wpdb->|@mysql_|@unlink|@file_/,
        message: 'Error suppression with @ - errors may go unnoticed',
        fix: 'Handle errors properly instead of suppressing'
      },
      {
        pattern: /chmod\s*\(\s*[^,]+,\s*0777/,
        message: 'Overly permissive file permissions (777)',
        fix: 'Use more restrictive permissions (644 for files, 755 for dirs)'
      }
    ]
  },
  
  /**
   * Execute the hook
   * @param {Object} context - Hook context
   * @returns {Promise<Object>} Hook result
   */
  async execute(context) {
    const { event, file, content } = context;
    
    if (!file || !content) {
      return {
        success: true,
        message: 'No file or content to check'
      };
    }
    
    // Only check PHP files
    if (!file.endsWith('.php')) {
      return {
        success: true,
        message: 'Not a PHP file, skipping security check'
      };
    }
    
    const issues = [];
    const lines = content.split('\n');
    
    // Run all security checks
    Object.entries(this.checks).forEach(([severity, checks]) => {
      checks.forEach(check => {
        lines.forEach((line, index) => {
          // Skip comments
          const trimmed = line.trim();
          if (trimmed.startsWith('//') || trimmed.startsWith('#') || trimmed.startsWith('*')) {
            return;
          }
          
          if (check.pattern.test(line)) {
            issues.push({
              severity: severity,
              line: index + 1,
              code: line.trim().substring(0, 100),
              message: check.message,
              fix: check.fix
            });
          }
        });
      });
    });
    
    // Remove duplicate issues (same line, same pattern)
    const uniqueIssues = issues.filter((issue, index, self) =>
      index === self.findIndex(i => 
        i.line === issue.line && i.message === issue.message
      )
    );
    
    // Determine result based on severity
    const hasCritical = uniqueIssues.some(i => i.severity === 'critical');
    const hasHigh = uniqueIssues.some(i => i.severity === 'high');
    
    // Group by severity
    const grouped = {
      critical: uniqueIssues.filter(i => i.severity === 'critical'),
      high: uniqueIssues.filter(i => i.severity === 'high'),
      medium: uniqueIssues.filter(i => i.severity === 'medium'),
      low: uniqueIssues.filter(i => i.severity === 'low')
    };
    
    // Result
    const result = {
      success: !hasCritical, // Fail if critical issues found
      message: uniqueIssues.length === 0 
        ? 'No security issues detected'
        : `Found ${uniqueIssues.length} security issue(s)`,
      issues: uniqueIssues,
      summary: {
        critical: grouped.critical.length,
        high: grouped.high.length,
        medium: grouped.medium.length,
        low: grouped.low.length
      },
      details: {
        file: file,
        total: uniqueIssues.length
      }
    };
    
    return result;
  },
  
  /**
   * Format message for display
   * @param {Object} result - Hook result
   * @returns {string} Formatted message
   */
  formatMessage(result) {
    if (result.issues.length === 0) {
      return `✓ ${result.message}`;
    }
    
    let message = result.success ? `⚠ ${result.message}\n` : `✗ ${result.message}\n`;
    
    // Group by severity
    const severities = ['critical', 'high', 'medium', 'low'];
    const icons = {
      critical: '🚨',
      high: '⚠️',
      medium: 'ℹ️',
      low: 'ℹ️'
    };
    
    severities.forEach(severity => {
      const issues = result.issues.filter(i => i.severity === severity);
      if (issues.length > 0) {
        message += `\n${icons[severity]} ${severity.toUpperCase()} (${issues.length}):\n`;
        issues.slice(0, 3).forEach(issue => {
          message += `  Line ${issue.line}: ${issue.message}\n`;
        });
        if (issues.length > 3) {
          message += `  ... and ${issues.length - 3} more\n`;
        }
      }
    });
    
    return message;
  }
};