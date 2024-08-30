<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bestitsol.com/
 * @since             1.0.0
 * @package           Dspp_Crm
 *
 * @wordpress-plugin
 * Plugin Name:       Digital Service Provider Pro CRM
 * Plugin URI:        https://bestitsol.com/service-provider-pro/
 * Description:       Optimize client management with Digital Service Provider Pro CRM, an essential WordPress plugin for streamlined invoicing.
 * Version:           1.0.0
 * Author:            Best IT Solutions
 * Author URI:        https://bestitsol.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       digital-service-provider-crm
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('DSPP_CRM_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spp-crm-activator.php
 */
function dspp_activate_spp_crm() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-spp-crm-activator.php';
    Dspp_Crm_Activator::dspp_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spp-crm-deactivator.php
 */
function dspp_deactivate_spp_crm() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-spp-crm-deactivator.php';
    Dspp_Crm_Deactivator::dspp_deactivate();
}

register_activation_hook(__FILE__, 'dspp_activate_spp_crm');
register_deactivation_hook(__FILE__, 'dspp_deactivate_spp_crm');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-spp-crm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function dspp_run_spp_crm() {
    $plugin = new Dspp_Crm();
    $plugin->run();
}

dspp_run_spp_crm();
include(plugin_dir_path(__FILE__) . 'includes/cpt-services.php');

// Create custom post type "Coupons"
function dspp_custom_coupons_post_type() {
    $labels = array(
        'name' => _x('DSP Coupons', 'post type general name', 'digital-service-provider-crm'),
        'singular_name' => _x('DSP Coupon', 'post type singular name', 'digital-service-provider-crm'),
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_position' => null,
        'supports' => array('title', 'editor'),
    );
    register_post_type('dspp_coupons', $args);
}

add_action('init', 'dspp_custom_coupons_post_type');

// Add custom fields to Coupons
function dspp_add_coupon_custom_fields() {
    add_meta_box('coupon_details', 'Coupon Details', 'dspp_coupon_details_callback', 'dspp_coupons', 'normal', 'high');
}

add_action('add_meta_boxes', 'dspp_add_coupon_custom_fields');

function dspp_get_categories_for_post_type($post_type) {
    $taxonomy = 'dspp_services_category';
    $categories = get_categories(array(
        'taxonomy' => $taxonomy,
        'object_type' => array($post_type),
    ));
    $category_list = array();
    foreach ($categories as $category) {
        $category_list[$category->term_id] = $category->name;
    }
    return $category_list;
}

function dspp_coupon_details_callback($post) {
    wp_nonce_field('coupon_details_nonce', 'coupon_details_nonce');
    $coupon_discount_type = get_post_meta($post->ID, 'coupon_discount_type', true);
    $coupon_value = get_post_meta($post->ID, 'coupon_value', true);
    $coupon_expiry_date = get_post_meta($post->ID, 'coupon_expiry_date', true);
    if (get_post_meta($post->ID, 'excluded_categories', true)) {
        $excluded_categories = get_post_meta($post->ID, 'excluded_categories', true);
    } else {
        $excluded_categories = array();
    }

    $web_categories = dspp_get_categories_for_post_type('dspp_service');
//    print_r($excluded_categories);
//    exit;
    ?>
    <label for="coupon_discount_type" style="width: 10% !important;display: inline-flex;">Discount Type:</label>
    <select id="coupon_discount_type" name="coupon_discount_type" style="width: 40%;margin: 1%;">
        <option value="percentage" <?php selected($coupon_discount_type, 'percentage'); ?>>Percentage</option>
        <option value="fixed_discount" <?php selected($coupon_discount_type, 'fixed_discount'); ?>>Fixed Discount</option>
    </select><br>

    <label for="coupon_value" style="width: 10% !important;display: inline-flex;">Coupon Value:</label>
    <input type="text" id="coupon_value" name="coupon_value" value="<?php echo esc_attr($coupon_value); ?>" style="width: 40%;margin: 1%;"><br>

    <label for="coupon_expiry_date" style="width: 10% !important;display: inline-flex;">Expiry Date:</label>
    <input type="date" id="coupon_expiry_date" name="coupon_expiry_date" value="<?php echo esc_attr($coupon_expiry_date); ?>" style="width: 40%;margin: 1%;"><br>
    <label for="excluded_categories" style="width: 10% !important;display: inline-flex;">Exclude Categories:</label>
    <select id="excluded_categories" name="excluded_categories[]" multiple style="width: 40%;margin: 1%;">
        <?php
        foreach ($web_categories as $key => $category) {
//            var_dump(in_array($key, $excluded_categories));
            ?>
            <option value="<?php echo esc_attr($key); ?>" <?php echo (in_array($key, $excluded_categories)) ? 'Selected' : '' ?> style="width: 40%;margin: 1%;">
                <?php echo esc_html($category); ?>
            </option>
        <?php } ?>
    </select><br>
    <?php
}

