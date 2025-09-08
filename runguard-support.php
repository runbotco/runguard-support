<?php

/**
 * Plugin Name: Runguard Support
 * Description: Lightweight WordPress management and monitoring plugin for ongoing site maintenance and support services.
 * Version:     1.6.1
 * Author:      Runguard
 * Author URI:  https://runguard.co
 * GitHub URI:  runbotco/runguard-support
 * License:     GPLv2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// GitHub updater
include( dirname( __FILE__ ) . '/github-updater.php' );

// Load Admin menu
include( dirname( __FILE__ ) . '/includes/admin-menu.php' );

if ( ! defined( 'RUNGUARD_PLUGIN_VERSION' ) ) {
	define( 'RUNGUARD_PLUGIN_VERSION', '1.6.1' );
}

if ( ! class_exists( 'Runguard' ) ) {
		/**
		 * Runguard main class.
		 *
		 * @package  Runguard
		 * @category Core
		 * @author   Fernando Acosta, Andrew Wilder, Sergio Scabuzzo
		 */
	class Runguard {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Runguard plugin root URL.
		 */
		public static $plugin_dir_url = '';

		/**
		 * Initialize the plugin.
		 */
		private function __construct() {
			// Include classes.
			$this->includes();

			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				$this->admin_includes();
			}

			self::$plugin_dir_url = plugin_dir_url( __FILE__ );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Include admin actions.
		 */
		protected function admin_includes() {
			include dirname( __FILE__ ) . '/includes/admin/class-support-admin.php';
		}

		/**
		 * Include plugin functions.
		 */
		protected function includes() {
			include_once dirname( __FILE__ ) . '/includes/class-support-helpers.php';
			include_once dirname( __FILE__ ) . '/includes/class-support-widget.php';
			include_once dirname( __FILE__ ) . '/includes/class-support-overrides.php';
		}
	}

	/**
	 * Init the plugin.
	 */
	add_action( 'plugins_loaded', array( 'Runguard', 'get_instance' ) );
}

