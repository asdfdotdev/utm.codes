<?php
/**
 * Class TestUtmDotCodesUnit
 *
 * @package UtmDotCodes
 */

/**
 * Unit tests
 */
class TestUtmDotCodesUnit extends WP_UnitTestCase {

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
	 * @depends test_version_numbers_active
	 */
	function test_post_type() {
		$post_types = get_post_types();
		$this->assertEquals(
			$post_types[ UtmDotCodes::POST_TYPE ],
			UtmDotCodes::POST_TYPE,
			'Failed to create post type'
		);

		$this->assertFalse(
			post_type_supports( UtmDotCodes::POST_TYPE, 'revisions' ),
			'Revisions have not been disabled'
		);

		$post_object = get_post_type_object( UtmDotCodes::POST_TYPE );

		$this->assertEquals( $post_object->name, 'utmdclink' );
		$this->assertEquals( $post_object->label, 'Marketing Links' );
		$this->assertEquals( $post_object->labels->name, 'Marketing Links' );
		$this->assertEquals( $post_object->labels->singular_name, 'Marketing Link' );
		$this->assertEquals( $post_object->labels->add_new, 'Add New Link' );
		$this->assertEquals( $post_object->labels->add_new_item, 'Add New Marketing Link' );
		$this->assertEquals( $post_object->labels->edit_item, 'Edit Marketing Link' );
		$this->assertEquals( $post_object->labels->new_item, 'New Marketing Link' );
		$this->assertEquals( $post_object->labels->view_item, 'View Marketing Link' );
		$this->assertEquals( $post_object->labels->view_items, 'View Posts' );
		$this->assertEquals( $post_object->labels->search_items, 'Search Marketing Links' );
		$this->assertEquals( $post_object->labels->not_found, 'No marketing links found.' );
		$this->assertEquals( $post_object->labels->not_found_in_trash, 'No marketing links found in Trash.' );
		$this->assertEquals( $post_object->labels->parent_item_colon, 'Parent Link:' );
		$this->assertEquals( $post_object->labels->all_items, 'All Marketing Links' );
		$this->assertEquals( $post_object->labels->archives, 'All Marketing Links' );
		$this->assertEquals( $post_object->labels->attributes, 'Post Attributes' );
		$this->assertEquals( $post_object->labels->insert_into_item, 'Insert into post' );
		$this->assertEquals( $post_object->labels->uploaded_to_this_item, 'Uploaded to this post' );
		$this->assertEquals( $post_object->labels->featured_image, 'Featured Image' );
		$this->assertEquals( $post_object->labels->set_featured_image, 'Set featured image' );
		$this->assertEquals( $post_object->labels->remove_featured_image, 'Remove featured image' );
		$this->assertEquals( $post_object->labels->use_featured_image, 'Use as featured image' );
		$this->assertEquals( $post_object->labels->filter_items_list, 'Filter posts list' );
		$this->assertEquals( $post_object->labels->items_list_navigation, 'Posts list navigation' );
		$this->assertEquals( $post_object->labels->items_list, 'Posts list' );
		$this->assertEquals( $post_object->labels->menu_name, 'utm.codes' );
		$this->assertEquals( $post_object->labels->name_admin_bar, 'Marketing Link' );
		$this->assertEquals( $post_object->description, 'utm.codes Marketing Links' );
		$this->assertFalse( $post_object->public );
		$this->assertFalse( $post_object->hierarchical );
		$this->assertTrue( $post_object->exclude_from_search );
		$this->assertFalse( $post_object->publicly_queryable );
		$this->assertTrue( $post_object->show_ui );
		$this->assertTrue( $post_object->show_in_menu );
		$this->assertFalse( $post_object->show_in_nav_menus );
		$this->assertTrue( $post_object->show_in_admin_bar );
		$this->assertEquals( $post_object->menu_position, null );
		$this->assertEquals( $post_object->menu_icon, 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9Ii0yMi4yMjIyMjIyMjIyMjIyMjUgLTIyLjIyMjIyMjIyMjIyMjIyNSAxNDQuNDQ0NDQ0NDQ0NDQ0NDYgMTU1LjU1NTU1NTU1NTU1NTU3IiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIj48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMTYuNjY2NjY2NjY2NjY2NjY0IC0xMS4xMTExMTExMTExMTExMSkgc2NhbGUoNS41NTU1NTU1NTU1NTU1NTUpIj48ZyBmaWxsPSIjMDAwMDAwIj48cGF0aCBkPSJNMTUgMmMtMS42IDAtMy4xLjctNC4yIDEuNy44LjIgMS41LjUgMi4xLjkuNi0uNCAxLjMtLjYgMi4xLS42IDIuMiAwIDQgMS44IDQgNHY1YzAgMi4yLTEuOCA0LTQgNHMtNC0xLjgtNC00VjkuNWMtLjUtLjYtMS4yLTEtMi0xVjEzYzAgMy4zIDIuNyA2IDYgNnM2LTIuNyA2LTZWOGMwLTMuMy0yLjctNi02LTZ6Ij48L3BhdGg+PHBhdGggZD0iTTkgMjJjMS42IDAgMy4xLS43IDQuMi0xLjctLjgtLjItMS41LS41LTIuMS0uOS0uNi40LTEuMy42LTIuMS42LTIuMiAwLTQtMS44LTQtNHYtNWMwLTIuMiAxLjgtNCA0LTRzNCAxLjggNCA0djMuNWMuNS42IDEuMiAxIDIgMVYxMWMwLTMuMy0yLjctNi02LTZzLTYgMi43LTYgNnY1YzAgMy4zIDIuNyA2IDYgNnoiPjwvcGF0aD48L2c+PC9nPjwvc3ZnPg==' );
		$this->assertEquals( $post_object->capability_type, 'post' );
		$this->assertTrue( $post_object->map_meta_cap );
		$this->assertEquals( count( $post_object->taxonomies ), 0 );
		$this->assertTrue( $post_object->can_export );
		$this->assertFalse( $post_object->show_in_rest );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_post_taxonomy() {
		$plugin = new UtmDotCodes();

		update_option( UtmDotCodes::POST_TYPE . '_labels', '' );
		$plugin->create_post_type();
		$taxonomy_object = get_object_taxonomies( UtmDotCodes::POST_TYPE, 'objects' );

		$this->assertEquals( count( $taxonomy_object ), 1 );

		update_option( UtmDotCodes::POST_TYPE . '_labels', 'on' );
		$plugin->create_post_type();
		$taxonomy_object = get_object_taxonomies( UtmDotCodes::POST_TYPE, 'objects' )['utmdclink-label'];

		$this->assertEquals( $taxonomy_object->labels->name, 'Link Labels' );
		$this->assertEquals( $taxonomy_object->labels->singular_name, 'Link Label' );
		$this->assertEquals( $taxonomy_object->labels->menu_name, 'Link Labels' );
		$this->assertEquals( $taxonomy_object->labels->all_items, 'All Link Labels' );
		$this->assertEquals( $taxonomy_object->labels->edit_item, 'Edit Link Label' );
		$this->assertEquals( $taxonomy_object->labels->view_item, 'View Link Label' );
		$this->assertEquals( $taxonomy_object->labels->update_item, 'Update Link Label' );
		$this->assertEquals( $taxonomy_object->labels->add_new_item, 'Add New Link Label' );
		$this->assertEquals( $taxonomy_object->labels->new_item_name, 'New Label' );
		$this->assertEquals( $taxonomy_object->labels->search_items, 'Search Labels' );
		$this->assertEquals( $taxonomy_object->labels->separate_items_with_commas, 'Separate labels with commas.' );
		$this->assertEquals( $taxonomy_object->labels->add_or_remove_items, 'Add or remove labels' );
		$this->assertEquals( $taxonomy_object->labels->choose_from_most_used, 'Select from most popular labels.' );
		$this->assertEquals( $taxonomy_object->labels->not_found, 'Not Found' );
		$this->assertEquals( $taxonomy_object->labels->no_terms, 'No labels' );
		$this->assertEquals( $taxonomy_object->labels->items_list, 'Labels list' );
		$this->assertEquals( $taxonomy_object->labels->items_list_navigation, 'Labels list navigation' );
		$this->assertFalse( $taxonomy_object->hierarchical );
		$this->assertFalse( $taxonomy_object->public );
		$this->assertFalse( $taxonomy_object->publicly_queryable );
		$this->assertTrue( $taxonomy_object->show_ui );
		$this->assertTrue( $taxonomy_object->show_admin_column );
		$this->assertFalse( $taxonomy_object->show_in_nav_menus );
		$this->assertFalse( $taxonomy_object->show_in_rest );
		$this->assertTrue( $taxonomy_object->show_tagcloud );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_bulk_action_remove() {
		$plugin = new UtmDotCodes();

		$test_actions = [
			'edit'  => 'Edit',
			'trash' => 'Move to Trash',
		];

		$filtered = $plugin->bulk_actions( $test_actions );

		$this->assertFalse( array_key_exists( 'edit', $filtered ) );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_batch_alternative_text() {
		$plugin = new UtmDotCodes();

		$this->assertTrue( $plugin->batch_alt( 'source' ) !== '' );
		$this->assertTrue( $plugin->batch_alt( 'medium' ) !== '' );
		$this->assertTrue( $plugin->batch_alt( 'nothing' ) === '' );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_alphanumeric_elements() {
		$plugin = new UtmDotCodes();

		update_option( UtmDotCodes::POST_TYPE . '_alphanumeric', '' );
		$unformatted = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 1234567890`~!@#$%^&* ()_+-= ?,./:";\'';
		$setting_off = $plugin->filter_link_element( 'test_param', $unformatted );

		$this->assertTrue( $unformatted === $setting_off );

		update_option( UtmDotCodes::POST_TYPE . '_alphanumeric', 'on' );
		$setting_on = $plugin->filter_link_element( 'test_param', $unformatted );

		$this->assertTrue( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 1234567890 -' === $setting_on );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_nospaces_elements() {
		$plugin = new UtmDotCodes();

		update_option( UtmDotCodes::POST_TYPE . '_nospaces', '' );
		$unformatted = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 1234567890`~!@#$%^&* ()_+-= ?,./:";\'';
		$setting_off = $plugin->filter_link_element( 'test_param', $unformatted );

		$this->assertTrue( $unformatted === $setting_off );

		update_option( UtmDotCodes::POST_TYPE . '_nospaces', 'on' );
		$setting_on = $plugin->filter_link_element( 'test_param', $unformatted );

		$this->assertTrue( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ-abcdefghijklmnopqrstuvwxyz-1234567890`~!@#$%^&*-()_+-=-?,./:";\'' === $setting_on );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_lowercase_elements() {
		$plugin = new UtmDotCodes();

		update_option( UtmDotCodes::POST_TYPE . '_lowercase', '' );
		$unformatted = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 1234567890`~!@#$%^&* ()_+-= ?,./:";\'';
		$setting_off = $plugin->filter_link_element( 'test_param', $unformatted );

		$this->assertTrue( $unformatted === $setting_off );

		update_option( UtmDotCodes::POST_TYPE . '_lowercase', 'on' );
		$setting_on = $plugin->filter_link_element( 'test_param', $unformatted );

		$this->assertTrue( 'abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz 1234567890`~!@#$%^&* ()_+-= ?,./:";\'' === $setting_on );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_validate_url() {
		$plugin = new UtmDotCodes();

		$valid_url = $plugin->validate_url( 'https://utm.codes' );
		$this->assertEquals( $valid_url, 'https://utm.codes' );

		$invalid_url = $plugin->validate_url( 'invalid' );
		$this->assertEquals( $invalid_url, get_home_url( null, '/' ) );
	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_editor_meta_box_error_messages() {
		global $post, $_GET;

		$plugin = new UtmDotCodes();
		$plugin->create_post_type();
		$post = $this->factory->post->create_and_get( [ 'post_type' => UtmDotCodes::POST_TYPE ] );

		$_GET['utmdc-error'] = 1;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-warning"><p>%s</p></div>',
					__( 'Invalid URL format. Replaced with site URL. Please update as needed.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 2;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Unable to save link. Please try again, your changes were not saved.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 1000;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Invalid URL shortener config.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 100;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Unable to connect to Bitly API to shorten url. Please try again later.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 4030;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Bitly API responded with unauthorized error. API Key is invalid or rate limit exceeded.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 500;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Bitly API experienced an error when shortening the link, please try again later.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 401;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Rebrandly API responded with unauthorized error. API Key is invalid or rate limit exceeded.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

		$_GET['utmdc-error'] = 4031;
		$form_markup         = $plugin->meta_box_contents();
		$this->assertTrue(
			in_array(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( 'Rebrandly API experienced an error when shortening the link, please try again later.', 'utm-dot-codes' )
				),
				$form_markup
			)
		);

	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_plugin_settings_links() {
		$plugin = new UtmDotCodes();

		$links = $plugin->add_links( [] );
		$this->assertEquals(
			[
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'options-general.php?page=' . UtmDotCodes::SETTINGS_PAGE ) ),
					__( 'Settings', 'utm-dot-codes' )
				),
				sprintf(
					'<a href="https://github.com/asdfdotdev/utm.codes" target="_blank">%s</a>',
					__( 'Code', 'utm-dot-codes' )
				),
			],
			$links
		);

		$bonus_links = [ '<a href="https://blah.edu">This is a test</a>', '<a href="https://another.test">This is another test</a>' ];
		$links       = $plugin->add_links( $bonus_links );
		$this->assertEquals(
			[
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'options-general.php?page=' . UtmDotCodes::SETTINGS_PAGE ) ),
					__( 'Settings', 'utm-dot-codes' )
				),
				sprintf(
					'<a href="https://github.com/asdfdotdev/utm.codes" target="_blank">%s</a>',
					__( 'Code', 'utm-dot-codes' )
				),
				'<a href="https://blah.edu">This is a test</a>',
				'<a href="https://another.test">This is another test</a>',
			],
			$links
		);

	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_get_links() {
		$plugin = new UtmDotCodes();
		$plugin->create_post_type();
		$link_elements = $plugin->get_link_elements();

		$this->assertEquals(
			$link_elements['url'],
			[
				'label'       => esc_html_x( 'Link URL', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'URL', 'utm-dot-codes' ),
				'type'        => 'url',
				'required'    => true,
				'batch_alt'   => true,
			]
		);

		$this->assertEquals(
			$link_elements['source'],
			[
				'label'       => esc_html_x( 'Campaign Source', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Source', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => true,
				'batch_alt'   => true,
			]
		);

		$this->assertEquals(
			$link_elements['medium'],
			[
				'label'       => esc_html_x( 'Campaign Medium', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Medium', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => true,
			]
		);

		$this->assertEquals(
			$link_elements['campaign'],
			[
				'label'       => esc_html_x( 'Campaign Name', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Campaign', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => false,
			]
		);

		$this->assertEquals(
			$link_elements['term'],
			[
				'label'       => esc_html_x( 'Campaign Term', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Term', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => false,
			]
		);

		$this->assertEquals(
			$link_elements['content'],
			[
				'label'       => esc_html_x( 'Campaign Content', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Content', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => false,
			]
		);

		$this->assertEquals(
			$link_elements['shorturl'],
			[
				'label'       => esc_html_x( 'Short URL', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Short URL', 'utm-dot-codes' ),
				'type'        => 'url',
				'required'    => false,
				'batch_alt'   => false,
			]
		);

	}

	/**
	 * @depends test_version_numbers_active
	 */
	function test_disable_month_dropdown() {
		$plugin = new UtmDotCodes();
		$plugin->create_post_type();

		$test_months = [
			[
				'year' => '2018',
				'month' => '1',
			],
			[
				'year' => '2018',
				'month' => '2',
			],
			[
				'year' => '2018',
				'month' => '3',
			],
		];

		$unchnaged_months = $plugin->months_dropdown_results( $test_months, 'not-our-post-type' );
		$this->assertCount( 3, $test_months );
		$this->assertEquals( $test_months, $unchnaged_months );

		$changed_months = $plugin->months_dropdown_results( $test_months, $plugin::POST_TYPE );
		$this->assertCount( 0, $changed_months );
		$this->assertEquals( [], $changed_months );
	}

}
