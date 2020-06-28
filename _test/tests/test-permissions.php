<?php
/**
 * Class TestUtmDotCodesPermissions
 *
 * @package UtmDotCodes
 */

/**
 * Integration tests, these should be run after Unit tests
 */
class TestUtmDotCodesPermissions extends WP_UnitTestCase
{

	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Confirm our plugin knows we're testing
	 */
	function test_is_test()
	{
		$plugin = new UtmDotCodes();

		$this->assertTrue($plugin->is_test());
	}

	/**
	 * Confirm the user can edit their own links.
	 *
	 * @depends test_is_test
	 */
	function test_admin_edit_own_post()
	{
		$roles = [
			['role' => 'administrator', 'should_work' => true],
			['role' => 'editor', 'should_work' => true],
			['role' => 'author', 'should_work' => true],
			['role' => 'contributor', 'should_work' => false],
			['role' => 'subscriber', 'should_work' => false]
		];

		foreach($roles as $role) {
			$wp_die_message = '';

			try {
				wp_set_current_user( $this->factory->user->create( array( 'role' => $role['role'] ) ) );
				$post = $this->factory->post->create_and_get( [ 'post_type' => UtmDotCodes::POST_TYPE ] );

				$test_data = [
					'utm_source'   => wp_generate_password( 15, false ),
					'utm_medium'   => 'utm.codes',
					'utm_campaign' => md5( rand( 42, 4910984 ) ),
					'utm_term'     => wp_generate_password( 15, false ),
					'utm_content'  => md5( wp_generate_password( 30, true, true ) ),
				];

				$query_string = '?' . http_build_query( $test_data ) . '&utm_gen=utmdc';

				array_map(
					function( $key, $value ) use ( &$test_data ) {
						$test_data[ str_replace( 'utm', UtmDotCodes::POST_TYPE, $key ) ] = $value;
						unset( $test_data[ $key ] );
					},
					array_keys( $test_data ),
					$test_data
				);

				$_POST = array_merge(
					$test_data,
					[
						'post_ID'                            => $post->ID,
						UtmDotCodes::POST_TYPE . '_url'      => 'https://www.' . uniqid() . '.test',
						UtmDotCodes::POST_TYPE . '_shorturl' => '',
						UtmDotCodes::POST_TYPE . '_shorten'  => '',
						UtmDotCodes::POST_TYPE . '_batch'    => '',
						UtmDotCodes::POST_TYPE . '_notes'    => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.',
					]
				);

				$test_id = edit_post();
				$test_post = get_post( $test_id );
			} catch ( WPDieException $e ) {
				$wp_die_message = $e->getMessage();
			}

			if (true === $role['should_work']) {
				$this->assertEquals(
					'',
					$wp_die_message
				);
				$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
				$this->assertEquals( $test_post->post_content, $_POST[ UtmDotCodes::POST_TYPE . '_url' ] . $query_string );
				$this->assertEquals(
					filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
					$test_post->post_content
				);
				$this->assertEquals( $test_post->post_title, $_POST[ UtmDotCodes::POST_TYPE . '_url' ] );
				$this->assertEquals( $test_post->post_status, 'publish' );

				$test_meta = get_post_meta( $test_post->ID );
				$this->assertEquals( $test_meta['utmdclink_url'][0], $_POST[ UtmDotCodes::POST_TYPE . '_url' ] );
				$this->assertEquals( $test_meta['utmdclink_source'][0], $test_data[ UtmDotCodes::POST_TYPE . '_source' ] );
				$this->assertEquals( $test_meta['utmdclink_medium'][0], $test_data[ UtmDotCodes::POST_TYPE . '_medium' ] );
				$this->assertEquals( $test_meta['utmdclink_campaign'][0], $test_data[ UtmDotCodes::POST_TYPE . '_campaign' ] );
				$this->assertEquals( $test_meta['utmdclink_term'][0], $test_data[ UtmDotCodes::POST_TYPE . '_term' ] );
				$this->assertEquals( $test_meta['utmdclink_content'][0], $test_data[ UtmDotCodes::POST_TYPE . '_content' ] );
				$this->assertFalse( isset( $test_meta['utmdclink_shorturl'][0] ) );
				$this->assertEquals( $test_meta['utmdclink_notes'][0], 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.' );
			} else {
				$this->assertEquals(
					'Sorry, you are not allowed to edit this post.',
					$wp_die_message
				);
			}
		}
	}

	/**
	 * Confirm the user can edit others links.
	 *
	 * @depends test_is_test
	 */
	function test_admin_edit_others_post()
	{
		$roles = [
			['role' => 'administrator', 'should_work' => true],
			['role' => 'editor', 'should_work' => true],
			['role' => 'author', 'should_work' => false],
			['role' => 'contributor', 'should_work' => false],
			['role' => 'subscriber', 'should_work' => false]
		];

		foreach($roles as $role) {
			$wp_die_message = '';

			try {
				wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
				$post = $this->factory->post->create_and_get( [ 'post_type' => UtmDotCodes::POST_TYPE ] );
				wp_set_current_user( $this->factory->user->create( array( 'role' => $role['role'] ) ) );

				$test_data = [
					'utm_source'   => wp_generate_password( 15, false ),
					'utm_medium'   => 'utm.codes',
					'utm_campaign' => md5( rand( 42, 4910984 ) ),
					'utm_term'     => wp_generate_password( 15, false ),
					'utm_content'  => md5( wp_generate_password( 30, true, true ) ),
				];

				$query_string = '?' . http_build_query( $test_data ) . '&utm_gen=utmdc';

				array_map(
					function( $key, $value ) use ( &$test_data ) {
						$test_data[ str_replace( 'utm', UtmDotCodes::POST_TYPE, $key ) ] = $value;
						unset( $test_data[ $key ] );
					},
					array_keys( $test_data ),
					$test_data
				);

				$_POST = array_merge(
					$test_data,
					[
						'post_ID'                            => $post->ID,
						UtmDotCodes::POST_TYPE . '_url'      => 'https://www.' . uniqid() . '.test',
						UtmDotCodes::POST_TYPE . '_shorturl' => '',
						UtmDotCodes::POST_TYPE . '_shorten'  => '',
						UtmDotCodes::POST_TYPE . '_batch'    => '',
						UtmDotCodes::POST_TYPE . '_notes'    => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.',
					]
				);

				$test_id = edit_post();
				$test_post = get_post( $test_id );
			} catch ( WPDieException $e ) {
				$wp_die_message = $e->getMessage();
			}

			if (true === $role['should_work']) {
				$this->assertEquals(
					'',
					$wp_die_message
				);
				$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
				$this->assertEquals( $test_post->post_content, $_POST[ UtmDotCodes::POST_TYPE . '_url' ] . $query_string );
				$this->assertEquals(
					filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
					$test_post->post_content
				);
				$this->assertEquals( $test_post->post_title, $_POST[ UtmDotCodes::POST_TYPE . '_url' ] );
				$this->assertEquals( $test_post->post_status, 'publish' );

				$test_meta = get_post_meta( $test_post->ID );
				$this->assertEquals( $test_meta['utmdclink_url'][0], $_POST[ UtmDotCodes::POST_TYPE . '_url' ] );
				$this->assertEquals( $test_meta['utmdclink_source'][0], $test_data[ UtmDotCodes::POST_TYPE . '_source' ] );
				$this->assertEquals( $test_meta['utmdclink_medium'][0], $test_data[ UtmDotCodes::POST_TYPE . '_medium' ] );
				$this->assertEquals( $test_meta['utmdclink_campaign'][0], $test_data[ UtmDotCodes::POST_TYPE . '_campaign' ] );
				$this->assertEquals( $test_meta['utmdclink_term'][0], $test_data[ UtmDotCodes::POST_TYPE . '_term' ] );
				$this->assertEquals( $test_meta['utmdclink_content'][0], $test_data[ UtmDotCodes::POST_TYPE . '_content' ] );
				$this->assertFalse( isset( $test_meta['utmdclink_shorturl'][0] ) );
				$this->assertEquals( $test_meta['utmdclink_notes'][0], 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.' );
			} else {
				$this->assertEquals(
					'Sorry, you are not allowed to edit this post.',
					$wp_die_message
				);
			}
		}
	}
}
