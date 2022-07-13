<?php
/*
* Plugin Name: WooCommerce Stock Management
* Plugin URI: https://google.com/
* Description: Add stock management option for WooCommerce orders
* Author: 
* Version: 1.1.0
* Author URI: https://profiles.wordpress.org/
* Text Domain: stock-management
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Stock_Management' ) ) {
	class Stock_Management{

		/**
		 * Constructor.
		 *
		 * Fire all required wp actions
		 *
		 * @since  1.0.0
		 */
		function __construct(){

			add_action( 'init', [
				$this,
				'load'
			] );

		}

		/**
		 * Define plugin constants
		 *
		 * Include plugin files
		 *
		 * @since  1.0.0
		 */
		public function load(){

			if ( !defined( 'SM_VERSION' ) ) {
				define( 'SM_VERSION', "1.0.0" );
			}

			if ( !defined( 'SM_DIR' ) ) {
				define( 'SM_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( !defined( 'SM_URL' ) ) {
				define( 'SM_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( !class_exists( 'Stock_Management_Manager' ) ) {
				include SM_DIR . 'includes/class-stock-management-manager.php';
			}
		}

	}
	new Stock_Management();
}
