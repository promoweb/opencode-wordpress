# OpenCode WooCommerce Extension

A comprehensive WooCommerce extension demonstrating best practices for WooCommerce development, including custom product types, payment gateways, and order handling.

## Features

- **Custom Product Type**: New product type "OpenCode Custom" with dedicated fields
- **Custom Payment Gateway**: Full payment gateway implementation with test mode
- **Order Handling**: Custom order meta, validation, and status handling
- **Admin Integration**: Custom admin pages, settings, and order displays
- **Email Integration**: Custom email templates and order meta
- **My Account**: Custom columns and order details display
- **Fees**: Custom fee handling for cart and orders

## Plugin Structure

```
opencode-wc-extension/
├── includes/
│   ├── class-main.php          # Main extension class
│   ├── class-gateway.php       # Custom payment gateway
│   ├── class-product-type.php  # Custom product type
│   └── class-order.php         # Order handling
├── assets/
│   ├── css/
│   │   ├── admin.css           # Admin stylesheet
│   │   └── frontend.css        # Frontend stylesheet
│   ├── js/
│   │   ├── admin.js            # Admin JavaScript
│   │   └── frontend.js         # Frontend JavaScript
│   └── images/
│       └── gateway-icon.png    # Gateway icon
├── templates/
│   ├── emails/
│   │   └── custom-order-meta.php
│   └── order/
│       └── custom-details.php
├── languages/                   # Translation files
├── wc-extension.php            # Main plugin file
└── README.md                   # This file
```

## Requirements

- WordPress 5.8 or higher
- WooCommerce 6.0 or higher
- PHP 7.4 or higher

## Installation

### Manual Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin in WordPress Dashboard → Plugins
3. WooCommerce must be installed and active
4. Configure settings in WooCommerce → Settings → Products → OpenCode Settings

### Composer Installation

```bash
composer require opencode/wc-extension
```

## Configuration

### WooCommerce Settings

Navigate to **WooCommerce → Settings → Products → OpenCode Settings**:

- **Enable Custom Features**: Toggle custom product types and features
- **Custom Fee Amount**: Set a fixed fee to add to cart
- **Custom Fee Name**: Label for the custom fee

### Payment Gateway Settings

Navigate to **WooCommerce → Settings → Payments → OpenCode Custom Gateway**:

- **Enable/Disable**: Toggle the payment gateway
- **Title**: Payment method title shown to customers
- **Description**: Payment method description
- **Test Mode**: Enable test mode for development
- **API Key**: Your payment gateway API key
- **API Secret**: Your payment gateway API secret

## Usage

### Custom Product Type

Create products with the new "OpenCode Custom" product type:

1. Go to **Products → Add New**
2. Select "OpenCode Custom" from the product type dropdown
3. Configure custom fields:
   - **Custom Attribute**: Product-specific attribute
   - **Custom Type**: Standard, Premium, or Exclusive
   - **Enable Custom Features**: Toggle additional features
   - **Custom Notes**: Internal notes

### Get Product Custom Data

```php
// Get custom attribute
$attribute = \OpenCode_WC_Extension\Product_Type::get_custom_attribute( $product_id );

// Get custom type
$type = \OpenCode_WC_Extension\Product_Type::get_custom_type( $product_id );

// Check if custom features enabled
$enabled = \OpenCode_WC_Extension\Product_Type::is_custom_enabled( $product_id );

// Get custom notes
$notes = \OpenCode_WC_Extension\Product_Type::get_custom_notes( $product_id );
```

### Order Handling

Add custom fields to checkout:

```php
// Add custom checkout field
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    $fields['billing']['billing_custom_field'] = array(
        'type'        => 'text',
        'label'       => 'Custom Billing Field',
        'placeholder' => 'Enter custom information',
        'required'    => false,
        'class'       => array( 'form-row-wide' ),
        'priority'    => 120,
    );
    return $fields;
} );
```

### Get Order Custom Data

```php
// Get custom field from order
$custom_field = \OpenCode_WC_Extension\Order_Handler::get_order_custom_field( $order_id );

// Get order source
$source = \OpenCode_WC_Extension\Order_Handler::get_order_source( $order_id );

// Check if order has custom items
$has_custom = \OpenCode_WC_Extension\Order_Handler::has_custom_items( $order_id );
```

### Payment Gateway

Process payments programmatically:

```php
// Get gateway instance
$gateways = WC()->payment_gateways->get_available_payment_gateways();
$gateway = $gateways['opencode_custom_gateway'];

// Process payment
$result = $gateway->process_payment( $order_id );

if ( $result['result'] === 'success' ) {
    // Payment successful
    $redirect_url = $result['redirect'];
} else {
    // Payment failed
    // Handle error
}
```

### Custom Fees

Add fees to cart programmatically:

```php
add_action( 'woocommerce_cart_calculate_fees', function( $cart ) {
    $cart->add_fee( 'Custom Fee', 10, true, 'standard' );
} );
```

## Hooks

### Actions

```php
// Order processing
add_action( 'opencode_wc_order_processing', function( $order_id, $order ) {
    // Custom logic when order moves to processing
} );

// Order cancelled
add_action( 'opencode_wc_order_cancelled', function( $order_id, $order ) {
    // Custom logic when order is cancelled
} );

// Order refunded
add_action( 'opencode_wc_order_refunded', function( $order_id, $order, $amount ) {
    // Custom logic when order is refunded
} );
```

