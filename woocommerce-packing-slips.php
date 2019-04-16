<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://welaunch.io/plugins/woocommerce-packing-slips/
 * @since             1.0.
 * @package           WooCommerce_Packing_Slips
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Packing Slips
 * Plugin URI:        https://welaunch.io/plugins/woocommerce-packing-slips/
 * Description:       Generate Packaging Slips for WooCommerce Orders with Ease.
 * Version:           1.0.5
 * Author:            weLaunch
 * Author URI:        https://welaunch.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-packing-slips
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-packing-slips-activator.php
 */
function activate_woocommerce_packing_slips() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-packing-slips-activator.php';
	WooCommerce_Packing_Slips_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-packing-slips-deactivator.php
 */
function deactivate_woocommerce_packing_slips() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-packing-slips-deactivator.php';
	WooCommerce_Packing_Slips_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_packing_slips' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_packing_slips' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-packing-slips.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_packing_slips() {

	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new WooCommerce_Packing_Slips($version);
	$plugin->run();

	return $plugin;

}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php') && (is_plugin_active('redux-framework/redux-framework.php') || is_plugin_active('redux-dev-master/redux-framework.php') ) ){
	$WooCommerce_Packing_Slips = run_woocommerce_packing_slips();
} else {
	add_action( 'admin_notices', 'woocommerce_packing_slips_installed_notice' );
}

function woocommerce_packing_slips_installed_notice()
{
	?>
    <div class="error">
      <p><?php _e( 'WooCommerce Packing Slips requires the WooCommerce and Redux Framework plugin. Please install or activate them before!', 'woocommerce-packing-slips'); ?></p>
    </div>
    <?php
}