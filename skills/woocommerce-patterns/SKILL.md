name
woocommerce-patterns

description
WooCommerce extension patterns, hooks, product CRUD, order management, cart manipulation, payment gateways, shipping methods, REST API integration, and store optimization for production-grade WooCommerce development.

origin
OpenCode WordPress

# WooCommerce Development Patterns

Production-grade WooCommerce patterns for scalable, maintainable WooCommerce extensions and integrations.

## When to Use

- Building WooCommerce extensions/plugins
- Creating custom product types
- Managing orders and checkout flow
- Implementing payment gateways
- Adding custom shipping methods
- Working with WooCommerce REST API
- Extending WooCommerce hooks
- Optimizing WooCommerce performance
- Integrating third-party services with WooCommerce

## How It Works

- Structure extensions around WooCommerce hooks system
- Use WooCommerce CRUD classes for products, orders, customers
- Extend WooCommerce classes for payment/shipping gateways
- Implement WooCommerce REST API endpoints
- Leverage Action Scheduler for background processing
- Optimize with transients and proper querying
- Follow WooCommerce coding standards

## Examples

### WooCommerce Extension Structure

```
my-woocommerce-extension/
├── my-woocommerce-extension.php  # Main plugin file
├── includes/
│   ├── class-main.php            # Main extension class
│   ├── class-product.php         # Product handling
│   ├── class-order.php           # Order handling
│   ├── class-cart.php            # Cart manipulation
│   ├── class-checkout.php        # Checkout customization
│   ├── class-payment-gateway.php # Custom payment gateway
│   ├── class-shipping.php        # Custom shipping method
│   ├── class-email.php           # Custom email notifications
│   ├── class-rest-api.php        # REST API endpoints
│   ├── class-admin.php           # Admin functionality
│   ├── class-settings.php        # WooCommerce settings
│   └── hooks/
│       ├── product-hooks.php     # Product-related hooks
│       ├── order-hooks.php       # Order-related hooks
│       ├── cart-hooks.php        # Cart-related hooks
│       └── checkout-hooks.php    # Checkout hooks
├── templates/
│   ├── emails/
│   │   ├── custom-order-email.php
│   │   └── plain/
│   │       └── custom-order-email-plain.php
│   ├── checkout/
│   │   └── custom-checkout-field.php
│   └── product/
│   │   └── custom-product-type.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│   │   ├── admin.js
│   │   └── frontend.js
└── languages/
    └── my-woocommerce-extension.pot
```

### Main Plugin File

```php
<?php
/**
 * Plugin Name: My WooCommerce Extension
 * Plugin URI: https://example.com/my-woocommerce-extension
 * Description: Custom WooCommerce extension with advanced features
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * WooCommerce requires at least: 8.0
 * WooCommerce tested up to: 8.5
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: my-wc-extension
 *
 * @package My_WC_Extension
 */

defined('ABSPATH') || exit;

// Check WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {
    return;
}

define('MYWC_VERSION', '1.0.0');
define('MYWC_FILE', __FILE__);
define('MYWC_DIR', plugin_dir_path(__FILE__));
define('MYWC_URI', plugin_dir_url(__FILE__));

// Load extension
add_action('woocommerce_loaded', function () {
    require_once MYWC_DIR . 'includes/class-main.php';
    new MyWCExtension\Main();
});
```

### WooCommerce Product CRUD

```php
<?php
/**
 * Product Operations using WooCommerce CRUD
 */

namespace MyWCExtension;

class Product {
    /**
     * Create a simple product
     */
    public function create_simple_product(array $data): int {
        $product = new \WC_Product_Simple();
        
        $product->set_name($data['name']);
        $product->set_description($data['description']);
        $product->set_short_description($data['short_description']);
        $product->set_regular_price($data['price']);
        $product->set_sale_price($data['sale_price'] ?? '');
        $product->set_sku($data['sku'] ?? '');
        $product->set_manage_stock($data['manage_stock'] ?? false);
        $product->set_stock_quantity($data['stock_quantity'] ?? 0);
        $product->set_stock_status($data['stock_status'] ?? 'instock');
        $product->set_weight($data['weight'] ?? '');
        $product->set_length($data['length'] ?? '');
        $product->set_width($data['width'] ?? '');
        $product->set_height($data['height'] ?? '');
        $product->set_category_ids($data['categories'] ?? []);
        $product->set_tag_ids($data['tags'] ?? []);
        $product->set_image_id($data['image_id'] ?? 0);
        $product->set_gallery_image_ids($data['gallery_ids'] ?? []);
        
        // Save and get ID
        $product_id = $product->save();
        
        // Set attributes if provided
        if (!empty($data['attributes'])) {
            $this->set_product_attributes($product_id, $data['attributes']);
        }
        
        return $product_id;
    }

    /**
     * Create a variable product with variations
     */
    public function create_variable_product(array $data): int {
        $product = new \WC_Product_Variable();
        
        $product->set_name($data['name']);
        $product->set_description($data['description']);
        $product->set_sku($data['sku'] ?? '');
        $product->set_category_ids($data['categories'] ?? []);
        
        // Set variable attributes (these are used for variations)
        if (!empty($data['variation_attributes'])) {
            $attributes = [];
            foreach ($data['variation_attributes'] as $attr_name => $attr_options) {
                $attribute = new \WC_Product_Attribute();
                $attribute->set_name($attr_name);
                $attribute->set_options($attr_options);
                $attribute->set_visible(true);
                $attribute->set_variation(true);
                $attributes[] = $attribute;
            }
            $product->set_attributes($attributes);
        }
        
        $parent_id = $product->save();
        
        // Create variations
        if (!empty($data['variations'])) {
            foreach ($data['variations'] as $variation_data) {
                $this->create_product_variation($parent_id, $variation_data);
            }
        }
        
        // Sync variations
        \WC_Product_Variable::sync($parent_id);
        
        return $parent_id;
    }

    /**
     * Create product variation
     */
    private function create_product_variation(int $parent_id, array $data): int {
        $variation = new \WC_Product_Variation();
        $variation->set_parent_id($parent_id);
        $variation->set_attributes($data['attributes']);
        $variation->set_regular_price($data['price']);
        $variation->set_sale_price($data['sale_price'] ?? '');
        $variation->set_sku($data['sku'] ?? '');
        $variation->set_stock_quantity($data['stock_quantity'] ?? 0);
        $variation->set_manage_stock($data['manage_stock'] ?? false);
        
        return $variation->save();
    }

    /**
     * Set product attributes
     */
    private function set_product_attributes(int $product_id, array $attributes): void {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return;
        }
        
        $wc_attributes = [];
        
        foreach ($attributes as $attr_data) {
            // Check if attribute taxonomy exists
            $taxonomy = 'pa_' . sanitize_title($attr_data['name']);
            
            if (taxonomy_exists($taxonomy)) {
                // Use existing taxonomy attribute
                $attribute = new \WC_Product_Attribute();
                $attribute->set_id(wc_attribute_taxonomy_id_by_name($attr_data['name']));
                $attribute->set_name($taxonomy);
                $attribute->set_options($attr_data['options']);
                $attribute->set_visible(true);
                $attribute->set_variation($attr_data['variation'] ?? false);
                $wc_attributes[] = $attribute;
            } else {
                // Create custom attribute
                $attribute = new \WC_Product_Attribute();
                $attribute->set_name($attr_data['name']);
                $attribute->set_options($attr_data['options']);
                $attribute->set_visible(true);
                $attribute->set_variation($attr_data['variation'] ?? false);
                $wc_attributes[] = $attribute;
            }
        }
        
        $product->set_attributes($wc_attributes);
        $product->save();
    }

    /**
     * Get product by ID
     */
    public function get_product(int $product_id): ?\WC_Product {
        return wc_get_product($product_id);
    }

    /**
     * Get products by criteria
     */
    public function get_products(array $args = []): array {
        $defaults = [
            'status'     => 'publish',
            'limit'      => 10,
            'orderby'    => 'date',
            'order'      => 'DESC',
            'category'   => [],
            'type'       => [],
            'return'     => 'objects',
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        return wc_get_products($args);
    }

    /**
     * Update product
     */
    public function update_product(int $product_id, array $data): bool {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return false;
        }
        
        foreach ($data as $key => $value) {
            $method = 'set_' . $key;
            if (method_exists($product, $method)) {
                $product->$method($value);
            }
        }
        
        $product->save();
        
        return true;
    }

    /**
     * Delete product
     */
    public function delete_product(int $product_id, bool $force_delete = false): bool {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return false;
        }
        
        return $product->delete($force_delete);
    }
}
```

