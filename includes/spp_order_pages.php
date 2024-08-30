<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly?>
<h1><?php esc_html_e('Orders', 'digital-service-provider-crm'); ?></h1>
<!-- Add your tabs here -->
<h2 class="nav-tab-wrapper">
    <?php
    $status_id = -1;
    if (!isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'] ) ) , 'filter_order_on_status' )) {
        $status_id = isset($_GET['status_id']) ? sanitize_key($_GET['status_id']) : -1;
    }
    $statuses_list = dspp_get_all_statuses();
    $custom_orders = ($status_id == -1) ? dspp_get_custom_orders() : dspp_get_custom_orders_status_id($status_id);
    foreach ($statuses_list as $status) {
        ?>
    <a href="<?php echo esc_url(add_query_arg('status_id', $status['status_id'], esc_attr(admin_url('admin.php?page=spp_orders&nonce='. esc_attr(wp_create_nonce('filter_order_on_status')))))); ?>" class="nav-tab"><?php echo esc_html($status['status_name']); ?></a>
        <?php
    }
    ?>
</h2>

<br>
<table id="customers">
    <thead>
        <tr>
            <th><?php esc_html_e('Order', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Date', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Status', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Total', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Invoice', 'digital-service-provider-crm'); ?></th>
        </tr> 
    </thead>
    <tbody>
        <?php
        foreach ($custom_orders as $order) {
            $invoice = dspp_get_invoice_details($order['invoice_id']);
            $user_details = get_user_by('ID', $order['user_id']);
            $status_name = array_shift(dspp_get_status_by_id($order['order_status']));
            $shifted_array = array_shift($invoice);
            $order_title = '#' . $order['order_id'] . ' ' . $user_details->display_name;
            ?>
            <tr>
                <td class="product-name">
                    <a href="<?php echo esc_url(add_query_arg('order_id', $order['order_id'], esc_attr(admin_url('admin.php?page=dspp_orders_details&nonce='. esc_attr(wp_create_nonce('order_details_by_id')))))); ?>" class="order-view">
                        <strong><?php echo esc_html($order_title); ?></strong>
                    </a>
                </td>
                <td class="product-name"><?php echo esc_html($order['order_date']); ?></td>
                <td class="product-name"><?php echo esc_html($status_name['status_name']); ?></td>
                <td class="product-name"><?php echo esc_html($shifted_array['invoice_total']); ?></td>
                <td class="product-name"><?php echo esc_html($order['invoice_id']); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th><?php esc_html_e('Order', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Date', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Status', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Total', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Invoice', 'digital-service-provider-crm'); ?></th>
        </tr>
    </tfoot>
</table>