// Save custom fields data
function dspp_save_coupon_custom_fields($post_id) {
    if (!isset($_POST['coupon_details_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['coupon_details_nonce'])) , 'coupon_details_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    update_post_meta($post_id, 'coupon_discount_type', sanitize_text_field($_POST['coupon_discount_type']));
    update_post_meta($post_id, 'coupon_value', sanitize_text_field($_POST['coupon_value']));
    update_post_meta($post_id, 'coupon_expiry_date', sanitize_text_field($_POST['coupon_expiry_date']));
    if (isset($_POST['excluded_categories']) && !empty($_POST['excluded_categories'])) {
        update_post_meta($post_id, 'excluded_categories', array_map('absint', $_POST['excluded_categories']));
    } else {
        update_post_meta($post_id, 'excluded_categories', '');
    }
}

add_action('save_post_dspp_coupons', 'dspp_save_coupon_custom_fields');

// Modify admin labels
function dspp_modify_coupon_admin_labels($labels) {
    $labels->name = _x('Coupons', 'post type general name', 'digital-service-provider-crm');
    $labels->singular_name = _x('Coupon', 'post type singular name', 'digital-service-provider-crm');
    $labels->add_new = _x('Add New Coupon', 'coupon', 'digital-service-provider-crm');
    $labels->add_new_item = __('Add New Coupon', 'digital-service-provider-crm');
    $labels->edit_item = __('Edit Coupon', 'digital-service-provider-crm');
    $labels->new_item = __('New Coupon', 'digital-service-provider-crm');
    $labels->view_item = __('View Coupon', 'digital-service-provider-crm');
    $labels->search_items = __('Search Coupons', 'digital-service-provider-crm');
    $labels->not_found = __('No coupons found.', 'digital-service-provider-crm');
    $labels->not_found_in_trash = __('No coupons found in Trash.', 'digital-service-provider-crm');
    return $labels;
}

add_filter('post_type_labels_dspp_coupons', 'dspp_modify_coupon_admin_labels');

// Add custom columns to the Coupons list table
function dspp_add_coupon_custom_columns($columns) {
    $columns['coupon_discount_type'] = 'Discount Type';
    $columns['coupon_value'] = 'Coupon Value';
    $columns['coupon_expiry_date'] = 'Expiry Date';
    return $columns;
}

add_filter('manage_edit-dspp_coupons_columns', 'dspp_add_coupon_custom_columns');

// Populate custom columns with data
function dspp_populate_coupon_custom_columns($column, $post_id) {
    switch ($column) {
        case 'coupon_discount_type':
            echo esc_html(get_post_meta($post_id, 'coupon_discount_type', true));
            break;
        case 'coupon_value':
            echo esc_html(get_post_meta($post_id, 'coupon_value', true));
            break;
        case 'coupon_expiry_date':
            echo esc_html(get_post_meta($post_id, 'coupon_expiry_date', true));
            break;
    }
}

add_action('manage_dspp_coupons_posts_custom_column', 'dspp_populate_coupon_custom_columns', 10, 2);

// Add Generate Coupon Code button
function dspp_add_generate_coupon_code_button() {
    global $post;
    if ($post->post_type == 'dspp_coupons') {
        echo '<a href="#" id="generate_coupon_code_button" class="button">Generate Coupon Code</a>';
    }
}

add_action('edit_form_after_title', 'dspp_add_generate_coupon_code_button');

// JavaScript to handle coupon code generation
function dspp_generate_coupon_code_js() {
    global $post;
    if (isset($post) && $post->post_type == 'dspp_coupons') {
        wp_enqueue_script('generate_coupon_code', plugin_dir_url(__FILE__) . 'public/js/generate_coupon_code.js', array(), filemtime(plugin_dir_path(__FILE__) . 'public/js/generate_coupon_code.js'), true);
    }
}

add_action('admin_footer', 'dspp_generate_coupon_code_js');

// Enqueue scripts
function dspp_enqueue_coupon_scripts($hook) {
    if ($hook === 'post-new.php' || $hook === 'post.php') {
        global $post_type;
        if ($post_type === 'dspp_coupons') {
            wp_enqueue_script('coupon-validation', plugin_dir_url(__FILE__) . '/admin/js/coupon-validation.js', array('jquery'), DSPP_CRM_VERSION, true);
        }
    }
}

add_action('admin_enqueue_scripts', 'dspp_enqueue_coupon_scripts');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function dspp_add_payment_methods_menu() {
    add_menu_page(
            'DSP Payment Methods',
            'DSP Payment Methods',
            'manage_options',
            'payment_methods',
            'dspp_payment_methods_page',
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M2 6c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1z"/></svg>'),
            20 // Menu position
    );
}

add_action('admin_menu', 'dspp_add_payment_methods_menu');

// Callback function to display the page content
function dspp_payment_methods_page() {
    // Verify nonce
    $active_tab = 'bank_transfer';
    if (isset($_GET['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_GET['nonce'])) , 'payment_methods_nonce'))
    {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'bank_transfer';
    }
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Payment Methods</h1>
        <hr class="wp-header-end">
        <!-- Add your tabs here -->
        <h2 class="nav-tab-wrapper">
<!--            <a href="?page=payment_methods&tab=stripe&nonce=<?php // echo wp_create_nonce('payment_methods_nonce'); ?>" class="nav-tab">Stripe</a>
            <a href="?page=payment_methods&tab=paypal&nonce=<?php // echo wp_create_nonce('payment_methods_nonce'); ?>" class="nav-tab">PayPal</a>-->
            <a href="?page=payment_methods&tab=bank_transfer&nonce=<?php echo esc_attr(wp_create_nonce('payment_methods_nonce')); ?>" class="nav-tab">Bank Transfer</a>
        </h2>

        <div class="tab-content">
            <?php
            // Check which tab is active
            // Include content based on the active tab
            switch ($active_tab) {
                case 'stripe':
                    include_once 'stripe-content.php'; // Create a separate file for each tab's content
                    break;
                case 'paypal':
                    include_once 'paypal-content.php';
                    break;
                case 'bank_transfer':
                    include_once 'bank-transfer-content.php';
                    break;
            }
            ?>
        </div>
    </div>
    <?php
}

// Add your scripts and styles to handle tab switching if needed
function dspp_enqueue_payment_methods_scripts() {
    // Enqueue scripts and styles here
}

add_action('admin_enqueue_scripts', 'dspp_enqueue_payment_methods_scripts');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CRM SETTING   
function dspp_add_crm_settings_menu() {
    add_menu_page(
            'DSP Settings',
            'DSP Settings',
            'manage_options',
            'crm_settings',
            'dspp_crm_settings_page',
            'dashicons-admin-settings', 20
    );
}

add_action('admin_menu', 'dspp_add_crm_settings_menu');

function dspp_crm_settings_page() {
    $active_module = 'settings';
    wp_nonce_field('crm_settings_action', 'crm_settings_nonce');
    if (isset( $_POST['crm_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['crm_settings_nonce'])) , 'crm_settings_action' )) {
        $active_module = isset($_GET['module']) ? sanitize_text_field($_GET['module']) : 'settings';
    } else {
        $active_module = isset($_GET['module']) ? sanitize_text_field($_GET['module']) : 'settings';
    }
    ?>
    <div class="container">
        <?php
        switch ($active_module) {
            case 'settings':
                include_once 'includes/crm-setting.php';
                break;
            case 'general':
                include_once 'company_settings.php';
                break;
            case 'orders':
                include_once 'order_settings.php';
                break;
            case 'invoices':
                include_once 'invoice_settings.php';
                break;
            case 'order_forms':
                include_once 'order_form_settings.php';
                break;
            case 'emails':
                include_once 'email_settings.php';
                break;
            case 'languages':
                include_once 'language_settings.php';
                break;
        }
        ?>
    </div>
    <?php
}

function dspp_display_uploaded_settings() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_settings';
    $settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE settings_id = %d", $table_name, 1));
    return $settings;
}

function dspp_handle_logo_upload($inputName) {
    if (isset( $_POST['handle_logo_upload'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['handle_logo_upload'])) , 'handle_logo_upload' )){
        if (!empty(sanitize_text_field($_FILES[$inputName]['name']))) {
            $uploadedFile = wp_handle_upload($_FILES[$inputName], array('test_form' => false));
            return $uploadedFile['url'];
        }
    } else {
        if (!empty(sanitize_text_field($_FILES[$inputName]['name']))) {
            $uploadedFile = wp_handle_upload($_FILES[$inputName], array('test_form' => false));
            return $uploadedFile['url'];
        }
    }
    return '';
}

function dspp_save_settings($logo, $sidebarLogo, $sidebarColor, $accentColor) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_settings';
    $existingRecord = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE settings_id = %d", $table_name, 1));
    if ($existingRecord) {
        $wpdb->update(
            $table_name, array(
                'logo' => $logo,
                'sidebar_logo' => $sidebarLogo,
                'sidebar_color' => $sidebarColor,
                'accent_color' => $accentColor,
            ), array('settings_id' => 1)
        );
    } else {
        $wpdb->insert(
            $table_name, array(
                'settings_id' => 1,
                'logo' => $logo,
                'sidebar_logo' => $sidebarLogo,
                'sidebar_color' => $sidebarColor,
                'accent_color' => $accentColor,
            )
        );
    }
    $current_url = home_url(add_query_arg( null, null));
    wp_redirect($current_url);
}

////////////////////////////////SUPER ADMIN ORDERS 
function dspp_add_spp_orders_menu() {
    add_menu_page(
            'DSP Orders', 'DSP Orders', 'manage_options', 'spp_orders', 'dspp_orders_page', 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M2 6c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1z"/></svg>'), 20 // Menu position
    );
    // Call add_submenu_page outside the loop or in a separate function
    add_submenu_page(
            '', // Parent menu slug
            'DSP Order Details', // Page title
            '', // Menu title
            'manage_options', // Capability
            'dspp_orders_details', // Submenu slug
            'dspp_orders_details_page' // Callback function to display the submenu page
    );
}

add_action('admin_menu', 'dspp_add_spp_orders_menu');

function dspp_orders_page() {
    include(plugin_dir_path(__FILE__) . 'includes/spp_order_pages.php');
}

function dspp_orders_details_page() {
    include 'order_details.php';
}

//
function dspp_add_spp_invoice_menu() {
    add_menu_page(
            'DSP Invoices', 'DSP Invoices', 'manage_options', 'spp_invoices', 'dspp_invoice_page', 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M2 6c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1z"/></svg>'), 20 // Menu position
    );
    // Call add_submenu_page outside the loop or in a separate function
    add_submenu_page(
            '', // Parent menu slug
            'DSP Invoice Details', // Page title
            '', // Menu title
            'manage_options', // Capability
            'dspp_invoice_details', // Submenu slug
            'dspp_invoice_details_page' // Callback function to display the submenu page
    );
}

add_action('admin_menu', 'dspp_add_spp_invoice_menu');

function dspp_invoice_page() {
    include(plugin_dir_path(__FILE__) . 'includes/spp_invoice_pages.php');
}

function dspp_invoice_details_page() {
    include 'invoice_details.php';
}

//
function dspp_get_all_statuses() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i", $table_name), ARRAY_A);
    return $results;
}

function dspp_get_all_invoice_statuses() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_statuses';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i", $table_name), ARRAY_A);
    return $results;
}

function dspp_get_status_id_by_name($status_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `status_name` LIKE %s", $table_name, '%' . $wpdb->esc_like($status_name) . '%'), ARRAY_A);
    return $results;
}

function dspp_get_invoices_status_id_by_name($status_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_statuses';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `status_name` LIKE %s", $table_name, '%' . $wpdb->esc_like($status_name) . '%'), ARRAY_A);
    return $results;
}

function dspp_get_status_by_id($status_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `status_id` = %d", $table_name, $status_id), ARRAY_A);
    return $results;
}

function dspp_get_invoice_status_by_id($status_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_statuses';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `status_id` = %d", $table_name, $status_id), ARRAY_A);
    return $results;
}

function dspp_get_custom_orders() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_orders';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `active` = %d", $table_name, 1), ARRAY_A);
    return $results;
}

function dspp_get_custom_invoices() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i", $table_name), ARRAY_A);
    return $results;
}

function dspp_get_orders_by_user($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_orders';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `user_id`= %d AND `active` = %d", $table_name, $user_id, 1), ARRAY_A);
    return $results;
}

function dspp_get_invoices_by_user($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `user_id`= %d", $table_name, $user_id), ARRAY_A);
    return $results;
}

function dspp_get_unpaid_invoices_by_user($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `user_id`= %d AND invoice_status != %s", $table_name, $user_id, '3'), ARRAY_A);
    return $results;
}

function dspp_get_open_orders_by_user($user_id) {
    global $wpdb;
    $status = dspp_get_status_id_by_name('completed');
    $status_id = $status[0]['status_id'];
    $table_name = $wpdb->prefix . 'sppcrm_orders';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `user_id`= %d AND order_status != %d AND `active` = %d", $table_name, $user_id, $status_id, 1), ARRAY_A);
    return $results;
}

function dspp_get_complete_orders_by_user($user_id) {
    global $wpdb;
    $status = dspp_get_status_id_by_name('completed');
    $status_id = $status[0]['status_id'];
    $table_name = $wpdb->prefix . 'sppcrm_orders';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `user_id`= %d AND order_status = %d AND `active` = %d", $table_name, $user_id, $status_id, 1), ARRAY_A);
    return $results;
}

function dspp_get_billing_details_by_id($billing_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_billing_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `billing_id`= %d", $table_name, $billing_id), ARRAY_A);
    return $results;
}

function dspp_get_billing_details_by_user_id($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_billing_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `user_id`= %d", $table_name, $user_id), ARRAY_A);
    return $results;
}

function dspp_get_payment_method_by_id($method_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_payment_methods';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `method_id`= %d", $table_name, $method_id), ARRAY_A);
    return $results;
}

function dspp_get_invoice_details($invoice_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i where invoice_id = %d", $table_name, $invoice_id), ARRAY_A);
    return $results;
}

add_action('wp_ajax_dspp_delete_status_action', 'dspp_delete_status_callback');

function dspp_delete_status_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'delete_status_action' )){
        $statusId = isset($_POST['status_id']) ? sanitize_key(intval($_POST['status_id'])) : 0;
        if ($statusId > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
            $result = $wpdb->delete($table_name, array('status_id' => $statusId));
            if ($result !== false) {
                wp_send_json_success(array('message' => 'Status deleted successfully.'));
            } else {
                wp_send_json_error(array('message' => 'Failed to delete status.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_edit_status_action', 'dspp_edit_status_callback');

function dspp_edit_status_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'edit_status_action')){
        $statusId = isset($_POST['status_id']) ? sanitize_key(intval($_POST['status_id'])) : 0;
        $statusName = isset($_POST['status_name']) ? sanitize_text_field($_POST['status_name']) : '';
        $statusDescription = isset($_POST['status_description']) ? sanitize_textarea_field($_POST['status_description']) : '';

        if ($statusId > 0 && !empty($statusName)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
            $result = $wpdb->update(
                    $table_name, array('status_name' => $statusName, 'status_description' => $statusDescription, 'status_status' => 'Active'), array('status_id' => $statusId)
            );
            if ($result !== false) {
                wp_send_json_success(array('status_id' => $statusId, 'status_name' => $statusName, 'status_description' => $statusDescription));
            } else {
                wp_send_json_error(array('message' => 'Failed to update status.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }

    wp_die();
}

add_action('wp_ajax_dspp_delete_invoice_status_action', 'dspp_delete_invoice_status_callback');

function dspp_delete_invoice_status_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'delete_invoice_status_action')){
        $statusId = isset($_POST['status_id']) ? sanitize_key(intval($_POST['status_id'])) : 0;
//        print_r($statusId);
//        exit;
        if ($statusId > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_invoice_statuses';
            $result = $wpdb->delete($table_name, array('status_id' => $statusId));
            if ($result !== false) {
                wp_send_json_success(array('message' => 'Status deleted successfully.'));
            } else {
                wp_send_json_error(array('message' => 'Failed to delete status.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_edit_invoice_status_action', 'dspp_edit_invoice_status_callback');

function dspp_edit_invoice_status_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'edit_invoice_status_action')){
        $statusId = isset($_POST['status_id']) ? sanitize_key(intval($_POST['status_id'])) : 0;
        $statusName = isset($_POST['status_name']) ? sanitize_text_field($_POST['status_name']) : '';
        $statusDescription = isset($_POST['status_description']) ? sanitize_textarea_field($_POST['status_description']) : '';

        if ($statusId > 0 && !empty($statusName)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_invoice_statuses';
            $result = $wpdb->update(
                    $table_name, array('status_name' => $statusName, 'status_description' => $statusDescription, 'status_status' => 'Active'), array('status_id' => $statusId)
            );
            if ($result !== false) {
                wp_send_json_success(array('status_id' => $statusId, 'status_name' => $statusName, 'status_description' => $statusDescription));
            } else {
                wp_send_json_error(array('message' => 'Failed to update status.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

function dspp_get_custom_orders_by_id($order_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_orders';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `order_id` = %d", $table_name, $order_id), ARRAY_A);
    return $results;
}

function dspp_get_custom_orders_status_id($status_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_orders';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `order_status` = %d AND `active` = %d", $table_name, $status_id, 1), ARRAY_A);
    return $results;
}

function dspp_get_custom_invoice_status_id($status_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_invoice_details';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE `invoice_status` = %d", $table_name, $status_id), ARRAY_A);
    return $results;
}

add_action('wp_ajax_dspp_get_status_details_action', 'dspp_get_status_details_callback');

function dspp_get_status_details_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'get_status_details_action')){
        $statusId = isset($_POST['status_id']) ? sanitize_key(intval($_POST['status_id'])) : 0;
        if ($statusId > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
            $status = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE status_id = %d", $table_name, $statusId), ARRAY_A);
            if ($status) {
                wp_send_json_success($status);
            } else {
                wp_send_json_error(array('message' => 'Failed to retrieve status details.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_get_invoice_status_details_action', 'dspp_get_invoice_status_details_callback');

function dspp_get_invoice_status_details_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'get_invoice_status_details_action')){
        $statusId = isset($_POST['status_id']) ? sanitize_key(intval($_POST['status_id'])) : 0;
        if ($statusId > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_invoice_statuses';
            $status = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE status_id = %d", $table_name, $statusId), ARRAY_A);
            if ($status) {
                wp_send_json_success($status);
            } else {
                wp_send_json_error(array('message' => 'Failed to retrieve status details.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

// Order forms work added by GM
//function add_spp_order_forms_menu() {
//    add_menu_page(
//            'DSP Forms', 'DSP Forms', 'manage_options', 'spp_order_forms', 'spp_order_forms_page', 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M2 6c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1zm0 4c0-.55.45-1 1-1h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1z"/></svg>'), 20 // Menu position
//    );
//    add_submenu_page(
//            'spp_order_forms', // Parent menu slug
//            'Add New', // Page title
//            'Add New Form', // Menu title
//            'manage_options', // Capability
//            'spp_add_new_orders_form', // Submenu slug
//            'spp_add_new_orders_forms_page' // Callback function to display the submenu page
//    );
//    add_submenu_page(
//            'spp_order_forms', // Parent menu slug
//            'Form Details', // Page title
//            '', // Menu title
//            'manage_options', // Capability
//            'spp_form_details', // Submenu slug
//            'spp_form_details_page' // Callback function to display the submenu page
//    );
//}
//
//add_action('admin_menu', 'add_spp_order_forms_menu');

function dspp_spp_add_new_orders_forms_page() {
    include 'order_forms.php';
}

function dspp_spp_form_details_page() {
    include 'order_forms_details.php';
}

function dspp_spp_order_forms_page() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'spp_order_forms_page')){
        
    } else {
        $form_type_id = isset($_GET['form_type_id']) ? sanitize_key($_GET['form_type_id']) : -1;
        $form_types = get_all_form_types();
        $order_forms = ($form_type_id == -1) ? get_custom_forms() : get_custom_orders_form_type_id($form_type_id);
        ?>
        <h1>Forms</h1>
        <a href="?page=spp_add_new_orders_form"> Add New </a>
        <!-- Add your tabs here -->
        <h2 class="nav-tab-wrapper">
            <?php foreach ($form_types as $form_type) {
                ?><a href="?page=spp_order_forms&form_type_id=<?php echo esc_html($form_type['form_type_id']); ?>" class="nav-tab"><?php echo esc_html($form_type['form_type_name']); ?></a><?php }
            ?>
        </h2>
        <br>
        <table id="order_forms">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr> 
            </thead>
            <tbody>
                <?php
                echo '<pre>';
                foreach ($order_forms as $form) {
                    $form_id = $form['form_id'];
                    $form_title = $form['form_title'];
                    $form_created_date = $form['form_created_date'];
                    $form_type_id = $form['form_type_id'];
                    $form_type = array_shift(get_form_type_by_id($form_type_id));
                    $form_status = $form['form_status'];
                    $status_btn = strtolower($form_status) == "active" ? '<a href="#" data-form-id="' . esc_html($form_id) . '" data-new-status="Deactive" class="change_status_order_form">Deactivate</a>' : '<a href="#" data-form-id="' . $form_id . '" data-new-status="Active" class="change_status_order_form">Activate</a>';
                    echo '<tr>';
                    echo '<td class="product-name">' . esc_html($form_id) . '</td>';
                    echo '<td class="product-name"><a href="admin.php?page=spp_form_details&form_id=' . esc_html($form_id) . '" class="order-view"><strong>' . esc_html($form_title). '</strong></a></td>';
                    echo '<td class="product-name">' . esc_html($form_type['form_type_name']) . '</td>';
                    echo '<td class="product-name">' . esc_html($form_created_date) . '</td>';
                    echo '<td class="product-name">' . esc_html($form_status) . '</td>';
                    echo '<td class="product-name"> ' .esc_html($status_btn). ' | <a href="#" data-form-id="' . esc_html($form_id) . '" class="delete_order_form">Delete</a></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr> 
            </tfoot>
        </table>
        <?php
    }
}

function dspp_get_custom_forms() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_forms';
    $results = $wpdb->get_results("SELECT * FROM %i", $table_name, ARRAY_A);
    return $results;
}

function dspp_get_custom_forms_by_id($form_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_forms';
    $results = $wpdb->get_results("SELECT * FROM %i WHERE `form_id` = %d", $table_name, $form_id, ARRAY_A);
    return $results;
}

function dspp_get_custom_orders_form_type_id($form_type_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_forms';
    $results = $wpdb->get_results("SELECT * FROM %i WHERE `form_type_id` = %d", $table_name, $form_type_id, ARRAY_A);
    return $results;
}

function dspp_get_all_form_types() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
    $results = $wpdb->get_results("SELECT * FROM %i", $table_name, ARRAY_A);
    return $results;
}

function dspp_get_form_type_by_id($form_type_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
    $results = $wpdb->get_results("SELECT * FROM %i WHERE `form_type_id` = %d", $table_name, $form_type_id, ARRAY_A);
    return $results;
}

add_action('wp_ajax_dspp_delete_form_type_action', 'dspp_delete_form_type_callback');

function dspp_delete_form_type_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'delete_form_type_action')){
        $form_type_id = isset($_POST['form_type_id']) ? sanitize_key(intval($_POST['form_type_id'])) : 0;
        if ($form_type_id > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
            $result = $wpdb->delete($table_name, array('form_type_id' => $form_type_id));
            if ($result !== false) {
                wp_send_json_success(array('message' => 'Status deleted successfully.'));
            } else {
                wp_send_json_error(array('message' => 'Failed to delete status.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_get_form_type_details_action', 'dspp_get_form_type_details_callback');

function dspp_get_form_type_details_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'get_form_type_details_action')){
        $form_type_id = isset($_POST['form_type_id']) ? sanitize_key(intval($_POST['form_type_id'])) : 0;
        if ($form_type_id > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
            $status = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE form_type_id = %d", $table_name, $form_type_id), ARRAY_A);
            if ($status) {
                wp_send_json_success($status);
            } else {
                wp_send_json_error(array('message' => 'Failed to retrieve status details.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_edit_form_type_action', 'dspp_edit_form_type_callback');

function dspp_edit_form_type_callback() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'edit_form_type_action')){
        $form_type_id = isset($_POST['form_type_id']) ? sanitize_key(intval($_POST['form_type_id'])) : 0;
        $form_type_name = isset($_POST['form_type_name']) ? sanitize_text_field($_POST['form_type_name']) : '';
        $form_type_description = isset($_POST['form_type_description']) ? sanitize_textarea_field($_POST['form_type_description']) : '';
        if ($form_type_id > 0 && !empty($form_type_name)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
            $result = $wpdb->update(
                    $table_name, array('form_type_name' => $form_type_name, 'form_type_description' => $form_type_description), array('form_type_id' => $form_type_id)
            );
            if ($result !== false) {
                wp_send_json_success(array('form_type_id' => $form_type_id, 'form_type_name' => $form_type_name, 'form_type_description' => $form_type_description));
            } else {
                wp_send_json_error(array('message' => 'Failed to update status.'));
            }
        }
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

function dspp_get_all_services_callback() {
    $custom_post_type = 'dspp_service';
    $args = array(
        'post_type' => $custom_post_type,
        'posts_per_page' => 10,
    );
    $custom_query = new WP_Query($args);
    $services = array();
    if ($custom_query->have_posts()) {
        while ($custom_query->have_posts()) : $custom_query->the_post();
            $service_info = array(
                'label' => get_the_title(),
                'value' => get_the_ID(),
                'selected' => false
            );
            $services[] = $service_info;
        endwhile;
        wp_reset_postdata();
    }
    if (count($services) > 0) {
        wp_send_json_success($services);
    } else {
        wp_send_json_error('Error: Form could not be saved.');
    }
    wp_die();
}

// Register the AJAX action
add_action('wp_ajax_dspp_get_all_services', 'dspp_get_all_services_callback');
add_action('wp_ajax_nopriv_dspp_get_all_services', 'dspp_get_all_services_callback');
include(plugin_dir_path(__FILE__) . 'includes/order-form-functions.php');
include(plugin_dir_path(__FILE__) . 'includes/payment-methods-functions.php');
include(plugin_dir_path(__FILE__) . 'includes/page-shortcode-functions.php');
include(plugin_dir_path(__FILE__) . 'includes/user-functions.php');
include(plugin_dir_path(__FILE__) . 'includes/menu-functions.php');
include(plugin_dir_path(__FILE__) . 'includes/template-functions.php');

