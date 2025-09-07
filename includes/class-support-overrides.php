<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Runguard_Support_Overrides
 *
 * @package  Runguard
 * @category Core
 * @author Runguard
 */
class Runguard_Support_Overrides {
	private static $options_array     = 'runguard_support_settings';
	private static $runguard_options = '';

	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		self::$runguard_options = get_option( self::$options_array, array() );
		add_action( 'init', array( $this, 'is_auto_update_set' ) );
		add_action( 'init', array( $this, 'check_default_options' ) );
		add_filter( 'wp_mail', array( $this, 'runguard_override_alert_email' ) );
    	add_action( 'admin_head-users.php', array( $this, 'hide_delete_all_content' ) );
		add_action( 'admin_menu', array( $this, 'hide_logtivity_settings' ) );

		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_background_image_regeneration', '__return_false' );
		}

		add_filter( 'site_status_tests', array( $this, 'remove_site_health_test'), 99 );
	}

	public static function remove_site_health_test( $tests ) {
		unset( $tests['direct']['persistent_object_cache'], $tests['direct']['yoast-page-comments-check'] );
		return $tests;
	}

	public function hide_logtivity_settings() {
		if ( ! Runguard_Helpers::is_runguard() ) {
			remove_submenu_page( 'logs', 'logtivity-settings' );
			remove_submenu_page( 'lgtvy-logs', 'logtivity-settings' );
			add_filter( 'logtivity_hide_settings_page', '__return_true' );
		}
	}

	public function hide_delete_all_content() {
		?>
		<style type="text/css">
			#delete_option0,
			#delete_option1,
			label[for=delete_option0],
			form#updateusers div.wrap fieldset ul:first-child li label
			{display: none;}
		</style>
		<?php
	}

	public function is_auto_update_set() {
		if ( ! isset( self::$runguard_options['auto_update_core'] ) ) {
			add_filter( 'allow_major_auto_core_updates', '__return_false' );
		}

		if ( ! isset( self::$runguard_options['auto_update_plugins'] ) && ! Runguard_Helpers::is_runguard() ) {
			add_filter( 'plugins_auto_update_enabled', '__return_false' );
			add_filter( 'auto_update_plugin', '__return_false' );
		}

		if ( ! isset( self::$runguard_options['auto_update_themes'] ) && ! Runguard_Helpers::is_runguard() ) {
			add_filter( 'themes_auto_update_enabled', '__return_false' );
			add_filter( 'auto_update_theme', '__return_false' );
		}
	}

	/**
	 * Check mandatory options, set to default if not present
	 */
	public function check_default_options() {
		// No default options needed anymore
		$runguard_default_options = array();

		foreach ( $runguard_default_options as $key => $val ) {
			if ( ! array_key_exists( $key, self::$runguard_options ) || ! isset( self::$runguard_options[ $key ] ) ) {
				self::$runguard_options[ $key ] = $val;
			}
		}

		update_option( self::$options_array, self::$runguard_options );
	}

	public function runguard_override_alert_email( $atts ) {
		$email_list         = !is_array( $atts['to'] ) ? [ $atts['to'] ] : $atts['to'];
		$is_runguard_alert = false;
		foreach ( $email_list as $email ) {
			if ( ( str_contains( $email, 'alerts@runguard.net' ) != false) || ( str_contains( $email, 'alerts@blogtutor.com' ) != false ) ) {
				$is_runguard_alert = true;
			}
		}
		if ( $is_runguard_alert ) {

			$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
			if ( 'www.' === substr( $sitename, 0, 4 ) ) {
				$sitename = substr( $sitename, 4 );
			}

			$replyto_email = 'wordpress@' . $sitename;

			$atts['headers'][] = 'Reply-To: ' . get_bloginfo( 'name' ) . '<' . $replyto_email . '>';
			$atts['headers'][] = 'X-Auto-Response-Suppress: AutoReply';
		}

		return $atts;
	}
}

new Runguard_Support_Overrides();