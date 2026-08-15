<?php
/**
 * Utm.codes - A plugin that makes building analytics friendly links quick and easy.
 *
 * @package UtmDotCodes
 * @copyright 2018-2026 Chris Carlevato (https://asdf.dev)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @link https://utm.codes
 *
 * @wordpress-plugin
 * Plugin Name: utm.codes
 * Plugin URI: https://utm.codes
 * Description: A plugin that makes building analytics friendly links quick and easy.
 * Version: 1.9.2
 * Author: Chris Carlevato
 * Author URI: https://asdf.dev
 * License: GPL v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: utm-dot-codes
 */

/**
 * Plugins shouldn't be called directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UTMDC_VERSION', '1.9.2' );
define( 'UTMDC_MINIMUM_WP_VERSION', '5.1.0' );
define( 'UTMDC_MINIMUM_PHP_VERSION', '7.1.0' );
define( 'UTMDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UTMDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'UTMDC_PLUGIN_FILE', plugin_basename( __FILE__ ) );

$utmdc_in_wp_admin   = is_admin();
$utmdc_running_tests = ( defined( 'UTMDC_IS_TEST' ) && UTMDC_IS_TEST );
$utmdc_should_load   = ( ! class_exists( 'UtmDotCodes' ) );

if ( ( $utmdc_in_wp_admin || $utmdc_running_tests ) && $utmdc_should_load ) {

	require_once UTMDC_PLUGIN_DIR . '/classes/class-utmdotcodes.php';
	new UtmDotCodes();

	// (De)Activation Hooks
	require_once UTMDC_PLUGIN_DIR . '/classes/class-utmdotcodesactivation.php';
	new UtmDotCodesActivation();

}
