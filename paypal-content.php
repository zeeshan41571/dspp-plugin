<?php
if (!defined('ABSPATH'))
    exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$method = "Paypal";
$details = array_shift(dspp_check_payment_method_enabled($method));
$enabled = 0;
$method_id = 0;
$stripe_publishable_key = '';
$stripe_secret_key = '';
if (isset($details) && !empty($details)) {
    $enabled = ($details->enable == "true") ? true : false;
    $method_id = $details->methode_id;
    $settings = $details->methode_details;
    $decoded = json_decode($settings);
    $stripe_publishable_key = $decoded->client_or_strip;
    $stripe_secret_key = $decoded->secret_key;
}
?>
<div id="paypal_tab" class="payment-method-tab-content" style="margin-top: 1%">
    <table>
        <tr>
            <td><label for="enable_paypal"><?php echo esc_html_e('Enable PayPal:', 'digital-service-provider-crm') ?></label></td>
            <td><input type="checkbox" id="enable_paypal" name="enable_stripe" <?php echo $enabled ? 'checked="checked"' : ''; ?>></td>
        </tr>
        <tr>
            <td><label for="stripe_publishable_key"><?php echo esc_html_e('PayPal User ID:', 'digital-service-provider-crm') ?></label></td>
            <td><input type="text" id="stripe_publishable_key" name="stripe_publishable_key" value="<?php echo esc_attr($stripe_publishable_key); ?>" required="required"><br></td>
        </tr>
        <tr>
            <td><label for="stripe_secret_key"><?php echo esc_html_e('PayPal Secret Key:', 'digital-service-provider-crm') ?></label></td>
            <td><input type="text" id="stripe_secret_key" name="stripe_secret_key" value="<?php echo esc_attr($stripe_secret_key); ?>" required="required"><br></td>
        </tr>
        <tr>
        <input type="hidden" id="method_id" name="method_id" value="<?php echo esc_attr($method_id); ?>">
        <td><button class="paypal_save btn btn-primary"><?php echo esc_html_e('Save', 'digital-service-provider-crm') ?></button></td>
        </tr>
    </table>
    <span class="success"></span>
</div>