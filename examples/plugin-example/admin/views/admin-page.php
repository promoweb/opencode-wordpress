<?php
/**
 * Admin page view for OpenCode Plugin Example
 *
 * @package Opencode_Plugin_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <div class="opencode-plugin-admin-content">
        <div class="opencode-plugin-overview">
            <h2><?php esc_html_e( 'Plugin Overview', 'opencode-plugin-example' ); ?></h2>
            
            <div class="opencode-plugin-stats">
                <div class="opencode-stat-box">
                    <h3><?php esc_html_e( 'Custom Items', 'opencode-plugin-example' ); ?></h3>
                    <p class="stat-value">
                        <?php
                        $count = wp_count_posts( 'opencode_item' );
                        echo esc_html( number_format_i18n( $count->publish ) );
                        ?>
                    </p>
                </div>
                
                <div class="opencode-stat-box">
                    <h3><?php esc_html_e( 'Plugin Version', 'opencode-plugin-example' ); ?></h3>
                    <p class="stat-value"><?php echo esc_html( OPENCODE_PLUGIN_VERSION ); ?></p>
                </div>
            </div>
        </div>

        <div class="opencode-plugin-actions">
            <h2><?php esc_html_e( 'Quick Actions', 'opencode-plugin-example' ); ?></h2>
            
            <p>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=opencode_item' ) ); ?>" class="button">
                    <?php esc_html_e( 'View All Items', 'opencode-plugin-example' ); ?>
                </a>
                
                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=opencode_item' ) ); ?>" class="button">
                    <?php esc_html_e( 'Add New Item', 'opencode-plugin-example' ); ?>
                </a>
                
                <a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=opencode_category&post_type=opencode_item' ) ); ?>" class="button">
                    <?php esc_html_e( 'Manage Categories', 'opencode-plugin-example' ); ?>
                </a>
                
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=opencode-plugin-settings' ) ); ?>" class="button button-primary">
                    <?php esc_html_e( 'Settings', 'opencode-plugin-example' ); ?>
                </a>
            </p>
        </div>

        <div class="opencode-plugin-documentation">
            <h2><?php esc_html_e( 'Documentation', 'opencode-plugin-example' ); ?></h2>
            
            <p><?php esc_html_e( 'This plugin demonstrates WordPress best practices including:', 'opencode-plugin-example' ); ?></p>
            
            <ul>
                <li><?php esc_html_e( 'Custom Post Type registration with proper labels and capabilities', 'opencode-plugin-example' ); ?></li>
                <li><?php esc_html_e( 'Custom Taxonomy with hierarchical structure', 'opencode-plugin-example' ); ?></li>
                <li><?php esc_html_e( 'Settings API implementation', 'opencode-plugin-example' ); ?></li>
                <li><?php esc_html_e( 'Meta box handling with nonce verification', 'opencode-plugin-example' ); ?></li>
                <li><?php esc_html_e( 'Proper sanitization and escaping', 'opencode-plugin-example' ); ?></li>
                <li><?php esc_html_e( 'Admin and frontend asset enqueueing', 'opencode-plugin-example' ); ?></li>
            </ul>
        </div>
    </div>
</div>