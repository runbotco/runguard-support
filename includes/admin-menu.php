<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add Admin Bar Menu Items.
function bt_custom_toolbar_links( $wp_admin_bar ) {

	if ( current_user_can( 'edit_others_posts' ) ) {

		?>
			<link rel="stylesheet" href="<?php echo Runguard::$plugin_dir_url . 'includes/css/html-admin-menu.css?ver=' . RUNGUARD_PLUGIN_VERSION; ?>" type="text/css" media="all">
		<?php

		// Add "Runbot Help" parent menu items.
		$args = array(
			'id'     => 'runguard-menu',
			'title'  => '<span class="ab-icon"></span><span class="ab-label">' . __( 'Runbot Help', 'runguard-support' ) . '</span>',
			'parent' => false,
		);
		$wp_admin_bar->add_node( $args );

		// "Request Support" link to open the Support Hero widget
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'feast-support' ) {
			$args = array(
				'id'     => 'bt-get-help',
				'title'  => 'Request Support',
				'href'   => '#',
				'parent' => 'runguard-menu',
				'meta'   => array(
					'class'   => 'btButton',
					'title'   => 'Click to open the support contact form.',
					'onclick' => Runguard_Helpers::$help_scout_widget_menu_init,
				),
			);
			$wp_admin_bar->add_node( $args );
		}

		if ( Runguard_Helpers::is_runguard() ) {

			// "Plugin Settings" link to open the Runguard Support settings page.
			$args = array(
				'id'     => 'bt-settings',
				'title'  => 'Plugin Settings',
				'href'   => admin_url( 'options-general.php?page=runguard-support' ),
				'parent' => 'runguard-menu',
				'meta'   => array(
					'class' => 'btButton',
					'title' => 'Open Runbot Support plugin settings.',
				),
			);
			$wp_admin_bar->add_node( $args );
		}

		if ( Runguard_Helpers::is_runguard() ) {
			// add cpu load to admin menu.
			function serverinfo_admin_menu_item( $wp_admin_bar ) {

				$cpu_load_info = '';

				if ( function_exists( 'sys_getloadavg' ) ) {
					$cpu_loads = sys_getloadavg();
					if ( $cpu_loads && is_array( $cpu_loads ) ) {
						$cpu_load_info = '<span>Load: ' . esc_html( round( $cpu_loads[0], 2) ) . ' &nbsp;' . esc_html( round( $cpu_loads[1], 2 ) ) . ' &nbsp;' . esc_html( round( $cpu_loads[2], 2 ) ) . '  &nbsp; ';
					}
				}

				$disk_space_info = 'Free Disk: ' . esc_html( Runguard_Helpers::format_size( Runguard_Helpers::get_disk_info()['disk_free'] ) ) . '</span>';
				$cpu_disk_info   = $cpu_load_info . $disk_space_info;
				$args            = array(
					'id'    => 'cpu-disk-info',
					'title' => $cpu_disk_info,
					'href'  => admin_url( 'options-general.php?page=runguard-support&tab=server_information' ),
					'meta'  => array(
						'class' => 'btButton',
						'title' => 'Open Runguard Support plugin settings.',
					),
				);
				$wp_admin_bar->add_node( $args );
			}
			add_action( 'admin_bar_menu', 'serverinfo_admin_menu_item', 1000 );
		}
	}
}
add_action( 'admin_bar_menu', 'bt_custom_toolbar_links', 99 );
