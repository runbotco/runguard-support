<?php
/**
 * Settings page view.
 *
 * @package Runguard/Admin/View
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'runguard_settings';
	?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=runguard-support&tab=runguard_settings" class="nav-tab <?php echo 'runguard_settings' === $active_tab ? 'nav-tab-active' : ''; ?>">Runguard Settings</a>
		<a href="?page=runguard-support&tab=server_information" class="nav-tab <?php echo 'server_information' === $active_tab ? 'nav-tab-active' : ''; ?>">Server Information</a>
	</h2>

	<?php
	if ( 'runguard_settings' === $active_tab ) {
		?>
		<form method="post" action="options.php">
		<?php
			submit_button();		
			settings_fields( 'runguard_support_settings' );
			do_settings_sections( 'runguard_support_settings' );
			submit_button();
		?>
		</form>

		<?php
	} elseif ( 'server_information' === $active_tab ) {
		settings_fields( 'runguard_server_information' );
		do_settings_sections( 'runguard_server_information' );
	}
	?>

</div>
<?php
