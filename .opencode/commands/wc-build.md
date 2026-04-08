# WooCommerce Development Workflow

Comprehensive WooCommerce extension development and review workflow.

## Task

$ARGUMENTS

## Output Directory

**IMPORTANT**: All WooCommerce extension files MUST be saved in the `wp-content/plugins/` directory relative to the WordPress project root.

- Create the plugin directory at: `wp-content/plugins/{plugin-name}/`
- WooCommerce extensions are plugins and follow the same directory structure
- Do NOT save files in any other location (examples/, current directory, etc.)

## WooCommerce Context Check

Before proceeding, verify:
- WooCommerce is active
- WooCommerce version compatibility
- WordPress version compatibility
- PHP version compatibility

```php
// Check WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}

// Check WooCommerce version
if ( version_compare( WC_VERSION, '8.0', '<' ) ) {
    // Handle version mismatch
}
```

## Development Areas

### 1. Product Operations

**Product CRUD**:
```php
// Get product
$product = wc_get_product( $product_id );

// Create simple product
$product = new WC_Product_Simple();
$product->set_name( 'Product Name' );
$product->set_regular_price( '19.99' );
$product->set_description( 'Description' );
$product->set_sku( 'SKU-123' );
$product_id = $product->save();

// Create variable product
$product = new WC_Product_Variable();
$product->set_name( 'Variable Product' );
$parent_id = $product->save();

// Create variation
$variation = new WC_Product_Variation();
$variation->set_parent_id( $parent_id );
$variation->set_attributes( [ 'color' => 'blue' ] );
$variation->set_regular_price( '29.99' );
$variation->save();

// Update product
$product->set_price( '24.99' );
$product->save();

// Delete product
$product->delete( true ); // true = force delete
```

**Checklist**:
- [ ] Use WooCommerce CRUD classes
- [ ] Proper data validation
- [ ] Handle variations correctly
- [ ] Manage stock appropriately

### 2. Order Management

**Order Operations**:
```php
// Create order
$order = new WC_Order();
$order->set_billing_first_name( 'John' );
$order->set_billing_last_name( 'Doe' );
$order->set_billing_email( 'john@example.com' );
$order->add_product( wc_get_product( $product_id ), $quantity );
$order->calculate_totals();
$order->set_status( 'pending' );
$order_id = $order->save();

// Get order
$order = wc_get_order( $order_id );

// Update order status
$order->update_status( 'completed', 'Order completed.' );

// Add order note
$order->add_order_note( 'Custom note' );

// Get order items
$items = $order->get_items();
foreach ( $items as $item_id => $item ) {
    $product_name = $item->get_name();
    $quantity = $item->get_quantity();
}

// Refund order
$refund = wc_create_refund( [
    'amount'   => $amount,
    'order_id' => $order_id,
    'reason'   => 'Customer request',
] );
```

**Checklist**:
- [ ] Proper order creation workflow
- [ ] Calculate totals correctly
- [ ] Handle status transitions
- [ ] Error handling for failures

### 3. Cart Operations

**Cart Manipulation**:
```php
// Add to cart
$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );

// Remove from cart
WC()->cart->remove_cart_item( $cart_item_key );

// Update cart item quantity
WC()->cart->set_quantity( $cart_item_key, $new_quantity );

// Empty cart
WC()->cart->empty_cart();

// Get cart contents
foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $product = $cart_item['data'];
    $quantity = $cart_item['quantity'];
}

// Calculate totals
WC()->cart->calculate_totals();

// Apply coupon
WC()->cart->apply_coupon( 'coupon_code' );

// Get cart total
$total = WC()->cart->get_totals();
```

**Checklist**:
- [ ] Check cart exists before operations
- [ ] Validate product before adding
- [ ] Handle stock checks
- [ ] Recalculate totals after changes

### 4. WooCommerce Hooks

**Common Hooks**:

```php
// Product hooks
add_action( 'woocommerce_before_add_to_cart_button', 'my_function' );
add_filter( 'woocommerce_product_get_price', 'my_price_filter', 10, 2 );

// Cart hooks
add_action( 'woocommerce_add_to_cart', 'my_cart_function', 10, 6 );
add_filter( 'woocommerce_add_to_cart_validation', 'my_validation', 10, 3 );

// Checkout hooks
add_action( 'woocommerce_before_checkout_form', 'my_function' );
add_filter( 'woocommerce_checkout_fields', 'my_fields_filter' );

// Order hooks
add_action( 'woocommerce_order_status_completed', 'my_order_function' );
add_action( 'woocommerce_payment_complete', 'my_payment_function' );

// Email hooks
add_filter( 'woocommerce_email_headers', 'my_email_headers', 10, 3 );
add_action( 'woocommerce_email_before_order_table', 'my_email_content' );
```

**Checklist**:
- [ ] Use appropriate hook priorities
- [ ] Return values in filters
- [ ] Proper parameter handling
- [ ] Avoid infinite loops

### 5. Custom Payment Gateway

**Gateway Structure**:
```php
class My_Payment_Gateway extends WC_Payment_Gateway {
    public function __construct() {
        $this->id                 = 'my_gateway';
        $this->method_title       = 'My Gateway';
        $this->method_description = 'Payment gateway description';
        
        $this->init_form_fields();
        $this->init_settings();
        
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
    }
    
    public function init_form_fields() {
        $this->form_fields = [
            'enabled' => [
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable gateway',
                'default' => 'yes',
            ],
        ];
    }
    
    public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        
        // Process payment
        
        $order->payment_complete();
        
        return [
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        ];
    }
}

add_filter( 'woocommerce_payment_gateways', function( $methods ) {
    $methods[] = 'My_Payment_Gateway';
    return $methods;
} );
```

