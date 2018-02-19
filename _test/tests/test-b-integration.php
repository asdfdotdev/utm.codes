<?php
/**
 * Class TestUtmDotCodesIntegration
 *
 * @package utm.codes
 */

/**
 * Integration tests, these should be run after Unit tests
 */
class TestUtmDotCodesIntegration extends WP_UnitTestCase
{

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_post_create() {
		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );

		$test_data = [
			'utm_source' => rand(25, 173929),
			'utm_medium' => 'utm.codes',
			'utm_campaign' => md5( rand(42, 4910984) ),
			'utm_term' => wp_generate_password( 15, false ),
			'utm_content' => md5( wp_generate_password( 30, true, true ) ),
		];

		$query_string = '?' . http_build_query($test_data) . '&utm_gen=utmdc';

		array_map(function($key, $value) use(&$test_data) {
			$test_data[str_replace('utm', UtmDotCodes::POST_TYPE, $key)] = $value;
			unset($test_data[$key]);
		}, array_keys($test_data), $test_data );

		$_POST = array_merge(
			$test_data,
			[
				'post_ID' => $post->ID,
				UtmDotCodes::POST_TYPE . '_url' => 'https://www.' . uniqid() . '.test',
				UtmDotCodes::POST_TYPE . '_shorturl' => '',
				UtmDotCodes::POST_TYPE . '_shorten' => '',
				UtmDotCodes::POST_TYPE . '_batch' => '',
			]
		);

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$test_id = edit_post();

		$test_post = get_post($test_id);
		$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
		$this->assertEquals( $test_post->post_content, $_POST[UtmDotCodes::POST_TYPE . '_url'] . $query_string);
		$this->assertEquals(
			filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
			$test_post->post_content
		);
		$this->assertEquals( $test_post->post_title, $_POST[UtmDotCodes::POST_TYPE . '_url'] );
		$this->assertEquals( $test_post->post_status, 'publish' );

		$test_meta = get_post_meta( $test_post->ID );
		$this->assertEquals( $test_meta['utmdclink_url'][0], $_POST[UtmDotCodes::POST_TYPE . '_url'] );
		$this->assertEquals( $test_meta['utmdclink_source'][0], $test_data[UtmDotCodes::POST_TYPE . '_source'] );
		$this->assertEquals( $test_meta['utmdclink_medium'][0], $test_data[UtmDotCodes::POST_TYPE . '_medium'] );
		$this->assertEquals( $test_meta['utmdclink_campaign'][0], $test_data[UtmDotCodes::POST_TYPE . '_campaign'] );
		$this->assertEquals( $test_meta['utmdclink_term'][0], $test_data[UtmDotCodes::POST_TYPE . '_term'] );
		$this->assertEquals( $test_meta['utmdclink_content'][0], $test_data[UtmDotCodes::POST_TYPE . '_content'] );
		$this->assertFalse( isset($test_meta['utmdclink_shorturl'][0]) );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_post_create_extra_params() {
		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );

		$test_data = [
			'utm_source' => rand(25, 173929),
			'utm_medium' => 'utm.codes',
			'utm_campaign' => md5( rand(42, 4910984) ),
			'utm_term' => wp_generate_password( 15, false ),
			'utm_content' => md5( wp_generate_password( 30, true, true ) ),
		];

		$query_string = '&' . http_build_query($test_data) . '&utm_gen=utmdc';

		array_map(function($key, $value) use(&$test_data) {
			$test_data[str_replace('utm', UtmDotCodes::POST_TYPE, $key)] = $value;
			unset($test_data[$key]);
		}, array_keys($test_data), $test_data );

		$_POST = array_merge(
			$test_data,
			[
				'post_ID' => $post->ID,
				UtmDotCodes::POST_TYPE . '_url' => 'https://www.' . uniqid() . '.test?bonus=param',
				UtmDotCodes::POST_TYPE . '_shorturl' => '',
				UtmDotCodes::POST_TYPE . '_shorten' => '',
				UtmDotCodes::POST_TYPE . '_batch' => '',
			]
		);

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$test_id = edit_post();

		$test_post = get_post($test_id);
		$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
		$this->assertEquals( $test_post->post_content, $_POST[UtmDotCodes::POST_TYPE . '_url'] . $query_string);
		$this->assertEquals(
			filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
			$test_post->post_content
		);
		$this->assertEquals( $test_post->post_title, $_POST[UtmDotCodes::POST_TYPE . '_url'] );
		$this->assertEquals( $test_post->post_status, 'publish' );

