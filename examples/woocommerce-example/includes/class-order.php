<?php
/**
 * WooCommerce Order Handler
 *
 * @package Opencode_WC_Extension
 */

namespace OpenCode_WC_Extension;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Order_Handler {

    public function init() {
        add_action( 'woocommerce_checkout_create_order', array( $this, 'add_custom_order_meta' ), 10, 2 );
        add_action( 'woocommerce_before_checkout_process', array( $this, 'validate_custom_fields' ) );
        add_filter( 'woocommerce_email_order_meta_fields', array( $this, 'add_email_order_meta' ), 10, 3 );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_custom_order_data' ), 10, 1 );
        add_filter( 'woocommerce_my_account_my_orders_columns', array( $this, 'add_custom_orders_column' ) );
        add_action( 'woocommerce_my_account_my_orders_column_custom_column', array( $this, 'render_custom_orders_column' ), 10, 1 );
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_admin_order_meta' ), 10, 1 );
        add_filter( 'woocommerce_order_table_item', array( $this, 'customize_order_table_item' ), 10, 1 );
        add_action( 'woocommerce_order_status_cancelled', array( $this, 'handle_cancelled_order' ), 10, 1 );
        add_action( 'woocommerce_order_status_refunded', array( $this, 'handle_refunded_order' ), 10, 1 );
        add_filter( 'woocommerce_get_order_item_totals', array( $this, 'customize_order_totals' ), 10, 2 );
    }

    public function add_custom_order_meta( $order, $data ) {
        if ( isset( $data['billing_custom_field'] ) ) {
            $order->update_meta_data( '_billing_custom_field', sanitize_text_field( $data['billing_custom_field'] ) );
        }

        if ( isset( $data['opencode_custom_data'] ) ) {
            $order->update_meta_data( '_opencode_custom_order_data', sanitize_textarea_field( $data['opencode_custom_data'] ) );
        }

        $order->update_meta_data( '_opencode_order_source', 'opencode_wc_extension' );
        $order->update_meta_data( '_opencode_order_created_time', current_time( 'mysql' ) );

        foreach ( $order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            
            if ( Product_Type::is_custom_enabled( $product_id ) ) {
                $custom_attribute = Product_Type::get_custom_attribute( $product_id );
                $custom_type = Product_Type::get_custom_type( $product_id );
                
                $item->update_meta_data( '_opencode_custom_attribute', $custom_attribute );
                $item->update_meta_data( '_opencode_custom_type', $custom_type );
            }
        }
    }

    public function validate_custom_fields() {
        $custom_field_required = get_option( 'opencode_wc_custom_field_required', 'no' );

        if ( 'yes' === $custom_field_required ) {
            if ( empty( $_POST['billing_custom_field'] ) ) {
                wc_add_notice( __( 'Custom billing field is required.', 'opencode-wc-extension' ), 'error' );
            }
        }
    }

    public function add_email_order_meta( $fields, $sent_to_admin, $order ) {
        $custom_field = $order->get_meta( '_billing_custom_field' );
        
        if ( $custom_field ) {
            $fields['billing_custom_field'] = array(
                'label' => __( 'Custom Field', 'opencode-wc-extension' ),
                'value' => $custom_field,
            );
        }

        $order_source = $order->get_meta( '_opencode_order_source' );
        
        if ( $order_source ) {
            $fields['opencode_order_source'] = array(
                'label' => __( 'Order Source', 'opencode-wc-extension' ),
                'value' => $order_source,
            );
        }

        return $fields;
    }

