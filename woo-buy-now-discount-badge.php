<?php
/**
 * Plugin Name: WooCommerce Buy Now + Discount Badge
 * Plugin URI:  https://azmayenfarhan.com
 * Description: Adds "Buy Now Button" and "Discount Badge" Elementor widgets (plus matching [buy_now_button] / [loop_save_price] shortcodes) for WooCommerce products.
 * Version:     1.4.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * Author:      Azmayen Farhan
 * Author URI:  https://azmayenfarhan.com
 * Text Domain: woo-buy-now-discount-badge
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------------
 * WooCommerce dependency check
 * ---------------------------------------------------------------------- */

add_action( 'admin_notices', 'wbndb_woocommerce_missing_notice' );
function wbndb_woocommerce_missing_notice() {
    if ( class_exists( 'WooCommerce' ) ) {
        return;
    }
    echo '<div class="notice notice-error"><p>' .
        esc_html__( 'WooCommerce Buy Now + Discount Badge requires WooCommerce to be installed and active.', 'woo-buy-now-discount-badge' ) .
        '</p></div>';
}

/* ------------------------------------------------------------------------
 * 1. Buy Now button — shared markup, used by both the [buy_now_button]
 *    shortcode and the "Buy Now Button" Elementor widget below.
 * ---------------------------------------------------------------------- */

/**
 * Build the Buy Now anchor markup for the current global $product.
 *
 * @param array $args {
 *     @type string $text    Button label. Default "Buy Now".
 *     @type string $classes Extra CSS classes to add, space separated.
 * }
 * @return string HTML, or '' if there's no valid product to link to.
 */
function wbndb_get_buy_now_button_html( $args = array() ) {
    global $product;

    // $product isn't always a real WC_Product when this runs — quick view /
    // AJAX popups, page-builder preview mode, and some themes can render it
    // before the product is set up. Without this check, calling ->get_id()
    // on null/false is a fatal error in PHP 8, which is what was showing as
    // "Critical Error" on the single product page.
    if ( ! $product instanceof WC_Product ) {
        return '';
    }

    $args = wp_parse_args(
        $args,
        array(
            'text'    => __( 'Buy Now', 'woo-buy-now-discount-badge' ),
            'classes' => '',
        )
    );

    $product_id   = $product->get_id();
    $checkout_url = wc_get_checkout_url();
    $classes      = trim( 'button buy-now-btn ' . $args['classes'] );

    if ( $product->is_type( 'variable' ) ) {
        // For variable products — variation selected via JS. The id is
        // relied on by the inline script below, so keep only one Buy Now
        // button (shortcode or widget) per product page.
        return sprintf(
            '<a href="#" class="%1$s disabled" data-checkout-url="%2$s" data-product-id="%3$s" id="buy-now-btn">%4$s</a>',
            esc_attr( $classes ),
            esc_url( $checkout_url ),
            esc_attr( $product_id ),
            esc_html( $args['text'] )
        );
    }

    // For simple products
    $url = add_query_arg( 'buy_now', $product_id, $checkout_url );
    return sprintf(
        '<a href="%1$s" class="%2$s">%3$s</a>',
        esc_url( $url ),
        esc_attr( $classes ),
        esc_html( $args['text'] )
    );
}

add_shortcode( 'buy_now_button', 'wbndb_buy_now_button_shortcode' );
function wbndb_buy_now_button_shortcode( $atts ) {
    $atts = shortcode_atts(
        array( 'text' => __( 'Buy Now', 'woo-buy-now-discount-badge' ) ),
        $atts,
        'buy_now_button'
    );

    return wbndb_get_buy_now_button_html( array( 'text' => $atts['text'] ) );
}

// Handle the redirect
add_action( 'template_redirect', 'wbndb_handle_buy_now_redirect' );
function wbndb_handle_buy_now_redirect() {
    if ( ! isset( $_GET['buy_now'] ) ) {
        return;
    }

    $product_id   = intval( $_GET['buy_now'] );
    $variation_id = isset( $_GET['variation_id'] ) ? intval( $_GET['variation_id'] ) : 0;

    // Guard against a stale/bad product id (e.g. an old shared link) wiping
    // out the customer's cart for nothing before redirecting to checkout.
    if ( ! $product_id || ! wc_get_product( $product_id ) ) {
        return;
    }

    WC()->cart->empty_cart();

    if ( $variation_id ) {
        WC()->cart->add_to_cart( $product_id, 1, $variation_id );
    } else {
        WC()->cart->add_to_cart( $product_id );
    }

    wp_safe_redirect( wc_get_checkout_url() );
    exit;
}

// JS to update the Buy Now URL when a variation is selected.
// Uses wp_add_inline_script() rather than an inline <script> tag — this is
// also what let the code get past the firewall that was blocking the
// original WPCode snippet.
add_action( 'wp_enqueue_scripts', 'wbndb_variable_script_enqueue' );
function wbndb_variable_script_enqueue() {
    if ( ! is_product() ) {
        return;
    }

    // Don't read the global $product here — wp_enqueue_scripts fires
    // before WooCommerce sets that global up (it's populated later, when
    // the loop runs), so this always failed silently for variable products
    // and the variation-picker script never loaded. Fetch the product
    // directly from the queried object instead.
    $product = wc_get_product( get_queried_object_id() );

    if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
        return;
    }

    wp_enqueue_script( 'jquery' );

    $inline_js = <<<'EOT'
    jQuery(function($) {
        var $btn = $('#buy-now-btn');

        // When user selects a variation
        $('form.variations_form').on('found_variation', function(event, variation) {
            var productId   = $btn.data('product-id');
            var checkoutUrl = $btn.data('checkout-url');
            var variationId = variation.variation_id;
            var buyNowUrl   = checkoutUrl + '?buy_now=' + productId + '&variation_id=' + variationId;
            $btn.attr('href', buyNowUrl).removeClass('disabled');
        });

        // When variation is cleared/reset
        $('form.variations_form').on('reset_data', function() {
            $btn.attr('href', '#').addClass('disabled');
        });

        // Block click if no variation selected yet
        $btn.on('click', function(e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
                alert('Please select product options first.');
            }
        });
    });
    EOT;

    wp_add_inline_script( 'jquery', $inline_js );
}

