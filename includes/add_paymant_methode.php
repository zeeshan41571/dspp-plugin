<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="payment-method-tabs">
    <!--<label for="payment_method" style="width: 10% !important;display: inline-flex;">Select Payment Method:</label>-->
    <div class="payment-method-options">
        <div class="payment-method-tab <?php echo ($payment_method === 'stripe') ? 'active' : ''; ?>" data-tab="stripe">
            Stripe
        </div>
        <div class="payment-method-tab <?php echo ($payment_method === 'paypal') ? 'active' : ''; ?>" data-tab="paypal">
            PayPal
        </div>
        <div class="payment-method-tab <?php echo ($payment_method === 'bank_transfer') ? 'active' : ''; ?>" data-tab="bank_transfer">
            Bank Transfer
        </div>
    </div>
</div>

<div class="payment-method-settings">
    <div id="stripe_tab" class="payment-method-tab-content <?php echo ($payment_method === 'stripe') ? 'active' : 'hidden'; ?>">
        <label for="stripe_enable">Enable Stripe:</label>
        <input type="checkbox" id="stripe_enable" name="stripe_enable" value="1" <?php checked($enable_payment_method === '1' && $payment_method === 'stripe'); ?>><br>

        <label for="stripe_key">Stripe Key:</label>
        <input type="text" id="stripe_key" name="stripe_key" value="<?php echo esc_attr(get_post_meta($post->ID, 'stripe_key', true)); ?>"><br>
        <!-- Add more fields specific to Stripe as needed -->
    </div>

    <div id="paypal_tab" class="payment-method-tab-content <?php echo ($payment_method === 'paypal') ? 'active' : 'hidden'; ?>">
        <label for="paypal_enable">Enable PayPal:</label>
        <input type="checkbox" id="paypal_enable" name="paypal_enable" value="1" <?php checked($enable_payment_method === '1' && $payment_method === 'paypal'); ?>><br>

        <label for="paypal_email">PayPal Email:</label>
        <input type="text" id="paypal_email" name="paypal_email" value="<?php echo esc_attr(get_post_meta($post->ID, 'paypal_email', true)); ?>"><br>
        <!-- Add more fields specific to PayPal as needed -->
    </div>

    <div id="bank_transfer_tab" class="payment-method-tab-content <?php echo ($payment_method === 'bank_transfer') ? 'active' : 'hidden'; ?>">
        <label for="bank_transfer_enable">Enable Bank Transfer:</label>
        <input type="checkbox" id="bank_transfer_enable" name="bank_transfer_enable" value="1" <?php checked($enable_payment_method === '1' && $payment_method === 'bank_transfer'); ?>><br>

        <label for="bank_account">Bank Account Details:</label>
        <textarea id="bank_account" name="bank_account"><?php echo esc_textarea(get_post_meta($post->ID, 'bank_account', true)); ?></textarea><br>
        <!-- Add more fields specific to Bank Transfer as needed -->
    </div>
</div>