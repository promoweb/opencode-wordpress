<?php
/**
 * Main WooCommerce extension class
 *
 * @package Opencode_WC_Extension
 */

namespace OpenCode_WC_Extension;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Main {

    protected $version;

    public function __construct() {
        $this->version = OPENCODE_WC_VERSION;
    }

    public function init() {
        $this->load_textdomain();
        $this->init_hooks();
        $this->enqueue_assets();
    }

    protected function load_textdomain() {
        load_plugin_textdomain(
            'opencode-wc-extension',
            false,
            dirname( OPENCODE_WC_BASENAME ) . '/languages'
        );
    }

    protected function init_hooks() {
        add_filter( 'woocommerce_get_sections_products', array( $this, 'add_settings_section' ) );
        add_filter( 'woocommerce_get_settings_products', array( $this, 'add_section_settings' ), 10, 2 );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_product_fields' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_fields' ), 10, 2 );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tab' ) );
        add_filter( 'plugin_action_links_' . OPENCODE_WC_BASENAME, array( $this, 'add_action_links' ) );
        add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_custom_fees' ) );
        add_filter( 'woocommerce_checkout_fields', array( $this, 'custom_checkout_fields' ) );
        add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 4 );
    }

    public function enqueue_assets() {
        if ( is_admin() ) {
            wp_enqueue_style(
                'opencode-wc-admin',
                OPENCODE_WC_URL . 'assets/css/admin.css',
                array(),
                $this->version
            );

            wp_enqueue_script(
                'opencode-wc-admin',
                OPENCODE_WC_URL . 'assets/js/admin.js',
                array( 'jquery' ),
                $this->version,
                true
            );
        }

        if ( is_front_page() || is_shop() || is_product() ) {
            wp_enqueue_style(
                'opencode-wc-frontend',
                OPENCODE_WC_URL . 'assets/css/frontend.css',
                array(),
                $this->version
            );
        }
    }

    public function add_settings_section( $sections ) {
        $sections['opencode_wc'] = __( 'OpenCode Settings', 'opencode-wc-extension' );
        return $sections;
    }

    public function add_section_settings( $settings, $current_section ) {
        if ( 'opencode_wc' === $current_section ) {
            $custom_settings = array(
                array(
                    'title' => __( 'OpenCode WooCommerce Settings', 'opencode-wc-extension' ),
                    'type'  => 'title',
                    'desc'  => __( 'Configure OpenCode WooCommerce Extension settings.', 'opencode-wc-extension' ),
                    'id'    => 'opencode_wc_settings_title',
                ),
                array(
                    'title'    => __( 'Enable Custom Features', 'opencode-wc-extension' ),
                    'desc'     => __( 'Enable custom product types and features', 'opencode-wc-extension' ),
                    'id'       => 'opencode_wc_enable_features',
                    'default'  => 'yes',
                    'type'     => 'checkbox',
                    'desc_tip' => true,
                ),
                array(
                    'title'    => __( 'Custom Fee Amount', 'opencode-wc-extension' ),
                    'desc'     => __( 'Fixed fee amount to add to cart', 'opencode-wc-extension' ),
                    'id'       => 'opencode_wc_custom_fee',
                    'default'  => '0',
                    'type'     => 'number',
                    'desc_tip' => true,
                ),
                array(
                    'title'   => __( 'Custom Fee Name', 'opencode-wc-extension' ),
                    'desc'    => __( 'Label for custom fee', 'opencode-wc-extension' ),
                    'id'      => 'opencode_wc_custom_fee_name',
                    'default' => __( 'Handling Fee', 'opencode-wc-extension' ),
                    'type'    => 'text',
                ),
                array(
                    'type' => 'sectionend',
                    'id'   => 'opencode_wc_settings_end',
                ),
            );
            return $custom_settings;
        }
        return $settings;
    }

    public function add_product_fields() {
        global $woocommerce, $post;

        // Add nonce field for security
        wp_nonce_field( 'opencode_wc_product_save', 'opencode_wc_product_nonce' );

        echo '<div class="options_group">';

        woocommerce_wp_text_input(
            array(
                'id'          => '_opencode_wc_custom_field',
                'label'       => __( 'Custom Product Field', 'opencode-wc-extension' ),
                'placeholder' => __( 'Enter custom value', 'opencode-wc-extension' ),
                'desc_tip'    => 'true',
                'description' => __( 'This is a custom product field for OpenCode extension.', 'opencode-wc-extension' ),
            )
        );

        woocommerce_wp_checkbox(
            array(
                'id'          => '_opencode_wc_special_product',
                'label'       => __( 'Special Product', 'opencode-wc-extension' ),
                'desc_tip'    => 'true',
                'description' => __( 'Mark this product as special', 'opencode-wc-extension' ),
            )
        );

        echo '</div>';
    }

    public function save_product_fields( $post_id, $post ) {
        // Verify nonce for security (CSRF protection)
        $nonce = isset( $_POST['opencode_wc_product_nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['opencode_wc_product_nonce'] ) )
            : '';

        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'opencode_wc_product_save' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check capabilities
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check post type
        if ( get_post_type( $post_id ) !== 'product' ) {
            return;
        }

        if ( isset( $_POST['_opencode_wc_custom_field'] ) ) {
            update_post_meta( $post_id, '_opencode_wc_custom_field', sanitize_text_field( wp_unslash( $_POST['_opencode_wc_custom_field'] ) ) );
        }

        if ( isset( $_POST['_opencode_wc_special_product'] ) ) {
            update_post_meta( $post_id, '_opencode_wc_special_product', 'yes' );
        } else {
            update_post_meta( $post_id, '_opencode_wc_special_product', 'no' );
        }
    }

    public function add_product_data_tab( $tabs ) {
        $tabs['opencode_wc'] = array(
            'label'    => __( 'OpenCode', 'opencode-wc-extension' ),
            'target'   => 'opencode_wc_product_data',
            'class'    => array( 'show_if_simple', 'show_if_variable' ),
            'priority' => 21,
        );
        return $tabs;
    }

    public function add_action_links( $links ) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url( 'admin.php?page=wc-settings&tab=products&section=opencode_wc' ),
            __( 'Settings', 'opencode-wc-extension' )
        );
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function add_custom_fees( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        $fee_amount = get_option( 'opencode_wc_custom_fee', 0 );
        $fee_name = get_option( 'opencode_wc_custom_fee_name', __( 'Handling Fee', 'opencode-wc-extension' ) );

        if ( $fee_amount > 0 ) {
            $cart->add_fee( $fee_name, $fee_amount, true, 'standard' );
        }
    }

    public function custom_checkout_fields( $fields ) {
        $fields['billing']['billing_custom_field'] = array(
            'type'        => 'text',
            'label'       => __( 'Custom Billing Field', 'opencode-wc-extension' ),
            'placeholder' => __( 'Enter custom information', 'opencode-wc-extension' ),
            'required'    => false,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 120,
        );

        $fields['order']['order_comments']['placeholder'] = __( 'Special instructions for your order', 'opencode-wc-extension' );

        return $fields;
    }

    public function order_status_changed( $order_id, $old_status, $new_status, $order ) {
        if ( 'completed' === $new_status ) {
            $custom_field = $order->get_meta( '_opencode_wc_custom_order_field' );
            if ( ! empty( $custom_field ) ) {
                $order->add_order_note( sprintf( __( 'Custom field value: %s', 'opencode-wc-extension' ), $custom_field ) );
            }
        }

        if ( 'processing' === $new_status && 'pending' === $old_status ) {
            do_action( 'opencode_wc_order_processing', $order_id, $order );
        }
    }

    public static function get_product_custom_field( $product_id ) {
        return get_post_meta( $product_id, '_opencode_wc_custom_field', true );
    }

    public static function is_special_product( $product_id ) {
        return get_post_meta( $product_id, '_opencode_wc_special_product', true ) === 'yes';
    }
}