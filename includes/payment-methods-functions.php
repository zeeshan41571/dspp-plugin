<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function dspp_check_payment_method_enabled($method) {
    global $wpdb;
    $user_id = 1;
    $table_name = $wpdb->prefix . 'sppcrm_payment';
    $payment_methods = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE `methode` = %s AND `user_id` = %d",
            $table_name,
            $method,
            $user_id
    ));
    return $payment_methods;
}

function dspp_add_payment_methode() {
    global $wpdb;
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'add_payment_methode' ))
    {
        $checkbox = sanitize_text_field($_POST['enable']);
        $client_or_strip = sanitize_text_field($_POST['user_plublish']);
        $secret_key = sanitize_text_field($_POST['secret_key']);
        $methode = sanitize_text_field($_POST['methode']);
        $method_id = sanitize_key($_POST['method_id']);
        $user_id = get_current_user_id();
        $errors = false;
//        print_r($method_id);
        if ($method_id) {
            $table_name = $wpdb->prefix . 'sppcrm_payment';
            $data = array(
                'methode' => $methode,
                'enable' => $checkbox,
                'user_id' => $user_id,
                'methode_details' => wp_json_encode(array(
                    'client_or_strip' => $client_or_strip,
                    'secret_key' => $secret_key,
                )),
            );
            $where = array(
                'methode_id' => $method_id,
            );
            $result = $wpdb->update($table_name, $data, $where);
            if ($result) {
                $errors = true;
                $message = 'Details updated successfully.';
            } else {
                $message = 'Error! Failed to update the details.';
            }
        } else {
            $result = $wpdb->insert(
                    $wpdb->prefix . 'sppcrm_payment', // Adjust the table name based on your actual table name
                    array(
                        'methode' => $methode,
                        'enable' => $checkbox,
                        'user_id' => $user_id,
                        'methode_details' => wp_json_encode(array(
                            'client_or_strip' => $client_or_strip,
                            'secret_key' => $secret_key,
                        )),
                    ),
                    array('%s')
            );
            if ($result) {
                $errors = true;
                $message = 'Payment method enabled successfully.';
            } else {
                $message = 'Error! Failed to enabled the payment method.';
            }
        }
        if ($errors) {
            wp_send_json_success($message);
            wp_die();
        } else {
            wp_send_json_error($message);
            wp_die();
        }
    } else {
        wp_send_json_error('Nonce verification failed');
        wp_die();
    }
    wp_die(); // Always include this at the end of your Ajax callback function
}

add_action("wp_ajax_dspp_add_payment_methode", "dspp_add_payment_methode");
add_action("wp_ajax_nopriv_dspp_add_payment_methode", "dspp_add_payment_methode");

function dspp_save_billing_details($user_data) {
    global $wpdb;
    $user_id = get_current_user_id();
    $address_1 = $user_data['address_1'];
    $address_city = $user_data['address_city'];
    $address_state = $user_data['address_state'];
    $address_postcode = $user_data['address_postcode'];
    $company = $user_data['company'];
    $address_country = $user_data['address_country'];
    $tax_id = $user_data['tax_id'];
    $billing_table_name = $wpdb->prefix . 'sppcrm_billing_details';
    $billing_data = array(
        'user_id' => $user_id,
        'billing_address' => $address_1,
        'billing_city' => $address_city,
        'billing_state' => $address_state,
        'billing_post_code' => $address_postcode,
        'billing_country' => $address_country,
        'billing_company' => $company,
        'billing_tax_id' => $tax_id,
    );
    $existing_billing_address = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM %i WHERE user_id = %d", $billing_table_name, $user_id)
    );
    if ($existing_billing_address) {
        $wpdb->update($billing_table_name, $billing_data, array('user_id' => $user_id));
        $billing_id = $existing_billing_address->billing_id;
    } else {
        $wpdb->insert($billing_table_name, $billing_data);
        $billing_id = $wpdb->insert_id;
    }
    return $billing_id;
}

function dspp_udpate_invoice_staus($invoice_id, $invoice_status) {
    global $wpdb;
    $update = $wpdb->update(
            $wpdb->prefix . "sppcrm_invoice_details",
            array(
                'invoice_status' => "$invoice_status",
                'invoice_updated_time' => gmdate('Y-m-d H:i:s')
            ),
            array('invoice_id' => $invoice_id)
    );
    if ($update) {
        return $invoice_id;
    }
    return $invoice_id;
}

function dspp_save_payment_method_info($method, $stripeToken) {
    global $wpdb;
    $user_id = get_current_user_id();
    $method_table_name = $wpdb->prefix . 'sppcrm_payment_methods';
    $payment_data = array(
        'user_id' => $user_id,
        'method_name' => $method,
        'card_number' => $stripeToken, // Replace with the actual card number
        'expiry_date' => gmdate('Y-m-d'),
    );
    $wpdb->insert($method_table_name, $payment_data);
    $method_id = $wpdb->insert_id;
    return $method_id;
}

