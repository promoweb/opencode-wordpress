<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Opencode_Theme_Example
 */

function opencode_theme_body_classes( $classes ) {
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    if ( is_front_page() && is_home() ) {
        $classes[] = 'home';
    }

    if ( is_singular() ) {
        $classes[] = 'singular';
    }

    $sidebar_position = get_theme_mod( 'sidebar_position', 'right' );
    if ( is_active_sidebar( 'sidebar-1' ) && $sidebar_position ) {
        $classes[] = 'sidebar-position-' . $sidebar_position;
    }

    if ( ! has_post_thumbnail() ) {
        $classes[] = 'no-thumbnail';
    }

    return $classes;
}
add_filter( 'body_class', 'opencode_theme_body_classes' );

function opencode_theme_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'opencode_theme_pingback_header' );

function opencode_theme_archive_title( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    }

    return $title;
}
add_filter( 'get_the_archive_title', 'opencode_theme_archive_title' );

function opencode_theme_excerpt_more( $more ) {
    if ( ! is_admin() ) {
        global $post;
        $more = sprintf(
            ' ... <a class="read-more" href="%1$s">%2$s</a>',
            get_permalink( $post->ID ),
            __( 'Read More', 'opencode-theme-example' )
        );
    }
    return $more;
}
add_filter( 'excerpt_more', 'opencode_theme_excerpt_more' );

function opencode_theme_read_more_link( $link ) {
    if ( ! is_admin() ) {
        $link = sprintf(
            '<a class="more-link" href="%1$s">%2$s</a>',
            get_permalink(),
            __( 'Continue Reading', 'opencode-theme-example' )
        );
    }
    return $link;
}
add_filter( 'the_content_more_link', 'opencode_theme_read_more_link' );

function opencode_theme_pre_get_posts( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        if ( is_home() ) {
            $query->set( 'posts_per_page', get_theme_mod( 'home_posts_per_page', 10 ) );
        }

        if ( is_archive() ) {
            $query->set( 'posts_per_page', get_theme_mod( 'archive_posts_per_page', 10 ) );
        }
    }
}
add_action( 'pre_get_posts', 'opencode_theme_pre_get_posts' );

function opencode_theme_custom_excerpt_length( $length ) {
    if ( is_admin() ) {
        return $length;
    }

    if ( is_front_page() && is_home() ) {
        return get_theme_mod( 'home_excerpt_length', 55 );
    }

    if ( is_archive() ) {
        return get_theme_mod( 'archive_excerpt_length', 35 );
    }

    return 55;
}
add_filter( 'excerpt_length', 'opencode_theme_custom_excerpt_length', 999 );

function opencode_theme_image_sizes( $sizes, $size, $image_src, $image_meta ) {
    if ( is_front_page() || is_home() ) {
        $sizes = '(max-width: 768px) 100vw, 50vw';
    } elseif ( is_singular() ) {
        $sizes = '(max-width: 768px) 100vw, 80vw';
    } else {
        $sizes = '(max-width: 768px) 100vw, 33vw';
    }

    return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'opencode_theme_image_sizes', 10, 4 );

function opencode_theme_add_async_script( $tag, $handle, $src ) {
    $async_scripts = array( 'opencode-theme-main' );

    if ( in_array( $handle, $async_scripts ) ) {
        return str_replace( ' src', ' async src', $tag );
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'opencode_theme_add_async_script', 10, 3 );

function opencode_theme_customize_partial_blogname() {
    bloginfo( 'name' );
}

function opencode_theme_customize_partial_blogdescription() {
    bloginfo( 'description' );
}