<?php

/**
 * Fired during plugin activation
 *
 * @link       https://bestitsol.com/
 * @since      1.0.0
 *
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/includes
 * @author     Best IT Solutions <info@bestitsol.com>
 */
class Dspp_Crm_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function dspp_activate() {
//        exit('hello');
        $wwmp_activator_plugin = new self();
        $wwmp_activator_plugin->dspp_create_pages();
        $wwmp_activator_plugin->dspp_activate_menu();
    }

    public function dspp_create_pages() {
        $template_directory = plugin_dir_url(__FILE__) . 'page-templates/';
        $pages = array(
            array(
                'title' => 'Dashboard',
                'slug' => 'bits-dashboard',
                'parent' => 0,
                'template' => 'template-dashboard.php',
                'short_code' => 'dspp-dashboard'
            ),
            array(
                'title' => 'Services',
                'slug' => 'bits-services',
                'parent' => 0,
                'template' => 'template-services.php',
                'short_code' => 'dspp-services'
            ),
            array(
                'title' => 'Orders',
                'slug' => 'bits-orders',
                'parent' => 0,
                'template' => 'template-preorder.php',
                'short_code' => 'dspp-preorder'
            ),
            array(
                'title' => 'Thank You',
                'slug' => 'bits-thankyou',
                'parent' => 0,
                'template' => 'template-thankyou.php',
                'short_code' => 'dspp-thankyou'
            ),
            array(
                'title' => 'Single Order',
                'slug' => 'bits-single-order',
                'parent' => 0,
                'template' => 'template-single-order.php',
                'short_code' => 'dspp-single-order'
            ),
            array(
                'title' => 'Invoices',
                'slug' => 'bits-invoices',
                'parent' => 0,
                'template' => 'template-invoice.php',
                'short_code' => 'dspp-invoice'
            ),
            array(
                'title' => 'Single Invoice',
                'slug' => 'bits-single-invoice',
                'parent' => 0,
                'template' => 'template-single-invoice.php',
                'short_code' => 'dspp-single-invoice'
            ),
            array(
                'title' => 'Generate Invoice',
                'slug' => 'bits-generate-invoice',
                'parent' => 0,
                'template' => 'template-generate-invoice.php',
                'short_code' => 'dspp-generate-invoice'
            ),
            array(
                'title' => 'Profile',
                'slug' => 'bits-profile',
                'parent' => 0,
                'template' => 'template-profile.php',
                'short_code' => 'dspp-profile'
            ),
            array(
                'title' => 'Cart',
                'slug' => 'bits-cart',
                'parent' => 0,
                'template' => 'template-cart.php',
                'short_code' => 'dspp-cart'
            ),
            array(
                'title' => 'Payment',
                'slug' => 'bits-payment',
                'parent' => 0,
                'template' => 'template-payment.php',
                'short_code' => 'dspp-payment'
            ),
            array(
                'title' => 'Payment Processor',
                'slug' => 'bits-payment-processor',
                'parent' => 0,
                'template' => 'template-payment-processor.php',
                'short_code' => 'dspp-payment-processor'
            ),
            array(
                'title' => 'Checkout',
                'slug' => 'bits-checkout',
                'parent' => 0,
                'template' => 'template-checkout.php',
                'short_code' => 'dspp-checkout'
            ),
            array(
                'title' => 'Login',
                'slug' => 'bits-login',
                'parent' => 0,
                'template' => 'template-login.php',
                'short_code' => 'dspp-login'
            ),
            array(
                'title' => 'Register',
                'slug' => 'bits-register',
                'parent' => 0,
                'template' => 'template-register.php',
                'short_code' => 'dspp-register'
            ),
            array(
                'title' => 'Forgot Password',
                'slug' => 'bits-forgot-password',
                'parent' => 0,
                'template' => 'template-forgot-password.php',
                'short_code' => 'dspp-forgot-password'
            ),
        );
        foreach ($pages as $page) {
            $page_title = $page['title'];
            $page_slug = $page['slug'];
            $page_template = $page['template'];
            $existing_page = dspp_get_page_by_title_custom($page_title);
            if (!$existing_page) {
                $new_page = array(
                    'post_title' => $page_title,
                    'post_name' => $page_slug,
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'page_template' => 'page-templates/template-blank.php'
                );
                $page_id = wp_insert_post($new_page);
//                if ($page_slug == 'bits-dashboard') {
//                    update_option('page_on_front', $page_id);
//                    update_option('show_on_front', 'page');
//                }
                if ($page_id && $page_template) {
                    $shortcode_content = '[' . $page['short_code'] . ']';
                    $updated_content = $shortcode_content . get_post_field('post_content', $page_id);
                    wp_update_post(array('ID' => $page_id, 'post_content' => $updated_content));
                } else {
                    error_log('Error creating page: ' . $page_title);
                }
            } else {
                error_log('Page already exists: ' . $page_title);
            }
        }
    }

    public function dspp_activate_menu() {
        // Check if the menu exists
        $menu_exist = get_term_by('slug', 'bits-crm-menu', 'nav_menu');

        // Delete the menu if it exists
        if ($menu_exist) {
            wp_delete_nav_menu($menu_exist->term_id);
        }

        // Create a new menu
        $menu_id = wp_create_nav_menu('BITS CRM Menu');

        // Register the menu location
        register_nav_menu('bits-crm-menu', __('BITS CRM Menu', 'digital-service-provider-crm'));

        // Set the menu as the theme's location
        set_theme_mod('nav_menu_locations', array('bits-crm-menu' => $menu_id));

        // Define menu items with headings and sub-menu items
        $menu_items = array(
            array(
                'type' => 'heading',
                'title' => 'Activity',
                'class' => 'crm-menu-item-heading',
            ),
            array(
                'type' => 'page',
                'slug' => 'bits-dashboard',
                'title' => 'Dashboard',
                'parent' => 'Activity', // Set parent heading title
                'class' => 'fa fa-home',
            ),
            array(
                'type' => 'page',
                'slug' => 'bits-orders',
                'title' => 'Orders',
                'parent' => 'Activity', // Set parent heading title
                'class' => 'fa fa-inbox',
            ),
            array(
                'type' => 'page',
                'slug' => 'bits-services',
                'title' => 'Services',
                'parent' => 'Activity', // Set parent heading title
                'class' => 'fa fa-shopping-cart',
            ),
            array(
                'type' => 'heading',
                'title' => 'Billing',
                'class' => 'crm-menu-item-heading',
            ),
            array(
                'type' => 'page',
                'slug' => 'bits-invoices',
                'title' => 'Invoices',
                'parent' => 'Billing', // Set parent heading title
                'class' => 'fa fa-file',
            ),
            array(
                'type' => 'heading',
                'title' => 'Account',
                'class' => 'crm-menu-item-heading',
            ),
            array(
                'type' => 'page',
                'slug' => 'bits-profile',
                'title' => 'Profile',
                'parent' => 'Account', // Set parent heading title
                'class' => 'fa fa-user',
            ),
        );

        // Loop through each menu item
        foreach ($menu_items as $item) {
            $menu_item_data = array(
                'menu-item-title' => $item['title'],
                'menu-item-object' => ($item['type'] === 'page') ? 'page' : '',
                'menu-item-object-id' => ($item['type'] === 'page') ? get_page_by_path($item['slug'])->ID : '',
                'menu-item-type' => ($item['type'] === 'page') ? 'post_type' : 'custom',
                'menu-item-status' => 'publish',
                'menu-item-classes' => isset($item['class']) ? $item['class'] : '',
            );

            // If it's a sub-menu item, set the parent
            if ($item['type'] === 'page' && isset($item['parent'])) {
                $parent_menu_item = wp_get_nav_menu_items($menu_id, array('title' => $item['parent']));

                if (!empty($parent_menu_item)) {
                    $menu_item_data['menu-item-parent-id'] = $parent_menu_item[0]->ID;
                }
            }

            wp_update_nav_menu_item($menu_id, 0, $menu_item_data);
        }
    }
}