### WooCommerce Order Management

```php
<?php
/**
 * Order Operations
 */

namespace MyWCExtension;

class Order {
    /**
     * Create a new order
     */
    public function create_order(array $data): int {
        $order = new \WC_Order();
        
        // Set customer data
        if (!empty($data['customer_id'])) {
            $order->set_customer_id($data['customer_id']);
        }
        
        // Set billing address
        $order->set_billing_first_name($data['billing']['first_name'] ?? '');
        $order->set_billing_last_name($data['billing']['last_name'] ?? '');
        $order->set_billing_company($data['billing']['company'] ?? '');
        $order->set_billing_address_1($data['billing']['address_1'] ?? '');
        $order->set_billing_address_2($data['billing']['address_2'] ?? '');
        $order->set_billing_city($data['billing']['city'] ?? '');
        $order->set_billing_state($data['billing']['state'] ?? '');
        $order->set_billing_postcode($data['billing']['postcode'] ?? '');
        $order->set_billing_country($data['billing']['country'] ?? '');
        $order->set_billing_email($data['billing']['email'] ?? '');
        $order->set_billing_phone($data['billing']['phone'] ?? '');
        
        // Set shipping address (copy from billing if not provided)
        if (empty($data['shipping'])) {
            $order->set_shipping_first_name($data['billing']['first_name'] ?? '');
            $order->set_shipping_last_name($data['billing']['last_name'] ?? '');
            $order->set_shipping_address_1($data['billing']['address_1'] ?? '');
            $order->set_shipping_address_2($data['billing']['address_2'] ?? '');
            $order->set_shipping_city($data['billing']['city'] ?? '');
            $order->set_shipping_state($data['billing']['state'] ?? '');
            $order->set_shipping_postcode($data['billing']['postcode'] ?? '');
            $order->set_shipping_country($data['billing']['country'] ?? '');
        } else {
            $order->set_shipping_first_name($data['shipping']['first_name'] ?? '');
            $order->set_shipping_last_name($data['shipping']['last_name'] ?? '');
            $order->set_shipping_address_1($data['shipping']['address_1'] ?? '');
            $order->set_shipping_address_2($data['shipping']['address_2'] ?? '');
            $order->set_shipping_city($data['shipping']['city'] ?? '');
            $order->set_shipping_state($data['shipping']['state'] ?? '');
            $order->set_shipping_postcode($data['shipping']['postcode'] ?? '');
            $order->set_shipping_country($data['shipping']['country'] ?? '');
        }
        
        // Add products to order
        if (!empty($data['products'])) {
            foreach ($data['products'] as $product_data) {
                $this->add_product_to_order($order, $product_data);
            }
        }
        
        // Set payment method
        if (!empty($data['payment_method'])) {
            $order->set_payment_method($data['payment_method']);
            $order->set_payment_method_title($data['payment_method_title'] ?? $data['payment_method']);
        }
        
        // Set shipping method
        if (!empty($data['shipping_method'])) {
            $shipping_rate = new \WC_Shipping_Rate();
            $shipping_rate->set_method_id($data['shipping_method']);
            $shipping_rate->set_method_title($data['shipping_method_title'] ?? 'Shipping');
            $shipping_rate->set_cost($data['shipping_cost'] ?? 0);
            $order->add_shipping($shipping_rate);
        }
        
        // Add fees
        if (!empty($data['fees'])) {
            foreach ($data['fees'] as $fee_data) {
                $this->add_fee_to_order($order, $fee_data);
            }
        }
        
        // Add coupons
        if (!empty($data['coupons'])) {
            foreach ($data['coupons'] as $coupon_code) {
                $order->apply_coupon($coupon_code);
            }
        }
        
        // Set order status
        $order->set_status($data['status'] ?? 'pending');
        
        // Set customer note
        if (!empty($data['customer_note'])) {
            $order->set_customer_note($data['customer_note']);
        }
        
        // Calculate totals
        $order->calculate_totals();
        
        // Save order
        $order_id = $order->save();
        
        return $order_id;
    }

    /**
     * Add product to order
     */
    private function add_product_to_order(\WC_Order $order, array $product_data): void {
        $product = wc_get_product($product_data['product_id']);
        
        if (!$product) {
            return;
        }
        
        $quantity = $product_data['quantity'] ?? 1;
        
        // For variable products, set variation attributes
        if ($product->is_type('variation') && !empty($product_data['variation_id'])) {
            $product = wc_get_product($product_data['variation_id']);
        }
        
        $args = [
            'variation' => $product_data['variation_data'] ?? [],
            'totals'    => [
                'subtotal'     => $product->get_price() * $quantity,
                'total'        => $product->get_price() * $quantity,
                'subtotal_tax' => 0,
                'total_tax'    => 0,
            ],
        ];
        
        $order->add_product($product, $quantity, $args);
    }

    /**
     * Add fee to order
     */
    private function add_fee_to_order(\WC_Order $order, array $fee_data): void {
        $fee = new \WC_Order_Item_Fee();
        $fee->set_name($fee_data['name']);
        $fee->set_amount($fee_data['amount']);
        $fee->set_total($fee_data['amount']);
        $fee->set_tax_class($fee_data['tax_class'] ?? '');
        
        $order->add_item($fee);
    }

    /**
     * Get order by ID
     */
    public function get_order(int $order_id): ?\WC_Order {
        return wc_get_order($order_id);
    }

    /**
     * Get orders by criteria
     */
    public function get_orders(array $args = []): array {
        $defaults = [
            'status'     => ['wc-processing', 'wc-completed'],
            'limit'      => 10,
            'orderby'    => 'date',
            'order'      => 'DESC',
            'customer_id' => 0,
            'return'     => 'objects',
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        return wc_get_orders($args);
    }

    /**
     * Update order status
     */
    public function update_order_status(int $order_id, string $new_status, string $note = ''): bool {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return false;
        }
        
        $order->update_status($new_status, $note);
        
        return true;
    }

    /**
     * Add order note
     */
    public function add_order_note(int $order_id, string $note, bool $is_customer_note = false): int {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return 0;
        }
        
        return $order->add_order_note($note, $is_customer_note);
    }

    /**
     * Refund order
     */
    public function refund_order(int $order_id, array $refund_data): int {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return 0;
        }
        
        $refund = wc_create_refund([
            'amount'         => $refund_data['amount'],
            'reason'         => $refund_data['reason'] ?? '',
            'order_id'       => $order_id,
            'refund_payment' => $refund_data['refund_payment'] ?? false,
            'restock_items'  => $refund_data['restock_items'] ?? true,
        ]);
        
        if (is_wp_error($refund)) {
            return 0;
        }
        
        return $refund->get_id();
    }

    /**
     * Delete order
     */
    public function delete_order(int $order_id, bool $force_delete = false): bool {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return false;
        }
        
        return $order->delete($force_delete);
    }
}
```

