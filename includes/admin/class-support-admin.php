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
class NerdPress_Admin {

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
		if ( NerdPress_Helpers::is_nerdpress() ) {
			add_action( 'admin_notices', array( $this, 'nerdpress_message' ), 59 );
			add_options_page(
				'Runguard Support',
				'Runguard Support',
				'manage_options',
				'nerdpress-support',
				array( $this, 'html_settings_page' )
			);
		}
	}

	public function nerdpress_message() {
		$option = get_option( 'runguard_support_settings' );
		if ( ! empty( $option['admin_notice'] ) ) {
			?>
			<div class="notice" style="border-left-color:#0F145B">
				<p><img src="<?php echo esc_url( plugins_url( 'images/nerdpress-icon-250x250.png', dirname( __FILE__ ) ) ); ?>" style="max-width:45px;vertical-align:middle;">Runguard Notes: <strong><?php esc_html_e( $option['admin_notice'] ); ?></strong></p>
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
			__( '', 'nerdpress-support' ),
			array( $this, 'section_options_callback' ),
			$settings_option
		);

		// Add admin notice text area
		add_settings_field(
			'admin_notice',
			__( 'Runguard Support Notice', 'nerdpress-support' ),
			array( $this, 'textarea_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'        => $settings_option,
				'id'          => 'admin_notice',
				'description' => __( 'Enter notice that will show for Runguard admins only.', 'nerdpress-support' ),
			)
		);

		// Add option to disable/enable Core auto updates.
		add_settings_field(
			'auto_update_core',
			__( 'Core Auto-Updates', 'nerdpress-support' ),
			array( $this, 'checkbox_auto_update_core_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'auto_update_core',
				'label' => __( 'Enable to allow major version auto-updates for Core.', 'nerdpress-support' ),
			)
		);

		// Add option to disable/enable plugin auto updates.
		add_settings_field(
			'auto_update_plugins',
			__( 'Plugin Auto-Updates', 'nerdpress-support' ),
			array( $this, 'checkbox_auto_update_plugins_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'auto_update_plugins',
				'label' => __( 'Enable core auto-update functionality for plugins.', 'nerdpress-support' ),
			)
		);

		// Add option to disable/enable theme auto updates.
		add_settings_field(
			'auto_update_themes',
			__( 'Theme Auto-Updates', 'nerdpress-support' ),
			array( $this, 'checkbox_auto_update_themes_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'auto_update_themes',
				'label' => __( 'Enable core auto-update functionality for themes.', 'nerdpress-support' ),
			)
		);

		// Add option to hide "Need Help?" tab in dashboard.
		add_settings_field(
			'hide_tab',
			__( 'Hide Help Tab?', 'nerdpress-support' ),
			array( $this, 'checkbox_element_callback' ),
			$settings_option,
			'options_section',
			array(
				'menu'  => $settings_option,
				'id'    => 'hide_tab',
				'label' => __( 'Hides the "Need Help?" tab in the bottom of the dashboard.', 'nerdpress-support' ),
			)
		);

		// Register settings.
		register_setting( $settings_option, $settings_option, array( $this, 'validate_options' ) );

		/**
		* Server Information form fields.
		*/
		$information_option = 'nerdpress_server_information';
		// Set Custom Fields section.
		add_settings_section(
			'information_section',
			__( '', 'nerdpress-support' ),
			array( $this, 'section_options_callback' ),
			$information_option
		);

		add_settings_field(
			'server_info',
			__( 'Server Stats', 'nerdpress-support' ),
			array( $this, 'server_info_element_callback' ),
			$information_option,
			'information_section',
			array(
				'menu'  => $information_option,
				'id'    => 'server_info',
				'label' => __( 'Showing server stats and variables.', 'nerdpress-support' ),
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

new NerdPress_Admin();