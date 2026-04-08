<?php
/**
 * Main plugin class
 *
 * @package Opencode_Plugin_Example
 */

namespace OpenCode_Plugin_Example;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Plugin {

    /**
     * Plugin version.
     *
     * @since 1.0.0
     * @var string
     */
    protected string $version;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->version = OPENCODE_PLUGIN_VERSION;
    }

    /**
     * Initialize the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        $this->load_textdomain();
        $this->init_hooks();
    }

    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     * @return void
     */
    protected function load_textdomain(): void {
        load_plugin_textdomain(
            'opencode-plugin-example',
            false,
            dirname( OPENCODE_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     * @return void
     */
    protected function init_hooks(): void {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        
        add_filter( 'plugin_action_links_' . OPENCODE_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
        
        add_action( 'plugins_loaded', array( $this, 'check_version' ) );
    }

    /**
     * Enqueue frontend scripts and styles.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_scripts(): void {
        wp_enqueue_style(
            'opencode-plugin-frontend',
            OPENCODE_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            'opencode-plugin-frontend',
            OPENCODE_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_localize_script(
            'opencode-plugin-frontend',
            'opencodePluginData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'opencode_plugin_nonce' ),
                'i18n'    => array(
                    'loading'  => __( 'Loading...', 'opencode-plugin-example' ),
                    'error'    => __( 'An error occurred', 'opencode-plugin-example' ),
                    'success'  => __( 'Success!', 'opencode-plugin-example' ),
                ),
            )
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @since 1.0.0
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function admin_enqueue_scripts( string $hook ): void {
        // Only load on plugin admin pages
        $plugin_pages = array(
            'toplevel_page_opencode-plugin',
            'opencode-plugin_page_opencode-plugin-settings',
            'edit.php?post_type=opencode_item',
            'post-new.php?post_type=opencode_item',
            'post.php',
        );

        // Check if we're on a plugin-related page
        $is_plugin_page = false;
        foreach ( $plugin_pages as $page ) {
            if ( false !== strpos( $hook, $page ) || $hook === $page ) {
                $is_plugin_page = true;
                break;
            }
        }

        // Also check for opencode_item post type
        $screen = get_current_screen();
        if ( $screen && 'opencode_item' === $screen->post_type ) {
            $is_plugin_page = true;
        }

        if ( ! $is_plugin_page ) {
            return;
        }

        wp_enqueue_style(
            'opencode-plugin-admin',
            OPENCODE_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            'opencode-plugin-admin',
            OPENCODE_PLUGIN_URL . 'admin/js/admin.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_localize_script(
            'opencode-plugin-admin',
            'opencodePluginAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'opencode_plugin_admin_nonce' ),
            )
        );
    }

    /**
     * Add action links to plugin list.
     *
     * @since 1.0.0
     * @param array<string> $links Existing action links.
     * @return array<string> Modified action links.
     */
    public function add_action_links( array $links ): array {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url( 'options-general.php?page=opencode-plugin-settings' ),
            __( 'Settings', 'opencode-plugin-example' )
        );

        array_unshift( $links, $settings_link );

        return $links;
    }

    /**
     * Check plugin version and run upgrades if needed.
     *
     * @since 1.0.0
     * @return void
     */
    public function check_version(): void {
        $installed_version = get_option( 'opencode_plugin_version' );

        if ( version_compare( $installed_version, $this->version, '<' ) ) {
            $this->upgrade( $installed_version );
            update_option( 'opencode_plugin_version', $this->version );
        }
    }

    /**
     * Run upgrade routines.
     *
     * @since 1.0.0
     * @param string|false $installed_version The installed version.
     * @return void
     */
    protected function upgrade( $installed_version ): void {
        if ( version_compare( $installed_version, '1.1.0', '<' ) ) {
            // Upgrade to version 1.1.0
        }

        if ( version_compare( $installed_version, '1.2.0', '<' ) ) {
            // Upgrade to version 1.2.0
        }
    }

    /**
     * Get plugin version.
     *
     * @since 1.0.0
     * @return string Plugin version.
     */
    public function get_version(): string {
        return $this->version;
    }

    /**
     * Plugin activation callback.
     *
     * @since 1.0.0
     * @return void
     */
    public static function activate(): void {
        if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
            deactivate_plugins( OPENCODE_PLUGIN_BASENAME );
            wp_die(
                __( 'This plugin requires PHP version 7.4 or higher.', 'opencode-plugin-example' ),
                'Plugin Activation Error',
                array( 'back_link' => true )
            );
        }

        if ( version_compare( $GLOBALS['wp_version'], '5.8', '<' ) ) {
            deactivate_plugins( OPENCODE_PLUGIN_BASENAME );
            wp_die(
                __( 'This plugin requires WordPress version 5.8 or higher.', 'opencode-plugin-example' ),
                'Plugin Activation Error',
                array( 'back_link' => true )
            );
        }
    }
}