### WooCommerce Hooks - Product Hooks

```php
<?php
/**
 * Product Hooks
 */

namespace MyWCExtension\Hooks;

class ProductHooks {
    /**
     * Register hooks
     */
    public function register(): void {
        // Before product save
        add_action('woocommerce_before_product_object_save', [$this, 'before_save'], 10, 1);
        
        // After product save
        add_action('woocommerce_after_product_object_save', [$this, 'after_save'], 10, 1);
        
        // Product price filters
        add_filter('woocommerce_product_get_price', [$this, 'modify_price'], 10, 2);
        add_filter('woocommerce_product_variation_get_price', [$this, 'modify_variation_price'], 10, 2);
        
        // Product availability
        add_filter('woocommerce_get_availability', [$this, 'modify_availability'], 10, 2);
        
        // Product title filter
        add_filter('woocommerce_product_title', [$this, 'modify_title'], 10, 2);
        
        // Product tabs
        add_filter('woocommerce_product_tabs', [$this, 'modify_tabs'], 10, 1);
        
        // Product attributes display
        add_filter('woocommerce_attribute', [$this, 'modify_attribute'], 10, 3);
        
        // Stock management
        add_action('woocommerce_product_set_stock_status', [$this, 'stock_status_changed'], 10, 2);
        add_action('woocommerce_variation_set_stock_status', [$this, 'variation_stock_changed'], 10, 2);
        
        // Product visibility
        add_filter('woocommerce_product_is_visible', [$this, 'modify_visibility'], 10, 2);
        
        // Single product page hooks
        add_action('woocommerce_before_single_product_summary', [$this, 'before_summary'], 10);
        add_action('woocommerce_after_single_product_summary', [$this, 'after_summary'], 10);
        add_action('woocommerce_single_product_summary', [$this, 'custom_content'], 20);
        
        // Archive/loop hooks
        add_action('woocommerce_before_shop_loop_item', [$this, 'before_loop_item'], 10);
        add_action('woocommerce_after_shop_loop_item', [$this, 'after_loop_item'], 10);
        add_action('woocommerce_shop_loop_item_title', [$this, 'loop_item_title'], 10);
    }

    /**
     * Before product save
     */
    public function before_save(\WC_Product $product): void {
        // Log before save
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Product saving: ID ' . $product->get_id());
        }
        
        // Custom validation
        if ($product->get_price() < 0) {
            throw new Exception('Product price cannot be negative');
        }
    }

    /**
     * After product save
     */
    public function after_save(\WC_Product $product): void {
        // Clear cache
        wp_cache_delete('wc_products_' . $product->get_id());
        
        // Sync to external service (example)
        do_action('mywc_product_sync_external', $product->get_id());
    }

    /**
     * Modify product price
     */
    public function modify_price(float $price, \WC_Product $product): float {
        // Apply custom discount for logged-in users
        if (is_user_logged_in()) {
            $discount_percent = apply_filters('mywc_user_discount', 10);
            $price = $price - ($price * $discount_percent / 100);
        }
        
        return $price;
    }

    /**
     * Modify product availability text
     */
    public function modify_availability(array $availability, \WC_Product $product): array {
        if ($product->is_in_stock()) {
            $availability['availability'] = __('Available', 'my-wc-extension');
            $availability['class'] = 'stock custom-stock-class';
        } elseif ($product->is_on_backorder()) {
            $availability['availability'] = __('On backorder - Ships in 2-3 weeks', 'my-wc-extension');
            $availability['class'] = 'available-on-backorder';
        }
        
        return $availability;
    }

    /**
     * Modify product tabs
     */
    public function modify_tabs(array $tabs): array {
        // Remove description tab
        unset($tabs['description']);
        
        // Add custom tab
        $tabs['custom_tab'] = [
            'title'    => __('Custom Info', 'my-wc-extension'),
            'priority' => 15,
            'callback' => [$this, 'custom_tab_content'],
        ];
        
        return $tabs;
    }

    /**
     * Custom tab content
     */
    public function custom_tab_content(): void {
        global $product;
        
        echo '<h2>' . esc_html__('Custom Information', 'my-wc-extension') . '</h2>';
        echo '<p>' . esc_html__('This is custom product information.', 'my-wc-extension') . '</p>';
    }

    /**
     * Add custom content to single product page
     */
    public function custom_content(): void {
        global $product;
        
        echo '<div class="custom-product-info">';
        echo '<p>' . esc_html__('Free shipping on this item!', 'my-wc-extension') . '</p>';
        echo '</div>';
    }

    /**
     * Before loop item
     */
    public function before_loop_item(): void {
        echo '<div class="custom-product-wrapper">';
    }

    /**
     * After loop item
     */
    public function after_loop_item(): void {
        echo '</div>';
    }
}
```

### WooCommerce Hooks - Order Hooks

