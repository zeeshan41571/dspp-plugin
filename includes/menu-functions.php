<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function dspp_theme_register_menus() {
    register_nav_menu('bits-crm-menu', __('BITS CRM Menu', 'digital-service-provider-crm'));
}
add_action('after_setup_theme', 'dspp_theme_register_menus');

function dspp_get_page_by_title_custom($page_title, $output = OBJECT, $post_type = 'page') {
    global $wpdb;
    if (is_array($post_type)) {
        $post_type = esc_sql($post_type);
        $post_type_in_string = "'" . implode("','", $post_type) . "'";
        $sql = $wpdb->prepare(
                "SELECT ID
                FROM %s
                WHERE post_title = %s
                AND post_type IN (%s)",
                $wpdb->posts,
                $page_title,
                $post_type_in_string
        );
    } else {
        $sql = $wpdb->prepare(
                "SELECT ID
                FROM %s
                WHERE post_title = %s
                AND post_type = %s",
                $wpdb->posts,
                $page_title,
                $post_type
        );
    }
    $page = $wpdb->get_var('%s', $sql);
    if ($page) {
        return get_post($page, $output);
    }
    return null;
}