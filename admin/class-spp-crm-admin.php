<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bestitsol.com/
 * @since      1.0.0
 *
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/admin
 * @author     Best IT Solutions <info@bestitsol.com>
 */
class Dspp_Crm_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function dspp_enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Spp_Crm_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Spp_Crm_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style('bootstrap-css', plugin_dir_url(__FILE__) .'css/bootstrap.css',array(), $this->version, 'all');
        wp_enqueue_style('bootstrap-select-css', plugin_dir_url(__FILE__) .'css/bootstrap-select.css',array(), $this->version, 'all');
        wp_enqueue_style('dataTables-css', plugin_dir_url(__FILE__) .'css/dataTables.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/spp-crm-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function dspp_enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Spp_Crm_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Spp_Crm_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        
        wp_enqueue_script('bootstrap',  plugin_dir_url(__FILE__).'js/bootstrap.bundle.js', array(), $this->version, true);
        wp_enqueue_script('dataTables',  plugin_dir_url(__FILE__).'js/dataTables.js', array(), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/spp-crm-admin.js', array(), $this->version, true);
        wp_enqueue_script('spp-crm-ajax-requests', plugin_dir_url(__FILE__) . 'js/spp-crm-ajax-requests.js', array(), $this->version, false);
        // Generate nonce
        $add_payment_methode = wp_create_nonce('add_payment_methode');
        $delete_status_action = wp_create_nonce('delete_status_action');
        $get_status_details_action = wp_create_nonce('get_status_details_action');
        $edit_status_action = wp_create_nonce('edit_status_action');
        $update_order_status_admin = wp_create_nonce('update_order_status_admin');
        $update_invoice_status_admin = wp_create_nonce('update_invoice_status_admin');
        $delete_invoice_status_action = wp_create_nonce('delete_invoice_status_action');
        $get_invoice_status_details_action = wp_create_nonce('get_invoice_status_details_action');
        $edit_invoice_status_action = wp_create_nonce('edit_invoice_status_action');
        
        // Pass the nonce to the JavaScript file
        wp_localize_script('spp-crm-ajax-requests', 'dspp_nonces', array(
            'add_payment_methode' => $add_payment_methode,
            'delete_status_action' => $delete_status_action,
            'get_status_details_action' => $get_status_details_action,
            'edit_status_action' => $edit_status_action,
            'update_order_status_admin' => $update_order_status_admin,
            'update_invoice_status_admin' => $update_invoice_status_admin,
            'delete_invoice_status_action' => $delete_invoice_status_action,
            'get_invoice_status_details_action' => $get_invoice_status_details_action,
            'edit_invoice_status_action' => $edit_invoice_status_action,
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}
