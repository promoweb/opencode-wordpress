<?php
/**
 * Settings page view for OpenCode Plugin Example
 *
 * @package Opencode_Plugin_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'opencode_plugin_settings' );
        do_settings_sections( 'opencode_plugin_settings' );
        submit_button( __( 'Save Settings', 'opencode-plugin-example' ) );
        ?>
    </form>

    <div class="opencode-settings-info">
        <h2><?php esc_html_e( 'Settings Information', 'opencode-plugin-example' ); ?></h2>
        
        <table class="form-table">
            <tr>
                <th><?php esc_html_e( 'Enable Feature', 'opencode-plugin-example' ); ?></th>
                <td>
                    <p class="description">
                        <?php esc_html_e( 'Toggle the main plugin functionality on or off. When disabled, custom post types and taxonomies remain registered but frontend display is suppressed.', 'opencode-plugin-example' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Display Mode', 'opencode-plugin-example' ); ?></th>
                <td>
                    <p class="description">
                        <?php esc_html_e( 'Choose how items are displayed on the frontend: grid layout, list view, or minimal display.', 'opencode-plugin-example' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Items Per Page', 'opencode-plugin-example' ); ?></th>
                <td>
                    <p class="description">
                        <?php esc_html_e( 'Number of items to show per page on archive views. Default is 10.', 'opencode-plugin-example' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Custom CSS', 'opencode-plugin-example' ); ?></th>
                <td>
                    <p class="description">
                        <?php esc_html_e( 'Add custom CSS to override plugin styles. This CSS is loaded after the main plugin stylesheet.', 'opencode-plugin-example' ); ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</div>