<?php
/**
 * Utm.codes activation class
 *
 * @package UtmDotCodes
 */

/**
 * Class UtmDotCodes_Activation
 *
 * Implements activation and deactivation hooks for the utm.codes plugin
 *
 * @package UtmDotCodes
 */
class UtmDotCodesActivation {

	/**
	 * UtmDotCodes_Activation constructor, adds (de)activation hooks for our plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {
		register_activation_hook( UTMDC_PLUGIN_FILE, [ &$this, 'activation' ] );
		register_deactivation_hook( UTMDC_PLUGIN_FILE, [ &$this, 'deactivation' ] );
	}

	/**
	 * Processes plugin activation, including version checks
	 *
	 * @since 1.0
	 */
	public function activation() {
		global $wp_version;

		/**
		 * Check minimum WordPress version to ensure compatibility
		 */
		if ( version_compare( UTMDC_MINIMUM_WP_VERSION, $wp_version, '>' ) ) {
			deactivate_plugins( basename( UTMDC_PLUGIN_FILE ) );

			/* translators: Placeholder in this string is the minimum WordPress version. */
			$error_response = _x( 'utm.codes plugin requires WordPress %s or newer.', 'Placeholder is minimum WordPress version.', 'utm-dot-codes' );

			wp_die(
				sprintf(
					esc_html( $error_response ),
					esc_html( UTMDC_MINIMUM_WP_VERSION )
				),
				'Plugin Activation Error',
				array(
					'response'  => 200,
					'back_link' => true,
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