```php
<?php
/**
 * Order Hooks
 */

namespace MyWCExtension\Hooks;

class OrderHooks {
    /**
     * Register hooks
     */
    public function register(): void {
        // Order status transitions
        add_action('woocommerce_order_status_pending', [$this, 'status_pending'], 10, 1);
        add_action('woocommerce_order_status_processing', [$this, 'status_processing'], 10, 1);
        add_action('woocommerce_order_status_on-hold', [$this, 'status_on_hold'], 10, 1);
        add_action('woocommerce_order_status_completed', [$this, 'status_completed'], 10, 1);
        add_action('woocommerce_order_status_cancelled', [$this, 'status_cancelled'], 10, 1);
        add_action('woocommerce_order_status_refunded', [$this, 'status_refunded'], 10, 1);
        add_action('woocommerce_order_status_failed', [$this, 'status_failed'], 10, 1);
        
        // Order creation hooks
        add_action('woocommerce_checkout_order_created', [$this, 'order_created'], 10, 1);
        add_action('woocommerce_new_order', [$this, 'new_order'], 10, 1);
        
        // Order update hooks
        add_action('woocommerce_update_order', [$this, 'order_updated'], 10, 1);
        add_action('woocommerce_before_save_order_items', [$this, 'before_save_items'], 10, 2);
        
        // Payment complete
        add_action('woocommerce_payment_complete', [$this, 'payment_complete'], 10, 1);
        add_action('woocommerce_payment_complete_order_status_' . 'processing', [$this, 'payment_complete_processing'], 10, 1);
        
        // Order details display
        add_action('woocommerce_order_details_after_order_table', [$this, 'custom_order_details'], 10, 1);
        add_action('woocommerce_order_details_before_order_table', [$this, 'before_order_table'], 10, 1);
        
        // Email hooks
        add_action('woocommerce_email_order_details', [$this, 'custom_email_content'], 10, 4);
        add_filter('woocommerce_email_subject_new_order', [$this, 'modify_new_order_email_subject'], 10, 2);
        add_filter('woocommerce_email_heading_new_order', [$this, 'modify_new_order_email_heading'], 10, 2);
        
        // Order item hooks
        add_action('woocommerce_before_order_item_meta', [$this, 'before_item_meta'], 10, 3);
        add_action('woocommerce_after_order_item_meta', [$this, 'after_item_meta'], 10, 3);
        
        // Admin order hooks
        add_action('woocommerce_admin_order_data_after_order_details', [$this, 'admin_order_details'], 10, 1);
        add_filter('woocommerce_admin_order_actions', [$this, 'admin_order_actions'], 10, 2);
        
        // My Account hooks
        add_action('woocommerce_view_order', [$this, 'view_order'], 10, 1);
        add_filter('woocommerce_my_account_my_orders_actions', [$this, 'my_orders_actions'], 10, 2);
    }

    /**
     * Order status: Processing
     */
    public function status_processing(\WC_Order $order): void {
        // Send custom notification
        $order_id = $order->get_id();
        
        do_action('mywc_order_processing_notification', $order_id);
        
        // Trigger external webhook
        $this->trigger_webhook($order, 'order_processing');
        
        // Reduce stock (normally handled by WooCommerce)
        // wc_reduce_stock_levels_for_order($order);
    }

    /**
     * Order status: Completed
     */
    public function status_completed(\WC_Order $order): void {
        // Send completion email
        $order_id = $order->get_id();
        
        // Mark order complete in external system
        do_action('mywc_order_complete_external_sync', $order_id);
        
        // Generate license keys (if selling digital products)
        $this->generate_license_keys($order);
        
        // Trigger webhook
        $this->trigger_webhook($order, 'order_completed');
    }

    /**
     * Order created
     */
    public function order_created(\WC_Order $order): void {
        // Log new order
        $order_id = $order->get_id();
        $order->add_order_note('Order created via custom hook');
        
        // Store custom metadata
        $order->update_meta_data('_mywc_custom_order_source', 'checkout');
        $order->save();
    }

    /**
     * Payment complete
     */
    public function payment_complete(int $order_id): void {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return;
        }
        
        // Mark payment time
        $order->update_meta_data('_mywc_payment_complete_time', current_time('mysql'));
        $order->save();
        
        // Send payment confirmation
        do_action('mywc_payment_confirmation', $order_id);
    }

    /**
     * Custom order details in My Account
     */
    public function custom_order_details(\WC_Order $order): void {
        $custom_data = $order->get_meta('_mywc_custom_order_data');
        
        if ($custom_data) {
            echo '<div class="custom-order-info">';
            echo '<h3>' . esc_html__('Custom Information', 'my-wc-extension') . '</h3>';
            echo '<p>' . esc_html($custom_data) . '</p>';
            echo '</div>';
        }
    }

    /**
     * Modify new order email subject
     */
    public function modify_new_order_email_subject(string $subject, \WC_Order $order): string {
        $site_name = get_bloginfo('name');
        $order_id = $order->get_id();
        
        return sprintf(
            '[%s] New Order #%d Received',
            $site_name,
            $order_id
        );
    }

    /**
     * Trigger webhook for external service
     */
    private function trigger_webhook(\WC_Order $order, string $event): void {
        $webhook_url = get_option('mywc_webhook_url');
        
        if (!$webhook_url) {
            return;
        }
        
        $payload = [
            'event'     => $event,
            'order_id'  => $order->get_id(),
            'status'    => $order->get_status(),
            'total'     => $order->get_total(),
            'currency'  => $order->get_currency(),
            'customer'  => [
                'email' => $order->get_billing_email(),
                'name'  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            ],
            'timestamp' => current_time('mysql'),
        ];
        
        wp_remote_post($webhook_url, [
            'body' => json_encode($payload),
            'headers' => ['Content-Type' => 'application/json'],
        ]);
    }

    /**
     * Generate license keys for digital products
     */
    private function generate_license_keys(\WC_Order $order): void {
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            
            if (!$product) {
                continue;
            }
            
            // Check if product is digital and needs license
            $needs_license = $product->get_meta('_mywc_needs_license');
            
            if ($needs_license) {
                $license_key = $this->generate_license();
                wc_add_order_item_meta($item_id, '_license_key', $license_key);
                
                // Email license to customer
                do_action('mywc_send_license_email', $order->get_id(), $license_key);
            }
        }
    }

    /**
     * Generate random license key
     */
    private function generate_license(): string {
        return sprintf(
            '%s-%s-%s-%s',
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4)
        );
    }
}
```

### WooCommerce Hooks - Cart Hooks

