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
            if (!preg_match('/^(0[1-9]|1[0-2]) \/ ([0-9]{4})$/', $_POST['cc_expiry'])) {
                wc_add_notice(__('Invalid expiry date format. Use MM / YYYY.', 'woocommerce'), 'error');
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

        private function create_session($card_number, $expiry_month, $expiry_year, $cvc) {
            $api_url = 'https://try.access.worldpay.com/sessions/card'; // Session API endpoint

            $payload = array(
                'identity' => 'f261c0e5-d7b0-49fd-a957-5aa449058f12',
                'cardExpiryDate' => array(
                    'month' => $expiry_month,
                    'year' => $expiry_year
                ),
                'cvc' => $cvc,
                'cardNumber' => $card_number
            );

            // Log the payload
            error_log('Payload: ' . json_encode($payload));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/vnd.worldpay.sessions-v1.hal+json',
                'Content-Type: application/vnd.worldpay.sessions-v1.hal+json',
                'X-WP-SDK: access-checkout-web/2.0.0'
            ]);

            $response = curl_exec($ch);
            if (!$response) {
                return ['status' => 'failed', 'message' => 'Session API request failed'];
            }
            curl_close($ch);

            error_log('API Response: ' . $response);

            $response_data = json_decode($response, true);
            if (isset($response_data['_links']['sessions:session']['href'])) {
                return ['status' => 'success', 'sessionUrl' => $response_data['_links']['sessions:session']['href']];
            } else {
                return ['status' => 'failed', 'message' => 'Session URL not found in response'];
            }
        }

        public function process_payment($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);

            // Collect payment details
            $cc_number = sanitize_text_field($_POST['cc_number']);
            $cc_expiry = sanitize_text_field($_POST['cc_expiry']);
            $cc_cvc = sanitize_text_field($_POST['cc_cvc']);

            // Extract month and year from expiry date
            list($expiry_month, $expiry_year) = explode(' / ', $cc_expiry);

            // Create session
            $session_response = $this->create_session($cc_number, $expiry_month, $expiry_year, $cc_cvc);
            if ($session_response['status'] !== 'success') {
                wc_add_notice(__('Session creation failed: ' . $session_response['message'], 'woocommerce'), 'error');
                return false;
            }

            // Prepare payload for Pylon Process Transaction
            $payload = array(
                'sessionUrl' => $session_response['sessionUrl'],
                'order' => array(
                    'merchant' => array(
                        'id' => 3 // Assuming merchant ID is constant; replace with actual ID if dynamic
                    ),
                    'buyer' => array(
                        'isShippingEqualBilling' => true,
                        'billingAddress' => array(
                            'firstName' => $order->get_billing_first_name(),
                            'lastName' => $order->get_billing_last_name(),
                            'address1' => $order->get_billing_address_1(),
                            'postalCode' => $order->get_billing_postcode(),
                            'city' => $order->get_billing_city(),
                            'state' => $order->get_billing_state(),
                            'countryCode' => $order->get_billing_country()
                        )
                    ),
                    'value' => array(
                        'currency' => get_woocommerce_currency(),
                        'amount' => $order->get_total() * 100 // Assuming the API expects the amount in cents
                    )
                )
            );

            // Call Pylon API
            $response = $this->send_transaction_to_pylon($payload);

            // Handle the response from Pylon API
            if ($response['status'] === 'AUTHORIZED') {
                // Payment is successful
                $order->payment_complete();
                $woocommerce->cart->empty_cart();

                // Return thank you page redirect
                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url($order)
                );
            } else {
                wc_add_notice(__('Payment error: ' . $response['message'], 'woocommerce'), 'error');
                return false;
            }
        }

        private function send_transaction_to_pylon($data) {
            $api_url = 'https://pylon-v2-staging.up.railway.app/v1/transaction/process?paymentProcessor=WORLDPAY'; // Replace with actual Pylon API URL

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);

            $response = curl_exec($ch);
            if (!$response) {
                return ['status' => 'failed', 'message' => 'API request failed'];
            }
            curl_close($ch);

            return json_decode($response, true);
        }

        public function webhook()
        {
            // Intentionally left blank
        }
    }
}