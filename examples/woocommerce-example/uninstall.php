<?php
/**
 * WooCommerce Extension Uninstall Script
 *
 * @package Opencode_WC_Extension
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Delete plugin options
$wpdb->delete(
    $wpdb->options,
    array( 'option_name' => 'woocommerce_opencode_custom_gateway_settings' ),
    array( '%s' )
);

// Delete custom product type posts
$custom_products = get_posts( array(
    'post_type'   => 'product',
    'meta_key'    => '_opencode_product_type',
    'meta_value'  => 'opencode_custom',
    'numberposts' => -1,
    'post_status' => 'any',
) );

foreach ( $custom_products as $product ) {
    // Delete product meta
    $wpdb->delete(
        $wpdb->postmeta,
        array( 'post_id' => $product->ID ),
        array( '%d' )
    );

    // Note: We don't delete the actual product posts as they may be user content
    // Only remove the custom meta data
}

// Delete order meta related to this gateway
$wpdb->query(
    "DELETE FROM {$wpdb->postmeta} 
    WHERE meta_key LIKE '_opencode_wc_%'"
);

// Delete any transients
$wpdb->query(
    "DELETE FROM {$wpdb->options} 
    WHERE option_name LIKE '_transient_opencode_wc_%' 
    OR option_name LIKE '_transient_timeout_opencode_wc_%'"
);

// Clear any cached data
wp_cache_flush();