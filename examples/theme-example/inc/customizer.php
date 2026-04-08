<?php
/**
 * Theme Customizer Settings
 *
 * @package Opencode_Theme_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register theme customizer settings
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function opencode_theme_customize_register( $wp_customize ) {
    // Theme Options Section
    $wp_customize->add_section( 'opencode_theme_options', array(
        'title'       => __( 'Theme Options', 'opencode-theme-example' ),
        'description' => __( 'Customize your theme settings.', 'opencode-theme-example' ),
        'priority'    => 30,
    ) );

    // Sidebar Position
    $wp_customize->add_setting( 'sidebar_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'opencode_theme_sanitize_sidebar_position',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'sidebar_position', array(
        'label'       => __( 'Sidebar Position', 'opencode-theme-example' ),
        'section'     => 'opencode_theme_options',
        'type'        => 'select',
        'choices'     => array(
            'left'  => __( 'Left', 'opencode-theme-example' ),
            'right' => __( 'Right', 'opencode-theme-example' ),
            'none'  => __( 'No Sidebar', 'opencode-theme-example' ),
        ),
    ) );

    // Home Posts Per Page
    $wp_customize->add_setting( 'home_posts_per_page', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'home_posts_per_page', array(
        'label'       => __( 'Home Posts Per Page', 'opencode-theme-example' ),
        'description' => __( 'Number of posts to display on the homepage.', 'opencode-theme-example' ),
        'section'     => 'opencode_theme_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 20,
        ),
    ) );

    // Archive Posts Per Page
    $wp_customize->add_setting( 'archive_posts_per_page', array(
        'default'           => 10,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'archive_posts_per_page', array(
        'label'       => __( 'Archive Posts Per Page', 'opencode-theme-example' ),
        'description' => __( 'Number of posts to display on archive pages.', 'opencode-theme-example' ),
        'section'     => 'opencode_theme_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 50,
        ),
    ) );

    // Home Excerpt Length
    $wp_customize->add_setting( 'home_excerpt_length', array(
        'default'           => 55,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'home_excerpt_length', array(
        'label'       => __( 'Excerpt Length', 'opencode-theme-example' ),
        'description' => __( 'Number of words in post excerpts.', 'opencode-theme-example' ),
        'section'     => 'opencode_theme_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 10,
            'max' => 100,
        ),
    ) );

    // Show Author Info
    $wp_customize->add_setting( 'show_author_info', array(
        'default'           => true,
        'sanitize_callback' => 'opencode_theme_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'show_author_info', array(
        'label'   => __( 'Show Author Info on Posts', 'opencode-theme-example' ),
        'section' => 'opencode_theme_options',
        'type'    => 'checkbox',
    ) );

    // Show Post Date
    $wp_customize->add_setting( 'show_post_date', array(
        'default'           => true,
        'sanitize_callback' => 'opencode_theme_sanitize_checkbox',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'show_post_date', array(
        'label'   => __( 'Show Post Date', 'opencode-theme-example' ),
        'section' => 'opencode_theme_options',
        'type'    => 'checkbox',
    ) );

    // Colors Section
    $wp_customize->add_section( 'opencode_theme_colors', array(
        'title'       => __( 'Theme Colors', 'opencode-theme-example' ),
        'description' => __( 'Customize theme colors.', 'opencode-theme-example' ),
        'priority'    => 40,
    ) );

    // Primary Color
    $wp_customize->add_setting( 'primary_color', array(
        'default'           => '#0073aa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'primary_color', array(
        'label'   => __( 'Primary Color', 'opencode-theme-example' ),
        'section' => 'opencode_theme_colors',
    ) ) );

    // Accent Color
    $wp_customize->add_setting( 'accent_color', array(
        'default'           => '#96588a',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
        'label'   => __( 'Accent Color', 'opencode-theme-example' ),
        'section' => 'opencode_theme_colors',
    ) ) );

    // Link Color
    $wp_customize->add_setting( 'link_color', array(
        'default'           => '#0073aa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
        'label'   => __( 'Link Color', 'opencode-theme-example' ),
        'section' => 'opencode_theme_colors',
    ) ) );
}
add_action( 'customize_register', 'opencode_theme_customize_register' );

/**
 * Sanitize sidebar position value
 *
 * @param string $value Input value.
 * @return string Sanitized value.
 */
function opencode_theme_sanitize_sidebar_position( $value ) {
    $valid = array( 'left', 'right', 'none' );
    return in_array( $value, $valid, true ) ? $value : 'right';
}

/**
 * Sanitize checkbox value
 *
 * @param bool $checked Input value.
 * @return bool Sanitized value.
 */
function opencode_theme_sanitize_checkbox( $checked ) {
    return ( bool ) $checked;
}

/**
 * Output customizer CSS
 */
function opencode_theme_customizer_css() {
    // Get and validate color values
    $primary_color = sanitize_hex_color( get_theme_mod( 'primary_color', '#0073aa' ) );
    $accent_color  = sanitize_hex_color( get_theme_mod( 'accent_color', '#96588a' ) );
    $link_color    = sanitize_hex_color( get_theme_mod( 'link_color', '#0073aa' ) );

    // Fallback to defaults if validation fails
    $primary_color = $primary_color ? $primary_color : '#0073aa';
    $accent_color  = $accent_color ? $accent_color : '#96588a';
    $link_color    = $link_color ? $link_color : '#0073aa';
    ?>
    <style type="text/css" id="opencode-theme-customizer-css">
        :root {
            --primary-color: <?php echo esc_attr( $primary_color ); ?>;
            --accent-color: <?php echo esc_attr( $accent_color ); ?>;
            --link-color: <?php echo esc_attr( $link_color ); ?>;
        }
        a { color: <?php echo esc_attr( $link_color ); ?>; }
        .site-header { background-color: <?php echo esc_attr( $primary_color ); ?>; }
        .button, .wp-block-button__link { background-color: <?php echo esc_attr( $accent_color ); ?>; }
        .entry-title a:hover { color: <?php echo esc_attr( $accent_color ); ?>; }
    </style>
    <?php
}
add_action( 'wp_head', 'opencode_theme_customizer_css' );

/**
 * Enqueue customizer preview script
 */
function opencode_theme_customize_preview_js() {
    wp_enqueue_script(
        'opencode-theme-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array( 'customize-preview' ),
        OPENCODE_THEME_VERSION,
        true
    );
}
add_action( 'customize_preview_init', 'opencode_theme_customize_preview_js' );