		$test_meta = get_post_meta( $test_post->ID );
		$this->assertEquals( $test_meta['utmdclink_url'][0], $_POST[UtmDotCodes::POST_TYPE . '_url'] );
		$this->assertEquals( $test_meta['utmdclink_source'][0], $test_data[UtmDotCodes::POST_TYPE . '_source'] );
		$this->assertEquals( $test_meta['utmdclink_medium'][0], $test_data[UtmDotCodes::POST_TYPE . '_medium'] );
		$this->assertEquals( $test_meta['utmdclink_campaign'][0], $test_data[UtmDotCodes::POST_TYPE . '_campaign'] );
		$this->assertEquals( $test_meta['utmdclink_term'][0], $test_data[UtmDotCodes::POST_TYPE . '_term'] );
		$this->assertEquals( $test_meta['utmdclink_content'][0], $test_data[UtmDotCodes::POST_TYPE . '_content'] );
		$this->assertFalse( isset($test_meta['utmdclink_shorturl'][0]) );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_post_create_shorten() {
		update_option( UtmDotCodes::POST_TYPE . '_apikey', getenv('UTMDC_GOOGLE_API') );

		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );

		$test_data = [
			'utm_source' => rand(25, 173929),
			'utm_medium' => 'utm.codes',
			'utm_campaign' => md5( rand(42, 4910984) ),
			'utm_term' => wp_generate_password( 15, false ),
			'utm_content' => md5( wp_generate_password( 30, true, true ) ),
		];

		$query_string = '?' . http_build_query($test_data) . '&utm_gen=utmdc';

		array_map(function($key, $value) use(&$test_data) {
			$test_data[str_replace('utm', UtmDotCodes::POST_TYPE, $key)] = $value;
			unset($test_data[$key]);
		}, array_keys($test_data), $test_data );

		$_POST = array_merge(
			$test_data,
			[
				'post_ID' => $post->ID,
				UtmDotCodes::POST_TYPE . '_url' => 'https://www.' . uniqid() . '.test',
				UtmDotCodes::POST_TYPE . '_shorturl' => '',
				UtmDotCodes::POST_TYPE . '_shorten' => 'on',
				UtmDotCodes::POST_TYPE . '_batch' => '',
			]
		);

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$test_id = edit_post();

		$test_post = get_post($test_id);
		$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
		$this->assertEquals( $test_post->post_content, $_POST[UtmDotCodes::POST_TYPE . '_url'] . $query_string);
		$this->assertEquals(
			filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
			$test_post->post_content
		);
		$this->assertEquals( $test_post->post_title, $_POST[UtmDotCodes::POST_TYPE . '_url'] );
		$this->assertEquals( $test_post->post_status, 'publish' );

		$test_meta = get_post_meta( $test_post->ID );
		$this->assertEquals( $test_meta['utmdclink_url'][0], $_POST[UtmDotCodes::POST_TYPE . '_url'] );
		$this->assertEquals( $test_meta['utmdclink_source'][0], $test_data[UtmDotCodes::POST_TYPE . '_source'] );
		$this->assertEquals( $test_meta['utmdclink_medium'][0], $test_data[UtmDotCodes::POST_TYPE . '_medium'] );
		$this->assertEquals( $test_meta['utmdclink_campaign'][0], $test_data[UtmDotCodes::POST_TYPE . '_campaign'] );
		$this->assertEquals( $test_meta['utmdclink_term'][0], $test_data[UtmDotCodes::POST_TYPE . '_term'] );
		$this->assertEquals( $test_meta['utmdclink_content'][0], $test_data[UtmDotCodes::POST_TYPE . '_content'] );
		$this->assertTrue( strpos($test_meta['utmdclink_shorturl'][0], 'https://goo.gl/') !== false );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_post_create_batch() {
		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );

		$test_data = [
			'utm_source' => 'this should be overwritten',
			'utm_medium' => 'so should this',
			'utm_campaign' => md5( rand(42, 4910984) ),
			'utm_term' => wp_generate_password( 15, false ),
			'utm_content' => md5( wp_generate_password( 30, true, true ) ),
		];

