<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly?>
<h1><?php esc_html_e('Invoices', 'digital-service-provider-crm'); ?></h1>
<!-- Add your tabs here -->
<h2 class="nav-tab-wrapper">
    <?php
    $status_id = -1;
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'filter_invoice_on_status' ) )
    {
        $status_id = isset($_GET['status_id']) ? sanitize_key(absint($_GET['status_id'])) : -1;
    }    
    $statuses_list = dspp_get_all_invoice_statuses();
    $custom_orders = ($status_id == -1) ? dspp_get_custom_invoices() : dspp_get_custom_invoice_status_id($status_id);
    foreach ($statuses_list as $status) {
        ?><a href="<?php echo esc_url(add_query_arg('status_id', $status['status_id'],'?page=spp_invoices&nonce='. esc_attr(wp_create_nonce('filter_invoice_on_status')))); ?>" class="nav-tab"><?php echo esc_html($status['status_name']); ?></a><?php
    }
    ?>
</h2>
<br>
<table id="customers">
    <thead>
        <tr>
            <th><?php esc_html_e('Invoice #', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Date', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Status', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Total', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Last Updated', 'digital-service-provider-crm'); ?></th>
        </tr> 
    </thead>
    <tbody>
        <?php foreach ($custom_orders as $order) : ?>
            <?php
            $user_details = get_user_by('ID', $order['user_id']);
            $invoice_status_details = array_shift(dspp_get_invoice_status_by_id($order['invoice_status']));
            $status_name = $invoice_status_details['status_name'];
            $order_title = '#' . $order['invoice_id'] . ' ' . $user_details->display_name;
            ?>
            <tr>
                <td class="product-name"><a href="<?php echo esc_url(esc_attr(admin_url('admin.php?page=dspp_invoice_details&nonce='. esc_attr(wp_create_nonce('invoice_details_by_id')).'&invoice_id=' . $order['invoice_id']))); ?>" class="order-view"><strong><?php echo esc_html($order_title); ?></strong></a></td>
                <td class="product-name"><?php echo esc_html($order['invoice_date']); ?></td>
                <td class="product-name"><?php echo esc_html($status_name); ?></td>
                <td class="product-name"><?php echo esc_html($order['invoice_total']); ?></td>
                <td class="product-name"><?php echo esc_html($order['invoice_updated_time']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th><?php esc_html_e('Invoice #', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Date', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Status', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Total', 'digital-service-provider-crm'); ?></th>
            <th><?php esc_html_e('Last Updated', 'digital-service-provider-crm'); ?></th>
        </tr>
    </tfoot>
</table>