function dspp_create_tables() {
    global $wpdb;
    $new_prefix = $wpdb->prefix . 'sppcrm_';
    $charset_collate = $wpdb->get_charset_collate();
    $tables = array(
        array(
            'name' => 'invoice_statuses',
            'sql' => "CREATE TABLE `{$new_prefix}invoice_statuses` (
                `status_id` INT(11) AUTO_INCREMENT NOT NULL,
                `status_name` TEXT NOT NULL,
                `status_description` VARCHAR(1000) NOT NULL,
                `status_status` VARCHAR(100) NOT NULL,
                `status_date` DATETIME DEFAULT current_timestamp() NOT NULL,
                PRIMARY KEY (`status_id`)
            ) $charset_collate;"
        ),
        array(
            'name' => 'invoice_details',
            'sql' => "CREATE TABLE {$new_prefix}invoice_details (
                invoice_id INT AUTO_INCREMENT NOT NULL,
                invoice_items TEXT NOT NULL,
                invoice_total VARCHAR(100) NOT NULL,
                user_id VARCHAR(100) NOT NULL,
                invoice_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                invoice_status VARCHAR(100) NOT NULL,
                invoice_updated_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                coupon_code VARCHAR(100) NULL,
                coupon_value VARCHAR(100) NULL,
                PRIMARY KEY (invoice_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'billing_details',
            'sql' => "CREATE TABLE {$new_prefix}billing_details (
                billing_id  INT AUTO_INCREMENT NOT NULL,
                user_id  TEXT(200) ,
                billing_address	 VARCHAR(100),
                billing_city	 VARCHAR(100),
                billing_state VARCHAR(100) ,
                billing_post_code VARCHAR(100) ,
                billing_country VARCHAR(100) ,
                billing_company VARCHAR(100) ,
                billing_tax_id   VARCHAR(100),
                PRIMARY KEY (billing_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'payment_methods',
            'sql' => "CREATE TABLE {$new_prefix}payment_methods (
                method_id INT(11) AUTO_INCREMENT NOT NULL,
                user_id TEXT(100) NOT NULL,
                method_name VARCHAR(1000) NOT NULL,
                card_number VARCHAR(100) NOT NULL,
                expiry_date DATE NOT NULL,
                PRIMARY KEY (method_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'order_statuses',
            'sql' => "CREATE TABLE `{$new_prefix}order_statuses` (
                `status_id` INT(11) AUTO_INCREMENT NOT NULL,
                `status_name` TEXT NOT NULL,
                `status_description` VARCHAR(1000) NOT NULL,
                `status_status` VARCHAR(100) NOT NULL,
                `status_date` DATETIME DEFAULT current_timestamp() NOT NULL,
                PRIMARY KEY (`status_id`)
            ) $charset_collate;"
        ),
        array(
            'name' => 'orders',
            'sql' => "CREATE TABLE {$new_prefix}orders (
                order_id  INT AUTO_INCREMENT NOT NULL,
                invoice_id  TEXT(100) NOT NULL,
                method_id	 VARCHAR(100) NOT NULL,
                billing_id VARCHAR(100) NOT NULL,
                user_id VARCHAR(100) NOT NULL,
                order_date datetime NOT NULL DEFAULT current_timestamp(),
                order_status VARCHAR(100) NOT NULL,
                active BOOLEAN NOT NULL DEFAULT FALSE,
                PRIMARY KEY (order_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'settings',
            'sql' => "CREATE TABLE {$new_prefix}settings (
                settings_id   INT(11) AUTO_INCREMENT NOT NULL,
                logo          TEXT NOT NULL,
                sidebar_logo  VARCHAR(1000) NOT NULL,
                sidebar_color VARCHAR(100) NOT NULL,
                accent_color  VARCHAR(100) NOT NULL,
                PRIMARY KEY (settings_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'payment',
            'sql' => "CREATE TABLE {$new_prefix}payment (
                methode_id  INT(11) AUTO_INCREMENT NOT NULL,
                user_id INT(20) NOT NULL,
                enable  TEXT NOT NULL,
                methode VARCHAR(200) NOT NULL,
                methode_details VARCHAR(1000) NOT NULL,
                PRIMARY KEY (methode_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'order_form_types',
            'sql' => "CREATE TABLE {$new_prefix}order_form_types (
                form_type_id int(11) AUTO_INCREMENT NOT NULL,
                form_type_name text NOT NULL,
                form_type_description text NOT NULL,
                form_type_status tinyint(1) NOT NULL DEFAULT 1,
                form_type_date datetime NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (form_type_id)
            ) $charset_collate;"
        ),
        array(
            'name' => 'order_forms',
            'sql' => "CREATE TABLE {$new_prefix}order_forms (
                form_id int(11) AUTO_INCREMENT NOT NULL,
                form_title text NOT NULL,
                form_information text NOT NULL,
                form_details text NOT NULL,
                form_created_date datetime NOT NULL DEFAULT current_timestamp(),
                form_status text NOT NULL,
                service_ids text Null,
                form_type_id int(11) NOT NULL,
                PRIMARY KEY (form_id)
            ) $charset_collate;"
        ),
    );
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    foreach ($tables as $table) {
        $sql = $table['sql'];
        dbDelta($sql);
    }
}
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
dspp_create_tables();

function dspp_insert_order_statuses() {
    global $wpdb;
    $new_prefix = $wpdb->prefix . 'sppcrm_';
    $order_statuses = array(
        'Pending' => 'pending',
        'Submitted' => 'submitted',
        'Processing' => 'processing',
        'Completed' => 'completed',
    );
    $order_statuses_table = $new_prefix . 'order_statuses';
    foreach ($order_statuses as $status_key => $status_label) {
        $wpdb->insert(
                $order_statuses_table,
                array(
                    'status_name' => $status_key,
                    'status_description' => $status_label,
                    'status_status' => 'active'
                ),
                array('%s', '%s')
        );
    }
}
function dspp_insert_invoice_statuses() {
    global $wpdb;
    $new_prefix = $wpdb->prefix . 'sppcrm_';
    $order_statuses = array(
        'Abandoned Checkout' => 'abandoned',
        'Payment Pending' => 'pending',
        'Paid' => 'paid',
    );
    $order_statuses_table = $new_prefix . 'invoice_statuses';
    foreach ($order_statuses as $status_key => $status_label) {
        $wpdb->insert(
            $order_statuses_table,
            array(
                'status_name' => $status_key,
                'status_description' => $status_label,
                'status_status' => 'active'
            ),
            array('%s', '%s')
        );
    }
}
if (!get_option('dspp_order_statuses_inserted')) {
    dspp_insert_order_statuses();
    update_option('dspp_order_statuses_inserted', true);
}
if (!get_option('dspp_invoice_statuses_inserted')) {
    dspp_insert_invoice_statuses();
    update_option('dspp_invoice_statuses_inserted', true);
}