		$test_networks = ['a', 'b', 'c', 'd', 'e'];
		$test_networks = array_map( function($value){
			return wp_generate_password( 15, false );
		}, $test_networks );
		$test_networks = array_fill_keys( $test_networks, 'on' );
		update_option( UtmDotCodes::POST_TYPE . '_social', $test_networks );
		$test_networks = array_keys($test_networks);

		array_map(function($key, $value) use(&$test_data) {
			$test_data[str_replace('utm', UtmDotCodes::POST_TYPE, $key)] = $value;
			unset($test_data[$key]);
		}, array_keys($test_data), $test_data );

		$_POST = array_merge(
			$test_data,
			[
				'post_ID' => $post->ID,
				UtmDotCodes::POST_TYPE . '_url' => 'https://www.' . uniqid() . '.test',
				UtmDotCodes::POST_TYPE . '_shorturl' => '',
				UtmDotCodes::POST_TYPE . '_shorten' => '',
				UtmDotCodes::POST_TYPE . '_batch' => 'on',
			]
		);

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		edit_post();

		$test_posts = get_posts(
			[
				'posts_per_page'	=> 100,
				'offset'			=> 0,
				'meta_key'			=> UtmDotCodes::POST_TYPE . '_url',
				'meta_value'		=> $_POST[UtmDotCodes::POST_TYPE . '_url'],
				'post_type'			=> UtmDotCodes::POST_TYPE,
				'author'			=> $user_id,
				'post_status'		=> 'publish',
				'suppress_filters'	=> true,
				'orderby'			=> 'date',
				'order'				=> 'DESC'
			]
		);

		$x = 0;
		array_map( function($test_post) use($test_posts, $test_networks, $x) {
			$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
			$this->assertEquals(
				filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
				$test_post->post_content
			);
			$this->assertEquals( $test_post->post_title, $_POST[UtmDotCodes::POST_TYPE . '_url'] );
			$this->assertEquals( $test_post->post_status, 'publish' );

			$test_meta = get_post_meta( $test_post->ID );
			$this->assertEquals( $test_meta['utmdclink_url'][$x], $_POST[UtmDotCodes::POST_TYPE . '_url'] );
			$this->assertTrue( in_array($test_meta['utmdclink_source'][$x], $test_networks) );
			$this->assertEquals( $test_meta['utmdclink_medium'][$x], 'social' );
			$this->assertEquals( $test_meta['utmdclink_campaign'][$x], $_POST[UtmDotCodes::POST_TYPE . '_campaign'] );
			$this->assertEquals( $test_meta['utmdclink_term'][$x], $_POST[UtmDotCodes::POST_TYPE . '_term'] );
			$this->assertEquals( $test_meta['utmdclink_content'][$x], $_POST[UtmDotCodes::POST_TYPE . '_content'] );
			$this->assertFalse( isset($test_meta['utmdclink_shorturl'][$x]) );
			$this->assertEquals(
				$test_post->post_content,
				sprintf(
					'%s?utm_source=%s&utm_medium=%s&utm_campaign=%s&utm_term=%s&utm_content=%s&utm_gen=utmdc',
					$test_meta['utmdclink_url'][$x],
					$test_meta['utmdclink_source'][$x],
					'social',
					$test_meta['utmdclink_campaign'][$x],
					$test_meta['utmdclink_term'][$x],
					$test_meta['utmdclink_content'][$x]
				)
			);

			++$x;
		}, $test_posts );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_post_create_batch_shorten() {
		update_option( UtmDotCodes::POST_TYPE . '_apikey', getenv('UTMDC_GOOGLE_API') );

		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );

		$test_data = [
			'utm_source' => 'this should be overwritten',
			'utm_medium' => 'so should this',
			'utm_campaign' => md5( rand(42, 4910984) ),
			'utm_term' => wp_generate_password( 15, false ),
			'utm_content' => md5( wp_generate_password( 30, true, true ) ),
		];

		$test_networks = ['a', 'b', 'c', 'd', 'e'];
		$test_networks = array_map( function($value){
			return wp_generate_password( 15, false );
		}, $test_networks );
		$test_networks = array_fill_keys( $test_networks, 'on' );
		update_option( UtmDotCodes::POST_TYPE . '_social', $test_networks );
		$test_networks = array_keys($test_networks);

