<?php
/**
 * Admin functionality
 *
 * @package Opencode_Plugin_Example
 */

namespace OpenCode_Plugin_Example;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Admin {

    /**
     * Initialize admin functionality.
     *
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_filter( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Add admin menu pages.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_admin_menu(): void {
        add_menu_page(
            __( 'OpenCode Plugin', 'opencode-plugin-example' ),
            __( 'OpenCode', 'opencode-plugin-example' ),
            'manage_options',
            'opencode-plugin',
            array( $this, 'admin_page' ),
            'dashicons-admin-generic',
            30
        );

        add_submenu_page(
            'opencode-plugin',
            __( 'Settings', 'opencode-plugin-example' ),
            __( 'Settings', 'opencode-plugin-example' ),
            'manage_options',
            'opencode-plugin-settings',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Render admin page.
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'opencode-plugin-example' ) );
        }

        include OPENCODE_PLUGIN_PATH . 'admin/views/admin-page.php';
    }

    /**
     * Render settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'opencode-plugin-example' ) );
        }

        include OPENCODE_PLUGIN_PATH . 'admin/views/settings-page.php';
    }

    /**
     * Admin init callback.
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_init(): void {
        if ( isset( $_GET['opencode_plugin_dismiss_notice'] ) ) {
            check_admin_referer( 'opencode_plugin_dismiss_notice' );
            update_user_meta( get_current_user_id(), 'opencode_plugin_dismiss_notice', true );
            wp_safe_redirect( admin_url() );
            exit;
        }
    }

    /**
     * Add custom post type count to dashboard glance items.
     *
     * @since 1.0.0
     * @param array<string> $items Existing glance items.
     * @return array<string> Modified glance items.
     */
    public function dashboard_glance_items( array $items ): array {
        $count = wp_count_posts( 'opencode_item' );

        if ( $count && $count->publish > 0 ) {
            $items[] = sprintf(
                '<a href="%s" class="opencode-item-count">%s %s</a>',
                admin_url( 'edit.php?post_type=opencode_item' ),
                number_format_i18n( $count->publish ),
                _n( 'OpenCode Item', 'OpenCode Items', $count->publish, 'opencode-plugin-example' )
            );
        }

        return $items;
    }

    /**
     * Display admin notices.
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_notices(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $dismissed = get_user_meta( get_current_user_id(), 'opencode_plugin_dismiss_notice', true );

        if ( ! $dismissed ) {
            $message = __( 'Welcome to OpenCode Plugin! Visit the settings page to configure.', 'opencode-plugin-example' );
            $dismiss_url = wp_nonce_url( add_query_arg( 'opencode_plugin_dismiss_notice', '1' ), 'opencode_plugin_dismiss_notice' );

            printf(
                '<div class="notice notice-info is-dismissible"><p>%s <a href="%s">%s</a> | <a href="%s">%s</a></p></div>',
                esc_html( $message ),
                esc_url( admin_url( 'admin.php?page=opencode-plugin-settings' ) ),
                esc_html__( 'Configure', 'opencode-plugin-example' ),
                esc_url( $dismiss_url ),
                esc_html__( 'Dismiss', 'opencode-plugin-example' )
            );
        }
    }

    // Note: Meta box handlers are in class-cpt.php to avoid duplication
}
