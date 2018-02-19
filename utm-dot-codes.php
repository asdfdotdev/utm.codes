<?php
/**
 * utm.codes - Marketing Campaign Link Builder
 *
 * @package utm.codes
 * @copyright 2018- ChristopherL (https://christopherl.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @wordpress-plugin
 * Plugin Name: utm.codes
 * Plugin URI: https://utm.codes
 * Description: Create and manage your marketing campaign links in WordPress.
 * Version: 1.0.1
 * Author: ChristopherL
 * Author URI: https://christopherl.com
 * License: GPL v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: utm-dot-codes
 */


// Plugins shouldn't be called directly
if ( !function_exists( 'add_action' ) ) {
	die('-1');
}

define( 'UTMDC_VERSION', '1.0.1' );
define( 'UTMDC_MINIMUM_WP_VERSION', '4.7' );
define( 'UTMDC_MINIMUM_PHP_VERSION', '5.6' );
define( 'UTMDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UTMDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'UTMDC_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'UTMDC_TEXT_DOMAIN', 'utm-dot-codes' );

if ( ( is_admin() || @UTMDC_IS_TEST ) && !class_exists('UtmDotCodes') ) {

	require_once UTMDC_PLUGIN_DIR . '/classes/utmdotcodes.php';
	new UtmDotCodes();

	// (De)Activation Hooks
	require_once UTMDC_PLUGIN_DIR . '/classes/utmdotcodes_activation.php';
	new UtmDotCodes_Activation();

}
