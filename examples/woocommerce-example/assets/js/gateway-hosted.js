/**
 * OpenCode WooCommerce Gateway - Hosted Payment Integration
 *
 * PCI Compliant: This script handles tokenization via hosted payment fields
 * No card data is ever collected or transmitted through this code
 *
 * @package Opencode_WC_Extension
 */

(function( $ ) {
    'use strict';

    // Document ready
    $( document ).ready( function() {
        initHostedPayment();
    });

    /**
     * Initialize hosted payment integration
     */
    function initHostedPayment() {
        var gatewayId = opencodeGateway.gatewayId;
        var apiKey = opencodeGateway.apiKey;

        // Initialize hosted payment provider (example implementation)
        // Replace with actual payment provider SDK integration
        if ( typeof HostedPaymentProvider !== 'undefined' ) {
            HostedPaymentProvider.init({
                apiKey: apiKey,
                containerId: 'wc-' + gatewayId + '-form',
                onSuccess: function( token ) {
                    // Set the payment token
                    $( '#' + gatewayId + '-payment-token' ).val( token );
                },
                onError: function( error ) {
                    console.error( 'Payment initialization error:', error );
                }
            });
        }

        // Handle checkout form submission
        $( 'form.checkout' ).on( 'checkout_place_order_' + gatewayId, function() {
            var token = $( '#' + gatewayId + '-payment-token' ).val();
            
            if ( ! token ) {
                // Show error message
                $( '.woocommerce-error' ).remove();
                $( 'form.checkout' ).prepend(
                    '<ul class="woocommerce-error" role="alert">' +
                    '<li>' + opencodeGateway.tokenRequired + '</li>' +
                    '</ul>'
                );
                return false;
            }
            
            return true;
        });

        // Handle payment method change
        $( 'input[name="payment_method"]' ).on( 'change', function() {
            if ( $( this ).val() === gatewayId ) {
                initHostedPayment();
            }
        });
    }

})( jQuery );