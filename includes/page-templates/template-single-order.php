<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (isset( $_POST['single_order_page_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['single_order_page_nonce'])) , 'single_order_page_action' ))
{
    $order_id = isset($_GET['order_id']) ? sanitize_key($_GET['order_id']) : '';
} else {
    $order_id = isset($_GET['order_id']) ? sanitize_key($_GET['order_id']) : '';
}

$order_details = array_shift(dspp_get_custom_orders_by_id($order_id));
$statuses_list = dspp_get_all_statuses();
$invoice_id = $order_details['invoice_id'];
$invoice_details = dspp_get_invoice_details($invoice_id);
$decoded_details = json_decode($invoice_details[0]['invoice_items']);
$subtotal_price = 0;
foreach ($decoded_details as $item) {
    if(isset($item->price) && isset($item->quantity)) {
        $subtotal_price += $item->price * $item->quantity;
    }
}
$method_id = $order_details['method_id'];
$payment_method = array_shift(dspp_get_payment_method_by_id($method_id));
$billing_id = $order_details['billing_id'];
$billing_details = array_shift(dspp_get_billing_details_by_id($billing_id));
$order_date = $order_details['order_date'];
$order_status = $order_details['order_status'];
$status_name = array_shift(dspp_get_status_by_id($order_status));
$user_id = $order_details['user_id'];
$user_details = get_user_by('ID', $user_id);
$method_name = "";
if ($payment_method['method_name'] == 'stripe_card') {
    $method_name = "Stripe Card";
} elseif ($payment_method['method_name'] == 'paypal') {
    $method_name = "Paypal";
} else {
    $method_name = "Bank Transfer";
}
$coupon_code = isset($invoice_details[0]['coupon_code']) ? $invoice_details[0]['coupon_code'] : 'N/A';
$coupon_value = isset($invoice_details[0]['coupon_value']) ? $invoice_details[0]['coupon_value'] : 0;
?>
<div class="container">
    <div id="message-box"></div>
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2 class="h5 mb-0">Order # <?php echo esc_html($order_id); ?></h2>
        <h2 class="h5 mb-0">Order Date: <?php echo esc_html(gmdate("Y-m-d", strtotime($order_date))); ?></h2>
        <h2 class="h5 mb-0">Order Status: <?php echo esc_html($status_name['status_name']); ?></h2>
    </div>

    <!-- Main content -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card-custom">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <h3 class="h6">Billing address</h3>
                            <hr>
                            <address>
                                <strong><?php echo esc_html($user_details->user_nicename); ?></strong><br>
                                <?php echo esc_html($billing_details['billing_address']); ?><br>
                                <?php echo esc_html($billing_details['billing_city']); ?>, <?php echo esc_html($billing_details['billing_country']); ?> <?php echo esc_html($billing_details['billing_post_code']); ?><br>
                                <?php echo esc_html($user_details->user_email); ?>
                            </address>
                        </div>
                        <div class="col-lg-4">
                            <h3 class="h6">Shipping Information</h3>
                            <hr>
                            <address>
                                <strong><?php echo esc_html($user_details->user_nicename); ?></strong><br>
                                <?php echo esc_html($billing_details['billing_address']); ?><br>
                                <?php echo esc_html($billing_details['billing_city']); ?>, <?php echo esc_html($billing_details['billing_country']); ?> <?php echo esc_html($billing_details['billing_post_code']); ?><br>
                                <?php echo esc_html($user_details->user_email); ?>
                            </address>
                        </div>
                        <div class="col-lg-4">
                            <h3 class="h6">Payment Method</h3>
                            <hr>
                            <p><?php echo esc_html($method_name); ?><br>
                                Total: $<?php echo esc_html($subtotal_price - $coupon_value); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-custom mb-8">
                <div class="card-body">
                    <h3 class="h6">Order Details</h3>
                    <table class="table table-strip">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Item Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal = 0;
                            foreach ($decoded_details as $item) {
                                if (!is_object($item)) {
                                    continue;
                                }
                                $subtotal += $item->price * $item->quantity;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <div class="flex-lg-grow-1">
                                                <h6 class="small mb-0"><a href="#" class="text-reset"><?php echo esc_html($item->site); ?></a></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?php echo esc_html($item->price); ?></td>
                                    <td><?php echo esc_html($item->quantity); ?></td>
                                    <td class="text-end">$<?php echo esc_html($item->price * $item->quantity); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Subtotal</td>
                                <td class="text-end">$<?php echo esc_html($subtotal); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3">Discount (Code: <?php echo esc_html($coupon_code); ?>)</td>
                                <td class="text-danger text-end">-$<?php echo esc_html($coupon_value); ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="3">ORDER TOTAL</td>
                                <td class="text-end">$<?php echo esc_html($subtotal - $coupon_value); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>