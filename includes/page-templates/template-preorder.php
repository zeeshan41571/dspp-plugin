<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$user_id = get_current_user_id();
$custom_orders = dspp_get_orders_by_user($user_id);
?>
<h2>Recent Orders</h2>
<br>
<table id="user_orders">
    <thead>
        <tr>
            <th>Order</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
            <th>Invoice</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($custom_orders as $order) : ?>
            <?php
            $invoice = dspp_get_invoice_details($order['invoice_id']);
            $user_details = get_user_by('ID', $order['user_id']);
            $status_name = array_shift(dspp_get_status_by_id($order['order_status']))['status_name'];
            $shifted_array = array_shift($invoice);
            $order_title = '#' . esc_html($order['order_id']) . ' ' . esc_html($user_details->display_name);
            ?>
            <tr>
                <td class="product-name">
                    <a href="<?php echo esc_url(site_url().'/bits-single-order?order_id='.esc_html($order['order_id'])); ?>" class="order-view"><strong><?php echo esc_html($order_title); ?></strong></a>
                </td>
                <td class="product-name"><?php echo esc_html($order['order_date']); ?></td>
                <td class="product-name"><?php echo esc_html($status_name); ?></td>
                <td class="product-name"><?php echo esc_html($shifted_array['invoice_total']); ?></td>
                <td class="product-name"><?php echo esc_html($order['invoice_id']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Order</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
            <th>Invoice</th>
        </tr>
    </tfoot>
</table>
