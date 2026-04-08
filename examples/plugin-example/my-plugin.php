<?php
/**
 * Plugin Name:       OpenCode Plugin Example
 * Plugin URI:        https://example.com/plugins/opencode-plugin-example/
 * Description:       A comprehensive example WordPress plugin demonstrating best practices and modern development patterns.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            OpenCode
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       opencode-plugin-example
 * Domain Path:       /languages
 *
 * @package Opencode_Plugin_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OPENCODE_PLUGIN_VERSION', '1.0.0' );
define( 'OPENCODE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPENCODE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENCODE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once OPENCODE_PLUGIN_PATH . 'includes/class-plugin.php';
require_once OPENCODE_PLUGIN_PATH . 'includes/class-admin.php';
require_once OPENCODE_PLUGIN_PATH . 'includes/class-settings.php';
require_once OPENCODE_PLUGIN_PATH . 'includes/class-cpt.php';

function opencode_plugin_init() {
    $plugin = new OpenCode_Plugin_Example\Plugin();
    $plugin->init();
    
    if ( is_admin() ) {
        $admin = new OpenCode_Plugin_Example\Admin();
        $admin->init();
        
        $settings = new OpenCode_Plugin_Example\Settings();
        $settings->init();
    }
    
    $cpt = new OpenCode_Plugin_Example\Custom_Post_Type();
    $cpt->init();
}
add_action( 'plugins_loaded', 'opencode_plugin_init' );

function opencode_plugin_activate() {
    require_once OPENCODE_PLUGIN_PATH . 'includes/class-cpt.php';
    
    $cpt = new OpenCode_Plugin_Example\Custom_Post_Type();
    $cpt->register_post_type();
    
    flush_rewrite_rules();
    
    add_option( 'opencode_plugin_version', OPENCODE_PLUGIN_VERSION );
}
register_activation_hook( __FILE__, 'opencode_plugin_activate' );

function opencode_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'opencode_plugin_deactivate' );