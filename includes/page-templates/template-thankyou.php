<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (isset($_GET['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])),'user-order-completed')) {
    $processor = sanitize_text_field($_GET['processor']);
    if ($processor == 'stripe' || $processor == 'paypal') {
        $order_id = sanitize_key($_GET['order_id']);
        $order_details = array_shift(dspp_get_custom_orders_by_id($order_id));
        $invoice_id = $order_details['invoice_id'];
        $billing_id = sanitize_key($_GET['billing_id']);
    } else {
        $order_id = sanitize_key($_GET['invoice_id']);
        $invoice_id = sanitize_key($_GET['invoice_id']);
        $billing_id = sanitize_key($_GET['billing_id']);
    }
} else {
    echo 'Nonce verification failed';
    exit;
}
$statuses_list = dspp_get_all_statuses();
$invoice_details = dspp_get_invoice_details($invoice_id);
$decoded_details = json_decode($invoice_details[0]['invoice_items']);
$subtotal_price = 0;
foreach ($decoded_details as $item) {
    if(isset($item->price) && isset($item->quantity)) {
        $subtotal_price += $item->price * $item->quantity;
    }
}
$order_date = gmdate('Y-m-d');
$user_id = get_current_user_id();
$user_details = get_user_by('ID', $user_id);
$billing_details = array_shift(dspp_get_billing_details_by_id($billing_id));
$method_name = "";
if ($processor == 'stripe') {
    $status_name = "Completed";
    $method_name = "Stripe Card";
} elseif ($processor == 'paypal') {
    $status_name = "Completed";
    $method_name = "Paypal";
} else {
    $status_name = "Payment Pending";
    $method_name = "Bank Transfer";
}
//echo "<pre>";
$coupon_code = isset($invoice_details[0]['coupon_code']) ? $invoice_details[0]['coupon_code'] : 'N/A';
$coupon_value = isset($invoice_details[0]['coupon_value']) ? $invoice_details[0]['coupon_value'] : 0;
//print_r($billing_details);
//print_r($invoice_details[0]['coupon_code']);
//print_r($invoice_details[0]['coupon_value']);
//exit;
?>
<div class="container">
    <h2 class="main-content__body" id="h2" data-lead-id="main-content-body">Order confirmed successfully!</h2>
    <!-- Main content -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card-custom mb-8">
                <div class="card-body">
                    <h3 class="h6">Order Details</h3>
                    <div class="mb-3 d-flex justify-content-between">
                        <div>
                            <span class="me-3">Order #<?php echo esc_html($order_id); ?></span><br>
                            <span class="me-3">Order Date: <?php echo esc_html($order_date); ?></span><br>
                            <span class="me-3">Order Status: <?php echo esc_html($status_name); ?></span>

                        </div>
                    </div>
                    <table class="table table-strip">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th class="text-end">Price</th>
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
                                    <td><?php echo esc_html($item->quantity); ?></td>
                                    <td class="text-end">$<?php echo esc_html($item->price * $item->quantity); ?></td>
                                </tr> 
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Subtotal</td>
                                <td class="text-end">$<?php echo esc_html($subtotal); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Discount (Code: <?php echo esc_html($coupon_code); ?>)</td>
                                <td class="text-danger text-end">-$<?php echo esc_html($coupon_value); ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="2">TOTAL</td>
                                <td class="text-end">$<?php echo esc_html($subtotal - $coupon_value); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-custom">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <h3 class="h6">Billing address</h3>
                            <address>
                                <strong><?php echo esc_html($user_details->user_nicename); ?></strong><br>
                                <?php echo esc_html($billing_details['billing_address']); ?><br>
                                <?php echo esc_html($billing_details['billing_city']); ?>, <?php echo esc_html($billing_details['billing_country']); ?> <?php echo esc_html($billing_details['billing_post_code']); ?><br>
                                <?php echo esc_html($user_details->user_email); ?>
                            </address>
                        </div>
                        <div class="col-lg-4">
                            <h3 class="h6">Payment Method</h3>
                            <p><?php echo esc_html($method_name); ?><br>
                                Total: $<?php echo esc_html($subtotal_price - $coupon_value); ?></p>
                        </div>
                        <div class="col-lg-4">
                            <?php
                            if ($processor == "manual_payment") {
                                $method = "Bank";
                                $details = array_shift(dspp_check_payment_method_enabled($method));
                                $enabled = ($details->enable == "true") ? true : false;
                                $method_id = $details->methode_id;
                                $settings = $details->methode_details;
                                $decoded = json_decode($settings);
                                ?>
                                <div>
                                    <h3 class="h6">Account Details</h3>
                                    <hr>
                                    <address>
                                        <p>
                                            <?php echo esc_html($decoded->client_or_strip); ?>
                                        </p>
                                    </address>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
