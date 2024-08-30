<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://bestitsol.com/
 * @since      1.0.0
 *
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Dspp_Crm
 * @subpackage Dspp_Crm/includes
 * @author     Best IT Solutions <info@bestitsol.com>
 */
class Dspp_Crm_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
                'digital-service-provider-crm',
                false,
                dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
