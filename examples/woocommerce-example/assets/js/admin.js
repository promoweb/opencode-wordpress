/**
 * OpenCode WooCommerce Extension - Admin JavaScript
 *
 * @package Opencode_WC_Extension
 */

(function( $ ) {
    'use strict';

    // Document ready
    $( document ).ready( function() {
        initProductTypeToggle();
        initOrderMetaBox();
        initGatewaySettings();
    });

    /**
     * Initialize product type toggle functionality
     */
    function initProductTypeToggle() {
        // Show/hide custom product fields based on product type
        $( 'select#product-type' ).on( 'change', function() {
            var productType = $( this ).val();
            
            if ( productType === 'opencode_custom' ) {
                $( '.opencode_product_data_panel' ).show();
                $( '#opencode_custom_product_options' ).show();
            } else {
                $( '.opencode_product_data_panel' ).hide();
                $( '#opencode_custom_product_options' ).hide();
            }
        }).trigger( 'change' );

        // Handle custom product tab visibility
        $( '.product_data_tabs' ).on( 'click', '.opencode_product_tab a', function( e ) {
            e.preventDefault();
            $( this ).parent().addClass( 'active' );
            $( '.opencode_product_data_panel' ).show();
        });
    }

    /**
     * Initialize order meta box functionality
     */
    function initOrderMetaBox() {
        // Refresh order meta on status change
        $( '#opencode_order_meta' ).on( 'change', '.opencode-order-status-select', function() {
            var $select = $( this );
            var orderId = $select.data( 'order-id' );
            var status = $select.val();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'opencode_wc_update_order_status',
                    order_id: orderId,
                    status: status,
                    nonce: opencodeWcAdmin.nonce
                },
                success: function( response ) {
                    if ( response.success ) {
                        $select.closest( '.opencode-meta-row' ).find( '.opencode-meta-value' ).text( status );
                    }
                }
            });
        });

        // Copy transaction ID
        $( '#opencode_order_meta' ).on( 'click', '.opencode-copy-transaction', function( e ) {
            e.preventDefault();
            var transactionId = $( this ).data( 'transaction-id' );
            
            if ( navigator.clipboard ) {
                navigator.clipboard.writeText( transactionId ).then( function() {
                    alert( opencodeWcAdmin.copiedText );
                });
            } else {
                // Fallback for older browsers
                var $temp = $( '<input>' );
                $( 'body' ).append( $temp );
                $temp.val( transactionId ).select();
                document.execCommand( 'copy' );
                $temp.remove();
                alert( opencodeWcAdmin.copiedText );
            }
        });
    }

    /**
     * Initialize gateway settings functionality
     */
    function initGatewaySettings() {
        // Toggle test mode notice
        $( '#woocommerce_opencode_custom_gateway_testmode' ).on( 'change', function() {
            if ( $( this ).is( ':checked' ) ) {
                $( '.opencode-gateway-test-mode-notice' ).show();
            } else {
                $( '.opencode-gateway-test-mode-notice' ).hide();
            }
        }).trigger( 'change' );

        // Validate API credentials
        $( '#woocommerce_opencode_custom_gateway_api_key, #woocommerce_opencode_custom_gateway_api_secret' ).on( 'blur', function() {
            var apiKey = $( '#woocommerce_opencode_custom_gateway_api_key' ).val();
            var apiSecret = $( '#woocommerce_opencode_custom_gateway_api_secret' ).val();

            if ( apiKey && apiSecret ) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'opencode_wc_validate_credentials',
                        api_key: apiKey,
                        api_secret: apiSecret,
                        nonce: opencodeWcAdmin.nonce
                    },
                    success: function( response ) {
                        if ( response.success ) {
                            $( '.opencode-credentials-status' )
                                .removeClass( 'invalid' )
                                .addClass( 'valid' )
                                .text( opencodeWcAdmin.credentialsValid );
                        } else {
                            $( '.opencode-credentials-status' )
                                .removeClass( 'valid' )
                                .addClass( 'invalid' )
                                .text( opencodeWcAdmin.credentialsInvalid );
                        }
                    }
                });
            }
        });
    }

})( jQuery );