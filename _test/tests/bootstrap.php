<?php
/**
 * PHPUnit bootstrap file
 *
 * @package UtmDotCodes
 */

define( 'UTMDC_IS_TEST', true );

$config_path       = realpath( dirname( __FILE__ ) . '/../' );
$env_variables_set = ( (bool) getenv( 'UTMDC_PLUGIN_DIR' ) && (bool) getenv( 'UTMDC_BITLY_API' ) );

if ( ! $env_variables_set ) {
	if ( file_exists( $config_path . '/config.inc.local.php' ) ) {
		require_once $config_path . '/config.inc.local.php';
	} elseif ( ! file_exists( $config_path . '/config.inc.php' ) ) {
		throw new Exception( 'Could not find config.inc.php, nor config.inc.local.php file. Please add config file before continuing.' );
	} else {
		require_once $config_path . '/config.inc.php';
	}
}

$wp_tests_dir = getenv( 'WP_TEST_DIR' );
if ( ! $wp_tests_dir ) {
	$wp_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $wp_tests_dir . '/includes/functions.php' ) ) {
	throw new Exception( "Could not find $wp_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" );
}

// Give access to tests_add_filter() function.
require_once $wp_tests_dir . '/includes/functions.php';


/**
 * Manually load the utm.codes plugin for testing
 */
function _manually_load_utm_dot_codes() {
	require_once '../utm-dot-codes.php';
	update_option( 'active_plugins', 'utm-dot-codes/utm-dot-codes.php' );
}
tests_add_filter( 'muplugins_loaded', '_manually_load_utm_dot_codes' );


// Start up the WP testing environment.
require $wp_tests_dir . '/includes/bootstrap.php';