```php
<?php
/**
 * Cart Hooks
 */

namespace MyWCExtension\Hooks;

class CartHooks {
    /**
     * Register hooks
     */
    public function register(): void {
        // Add to cart
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_add_to_cart'], 10, 3);
        add_action('woocommerce_add_to_cart', [$this, 'item_added'], 10, 6);
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_cart_item_data'], 10, 4);
        
        // Cart item display
        add_filter('woocommerce_cart_item_name', [$this, 'modify_cart_item_name'], 10, 3);
        add_filter('woocommerce_cart_item_thumbnail', [$this, 'modify_cart_thumbnail'], 10, 3);
        add_filter('woocommerce_cart_item_price', [$this, 'modify_cart_item_price'], 10, 3);
        add_filter('woocommerce_cart_item_subtotal', [$this, 'modify_cart_subtotal'], 10, 3);
        
        // Cart calculations
        add_action('woocommerce_before_calculate_totals', [$this, 'before_calculate_totals'], 10, 1);
        add_action('woocommerce_after_calculate_totals', [$this, 'after_calculate_totals'], 10, 1);
        add_filter('woocommerce_cart_item_product', [$this, 'modify_cart_product'], 10, 2);
        
        // Cart quantities
        add_filter('woocommerce_cart_item_quantity', [$this, 'modify_cart_quantity'], 10, 3);
        add_action('woocommerce_after_cart_item_quantity_update', [$this, 'quantity_updated'], 10, 2);
        
        // Remove from cart
        add_action('woocommerce_remove_cart_item', [$this, 'item_removed'], 10, 2);
        add_action('woocommerce_before_cart_item_quantity_zero', [$this, 'quantity_zero'], 10, 2);
        
        // Cart fragments (AJAX updates)
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'cart_fragments'], 10, 1);
        
        // Empty cart
        add_action('woocommerce_cart_emptied', [$this, 'cart_emptied'], 10, 1);
        
        // Cart totals
        add_filter('woocommerce_calculated_total', [$this, 'modify_total'], 10, 2);
        
        // Shipping in cart
        add_filter('woocommerce_cart_needs_shipping', [$this, 'needs_shipping'], 10, 1);
        add_filter('woocommerce_cart_needs_shipping_address', [$this, 'needs_shipping_address'], 10, 1);
        
        // Cart page hooks
        add_action('woocommerce_before_cart', [$this, 'before_cart'], 10);
        add_action('woocommerce_after_cart', [$this, 'after_cart'], 10);
        add_action('woocommerce_before_cart_table', [$this, 'before_cart_table'], 10);
        add_action('woocommerce_after_cart_table', [$this, 'after_cart_table'], 10);
        add_action('woocommerce_before_cart_totals', [$this, 'before_totals'], 10);
        add_action('woocommerce_after_cart_totals', [$this, 'after_totals'], 10);
        
        // Cross-sells and upsells
        add_action('woocommerce_cart_collaterals', [$this, 'cart_collaterals'], 10);
        add_filter('woocommerce_cross_sells_total', [$this, 'cross_sells_limit'], 10, 1);
        add_filter('woocommerce_cross_sells_orderby', [$this, 'cross_sells_orderby'], 10, 1);
    }

    /**
     * Validate before adding to cart
     */
    public function validate_add_to_cart(bool $passed, int $product_id, int $quantity): bool {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return false;
        }
        
        // Custom validation: Maximum quantity per product
        $max_qty = $product->get_meta('_mywc_max_cart_quantity');
        
        if ($max_qty && $quantity > $max_qty) {
            wc_add_notice(
                sprintf('Maximum %d items allowed for this product.', $max_qty),
                'error'
            );
            return false;
        }
        
        // Check if product requires login
        $requires_login = $product->get_meta('_mywc_requires_login');
        
        if ($requires_login && !is_user_logged_in()) {
            wc_add_notice('You must be logged in to purchase this product.', 'error');
            return false;
        }
        
        return $passed;
    }

    /**
     * Add custom cart item data
     */
    public function add_cart_item_data(array $cart_item_data, int $product_id, int $variation_id, int $quantity): array {
        // Store custom options
        if (isset($_POST['mywc_custom_option'])) {
            $cart_item_data['mywc_custom_option'] = sanitize_text_field($_POST['mywc_custom_option']);
        }
        
        // Store timestamp
        $cart_item_data['mywc_added_at'] = current_time('mysql');
        
        return $cart_item_data;
    }

    /**
     * Before cart totals calculation
     */
    public function before_calculate_totals(\WC_Cart $cart): void {
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            
            // Apply custom pricing based on cart item data
            if (isset($cart_item['mywc_custom_option'])) {
                $option_price = $this->get_option_price($cart_item['mywc_custom_option']);
                $new_price = $product->get_price() + $option_price;
                $product->set_price($new_price);
            }
            
            // Bulk discount
            if ($cart_item['quantity'] >= 5) {
                $original_price = $product->get_price();
                $discounted_price = $original_price * 0.9; // 10% discount
                $product->set_price($discounted_price);
            }
        }
    }

    /**
     * Modify cart item name
     */
    public function modify_cart_item_name(string $name, array $cart_item, string $cart_item_key): string {
        // Append custom option to name
        if (isset($cart_item['mywc_custom_option'])) {
            $name .= ' <span class="custom-option">(' . esc_html($cart_item['mywc_custom_option']) . ')</span>';
        }
        
        return $name;
    }

    /**
     * Cart fragments for AJAX
     */
    public function cart_fragments(array $fragments): array {
        $fragments['.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
        $fragments['.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';
        
        return $fragments;
    }

    /**
     * Modify cart total
     */
    public function modify_total(float $total, \WC_Cart $cart): float {
        // Apply custom discount for logged-in users
        if (is_user_logged_in()) {
            $user_discount = apply_filters('mywc_user_cart_discount', 5);
            $total = $total - ($total * $user_discount / 100);
        }
        
        // Add custom fee (example)
        $custom_fee = get_option('mywc_cart_fee', 0);
        if ($custom_fee > 0) {
            $total += $custom_fee;
        }
        
        return $total;
    }

    /**
     * Item added to cart
     */
    public function item_added(int $cart_item_key, int $product_id, int $quantity, int $variation_id, array $variation, array $cart_item_data): void {
        // Track cart addition
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            do_action('mywc_track_cart_addition', $user_id, $product_id, $quantity);
        }
    }

    /**
     * Get custom option price
     */
    private function get_option_price(string $option): float {
        $prices = [
            'option_1' => 5.00,
            'option_2' => 10.00,
            'option_3' => 15.00,
        ];
        
        return $prices[$option] ?? 0;
    }
}
```

### Custom Payment Gateway

