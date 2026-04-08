<?php
/**
 * Custom WooCommerce Payment Gateway
 *
 * @package Opencode_WC_Extension
 */

namespace OpenCode_WC_Extension;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Gateway extends \WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'opencode_custom_gateway';
        $this->icon               = OPENCODE_WC_URL . 'assets/images/gateway-icon.svg';
        $this->has_fields         = true;
        $this->method_title       = __( 'OpenCode Custom Gateway', 'opencode-wc-extension' );
        $this->method_description = __( 'Custom payment gateway for OpenCode WooCommerce Extension', 'opencode-wc-extension' );

        $this->supports = array(
            'products',
            'refunds',
        );

        $this->init_form_fields();
        $this->init_settings();

        $this->title        = $this->get_option( 'title' );
        $this->description  = $this->get_option( 'description' );
        $this->enabled      = $this->get_option( 'enabled' );
        $this->testmode     = 'yes' === $this->get_option( 'testmode' );
        $this->api_key      = $this->get_option( 'api_key' );
        $this->api_secret   = $this->get_option( 'api_secret' );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
        add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }

    public function init() {
        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
    }

    public function add_gateway( $methods ) {
        $methods[] = $this;
        return $methods;
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'opencode-wc-extension' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable OpenCode Custom Gateway', 'opencode-wc-extension' ),
                'default' => 'yes',
            ),
            'title' => array(
                'title'       => __( 'Title', 'opencode-wc-extension' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'opencode-wc-extension' ),
                'default'     => __( 'OpenCode Custom Payment', 'opencode-wc-extension' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', 'opencode-wc-extension' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your checkout.', 'opencode-wc-extension' ),
                'default'     => __( 'Pay securely using our custom payment gateway.', 'opencode-wc-extension' ),
                'desc_tip'    => true,
            ),
            'testmode' => array(
                'title'       => __( 'Test mode', 'opencode-wc-extension' ),
                'type'        => 'checkbox',
                'label'       => __( 'Enable Test Mode', 'opencode-wc-extension' ),
                'default'     => 'yes',
                'description' => __( 'Place the payment gateway in test mode using test API keys.', 'opencode-wc-extension' ),
                'desc_tip'    => true,
            ),
            'api_key' => array(
                'title'       => __( 'API Key', 'opencode-wc-extension' ),
                'type'        => 'text',
                'description' => __( 'Get your API Key from your payment gateway provider.', 'opencode-wc-extension' ),
                'desc_tip'    => true,
            ),
            'api_secret' => array(
                'title'       => __( 'API Secret', 'opencode-wc-extension' ),
                'type'        => 'password',
                'description' => __( 'Get your API Secret from your payment gateway provider.', 'opencode-wc-extension' ),
                'desc_tip'    => true,
            ),
        );
    }

    public function payment_fields() {
        if ( $this->description ) {
            if ( $this->testmode ) {
                $this->description .= ' ' . __( 'TEST MODE ENABLED. In test mode, you can use test card numbers.', 'opencode-wc-extension' );
                $this->description  = trim( $this->description );
            }
            echo wpautop( wp_kses_post( $this->description ) );
        }

        // PCI Compliance: Use hosted fields or redirect approach
        // This example uses a redirect to a hosted payment page
        echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-form" class="wc-payment-form" style="background:transparent;">';

        do_action( 'woocommerce_credit_card_form_start', $this->id );

        // Instead of collecting card data directly, we use a tokenization approach
        // The actual card data is collected on the payment provider's hosted page
        echo '<div class="form-row form-row-wide">';
        echo '<p>' . esc_html__( 'You will be redirected to our secure payment provider to complete your payment.', 'opencode-wc-extension' ) . '</p>';
        echo '<input type="hidden" name="' . esc_attr( $this->id ) . '_payment_token" id="' . esc_attr( $this->id ) . '-payment-token" value="" />';
        echo '</div>';

        do_action( 'woocommerce_credit_card_form_end', $this->id );

        echo '<div class="clear"></div></fieldset>';

        // Add JavaScript for hosted payment integration
        if ( ! $this->testmode ) {
            wp_enqueue_script(
                'opencode-wc-gateway-hosted',
                OPENCODE_WC_URL . 'assets/js/gateway-hosted.js',
                array( 'jquery' ),
                OPENCODE_WC_VERSION,
                true
            );

            wp_localize_script( 'opencode-wc-gateway-hosted', 'opencodeGateway', array(
                'apiKey'    => $this->api_key,
                'gatewayId' => $this->id,
            ) );
        }
    }

    public function validate_fields() {
        // PCI Compliance: We no longer collect card data directly
        // Validation happens on the hosted payment page
        return true;
    }

    public function process_payment( $order_id ) {
        global $woocommerce;

        $order = wc_get_order( $order_id );

        // PCI Compliance: Use tokenization instead of raw card data
        $payment_token = isset( $_POST[ $this->id . '_payment_token' ] ) 
            ? sanitize_text_field( wp_unslash( $_POST[ $this->id . '_payment_token' ] ) ) 
            : '';

        $payment_response = $this->process_gateway_payment( $order, $payment_token );

        if ( $payment_response['success'] ) {
            $order->payment_complete( $payment_response['transaction_id'] );
            $order->add_order_note( sprintf( 
                __( 'Payment completed via OpenCode Custom Gateway. Transaction ID: %s', 'opencode-wc-extension' ), 
                $payment_response['transaction_id'] 
            ) );

            $woocommerce->cart->empty_cart();

            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            );
        } else {
            wc_add_notice( $payment_response['message'], 'error' );
            $order->add_order_note( sprintf( 
                __( 'Payment failed: %s', 'opencode-wc-extension' ), 
                $payment_response['message'] 
            ) );
            return array(
                'result'   => 'fail',
                'redirect' => '',
            );
        }
    }

    /**
     * Process payment using tokenization (PCI compliant)
     *
     * @param WC_Order $order The order object
     * @param string   $payment_token The payment token from hosted fields
     * @return array Payment response
     */
    protected function process_gateway_payment( $order, $payment_token ) {
        if ( $this->testmode ) {
            return array(
                'success'        => true,
                'transaction_id' => 'TEST_' . uniqid(),
                'message'        => 'Test payment successful',
            );
        }

        $amount    = $order->get_total();
        $currency  = get_woocommerce_currency();
        $order_id  = $order->get_id();

        // PCI Compliance: Send token instead of card data
        $payload = array(
            'amount'        => $amount,
            'currency'      => $currency,
            'order_id'      => $order_id,
            'payment_token' => $payment_token,  // Token from hosted payment page
            'api_key'       => $this->api_key,
        );

        $response = wp_remote_post( 'https://api.example.com/payments', array(
            'method'  => 'POST',
            'body'    => wp_json_encode( $payload ),
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_secret,
            ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
            );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['success'] ) && $body['success'] ) {
            return array(
                'success'        => true,
                'transaction_id' => $body['transaction_id'],
                'message'        => 'Payment successful',
            );
        }

        return array(
            'success' => false,
            'message' => isset( $body['error'] ) ? $body['error'] : 'Payment failed',
        );
    }

    public function process_refund( $order_id, $amount = null, $reason = '' ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return false;
        }

        $transaction_id = $order->get_transaction_id();

        if ( $this->testmode ) {
            $order->add_order_note( sprintf( __( 'Test refund: %s - Reason: %s', 'opencode-wc-extension' ), wc_price( $amount ), $reason ) );
            return true;
        }

        $payload = array(
            'transaction_id' => $transaction_id,
            'amount'         => $amount,
            'reason'         => $reason,
        );

        $response = wp_remote_post( 'https://api.example.com/refunds', array(
            'method'  => 'POST',
            'body'    => json_encode( $payload ),
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_secret,
            ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['success'] ) && $body['success'] ) {
            $order->add_order_note( sprintf( __( 'Refunded %s - Reason: %s', 'opencode-wc-extension' ), wc_price( $amount ), $reason ) );
            return true;
        }

        return false;
    }

    public function thankyou_page( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( $this->id === $order->get_payment_method() ) {
            echo '<div class="opencode-wc-thankyou">';
            echo '<h2>' . esc_html__( 'Payment Information', 'opencode-wc-extension' ) . '</h2>';
            echo '<p>' . esc_html__( 'Thank you for your payment. Your order has been received.', 'opencode-wc-extension' ) . '</p>';
            echo '</div>';
        }
    }

    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
        if ( $this->id === $order->get_payment_method() && $sent_to_admin ) {
            if ( $plain_text ) {
                echo "\n" . esc_html__( 'Payment Method: OpenCode Custom Gateway', 'opencode-wc-extension' ) . "\n";
            } else {
                echo '<h3>' . esc_html__( 'Payment Method', 'opencode-wc-extension' ) . '</h3>';
                echo '<p>' . esc_html__( 'OpenCode Custom Gateway', 'opencode-wc-extension' ) . '</p>';
            }
        }
    }
}