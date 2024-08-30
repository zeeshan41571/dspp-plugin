<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function dspp_get_all_services_cpt() {
    $custom_post_type = 'dspp_service';
    $args = array(
        'post_type' => $custom_post_type,
        'posts_per_page' => -1,
    );
    $custom_query = new WP_Query($args);
    $services = array();
    if ($custom_query->have_posts()) {
        while ($custom_query->have_posts()) : $custom_query->the_post();
            $services[] = get_post();
        endwhile;
        wp_reset_postdata();
    }
    return $services;
}

function dspp_save_order_form_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'save_order_form')){
        global $wpdb;
        parse_str(sanitize_text_field($_POST['formData']), $form_data);
        $form_title = $form_data['form-name'];
        $form_information = $form_data['form-information'];
        $form_type_id = $form_data['form-type'];
        $selectedValues = isset($_POST['selectedValues']) ? sanitize_text_field($_POST['selectedValues']) : 0;
        $form_html_data = $form_data['buildWrapContent'];
        $data_to_insert = array(
            'form_title' => sanitize_text_field($form_title),
            'form_information' => sanitize_textarea_field($form_information),
            'form_details' => sanitize_textarea_field($form_html_data),
            'form_status' => 'Active',
            'service_ids' => wp_json_encode($selectedValues),
            'form_type_id' => absint($form_type_id)
        );
        $wpdb->insert(
                $wpdb->prefix . 'sppcrm_order_forms', // Replace 'your_table_name' with your actual table name
                $data_to_insert
        );
        if ($wpdb->insert_id) {
            wp_send_json_success("Form saved successfully!");
        } else {
            wp_send_json_error('Error: Form could not be saved.');
        }
    } else {
        // Nonce is not valid, handle error
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

// Register the AJAX action
add_action('wp_ajax_dspp_save_order_form', 'dspp_save_order_form_callback');
add_action('wp_ajax_nopriv_dspp_save_order_form', 'dspp_save_order_form_callback');

function dspp_update_order_form_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'update_order_form')) {
        global $wpdb;
        parse_str(sanitize_text_field($_POST['formData']), $form_data);
        $form_id = absint($form_data['form_id']);
        $form_title = sanitize_text_field($form_data['form-name']);
        $form_information = sanitize_textarea_field($form_data['form-information']);
        $form_type_id = absint($form_data['form-type']);
        $selectedValues = isset($_POST['selectedValues']) ? sanitize_text_field($_POST['selectedValues']) : 0;
        $form_html_data = sanitize_textarea_field($form_data['buildWrapContent']);
        $data_to_update = array(
            'form_title' => $form_title,
            'form_information' => $form_information,
            'form_details' => $form_html_data,
            'service_ids' => wp_json_encode($selectedValues),
            'form_type_id' => $form_type_id,
        );
        $where = array('form_id' => $form_id);
        $result = $wpdb->update(
                $wpdb->prefix . 'sppcrm_order_forms', // Replace 'your_table_name' with your actual table name
                $data_to_update,
                $where
        );
        if ($result !== false) {
            wp_send_json_success("Form updated successfully!");
        } else {
            wp_send_json_error('Error: Form could not be updated.');
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_update_order_form', 'dspp_update_order_form_callback');
add_action('wp_ajax_nopriv_dspp_update_order_form', 'dspp_update_order_form_callback');

function dspp_get_all_services_list() {
    $custom_post_type = 'dspp_service';
    $args = array(
        'post_type' => $custom_post_type,
        'posts_per_page' => 10,
    );
    $custom_query = new WP_Query($args);
    $services = array();
    if ($custom_query->have_posts()) {
        while ($custom_query->have_posts()) : $custom_query->the_post();
            $service_info = array(
                'label' => get_the_title(),
                'value' => get_the_ID(),
                'selected' => false
            );
            $services[] = $service_info;
        endwhile;
        wp_reset_postdata();
    }
    return $services;
}

// Add this in your functions.php or a custom plugin file
add_action('wp_ajax_dspp_delete_order_form', 'dspp_delete_order_form_callback');

function dspp_delete_order_form_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'delete_order_form')){
        global $wpdb;
        $form_id = isset($_POST['form_id']) ? sanitize_key(intval($_POST['form_id'])) : 0;
        $table_name = $wpdb->prefix . 'sppcrm_order_forms';
        $deleted = $wpdb->delete($table_name, array('form_id' => $form_id), array('%d'));
        if ($deleted !== false) {
            echo wp_json_encode(array('success' => true, 'message' => 'Form deleted successfully'));
        } else {
            echo wp_json_encode(array('success' => false, 'message' => 'Failed to delete form'));
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

// Add this in your functions.php or a custom plugin file
add_action('wp_ajax_dspp_change_status_order_form', 'dspp_change_status_order_form_callback');

function dspp_change_status_order_form_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'change_status_order_form')){
        global $wpdb;
        $form_id = isset($_POST['form_id']) ? sanitize_key(intval($_POST['form_id'])) : 0;
        $new_status = isset($_POST['new_status']) ? sanitize_text_field($_POST['new_status']) : '';
        $table_name = $wpdb->prefix . 'sppcrm_order_forms';
        $updated = $wpdb->update($table_name, array('form_status' => $new_status), array('form_id' => $form_id), array('%s'), array('%d'));
        if ($updated !== false) {
            echo wp_json_encode(array('success' => true, 'message' => 'Form status changed successfully'));
        } else {
            echo wp_json_encode(array('success' => false, 'message' => 'Failed to change form status'));
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

function dspp_get_all_payment_methods_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'get_all_payment_methods')){
        $custom_post_type = 'dspp_service';
        $args = array(
            'post_type' => $custom_post_type,
            'posts_per_page' => 10,
        );
        $custom_query = new WP_Query($args);
        $services = array();
        if ($custom_query->have_posts()) {
            while ($custom_query->have_posts()) : $custom_query->the_post();
                $service_info = array(
                    'label' => get_the_title(),
                    'value' => get_the_ID(),
                    'selected' => false
                );
                $services[] = $service_info;
            endwhile;
            wp_reset_postdata();
        }
        if (count($services) > 0) {
            wp_send_json_success($services);
        } else {
            wp_send_json_error('Error: Form could not be saved.');
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

// Register the AJAX action
add_action('wp_ajax_dspp_get_all_payment_methods', 'dspp_get_all_payment_methods_callback');
add_action('wp_ajax_nopriv_dspp_get_all_payment_methods', 'dspp_get_all_payment_methods_callback');

function dspp_save_billing_details_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'save_billing_details_ajax')){
        global $wpdb;
        $form_data = sanitize_text_field($_POST['data']);
        $pairs = explode('&', $form_data);
        $user_data = array();
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair);
            $user_data[urldecode($key)] = urldecode($value);
        }
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
        $wpdb->insert($billing_table_name, $billing_data);
        $billing_id = $wpdb->insert_id;
        $response = array('status' => true, 'billing_id' => $billing_id, 'message' => 'Data inserted successfully!');
        wp_send_json($response);
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_save_billing_details_ajax', 'dspp_save_billing_details_callback');
add_action('wp_ajax_nopriv_dspp_save_billing_details_ajax', 'dspp_save_billing_details_callback');

