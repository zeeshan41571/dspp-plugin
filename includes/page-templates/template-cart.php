<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$user_id_wp = get_current_user_id();
$custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
$decoded_array = json_decode($custom_website_data, true);
$cart_data = isset($decoded_array['custom_website_data_' . $user_id_wp]) ? $decoded_array['custom_website_data_' . $user_id_wp] : array();
?>
<div class="container">
    <h2><a href="<?php echo esc_url(site_url("/bits-services")); ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></a> <?php esc_html_e('Cart', 'digital-service-provider-crm'); ?></h2>

    <div class="col-lg-12">
        <div class="cartWrapper invoice-items nosticky" style="width:100%;">
            <div class="cartWrapperInner">
                <!-- End Campaigns -->
                <div id="cart_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row"><div class="col-sm-12">
                            <table id="cart_table" class="display table last-right dataTable table-striped" cellspacing="0" width="100%" role="grid" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_disabled"></th>
                                        <th class="sorting_disabled"><?php echo esc_html('Service Name'); ?></th>
                                        <th class="sorting_disabled"><?php echo esc_html('Quantity'); ?></th>
                                        <th class="sorting_disabled"><?php echo esc_html('Price'); ?></th>
                                        <th class="sorting_disabled domainTotal"><?php echo esc_html('Summary'); ?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $price = 0;
                                    $class = 'disabled';
                                    if (count($cart_data) > 0) {
                                        $class = '';
                                        foreach ($cart_data as $key => $site) {
                                            if ($key == 'total_price' || $key == 'coupon_code' || $key == 'coupon_value' || $key == 'coupon_discount_type' || $key == 'discount_price') {
                                                continue;
                                            }
                                            $price += $site['price'] * $site['quantity'];
                                            $post_id = (isset($site['post_id']) && !empty($site['post_id'])) ? $site['post_id'] : 0;
                                            ?>
                                            <tr role="row">
                                                <td><button style="margin-left: 12px !important;" data-user-id="<?php echo esc_attr($user_id_wp); ?>" data-site="<?php echo esc_attr($site['site']); ?>" data-price="<?php echo esc_attr($site['price'] * $site['quantity']); ?>" class="btn btn_remove btn-outline-danger ml-auto" onclick="remove_row(this);"><i class="fa fa-trash fa-1x red" aria-hidden="true"></i></button></td>
                                                <td><a href="<?php echo esc_url('//' . $site['site']); ?>" target="_blank"><?php echo esc_html($site['site']); ?></a></td>
                                                <td class="quantity">
                                                    <input id="quantity" data-user-id="<?php echo esc_attr($user_id_wp); ?>" data-site="<?php echo esc_attr($site['site']); ?>" data-price="<?php echo esc_attr($site['price']); ?>" data-product-id="<?php echo esc_attr($post_id); ?>" type="number" value="<?php echo esc_attr($site['quantity']); ?>" onchange="dspp_updateQuantity(this);"/>
                                                </td>
                                                <td class="domainTotal">
                                                    <div id="price">$<?php echo esc_html($site['price']); ?></div>
                                                </td>
                                                <td class="domainTotal">
                                                    <div id="price">$<?php echo esc_html($site['price'] * $site['quantity']); ?></div>
                                                </td>
                                            </tr>

                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr class="dataTables_empty">
                                            <td valign="top" colspan="11" class="dataTables_empty">
                                                <?php echo esc_html('Your cart is empty'); ?>
                                            </td>
                                        </tr>

                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="domainTotal tabcol" rowspan="1" style="text-align: right;">
                                            <div class="checkout_total">
                                                <?php
                                                if (isset($cart_data['coupon_code'])) {
                                                    $coupon_value = $cart_data['coupon_discount_type'] == 'fixed_discount' ? $cart_data['coupon_value'] : (($cart_data['coupon_value'] / 100) * $price);
                                                    $price = $price - $coupon_value;
                                                    ?>
                                                    <span>(Coupon Code: <?php echo esc_html($cart_data['coupon_code']); ?>)</span>
                                                    <?php
                                                }
                                                ?>
                                                <strong><?php esc_html_e('Total: ', 'digital-service-provider-crm'); ?></strong> USD <span id="total-invoice">$<?php echo esc_html($price); ?></span>
                                            </div>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                    </div>
                    <div class="col-lg-4" style="text-align: right;">
                        <div id="coupon-validate" class="alert alert-success" style="display:none"></div>
                        <!-- Coupon code field, hidden until clicked -->
                        <br>
                        <div class="input-group" id="coupon-fields">
                            <input type="text" name="orderCoupon" id="orderCoupon" class="form-control" placeholder="<?php esc_attr_e('Coupon code', 'digital-service-provider-crm'); ?>" value="">
                            <div class="input-group-append">
                                <button id="check_coupon_button" data-user-id="<?php echo esc_attr($user_id_wp); ?>" type="button" class="btn btn-secondary"><?php esc_html_e('Apply', 'digital-service-provider-crm'); ?></button>
                            </div>
                        </div>
                        <!-- Lower part of side panel -->
                        <div class="d-flex align-items-center justify-content-between coupon-row mt-2 a_checkout_outer checkout_btn"></div>
                        <!-- Secondary submit button, useful for mobile devices -->
                        <a id="purchaseButton" class="a_checkout btn btn-outline-primary ml-auto"><?php esc_html_e('Complete Purchase', 'digital-service-provider-crm'); ?></a>
                    </div>
                </div>
                <div class="text-right" style="margin-top:30px;">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__))?>images/badge-secure.png" title="<?php esc_attr_e('Secure order form', 'digital-service-provider-crm'); ?>" width="100" alt="<?php esc_attr_e('Secure checkout badge', 'digital-service-provider-crm'); ?>">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__))?>images/badge-privacy.png" title="<?php esc_attr_e('Your privacy is guaranteed', 'digital-service-provider-crm'); ?>" width="100" alt="<?php esc_attr_e('Privacy guarantee badge', 'digital-service-provider-crm'); ?>">
                </div>
            </div>
        </div>
        <div id="cartwidget"></div>
    </div>
</div>