		array_map(function($key, $value) use(&$test_data) {
			$test_data[str_replace('utm', UtmDotCodes::POST_TYPE, $key)] = $value;
			unset($test_data[$key]);
		}, array_keys($test_data), $test_data );

		$_POST = array_merge(
			$test_data,
			[
				'post_ID' => $post->ID,
				UtmDotCodes::POST_TYPE . '_url' => 'https://www.' . uniqid() . '.test',
				UtmDotCodes::POST_TYPE . '_shorturl' => '',
				UtmDotCodes::POST_TYPE . '_shorten' => 'on',
				UtmDotCodes::POST_TYPE . '_batch' => 'on',
			]
		);

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		edit_post();

		$test_posts = get_posts(
			[
				'posts_per_page'	=> 100,
				'offset'			=> 0,
				'meta_key'			=> UtmDotCodes::POST_TYPE . '_url',
				'meta_value'		=> $_POST[UtmDotCodes::POST_TYPE . '_url'],
				'post_type'			=> UtmDotCodes::POST_TYPE,
				'author'			=> $user_id,
				'post_status'		=> 'publish',
				'suppress_filters'	=> true,
				'orderby'			=> 'date',
				'order'				=> 'DESC'
			]
		);

		$x = 0;
		array_map( function($test_post) use($test_posts, $test_networks, $x) {
			$this->assertEquals( $test_post->post_type, UtmDotCodes::POST_TYPE );
			$this->assertEquals(
				filter_var( $test_post->post_content, FILTER_VALIDATE_URL ),
				$test_post->post_content
			);
			$this->assertEquals( $test_post->post_title, $_POST[UtmDotCodes::POST_TYPE . '_url'] );
			$this->assertEquals( $test_post->post_status, 'publish' );

			$test_meta = get_post_meta( $test_post->ID );
			$this->assertEquals( $test_meta['utmdclink_url'][$x], $_POST[UtmDotCodes::POST_TYPE . '_url'] );
			$this->assertTrue( in_array($test_meta['utmdclink_source'][$x], $test_networks) );
			$this->assertEquals( $test_meta['utmdclink_medium'][$x], 'social' );
			$this->assertEquals( $test_meta['utmdclink_campaign'][$x], $_POST[UtmDotCodes::POST_TYPE . '_campaign'] );
			$this->assertEquals( $test_meta['utmdclink_term'][$x], $_POST[UtmDotCodes::POST_TYPE . '_term'] );
			$this->assertEquals( $test_meta['utmdclink_content'][$x], $_POST[UtmDotCodes::POST_TYPE . '_content'] );
			$this->assertTrue( strpos($test_meta['utmdclink_shorturl'][$x], 'https://goo.gl/') !== false );
			$this->assertEquals(
				$test_post->post_content,
				sprintf(
					'%s?utm_source=%s&utm_medium=%s&utm_campaign=%s&utm_term=%s&utm_content=%s&utm_gen=utmdc',
					$test_meta['utmdclink_url'][$x],
					$test_meta['utmdclink_source'][$x],
					'social',
					$test_meta['utmdclink_campaign'][$x],
					$test_meta['utmdclink_term'][$x],
					$test_meta['utmdclink_content'][$x]
				)
			);

			++$x;
		}, $test_posts );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_editor_meta_box() {
		global $wp_meta_boxes;

		$this->assertNull( $wp_meta_boxes );

		$plugin = new UtmDotCodes();
		$plugin->add_meta_box();

		$this->assertNotNull( $wp_meta_boxes );
		$this->assertTrue( is_array($wp_meta_boxes[UtmDotCodes::POST_TYPE]) );
		$this->assertTrue( isset($wp_meta_boxes[UtmDotCodes::POST_TYPE]['normal']['high']['utmdc_link_meta_box']) );
		$this->assertEquals(
			$wp_meta_boxes[UtmDotCodes::POST_TYPE]['normal']['high']['utmdc_link_meta_box']['id'],
			'utmdc_link_meta_box'
		);
		$this->assertEquals(
			$wp_meta_boxes[UtmDotCodes::POST_TYPE]['normal']['high']['utmdc_link_meta_box']['title'],
			'utm.codes Editor'
		);
		$this->assertEquals(
			$wp_meta_boxes[UtmDotCodes::POST_TYPE]['normal']['high']['utmdc_link_meta_box']['callback'],
			[$plugin, 'meta_box_contents']
		);
		$this->assertNull( $wp_meta_boxes[UtmDotCodes::POST_TYPE]['normal']['high']['utmdc_link_meta_box']['args'] );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_editor_meta_box_contents_empty() {
		global $post;

		$plugin = new UtmDotCodes();
		$plugin->create_post_type();
		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );
		$form_markup = $plugin->meta_box_contents();

		$this->assertTrue(
			strpos(
				$form_markup[2],
				'<input type="url" name="' . UtmDotCodes::POST_TYPE . '_url" id="' . UtmDotCodes::POST_TYPE . '_url" required="required" value="">'
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[3],
				'<input type="text" name="' . UtmDotCodes::POST_TYPE . '_source" id="' . UtmDotCodes::POST_TYPE . '_source" required="required" value="">'
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[4],
				'<input type="text" name="' . UtmDotCodes::POST_TYPE . '_medium" id="' . UtmDotCodes::POST_TYPE . '_medium" value="">'
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[5],
				'<input type="text" name="' . UtmDotCodes::POST_TYPE . '_campaign" id="' . UtmDotCodes::POST_TYPE . '_campaign" value="">'
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[6],
				'<input type="text" name="' . UtmDotCodes::POST_TYPE . '_term" id="' . UtmDotCodes::POST_TYPE . '_term" value="">'
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[7],
				'<input type="text" name="' . UtmDotCodes::POST_TYPE . '_content" id="' . UtmDotCodes::POST_TYPE . '_content" value="">'
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[8],
				'<input type="url" name="' . UtmDotCodes::POST_TYPE . '_shorturl" id="' . UtmDotCodes::POST_TYPE . '_shorturl" value="">'
			) !== false
		);
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_editor_meta_box_contents_editing() {
		global $post;

		update_option( UtmDotCodes::POST_TYPE . '_apikey', getenv('UTMDC_GOOGLE_API') );

		$plugin = new UtmDotCodes();
		$plugin->create_post_type();
		$post = $this->factory->post->create_and_get( ['post_type' => UtmDotCodes::POST_TYPE] );

		$test_data = [
			'utm_source' => rand(25, 173929),
			'utm_medium' => 'utm.codes',
			'utm_campaign' => md5( rand(42, 4910984) ),
			'utm_term' => wp_generate_password( 15, false ),
			'utm_content' => md5( wp_generate_password( 30, true, true ) ),
		];

		$query_string = '?' . http_build_query($test_data) . '&utm_gen=utmdc';

		array_map(function($key, $value) use(&$test_data) {
			$test_data[str_replace('utm', UtmDotCodes::POST_TYPE, $key)] = $value;
			unset($test_data[$key]);
		}, array_keys($test_data), $test_data );

		$_POST = array_merge(
			$test_data,
			[
				'post_ID' => $post->ID,
				UtmDotCodes::POST_TYPE . '_url' => 'https://www.' . uniqid() . '.test',
				UtmDotCodes::POST_TYPE . '_shorturl' => '',
				UtmDotCodes::POST_TYPE . '_shorten' => 'on',
				UtmDotCodes::POST_TYPE . '_batch' => '',
			]
		);

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$test_id = edit_post();
		$post = get_post($test_id);
		$form_markup = $plugin->meta_box_contents();

		$this->assertTrue(
			strpos(
				$form_markup[3],
				sprintf(
					'<input type="url" name="%1$s_url" id="%1$s_url" required="required" value="%2$s">',
					UtmDotCodes::POST_TYPE,
					$_POST[UtmDotCodes::POST_TYPE . '_url']
				)
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[4],
				sprintf(
					'<input type="text" name="%1$s_source" id="%1$s_source" required="required" value="%2$s">',
					UtmDotCodes::POST_TYPE,
					$_POST[UtmDotCodes::POST_TYPE . '_source']
				)
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[5],
				sprintf(
					'<input type="text" name="%1$s_medium" id="%1$s_medium" value="%2$s">',
					UtmDotCodes::POST_TYPE,
					$_POST[UtmDotCodes::POST_TYPE . '_medium']
				)
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[6],
				sprintf(
					'<input type="text" name="%1$s_campaign" id="%1$s_campaign" value="%2$s">',
					UtmDotCodes::POST_TYPE,
					$_POST[UtmDotCodes::POST_TYPE . '_campaign']
				)
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[7],
				sprintf(
					'<input type="text" name="%1$s_term" id="%1$s_term" value="%2$s">',
					UtmDotCodes::POST_TYPE,
					$_POST[UtmDotCodes::POST_TYPE . '_term']
				)
			) !== false
		);
		$this->assertTrue(
			strpos(
				$form_markup[8],
				sprintf(
					'<input type="text" name="%1$s_content" id="%1$s_content" value="%2$s">',
					UtmDotCodes::POST_TYPE,
					$_POST[UtmDotCodes::POST_TYPE . '_content']
				)
			) !== false
		);
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_settings_page() {
		$start_page = get_current_screen();
		$this->assertNull( $start_page );

		set_current_screen( 'settings_page_utm-dot-codes' );
		$settings_page = get_current_screen();
		$this->assertEquals( $settings_page->base, 'settings_page_utm-dot-codes' );
		$this->assertEquals( $settings_page->id, 'settings_page_utm-dot-codes' );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_settings_register() {
		global $wp_registered_settings;

		$plugin = new UtmDotCodes();
		$plugin->register_plugin_settings();

		$this->assertTrue( is_array($wp_registered_settings['utmdclink_social']) );
		$this->assertEquals( $wp_registered_settings['utmdclink_social']['type'], 'string' );
		$this->assertEquals( $wp_registered_settings['utmdclink_social']['group'], UtmDotCodes::SETTINGS_GROUP );

		$this->assertTrue( is_array($wp_registered_settings['utmdclink_apikey']) );
		$this->assertEquals( $wp_registered_settings['utmdclink_apikey']['type'], 'string' );
		$this->assertEquals( $wp_registered_settings['utmdclink_apikey']['group'], UtmDotCodes::SETTINGS_GROUP );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_post_list_columns() {
		$columns = _get_list_table('WP_Posts_List_Table', ['screen' => 'edit-' . UtmDotCodes::POST_TYPE])->get_column_info();
		$this->assertEquals( $columns[0]['cb'], '<input type="checkbox" />');
		$this->assertEquals( $columns[0]['utmdc_link'], 'Link');
		$this->assertEquals( $columns[0]['utmdc_source'], 'Source');
		$this->assertEquals( $columns[0]['utmdc_medium'], 'Medium');
		$this->assertEquals( $columns[0]['utmdc_campaign'], 'Campaign');
		$this->assertEquals( $columns[0]['utmdc_term'], 'Term');
		$this->assertEquals( $columns[0]['utmdc_content'], 'Content');
		$this->assertEquals( $columns[0]['copy_utmdc_link'], 'Copy Links');
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_add_static_resources() {
		global $wp_scripts, $wp_styles;

		$plugin = new UtmDotCodes();
		$plugin->add_css();
		$plugin->add_js();

		$this->assertTrue( array_key_exists('utm-dot-codes', $wp_scripts->registered) );
		$this->assertEquals( $wp_scripts->registered['utm-dot-codes']->deps[0], 'jquery' );
		$this->assertEquals( $wp_scripts->registered['utm-dot-codes']->src, UTMDC_PLUGIN_URL . 'js/utmdotcodes.min.js' );
		$this->assertTrue( in_array('utm-dot-codes', $wp_scripts->queue) );

		$this->assertTrue( array_key_exists('font-awesome', $wp_styles->registered) );
		$this->assertEquals( $wp_styles->registered['font-awesome']->src, 'https://use.fontawesome.com/releases/v5.0.4/css/all.css' );
		$this->assertTrue( in_array('font-awesome', $wp_styles->queue) );

		$this->assertTrue( array_key_exists('utm-dot-codes', $wp_styles->registered) );
		$this->assertEquals( $wp_styles->registered['utm-dot-codes']->deps[0], 'font-awesome' );
		$this->assertEquals( $wp_styles->registered['utm-dot-codes']->src, UTMDC_PLUGIN_URL . 'css/utmdotcodes.css' );
		$this->assertTrue( in_array('utm-dot-codes', $wp_styles->queue) );
	}

	/**
	 * @depends TestUtmDotCodesUnit::test_version_numbers_active
	 */
	function test_add_glance() {
		$glance_markup = apply_filters( 'dashboard_glance_items', array() );

		$this->assertTrue(
			in_array(
				'<a href="javascript:;" class="utmdclink-count">0 Marketing Links</a>',
				$glance_markup
			)
		);
	}
}