**Checklist**:
- [ ] Extend WC_Payment_Gateway
- [ ] Implement required methods
- [ ] Handle payment errors
- [ ] Proper order status updates
- [ ] Webhook handling

### 6. Custom Shipping Method

**Shipping Structure**:
```php
class My_Shipping_Method extends WC_Shipping_Method {
    public function __construct( $instance_id = 0 ) {
        $this->id                 = 'my_shipping';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = 'My Shipping';
        
        $this->init();
    }
    
    public function calculate_shipping( $package = [] ) {
        $rate = [
            'id'    => $this->get_rate_id(),
            'label' => $this->title,
            'cost'  => 10,
        ];
        
        $this->add_rate( $rate );
    }
}

add_filter( 'woocommerce_shipping_methods', function( $methods ) {
    $methods['my_shipping'] = 'My_Shipping_Method';
    return $methods;
} );
```

**Checklist**:
- [ ] Extend WC_Shipping_Method
- [ ] Implement calculate_shipping
- [ ] Handle shipping zones
- [ ] Proper rate calculation

### 7. REST API Integration

**WooCommerce REST API**:
```php
register_rest_route( 'wc/v3', '/my-endpoint', [
    'methods'  => 'GET',
    'callback' => 'my_handler',
    'permission_callback' => function() {
        return current_user_can( 'manage_woocommerce' );
    },
] );

// Use WooCommerce REST API client
use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'https://example.com',
    'consumer_key',
    'consumer_secret',
    [ 'version' => 'wc/v3' ]
);

$products = $woocommerce->get( 'products' );
```

**Checklist**:
- [ ] Proper authentication
- [ ] Permission checks
- [ ] Error handling
- [ ] Response formatting

### 8. WooCommerce Admin Settings

**Add Settings Tab**:
```php
add_filter( 'woocommerce_settings_tabs_array', function( $tabs ) {
    $tabs['my_tab'] = 'My Settings';
    return $tabs;
}, 50 );

add_action( 'woocommerce_settings_tabs_my_tab', function() {
    woocommerce_admin_fields( get_my_settings() );
} );

add_action( 'woocommerce_update_options_my_tab', function() {
    woocommerce_update_options( get_my_settings() );
} );

function get_my_settings() {
    return [
        [
            'name' => 'My Setting',
            'type' => 'title',
            'desc' => '',
            'id'   => 'my_settings',
        ],
        [
            'name'    => 'Enable Feature',
            'id'      => 'my_feature_enabled',
            'type'    => 'checkbox',
            'default' => 'no',
        ],
        [
            'type' => 'sectionend',
            'id'   => 'my_settings',
        ],
    ];
}
```

**Checklist**:
- [ ] Use WooCommerce settings API
- [ ] Proper settings registration
- [ ] Sanitization
- [ ] Default values

### 9. Custom Product Type

**Register Product Type**:
```php
add_filter( 'woocommerce_product_class', function( $classname, $product_type ) {
    if ( $product_type === 'my_type' ) {
        $classname = 'WC_Product_My_Type';
    }
    return $classname;
}, 10, 2 );

class WC_Product_My_Type extends WC_Product {
    public function __construct( $product ) {
        $this->product_type = 'my_type';
        parent::__construct( $product );
    }
}

add_filter( 'product_type_selector', function( $types ) {
    $types['my_type'] = 'My Type';
    return $types;
} );
```

**Checklist**:
- [ ] Extend WC_Product
- [ ] Register product type
- [ ] Add to selector
- [ ] Custom metaboxes

### 10. WooCommerce Security

**Security Checklist**:
- [ ] Verify nonces in forms
- [ ] Check capabilities (`manage_woocommerce`)
- [ ] Sanitize and validate input
- [ ] Escape output
- [ ] Use prepared statements
- [ ] Validate payment data
- [ ] Secure webhook signatures

### 11. Performance

**Optimization**:
- [ ] Cache expensive queries
- [ ] Use transients for data
- [ ] Optimize product queries
- [ ] Limit API requests
- [ ] Batch operations

### 12. Testing

**WooCommerce Testing**:
- [ ] Test with different product types
- [ ] Test checkout flow
- [ ] Test with various payment gateways
- [ ] Test with different currencies
- [ ] Test with various shipping methods
- [ ] Test with WP_DEBUG enabled

## Output Format

```markdown
# WooCommerce Development Report

## Summary
[Overall assessment]

## Implementation
[What was built/modified]

## WooCommerce Compatibility
- WooCommerce version: [X.X+]
- WordPress version: [X.X+]
- PHP version: [X.X+]

## Features Implemented
1. [Feature]: [Description]

## Hooks Used
- [Hook name]: [Purpose]

## Security Considerations
[Security measures taken]

## Testing Notes
[How to test]

## Performance
[Optimizations applied]

## Recommendations
[Improvements]
```

## Common Issues

### Product Not Found
```php
$product = wc_get_product( $id );
if ( ! $product ) {
    return new WP_Error( 'invalid_product', 'Product not found' );
}
```

### Order Not Found
```php
$order = wc_get_order( $id );
if ( ! $order ) {
    return new WP_Error( 'invalid_order', 'Order not found' );
}
```

### Cart Empty Check
```php
if ( WC()->cart && ! WC()->cart->is_empty() ) {
    // Process cart
}
```

## Resources

- WooCommerce Developer Documentation: https://developer.woocommerce.com/
- WooCommerce REST API: https://woocommerce.github.io/woocommerce-rest-api-docs/
- WooCommerce Hooks Reference: https://woocommerce.github.io/code-reference/

Use `wordpress-reviewer` agent for security and general code review.