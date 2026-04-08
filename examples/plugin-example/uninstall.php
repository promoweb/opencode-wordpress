<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Opencode_Plugin_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$option_names = array(
    'opencode_plugin_version',
    'opencode_plugin_settings',
    'opencode_plugin_options',
);

foreach ( $option_names as $option ) {
    delete_option( $option );
    delete_site_option( $option );
}

$args = array(
    'post_type'      => 'opencode_item',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'post_status'    => 'any',
);

$posts = get_posts( $args );

foreach ( $posts as $post_id ) {
    wp_delete_post( $post_id, true );
}

$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = 'opencode_item')" );

$table_name = $wpdb->prefix . 'opencode_custom_table';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

wp_clear_scheduled_hook( 'opencode_plugin_daily_cleanup' );

// Clear any transients set by the plugin
for ( $i = 1; $i <= 10; $i++ ) {
    delete_transient( 'opencode_plugin_transient_' . $i );
}

// Clear any user meta stored by the plugin
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
        $wpdb->esc_like( 'opencode_plugin_' ) . '%'
    )
);
