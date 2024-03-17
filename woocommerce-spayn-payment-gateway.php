<?php
/* @wordpress-plugin
 * Plugin Name:       WooCommerce SPayN Payment Gateway v 2.0
 * Plugin URI:        https://spayn.es
 * Description:       SPayN plugin for WooCommerce.
 * Version:           2.0.0
 * WC requires at least: 3.0
 * WC tested up to: 4.6.1
 * Author:            Seglan (Iñaki Garaizabal igaraizabal@seglan.com), Gonzalo Aguilar (gaguilar.delgado@gmail.com)
 * Author URI:        https://seglan.com
 * Text Domain:       woocommerce-spayn-payment-gateway-2
 * Domain Path: 	  /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

//include plugins_url("api.php");
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.


    
if ( ! defined( 'WOOCOMMERCE_SPAYN_PLUGIN_VERSION' ) ) {
	define( 'WOOCOMMERCE_SPAYN_PLUGIN_VERSION', '2.0.1' );
}

if ( ! defined( 'WOOCOMMERCE_SPAYN_PLUGIN_FILE' ) ) {
	define( 'WOOCOMMERCE_SPAYN_PLUGIN_FILE', __FILE__ );
}



register_activation_hook(
	__FILE__,
	'woocommerce_spayn_activate'
);

function woocommerce_spayn_activate() {

	error_log('WooCommerce SPayN Payment Gateway has been activated');
}

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));


if(spayn_payment_is_woocommerce_active()){
	// Include WC_Payment_Gateway in the payment options
	add_filter('woocommerce_payment_gateways', 'woocommerce_spayn_add_payment_gateway');
	function woocommerce_spayn_add_payment_gateway( $gateways ){
		$gateways[] = 'WC_Spayn_Payment_Gateway';
		return $gateways; 
	}
	add_action('plugins_loaded', 'woocommerce_spayn_init_payment_gateway');
	function woocommerce_spayn_init_payment_gateway(){
		// Include the main class.
		if ( ! class_exists( 'WC_Spayn_Paymen_Gateway', false ) ) {
			require_once dirname( __FILE__ ) . '/classes/class-woocommerce-spayn-payment-gateway.php';
		}
	}
}




// add_action( 'woocommerce_after_order_notes', function() {
// 	ob_start();
// 	$gw=(new WC_spayn_Payment_Gateway())->getInstance();
// 	$token=new tokenRequest($gw);
// 	$body='<div id="spayn-iFrame" style="width:100%;margin:0;padding:0;"><p><h3>';
// 	$body.= $payment_ref;//$gw->title;	
// 	$body.='</h3><br><iframe sandbox="allow-same-origin allow-scripts allow-popups allow-forms" frameBorder="0" align="center" style="width:100%; margin:0; padding:0; overflow: auto;" height="380" frameborder="no" src="';
// 	$body.= $token->sendMessage();
// 	$body.='"></iframe></div>';
// 	echo $body;
// });


/**
 * @return bool
 */
function spayn_payment_is_woocommerce_active()
{
	$active_plugins = (array) get_option('active_plugins', array());

	if (is_multisite()) {
		$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	}

	return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}
