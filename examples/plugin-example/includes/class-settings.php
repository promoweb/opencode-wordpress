<?php
/**
 * Settings API implementation
 *
 * @package Opencode_Plugin_Example
 */

namespace OpenCode_Plugin_Example;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Settings {

    /**
     * Option name for settings.
     *
     * @since 1.0.0
     * @var string
     */
    private string $option_name = 'opencode_plugin_settings';

    /**
     * Initialize settings functionality.
     *
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
    }

    /**
     * Register settings with WordPress Settings API.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_settings(): void {
        register_setting(
            $this->option_name,
            $this->option_name,
            array( $this, 'sanitize_settings' )
        );

        add_settings_section(
            'opencode_plugin_general',
            __( 'General Settings', 'opencode-plugin-example' ),
            array( $this, 'render_section_general' ),
            $this->option_name
        );

        add_settings_field(
            'enable_feature',
            __( 'Enable Feature', 'opencode-plugin-example' ),
            array( $this, 'render_field_checkbox' ),
            $this->option_name,
            'opencode_plugin_general',
            array(
                'id'          => 'enable_feature',
                'description' => __( 'Enable the main plugin feature.', 'opencode-plugin-example' ),
            )
        );

        add_settings_field(
            'api_key',
            __( 'API Key', 'opencode-plugin-example' ),
            array( $this, 'render_field_text' ),
            $this->option_name,
            'opencode_plugin_general',
            array(
                'id'          => 'api_key',
                'description' => __( 'Enter your API key for external services.', 'opencode-plugin-example' ),
                'type'        => 'password',
            )
        );

        add_settings_field(
            'cache_duration',
            __( 'Cache Duration', 'opencode-plugin-example' ),
            array( $this, 'render_field_select' ),
            $this->option_name,
            'opencode_plugin_general',
            array(
                'id'          => 'cache_duration',
                'description' => __( 'How long to cache API responses.', 'opencode-plugin-example' ),
                'options'     => array(
                    '3600'   => __( '1 Hour', 'opencode-plugin-example' ),
                    '21600'  => __( '6 Hours', 'opencode-plugin-example' ),
                    '43200'  => __( '12 Hours', 'opencode-plugin-example' ),
                    '86400'  => __( '24 Hours', 'opencode-plugin-example' ),
                ),
            )
        );
    }

    /**
     * Sanitize settings input.
     *
     * @since 1.0.0
     * @param array<string, mixed> $input Raw input values.
     * @return array<string, mixed> Sanitized values.
     */
    public function sanitize_settings( array $input ): array {
        $sanitized = array();

        if ( isset( $input['enable_feature'] ) ) {
            $sanitized['enable_feature'] = (bool) $input['enable_feature'];
        }

        if ( isset( $input['api_key'] ) ) {
            $sanitized['api_key'] = sanitize_text_field( $input['api_key'] );
        }

        if ( isset( $input['cache_duration'] ) && in_array( $input['cache_duration'], array( '3600', '21600', '43200', '86400' ), true ) ) {
            $sanitized['cache_duration'] = $input['cache_duration'];
        }

        return $sanitized;
    }

    /**
     * Render general section description.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_section_general(): void {
        printf( '<p>%s</p>', esc_html__( 'Configure general plugin settings.', 'opencode-plugin-example' ) );
    }

    /**
     * Render text field.
     *
     * @since 1.0.0
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_field_text( array $args ): void {
        $options = get_option( $this->option_name );
        $value = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
        $type = isset( $args['type'] ) ? $args['type'] : 'text';

        printf(
            '<input type="%s" id="%s" name="%s[%s]" value="%s" class="regular-text" />',
            esc_attr( $type ),
            esc_attr( $args['id'] ),
            esc_attr( $this->option_name ),
            esc_attr( $args['id'] ),
            esc_attr( $value )
        );

        if ( ! empty( $args['description'] ) ) {
            printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
        }
    }

    /**
     * Render checkbox field.
     *
     * @since 1.0.0
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_field_checkbox( array $args ): void {
        $options = get_option( $this->option_name );
        $checked = isset( $options[ $args['id'] ] ) ? checked( $options[ $args['id'] ], true, false ) : '';

        printf(
            '<label><input type="checkbox" id="%s" name="%s[%s]" value="1" %s /> %s</label>',
            esc_attr( $args['id'] ),
            esc_attr( $this->option_name ),
            esc_attr( $args['id'] ),
            $checked,
            esc_html__( 'Enable', 'opencode-plugin-example' )
        );

        if ( ! empty( $args['description'] ) ) {
            printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
        }
    }

    /**
     * Render select field.
     *
     * @since 1.0.0
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_field_select( array $args ): void {
        $options = get_option( $this->option_name );
        $value = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';

        printf( '<select id="%s" name="%s[%s]">', esc_attr( $args['id'] ), esc_attr( $this->option_name ), esc_attr( $args['id'] ) );

        foreach ( $args['options'] as $option_value => $option_label ) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $option_value ),
                selected( $value, $option_value, false ),
                esc_html( $option_label )
            );
        }

        echo '</select>';

        if ( ! empty( $args['description'] ) ) {
            printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
        }
    }

    /**
     * Add settings page to admin menu.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_settings_page(): void {
        add_options_page(
            __( 'OpenCode Plugin Settings', 'opencode-plugin-example' ),
            __( 'OpenCode Plugin', 'opencode-plugin-example' ),
            'manage_options',
            'opencode-plugin-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Render settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'opencode-plugin-example' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( $this->option_name );
                do_settings_sections( $this->option_name );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Get a specific option value.
     *
     * @since 1.0.0
     * @param string $key     Option key.
     * @param mixed  $default Default value if not set.
     * @return mixed Option value or default.
     */
    public static function get_option( string $key, $default = false ): mixed {
        $options = get_option( 'opencode_plugin_settings' );

        if ( isset( $options[ $key ] ) ) {
            return $options[ $key ];
        }

        return $default;
    }
}