<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runguard Widget.
 *
 * @package  Runguard/Frontend
 * @category Widget
 * @author   Runguard
 */
class Runguard_Widget {
	/**
	 * Initialize the widget.
	 */
	public function __construct() {
		if ( current_user_can( 'edit_others_posts' ) ) {
			if ( ! is_admin() ) {
				add_action( 'wp_footer', array( $this, 'widget' ), 50 );
			} else {
				if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'feast-support' ) {
					add_action( 'admin_footer', array( $this, 'widget' ), 50 );
				}
			}
		}
	}

	public function widget() {
		$options                = get_option( 'runguard_support_settings', array() );
		$runguard_current_user = wp_get_current_user();
		
		// Use first name and last name if available, otherwise use site name
		$user_name = trim( $runguard_current_user->user_firstname . ' ' . $runguard_current_user->user_lastname );
		if ( empty( $user_name ) ) {
			$user_name = get_bloginfo( 'name' );
		}
		?>
		<script type = "text/javascript">
			! function(e, t, n) {
				function a() {
					var e = t.getElementsByTagName("script")[0],
						n = t.createElement("script");
					n.type = "text/javascript", n.async = !0, n.src = "https://beacon-v2.helpscout.net", e.parentNode.insertBefore(n, e)
				}
				if (e.Beacon = n = function(t, n, a) {
						e.Beacon.readyQueue.push({
							method: t,
							options: n,
							data: a
						})
					}, n.readyQueue = [], "complete" === t.readyState) return a();
				e.attachEvent ? e.attachEvent("onload", a) : e.addEventListener("load", a, !1)
				e.Beacon('prefill', {
					name: '<?php echo esc_html( sanitize_text_field( $user_name ) ); ?>',
					email: '<?php echo esc_html( sanitize_text_field( $runguard_current_user->user_email ) ); ?>'
				})

			}(window, document, window.Beacon || function() {});
		</script>
		<?php
		if ( is_admin() && ( ! isset( $options['hide_tab'] ) ) && ! defined('IFRAME_REQUEST') && ! Runguard_Helpers::is_runguard() ) {
			?>
			<script type = "text/javascript">
				<?php echo Runguard_Helpers::$help_scout_widget_init; ?>
			</script>
			<?php
		}
	}
}

new Runguard_Widget();