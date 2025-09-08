<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runguard helper class.
 *
 * @package  Runguard
 * @category Core
 * @author  Runguard
 */
class Runguard_Helpers {
	public static $help_scout_widget_init      = 'window.Beacon("init", "dd888cd8-106f-4ce7-8178-cb86abe53ed6")';
	public static $help_scout_widget_menu_init = 'window.Beacon("init", "dd888cd8-106f-4ce7-8178-cb86abe53ed6"); window.Beacon("open");';

	/**
	 * Check email address to see if user is a member of the Runguard team (and also an administrator).
	 */
	public static function is_runguard() {
		$current_user = wp_get_current_user();
		return (
			current_user_can( 'manage_options' )
			&& (
				strpos( $current_user->user_email, '@runbot.co' ) !== false
				|| strpos( $current_user->user_email, '@runguard.co' ) !== false
			)
		);
	}

	/**
	 * Disk information.
	 *
	 * @return array disk information.
	 */
	public static function get_disk_info() {
		$disk_info                    = array();
		$disk_info['disk_total']      = 'Unavailable';
		$disk_info['disk_used']       = 'Unavailable';
		$disk_info['disk_free']       = 'Unavailable';
		$disk_info['disk_percentage'] = 'Unavailable';

		if (
			function_exists( 'disk_free_space' )
			&& ( disk_free_space( __DIR__ ) !== false )
			&& function_exists( 'disk_total_space' )
			&& ( disk_total_space( __DIR__ ) > 0 )
		) {
			/* Get disk space free (in bytes). */
			$disk_free                    = disk_free_space( __DIR__ );
			/* And get disk space total (in bytes).  */
			$disk_total                   = disk_total_space( __DIR__ );
			/* Now we calculate the disk space used (in bytes). */
			$disk_used                    = $disk_total - $disk_free;
			/* Percentage of disk used - this will be used to also set the width % of the progress bar. */
			$disk_percentage              = sprintf( '%.2f', ( $disk_used / $disk_total ) * 100 );
			$disk_info['disk_total']      = $disk_total;
			$disk_info['disk_used']       = $disk_used;
			$disk_info['disk_free']       = $disk_free;
			$disk_info['disk_percentage'] = $disk_percentage;
		}

		return $disk_info;
	}

	/**
	 * Format the argument from bytes to MB, GB, etc.
	 *
	 * @param array bytes size.
	 *
	 * @return array size from bytes to larger ammount.
	 */
	public static function format_size( $bytes ) {
		if ( $bytes === 'Unavailable' ) {
			return $bytes;
		}

		$types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		for (
				$i = 0;
				$bytes >= 1000 && $i < ( count( $types ) - 1 );
				$bytes /= 1024, $i++
		);
		return ( round( $bytes, 2 ) . ' ' . $types[ $i ] );
	}

	/**
	 * Display Runguard Notification
	 * @param string $msg. String to display on the notification
	 * @return void
	 */
	public static function display_notification( $msg ) {
		if ( ! is_array( $msg ) ) {
			$msg = array(
				'status' => 1,
				'msg'    => $msg,
			);
		}

		// Exit if message is empty
		if ( $msg['msg'] === '' ) {
			return;
		}

		$msg_class = ( $msg['status'] ? 'np-notice' : 'error np-notice' );
		?>
			<link rel="stylesheet" href="<?php echo esc_url( Runguard::$plugin_dir_url . 'includes/css/html-notifications-style.css' ); ?>" type="text/css" media="all">
			<div class="notice <?php echo esc_attr( $msg_class ); ?>">
				<p><img src="<?php echo esc_url( Runguard::$plugin_dir_url . 'includes/images/runbot-logo.png' ); ?>" style="max-width:45px;margin-right:15px;vertical-align:middle;"><strong><?php echo esc_html( $msg['msg'] ); ?></strong></p>
			</div>
		<?php
	}

	/**
	 * Bypass clearing cache for non-production domains.
	 * @param string $domain. URL to be cleared
	 * @return boolean. true if any of the strings match, or the WP_ENVIRONMENT_TYPE constant is set to staging or development
	 */
	public static function is_production( $home_url ) {
		if ( defined( 'RUNGUARD_PRODUCTION_CHECK_BYPASS' ) ) {
			return true;
		}

		$domain_bypass_strings = array(
			'development',
			'staging',
			'local',
			'localhost',
			'yawargenii',
			'iwillfixthat',
			'wpstagecoach',
			'bigscoots-staging',
			'dev',
			'test',
			'flywheelsites',
			'closte',
			'runcloud',
			'kinsta',
			'cloudwaysapps',
			'pantheonsite',
			'sg-host',
			'onrocket',
			'pressdns',
			'wpengine',
			'wpstage',
		);

		if ( function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() !== 'production' ) {
			return false;
		}

		foreach ( $domain_bypass_strings as $string ) {
			// Is $string prepended and appended by a / or . in $home_url.
			if ( preg_match( '#([/.]' . $string . '[/.])#m', $home_url ) ) {
				return false;
			}
		}

		return true;
	}
}