```php
<?php
/**
 * Custom Payment Gateway Implementation
 */

namespace MyWCExtension;

class Custom_Payment_Gateway extends \WC_Payment_Gateway {
    /**
     * Gateway ID
     */
    const GATEWAY_ID = 'my_custom_gateway';

    /**
     * Constructor
     */
    public function __construct() {
        $this->id                 = self::GATEWAY_ID;
        $this->icon               = MYWC_URI . 'assets/images/payment-icon.png';
        $this->has_fields         = true;
        $this->method_title       = __('My Custom Payment', 'my-wc-extension');
        $this->method_description = __('Custom payment gateway description.', 'my-wc-extension');
        $this->supports           = [
            'products',
            'refunds',
            'subscriptions',
            'subscription_cancellation',
            'subscription_suspension',
            'subscription_reactivation',
        ];

        // Load settings
        $this->init_form_fields();
        $this->init_settings();

        // Get setting values
        $this->title        = $this->get_option('title');
        $this->description  = $this->get_option('description');
        $this->enabled      = $this->get_option('enabled');
        $this->testmode     = $this->get_option('testmode') === 'yes';
        $this->api_key      = $this->get_option('api_key');
        $this->api_secret   = $this->get_option('api_secret');

        // Save settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);

        // Payment listener/hooks
        add_action('woocommerce_api_' . $this->id, [$this, 'webhook_handler']);
    }

    /**
     * Initialize gateway settings form fields
     */
    public function init_form_fields(): void {
        $this->form_fields = [
            'enabled' => [
                'title'   => __('Enable/Disable', 'my-wc-extension'),
                'type'    => 'checkbox',
                'label'   => __('Enable this payment gateway', 'my-wc-extension'),
                'default' => 'yes',
            ],
            'title' => [
                'title'       => __('Title', 'my-wc-extension'),
                'type'        => 'text',
                'description' => __('Payment method title displayed to customers', 'my-wc-extension'),
                'default'     => __('My Custom Payment', 'my-wc-extension'),
                'desc_tip'    => true,
            ],
            'description' => [
                'title'       => __('Description', 'my-wc-extension'),
                'type'        => 'textarea',
                'description' => __('Payment method description', 'my-wc-extension'),
                'default'     => __('Pay securely using our custom payment gateway.', 'my-wc-extension'),
            ],
            'testmode' => [
                'title'       => __('Test Mode', 'my-wc-extension'),
                'type'        => 'checkbox',
                'label'       => __('Enable test mode', 'my-wc-extension'),
                'default'     => 'yes',
                'description' => __('Place gateway in test mode using test API keys.', 'my-wc-extension'),
            ],
            'api_key' => [
                'title'       => __('API Key', 'my-wc-extension'),
                'type'        => 'text',
                'description' => __('Your API key from the payment provider', 'my-wc-extension'),
            ],
            'api_secret' => [
                'title'       => __('API Secret', 'my-wc-extension'),
                'type'        => 'password',
                'description' => __('Your API secret from the payment provider', 'my-wc-extension'),
            ],
        ];
    }

    /**
     * Payment fields displayed on checkout
     */
    public function payment_fields(): void {
        if ($this->description) {
            echo '<p>' . wp_kses_post($this->description) . '</p>';
        }

        if ($this->testmode) {
            echo '<p><strong>' . esc_html__('TEST MODE ENABLED', 'my-wc-extension') . '</strong></p>';
        }

        // Custom payment fields
        echo '<div class="my-custom-payment-fields">';
        echo '<p class="form-row form-row-wide">';
        echo '<label for="my_custom_card">' . esc_html__('Card Number', 'my-wc-extension') . '</label>';
        echo '<input type="text" id="my_custom_card" name="my_custom_card" class="input-text" placeholder="XXXX-XXXX-XXXX-XXXX">';
        echo '</p>';
        echo '</div>';
    }

    /**
     * Process payment
     */
    public function process_payment(int $order_id): array {
        $order = wc_get_order($order_id);

        // Get payment data
        $card_number = isset($_POST['my_custom_card']) 
            ? sanitize_text_field($_POST['my_custom_card']) 
            : '';

        // Validate payment data
        if (empty($card_number)) {
            wc_add_notice(__('Payment error: Please provide card number', 'my-wc-extension'), 'error');
            return ['result' => 'failure'];
        }

        // Call payment API
        $payment_result = $this->process_payment_api($order, $card_number);

        if ($payment_result['success']) {
            // Payment successful
            $order->payment_complete($payment_result['transaction_id']);
            $order->add_order_note(
                sprintf('Payment completed via %s. Transaction ID: %s', $this->title, $payment_result['transaction_id'])
            );

            // Empty cart
            WC()->cart->empty_cart();

            // Redirect to thank you page
            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            ];
        } else {
            // Payment failed
            wc_add_notice($payment_result['message'], 'error');
            $order->add_order_note('Payment failed: ' . $payment_result['message']);

            return ['result' => 'failure'];
        }
    }

    /**
     * Process payment via API
     */
    private function process_payment_api(\WC_Order $order, string $card_number): array {
        $amount = $order->get_total();
        $currency = $order->get_currency();
        
        $api_url = $this->testmode 
            ? 'https://api-test.example.com/payments' 
            : 'https://api.example.com/payments';
        
        $body = [
            'amount'      => $amount,
            'currency'    => $currency,
            'card_number' => $card_number,
            'order_id'    => $order->get_id(),
            'description' => 'Order #' . $order->get_order_number(),
        ];
        
        $response = wp_remote_post($api_url, [
            'method'  => 'POST',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type'  => 'application/json',
            ],
            'body'    => json_encode($body),
        ]);
        
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($data['status'] === 'success') {
            return [
                'success'       => true,
                'transaction_id' => $data['transaction_id'],
            ];
        }
        
        return [
            'success' => false,
            'message' => $data['error_message'] ?? 'Unknown error',
        ];
    }

    /**
     * Process refund
     */
    public function process_refund(int $order_id, float $amount = null, string $reason = ''): bool {
        $order = wc_get_order($order_id);

        if (!$order) {
            return false;
        }

        // Call refund API
        $refund_result = $this->process_refund_api($order, $amount, $reason);

        if ($refund_result['success']) {
            $order->add_order_note(
                sprintf('Refunded %s via %s. Transaction ID: %s. Reason: %s', 
                    wc_price($amount), 
                    $this->title, 
                    $refund_result['refund_id'],
                    $reason
                )
            );
            return true;
        }

        $order->add_order_note('Refund failed: ' . $refund_result['message']);
        return false;
    }

    /**
     * Process refund via API
     */
    private function process_refund_api(\WC_Order $order, float $amount, string $reason): array {
        $transaction_id = $order->get_transaction_id();

        $api_url = $this->testmode 
            ? 'https://api-test.example.com/refunds' 
            : 'https://api.example.com/refunds';

        $body = [
            'transaction_id' => $transaction_id,
            'amount'         => $amount,
            'reason'         => $reason,
        ];

        $response = wp_remote_post($api_url, [
            'method'  => 'POST',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type'  => 'application/json',
            ],
            'body'    => json_encode($body),
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data['status'] === 'success'
            ? ['success' => true, 'refund_id' => $data['refund_id']]
            : ['success' => false, 'message' => $data['error_message'] ?? 'Refund failed'];
    }

    /**
     * Webhook handler for payment notifications
     */
    public function webhook_handler(): void {
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        // Verify webhook signature (example)
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $expected_signature = hash_hmac('sha256', $payload, $this->api_secret);

        if ($signature !== $expected_signature) {
            status_header(401);
            exit('Invalid signature');
        }

        // Process webhook event
        $event_type = $data['event_type'] ?? '';
        $order_id = $data['order_id'] ?? 0;
        $order = wc_get_order($order_id);

        if (!$order) {
            status_header(404);
            exit('Order not found');
        }

        switch ($event_type) {
            case 'payment_completed':
                $order->payment_complete($data['transaction_id']);
                break;
            case 'payment_failed':
                $order->update_status('failed', 'Payment failed via webhook');
                break;
            case 'refund_completed':
                $order->add_order_note('Refund completed via webhook');
                break;
        }

        status_header(200);
        exit('Webhook processed');
    }
}

// Register gateway
add_filter('woocommerce_payment_gateways', function (array $gateways): array {
    $gateways[] = Custom_Payment_Gateway::class;
    return $gateways;
});
```

### Custom Shipping Method

