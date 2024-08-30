<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$invoice_id = '';
if (isset($_GET['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'] ) ) , 'invoice_details_by_id')) {
    $invoice_id = isset($_GET['invoice_id']) ? sanitize_key($_GET['invoice_id']) : '';
}
$invoice_details = dspp_get_invoice_details($invoice_id);
$shift_invoice = array_shift($invoice_details);
$decoded_details = json_decode($shift_invoice['invoice_items']);
$user_id = $shift_invoice['user_id'];
$user_details = get_user_by('ID', $user_id);
$statuses_list = dspp_get_all_invoice_statuses();
$invoice_status = $shift_invoice['invoice_status'];
$invoice_status_details = array_shift(dspp_get_invoice_status_by_id($invoice_status));
$status_name = $invoice_status_details['status_name'];
//echo '<pre>';
//print_r($invoice_status);
//exit;
$coupon_code = isset($shift_invoice['coupon_code']) ? $shift_invoice['coupon_code'] : 'N/A';
$coupon_value = isset($shift_invoice['coupon_value']) ? $shift_invoice['coupon_value'] : 0;
?>
<div class="container">
    <div id="message-box"></div>
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2 class="h5 mb-0">Invoice # <?php echo esc_html($shift_invoice['invoice_id']); ?></h2>
    </div>
    <!-- Main content -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom mb-8">
                <div class="card-body">
                    <h3 class="h6">Invoice Details</h3>
                    <p class="small mb-0">Invoice Date: <?php echo esc_html(gmdate("Y-m-d", strtotime($shift_invoice['invoice_date']))); ?></p>
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
                                <td colspan="3">INVOICE TOTAL</td>
                                <td class="text-end">$<?php echo esc_html($subtotal - $coupon_value); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-custom">
                <!-- Shipping information -->
                <div class="card-body">
                    <h3 class="h6">Invoice Status</h3>
                    <form id="update-invoice-status-admin" action="" method="post" <?php echo $invoice_status == 3 ? 'disabled' : ''; ?>>
                        <input type="hidden" name="invoice_id" value="<?php echo esc_attr($invoice_id); ?>">
                        <select style="width: 100%;" name="invoice_status_option" <?php echo $invoice_status == 3 ? 'disabled' : ''; ?>>
                            <?php foreach ($statuses_list as $status) {
                                ?><option value="<?php echo esc_attr($status['status_id']); ?>" <?php echo ($invoice_status == $status['status_id']) ? "selected" : ""; ?>><?php echo esc_html($status['status_name']); ?></option><?php }
                            ?>
                        </select><br><br>
                        <input type="submit" value="Update" class="btn btn-primary" <?php echo $invoice_status == 3 ? 'disabled' : ''; ?>/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>