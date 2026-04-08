<?php
/**
 * Custom WooCommerce Product Type
 *
 * @package Opencode_WC_Extension
 */

namespace OpenCode_WC_Extension;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Product_Type {

    const PRODUCT_TYPE = 'opencode_custom';

    public function init() {
        add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 3 );
        add_filter( 'woocommerce_product_types', array( $this, 'add_product_type' ) );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'custom_product_type_tab' ) );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'custom_product_type_options' ) );
        add_action( 'woocommerce_process_product_meta_opencode_custom', array( $this, 'save_custom_product_type_meta' ) );
        add_action( 'admin_footer', array( $this, 'product_type_custom_js' ) );
        add_filter( 'woocommerce_product_type_query_var_keys', array( $this, 'add_query_var_keys' ) );
    }

    public function product_class( $classname, $product_type, $product_id ) {
        if ( $product_type === self::PRODUCT_TYPE ) {
            $classname = 'WC_Product_' . ucfirst( str_replace( '-', '_', self::PRODUCT_TYPE ) );
        }
        return $classname;
    }

    public function add_product_type( $types ) {
        $types[self::PRODUCT_TYPE] = __( 'OpenCode Custom', 'opencode-wc-extension' );
        return $types;
    }

    public function custom_product_type_tab( $tabs ) {
        $tabs['opencode_custom'] = array(
            'label'    => __( 'OpenCode Custom', 'opencode-wc-extension' ),
            'target'   => 'opencode_custom_product_data',
            'class'    => array( 'show_if_opencode_custom', 'hide_if_simple', 'hide_if_variable', 'hide_if_grouped', 'hide_if_external' ),
            'priority' => 10,
        );
        return $tabs;
    }

    public function custom_product_type_options() {
        global $post;

        $product = wc_get_product( $post->ID );

        if ( $product && $product->get_type() === self::PRODUCT_TYPE ) {
            // Add nonce field for security
            wp_nonce_field( 'opencode_wc_custom_product_save', 'opencode_wc_custom_product_nonce' );

            echo '<div class="options_group show_if_opencode_custom">';

            woocommerce_wp_text_input(
                array(
                    'id'          => '_opencode_custom_attribute',
                    'label'       => __( 'Custom Attribute', 'opencode-wc-extension' ),
                    'placeholder' => __( 'Enter custom attribute', 'opencode-wc-extension' ),
                    'desc_tip'    => 'true',
                    'description' => __( 'This attribute is specific to OpenCode Custom product type.', 'opencode-wc-extension' ),
                    'value'       => $product->get_meta( '_opencode_custom_attribute' ),
                )
            );

            woocommerce_wp_select(
                array(
                    'id'          => '_opencode_custom_type',
                    'label'       => __( 'Custom Type', 'opencode-wc-extension' ),
                    'options'     => array(
                        'standard' => __( 'Standard', 'opencode-wc-extension' ),
                        'premium'  => __( 'Premium', 'opencode-wc-extension' ),
                        'exclusive' => __( 'Exclusive', 'opencode-wc-extension' ),
                    ),
                    'value'       => $product->get_meta( '_opencode_custom_type' ) ?: 'standard',
                    'desc_tip'    => 'true',
                    'description' => __( 'Select the custom product type.', 'opencode-wc-extension' ),
                )
            );

            woocommerce_wp_checkbox(
                array(
                    'id'          => '_opencode_custom_enabled',
                    'label'       => __( 'Enable Custom Features', 'opencode-wc-extension' ),
                    'desc_tip'    => 'true',
                    'description' => __( 'Enable additional custom features for this product.', 'opencode-wc-extension' ),
                    'value'       => $product->get_meta( '_opencode_custom_enabled' ) === 'yes' ? 'yes' : 'no',
                )
            );

            woocommerce_wp_textarea_input(
                array(
                    'id'          => '_opencode_custom_notes',
                    'label'       => __( 'Custom Notes', 'opencode-wc-extension' ),
                    'placeholder' => __( 'Enter custom notes', 'opencode-wc-extension' ),
                    'desc_tip'    => 'true',
                    'description' => __( 'Internal notes for this custom product.', 'opencode-wc-extension' ),
                    'value'       => $product->get_meta( '_opencode_custom_notes' ),
                    'rows'        => 3,
                )
            );

            echo '</div>';
        }
    }

    public function save_custom_product_type_meta( $post_id ) {
        // Verify nonce for security (CSRF protection)
        $nonce = isset( $_POST['opencode_wc_custom_product_nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['opencode_wc_custom_product_nonce'] ) )
            : '';

        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'opencode_wc_custom_product_save' ) ) {
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

        $product = wc_get_product( $post_id );

        if ( ! $product ) {
            return;
        }

        if ( isset( $_POST['_opencode_custom_attribute'] ) ) {
            $product->update_meta_data( '_opencode_custom_attribute', sanitize_text_field( wp_unslash( $_POST['_opencode_custom_attribute'] ) ) );
        }

        if ( isset( $_POST['_opencode_custom_type'] ) ) {
            $product->update_meta_data( '_opencode_custom_type', sanitize_text_field( wp_unslash( $_POST['_opencode_custom_type'] ) ) );
        }

        if ( isset( $_POST['_opencode_custom_enabled'] ) ) {
            $product->update_meta_data( '_opencode_custom_enabled', 'yes' );
        } else {
            $product->update_meta_data( '_opencode_custom_enabled', 'no' );
        }

        if ( isset( $_POST['_opencode_custom_notes'] ) ) {
            $product->update_meta_data( '_opencode_custom_notes', sanitize_textarea_field( wp_unslash( $_POST['_opencode_custom_notes'] ) ) );
        }

        $product->save();
    }

    public function product_type_custom_js() {
        global $typenow, $post;

        if ( 'product' !== $typenow ) {
            return;
        }

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#product_type').on('change', function() {
                    var productType = $(this).val();
                    
                    if (productType === 'opencode_custom') {
                        $('.show_if_opencode_custom').show();
                        $('.hide_if_opencode_custom').hide();
                        $('.general_options').show();
                        $('.inventory_options').show();
                    } else {
                        $('.show_if_opencode_custom').hide();
                    }
                });

                <?php if ( isset( $post ) && $post ) : ?>
                var product = '<?php echo esc_js( wc_get_product( $post->ID )->get_type() ); ?>';
                if (product === 'opencode_custom') {
                    $('.show_if_opencode_custom').show();
                    $('.hide_if_opencode_custom').hide();
                }
                <?php endif; ?>
            });
        </script>
        <?php
    }

    public function add_query_var_keys( $query_vars ) {
        $query_vars[self::PRODUCT_TYPE] = self::PRODUCT_TYPE;
        return $query_vars;
    }

    public static function get_custom_attribute( $product_id ) {
        $product = wc_get_product( $product_id );
        return $product ? $product->get_meta( '_opencode_custom_attribute' ) : '';
    }

    public static function get_custom_type( $product_id ) {
        $product = wc_get_product( $product_id );
        return $product ? $product->get_meta( '_opencode_custom_type' ) : 'standard';
    }

    public static function is_custom_enabled( $product_id ) {
        $product = wc_get_product( $product_id );
        return $product ? $product->get_meta( '_opencode_custom_enabled' ) === 'yes' : false;
    }

    public static function get_custom_notes( $product_id ) {
        $product = wc_get_product( $product_id );
        return $product ? $product->get_meta( '_opencode_custom_notes' ) : '';
    }
}

