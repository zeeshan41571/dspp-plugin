<?php
if (!defined('ABSPATH'))
    exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$method = "Strip";
$details = array_shift(dspp_check_payment_method_enabled($method));
$enabled = 0;
$method_id = 0;
$stripe_publishable_key = '';
$stripe_secret_key = '';
if (isset($details) && !empty($details)) {
    $method_id = $details->methode_id;
    $enabled = ($details->enable == "true") ? true : false;
    $settings = $details->methode_details;
    $decoded = json_decode($settings);
    $stripe_publishable_key = $decoded->client_or_strip;
    $stripe_secret_key = $decoded->secret_key;
}
?>
<div id="stripe_tab" class="payment-method-tab-content" style="margin-top: 1%">
    <table>
        <tr>
            <td><label for="enable_stripe"><?php esc_html('Enable Stripe:'); ?></label></td>
            <td><input type="checkbox" id="enable_stripe" name="enable_stripe" <?php echo ($enabled) ? "checked" : "" ?>></td>
        </tr>
        <tr>
            <td><label for="stripe_publishable_key"><?php esc_html('Stripe Publishable Key:'); ?></label></td>
            <td><input type="text" id="stripe_publishable_key" name="stripe_publishable_key" value="<?php echo esc_attr($stripe_publishable_key); ?>" required="required"><br></td>
        </tr>
        <tr>
            <td><label for="stripe_secret_key"><?php esc_html('Stripe Secret Key:'); ?></label></td>
            <td><input type="text" id="stripe_secret_key" name="stripe_secret_key" value="<?php echo esc_attr($stripe_secret_key); ?>" required="required"><br></td>
        </tr>
        <tr>
        <input type="hidden" id="method_id" name="method_id" value="<?php echo esc_attr($method_id); ?>">
        <td><button class="stripe_save btn btn-primary"><?php esc_html('Save'); ?></button></td>
        </tr>
    </table>
    <span class="success"></span>
</div>
