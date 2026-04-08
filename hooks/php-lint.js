/**
 * PHP Lint Hook - Syntax check for PHP files
 * 
 * Runs PHP lint on edited PHP files to catch syntax errors early.
 * Profile: standard
 */

module.exports = {
  name: 'php-lint',
  
  // Hook configuration
  config: {
    profile: 'standard',
    timeout: 10000, // 10 seconds max
  },
  
  /**
   * Execute the hook
   * @param {Object} context - Hook context
   * @param {string} context.event - Event name (e.g., 'file.edited')
   * @param {string} context.file - File path that was edited
   * @param {string} context.content - File content (if available)
   * @returns {Promise<Object>} Hook result
   */
  async execute(context) {
    const { event, file } = context;
    
    // Only check PHP files
    if (!file || !file.endsWith('.php')) {
      return {
        success: true,
        message: 'Not a PHP file, skipping lint'
      };
    }
    
    // Check if PHP is available
    const { execSync } = require('child_process');
    
    try {
      // Check if PHP command exists
      execSync('which php', { stdio: 'ignore' });
    } catch (error) {
      return {
        success: true,
        message: 'PHP not found, skipping lint'
      };
    }
    
    try {
      // Run PHP lint
      const result = execSync(`php -l "${file}"`, {
        encoding: 'utf-8',
        timeout: this.config.timeout,
      });
      
      // PHP lint returns "No syntax errors detected" on success
      if (result.includes('No syntax errors detected')) {
        return {
          success: true,
          message: 'PHP syntax OK'
        };
      }
      
      // Unexpected output
      return {
        success: true,
        message: result.trim()
      };
      
    } catch (error) {
      // PHP lint failed - syntax error found
      const errorMessage = error.stdout || error.stderr || error.message;
      
      // Parse error message
      const errorMatch = errorMessage.match(/Parse error:\s*(.+?)\s+in\s+.+?\s+on\s+line\s+(\d+)/);
      
      if (errorMatch) {
        return {
          success: false,
          message: `PHP Syntax Error: ${errorMatch[1]} on line ${errorMatch[2]}`,
          error: errorMessage,
          details: {
            file: file,
            line: parseInt(errorMatch[2]),
            description: errorMatch[1]
          }
        };
      }
      
      // Generic error
      return {
        success: false,
        message: 'PHP lint failed',
        error: errorMessage
      };
    }
  },
  
  /**
   * Format error message for display
   * @param {Object} result - Hook result
   * @returns {string} Formatted message
   */
  formatMessage(result) {
    if (result.success) {
      return `✓ ${result.message}`;
    }
    
    let message = `✗ ${result.message}`;
    
    if (result.details) {
      message += `\n  File: ${result.details.file}`;
      message += `\n  Line: ${result.details.line}`;
    }
    
    return message;
  }
};