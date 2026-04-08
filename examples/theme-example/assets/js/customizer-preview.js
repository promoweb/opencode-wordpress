/**
 * Theme Customizer Preview Script
 *
 * @package Opencode_Theme_Example
 */

(function( $ ) {
    'use strict';

    // Sidebar Position
    wp.customize( 'sidebar_position', function( value ) {
        value.bind( function( to ) {
            var $body = $( 'body' );
            $body.removeClass( 'sidebar-left sidebar-right sidebar-none' );
            $body.addClass( 'sidebar-' + to );
        });
    });

    // Primary Color
    wp.customize( 'primary_color', function( value ) {
        value.bind( function( to ) {
            $( '.site-header' ).css( 'background-color', to );
            document.documentElement.style.setProperty( '--primary-color', to );
        });
    });

    // Accent Color
    wp.customize( 'accent_color', function( value ) {
        value.bind( function( to ) {
            $( '.button, .wp-block-button__link' ).css( 'background-color', to );
            $( '.entry-title a:hover' ).css( 'color', to );
            document.documentElement.style.setProperty( '--accent-color', to );
        });
    });

    // Link Color
    wp.customize( 'link_color', function( value ) {
        value.bind( function( to ) {
            $( 'a' ).css( 'color', to );
            document.documentElement.style.setProperty( '--link-color', to );
        });
    });

    // Show Post Date
    wp.customize( 'show_post_date', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.entry-date' ).show();
            } else {
                $( '.entry-date' ).hide();
            }
        });
    });

})( jQuery );