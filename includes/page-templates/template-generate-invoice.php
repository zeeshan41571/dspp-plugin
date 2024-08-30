<?php //
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//// Get current user ID and billing address
//$user_id_wp = get_current_user_id();
//$billing_address = dspp_get_billing_details_by_user_id($user_id_wp);
//
//// Get user details
//$user_details = wp_get_current_user();
//
//// Get custom website data from user meta
//$custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
//
//// Decode the custom website data
//$decoded_array = json_decode($custom_website_data, true);
//
//// Get cart data
//$cart_data = isset($decoded_array['custom_website_data_' . $user_id_wp]) ? $decoded_array['custom_website_data_' . $user_id_wp] : array();
//
//// Encode cart data as JSON
//$json_data = wp_json_encode($cart_data);
//
//// Calculate cart total
//$cart_total = 0;
//foreach ($cart_data as $item) {
//    if (isset($item['price'])) {
//        $cart_total += $item['price'] * $item['quantity'];
//    }
//}
//
//// Check for applied coupon code and calculate discount
//$coupon_code = '';
//$coupon_value = 0;
//if (isset($cart_data['coupon_code'])) {
//    $coupon_code = $cart_data['coupon_code'];
//    $coupon_value = $cart_data['coupon_discount_type'] == 'fixed_discount' ? $cart_data['coupon_value'] : (($cart_data['coupon_value'] / 100) * $cart_total);
//    $cart_total -= $coupon_value;
//}
//
//// Get the status ID for 'Abandoned Checkout'
//$statuses = dspp_get_invoices_status_id_by_name('Abandoned Checkout');
//$status_id = !empty($statuses) ? $statuses[0]['status_id'] : 0;
//
//// Insert invoice details into the database
//global $wpdb;
//$table_name = $wpdb->prefix . 'sppcrm_invoice_details';
//$wpdb->insert(
//    $table_name,
//    array(
//        'invoice_items' => $json_data,
//        'invoice_total' => $cart_total,
//        'invoice_status' => $status_id,
//        'user_id' => $user_id_wp,
//        'coupon_code' => $coupon_code,
//        'coupon_value' => $coupon_value,
//    ),
//    array('%s')
//);
//// Get the ID of the inserted invoice
//$invoice_id = $wpdb->insert_id;
?>