### Filters

```php
// Customize product class
add_filter( 'woocommerce_product_class', function( $classname, $product_type, $product_id ) {
    // Return custom classname
    return $classname;
}, 10, 3 );

// Add product types
add_filter( 'woocommerce_product_types', function( $types ) {
    $types['my_custom_type'] = 'My Custom Type';
    return $types;
} );

// Customize email order meta
add_filter( 'woocommerce_email_order_meta_fields', function( $fields, $sent_to_admin, $order ) {
    $fields['my_field'] = array(
        'label' => 'My Field',
        'value' => $order->get_meta( '_my_field' ),
    );
    return $fields;
}, 10, 3 );
```

## Templates

Override WooCommerce templates by copying them to your theme:

```
your-theme/
└── woocommerce/
    ├── emails/
    │   └── custom-order-meta.php
    └── order/
        └── custom-details.php
```

## API Integration

### Payment API

The gateway integrates with external payment APIs:

```php
// Process payment
$payload = array(
    'amount'      => $order->get_total(),
    'currency'    => get_woocommerce_currency(),
    'order_id'    => $order->get_id(),
    'card_number' => $card_number,
    'card_expiry' => $card_expiry,
    'card_cvc'    => $card_cvc,
);

$response = wp_remote_post( 'https://api.example.com/payments', array(
    'method'  => 'POST',
    'body'    => json_encode( $payload ),
    'headers' => array(
        'Content-Type'  => 'application/json',
        'Authorization' => 'Bearer ' . $api_secret,
    ),
) );
```

### Refund API

```php
// Process refund
$payload = array(
    'transaction_id' => $transaction_id,
    'amount'         => $amount,
    'reason'         => $reason,
);

$response = wp_remote_post( 'https://api.example.com/refunds', array(
    'method'  => 'POST',
    'body'    => json_encode( $payload ),
) );
```

## Testing

### Test Mode

Enable test mode in gateway settings to use test card numbers:

- **Test Mode**: Enabled in settings
- **Test Card Number**: Any valid format (e.g., 4242 4242 4242 4242)
- **Test Expiry**: Any future date (e.g., 12/25)
- **Test CVC**: Any 3-4 digits (e.g., 123)

### Unit Testing

```php
// Test custom product type
public function test_custom_product_type() {
    $product = new WC_Product_Opencode_Custom();
    $this->assertEquals( 'opencode_custom', $product->get_type() );
}

// Test gateway
public function test_gateway_exists() {
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $this->assertArrayHasKey( 'opencode_custom_gateway', $gateways );
}
```

## Security

### Nonce Verification

```php
// Verify nonce in custom forms
if ( ! isset( $_POST['opencode_nonce'] ) || ! wp_verify_nonce( $_POST['opencode_nonce'], 'opencode_action' ) ) {
    wp_die( 'Security check failed' );
}
```

### Capability Checks

```php
// Check user capabilities
if ( ! current_user_can( 'manage_woocommerce' ) ) {
    wp_die( 'Unauthorized' );
}
```

### Data Sanitization

```php
// Sanitize input data
$custom_field = sanitize_text_field( $_POST['billing_custom_field'] );
$custom_notes = sanitize_textarea_field( $_POST['opencode_custom_notes'] );
```

## Performance

### Caching

```php
// Cache product custom data
$data = wp_cache_get( 'opencode_product_' . $product_id, 'opencode_wc' );

if ( false === $data ) {
    $data = get_post_meta( $product_id, '_opencode_custom_attribute', true );
    wp_cache_set( 'opencode_product_' . $product_id, $data, 'opencode_wc', HOUR_IN_SECONDS );
}
```

### Optimized Queries

```php
// Query custom products efficiently
$args = array(
    'post_type'      => 'product',
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'opencode_custom',
        ),
    ),
    'posts_per_page' => 50,
    'no_found_rows'  => true,
    'fields'         => 'ids',
);
```

## Internationalization

### Translation Functions

```php
// Basic translation
__( 'Custom Product', 'opencode-wc-extension' );

// Escaped translation
esc_html__( 'Order Details', 'opencode-wc-extension' );

// Translation with context
_x( 'Type', 'Product type', 'opencode-wc-extension' );

// Plural translation
printf( _n( 'One item', '%s items', $count, 'opencode-wc-extension' ), number_format_i18n( $count ) );
```

## Debugging

### WooCommerce Debug

```php
// Log custom events
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( 'OpenCode WC: Custom order created - ID: ' . $order_id );
}

// Use WooCommerce logger
$logger = wc_get_logger();
$logger->info( 'Custom payment processed', array( 'source' => 'opencode-wc-extension' ) );
```

## Support

For support, create an issue at:
https://example.com/support/opencode-wc-extension

## Credits

Developed by OpenCode

## License

GPL v2 or later

## Changelog

### 1.0.0
- Initial release
- Custom product type implementation
- Payment gateway integration
- Order handling and meta fields
- Admin and frontend integration
- Email templates customization
- My account customization
- Custom fees support
- Internationalization support