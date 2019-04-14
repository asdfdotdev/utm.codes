<?php
/**
 * Class TestUtmDotCodesActivation
 *
 * @package UtmDotCodes
 */

/**
 * Ajax tests
 */
class TestUtmDotCodesActivation extends WP_Ajax_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Confirm our plugin knows we're testing.
	 */
	public function test_is_test() {
		$plugin = new UtmDotCodes();

		$this->assertTrue( $plugin->is_test() );
	}

	/**
	 * Confirm WordPress and PHP versions meet minimum requirements and plugin is active.
	 *
	 * @depends test_is_test
	 */
	public function test_version_numbers_active() {
		$is_valid_wp = version_compare( get_bloginfo( 'version' ), UTMDC_MINIMUM_WP_VERSION, '>' );
		$this->assertTrue( $is_valid_wp );

		$is_valid_php = version_compare( phpversion(), UTMDC_MINIMUM_PHP_VERSION, '>' );
		$this->assertTrue( $is_valid_php );

		$this->assertTrue( is_plugin_active( 'utm-dot-codes/utm-dot-codes.php' ) );
	}

	/**
	 * Confirm (de)activation hooks are added successfully.
	 *
	 * @depends test_is_test
	 */
	public function test_plugin_hook_creation() {
		global $wp_filter;

		$this->assertEquals( 'WP_Hook', get_class( $wp_filter[ 'activate_' . UTMDC_PLUGIN_FILE ] ), true );
		$this->assertEquals( 'WP_Hook', get_class( $wp_filter[ 'deactivate_' . UTMDC_PLUGIN_FILE ] ), true );
	}

	/**
	 * Confirm activation hook creates settings successfully.
	 *
	 * @depends test_is_test
	 */
	public function test_activation_hook_valid() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );

		$plugin = new UtmDotCodesActivation();
		$plugin->activation();

		$this->assertEquals( get_option( 'utmdc_version' ), UTMDC_VERSION );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_social' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_apikey' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_labels' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ), '0' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_shortener' ), 'none' );
	}

	/**
	 * Confirm activation hook ignores non-administrator user.
	 *
	 * @depends test_is_test
	 */
	public function test_activation_hook_non_admin() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'author' ) ) );

		$plugin = new UtmDotCodesActivation();
		$plugin->activation();

		$this->assertFalse( get_option( 'utmdc_version' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_social' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_apikey' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_labels' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_shortener' ) );
	}

	/**
	 * Confirm deactivation hook removes settings successfully.
	 *
	 * @depends test_is_test
	 */
	public function test_deactivation_hook_valid() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );

		$plugin = new UtmDotCodesActivation();
		$plugin->activation();

		$this->assertEquals( get_option( 'utmdc_version' ), UTMDC_VERSION );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_social' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_apikey' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_labels' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ), '0' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_shortener' ), 'none' );

		$plugin->deactivation();

		$this->assertFalse( get_option( 'utmdc_version' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_social' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_apikey' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_labels' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_shortener' ) );
	}

	/**
	 * Confirm deactivation ignores non-administrator users.
	 *
	 * @depends test_is_test
	 */
	public function test_deactivation_hook_non_admin() {
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );

		$plugin = new UtmDotCodesActivation();
		$plugin->activation();

		$this->assertEquals( get_option( 'utmdc_version' ), UTMDC_VERSION );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_social' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_apikey' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_labels' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ), '0' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_shortener' ), 'none' );

		wp_set_current_user( $this->factory->user->create( array( 'role' => 'author' ) ) );
		$plugin->deactivation();

		$this->assertEquals( get_option( 'utmdc_version' ), UTMDC_VERSION );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_social' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_apikey' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_labels' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ), '' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ), '0' );
		$this->assertEquals( get_option( UtmDotCodes::POST_TYPE . '_shortener' ), 'none' );
	}

	/**
	 * Confirm failure for invalid PHP version number.
	 *
	 * @depends test_is_test
	 */
	public function test_wordpress_version_failure() {
		global $wp_version;
		$wp_version_actual = $wp_version;
		$wp_version = 4.6;

		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );

		try {
			ob_start();
			$plugin = new UtmDotCodesActivation();
			$plugin->activation();
			$output = ob_get_flush();
			ob_end_clean();
		} catch ( Exception $exception ) {
			$this->assertEquals( $exception->getMessage(), 'utm.codes plugin requires WordPress 4.7 or newer.' );
		}

		$this->assertFalse( get_option( 'utmdc_version' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_social' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_apikey' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_lowercase' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_alphanumeric' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_nospaces' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_labels' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_notes_show' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_notes_preview' ) );
		$this->assertFalse( get_option( UtmDotCodes::POST_TYPE . '_shortener' ) );

		$wp_version = $wp_version_actual;
	}
}