```php
<?php
/**
 * Custom Shipping Method Implementation
 */

namespace MyWCExtension;

class Custom_Shipping_Method extends \WC_Shipping_Method {
    /**
     * Shipping method ID
     */
    const METHOD_ID = 'my_custom_shipping';

    /**
     * Constructor
     */
    public function __construct(int $instance_id = 0) {
        $this->id                 = self::METHOD_ID;
        $this->instance_id        = absint($instance_id);
        $this->method_title       = __('My Custom Shipping', 'my-wc-extension');
        $this->method_description = __('Custom shipping method description', 'my-wc-extension');
        $this->supports           = [
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        ];

        // Load settings
        $this->init_form_fields();
        $this->init_instance_form_fields();

        // Get setting values
        $this->title        = $this->get_option('title');
        $this->enabled      = $this->get_option('enabled') === 'yes';

        // Save settings
        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
        add_action('woocommerce_update_options_shipping_' . $this->id . '_instance_' . $this->instance_id, [$this, 'process_admin_options']);
    }

    /**
     * Initialize global settings form fields
     */
    public function init_form_fields(): void {
        $this->form_fields = [
            'enabled' => [
                'title'   => __('Enable/Disable', 'my-wc-extension'),
                'type'    => 'checkbox',
                'label'   => __('Enable this shipping method', 'my-wc-extension'),
                'default' => 'yes',
            ],
        ];
    }

    /**
     * Initialize instance settings form fields
     */
    public function init_instance_form_fields(): void {
        $this->instance_form_fields = [
            'title' => [
                'title'       => __('Title', 'my-wc-extension'),
                'type'        => 'text',
                'description' => __('Shipping method title displayed to customers', 'my-wc-extension'),
                'default'     => __('Custom Shipping', 'my-wc-extension'),
            ],
            'cost' => [
                'title'       => __('Cost', 'my-wc-extension'),
                'type'        => 'number',
                'description' => __('Base shipping cost', 'my-wc-extension'),
                'default'     => 10,
                'desc_tip'    => true,
            ],
            'weight_based' => [
                'title'       => __('Weight-Based Pricing', 'my-wc-extension'),
                'type'        => 'checkbox',
                'label'       => __('Enable weight-based pricing', 'my-wc-extension'),
                'default'     => 'no',
            ],
            'weight_threshold' => [
                'title'       => __('Weight Threshold (kg)', 'my-wc-extension'),
                'type'        => 'number',
                'description' => __('Weight threshold for extra charge', 'my-wc-extension'),
                'default'     => 5,
                'desc_tip'    => true,
            ],
            'extra_cost' => [
                'title'       => __('Extra Cost (over threshold)', 'my-wc-extension'),
                'type'        => 'number',
                'description' => __('Extra cost for weight over threshold', 'my-wc-extension'),
                'default'     => 5,
                'desc_tip'    => true,
            ],
            'free_shipping_threshold' => [
                'title'       => __('Free Shipping Threshold', 'my-wc-extension'),
                'type'        => 'number',
                'description' => __('Order total for free shipping (leave empty to disable)', 'my-wc-extension'),
                'default'     => '',
                'desc_tip'    => true,
            ],
        ];
    }

    /**
     * Calculate shipping cost
     */
    public function calculate_shipping(array $package = []): void {
        $base_cost = floatval($this->get_instance_option('cost'));
        $total_weight = 0;
        $package_contents_total = 0;

        // Calculate package weight and total
        foreach ($package['contents'] as $item_id => $values) {
            if ($values['data']->needs_shipping()) {
                $product_weight = $values['data']->get_weight();
                if ($product_weight > 0) {
                    $total_weight += floatval($product_weight) * floatval($values['quantity']);
                }
                $package_contents_total += floatval($values['line_total']);
            }
        }

        // Check for free shipping threshold
        $free_threshold = floatval($this->get_instance_option('free_shipping_threshold'));
        if ($free_threshold > 0 && $package_contents_total >= $free_threshold) {
            $this->add_rate([
                'id'    => $this->get_instance_option_id(),
                'label' => $this->title . ' (Free)',
                'cost'  => 0,
            ]);
            return;
        }

        // Weight-based pricing
        if ($this->get_instance_option('weight_based') === 'yes') {
            $weight_threshold = floatval($this->get_instance_option('weight_threshold'));
            $extra_cost = floatval($this->get_instance_option('extra_cost'));

            if ($total_weight > $weight_threshold) {
                $extra_weight = $total_weight - $weight_threshold;
                $additional_cost = ceil($extra_weight / 1) * $extra_cost;
                $base_cost += $additional_cost;
            }
        }

        // Add shipping rate
        $this->add_rate([
            'id'    => $this->get_instance_option_id(),
            'label' => $this->title,
            'cost'  => $base_cost,
            'package' => $package,
        ]);
    }
}

// Register shipping method
add_filter('woocommerce_shipping_methods', function (array $methods): array {
    $methods[Custom_Shipping_Method::METHOD_ID] = Custom_Shipping_Method::class;
    return $methods;
});
```

### Custom Checkout Fields

```php
<?php
/**
 * Custom Checkout Fields
 */

namespace MyWCExtension;

class Checkout {
    /**
     * Add custom checkout fields
     */
    public function add_custom_fields(): void {
        // Add field after billing email
        add_action('woocommerce_after_checkout_billing_form', [$this, 'custom_billing_field']);

        // Add field before order notes
        add_action('woocommerce_before_order_notes', [$this, 'custom_order_field']);

        // Validate custom fields
        add_action('woocommerce_checkout_process', [$this, 'validate_custom_fields']);

        // Save custom fields
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_custom_fields']);

        // Display custom fields in admin
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_admin_fields']);

        // Display in emails
        add_filter('woocommerce_email_order_meta_fields', [$this, 'email_custom_fields'], 10, 3);

        // Display in My Account
        add_action('woocommerce_order_details_after_order_table_items', [$this, 'display_order_fields']);
    }

    /**
     * Custom billing field
     */
    public function custom_billing_field(\WC_Checkout $checkout): void {
        echo '<div class="custom-billing-field">';

        woocommerce_form_field('mywc_billing_custom', [
            'type'        => 'text',
            'class'       => ['form-row-wide'],
            'label'       => __('Custom Billing Field', 'my-wc-extension'),
            'placeholder' => __('Enter value...', 'my-wc-extension'),
            'required'    => false,
        ], $checkout->get_value('mywc_billing_custom'));

        echo '</div>';
    }

    /**
     * Custom order field
     */
    public function custom_order_field(\WC_Checkout $checkout): void {
        echo '<div class="custom-order-field">';

        woocommerce_form_field('mywc_delivery_date', [
            'type'        => 'date',
            'class'       => ['form-row-wide'],
            'label'       => __('Preferred Delivery Date', 'my-wc-extension'),
            'placeholder' => __('Select delivery date...', 'my-wc-extension'),
            'required'    => true,
        ], $checkout->get_value('mywc_delivery_date'));

        woocommerce_form_field('mywc_gift_wrap', [
            'type'        => 'checkbox',
            'class'       => ['form-row-wide'],
            'label'       => __('Add gift wrapping (+$5)', 'my-wc-extension'),
            'required'    => false,
        ], $checkout->get_value('mywc_gift_wrap'));

        echo '</div>';
    }

    /**
     * Validate custom fields
     */
    public function validate_custom_fields(): void {
        // Validate delivery date
        if (isset($_POST['mywc_delivery_date']) && empty($_POST['mywc_delivery_date'])) {
            wc_add_notice(__('Please select a delivery date.', 'my-wc-extension'), 'error');
        }

        // Validate date is not in past
        if (isset($_POST['mywc_delivery_date'])) {
            $delivery_date = sanitize_text_field($_POST['mywc_delivery_date']);
            $today = date('Y-m-d');
            
            if ($delivery_date < $today) {
                wc_add_notice(__('Delivery date cannot be in the past.', 'my-wc-extension'), 'error');
            }
        }
    }

    /**
     * Save custom fields to order meta
     */
    public function save_custom_fields(int $order_id): void {
        if (isset($_POST['mywc_billing_custom'])) {
            update_post_meta($order_id, '_mywc_billing_custom', sanitize_text_field($_POST['mywc_billing_custom']));
        }

        if (isset($_POST['mywc_delivery_date'])) {
            update_post_meta($order_id, '_mywc_delivery_date', sanitize_text_field($_POST['mywc_delivery_date']));
        }

        // Gift wrap checkbox
        $gift_wrap = isset($_POST['mywc_gift_wrap']) ? 'yes' : 'no';
        update_post_meta($order_id, '_mywc_gift_wrap', $gift_wrap);

        // Add gift wrap fee if selected
        if ($gift_wrap === 'yes') {
            $order = wc_get_order($order_id);
            $fee = new \WC_Order_Item_Fee();
            $fee->set_name(__('Gift Wrapping', 'my-wc-extension'));
            $fee->set_amount(5);
            $fee->set_total(5);
            $order->add_item($fee);
            $order->calculate_totals();
            $order->save();
        }
    }

    /**
     * Display custom fields in admin order details
     */
    public function display_admin_fields(\WC_Order $order): void {
        $billing_custom = $order->get_meta('_mywc_billing_custom');
        $delivery_date = $order->get_meta('_mywc_delivery_date');
        $gift_wrap = $order->get_meta('_mywc_gift_wrap');

        echo '<div class="custom-order-admin-fields">';
        echo '<h3>' . esc_html__('Custom Fields', 'my-wc-extension') . '</h3>';

        if ($billing_custom) {
            echo '<p><strong>' . esc_html__('Custom Billing:', 'my-wc-extension') . '</strong> ' . esc_html($billing_custom) . '</p>';
        }

        if ($delivery_date) {
            echo '<p><strong>' . esc_html__('Delivery Date:', 'my-wc-extension') . '</strong> ' . esc_html($delivery_date) . '</p>';
        }

        if ($gift_wrap === 'yes') {
            echo '<p><strong>' . esc_html__('Gift Wrapping:', 'my-wc-extension') . '</strong> ' . esc_html__('Yes', 'my-wc-extension') . '</p>';
        }

        echo '</div>';
    }

    /**
     * Display custom fields in emails
     */
    public function email_custom_fields(array $fields, bool $sent_to_admin, \WC_Order $order): array {
        $delivery_date = $order->get_meta('_mywc_delivery_date');
        $gift_wrap = $order->get_meta('_mywc_gift_wrap');

        if ($delivery_date) {
            $fields['delivery_date'] = [
                'label' => __('Delivery Date', 'my-wc-extension'),
                'value' => $delivery_date,
            ];
        }

        if ($gift_wrap === 'yes') {
            $fields['gift_wrap'] = [
                'label' => __('Gift Wrapping', 'my-wc-extension'),
                'value' => __('Yes', 'my-wc-extension'),
            ];
        }

        return $fields;
    }
}
```

