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
	 * Array of options "slugs" for storing plugin settings.
	 *
	 * @since TODO
;	 */
	protected $arr_options;

	/**
	 * UtmDotCodes_Activation constructor, adds (de)activation hooks for our plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->arr_options = array(
			'social',
			'apikey',
			'lowercase',
			'alphanumeric',
			'nospaces',
			'labels',
			'notes_show',
			'notes_preview',
			'shortener',
			'rebrandly_domains',
			'rebrandly_domains_active',
			'rebrandly_domains_update',
		);
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

		foreach ( $this->arr_options as $option ) {
			add_option( UtmDotCodes::POST_TYPE . '_' . $option, '', '', 'no' );
		}
		// The default value for _notes_preview is an exception so we update it here.
		update_option( UtmDotCodes::POST_TYPE . '_' . 'notes_preview', '0', '', 'no' );
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
		foreach ( $this->arr_options as $option ) {
			delete_option( UtmDotCodes::POST_TYPE . '_' . $option );
		}
	}

}
