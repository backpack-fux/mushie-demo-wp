<?php

/**
 * Plugin Name: My Custom Features
 * Plugin URI:
 * Description: Various custom features developed by Dev Team
 * Version: 1.0.11
 * Author: BP - Devin
 * Author URI:
 */

// Register activation hook
register_activation_hook(__FILE__, 'mcf_create_required_pages');
// Add admin menu
add_action('admin_menu', 'create_custom_feature_page');
// Add shortcodes
add_shortcode('thank-you-shortcode', 'thank_you');
// Add admin menu page
function create_custom_feature_page() {

    $page_title = 'Custom Payment Plugin';
    $menu_title = 'Custom Payment Plugin';
    $capability = 'read';
    $slug = 'custom_feature_page';
    $callback = 'custom_feature_content';
    $icon = 'dashicons-welcome-write-blog';
    $position = 100;
    add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
}

// Admin page content
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
        .successMsg {
            color: #006600;
            font-weight: bold;
        }
    </style>

    <h1 class="margin-top-10">Settings</h1>
    <?php
    $GLOBALS['msg'] = '';
    if (isset($_POST['btn_update_settings']) && $_REQUEST['mode'] == 'update_custom_feature_settings') {
        update_option('client_payment_page_url', $_POST['client_payment_page_url']);
        $GLOBALS['msg'] = 'Settings updated successfully.';
    }
    ?>

    <form name="frmUpdateCustomFeatureSettings" method="POST" action="admin.php?page=custom_feature_page&mode=update_custom_feature_settings">
        <table cellpadding="10" cellspacing="10">
            <?php if (!empty($GLOBALS['msg'])) {
                ?>
                <tr>
                    <td colspan="2" class="successMsg"><?php echo stripslashes($GLOBALS['msg']); ?></td>
                </tr>
                <?php
            } ?>
            <tr>
                <td><strong>Client URL</strong></td>
                <td>
                    <input type="text" name="client_payment_page_url" class="textbox-medium" value="<?php echo get_option('client_payment_page_url'); ?>" placeholder="Enter Client URL" required/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input name="btn_update_settings" class="btn_update_settings" type="submit" value="Submit" />
                </td>
            </tr>
        </table>
    </form>
    <?php
}

// Create required pages on plugin activation
function mcf_create_required_pages() {

    $pages = [
        'thank-you' => [
            'title' => 'Thank You',
            'content' => '[thank-you-shortcode]',
        ],
    ];
    foreach ($pages as $slug => $page) {
        if (!get_page_by_path($slug, OBJECT, 'page')) {
            wp_insert_post([
                'post_type' => 'page',
                'post_title' => $page['title'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_author' => 1,
                'post_name' => $slug,
                ]);
        }
    }
}

// Shortcode for thank you page
function thank_you()
{

    if (!empty($_REQUEST['order_ref'])) {
        $order_ref = base64_decode($_REQUEST['order_ref']);
        $order_id = $order_ref;
        if ($order_id > 0) {
            if ($order_id > 0) {
                $order = wc_get_order($order_id);
                if ($order->has_status('completed')) {
                    $order_note1 = 'Payment Intent: ' . sanitize_text_field($_REQUEST['payment_intent']);
                    $order->add_order_note($order_note1);
                    return '<p>Thank you for your payment. Your order has been completed.</p>';
                } else {
                    return '<p>Payment not completed. Please try again.</p>';
                }
            }
        }
    }
        return '<p>Invalid order reference.</p>';
}
