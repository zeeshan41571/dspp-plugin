<?php
function dspp_custom_template_filter($template) {
    global $post;
    if (is_object($post) && strpos($post->post_name, 'bits-login') !== false) {
        return plugin_dir_path(__FILE__) . 'page-templates/template-login.php';
    }
    if (is_object($post) && strpos($post->post_name, 'bits-register') !== false) {
        return plugin_dir_path(__FILE__) . 'page-templates/template-register.php';
    }
    if (is_object($post) && strpos($post->post_name, 'bits-forgot-password') !== false) {
        return plugin_dir_path(__FILE__) . 'page-templates/template-forgot-password.php';
    }
    if (is_object($post) && strpos($post->post_name, 'bits-') !== false) {
        return plugin_dir_path(__FILE__) . 'page-templates/template-blank.php';
    }
    return $template;
}

add_filter('template_include', 'dspp_custom_template_filter');
