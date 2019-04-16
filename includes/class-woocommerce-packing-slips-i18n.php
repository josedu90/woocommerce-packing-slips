<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://welaunch.io/plugins/woocommerce-packing-slips/
 * @since      1.0.0
 *
 * @package    WooCommerce_Packing_Slips
 * @subpackage WooCommerce_Packing_Slips/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WooCommerce_Packing_Slips
 * @subpackage WooCommerce_Packing_Slips/includes
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
class WooCommerce_Packing_Slips_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$loaded = load_plugin_textdomain(
			'woocommerce-packing-slips',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
