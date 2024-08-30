<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function dspp_dashboard() {
    ob_start();
    include "page-templates/template-dashboard.php";
    return ob_get_clean();
}

add_shortcode('dspp-dashboard', 'dspp_dashboard');

function dspp_services() {
    ob_start();
    include "page-templates/template-services.php";
    return ob_get_clean();
}

add_shortcode('dspp-services', 'dspp_services');

function dspp_display_order() {
    ob_start();
    include "page-templates/template-display-order.php";
    return ob_get_clean();
}

add_shortcode('dspp-display-order', 'dspp_display_order');

function dspp_preorder() {
    ob_start();
    include "page-templates/template-preorder.php";
    return ob_get_clean();
}

add_shortcode('dspp-preorder', 'dspp_preorder');

function dspp_thankyou() {
    ob_start();
    include "page-templates/template-thankyou.php";
    return ob_get_clean();
}

add_shortcode('dspp-thankyou', 'dspp_thankyou');

function dspp_single_order() {
    ob_start();
    include "page-templates/template-single-order.php";
    return ob_get_clean();
}

add_shortcode('dspp-single-order', 'dspp_single_order');

function dspp_invoice() {
    ob_start();
    include "page-templates/template-invoice.php";
    return ob_get_clean();
}

add_shortcode('dspp-invoice', 'dspp_invoice');

function dspp_single_invoice() {
    ob_start();
    include "page-templates/template-single-invoice.php";
    return ob_get_clean();
}

add_shortcode('dspp-single-invoice', 'dspp_single_invoice');

function dspp_generate_invoice() {
    ob_start();
    include "page-templates/template-generate-invoice.php";
    return ob_get_clean();
}

add_shortcode('dspp-generate-invoice', 'dspp_generate_invoice');

function dspp_profile() {
    ob_start();
    include "page-templates/template-profile.php";
    return ob_get_clean();
}

add_shortcode('dspp-profile', 'dspp_profile');

function dspp_cart() {
    ob_start();
    include "page-templates/template-cart.php";
    return ob_get_clean();
}

add_shortcode('dspp-cart', 'dspp_cart');

function dspp_checkout() {
    ob_start();
    include "page-templates/template-checkout.php";
    return ob_get_clean();
}

add_shortcode('dspp-checkout', 'dspp_checkout');

function dspp_payment() {
    ob_start();
    include "page-templates/template-payment.php";
    return ob_get_clean();
}

add_shortcode('dspp-payment', 'dspp_payment');

function dspp_payment_processor() {
    ob_start();
    include "page-templates/template-payment-processor.php";
    return ob_get_clean();
}

add_shortcode('dspp-payment-processor', 'dspp_payment_processor');

function dspp_login() {
    ob_start();
    include "page-templates/template-login.php";
    return ob_get_clean();
}

add_shortcode('dspp-login', 'dspp_login');

function dspp_register() {
    ob_start();
    include "page-templates/template-register.php";
    return ob_get_clean();
}

add_shortcode('dspp-register', 'dspp_register');

function dspp_forgot_password() {
    ob_start();
    include "page-templates/template-forgot-password.php";
    return ob_get_clean();
}

add_shortcode('dspp-forgot-password', 'dspp_forgot_password');
