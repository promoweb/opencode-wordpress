/**
 * WordPress Debug Check Hook
 * 
 * Checks for WP_DEBUG usage and potential debug code left in production.
 * Profile: standard
 */

module.exports = {
  name: 'wp-debug-check',
  
  config: {
    profile: 'standard',
    checkPatterns: [
      /var_dump\s*\(/,
      /print_r\s*\(/,
      /echo\s+['"]DEBUG:/,
      /error_log\s*\(/,
      /wp_die\s*\(/,
      /die\s*\(/,
      /exit\s*\(/,
      /console\.log\s*\(/,
      /dd\s*\(/, // Laravel dump function
    ],
    excludePatterns: [
      /vendor\//,
      /node_modules\//,
      /\.min\.js$/,
      /\.min\.css$/,
    ]
  },
  
  /**
   * Execute the hook
   * @param {Object} context - Hook context
   * @param {string} context.event - Event name
   * @param {string} context.file - File path
   * @param {string} context.content - File content
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
    
    // Skip excluded patterns
    if (this.config.excludePatterns.some(pattern => pattern.test(file))) {
      return {
        success: true,
        message: 'File excluded from debug check'
      };
    }
    
    // Only check PHP and JS files
    const isPHP = file.endsWith('.php');
    const isJS = file.endsWith('.js');
    
    if (!isPHP && !isJS) {
      return {
        success: true,
        message: 'Not a PHP or JS file, skipping debug check'
      };
    }
    
    const warnings = [];
    const lines = content.split('\n');
    
    // Check each line
    lines.forEach((line, index) => {
      // Skip comments (basic check)
      const trimmed = line.trim();
      if (trimmed.startsWith('//') || trimmed.startsWith('#') || trimmed.startsWith('*')) {
        return;
      }
      
      // Check for debug patterns
      this.config.checkPatterns.forEach(pattern => {
        if (pattern.test(line)) {
          const lineNumber = index + 1;
          const match = line.match(pattern);
          
          warnings.push({
            line: lineNumber,
            code: line.trim().substring(0, 100),
            pattern: pattern.toString(),
            message: this.getWarningMessage(pattern)
          });
        }
      });
    });
    
    // Check for WP_DEBUG in wp-config.php
    if (file.includes('wp-config.php') || file.includes('wp-config-sample.php')) {
      const hasWPDebugTrue = /define\s*\(\s*['"]WP_DEBUG['"]\s*,\s*true\s*\)/.test(content);
      const hasWPDebugDisplayTrue = /define\s*\(\s*['"]WP_DEBUG_DISPLAY['"]\s*,\s*true\s*\)/.test(content);
      
      if (hasWPDebugTrue && hasWPDebugDisplayTrue) {
        warnings.push({
          line: null,
          message: 'WP_DEBUG and WP_DEBUG_DISPLAY are both true - should be disabled in production'
        });
      }
    }
    
    // Return result
    if (warnings.length === 0) {
      return {
        success: true,
        message: 'No debug code detected'
      };
    }
    
    return {
      success: true, // Warning, not error
      message: `Found ${warnings.length} debug statement(s)`,
      warnings: warnings,
      details: {
        file: file,
        count: warnings.length
      }
    };
  },
  
  /**
   * Get warning message for pattern
   * @param {RegExp} pattern - Matched pattern
   * @returns {string} Warning message
   */
  getWarningMessage(pattern) {
    const patternStr = pattern.toString();
    
    if (patternStr.includes('var_dump')) {
      return 'var_dump() found - remove before production';
    }
    if (patternStr.includes('print_r')) {
      return 'print_r() found - remove before production';
    }
    if (patternStr.includes('error_log')) {
      return 'error_log() found - consider using WP_DEBUG_LOG instead';
    }
    if (patternStr.includes('wp_die')) {
      return 'wp_die() found - for debugging only';
    }
    if (patternStr.includes('console.log')) {
      return 'console.log() found - remove before production';
    }
    if (patternStr.includes('dd(')) {
      return 'dd() dump function found - remove before production';
    }
    
    return 'Debug code found - review before production';
  },
  
  /**
   * Format message for display
   * @param {Object} result - Hook result
   * @returns {string} Formatted message
   */
  formatMessage(result) {
    if (!result.warnings || result.warnings.length === 0) {
      return `✓ ${result.message}`;
    }
    
    let message = `⚠ ${result.message}\n`;
    
    result.warnings.slice(0, 5).forEach(warning => {
      if (warning.line) {
        message += `  Line ${warning.line}: ${warning.message}\n`;
      } else {
        message += `  ${warning.message}\n`;
      }
    });
    
    if (result.warnings.length > 5) {
      message += `  ... and ${result.warnings.length - 5} more`;
    }
    
    return message;
  }
};