### WooCommerce REST API Extension

```php
<?php
/**
 * WooCommerce REST API Extension
 */

namespace MyWCExtension;

class REST_API {
    /**
     * Register custom REST API routes
     */
    public function register_routes(): void {
        register_rest_route('mywc/v1', '/products/custom', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this, 'get_custom_products'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route('mywc/v1', '/orders/(?P<id>\d+)/custom-data', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this, 'get_order_custom_data'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route('mywc/v1', '/orders/(?P<id>\d+)/custom-data', [
            'methods'  => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'update_order_custom_data'],
            'permission_callback' => [$this, 'permission_check'],
            'args'     => [
                'custom_data' => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ]);
    }

    /**
     * Permission check for authenticated requests
     */
    public function permission_check(): bool {
        return current_user_can('manage_woocommerce');
    }

    /**
     * Get custom products endpoint
     */
    public function get_custom_products(\WP_REST_Request $request): \WP_REST_Response {
        $args = [
            'status'  => 'publish',
            'limit'   => $request->get_param('limit') ?? 10,
            'orderby' => $request->get_param('orderby') ?? 'date',
            'order'   => $request->get_param('order') ?? 'DESC',
            'type'    => $request->get_param('type') ?? [],
        ];

        $products = wc_get_products($args);

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id'          => $product->get_id(),
                'name'        => $product->get_name(),
                'price'       => $product->get_price(),
                'stock_status' => $product->get_stock_status(),
                'custom_meta' => $product->get_meta('_mywc_custom_product_data'),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'count'   => count($data),
            'data'    => $data,
        ]);
    }

    /**
     * Get order custom data endpoint
     */
    public function get_order_custom_data(\WP_REST_Request $request): \WP_REST_Response {
        $order_id = absint($request->get_param('id'));
        $order = wc_get_order($order_id);

        if (!$order) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $custom_data = $order->get_meta('_mywc_order_custom_data');

        return rest_ensure_response([
            'success' => true,
            'order_id' => $order_id,
            'custom_data' => $custom_data,
        ]);
    }

    /**
     * Update order custom data endpoint
     */
    public function update_order_custom_data(\WP_REST_Request $request): \WP_REST_Response {
        $order_id = absint($request->get_param('id'));
        $order = wc_get_order($order_id);

        if (!$order) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $custom_data = sanitize_text_field($request->get_param('custom_data'));

        $order->update_meta_data('_mywc_order_custom_data', $custom_data);
        $order->add_order_note('Custom data updated via API: ' . $custom_data);
        $order->save();

        return rest_ensure_response([
            'success' => true,
            'order_id' => $order_id,
            'custom_data' => $custom_data,
        ]);
    }
}

// Register REST API routes
add_action('rest_api_init', function () {
    $api = new REST_API();
    $api->register_routes();
});
```

## WooCommerce Best Practices

### Performance

- Use transients for caching expensive operations
- Optimize product queries (use wc_get_products)
- Cache cart calculations when possible
- Use Action Scheduler for background tasks
- Optimize database queries with indexes

### Security

- Always verify user capabilities
- Sanitize and validate all input
- Use nonces for AJAX operations
- Escape all output
- Never trust customer input

### Compatibility

- Check WooCommerce version requirements
- Use WooCommerce CRUD classes instead of direct DB
- Follow WooCommerce coding standards
- Test with major WooCommerce versions
- Handle deprecation warnings

### Extensibility

- Use hooks for customization (don't modify core)
- Provide filters for custom behavior
- Document your hooks clearly
- Use dependency injection where possible

## Reference

- WooCommerce Developer Documentation: https://developer.woocommerce.com/
- WooCommerce CRUD Classes: https://developer.woocommerce.com/documentation/utilities/crud-objects/
- WooCommerce Hooks Reference: https://woocommerce.github.io/code-reference/hooks/hooks.html
- WooCommerce REST API: https://developer.woocommerce.com/documentation/rest-api/
- WooCommerce Payment Gateways: https://developer.woocommerce.com/documentation/payment-gateway-sdk/
- WooCommerce Shipping Methods: https://developer.woocommerce.com/documentation/extension-development/shipping-methods/

**Remember**: Great WooCommerce extensions use WooCommerce APIs, follow coding standards, and are secure, performant, and maintainable. Use hooks, CRUD classes, and REST API for maximum compatibility.