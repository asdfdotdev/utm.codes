<?php
/**
 * Utm.codes - A plugin that makes building analytics friendly links quick and easy.
 *
 * @package UtmDotCodes
 * @copyright 2018-2022 Chris Carlevato (https://asdf.dev)
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @link https://utm.codes
 *
 * @wordpress-plugin
 * Plugin Name: utm.codes
 * Plugin URI: https://utm.codes
 * Description: A plugin that makes building analytics friendly links quick and easy.
 * Version: 1.8.2
 * Author: Chris Carlevato
 * Author URI: https://asdf.dev
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

define( 'UTMDC_VERSION', '1.8.2' );
define( 'UTMDC_MINIMUM_WP_VERSION', '4.7' );
define( 'UTMDC_MINIMUM_PHP_VERSION', '5.6' );
define( 'UTMDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UTMDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'UTMDC_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'UTMDC_POST_TYPE', 'utmdclink' );

$in_wp_admin   = is_admin();
$running_tests = ( defined( 'UTMDC_IS_TEST' ) && UTMDC_IS_TEST );
$should_load   = ( ! class_exists( 'UtmDotCodes' ) );

global $pagenow;

if ( ( $in_wp_admin || $running_tests ) && $should_load ) {

	require_once UTMDC_PLUGIN_DIR . '/classes/class-utmdotcodes.php';
	$new_udc = new UtmDotCodes();

	// The filter provides access to the instance of  UtmDotCodes().
	$new_udc_filters = apply_filters( 'utmdc_new_udc', $new_udc );
	// And if something goes sideways we re-instantiate the class.
	if ( true !== $new_udc_filters instanceof UtmDotCodes ) {
		$new_udc = new UtmDotCodes();
	}

	// The first set of actions as an array.
	$arr_udc_actions = array(

		//add_action( 'plugins_loaded', array( $new_udc, 'load_languages' ) );
		'plugins_loaded' => array(
			'h_n' => 'plugins_loaded',
			'cb'  => 'load_languages',
		),

		// add_action( 'init', array( $new_udc, 'create_post_type' ) );
		'init' => array(
			'h_n' => 'init',
			'cb'  => 'create_post_type',
		),

		// 	add_action( 'admin_menu', array( $new_udc, 'add_settings_page' ) );
		'admin_menu' => array(
			'h_n' => 'admin_menu',
			'cb'  => 'add_settings_page',
		),

		// 	add_action( 'admin_init', array( $new_udc, 'register_plugin_settings' ) );
		'admin_init' => array(
			'h_n' => 'admin_init',
			'cb'  => 'register_plugin_settings',
		),

		// add_action( 'admin_head', array( &$this, 'add_css' ) );
		'admin_head' => array(
			'h_n' => 'admin_head',
			'cb'  => 'add_css',
		),

		// add_action( 'admin_footer', array( &$this, 'add_js' ) );
		'admin_footer' => array(
			'h_n' => 'admin_footer',
			'cb'  => 'add_js',
		),

		// add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ), 10, 2 );
		'add_meta_boxes' => array(
			'h_n' => 'add_meta_boxes',
			'cb'  => 'add_meta_box',
			'a_a' => 2,
		),

		// add_action( 'add_meta_boxes', array( &$this, 'remove_meta_boxes' ) );
		'add_meta_boxes_1' => array(
			'h_n' => 'add_meta_boxes',
			'cb'  => 'remove_meta_boxes',
		),

		// add_action( 'save_post', array( &$this, 'save_post' ), 10, 1 );
		'save_post' => array(
			'h_n' => 'save_post',
			'cb'  => 'save_post',
		),

		// add_action( 'dashboard_glance_items', array( &$this, 'add_glance' ) );
		'dashboard_glance_items' => array(
			'h_n' => 'dashboard_glance_items',
			'cb'  => 'add_glance',
		),

		//( 'wp_ajax_utmdc_check_url_response', array( &$this, 'check_url_response' ) );
		'wp_ajax_utmdc_check_url_response' => array(
			'h_n' => 'wp_ajax_utmdc_check_url_response',
			'cb'  => 'check_url_response',
		),
	);

	// A chance to alter the actions array before they're added.
	$arr_udc_actions_filtered = apply_filters( 'utmdc_udc_actions', $arr_udc_actions );
	// Only use the returned array if it's an array.
	if ( is_array( $arr_udc_actions_filtered ) ) {
		$arr_udc_actions = $arr_udc_actions_filtered;
	}

	// The defaults for the hooks.
	$arr_udc_hook_defaults = array(
		'active' => true,     // simple bool on / off flag
		'h_n'    => false,    // hook name
		'new'    => $new_udc, // instance of the class the callback is in.
		'cb'     => false,    // callback
		'p'      => 10,       // priority
		'a_a'    => 1,        // accepted arguments count
	);

	// Add the actions.
	utmdc_add_action_helper( $arr_udc_actions, $arr_udc_hook_defaults );

	// The first set of filters as an array.
	$arr_udc_filters = array(

		// add_filter( 'plugin_action_links_' . UTMDC_PLUGIN_FILE, array( $new_udc, 'add_links' ), 10, 1 );
		'plugin_action_links_' . UTMDC_PLUGIN_FILE => array(
			'h_n' => 'plugin_action_links_' . UTMDC_PLUGIN_FILE,
			'cb'  => 'add_links',
		),

		// add_filter( 'wp_insert_post_data', array( $new_udc, 'insert_post_data' ), 10, 2 );
		'wp_insert_post_data' => array(
			'h_n' => 'wp_insert_post_data',
			'cb'  => 'insert_post_data',
			'a_a' => 2,
		),

		// 	add_filter( 'gettext', array( $new_udc, 'change_publish_button' ), 10, 2 );
		'gettext' => array(
			'h_n' => 'gettext',
			'cb'  => 'change_publish_button',
			'a_a' => 2,
		),

		// add_filter( sprintf( 'pre_update_option_%s', self::POST_TYPE . '_rebrandly_domains_update' ), array( $new_udc, 'pre_rebrandly_domains_update' ), 10, 3);
		'pre_update_option_' . UTMDC_POST_TYPE . '_rebrandly_domains_update' => array(
			'h_n' => 'pre_update_option_' . UTMDC_POST_TYPE . '_rebrandly_domains_update',
			'cb'  => 'pre_rebrandly_domains_update',
			'a_a' => 3,
		),
	);

	// A chance to alter the filters array before they're added.
	$arr_udc_filters_filtered = apply_filters( 'utmdc_udc_filters', $arr_udc_filters );
	if ( is_array( $arr_udc_filters_filtered ) ) {
		$arr_udc_filters = $arr_udc_filters_filtered;
	}

	// Add the filters.
	utmdc_add_filter_helper( $arr_udc_filters, $arr_udc_hook_defaults );


	// This next set of hooks is limited to: wp-admin/edit.php?post_type=utmdclink .
	$is_post_list  = ( 'edit.php' === $pagenow );
	$is_utmdc_post = ( UTMDC_POST_TYPE === filter_input( INPUT_GET, 'post_type', FILTER_DEFAULT ) );

	if ( ( is_admin() && $is_post_list && $is_utmdc_post ) || $new_udc->is_test() ) {

		$arr_udc_actions_1 = array(
			// add_action( 'restrict_manage_posts', array( &$this, 'filter_ui' ), 5, 1 );
			'restrict_manage_posts' => array(
				'h_n' => 'restrict_manage_posts',
				'cb'  => 'filter_ui',
				'p'   => 5,
			),

			// add_action( 'pre_get_posts', array( &$this, 'apply_filters' ), 5, 1 );
			'pre_get_posts' => array(
				'h_n' => 'pre_get_posts',
				'cb'  => 'apply_filters',
				'p'   => 5,
			),
		);

		// A chance to alter the actions_1 array before they're added.
		$arr_udc_actions_filtered = apply_filters( 'utmdc_udc_actions_1', $arr_udc_actions_1 );
		if ( is_array( $arr_udc_actions_filtered ) ) {
			$arr_udc_actions_1 = $arr_udc_actions_filtered;
		}

		utmdc_add_action_helper( $arr_udc_actions_1, $arr_udc_hooks_defaults );

		$arr_udc_filters_1 = array(

			// add_filter( 'manage_' . UTMDC_POST_TYPE . '_posts_columns', array( $new_udc, 'post_list_header' ), 10, 1 );
			'manage_' . UTMDC_POST_TYPE . '_posts_columns' => array(
				'h_n' => 'manage_' . UTMDC_POST_TYPE . '_posts_columns',
				'cb'  => 'post_list_header',
			),

			// add_filter( 'manage_' . UTMDC_POST_TYPE . '_posts_custom_column', array( $new_udc, 'post_list_columns' ), 10, 2 );
			'manage_' . UTMDC_POST_TYPE . '_posts_custom_column' => array(
				'h_n' => 'manage_' . UTMDC_POST_TYPE . '_posts_custom_column',
				'cb'  => 'post_list_columns',
				'a_a' => 2,
			),

			// add_filter( 'months_dropdown_results', array( $new_udc, 'months_dropdown_results' ), 10, 2 );
			'months_dropdown_results' => array(
				'h_n' => 'months_dropdown_results',
				'cb'  => 'months_dropdown_results',
				'a_a' => 2,
			),

			// add_filter( 'bulk_actions-edit-' . UTMDC_POST_TYPE, array($new_udc, 'bulk_actions' ) );
			'bulk_actions-edit-' . UTMDC_POST_TYPE => array(
				'h_n' => 'bulk_actions-edit-' . UTMDC_POST_TYPE,
				'cb'  => 'bulk_actions',
			),

			// add_filter( 'post_row_actions', array( $new_udc, 'remove_quick_edit' ), 10, 1 );
			'post_row_actions' => array(
				'h_n' => 'post_row_actions',
				'cb'  => 'remove_quick_edit',
			),
		);

		// A chance to alter the filters_1 array before they're added.
		$arr_udc_filters_filtered = apply_filters( 'utmdc_udc_filters_1', $arr_udc_filters_1 );
		if ( is_array( $arr_udc_filters_filtered ) ) {
			$arr_udc_filters_1 = $arr_udc_filters_filtered;
		}

		utmdc_add_filter_helper( $arr_udc_filters_1, $arr_udc_hook_defaults );
	}


	// (De)Activation Hooks
	require_once UTMDC_PLUGIN_DIR . '/classes/class-utmdotcodesactivation.php';
	$new_udca = new UtmDotCodesActivation();

	// The filter provides access to the instance of UtmDotCodesActivation().
	$new_udc_filters = apply_filters( 'utmdc_new_udca', $new_udca );
	// And if something goes sideways we re-instantiate the class.
	if ( true !== $new_udc_filters instanceof UtmDotCodesActivation ) {
		$new_udca = new UtmDotCodesActivation();
	}

	register_activation_hook( UTMDC_PLUGIN_FILE, array( $new_udca, 'activation' ) );
	register_deactivation_hook( UTMDC_PLUGIN_FILE, array( $new_udca, 'deactivation' ) );
}

/**
 * WP's add_action done via array.
 *
 * @param array $arr_actions Array of actions to add.
 * @param array $arr_defaults Array of default values for the actions.
 * @return void
 */
function utmdc_add_action_helper( $arr_actions, $arr_defaults ) {

	foreach ( $arr_actions as $key => $arr_action ) {
		$arr_action = wp_parse_args( $arr_action, $arr_defaults );
		if ( true === $arr_action['active'] && is_string( $arr_action['h_n'] ) && is_string( $arr_action['cb'] ) ) {
			$ret = add_action( $arr_action['h_n'], array( $arr_action['new'], $arr_action['cb'] ), $arr_action['p'], $arr_action['a_a'] );
		}
	}
}

/**
 * WP's add_filter done via array.
 *
 * @param array $arr_filters Array of filters to add.
 * @param array $arr_defaults Array of default values for the filters.
 * @return void
 */
function utmdc_add_filter_helper( $arr_filters, $arr_defaults ) {

	foreach ( $arr_filters as $key => $arr_filter ) {
		$arr_filter = wp_parse_args( $arr_filter, $arr_defaults );
		if ( true === $arr_filter['active'] && is_string( $arr_filter['h_n'] ) && is_string( $arr_filter['cb'] ) ) {
			$ret = add_filter( $arr_filter['h_n'], array($arr_filter['new'] , $arr_filter['cb'] ), $arr_filter['p'], $arr_filter['a_a'] );
		}
	}
}