function dspp_save_order_data($invoice_id, $method_id, $billing_id, $order_status, $active = 0) {
    global $wpdb;
    $user_id = get_current_user_id();
    $orders_table_name = $wpdb->prefix . 'sppcrm_orders';
    $order_data = array(
        'user_id' => $user_id,
        'invoice_id' => $invoice_id,
        'method_id' => $method_id,
        'billing_id' => $billing_id,
        'order_status' => $order_status,
        'active' => $active
    );
    $wpdb->insert($orders_table_name, $order_data);
    $order_id = $wpdb->insert_id;
    return $order_id;
}

function dspp_get_custom_post_categories($post_id, $excluded_categories) {
    $taxonomy = 'dspp_services_category';
    $categories = wp_get_post_terms($post_id, $taxonomy);
    $post_categories = wp_list_pluck($categories, 'term_id');
    if (!empty($excluded_categories) && !empty($post_categories)) {
        $exclude_coupon = !empty(array_intersect($post_categories, $excluded_categories));
    } else {
        $exclude_coupon = false;
    }
    return $exclude_coupon;
}

function dspp_check_coupon() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'check_coupon' ))
    {
        $user_id_wp = get_current_user_id();
        $custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
        $decoded_array = json_decode($custom_website_data, true);
        $data_array = (isset($decoded_array) && !empty($decoded_array)) ? $decoded_array : array();
        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        if (isset($data_array['custom_website_data_' . $user_id_wp]['coupon_code'])) {
            $response = array(
                'success' => false,
                'coupon_type' => 'nill',
                'coupon_value' => 'nill',
                'message' => 'Error! Only one coupon can be applied.'
            );
        } else {
            // Query to get the coupon post by its title (coupon code)
            $coupon_query = new WP_Query(array(
                'post_type' => 'dspp_coupons',
                'posts_per_page' => 1,
                'title' => $coupon_code,
            ));
            $is_valid_coupon = false;
            $message = '';
            if ($coupon_query->have_posts()) {
                $coupon_post = $coupon_query->posts[0];
                $coupon_discount_type = get_post_meta($coupon_post->ID, 'coupon_discount_type', true);
                $coupon_value = get_post_meta($coupon_post->ID, 'coupon_value', true);
                $coupon_expiry_date = get_post_meta($coupon_post->ID, 'coupon_expiry_date', true);
                $excluded_categories = get_post_meta($coupon_post->ID, 'excluded_categories', true);
                $current_price = 0;
                foreach ($decoded_array as $site_data) {
                    foreach ($site_data as $key => $site) {
                        $post_id = $site['post_id'];
                        $exclude_coupon = dspp_get_custom_post_categories($post_id, $excluded_categories);
                        if (!$exclude_coupon) {
                            $current_price += $site['price'];
                        }
                    }
                }
                if (strtotime($coupon_expiry_date) >= current_time('timestamp') && $current_price > 0) {
                    $data_array['custom_website_data_' . $user_id_wp]['coupon_code'] = $coupon_code;
                    $data_array['custom_website_data_' . $user_id_wp]['coupon_value'] = $coupon_value;
                    $data_array['custom_website_data_' . $user_id_wp]['coupon_discount_type'] = $coupon_discount_type;
                    if ($coupon_discount_type == 'percentage') {
                        $percentage = $current_price * ($coupon_value / 100);
                        $data_array['custom_website_data_' . $user_id_wp]['discount_price'] = $percentage;
                        $is_valid_coupon = true;
                        $message = 'Success! Coupon code is valid!';
                    } else if ($coupon_discount_type == 'fixed_discount') {
                        $data_array['custom_website_data_' . $user_id_wp]['discount_price'] = $coupon_value;
                        $is_valid_coupon = true;
                        $message = 'Success! Coupon code is valid!';
                    } else {
                        $message = 'Error! Invalid coupon discount type.';
                    }
                } else {
                    $message = 'Error! Coupon code is expired or the service is exluded from discounts.';
                }
            } else {
                $message = 'Error! Coupon code is invalid.';
            }
            if ($is_valid_coupon) {
                $encoded_array = wp_json_encode($data_array);
                update_user_meta($user_id_wp, 'custom_website_data', $encoded_array);
            }
            $response = array(
                'success' => $is_valid_coupon,
                'coupon_type' => $coupon_discount_type,
                'coupon_value' => $coupon_value,
                'message' => $message
            );
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_send_json($response);
}

add_action('wp_ajax_dspp_check_coupon', 'dspp_check_coupon');
add_action('wp_ajax_nopriv_dspp_check_coupon', 'dspp_check_coupon');

function dspp_view_cart_button () {
    
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'dspp_view_cart_button' ))
    {
        
        $user_id_wp = get_current_user_id();
        
        $response = '';
        $custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
        
        if (!empty($custom_website_data)) {
            $decoded_array = json_decode($custom_website_data, true);
            if(isset($decoded_array['custom_website_data_' . $user_id_wp]) && count($decoded_array['custom_website_data_' . $user_id_wp])> 0) {
                $response = array(
                    'success' => true,
                    'message' => 'Redirect to cart'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'No items in the cart'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'No items in the cart'
            );
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_send_json($response);
}
add_action('wp_ajax_dspp_view_cart_button', 'dspp_view_cart_button');
add_action('wp_ajax_nopriv_dspp_view_cart_button', 'dspp_view_cart_button');

function dspp_bits_generate_invoice () {
    global $wpdb;
    $response = '';
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'dspp_bits_generate_invoice' ))
    {   
        $user_id_wp = get_current_user_id();
//        $billing_address = dspp_get_billing_details_by_user_id($user_id_wp);
//        $user_details = wp_get_current_user();
        // Get custom website data from user meta
        $custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
        $decoded_array = json_decode($custom_website_data, true);
        $cart_data = isset($decoded_array['custom_website_data_' . $user_id_wp]) ? $decoded_array['custom_website_data_' . $user_id_wp] : array();
        $json_data = wp_json_encode($cart_data);
        $cart_total = 0;
        foreach ($cart_data as $item) {
            if (isset($item['price'])) {
                $cart_total += $item['price'] * $item['quantity'];
            }
        }
        $coupon_code = '';
        $coupon_value = 0;
        if (isset($cart_data['coupon_code'])) {
            $coupon_code = $cart_data['coupon_code'];
            $coupon_value = $cart_data['coupon_discount_type'] == 'fixed_discount' ? $cart_data['coupon_value'] : (($cart_data['coupon_value'] / 100) * $cart_total);
            $cart_total -= $coupon_value;
        }
        $statuses = dspp_get_invoices_status_id_by_name('Abandoned Checkout');
        $status_id = !empty($statuses) ? $statuses[0]['status_id'] : 0;
        $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
        $wpdb->insert(
            $table_name,
            array(
                'invoice_items' => $json_data,
                'invoice_total' => $cart_total,
                'invoice_status' => $status_id,
                'user_id' => $user_id_wp,
                'coupon_code' => $coupon_code,
                'coupon_value' => $coupon_value,
            ),
            array('%s')
        );
        // Get the ID of the inserted invoice
        $invoice_id = $wpdb->insert_id;
        $response = array(
            'success' => true,
            'invoice_id' => $invoice_id,
            'message' => 'No items in the cart'
        );
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_send_json($response);
}
add_action('wp_ajax_dspp_bits_generate_invoice', 'dspp_bits_generate_invoice');
add_action('wp_ajax_nopriv_dspp_bits_generate_invoice', 'dspp_bits_generate_invoice');

function dspp_bits_payment_processor () {
    global $wpdb;
    $response = '';
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'dspp_bits_payment_processor' ))
    {
        $order_id = '';
        $formData = isset($_POST['formData']) ? sanitize_textarea_field(wp_unslash($_POST['formData'])) : '';
//        print_r($formData);
        
        parse_str(urldecode($formData), $params);
//        print_r($params);
//        exit;
        $processer = sanitize_text_field($params['processor']);
        $billing_id = sanitize_key($params['billing_id']);
        $invoice_id = sanitize_key($params['invoice_id']);
        $user_id = get_current_user_id();
        $invoice_details = dspp_get_invoice_details($invoice_id);
        $cart_data = json_decode($invoice_details[0]['invoice_items'], true);
        $cart_total = 0;
        foreach ($cart_data as $item) {
            if (isset($item['price'])) {
                $cart_total += $item['price'] * $item['quantity'];
            }
        }
        if ($processer == "stripe_card") {
            $stripeToken = sanitize_text_field($params['stripeToken']);
            $cart_total = sanitize_key($params['cart_total']);
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

        } else if ($processer == 'paypal') {
            if (isset($params['paypal_order_id']) && !empty(sanitize_key($params['paypal_order_id']))) {
                $paypal_order_id = sanitize_key($params['paypal_order_id']);
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
        } 
        $response = array(
            'success' => true,
            'url' => $url,
            'message' => 'Redirect to url'
        );
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_send_json($response);
}
add_action('wp_ajax_dspp_bits_payment_processor', 'dspp_bits_payment_processor');
add_action('wp_ajax_nopriv_dspp_bits_payment_processor', 'dspp_bits_payment_processor');