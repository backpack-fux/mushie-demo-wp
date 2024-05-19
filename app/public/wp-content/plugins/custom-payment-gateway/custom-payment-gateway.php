<?php

/*
 * Plugin Name: WooCommerce Custom Payment Gateway
 * Plugin URI:
 * Description: WooCommerce Custom Payment Gateway
 * Author: BP - Devin
 * Author URI:
 * Version: 1.1.0
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

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
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
            ?>
            <fieldset>
                <p class="form-row form-row-wide">
                    <label for="cc_number"><?php _e('Card number', 'woocommerce'); ?> <span class="required">*</span></label>
                    <input id="cc_number" name="cc_number" type="text" autocomplete="off" placeholder="1234 1234 1234 1234">
                    <img src="https://example.com/path-to-your-icons/visa.png" alt="Visa">
                    <img src="https://example.com/path-to-your-icons/mastercard.png" alt="MasterCard">
                    <img src="https://example.com/path-to-your-icons/amex.png" alt="American Express">
                    <img src="https://example.com/path-to-your-icons/elo.png" alt="Elo">
                </p>
                <p class="form-row form-row-first">
                    <label for="cc_expiry"><?php _e('Expiration', 'woocommerce'); ?> <span class="required">*</span></label>
                    <input id="cc_expiry" name="cc_expiry" type="text" autocomplete="off" placeholder="MM / YY">
                </p>
                <p class="form-row form-row-last">
                    <label for="cc_cvc"><?php _e('CVC', 'woocommerce'); ?> <span class="required">*</span></label>
                    <input id="cc_cvc" name="cc_cvc" type="text" autocomplete="off" placeholder="CVC">
                    <img src="https://example.com/path-to-your-icons/cvc.png" alt="CVC">
                </p>
                <div class="clear"></div>
            </fieldset>
            <?php
        }

        public function payment_scripts() {
            if (!is_checkout() || !$this->enabled) {
                return;
            }
        }

        public function validate_fields() {
            if (empty($_POST['cc_number']) || empty($_POST['cc_expiry']) || empty($_POST['cc_cvc'])) {
                wc_add_notice(__('Please fill in all required fields.', 'woocommerce'), 'error');
                return false;
            }

            // Validate credit card number using Luhn algorithm
            if (!$this->is_valid_card_number($_POST['cc_number'])) {
                wc_add_notice(__('Invalid card number.', 'woocommerce'), 'error');
                return false;
            }

            // Validate expiry date format
            if (!preg_match('/^(0[1-9]|1[0-2]) \/ ([0-9]{2})$/', $_POST['cc_expiry'])) {
                wc_add_notice(__('Invalid expiry date format. Use MM / YY.', 'woocommerce'), 'error');
                return false;
            }

            // Validate CVC format
            if (!preg_match('/^[0-9]{3,4}$/', $_POST['cc_cvc'])) {
                wc_add_notice(__('Invalid CVC code.', 'woocommerce'), 'error');
                return false;
            }

            return true;
        }

        private function is_valid_card_number($number) {
            $number = preg_replace('/\D/', '', $number);
            $checksum = 0;
            $length = strlen($number);

            for ($i = (2 - ($length % 2)); $i <= $length; $i += 2) {
                $checksum += (int)($number[$i - 1]);
            }

            for ($i = ($length % 2) + 1; $i < $length; $i += 2) {
                $digit = (int)($number[$i - 1]) * 2;
                if ($digit < 10) {
                    $checksum += $digit;
                } else {
                    $checksum += ($digit - 9);
                }
            }

            return ($checksum % 10) === 0;
        }

        public function process_payment($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);

            // Set to true to test payment error
            $payment_error = false;

            if ($payment_error) {
                wc_add_notice(__('Payment error: ' . $payment_error, 'woocommerce'), 'error');
                return false;
            }

            // Payment is successful
            $order->payment_complete();

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Return thank you page redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

        public function webhook()
        {
            // Intentionally left blank
        }
    }
}