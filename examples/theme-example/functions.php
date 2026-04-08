<?php
/**
 * Theme functions and definitions
 *
 * @package Opencode_Theme_Example
 * @version 1.0.0
 */

if ( ! defined( 'OPENCODE_THEME_VERSION' ) ) {
    define( 'OPENCODE_THEME_VERSION', '1.0.0' );
}

if ( ! function_exists( 'opencode_theme_setup' ) ) :
function opencode_theme_setup() {
    load_theme_textdomain( 'opencode-theme-example', get_template_directory() . '/languages' );
    
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    
    // Add editor style support
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );
    
    // Add wide and full alignment support
    add_theme_support( 'align-wide' );
    
    // Add block styles support
    add_theme_support( 'wp-block-styles' );
    
    // Add responsive embeds support
    add_theme_support( 'responsive-embeds' );
    
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'opencode-theme-example' ),
        'footer'  => __( 'Footer Menu', 'opencode-theme-example' ),
    ) );
}
endif;
add_action( 'after_setup_theme', 'opencode_theme_setup' );

function opencode_theme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'opencode-theme-example' ),
        'id'            => 'sidebar-1',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'opencode_theme_widgets_init' );

function opencode_theme_scripts() {
    wp_enqueue_style( 'opencode-theme-style', get_stylesheet_uri(), array(), OPENCODE_THEME_VERSION );
    wp_enqueue_style( 'opencode-theme-main', get_template_directory_uri() . '/assets/css/style.css', array(), OPENCODE_THEME_VERSION );
    wp_enqueue_script( 'opencode-theme-main', get_template_directory_uri() . '/assets/js/main.js', array(), OPENCODE_THEME_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'opencode_theme_scripts' );

// Include customizer settings
require_once get_template_directory() . '/inc/customizer.php';
