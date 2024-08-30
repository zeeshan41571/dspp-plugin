<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (isset( $_POST['payment_page_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['payment_page_nonce'])) , 'payment_page_action' ))
{
    $billing_id = sanitize_key($_GET['billing_id']);
    $invoice_id = sanitize_key($_GET['invoice_id']);
} else {
    $billing_id = sanitize_key($_GET['billing_id']);
    $invoice_id = sanitize_key($_GET['invoice_id']);
}
$user_id_wp = get_current_user_id();
$invoice_details = dspp_get_invoice_details($invoice_id);
$decoded_details = json_decode($invoice_details[0]['invoice_items'], true);
$cart_data = json_decode($invoice_details[0]['invoice_items'], true);
$cart_total = 0;
foreach ($cart_data as $item) {
    if (isset($item['price'])) {
        $cart_total += $item['price'] * $item['quantity'];
    }
}
if (isset($cart_data['coupon_code'])) {
    $coupon_value = $cart_data['coupon_discount_type'] == 'fixed_discount' ? $cart_data['coupon_value'] : (($cart_data['coupon_value'] / 100) * $cart_total);
    $cart_total = $cart_total - $coupon_value;
}
?>
<div class="row">
    <div class="col-sm-6">
        <form action="<?php echo esc_url(site_url("/bits-payment-processor/")) ?>" method="post" class="formbuilder-form" id="payment-form">
            <?php wp_nonce_field('payment_processor_action', 'payment_processor_nonce'); ?>
            <input type="hidden" name="invoice_id" value="<?php echo esc_attr($invoice_id); ?>" />
            <input type="hidden" id="cart_total" name="cart_total" value="<?php echo esc_attr($cart_total); ?>"/>
            <input type="hidden" id="billing_id" name="billing_id" value="<?php echo esc_attr($billing_id); ?>"/>
            <div class="form-group form-group-lg" data-field="">
                <h2><a href="<?php echo esc_url(site_url("/bits-checkout/")) ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></a> Payment method</h2>
                <div class="list-group payment-methods">
                    <?php
                    $enabled_stripe = false;
                    $enabled_paypal = false;
                    $details_bank = array_shift(dspp_check_payment_method_enabled("Bank"));
//                    print_r($details_bank);
                    $enabled_bank = (isset($details_bank->enable) && $details_bank->enable == "true") ? true : false;
                    if ($enabled_bank) {
                        ?>
                        <div class="list-group-item">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="processor" class="custom-control-input" id="manual-payment" value="manual_payment" <?php echo (!$enabled_stripe && !$enabled_paypal) ? 'checked="checked"' : '' ?>/>
                                <label for="manual-payment" class="custom-control-label d-flex align-items-center">
                                    <div class="flex-fill">Bank transfer</div>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <input type="submit" value="Complete Purchase" class="btn btn-primary btn-block btn-lg submit_cart" <?php echo ($enabled_bank) ? '' : 'style="display:none !important;"' ?> <?php echo ($enabled_stripe || $enabled_paypal) ? 'style="display:none !important;"' : '' ?>/>
        </form>
        <?php if (!$enabled_stripe && !$enabled_paypal && !$enabled_bank) {
            ?>
                <div class="no-payment-method alert alert-danger">No payment method found!</div>
        <?php
            }
        ?>
    </div>

    <div class="col-sm-6">
        <aside class="mr-auto checkout-right">
            <div class="sticky">
                <div class="invoice-items" id="preview">
                    <h2 class="mb-4">Summary</h2>
                    <div class="cart-contents">
                        <?php
                        foreach ($cart_data as $cart_items) {
                            if (!is_array($cart_items)) {
                                continue;
                            }
                            ?>
                            <div class="mb-4 d-flex justify-content-between">
                                <div>
                                    <div class="text-500"><?php echo esc_html($cart_items['site']); ?></div>
                                    <div>
                                        <span class="mr-1 text-muted">Qty</span> <?php echo esc_html($cart_items['quantity']); ?>
                                    </div>
                                </div>
                                <div class="text-right text-500">
                                    $<?php echo esc_html($cart_items['price']); ?>
                                </div>
                            </div>

                        <?php } ?>
                        <hr />
                        <?php if (isset($cart_data['coupon_code'])) {
                            ?>
                            <div class="mb-4 d-flex justify-content-between">
                                <div>
                                    <div class="text-500">Discount (<?php echo esc_html($cart_data['coupon_code']) ?>)</div>
                                    <div class="text-muted">USD</div>
                                </div>
                                <div class="text-right">
                                    <h2 class="margin-0">
                                        $<?php echo esc_html(($coupon_value == 0 ) ? 0 : $coupon_value); ?>
                                    </h2>
                                </div>
                            </div>
                        <?php }
                        ?>
                        <!-- Show total and payment terms -->
                        <div class="mb-4 d-flex justify-content-between">
                            <div>
                                <div class="text-500">Total</div>
                                <div class="text-muted">USD</div>
                            </div>
                            <div class="text-right">
                                <h2 class="margin-0">
                                    $<?php echo esc_html($cart_total); ?>
                                </h2>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>