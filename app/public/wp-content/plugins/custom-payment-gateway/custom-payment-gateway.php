<?php
/**
 * Plugin Name: WooCommerce Custom Payment Gateway
 * Plugin URI: 
 * Description: WooCommerce Custom Payment Gateway
 * Version: 1.0.11
 * Author: BP - Devin
 * Author URI: 
 */
add_filter("woocommerce_payment_gateways", "Client33_Add_Gateway_Class");

function Client33_Add_Gateway_Class($gateways)
{
    $gateways[] = "\127\x43\x5f\x43\162\x65\x64\x69\164\x63\x61\162\x64\137\107\x61\x74\145\167\141\171"; 
    return $gateways;
}

add_action("\160\x6c\x75\147\x69\x6e\x73\137\154\157\x61\x64\145\x64", "\143\x6c\151\145\156\x74\x33\63\137\151\x6e\151\164\137\x67\141\x74\145\x77\141\x79\x5f\x63\154\x61\x73\163");

function client33_init_gateway_class()
{
    class WC_Creditcard_Gateway extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = "\x63\x6c\x69\x65\x6e\x74\63\63";
            $this->icon = '';
            $this->has_fields = true;
            $this->method_title = "\x43\x72\x65\x64\151\x74\x20\103\x61\x72\144";
            $this->method_description = '';
            $this->supports = array("\160\x72\x6f\x64\x75\143\164\163");
            $this->init_form_fields();
            $this->init_settings();
            $this->title = $this->get_option("\x74\x69\x74\154\x65");
            $this->description = $this->get_option("\x64\x65\163\143\162\151\x70\164\151\157\x6e");
            $this->enabled = $this->get_option("\145\x6e\x61\x62\x6c\x65\x64");
            add_action("\167\x6f\x6f\x63\x6f\155\x6d\x65\x72\x63\x65\137\165\x70\x64\x61\164\x65\x5f\x6f\x70\x74\x69\x6f\x6e\163\137\x70\141\x79\155\145\156\x74\x5f\147\x61\164\x65\167\x61\x79\163\x5f" . $this->id, array($this, "\160\162\157\143\x65\163\163\137\141\144\155\151\156\x5f\157\x70\164\x69\x6f\156\163"));
        }
        
        public function init_form_fields()
        {
            $this->form_fields = array(
                "\x65\156\141\x62\154\x65\x64" => array(
                    "\164\151\x74\x6c\145" => "\x45\156\141\x62\154\145\x2f\x44\x69\163\x61\142\154\145",
                    "\154\x61\x62\145\154" => "\105\x6e\141\142\x6c\x65\x20\x50\x61\171\x6d\x65\x6e\x74\40\107\141\164\x65\x77\141\x79",
                    "\x74\x79\x70\x65" => "\143\150\145\143\153\142\x6f\170",
                    "\144\x65\x73\143\162\x69\160\164\x69\x6f\156" => '',
                    "\x64\145\146\141\165\154\x74" => "\156\157"
                ),
                "\x74\x69\x74\154\145" => array(
                    "\164\151\x74\x6c\145" => "\124\151\x74\x6c\x65",
                    "\164\x79\160\x65" => "\164\145\170\164",
                    "\144\145\163\143\x72\151\x70\164\151\x6f\156" => "\124\150\x69\x73\x20\143\x6f\x6e\164\x72\x6f\154\163\x20\x74\x68\x65\40\x74\x69\x74\x6c\145\x20\167\x68\151\x63\x68\x20\x74\150\145\x20\165\x73\145\x72\40\163\145\x65\x73\40\x64\x75\162\151\156\x67\40\143\x68\145\143\153\x6f\165\164\56",
                    "\144\145\x66\x61\165\x6c\164" => "\x43\x72\x65\144\151\x74\x20\103\141\x72\x64",
                    "\144\x65\163\143\x5f\x74\151\160" => true
                ),
                "\144\145\163\x63\162\x69\x70\x74\151\157\156" => array(
                    "\x74\x69\164\x6c\145" => "\104\x65\163\x63\162\151\160\x74\x69\157\156",
                    "\164\171\160\145" => "\164\145\x78\x74\x61\x72\145\141",
                    "\144\145\163\x63\162\x69\x70\x74\x69\x6f\x6e" => "\x54\150\151\163\x20\143\x6f\156\164\162\x6f\x6c\x73\40\x74\x68\145\x20\x64\x65\x73\x63\162\151\x70\x74\x69\157\x6e\x20\x77\150\151\143\x68\x20\164\x68\x65\40\x75\163\x65\162\40\163\145\145\x73\x20\144\x75\x72\x69\156\147\x20\143\x68\145\x63\153\x6f\x75\164\56",
                    "\144\x65\x66\x61\x75\x6c\164" => "\x50\x61\171\x20\167\x69\164\150\40\171\157\x75\x72\40\143\x72\x65\144\151\x74\40\x63\x61\162\x64\x20\x76\x69\x61\x20\x6f\x75\162\40\x73\x75\160\145\162\x2d\143\157\157\x6c\40\x70\x61\x79\x6d\145\x6e\x74\40\x67\141\x74\145\167\x61\171\x2e"
                    )
                );
        }
                
        public function payment_fields()
        {
                
        }
               
        public function payment_scripts()
        {
                    
        }
                
        public function validate_fields()
        {
            return true;
        }
               
        public function process_payment($order_id)
        {
            return array(
                "\162\145\163\165\x6c\164" => "\163\x75\143\x63\145\x73\x73",
                "\x72\x65\x64\x69\x72\145\x63\164" => site_url() . "\57\x70\x61\171\x6d\x65\156\x74\57\77\157\x72\144\x65\162\137\162\x65\x66\75" . base64_encode($order_id)
            );
        }
                
        public function webhook()
        {
                    
        }
    }
}