<?php
/**
 * Class TestUtmDotCodesShortenBitly
 *
 * @package UtmDotCodes
 */

/**
 * Unit tests
 */
class TestUtmDotCodesShortenBitly extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Confirm our plugin knows we're testing.
	 */
	function test_is_test() {
		$plugin = new UtmDotCodes();

		$this->assertTrue( $plugin->is_test() );
	}

	/**
	 * Confirm WordPress and PHP versions meet minimum requirements and plugin is active.
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
	function test_bitly_request() {
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-bitly.php';

		$shortener = new \UtmDotCodes\Bitly(
			getenv( 'UTMDC_BITLY_API' )
		);

		$shortener->shorten(
			[ 'utmdclink_url' => 'https://www.' . uniqid() . '.test' ],
			'?test=1234'
		);

		$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
		$this->assertEquals( null, $shortener->get_error() );
		$this->assertTrue( strpos( $shortener->get_response(), 'https://bit.ly/' ) !== false );
	}

	/**
	 * Test invalid response: no api key.
	 *
	 * @depends test_is_test
	 */
	function test_bitly_request_no_api_key() {
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-bitly.php';

		$shortener = new \UtmDotCodes\Bitly(
			'this_wont_work'
		);

		$shortener->shorten(
			[ 'utmdclink_url' => 'https://www.' . uniqid() . '.test' ],
			'?test=1234'
		);

		$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
		$this->assertEquals( 4030, $shortener->get_error() );
		$this->assertTrue( strpos( $shortener->get_response(), 'http://bit.ly/' ) === false );
	}

	/**
	 * Test invalid response: no link.
	 *
	 * @depends test_is_test
	 */
	function test_bitly_request_no_link() {
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
		require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-bitly.php';

		$shortener = new \UtmDotCodes\Bitly(
			getenv( 'UTMDC_BITLY_API' )
		);

		$shortener->shorten(
			[ 'utmdclink_url' => '' ],
			''
		);

		$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
		$this->assertEquals( 500, $shortener->get_error() );
		$this->assertTrue( strpos( $shortener->get_response(), 'http://bit.ly/' ) === false );
	}

}
