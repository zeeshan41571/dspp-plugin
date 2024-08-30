<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bestitsol.com/
 * @since      1.0.0
 *
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/public
 * @author     Best IT Solutions <info@bestitsol.com>
 */
class Dspp_Crm_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style('font-awesome-css', plugin_dir_url(__FILE__) .'css/fontawesome/css/all.css', array(), $this->version, 'all', true);
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/spp-crm-public.css', array(), $this->version, 'all', true);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
        wp_enqueue_script('bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.js', array(), $this->version, true);
        wp_enqueue_script('dataTables', plugin_dir_url(__FILE__) . 'js/dataTables.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/spp-crm-public.js', array('jquery'), $this->version, true);
        wp_enqueue_script('spp-crm-ajax-requests-public', plugin_dir_url(__FILE__) . 'js/spp-crm-ajax-requests-public.js', array(), $this->version, false);
        $custom_forgot_password = wp_create_nonce('custom_forgot_password');
        $add_to_session_nonce = wp_create_nonce('add_to_session_nonce');
        $update_user_profile = wp_create_nonce('update_user_profile');
        $register_user = wp_create_nonce('register_user');
        $check_coupon = wp_create_nonce('check_coupon');
        $update_quantity_in_session = wp_create_nonce('update_quantity_in_session');
        $remove_from_session = wp_create_nonce('remove_from_session');
        $dspp_view_cart_button = wp_create_nonce('dspp_view_cart_button');
        $save_billing_details_ajax = wp_create_nonce('save_billing_details_ajax');
        $dspp_bits_generate_invoice = wp_create_nonce('dspp_bits_generate_invoice');
        $dspp_ajax_user_login = wp_create_nonce('dspp_ajax_user_login');
        $dspp_bits_payment_processor = wp_create_nonce('dspp_bits_payment_processor');
        
        // Pass the nonce to the JavaScript file
        wp_localize_script('spp-crm-ajax-requests-public', 'dspp_nonces', array(
            'custom_forgot_password' => $custom_forgot_password,
            'add_to_session_nonce' => $add_to_session_nonce,
            'update_user_profile' => $update_user_profile,
            'register_user' => $register_user,
            'check_coupon' => $check_coupon,
            'update_quantity_in_session' => $update_quantity_in_session,
            'remove_from_session' => $remove_from_session,
            'dspp_view_cart_button'=>$dspp_view_cart_button,
            'save_billing_details_ajax'=>$save_billing_details_ajax,
            'dspp_bits_generate_invoice'=>$dspp_bits_generate_invoice,
            'dspp_ajax_user_login'=>$dspp_ajax_user_login,
            'dspp_bits_payment_processor'=>$dspp_bits_payment_processor,
            'site_url'=> site_url(),
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}