/* ------------------------------------------------------------------------
 * 2. [loop_buy_now] — Buy Now button shortcode for shop/category loops
 * ---------------------------------------------------------------------- */

add_shortcode( 'loop_buy_now', 'wbndb_render_loop_buy_now_button' );
function wbndb_render_loop_buy_now_button() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return '';
    }

    $product_id   = $product->get_id();
    $checkout_url = wc_get_checkout_url();

    if ( $product->is_type( 'variable' ) ) {
        // Send to product page to select variation first
        $url = get_permalink( $product_id );
    } else {
        // Direct to checkout
        $url = add_query_arg( 'buy_now', $product_id, $checkout_url );
    }

    return '<a href="' . esc_url( $url ) . '" class="button loop-buy-now-btn">Buy Now</a>';
}

/* ------------------------------------------------------------------------
 * 3. Discount badge — shared markup, used by both the [loop_save_price]
 *    shortcode and the "Discount Badge" Elementor widget below.
 * ---------------------------------------------------------------------- */

/**
 * Build the "Save X" badge markup for the current global $product.
 *
 * @param array $args {
 *     @type string $format  'amount' (Save $10) or 'percent' (Save 20%). Default 'amount'.
 *     @type string $prefix  Text shown before the value. Default "Save".
 *     @type string $classes Extra CSS classes to add, space separated.
 * }
 * @return string HTML, or '' if the product isn't on sale / has no valid prices.
 */
function wbndb_get_discount_badge_html( $args = array() ) {
    global $product;

    if ( ! $product instanceof WC_Product ) {
        return '';
    }

    // Only show if product is on sale
    if ( ! $product->is_on_sale() ) {
        return '';
    }

    $args = wp_parse_args(
        $args,
        array(
            'format'  => 'amount',
            'prefix'  => __( 'Save', 'woo-buy-now-discount-badge' ),
            'classes' => '',
        )
    );

    // For variable products, get min prices; otherwise use the product's own
    if ( $product->is_type( 'variable' ) ) {
        $regular_price = (float) $product->get_variation_regular_price( 'min' );
        $sale_price    = (float) $product->get_variation_sale_price( 'min' );
    } else {
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();
    }

    if ( ! $regular_price || ! $sale_price ) {
        return '';
    }

    if ( 'percent' === $args['format'] ) {
        $percent_off = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
        $value       = $percent_off . '%';
    } else {
        $save_amount = $regular_price - $sale_price;
        $value       = get_woocommerce_currency_symbol() . number_format( $save_amount, 0 );
    }

    $prefix  = '' !== trim( (string) $args['prefix'] ) ? $args['prefix'] . ' ' : '';
    $classes = trim( 'loop-save-badge ' . $args['classes'] );

    return '<span class="' . esc_attr( $classes ) . '">' . esc_html( $prefix ) . esc_html( $value ) . '</span>';
}

add_shortcode( 'loop_save_price', 'wbndb_render_loop_save_price' );
function wbndb_render_loop_save_price( $atts ) {
    $atts = shortcode_atts(
        array(
            'format' => 'amount', // amount|percent
            'prefix' => __( 'Save', 'woo-buy-now-discount-badge' ),
        ),
        $atts,
        'loop_save_price'
    );

    return wbndb_get_discount_badge_html(
        array(
            'format' => $atts['format'],
            'prefix' => $atts['prefix'],
        )
    );
}

/* ------------------------------------------------------------------------
 * 4. Elementor widgets — "Buy Now Button" and "Discount Badge"
 *    Proper Elementor widgets (Content + Style tabs) as an alternative to
 *    the [buy_now_button] / [loop_save_price] shortcodes above.
 *    Requires Elementor 3.5+.
 * ---------------------------------------------------------------------- */

add_action( 'elementor/elements/categories_registered', 'wbndb_register_elementor_category' );
function wbndb_register_elementor_category( $elements_manager ) {
    $elements_manager->add_category(
        'wbndb-category',
        array(
            'title' => esc_html__( 'Buy Now + Discount Badge', 'woo-buy-now-discount-badge' ),
            'icon'  => 'eicon-cart-medium',
        )
    );
}

// Registered (not enqueued) up front — Elementor only actually enqueues
// this on pages where the Buy Now widget is present, via its
// get_style_depends() method.
add_action( 'wp_enqueue_scripts', 'wbndb_register_widget_styles' );
function wbndb_register_widget_styles() {
    wp_register_style(
        'wbndb-elementor-widgets',
        plugins_url( 'assets/css/wbndb-elementor-widgets.css', __FILE__ ),
        array(),
        '1.4.0'
    );
}

add_action( 'elementor/widgets/register', 'wbndb_register_elementor_widgets' );
function wbndb_register_elementor_widgets( $widgets_manager ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wbndb-elementor-buy-now-widget.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wbndb-elementor-discount-badge-widget.php';

    if ( class_exists( 'WBNDB_Elementor_Buy_Now_Widget' ) ) {
        $widgets_manager->register( new WBNDB_Elementor_Buy_Now_Widget() );
    }

    if ( class_exists( 'WBNDB_Elementor_Discount_Badge_Widget' ) ) {
        $widgets_manager->register( new WBNDB_Elementor_Discount_Badge_Widget() );
    }
}
