<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!is_user_logged_in()) {
    header('Location:' . site_url('/bits-login'));
}
$site_url = site_url();
$user_id_wp = get_current_user_id();
$custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
$decoded_array = json_decode($custom_website_data, true);
$cart_data = isset($decoded_array['custom_website_data_' . $user_id_wp]) ? $decoded_array['custom_website_data_' . $user_id_wp] : array();
$item_cout = 0;
foreach ($cart_data as $key => $value) {
    if (is_array($value)) {
        $item_cout++;
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-sm-6"><h2 class="page-title"><?php esc_html_e('Services', 'digital-service-provider-crm'); ?></h2></div>
        <div class="col-sm-6">
            <a class="btn btn-primary float-end view_cart_button" href="<?php echo esc_url(site_url("/bits-cart")); ?>">
                <?php esc_html_e('View Cart | ', 'digital-service-provider-crm'); ?><span id="cartCount"><?php echo esc_html($item_cout); ?></span>
            </a>
        </div>
    </div>
    <div class="view-cart alert alert-success" style="display:none;"><?php esc_html_e('Items added to cart successfully!', 'digital-service-provider-crm'); ?></div>

    <div class="row">
        <?php
        $services_list = dspp_get_all_services_cpt();
        foreach ($services_list as $service) {
            $post_id = $service->ID;
            $service_price = get_post_meta($post_id, '_service_price', true);
            $numericString = preg_replace("/[^0-9.]/", "", $service_price);
            $service_price_int = floatval($numericString);
            ?>
            <div class="col-sm">
                <div class="card">
                    <input type="hidden" name="service_id" value="<?php echo esc_attr($post_id); ?>">
                    <div class="card-body">
                        <h2 class="card-title"><?php echo esc_html($service->post_title); ?></h2>
                        <p class="card-text"><?php echo esc_html($service->post_content); ?></p>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex align-items-center">
                            <div class="mr-2 small text-muted flex-fill" data-price="<?php echo esc_attr($service_price); ?>">
                                $<?php echo esc_html($service_price); ?>
                            </div>
                            <div class="buttons">
                                <button data-post-id="<?php echo esc_attr($post_id); ?>" data-site="<?php echo esc_attr($service->post_title); ?>" data-price="<?php echo esc_attr($service_price_int); ?>" data-user-id="<?php echo esc_attr(get_current_user_id()); ?>" class="btncart btn btn-outline-primary ml-auto">
                                    <i class="fa fa-cart-plus fa-1x greencart" aria-hidden="true"></i><?php esc_html_e('Add to cart' , 'digital-service-provider-crm'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>