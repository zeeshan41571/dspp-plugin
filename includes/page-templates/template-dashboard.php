<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!is_user_logged_in()) {
    wp_logout();
    $redirect_url = site_url('/bits-login');
    wp_redirect($redirect_url);
    exit();
}
$user_id = get_current_user_id();
$user_data = get_userdata($user_id);
$completed_orders = dspp_get_complete_orders_by_user($user_id);
$open_orders = dspp_get_open_orders_by_user($user_id);
$user_name = '';
if(isset($user_data->first_name) && isset($user_data->last_name)) {
    $user_name = $user_data->first_name.' '.$user_data->last_name;
} else {
    $user_name = $user_data->user_nicename;
}
?>
<div class="content">
    <div class="container">
        <h1><?php printf('Welcome, %s !', esc_html($user_name));?></h1>
        <section class="p-4 mt-4">
            <div class="row">
                <div class="col text-center">
                    <div class="text-muted mb-1"><?php echo esc_html_e('Open Orders', 'digital-service-provider-crm');?></div>
                    <h2 class="h1 m-0"><?php printf('%s', count($open_orders));?></h2>
                </div>
                <div class="col text-center">
                    <div class="text-muted mb-1"><?php echo esc_html_e('Completed Orders', 'digital-service-provider-crm');?></div>
                    <h2 class="h1 m-0"><?php printf('%s', count($completed_orders));?></h2>
                </div>
                <div class="col text-center">
                    <div class="text-muted mb-1"><?php echo esc_html_e('Active Subscriptions', 'digital-service-provider-crm');?></div>
                    <h2 class="h1 m-0"><?php printf('0');?></h2>
                </div>
            </div>
        </section>
        <h2><?php echo esc_html_e('Unpaid invoices', 'digital-service-provider-crm');?></h2>
        <table class="table" id="user_invoices_dashboard">
            <thead>
                <tr>
                    <th><?php echo esc_html_e('Invoice ID', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Date', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Status', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Total', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Last Updated', 'digital-service-provider-crm');?></th>
                </tr> 
            </thead>
            <tbody>
                <?php
                echo '<pre>';
                $custom_invoices = dspp_get_unpaid_invoices_by_user($user_id);
                foreach ($custom_invoices as $order) {
                    $invoice = $order['invoice_id'];
                    $invoice_status_details = array_shift(dspp_get_invoice_status_by_id($order['invoice_status']));
                    $status_name = $invoice_status_details['status_name'];
                    $user_details = get_user_by('ID', $order['user_id']);
                    $order_title = '#' . $order['invoice_id'] . ' ' . $user_details->display_name;
                    echo '<tr>';
                    echo '<td class="product-name"><a href="' . esc_url( site_url('/bits-single-invoice?invoice_id=' . $order['invoice_id']) ) . '" class="order-view"><strong>' . esc_html($order_title) . '</strong></a></td>';
                    echo '<td class="product-name">' . esc_html($order['invoice_date']) . '</td>';
                    echo '<td class="product-name" style="text-transform:capitalize">' . esc_html($status_name) . '</td>';
                    echo '<td class="product-name">' . esc_html($order['invoice_total']) . '</td>';
                    echo '<td class="product-name">' . esc_html($order['invoice_updated_time']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th><?php echo esc_html_e('Invoice ID', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Date', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Status', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Total', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Last Updated', 'digital-service-provider-crm');?></th>
                </tr>
            </tfoot>
        </table>
        <!-- List of recent orders, visible when empty -->
        <h2><?php echo esc_html_e('Recent orders', 'digital-service-provider-crm');?></h2>
        <table id="user_orders_dashboard">
            <thead>
                <tr>
                    <th><?php echo esc_html_e('Order', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Date', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Status', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Total', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Invoice', 'digital-service-provider-crm');?></th>
                </tr> 
            </thead>
            <tbody>
                <?php
                $custom_orders = dspp_get_orders_by_user($user_id);
                foreach ($custom_orders as $order) {
                    $invoice = dspp_get_invoice_details($order['invoice_id']);
                    $user_details = get_user_by('ID', $order['user_id']);
                    $status_name = array_shift(dspp_get_status_by_id($order['order_status']));
                    $shifted_array = array_shift($invoice);
                    $order_title = '#' . $order['order_id'] . ' ' . $user_details->display_name;
                    echo '<tr>';
                    echo '<td class="product-name"><a href="' . esc_url( site_url('/bits-single-order?order_id=' . $order['order_id']) ) . '" class="order-view"><strong>' . esc_html($order_title). '</strong></a></td>';
                    echo '<td class="product-name">' . esc_html($order['order_date']) . '</td>';
                    echo '<td class="product-name">' . esc_html($status_name['status_name']) . '</td>';
                    echo '<td class="product-name">' . esc_html($shifted_array['invoice_total']) . '</td>';
                    echo '<td class="product-name">' . esc_html($order['invoice_id']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th><?php echo esc_html_e('Order', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Date', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Status', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Total', 'digital-service-provider-crm');?></th>
                    <th><?php echo esc_html_e('Invoice', 'digital-service-provider-crm');?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>