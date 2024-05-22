<?php

/*
 * Plugin Name: WooCommerce Custom Payment Gateway
 * Plugin URI:
 * Description: WooCommerce Custom Payment Gateway
 * Author: DEV Team
 * Author URI:
 * Version: 1.0.5
 */

add_filter('woocommerce_payment_gateways', 'client33_add_gateway_class');
add_action('plugins_loaded', 'client33_init_gateway_class');

function client33_add_gateway_class($gateways) {
    $gateways[] = 'WC_Creditcard_Gateway';
    return $gateways;
}

function client33_init_gateway_class() {
    class WC_Creditcard_Gateway extends WC_Payment_Gateway
    {
        public function __construct() {
            $this->id = 'client33';
            $this->icon = '';
            $this->has_fields = true;
            $this->method_title = 'Credit Card';
            $this->method_description = '';
            $this->supports = array('products');

            $this->order_total = WC()->cart->total;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'label' => 'Enable Payment Gateway',
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'Credit Card',
                    'desc_tip' => true
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default' => 'Pay with your credit card via our super-cool payment gateway.'
                )
            );
        }

        public function payment_fields() {
            if (isset($_POST['coupon_code'])) {
                $coupon_code = sanitize_text_field($_POST['coupon_code']);


                $result = WC()->cart->apply_coupon($coupon_code);

                if ($result === true) {
                    // Coupon applied successfully, recalculate the order total
                    $orderTotal = $order->calculate_totals();
                } else {
                    // Handle coupon application error
                    wc_add_notice($result, 'error');
                }
            } else {
                $orderTotal = $this->order_total;
            }

            echo do_shortcode('[payment-form-shortcode order_total="' . $orderTotal . '"]');
        }

        public function payment_scripts() {
            // Intentionally left blank
        }

        public function validate_fields() {
            return true;
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // Mark the order as processing or completed
            $order->update_status('processing', __('Payment received, your order is now being processed.', 'woocommerce'));

            // Reduce stock levels
            wc_reduce_stock_levels($order_id);

            // Remove cart
            WC()->cart->empty_cart();

            // Return thank you page redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

        public function webhook() {
            // Intentionally left blank
        }
    }
}
