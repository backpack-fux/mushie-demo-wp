<?php
/*
 * Plugin Name: WooCommerce Custom Payment Gateway
 * Plugin URI: 
 * Description: WooCommerce Custom Payment Gateway
 * Author: BP - Devin
 * Author URI: 
 * Version: 1.0.10
 */

add_filter('woocommerce_payment_gateways', 'client33_add_gateway_class');
add_action('plugins_loaded', 'client33_init_gateway_class');

function client33_add_gateway_class($gateways)
{
    $gateways[] = 'WC_Creditcard_Gateway';
    return $gateways;
}

function client33_init_gateway_class()
{
    class WC_Creditcard_Gateway extends WC_Payment_Gateway
    {
        public function __construct()
        {
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
        }

        public function init_form_fields()
        {
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

        public function payment_fields()
        {
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

        public function payment_scripts()
        {
            if (!is_checkout() || !$this->enabled) {
                return;
            }
        }

        public function validate_fields()
        {
            if (empty($_POST['cc_number']) || empty($_POST['cc_expiry']) || empty($_POST['cc_cvc'])) {
                wc_add_notice(__('Please fill in all required fields.', 'woocommerce'), 'error');
                return false;
            }
            return true;
        }

        public function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);
            
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