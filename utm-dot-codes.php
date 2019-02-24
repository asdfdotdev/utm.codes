<?php
/**
 * Utm.codes - Marketing Campaign Link Builder
 *
 * @package utm.codes
 * @copyright 2018-2019 ChristopherL (https://christopherl.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @wordpress-plugin
 * Plugin Name: utm.codes
 * Plugin URI: https://utm.codes
 * Description: Create and manage your marketing campaign links in WordPress.
 * Version: 1.5.0
 * Author: ChristopherL
 * Author URI: https://christopherl.com
 * License: GPL v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: utm-dot-codes
 */

/**
 * Plugins shouldn't be called directly.
 */
if ( ! function_exists( 'add_action' ) ) {
	die( '-1' );
}

define( 'UTMDC_VERSION', '1.5.0' );
define( 'UTMDC_MINIMUM_WP_VERSION', '4.7' );
define( 'UTMDC_MINIMUM_PHP_VERSION', '5.6' );
define( 'UTMDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UTMDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'UTMDC_PLUGIN_FILE', plugin_basename( __FILE__ ) );

$in_wp_admin   = is_admin();
$running_tests = ( defined( 'UTMDC_IS_TEST' ) && UTMDC_IS_TEST );
$should_load   = ( ! class_exists( 'UtmDotCodes' ) );

if ( ( $in_wp_admin || $running_tests ) && $should_load ) {

	require_once UTMDC_PLUGIN_DIR . '/classes/class-utmdotcodes.php';
	new UtmDotCodes();

	// (De)Activation Hooks
	require_once UTMDC_PLUGIN_DIR . '/classes/class-utmdotcodesactivation.php';
	new UtmDotCodesActivation();

}
