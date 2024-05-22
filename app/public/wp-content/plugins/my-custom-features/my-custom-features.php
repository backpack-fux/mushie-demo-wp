<?php

/**
 * Plugin Name: My Custom Features
 * Plugin URI:
 * Description: Various custom features developed by Dev Team
 * Version: 1.0.5
 * Author: DEV Team - Thomas
 * Author URI:
 */

add_action('admin_menu', 'create_custom_feature_page');
add_shortcode('payment-form-shortcode', 'payment_form');
add_shortcode('thank-you-shortcode', 'thank_you');
register_activation_hook(__FILE__, 'mcf_create_required_pages');
// Hook to add custom payment form to WooCommerce checkout page
//add_action('woocommerce_review_order_before_payment', 'add_custom_payment_form_to_checkout');

function create_custom_feature_page() {

    add_menu_page('Custom Features', 'Custom Features', 'manage_options', 'custom_feature_page', 'custom_feature_content');
}

function custom_feature_content() {

    ?>
  <style type="text/css">
        .btn_update_settings {
         cursor: pointer;
           padding: 10px 15px;
        }

     .textbox-large {
           width: 550px !important;
       }

     .textbox-medium {
          width: 350px !important;
       }

     .textbox-small {
           width: 200px !important;
       }

     .margin-top-10 {
           margin-top: 10px;
      }
  </style>

  <h1 class="margin-top-10">Settings</h1>
    <?php
    $msg = '';
    if (isset($_POST['btn_update_settings']) && $_REQUEST['mode'] == 'update_custom_feature_settings') {
        update_option('client_payment_page_url', $_POST['client_payment_page_url']);
        $msg = 'Settings updated successfully.';
    }
    ?>

  <form name="frmUpdateCustomFeatureSettings" method="POST"
      action="admin.php?page=custom_feature_page&mode=update_custom_feature_settings">
       <table cellpadding="10" cellspacing="10">
            <?php if (!empty($msg)) {
                ?>
             <tr>
                    <td colspan="2" class="successMsg"><?php echo stripslashes($msg); ?></td>
             </tr>
                <?php
            } ?>

          <tr>
               <td><strong>Client URL</strong></td>
               <td>
                   <input type="text" name="client_payment_page_url" class="textbox-medium"
                        value="<?php echo get_option('client_payment_page_url'); ?>" placeholder="Enter Client URL"
                       required />
                </td>
          </tr>

         <tr>
               <td>&nbsp;</td>
                <td><input name="btn_update_settings" class="btn_update_settings" type="submit" value="Submit" /></td>
         </tr>
      </table>
   </form>
    <?php
}

function payment_form() {
    ob_start();
    if (!empty($_REQUEST['order_ref'])) {
        $order_id = base64_decode(sanitize_text_field($_REQUEST['order_ref']));
        $order = wc_get_order($order_id);
        if ($order) {
            $order_total = $order->get_total();
            $order_ref = site_url() . '||' . $order_id;
            $order_ref = base64_encode($order_ref);
            $url = get_option('client_payment_page_url');

            if (empty($url)) {
                die("Error: URL is empty.");
            }

            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                die("Error: Invalid URL provided.");
            }

            $crl = curl_init();
            if ($crl === false) {
                die("Error: Unable to initialize cURL session.");
            }

            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($crl);
            if ($response === false) {
                die("Error: \"" . curl_error($crl) . "\" - Code: " . curl_errno($crl));
            }

            curl_close($crl);
            $response = str_replace('"', '', $response);
            ?>
            <style>
                .iframe-payment {
                    width: 600px;
                    height: 480px;
                }
                @media(max-width: 282px) {
                    .iframe-payment {
                        width: 100%;
                        height: 680px !important;
                    }
                }
                @media(max-width: 480px) {
                    .iframe-payment {
                        width: 100%;
                        height: 480px;
                    }
                }
            </style>
            <iframe src="<?php echo esc_url($response); ?>?order_total=<?php echo esc_attr($order_total); ?>&order_ref=<?php echo esc_attr($order_ref); ?>" class="iframe-payment" style="border:none;"></iframe>
            <?php
        } else {
            echo '<p align="center" style="color:#FF0000;">Invalid Order!</p>';
        }
    } else {
        echo '<p align="center" style="color:#FF0000;">Invalid Order!</p>';
    }
    return ob_get_clean();
}

function thank_you() {

    ob_start();
    if (!empty($_REQUEST['order_ref'])) {
        $order_id = base64_decode(sanitize_text_field($_REQUEST['order_ref']));
        $order = wc_get_order($order_id);
        if (!$order) {
            echo '<p><strong>Invalid Order!</strong></p>';
            return ob_get_clean();
        }
        $redirect_url = $order->get_checkout_order_received_url();
        if ($order->get_status() == 'processing') {
            ?>
            <form action="<?php echo esc_url($redirect_url); ?>" name="frmRedirectSuccessPage" method="get">
                <input type="hidden" name="key" value="<?php echo esc_attr($order->get_order_key()); ?>">
           </form>
            <script language="javascript">document.frmRedirectSuccessPage.submit();</script>
            <?php
            die;
        } else {
            ?>
            <p><strong>Invalid Order!</strong></p>
            <?php
        }
    }
    return ob_get_clean();
}

function mcf_create_required_pages() {

    $page_slug = 'payment';
    $new_page = array(
        'post_type' => 'page',
        'post_title' => 'Payment',
        'post_content' => '[payment-form-shortcode]',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_name' => $page_slug
    );
    if (!get_page_by_path($page_slug, OBJECT, 'page')) {
        wp_insert_post($new_page);
    }

    $page_slug = 'thank-you';
    $new_page = array(
        'post_type' => 'page',
        'post_title' => 'Thank You',
        'post_content' => '[thank-you-shortcode]',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_name' => $page_slug
    );
    if (!get_page_by_path($page_slug, OBJECT, 'page')) {
        wp_insert_post($new_page);
    }
}

// function add_custom_payment_form_to_checkout()
// {
//    echo do_shortcode('[payment-form-shortcode]');
// }
// ?>