if ( class_exists( 'WC_Product' ) ) {
    class WC_Product_Opencode_Custom extends WC_Product {

        protected $extra_data = array(
            '_opencode_custom_attribute' => '',
            '_opencode_custom_type'      => 'standard',
            '_opencode_custom_enabled'   => 'no',
            '_opencode_custom_notes'     => '',
        );

        public function __construct( $product = 0 ) {
            parent::__construct( $product );
            $this->product_type = 'opencode_custom';
        }

        public function get_type() {
            return 'opencode_custom';
        }

        public function get_opencode_custom_attribute( $context = 'view' ) {
            return $this->get_meta( '_opencode_custom_attribute', true, $context );
        }

        public function set_opencode_custom_attribute( $value ) {
            $this->update_meta_data( '_opencode_custom_attribute', $value );
        }

        public function get_opencode_custom_type( $context = 'view' ) {
            return $this->get_meta( '_opencode_custom_type', true, $context );
        }

        public function set_opencode_custom_type( $value ) {
            $this->update_meta_data( '_opencode_custom_type', $value );
        }

        public function get_opencode_custom_enabled( $context = 'view' ) {
            return $this->get_meta( '_opencode_custom_enabled', true, $context );
        }

        public function set_opencode_custom_enabled( $value ) {
            $this->update_meta_data( '_opencode_custom_enabled', $value ? 'yes' : 'no' );
        }

        public function get_opencode_custom_notes( $context = 'view' ) {
            return $this->get_meta( '_opencode_custom_notes', true, $context );
        }

        public function set_opencode_custom_notes( $value ) {
            $this->update_meta_data( '_opencode_custom_notes', $value );
        }
    }
}