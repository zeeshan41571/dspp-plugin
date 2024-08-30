<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$order_id = '';
if (isset( $_POST['payment_processor_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['payment_processor_nonce'])) , 'payment_processor_action' )) {
    $processer = sanitize_text_field($_POST['processor']);
    $billing_id = sanitize_key($_POST['billing_id']);
    $invoice_id = sanitize_key($_POST['invoice_id']);
}
$user_id = get_current_user_id();
$invoice_details = dspp_get_invoice_details($invoice_id);
$decoded_details = json_decode($invoice_details[0]['invoice_items'], true);
$cart_data = json_decode($invoice_details[0]['invoice_items'], true);
$cart_total = 0;
foreach ($cart_data as $item) {
    if (isset($item['price'])) {
        $cart_total += $item['price'] * $item['quantity'];
    }
}
if (isset( $_POST['payment_processor_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['payment_processor_nonce'])) , 'payment_processor_action' )) {
    if (isset($cart_data['coupon_code'])) {
        $coupon_code = $cart_data['coupon_code'];
        $coupon_value = $cart_data['coupon_discount_type'] == 'fixed_discount' ? $cart_data['coupon_value'] : (($cart_data['coupon_value'] / 100) * $cart_total);
    }
}

if ($processer == "stripe_card") {
    $stripeToken = sanitize_text_field($_POST['stripeToken']);
    $cart_total = sanitize_key($_POST['cart_total']);
    $charge_created = get_user_meta($user_id, 'charge_created_against_' . $invoice_id, true);
    $charge_data = '';
    if (!$charge_created) {
        require_once('stripe/init.php');
        $details_stripe = dspp_check_payment_method_enabled("Strip");
        $settings = $details_stripe->methode_details;
        $decoded = json_decode($settings);
        $secretKey = $decoded->secret_key;
        \Stripe\Stripe::setApiKey("$secretKey");
        $charge_data = \Stripe\Charge::create(array(
                    "amount" => $cart_total * 100,
                    "currency" => "usd",
                    "description" => "SPP CRM",
                    "source" => $stripeToken,
        ));
        if (!empty($charge_data)) {
            update_user_meta($user_id, 'charge_created_against_' . $invoice_id, 'yes');
        }
    }
    $statuses = dspp_get_invoices_status_id_by_name('paid');
    $invoice_status_id = $statuses[0]['status_id'];
    dspp_udpate_invoice_staus($invoice_id, $invoice_status_id);
    $method_id = dspp_save_payment_method_info('stripe_card', $stripeToken);
    $statuses_list = array_shift(dspp_get_status_id_by_name('processing'));
    $status_id = $statuses_list['status_id'];
    $order_id = dspp_save_order_data($invoice_id, $method_id, $billing_id, $status_id, 1);
    delete_user_meta($user_id, 'invoice_created');
    delete_user_meta($user_id, 'invoice_id');
    delete_user_meta($user_id, 'custom_website_data');
    update_user_meta($user_id, 'order_placed_against_' . $invoice_id, 'yes');
    $url = esc_url(site_url()).'/bits-thankyou?order_id='.esc_html($order_id).'&processor=stripe&billing_id='.esc_html($billing_id).'&nonce='.esc_attr(wp_create_nonce('user-order-completed'));
    wp_redirect($url);
} else if ($processer == 'paypal') {
    if (isset( $_POST['payment_processor_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['payment_processor_nonce'])) , 'payment_processor_action' ))
    {
        if (isset($_POST['paypal_order_id']) && !empty(sanitize_key($_POST['paypal_order_id']))) {
            $paypal_order_id = sanitize_key($_POST['paypal_order_id']);
            $statuses = dspp_get_invoices_status_id_by_name('paid');
            $invoice_status_id = $statuses[0]['status_id'];
            dspp_udpate_invoice_staus($invoice_id, $invoice_status_id);
            $method_id = dspp_save_payment_method_info('paypal', $paypal_order_id);
            $statuses_list = array_shift(dspp_get_status_id_by_name('processing'));
            $status_id = $statuses_list['status_id'];
            $order_id = dspp_save_order_data($invoice_id, $method_id, $billing_id, $status_id, 1);
            delete_user_meta($user_id, 'invoice_created');
            delete_user_meta($user_id, 'invoice_id');
            delete_user_meta($user_id, 'custom_website_data');
            update_user_meta($user_id, 'order_placed_against_' . $invoice_id, 'yes');
            $url = esc_url(site_url()).'/bits-thankyou?order_id=' .esc_html($order_id).'&processor=paypal&billing_id='.esc_html($billing_id).'&nonce='.esc_attr(wp_create_nonce('user-order-completed'));
            wp_redirect($url);
        }
    }
} else if ($processer == 'manual_payment') {
    $method_id = dspp_save_payment_method_info('manual_payment', 'Nill');
    $statuses = dspp_get_invoices_status_id_by_name('pending');
    $invoice_status_id = $statuses[0]['status_id'];
    dspp_udpate_invoice_staus($invoice_id, $invoice_status_id);
    $statuses_list = array_shift(dspp_get_status_id_by_name('processing'));
    $status_id = $statuses_list['status_id'];
    $order_id = dspp_save_order_data($invoice_id, $method_id, $billing_id, $status_id);
    delete_user_meta($user_id, 'invoice_created');
    delete_user_meta($user_id, 'invoice_id');
    delete_user_meta($user_id, 'custom_website_data');
    update_user_meta($user_id, 'order_placed_against_' . $invoice_id, 'yes');
    $url = esc_url(site_url()).'/bits-thankyou?invoice_id='.esc_html($invoice_id).'&processor=manual_payment&billing_id='.esc_html($billing_id).'&nonce='.esc_attr(wp_create_nonce('user-order-completed'));
    wp_redirect($url);
}
