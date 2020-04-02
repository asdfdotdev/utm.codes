<?php
/**
 * Class TestUtmDotCodesShortenRebrandly
 *
 * @package UtmDotCodes
 */

/**
 * Unit tests
 */
class TestUtmDotCodesShortenRebrandly extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Confirm our plugin knows we're testing
	 */
	function test_is_test() {
		$plugin = new UtmDotCodes();

		$this->assertTrue( $plugin->is_test() );
	}

	/**
	 * Confirm WordPress and PHP versions meet minimum requirements and plugin is active
	 *
	 * @depends test_is_test
	 */
	function test_version_numbers_active() {
		$is_valid_wp = version_compare( get_bloginfo( 'version' ), UTMDC_MINIMUM_WP_VERSION, '>' );
		$this->assertTrue( $is_valid_wp );

		$is_valid_php = version_compare( phpversion(), UTMDC_MINIMUM_PHP_VERSION, '>' );
		$this->assertTrue( $is_valid_php );

		$this->assertTrue( is_plugin_active( 'utm-dot-codes/utm-dot-codes.php' ) );
	}

	/**
	 * Test valid request.
	 *
	 * @depends test_is_test
	 */
	function test_rebrandly_request() {
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-rebrandly.php';

		$shortener = new \UtmDotCodes\Rebrandly(
			getenv( 'UTMDC_REBRANDLY_API' )
		);

		$shortener->shorten(
			[ 'utmdclink_url' => 'https://www.' . uniqid() . '.test' ],
			'?test=1234'
		);

		$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
		$this->assertEquals( null, $shortener->get_error() );
		$this->assertTrue( strpos( $shortener->get_response(), 'https://rebrand.ly/' ) !== false );
	}

	/**
	 * Test invalid response: no api key.
	 *
	 * @depends test_is_test
	 */
	function test_rebrandly_request_no_api_key() {
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-rebrandly.php';

		$shortener = new \UtmDotCodes\Rebrandly(
			'this_wont_work'
		);

		$shortener->shorten(
			[ 'utmdclink_url' => 'https://www.' . uniqid() . '.test' ],
			'?test=1234'
		);

		$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
		$this->assertEquals( 401, $shortener->get_error() );
		$this->assertTrue( strpos( $shortener->get_response(), 'https://rebrand.ly/' ) === false );
	}

	/**
	 * Test invalid response: no link.
	 *
	 * @depends test_is_test
	 */
	function test_rebrandly_request_no_link() {
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-rebrandly.php';

		$shortener = new \UtmDotCodes\Rebrandly(
			getenv( 'UTMDC_REBRANDLY_API' )
		);

		$shortener->shorten(
			[ 'utmdclink_url' => '' ],
			''
		);

		$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
		$this->assertEquals( 4031, $shortener->get_error() );
		$this->assertTrue( strpos( $shortener->get_response(), 'https://rebrand.ly/' ) === false );
	}

	/**
	 * Test rebrandly domains update checkbox reset
	 *
	 * @depends test_is_test
	 */
	function test_rebrandly_domains_update_checkbox() {
		$plugin = new UtmDotCodes();
		$value = $plugin->pre_rebrandly_domains_update('new value', 'old value', UtmDotCodes::POST_TYPE . '_rebrandly_domains_update');

		$this->assertEquals( '', $value );
	}

	/**
	 * Test rebrandly domain retrieval
	 *
	 * @depends test_is_test
	 */
	function test_rebrandly_domains_update_options() {
		$plugin = new UtmDotCodes();
		update_option( UtmDotCodes::POST_TYPE . '_shortener', 'rebrandly' );
		update_option( UtmDotCodes::POST_TYPE . '_apikey', getenv( 'UTMDC_REBRANDLY_API' ) );

		$value = $plugin->pre_rebrandly_domains_update('on', '', UtmDotCodes::POST_TYPE . '_rebrandly_domains_update');
		$domains = json_decode( get_option( UtmDotCodes::POST_TYPE . '_rebrandly_domains' ) );

		$this->assertGreaterThan( 0, count($domains) );
		$this->assertEquals( '', $value );
	}
}
