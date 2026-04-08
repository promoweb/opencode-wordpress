/**
 * OpenCode Plugin Example - Frontend JavaScript
 *
 * @package Opencode_Plugin_Example
 */

(function( $ ) {
    'use strict';

    // Document ready
    $( document ).ready( function() {
        initItemCards();
        initFilters();
        initLoadMore();
    });

    /**
     * Initialize item card interactions
     */
    function initItemCards() {
        // Add hover effect enhancement
        $( '.opencode-item-card' ).on( 'mouseenter', function() {
            $( this ).addClass( 'is-hovered' );
        }).on( 'mouseleave', function() {
            $( this ).removeClass( 'is-hovered' );
        });

        // Handle card clicks
        $( '.opencode-item-card' ).on( 'click', function( e ) {
            var $link = $( this ).find( '.opencode-item-title a' );
            if ( $link.length && ! $( e.target ).is( 'a' ) ) {
                window.location.href = $link.attr( 'href' );
            }
        });
    }

    /**
     * Initialize category filters
     */
    function initFilters() {
        $( '.opencode-filter-link' ).on( 'click', function( e ) {
            e.preventDefault();

            var $this = $( this );
            var category = $this.data( 'category' );

            // Update active state
            $( '.opencode-filter-link' ).removeClass( 'active' );
            $this.addClass( 'active' );

            // Filter items
            if ( category === 'all' ) {
                $( '.opencode-item-card' ).fadeIn( 200 );
            } else {
                $( '.opencode-item-card' ).each( function() {
                    var $card = $( this );
                    if ( $card.data( 'category' ) === category ) {
                        $card.fadeIn( 200 );
                    } else {
                        $card.fadeOut( 200 );
                    }
                });
            }
        });
    }

    /**
     * Initialize load more functionality
     */
    function initLoadMore() {
        $( '.opencode-load-more' ).on( 'click', function( e ) {
            e.preventDefault();

            var $button = $( this );
            var page = parseInt( $button.data( 'page' ) ) || 1;
            var nextPage = page + 1;

            $button.addClass( 'loading' ).text( opencodePluginFrontend.loadingText );

            $.ajax({
                url: opencodePluginFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'opencode_load_more_items',
                    page: nextPage,
                    nonce: opencodePluginFrontend.nonce
                },
                success: function( response ) {
                    if ( response.success && response.data.html ) {
                        $( '.opencode-items-grid' ).append( response.data.html );
                        $button.data( 'page', nextPage );

                        if ( response.data.hasMore ) {
                            $button.removeClass( 'loading' ).text( opencodePluginFrontend.loadMoreText );
                        } else {
                            $button.remove();
                        }
                    } else {
                        $button.removeClass( 'loading' ).text( opencodePluginFrontend.loadMoreText );
                    }
                },
                error: function() {
                    $button.removeClass( 'loading' ).text( opencodePluginFrontend.loadMoreText );
                }
            });
        });
    }

})( jQuery );