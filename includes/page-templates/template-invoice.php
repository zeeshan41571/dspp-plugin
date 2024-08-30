<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$user_id = get_current_user_id();
$custom_orders = dspp_get_invoices_by_user($user_id);
?>
<h2>Invoices</h2>
<table id="user_orders">
    <thead>
        <tr>
            <th>Invoice ID</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
            <th>Last Updated</th>
        </tr> 
    </thead>
    <tbody>
        <?php
        foreach ($custom_orders as $order) {
            $invoice = $order['invoice_id'];
            $invoice_status_details = array_shift(dspp_get_invoice_status_by_id($order['invoice_status']));
            $status_name = $invoice_status_details['status_name'];
//            print_r($status_name);
            $user_details = get_user_by('ID', $order['user_id']);
            $order_title = '#' . $order['invoice_id'] . ' ' . $user_details->display_name;
            ?>
            <tr>
                <td class="product-name">
                    <a href="<?php echo esc_url(site_url(). '/bits-single-invoice?invoice_id=' . $order['invoice_id']); ?>" class="order-view"><strong><?php echo esc_html($order_title); ?></strong></a>
                </td>
                <td class="product-name"><?php echo esc_html($order['invoice_date']); ?></td>
                <td class="product-name" style="text-transform:capitalize"><?php echo esc_html($status_name); ?></td>
                <td class="product-name"><?php echo esc_html($order['invoice_total']); ?></td>
                <td class="product-name"><?php echo esc_html($order['invoice_updated_time']); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Invoice ID</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
            <th>Last Updated</th>
        </tr>
    </tfoot>
</table>
