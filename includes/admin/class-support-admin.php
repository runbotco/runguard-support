<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runguard Support Admin.
 *
 * @package  Runguard/Admin
 * @category Admin
 * @author   Runguard
 */
class Runguard_Admin {

	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 59 );
		add_action( 'admin_init', array( $this, 'settings_tabs' ) );
	}

	/**
	 * Add the settings page.
	 */
	public function settings_menu() {
		if ( Runguard_Helpers::is_runguard() ) {
			add_action( 'admin_notices', array( $this, 'runguard_message' ), 59 );
			add_options_page(
				'Runbot Support',
				'Runbot Support',
				'manage_options',
				'runguard-support',
				array( $this, 'html_settings_page' )
			);
		}
	}

	public function runguard_message() {
		$option = get_option( 'runguard_support_settings' );
		if ( ! empty( $option['admin_notice'] ) ) {
			?>
			<div class="notice" style="border-left-color:#BAE0F4">
				<p><img src="<?php echo esc_url( plugins_url( 'images/runbot-logo.png', dirname( __FILE__ ) ) ); ?>" style="max-width:45px;margin-right:15px;vertical-align:middle;"><strong>Support Notes:</strong> <?php esc_html_e( $option['admin_notice'] ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Render the settings page for this plugin.
	 */
	public function html_settings_page() {
		include dirname( __FILE__ ) . '/views/html-settings-page.php';
	}

	/**
	 * Add Plugin Settings Tabs.
	 */
	public function settings_tabs() {
		/**
		* Plugin settings form fields.
		*/
		$settings_option = 'runguard_support_settings';
		$bt_options      = get_option( 'runguard_support_settings' );

		// Set Custom Fields section.
		add_settings_section(
			'options_section',
			__( '', 'runguard-support' ),
			array( $this, 'section_options_callback' ),
			$settings_option
		);

		// Add admin notice text area
		add_settings_field(
			'admin_notice',
			__( 'Runguard Support Notice', 'runguard-support' ),
			array( $this, 'textarea_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'        => $settings_option,
				'id'          => 'admin_notice',
				'description' => __( 'Enter notice that will show for Runguard admins only.', 'runguard-support' ),
			)
		);

		// Add option to disable/enable Core auto updates.
		add_settings_field(
			'auto_update_core',
			__( 'Core Auto-Updates', 'runguard-support' ),
			array( $this, 'checkbox_auto_update_core_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'auto_update_core',
				'label' => __( 'Enable to allow major version auto-updates for Core.', 'runguard-support' ),
			)
		);

		// Add option to disable/enable plugin auto updates.
		add_settings_field(
			'auto_update_plugins',
			__( 'Plugin Auto-Updates', 'runguard-support' ),
			array( $this, 'checkbox_auto_update_plugins_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'auto_update_plugins',
				'label' => __( 'Enable core auto-update functionality for plugins.', 'runguard-support' ),
			)
		);

		// Add option to disable/enable theme auto updates.
		add_settings_field(
			'auto_update_themes',
			__( 'Theme Auto-Updates', 'runguard-support' ),
			array( $this, 'checkbox_auto_update_themes_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'auto_update_themes',
				'label' => __( 'Enable core auto-update functionality for themes.', 'runguard-support' ),
			)
		);

		// Add option to hide Logtivity from normal users (only show if Logtivity is installed).
		if ( $this->is_plugin_installed( 'logtivity/logtivity.php' ) ) {
			add_settings_field(
				'enable_logtivity_menu',
				__( 'Hide Logtivity?', 'runguard-support' ),
				array( $this, 'checkbox_element_callback' ),
				$settings_option,
				'options_section',
				array(
					'menu'  => $settings_option,
					'id'    => 'enable_logtivity_menu',
					'label' => __( 'When checked, hides Logtivity menu and plugin from normal users (only Runguard admins can see it).', 'runguard-support' ),
				)
			);
		}

		// Add option to hide WP Umbrella from normal users (only show if WP Umbrella is installed).
		if ( $this->is_plugin_installed( 'wp-health/wp-health.php' ) ) {
			add_settings_field(
				'hide_wp_umbrella',
				__( 'Hide WP Umbrella?', 'runguard-support' ),
				array( $this, 'checkbox_element_callback' ),
				$settings_option,
				'options_section',
				array(
					'menu'  => $settings_option,
					'id'    => 'hide_wp_umbrella',
					'label' => __( 'When checked, hides WP Umbrella settings and plugin from normal users (only Runguard admins can see it).', 'runguard-support' ),
				)
			);
		}

		// Add option to hide WPvivid (only show if any WPvivid version is installed).
		if ( $this->is_plugin_installed( 'wpvivid-backuprestore/wpvivid-backuprestore.php' ) || $this->is_plugin_installed( 'wpvivid-backup-pro/wpvivid-backup-pro.php' ) ) {
			add_settings_field(
				'hide_wpvivid',
				__( 'Hide WPvivid?', 'runguard-support' ),
				array( $this, 'checkbox_element_callback' ),
				$settings_option,
				'options_section',
				array(
					'menu'  => $settings_option,
					'id'    => 'hide_wpvivid',
					'label' => __( 'When checked, hides WPvivid plugins from the plugins page and removes the admin menu for normal users (only Runguard admins can see it).', 'runguard-support' ),
				)
			);
		}

		// Add option to hide Helpscout bubble.
		add_settings_field(
			'hide_tab',
			__( 'Hide Helpscout Bubble', 'runguard-support' ),
			array( $this, 'checkbox_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'hide_tab',
				'label' => __( 'Hides the Helpscout chat bubble in the bottom-right corner.', 'runguard-support' ),
			)
		);

		// Add option to hide topbar help tab.
		add_settings_field(
			'disable_helpscout_chat',
			__( 'Hide Topbar Help Tab', 'runguard-support' ),
			array( $this, 'checkbox_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'disable_helpscout_chat',
				'label' => __( 'Hides the "Runbot Help" from the top admin bar.', 'runguard-support' ),
			)
		);

		// Register settings.
		register_setting( $settings_option, $settings_option, array( $this, 'validate_options' ) );

		/**
		* Server Information form fields.
		*/
		$information_option = 'runguard_server_information';
		// Set Custom Fields section.
		add_settings_section(
			'information_section',
			__( '', 'runguard-support' ),
			array( $this, 'section_options_callback' ),
			$information_option
		);

		add_settings_field(
			'server_info',
			__( 'Server Stats', 'runguard-support' ),
			array( $this, 'server_info_element_callback' ),
			$information_option,
			'information_section',
			array(
				'menu'  => $information_option,
				'id'    => 'server_info',
				'label' => __( 'Showing server stats and variables.', 'runguard-support' ),
			)
		);
		register_setting( $information_option, $information_option, array( $this, 'validate_options' ) );
	}

	/**
	 * Section null fallback.
	 */
	public function section_options_callback() {}

	/**
	 * Checkbox element callback.
	 *
	 * @param array $args Callback arguments.
	 */
	public function checkbox_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$current = $options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '0';
		}

		include dirname( __FILE__ ) . '/views/html-checkbox-field.php';
	}

	/**
	 * Checkbox auto update Core element callback.
	 *
	 * @param array $args Callback arguments.
	 */
	public function checkbox_auto_update_core_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$current = $options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '0';
		}

		include dirname( __FILE__ ) . '/views/html-auto-update-core-field.php';
	}

	/**
	 * Checkbox auto update plugins element callback.
	 *
	 * @param array $args Callback arguments.
	 */
	public function checkbox_auto_update_plugins_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$current = $options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '0';
		}

		include dirname( __FILE__ ) . '/views/html-auto-update-plugins-field.php';
	}

	/**
	 * Checkbox auto update themes element callback.
	 *
	 * @param array $args Callback arguments.
	 */
	public function checkbox_auto_update_themes_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$current = $options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '0';
		}

		include dirname( __FILE__ ) . '/views/html-auto-update-themes-field.php';
	}

	/**
	 * Textarea element callback.
	 *
	 * @param array $args Callback arguments.
	 */
	public function textarea_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$value = $options[ $id ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		include dirname( __FILE__ ) . '/views/html-textarea-field.php';
	}

	/**
	 * Serverinfo element callback.
	 *
	 * @param array $args Callback arguments.
	 */
	public function server_info_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$value = $options[ $id ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		include dirname( __FILE__ ) . '/views/html-serverinfo-field.php';
	}

	/**
	 * Check if a plugin is installed.
	 *
	 * @param  string $plugin_path Plugin path (e.g., 'plugin-folder/plugin-file.php').
	 * @return bool                True if plugin is installed, false otherwise.
	 */
	private function is_plugin_installed( $plugin_path ) {
		$all_plugins = get_plugins();
		return array_key_exists( $plugin_path, $all_plugins );
	}

	/**
	 * Valid options.
	 *
	 * @param  array $input options to valid.
	 *
	 * @return array        validated options.
	 */
	public function validate_options( $input ) {
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) && ! empty( $input[ $key ] ) ) {
				$output[ $key ] = $input[ $key ];
			}
		}

		return $output;
	}
}

new Runguard_Admin();
