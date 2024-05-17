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
            // Intentionally left blank
        }

        public function payment_scripts()
        {
            // Intentionally left blank  
        }

        public function validate_fields()
        {
            return true;
        }

        public function process_payment($order_id)
        {
            return array(
                'result' => 'success',
                'redirect' => site_url() . '/payment/?order_ref=' . base64_encode($order_id)
            );
        }

        public function webhook()
        {
            // Intentionally left blank
        }
    }
}