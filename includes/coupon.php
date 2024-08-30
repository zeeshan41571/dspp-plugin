<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly?>
<label for="coupon_discount_type" style="width: 10% !important;display: inline-flex;">Discount Type:</label>
<select id="coupon_discount_type" name="coupon_discount_type" style="width: 80%;margin: 1%;">
    <option value="percentage" <?php selected($coupon_discount_type, 'percentage'); ?>><?php esc_html_e('Percentage', 'digital-service-provider-crm'); ?></option>
    <option value="fixed_discount" <?php selected($coupon_discount_type, 'fixed_discount'); ?>><?php esc_html_e('Fixed Discount', 'digital-service-provider-crm'); ?></option>
</select><br>

<label for="coupon_value" style="width: 10% !important;display: inline-flex;">Coupon Value:</label>
<input type="text" id="coupon_value" name="coupon_value" value="<?php echo esc_attr($coupon_value); ?>" style="width: 80%;margin: 1%;"><br>

<label for="coupon_expiry_date" style="width: 10% !important;display: inline-flex;">Expiry Date:</label>
<input type="date" id="coupon_expiry_date" name="coupon_expiry_date" value="<?php echo esc_attr($coupon_expiry_date); ?>" style="width: 80%;margin: 1%;"><br>