// Add your AJAX handler for updating the order status
add_action('wp_ajax_dspp_update_order_status_admin', 'dspp_update_order_status_admin_callback');
add_action('wp_ajax_nopriv_dspp_update_order_status_admin', 'dspp_update_order_status_admin_callback');

function dspp_update_order_status_admin_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'update_order_status_admin')){
        global $wpdb;
        $formData = isset($_POST['formData']) ? sanitize_text_field($_POST['formData']) : '';
        $decoded_values = array();
        parse_str($formData, $decoded_values);
        $order_id = isset($decoded_values['order_id']) ? $decoded_values['order_id'] : '';
        $order_status_option = isset($decoded_values['order_status_option']) ? $decoded_values['order_status_option'] : '';
        if (!$order_id || !$order_status_option) {
            wp_send_json_error('Invalid order ID or status.');
        }
        $table_name = $wpdb->prefix . 'sppcrm_orders';
        $result = $wpdb->update(
                $table_name,
                array('order_status' => $order_status_option),
                array('order_id' => $order_id),
        );
        if ($result !== false) {
            wp_send_json_success('Order status updated successfully.');
        } else {
            wp_send_json_error('Failed to update order status.');
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_update_invoice_status_admin', 'dspp_update_invoice_status_admin_callback');
add_action('wp_ajax_nopriv_dspp_update_invoice_status_admin', 'dspp_update_invoice_status_admin_callback');

function dspp_update_invoice_status_admin_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'update_invoice_status_admin')){
        global $wpdb;
        $formData = isset($_POST['formData']) ? sanitize_text_field($_POST['formData']) : '';
        $decoded_values = array();
        parse_str($formData, $decoded_values);
        $invoice_id = isset($decoded_values['invoice_id']) ? $decoded_values['invoice_id'] : '';
        $invoice_status_option = isset($decoded_values['invoice_status_option']) ? $decoded_values['invoice_status_option'] : '';
        if (!$invoice_id || !$invoice_status_option) {
            wp_send_json_error('Invalid order ID or status.');
        }
        if ($invoice_status_option == '3') {
            $table_name = $wpdb->prefix . 'sppcrm_orders';
            $result = $wpdb->update(
                    $table_name,
                    array('active' => 1),
                    array('invoice_id' => $invoice_id),
            );
        }
        $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
        $result = $wpdb->update(
                $table_name,
                array('invoice_status' => $invoice_status_option),
                array('invoice_id' => $invoice_id),
        );
        if ($result !== false) {
            wp_send_json_success('Invoice status updated successfully.');
        } else {
            wp_send_json_error('Failed to update invoice status.');
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}
