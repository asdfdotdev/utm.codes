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

	function should_do_test() {
		return (getenv( 'UTMDC_BITLY_DO_TESTS' ) === 'true');
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
		if ($this->should_do_test()) {
			$is_valid_wp = version_compare( get_bloginfo( 'version' ), UTMDC_MINIMUM_WP_VERSION, '>' );
			$this->assertTrue( $is_valid_wp );

			$is_valid_php = version_compare( phpversion(), UTMDC_MINIMUM_PHP_VERSION, '>' );
			$this->assertTrue( $is_valid_php );

			$this->assertTrue( is_plugin_active( 'utm-dot-codes/utm-dot-codes.php' ) );
		} else {
			if (method_exists( $this, 'expectNotToPerformAssertions' )) {
				$this->expectNotToPerformAssertions();
			} else {
				$this->assertTrue( true );
			}
		}
	}

	/**
	 * Test valid request.
	 *
	 * @depends test_is_test
	 */
	function test_bitly_request() {
		if ($this->should_do_test()) {
			require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
			require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-bitly.php';

			$plugin = new UtmDotCodes();

			$shortener = new \UtmDotCodes\Bitly(
				getenv( 'UTMDC_BITLY_API' )
			);

			$shortener->shorten(
				[ 'utmdclink_url' => 'https://www.' . uniqid() . '.test' ],
				'?test=1234'
			);

			$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
			$this->assertEquals( null, $shortener->get_error() );
			$this->assertTrue( strpos( $plugin->null_string_check( $shortener->get_response() ), 'https://bit.ly/' ) !== false );
		} else {
			if (method_exists( $this, 'expectNotToPerformAssertions' )) {
				$this->expectNotToPerformAssertions();
			} else {
				$this->assertTrue( true );
			}
		}
	}

	/**
	 * Test invalid response: no api key.
	 *
	 * @depends test_is_test
	 */
	function test_bitly_request_no_api_key() {
		if ($this->should_do_test()) {
			require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
			require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-bitly.php';

			$plugin = new UtmDotCodes();

			$shortener = new \UtmDotCodes\Bitly(
				'this_wont_work'
			);

			$shortener->shorten(
				[ 'utmdclink_url' => 'https://www.' . uniqid() . '.test' ],
				'?test=1234'
			);

			$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
			$this->assertEquals( 4030, $shortener->get_error() );
			$this->assertTrue( strpos( $plugin->null_string_check( $shortener->get_response() ), 'http://bit.ly/' ) === false );
		} else {
			if (method_exists( $this, 'expectNotToPerformAssertions' )) {
				$this->expectNotToPerformAssertions();
			} else {
				$this->assertTrue( true );
			}
		}
	}

	/**
	 * Test invalid response: no link.
	 *
	 * @depends test_is_test
	 */
	function test_bitly_request_no_link() {
		if ($this->should_do_test()) {
			require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/interface.php';
			require_once getenv( 'UTMDC_PLUGIN_DIR' ) . '/classes/shorten/class-bitly.php';

			$plugin = new UtmDotCodes();

			$shortener = new \UtmDotCodes\Bitly(
				getenv( 'UTMDC_BITLY_API' )
			);

			$shortener->shorten(
				[ 'utmdclink_url' => '' ],
				''
			);

			$this->assertTrue( $shortener instanceof \UtmDotCodes\Shorten );
			$this->assertEquals( 500, $shortener->get_error() );
			$this->assertTrue( strpos( $plugin->null_string_check( $shortener->get_response() ), 'http://bit.ly/' ) === false );
		} else {
			if (method_exists( $this, 'expectNotToPerformAssertions' )) {
				$this->expectNotToPerformAssertions();
			} else {
				$this->assertTrue( true );
			}
		}
	}

}
