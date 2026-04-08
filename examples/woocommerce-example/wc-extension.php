<?php
/**
 * Plugin Name:       OpenCode WooCommerce Extension
 * Plugin URI:        https://example.com/plugins/opencode-wc-extension/
 * Description:       WooCommerce extension demonstrating best practices for WooCommerce development including custom product types, payment gateways, and order handling.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 * Author:            OpenCode
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       opencode-wc-extension
 * Domain Path:       /languages
 *
 * @package Opencode_WC_Extension
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OPENCODE_WC_VERSION', '1.0.0' );
define( 'OPENCODE_WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPENCODE_WC_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENCODE_WC_BASENAME', plugin_basename( __FILE__ ) );

function opencode_wc_check_woocommerce() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>' . esc_html__( 'OpenCode WooCommerce Extension requires WooCommerce to be installed and active.', 'opencode-wc-extension' ) . '</strong></p></div>';
        });
        return false;
    }
    return true;
}

function opencode_wc_init() {
    if ( ! opencode_wc_check_woocommerce() ) {
        return;
    }

    require_once OPENCODE_WC_PATH . 'includes/class-main.php';
    require_once OPENCODE_WC_PATH . 'includes/class-gateway.php';
    require_once OPENCODE_WC_PATH . 'includes/class-product-type.php';
    require_once OPENCODE_WC_PATH . 'includes/class-order.php';

    $main = new \OpenCode_WC_Extension\Main();
    $main->init();

    $product_type = new \OpenCode_WC_Extension\Product_Type();
    $product_type->init();

    $gateway = new \OpenCode_WC_Extension\Gateway();
    $gateway->init();

    $order_handler = new \OpenCode_WC_Extension\Order_Handler();
    $order_handler->init();
}
add_action( 'plugins_loaded', 'opencode_wc_init' );

function opencode_wc_activate() {
    if ( ! opencode_wc_check_woocommerce() ) {
        deactivate_plugins( OPENCODE_WC_BASENAME );
        wp_die( esc_html__( 'OpenCode WooCommerce Extension requires WooCommerce to be installed and active.', 'opencode-wc-extension' ) );
    }

    if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '6.0', '<' ) ) {
        deactivate_plugins( OPENCODE_WC_BASENAME );
        wp_die( esc_html__( 'OpenCode WooCommerce Extension requires WooCommerce version 6.0 or higher.', 'opencode-wc-extension' ) );
    }

    add_option( 'opencode_wc_version', OPENCODE_WC_VERSION );
}
register_activation_hook( __FILE__, 'opencode_wc_activate' );