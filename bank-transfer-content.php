<?php
if (!defined('ABSPATH'))
    exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$method = "Bank";
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
}

//print_r($stripe_publishable_key);
//exit;
?>
<div id="bank_tab" class="payment-method-tab-content" style="margin-top: 1%">
    <table>
        <tr>
            <td><label for="enable_bank"><?php esc_html_e('Enable bank:', 'digital-service-provider-crm'); ?></label></td>
            <td><input type="checkbox" id="enable_bank" name="enable_bank" <?php echo ($enabled) ? "checked" : ""; ?>></td>
        </tr>
        <tr>
            <td><label for="stripe_publishable_key"><?php esc_html_e('Bank Account Details' , 'digital-service-provider-crm'); ?></label></td>
            <td><textarea id="stripe_publishable_key" name="stripe_publishable_key"><?php echo esc_attr($stripe_publishable_key); ?></textarea><br></td>
        </tr>
        <tr>
        <input type="hidden" id="method_id" name="method_id" value="<?php echo esc_attr($method_id); ?>">
        <td><button class="bank_save btn btn-primary"><?php esc_html_e('Save', 'digital-service-provider-crm'); ?></button></td>
        </tr>
    </table>
    <span class="success"></span>
</div>