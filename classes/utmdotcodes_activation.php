<?php
/**
 * @package utm.codes
 */

/**
 * Class UtmDotCodes_Activation
 *
 * Implements activation and deactivation hooks for the utm.codes plugin
 *
 * @package utm.codes
 */
class UtmDotCodes_Activation {

	/**
	 * UtmDotCodes_Activation constructor, adds (de)activation hooks for our plugin
	 *
	 * @version 1.0
	 * @since 1.0
	 */
	public function __construct() {
		register_activation_hook( UTMDC_PLUGIN_FILE, [&$this, 'activation'] );
		register_deactivation_hook( UTMDC_PLUGIN_FILE, [&$this, 'deactivation'] );
	}

	/**
	 * Processes plugin activation, including version checks
	 *
	 * @version 1.0
	 * @since 1.0
	 */
	public function activation() {
		global $wp_version;

		/**
		 * Check minimum WordPress version to ensure compatibility
		 */
		if ( version_compare( UTMDC_MINIMUM_WP_VERSION, $wp_version, '>' ) ) {
			deactivate_plugins( basename( UTMDC_PLUGIN_FILE ) );
			wp_die(
				sprintf(
					__('utm.codes plugin requires WordPress %s or newer.', UTMDC_TEXT_DOMAIN),
					UTMDC_MINIMUM_WP_VERSION
				),
				'Plugin Activation Error',
				array(
					'response' => 200,
					'back_link' => true
				)
			);
		}

		update_option( 'utmdc_version', UTMDC_VERSION );
	}

	/**
	 * Deactivation hook not currently in use
	 */
	public function deactivation() {}

}
