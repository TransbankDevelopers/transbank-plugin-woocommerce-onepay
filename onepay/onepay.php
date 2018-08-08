<?php
/*
Plugin Name: Onepay
Plugin URI: https://github.com/TransbankDevelopers/transbank-plugin-woocommerce-onepay
Description: Permite pagar con Tarjetas de Crédito a través de la aplicación Onepay
Version: 1.0.0
Author: Onepay <transbankdevelopers@continuum.cl>
Author URI: https://github.com/TransbankDevelopers

Copyright: © 2017 Onepay <transbankdevelopers@continuum.cl>.
License: BSD-3-Clause
License URI: https://opensource.org/licenses/BSD-3-Clause
*/

add_action( 'plugins_loaded', 'Onepay_init', 0 );
function Onepay_init() {

	if ( ! class_exists( 'Onepay' ) ) return;

	/**
 	 * Localisation
	 */
	load_plugin_textdomain( 'onepay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
 	 * Gateway class
 	 */
	class Onepay extends WC_Payment_Gateway {
		/**
	     * Constructor for the gateway.
	     */
	    public function __construct() {
			$this->id                 = 'Onepay';
			$this->icon               = apply_filters('woocommerce_Onepay_icon', plugin_dir_url( dirname( __FILE__ ) ) . 'public/images/logo_onepay.png');
			$this->has_fields         = false;
			$this->method_title       = __( 'Onepay', 'onepay' );
			$this->method_description = __( 'This is the payment gateway description', 'onepay' );
			$this->supports = array(
				'products',
			  );



			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

	        // Define user set variables
			$this->title         = $this->get_option( 'title' );
			$this->description   = $this->get_option( 'description' );
			$this->example_field = $this->get_option( 'example_field' );

			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	    	add_action( 'woocommerce_thankyou_Onepay', array( $this, 'thankyou_page' ) );
	    }

		/**
	     * Create form fields for the payment gateway
	     *
	     * @return void
	     */
	    public function init_form_fields() {
	        $this->form_fields = array(
	            'enabled' => array(
	                'title' => __( 'Enable/Disable', 'onepay' ),
	                'type' => 'checkbox',
	                'label' => __( 'Enable Onepay', 'onepay' ),
	                'default' => 'no'
	            ),
	            'title' => array(
	                'title' => __( 'Title', 'onepay' ),
	                'type' => 'text',
	                'description' => __( 'This controls the title which the user sees during checkout', 'onepay' ),
	                'default' => __( 'Onepay', 'onepay' ),
	                'desc_tip'      => true,
	            ),
	            'description' => array(
	                'title' => __( 'Customer Message', 'onepay' ),
	                'type' => 'textarea',
	                'default' => __( 'Description of the payment gateway', 'onepay' )
	            ),
				'example_field' => array(
					'title' => __( 'Example field', 'onepay' ),
					'type' => 'text',
					'default' => __( 'Example field description', 'onepay' )
				),
	        );
	    }

	    /**
	     * Process the order payment status
	     *
	     * @param int $order_id
	     * @return array
	     */
	    public function process_payment( $order_id ) {
	        $order = new WC_Order( $order_id );

	        // Mark as on-hold (we're awaiting the cheque)
	        $order->update_status( 'on-hold', __( 'Awaiting payment', 'onepay' ) );

	        // Reduce stock levels
	        $order->reduce_order_stock();

	        // Remove cart
	        WC()->cart->empty_cart();

	        // Return thankyou redirect
	        return array(
	            'result'    => 'success',
	            'redirect'  => $this->get_return_url( $order )
	        );
	    }

	    /**
	     * Output for the order received page.
	     *
	     * @return void
	     */
	    public function thankyou() {
	        if ( $description = $this->get_description() )
	            echo wpautop( wptexturize( wp_kses_post( $description ) ) );

	        echo '<h2>' . __( 'Our Details', 'onepay' ) . '</h2>';

	        echo '<ul class="order_details Onepay_details">';

	        $fields = apply_filters( 'woocommerce_Onepay_fields', array(
	            'example_field'  => __( 'Example field', 'onepay' )
	        ) );

	        foreach ( $fields as $key => $value ) {
	            if ( ! empty( $this->$key ) ) {
	                echo '<li class="' . esc_attr( $key ) . '">' . esc_attr( $value ) . ': <strong>' . wptexturize( $this->$key ) . '</strong></li>';
	            }
	        }

	        echo '</ul>';
	    }
	}

	/**
 	* Add the Gateway to WooCommerce
 	**/
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_Onepay' );
	function woocommerce_add_gateway_Onepay($methods) {
		$methods[] = 'Onepay';
		return $methods;
	}
}