    public function display_custom_order_data( $order ) {
        $custom_field = $order->get_meta( '_billing_custom_field' );
        
        if ( $custom_field ) {
            echo '<section class="opencode-custom-order-data">';
            echo '<h3>' . esc_html__( 'Custom Information', 'opencode-wc-extension' ) . '</h3>';
            echo '<table class="opencode-custom-data-table">';
            echo '<tr><th>' . esc_html__( 'Custom Field:', 'opencode-wc-extension' ) . '</th><td>' . esc_html( $custom_field ) . '</td></tr>';
            echo '</table>';
            echo '</section>';
        }

        $has_custom_items = false;
        foreach ( $order->get_items() as $item ) {
            if ( $item->get_meta( '_opencode_custom_type' ) ) {
                $has_custom_items = true;
                break;
            }
        }

        if ( $has_custom_items ) {
            echo '<section class="opencode-custom-items">';
            echo '<h3>' . esc_html__( 'Custom Product Details', 'opencode-wc-extension' ) . '</h3>';
            echo '<table>';
            
            foreach ( $order->get_items() as $item ) {
                $custom_type = $item->get_meta( '_opencode_custom_type' );
                $custom_attribute = $item->get_meta( '_opencode_custom_attribute' );
                
                if ( $custom_type || $custom_attribute ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $item->get_name() ) . '</td>';
                    if ( $custom_type ) {
                        echo '<td><strong>' . esc_html__( 'Type:', 'opencode-wc-extension' ) . '</strong> ' . esc_html( $custom_type ) . '</td>';
                    }
                    if ( $custom_attribute ) {
                        echo '<td><strong>' . esc_html__( 'Attribute:', 'opencode-wc-extension' ) . '</strong> ' . esc_html( $custom_attribute ) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            
            echo '</table>';
            echo '</section>';
        }
    }

    public function add_custom_orders_column( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $value ) {
            $new_columns[$key] = $value;

            if ( 'order-status' === $key ) {
                $new_columns['custom-column'] = __( 'Custom', 'opencode-wc-extension' );
            }
        }

        return $new_columns;
    }

    public function render_custom_orders_column( $order ) {
        $custom_field = $order->get_meta( '_billing_custom_field' );
        
        if ( $custom_field ) {
            echo esc_html( substr( $custom_field, 0, 20 ) );
            if ( strlen( $custom_field ) > 20 ) {
                echo '...';
            }
        } else {
            echo '—';
        }
    }

    public function display_admin_order_meta( $order ) {
        $custom_field = $order->get_meta( '_billing_custom_field' );
        $order_source = $order->get_meta( '_opencode_order_source' );
        $created_time = $order->get_meta( '_opencode_order_created_time' );

        echo '<div class="opencode-admin-order-meta">';
        echo '<h3>' . esc_html__( 'OpenCode Custom Data', 'opencode-wc-extension' ) . '</h3>';
        echo '<table class="form-table">';

        if ( $custom_field ) {
            echo '<tr><th>' . esc_html__( 'Custom Field', 'opencode-wc-extension' ) . '</th><td>' . esc_html( $custom_field ) . '</td></tr>';
        }

        if ( $order_source ) {
            echo '<tr><th>' . esc_html__( 'Order Source', 'opencode-wc-extension' ) . '</th><td>' . esc_html( $order_source ) . '</td></tr>';
        }

        if ( $created_time ) {
            echo '<tr><th>' . esc_html__( 'Created Time', 'opencode-wc-extension' ) . '</th><td>' . esc_html( $created_time ) . '</td></tr>';
        }

        echo '</table>';
        echo '</div>';

        $has_custom_items = false;
        foreach ( $order->get_items() as $item ) {
            $custom_type = $item->get_meta( '_opencode_custom_type' );
            if ( $custom_type ) {
                $has_custom_items = true;
                break;
            }
        }

        if ( $has_custom_items ) {
            echo '<div class="opencode-admin-custom-items">';
            echo '<h4>' . esc_html__( 'Custom Product Items', 'opencode-wc-extension' ) . '</h4>';
            echo '<ul>';
            
            foreach ( $order->get_items() as $item ) {
                $custom_type = $item->get_meta( '_opencode_custom_type' );
                $custom_attribute = $item->get_meta( '_opencode_custom_attribute' );
                
                if ( $custom_type ) {
                    echo '<li>';
                    echo esc_html( $item->get_name() );
                    echo ' — ';
                    echo '<strong>' . esc_html__( 'Type:', 'opencode-wc-extension' ) . '</strong> ' . esc_html( $custom_type );
                    if ( $custom_attribute ) {
                        echo ', <strong>' . esc_html__( 'Attribute:', 'opencode-wc-extension' ) . '</strong> ' . esc_html( $custom_attribute );
                    }
                    echo '</li>';
                }
            }
            
            echo '</ul>';
            echo '</div>';
        }
    }

    public function customize_order_table_item( $item ) {
        if ( $item->get_meta( '_opencode_custom_type' ) ) {
            $custom_type = $item->get_meta( '_opencode_custom_type' );
            $item->set_name( $item->get_name() . ' (' . $custom_type . ')' );
        }

        return $item;
    }

    public function handle_cancelled_order( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $order_source = $order->get_meta( '_opencode_order_source' );

        if ( $order_source === 'opencode_wc_extension' ) {
            $order->add_order_note( __( 'Order cancelled via OpenCode WooCommerce Extension.', 'opencode-wc-extension' ) );
            
            do_action( 'opencode_wc_order_cancelled', $order_id, $order );
        }
    }

    public function handle_refunded_order( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $order_source = $order->get_meta( '_opencode_order_source' );

        if ( $order_source === 'opencode_wc_extension' ) {
            $order->add_order_note( __( 'Order refunded via OpenCode WooCommerce Extension.', 'opencode-wc-extension' ) );
            
            $refund_amount = 0;
            foreach ( $order->get_refunds() as $refund ) {
                $refund_amount += $refund->get_amount();
            }

            do_action( 'opencode_wc_order_refunded', $order_id, $order, $refund_amount );
        }
    }

    public function customize_order_totals( $total_rows, $order ) {
        $custom_fee = get_option( 'opencode_wc_custom_fee', 0 );

        if ( $custom_fee > 0 && ! isset( $total_rows['opencode_custom_fee'] ) ) {
            $fee_name = get_option( 'opencode_wc_custom_fee_name', __( 'Handling Fee', 'opencode-wc-extension' ) );
            
            $total_rows['opencode_custom_fee'] = array(
                'label' => $fee_name,
                'value' => wc_price( $custom_fee, array( 'currency' => $order->get_currency() ) ),
            );
        }

        return $total_rows;
    }

    public static function get_order_custom_field( $order_id ) {
        $order = wc_get_order( $order_id );
        return $order ? $order->get_meta( '_billing_custom_field' ) : '';
    }

    public static function get_order_source( $order_id ) {
        $order = wc_get_order( $order_id );
        return $order ? $order->get_meta( '_opencode_order_source' ) : '';
    }

    public static function has_custom_items( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return false;
        }

        foreach ( $order->get_items() as $item ) {
            if ( $item->get_meta( '_opencode_custom_type' ) ) {
                return true;
            }
        }

        return false;
    }
}