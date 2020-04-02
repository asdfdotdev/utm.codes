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
 */
class UtmDotCodesActivation {

	/**
	 * UtmDotCodes_Activation constructor, adds (de)activation hooks for our plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {
		register_activation_hook( UTMDC_PLUGIN_FILE, array( &$this, 'activation' ) );
		register_deactivation_hook( UTMDC_PLUGIN_FILE, array( &$this, 'deactivation' ) );
	}

	/**
	 * Activation hook callback, verify suitable WordPress version, add settings
	 *
	 * @since 1.0
	 */
	public function activation() {
		global $wp_version;

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

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

		add_option( 'utmdc_version', UTMDC_VERSION, '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_social', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_apikey', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_lowercase', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_alphanumeric', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_nospaces', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_labels', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_notes_show', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_notes_preview', '0', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_shortener', 'none', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains_active', '', '', 'no' );
		add_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains_update', '', '', 'no' );
	}

	/**
	 * Deactivation hook callback, remove the settings we created to clean things up.
	 *
	 * @since 1.6.0
	 */
	public function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		delete_option( 'utmdc_version' );
		delete_option( UtmDotCodes::POST_TYPE . '_social' );
		delete_option( UtmDotCodes::POST_TYPE . '_apikey' );
		delete_option( UtmDotCodes::POST_TYPE . '_lowercase' );
		delete_option( UtmDotCodes::POST_TYPE . '_alphanumeric' );
		delete_option( UtmDotCodes::POST_TYPE . '_nospaces' );
		delete_option( UtmDotCodes::POST_TYPE . '_labels' );
		delete_option( UtmDotCodes::POST_TYPE . '_notes_show' );
		delete_option( UtmDotCodes::POST_TYPE . '_notes_preview' );
		delete_option( UtmDotCodes::POST_TYPE . '_shortener' );
		delete_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains' );
		delete_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains_active' );
		delete_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains_update' );
	}

}
