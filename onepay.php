<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/TransbankDevelopers/transbank-plugin-woocommerce-onepay
 * @since             1.0.0
 * @package           Onepay
 *
 * @wordpress-plugin
 * Plugin Name:       Onepay
 * Plugin URI:        https://github.com/TransbankDevelopers/transbank-plugin-woocommerce-onepay
 * Description:       Pay with Onepay using your favorite Credit Card!
 * Version:           1.0.0
 * Author:            Onepay
 * Author URI:        https://github.com/TransbankDevelopers
 * License:           BSD-3-Clause
 * License URI:       https://opensource.org/licenses/BSD-3-Clause
 * Text Domain:       onepay
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-onepay-activator.php
 */
function activate_onepay() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-onepay-activator.php';
	Onepay_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-onepay-deactivator.php
 */
function deactivate_onepay() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-onepay-deactivator.php';
	Onepay_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_onepay' );
register_deactivation_hook( __FILE__, 'deactivate_onepay' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

add_action( 'plugins_loaded', 'run_onepay', 0 );
function run_onepay() {
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
	require plugin_dir_path( __FILE__ ) . 'includes/class-onepay.php';
	$plugin = new Onepay();
	$plugin->run();
}

add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_Onepay' );
function woocommerce_add_gateway_Onepay($methods) {
	$methods[] = 'Onepay';
	return $methods;
}
