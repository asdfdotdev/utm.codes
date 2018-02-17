<?php
/**
 * @package utm.codes
 */

/**
 * Class UtmDotCodes
 */
class UtmDotCodes {

	const POST_TYPE = 'utmdclink';
	const NONCE_LABEL = 'UTMDC_nonce';
	const SETTINGS_PAGE = 'utm-dot-codes';
	const SETTINGS_GROUP = 'UTMDC_settings_group';
	const API_URL = 'https://www.googleapis.com/urlshortener/v1/url';

	public $link_elements;

	/**
	 * utm.codes constructor, creates post type elements and adds hooks/filters used by the plugin
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	function __construct() {
		global $pagenow;

		remove_post_type_support(self::POST_TYPE, 'revisions');

		add_action( 'plugins_loaded', [&$this, 'load_languages'] );
		add_action( 'init', [&$this, 'create_post_type'] );
		add_action( 'admin_menu', [&$this, 'add_settings_page'] );
		add_action( 'admin_init', [&$this, 'register_plugin_settings'] );
		add_action( 'admin_head', [&$this, 'add_css'] );
		add_action( 'admin_footer', [&$this, 'add_js'] );
		add_action( 'add_meta_boxes', [&$this, 'add_meta_box'], 10, 2 );
		add_action( 'add_meta_boxes', [&$this, 'remove_meta_boxes'] );
		add_action( 'save_post', [&$this, 'save_post'], 10, 1 );
		add_action( 'dashboard_glance_items', [&$this, 'add_glance'] );

		add_filter( 'plugin_action_links_' . UTMDC_PLUGIN_FILE, [&$this, 'add_links'], 10, 1 );
		add_filter( 'wp_insert_post_data', [&$this, 'insert_post_data'], 10, 2 );

		$is_post_list = ( $pagenow == 'edit.php' );
		$is_link_list = ( isset($_GET['post_type']) && $_GET['post_type'] == self::POST_TYPE );
		if ( (is_admin() && $is_post_list && $is_link_list) || $this->is_test() ) {
			add_action( 'restrict_manage_posts', [&$this, 'filter_ui'], 5, 1 );
			add_action( 'pre_get_posts', [&$this, 'apply_filters'], 5, 1 );

			add_filter( 'manage_posts_columns', [&$this, 'post_list_header'], 10, 1 );
			add_filter( 'manage_posts_custom_column', [&$this, 'post_list_columns'], 10, 2 );
			add_filter( 'months_dropdown_results', '__return_empty_array' );
			add_filter( 'bulk_actions-edit-' . self::POST_TYPE, [&$this, 'bulk_actions'] );
		}
	}

	/**
	 * Create utm.codes link post type
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function create_post_type() {
		$this->link_elements = [
			'url' => [
				'label' => __( 'Link URL', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'URL', UTMDC_TEXT_DOMAIN ),
				'type' => 'url',
				'required' => true
			],
			'source' => [
				'label' => __( 'Campaign Source', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'Source', UTMDC_TEXT_DOMAIN ),
				'type' => 'text',
				'required' => true,
				'batch_alt' => true
			],
			'medium' => [
				'label' => __( 'Campaign Medium', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'Medium', UTMDC_TEXT_DOMAIN ),
				'type' => 'text',
				'required' => false,
				'batch_alt' => true
			],
			'campaign' => [
				'label' => __( 'Campaign Name', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'Name', UTMDC_TEXT_DOMAIN ),
				'type' => 'text',
				'required' => false
			],
			'term' => [
				'label' => __( 'Campaign Term', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'Term', UTMDC_TEXT_DOMAIN ),
				'type' => 'text',
				'required' => false
			],
			'content' => [
				'label' => __( 'Campaign Content', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'Content', UTMDC_TEXT_DOMAIN ),
				'type' => 'text',
				'required' => false
			],
			'shorturl' => [
				'label' => __( 'Short URL', UTMDC_TEXT_DOMAIN ),
				'short_label' => __( 'Short URL', UTMDC_TEXT_DOMAIN ),
				'type' => 'url',
				'required' => false
			]
		];

		register_post_type( self::POST_TYPE,
			[
				'labels' => array(
					'menu_name'				=> _x( 'utm.codes', 'admin menu', UTMDC_TEXT_DOMAIN ),
					'name'					=> _x( 'Marketing Links', 'post type general name', UTMDC_TEXT_DOMAIN ),
					'singular_name'			=> _x( 'Marketing Link', 'post type singular name', UTMDC_TEXT_DOMAIN ),
					'name_admin_bar'		=> _x( 'Marketing Link', 'add new on admin bar', UTMDC_TEXT_DOMAIN ),
					'add_new'				=> _x( 'Add New Link', 'marketing link', UTMDC_TEXT_DOMAIN ),
					'add_new_item'			=> __( 'Add New Marketing Link', UTMDC_TEXT_DOMAIN ),
					'new_item'				=> __( 'New Marketing Link', UTMDC_TEXT_DOMAIN ),
					'edit_item'				=> __( 'Edit Marketing Link', UTMDC_TEXT_DOMAIN ),
					'view_item'				=> __( 'View Marketing Link', UTMDC_TEXT_DOMAIN ),
					'all_items'				=> __( 'All Marketing Links', UTMDC_TEXT_DOMAIN ),
					'search_items'			=> __( 'Search Marketing Links', UTMDC_TEXT_DOMAIN ),
					'parent_item_colon'		=> __( 'Parent Link:', UTMDC_TEXT_DOMAIN ),
					'not_found'				=> __( 'No marketing links found.', UTMDC_TEXT_DOMAIN ),
					'not_found_in_trash'	=> __( 'No marketing links found in Trash.', UTMDC_TEXT_DOMAIN )
				),
				'description'			=> __( 'utm.codes Marketing Links', UTMDC_TEXT_DOMAIN ),
				'public'				=> false,
				'publicly_queryable'	=> false,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'query_var'				=> false,
				'capability_type'		=> 'post',
				'has_archive'			=> false,
				'hierarchical'			=> false,
				'supports'				=> [ 'author' ],
				'menu_icon'				=> 'dashicons-admin-links'
			]
		);
	}

	/**
	 * Add meta box to links post edit for the post meta form
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function add_meta_box() {
		add_meta_box(
			'utmdc_link_meta_box',
			'utm.codes Editor',
			[&$this, 'meta_box_contents'],
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Remove default meta boxes that we don't need
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box('slugdiv', self::POST_TYPE, 'normal');
	}

	/**
	 * Generate and output links form markup, used to update post meta with link contents
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function meta_box_contents() {
		global $post;

		if ( array_key_exists( 'utmdc-error', $_GET ) ) {
			switch ( $_GET['utmdc-error'] ) {
				case 1:
					echo sprintf(
						'<div class="notice notice-warning"><p>%s</p></div>',
						__( 'Invalid URL format. Replaced with site URL. Please update as needed.', UTMDC_TEXT_DOMAIN )
					);
					break;

				case 2:
					echo sprintf(
						'<div class="notice notice-error"><p>%s</p></div>',
						__( 'Unable to save link. Please try again, your changes were not saved.', UTMDC_TEXT_DOMAIN )
					);
					break;
			}
		}

		$form_markup = array_map( function($key, $entry) use($post) {
			$value = get_post_meta( $post->ID, self::POST_TYPE . '_' . $key, true );

			if ( $entry['type'] == 'url' ) {
				$value = esc_url($value);
			}
			else {
				$value = esc_attr($value);
			}

			return sprintf(
				'<p><label for="%1$s_%2$s" class="%1$s_%2$s">%3$s<br><input type="%4$s" name="%1$s_%2$s" id="%1$s_%2$s"%5$s value="%6$s"><span>%7$s</span></label></p>',
				self::POST_TYPE,
				$key,
				$entry['label'],
				$entry['type'],
				($entry['required'] ? ' required="required"' : ''),
				isset($value) ? $value : '',
				(@$entry['batch_alt']) ? $this->batch_alt($key) : ''
			);
		}, array_keys($this->link_elements), $this->link_elements );

		if ( get_option(self::POST_TYPE . '_apikey') != '' ) {
			array_unshift(
				$form_markup,
				sprintf(
					'<p><label for="%1$s_%2$s" class="selectit"><input type="checkbox" name="%1$s_%2$s" id="%1$s_%2$s">%3$s</label></p>',
					self::POST_TYPE,
					'shorten',
					__( 'Shorten Completed Link When Saving', UTMDC_TEXT_DOMAIN )
				)
			);
		}

		array_unshift(
			$form_markup,
			sprintf(
				'<input type="hidden" name="%s" value="%s">',
				self::NONCE_LABEL,
				wp_create_nonce( UTMDC_PLUGIN_FILE )
			)
		);

		if ( $post->post_content != '' ) {
			array_unshift(
				$form_markup,
				sprintf(
					'<p><b>%1$s</b><br><a href="%2$s" target="_blank">%2$s</a></p>',
					__( 'Marketing Link', UTMDC_TEXT_DOMAIN ),
					$post->post_content
				)
			);
		}
		else {
			if ( get_option(self::POST_TYPE . '_social') != '' ) {
				array_unshift(
					$form_markup,
					sprintf(
						'<p><label for="%1$s_%2$s" class="selectit"><input type="checkbox" name="%1$s_%2$s" id="%1$s_%2$s">%3$s</label></p>',
						self::POST_TYPE,
						'batch',
						__( 'Create Social Links in Batch', UTMDC_TEXT_DOMAIN )
					)
				);
			}
		}

		if ( $this->is_test() ) {
			return $form_markup;
		}
		else {
			echo implode(PHP_EOL, $form_markup);
		}
	}

	/**
	 * Register links plugin settings page
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'utm.codes Plugin Settings', UTMDC_TEXT_DOMAIN),
			__( 'utm.codes', UTMDC_TEXT_DOMAIN),
			'manage_options',
			self::SETTINGS_PAGE,
			[&$this, 'render_settings_options']
		);
	}

	/**
	 * Generate and output links settings page options
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function render_settings_options() {
		$networks = [
			'behance' => ['Behance', 'fab fa-behance'],
			'blogger' => ['Blogger', 'fab fa-blogger-b'],
			'facebook' => ['Facebook', 'fab fa-facebook-f'],
			'flickr' => ['Flickr', 'fab fa-flickr'],
			'googleplus' => ['Google+', 'fab fa-google-plus-g'],
			'instagram' => ['Instagram', 'fab fa-instagram'],
			'linkedin' => ['LinkedIn', 'fab fa-linkedin-in'],
			'medium' => ['Medium', 'fab fa-medium-m'],
			'meetup' => ['Meetup', 'fab fa-meetup'],
			'pinterest' => ['Pinterest', 'fab fa-pinterest-p'],
			'reddit' => ['Reddit', 'fab fa-reddit-alien'],
			'stumbleupon' => ['StumbleUpon', 'fab fa-stumbleupon'],
			'stack-exchange' => ['Stack Exchange', 'fab fa-stack-exchange'],
			'stack-overflow' => ['Stack Overflow', 'fab fa-stack-overflow'],
			'tumblr' => ['Tumblr', 'fab fa-tumblr'],
			'twitter' => ['Twitter', 'fab fa-twitter'],
			'vine' => ['Vine', 'fab fa-vine'],
			'vimeo' => ['Vimeo', 'fab fa-vimeo'],
			'xing' => ['Xing', 'fab fa-xing'],
			'youtube' => ['YouTube', 'fab fa-youtube']
		]; ?>

	<div class="wrap">
		<form method="post" action="options.php">
			<h1>
				<?php _ex( 'utm.codes Settings', 'Settings Page Title', UTMDC_TEXT_DOMAIN ); ?>
			</h1>
			<h2 class="title">
				<?php _e( 'Social', UTMDC_TEXT_DOMAIN ); ?>
			</h2>
			<p>
				<?php _e( 'Select the sites to include when batch creating social links.', UTMDC_TEXT_DOMAIN ); ?>
			</p>
			<div class="utmdclinks-settings-social">
			<?php
				settings_fields( self::SETTINGS_GROUP );

				$active_networks = get_option(self::POST_TYPE . '_social');
				$social_options = array_map( function($key, $value) use($active_networks) {
					return sprintf(
						'<label for="%1$s"><i class="%2$s"></i><input type="checkbox" name="%1$s" id="%1$s" %4$s/>%3$s</label>',
						self::POST_TYPE . '_social[' . $key . ']',
						$value[1],
						$value[0],
						checked( isset($active_networks[$key]), true, false )
					);
				}, array_keys($networks), $networks );

				echo implode(PHP_EOL, $social_options);
			?>
			</div>
			<h2 class="title">
				<?php _e( 'URL Shortener', UTMDC_TEXT_DOMAIN ); ?>
			</h2>
			<p>
				<?php _e( 'Setup Goo.gl api access to enable link shortening.', UTMDC_TEXT_DOMAIN ); ?>
			</p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php _ex( 'Goo.gl API Key:', 'Settings input label for API key input.', UTMDC_TEXT_DOMAIN ); ?>
					</th>
					<td>
						<?php
							echo sprintf(
								'<input type="text" name="%s" value="%s" size="40">',
								self::POST_TYPE . '_apikey',
								get_option( self::POST_TYPE . '_apikey' )
							);

							echo sprintf(
								'<br><sup>[ %s <a href="https://developers.google.com/url-shortener/v1/getting_started#APIKey" target="_blank">%s</a> ]</sup>',
								__( 'Need an API key?', UTMDC_TEXT_DOMAIN ),
								__( 'Click here to get one.', UTMDC_TEXT_DOMAIN )
							)
						?>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
<?php

	}

	/**
	 * Register plugin settings
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function register_plugin_settings() {
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_social' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_apikey' );
	}

	/**
	 * Add our special links for display beside the utm.codes plugin in the installed plugins list
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $links				Array of plugin action links
	 *
	 * @return mixed				Updated array of links
	 */
	public function add_links( $links ) {
		return array_merge(
			[
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'options-general.php?page=' . self::SETTINGS_PAGE ) ),
					__( 'Settings', UTMDC_TEXT_DOMAIN )
				),
				sprintf(
					'<a href="https://github.com/christopherldotcom/utm.codes" target="_blank">%s</a>',
					__( 'Code', UTMDC_TEXT_DOMAIN )
				)
			],
			$links
		);
	}

	/**
	 * Update link post meta data when saving the link
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $post_id				The post ID
	 *
	 * @return mixed				Post ID if we're not making changes, void if we do
	 */
	public function save_post( $post_id ) {
		if ( isset($_POST['post_type']) && self::POST_TYPE === $_POST['post_type'] ) {
			$invalid_nonce = ( isset($_POST[self::NONCE_LABEL]) && !wp_verify_nonce($_POST[self::NONCE_LABEL], UTMDC_PLUGIN_FILE) );
			$doing_autosave = ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE );
			$cannot_edit = ( !current_user_can('edit_page', $post_id) );

			if ( $invalid_nonce || $doing_autosave || $cannot_edit ) {
				return $post_id;
			}

			$valid_post_id = ( isset( $_POST['ID'] ) && is_numeric( $_POST['ID'] ) && $_POST['ID'] == $post_id );

			if ( !$valid_post_id ) {
				add_filter('redirect_post_location', function( $location ) {
					return add_query_arg( 'utmdc-error', '2', $location );
				});

				return $post_id;
			}

			$_POST[self::POST_TYPE . '_url'] = $this->validate_url( $_POST[self::POST_TYPE . '_url'] );

			if ( @$_POST[self::POST_TYPE . '_batch'] == 'on' ) {
				$networks = array_keys( get_option(self::POST_TYPE . '_social') );

				$_POST[self::POST_TYPE . '_source'] = $networks[0];
				$_POST[self::POST_TYPE . '_medium'] = __( 'social', UTMDC_TEXT_DOMAIN );
				unset($networks[0]);

				$post_template = [];
				array_map( function($key) use(&$post_template) {
					$post_template[self::POST_TYPE . '_' . $key] = $_POST[self::POST_TYPE . '_' . $key];
				}, array_keys($this->link_elements) );

				remove_action( 'save_post', [&$this, 'save_post'] );

				array_map( function($network) {
					$new_post = [
						'post_title' => '',
						'post_content' => '',
						'post_type' => self::POST_TYPE,
						'post_status' => 'publish',
						'meta_input' => [
							self::POST_TYPE . '_url' => $_POST[self::POST_TYPE . '_url'],
							self::POST_TYPE . '_source' => $network,
							self::POST_TYPE . '_medium' => __( 'social', UTMDC_TEXT_DOMAIN ),
							self::POST_TYPE . '_campaign' => $_POST[self::POST_TYPE . '_campaign'],
							self::POST_TYPE . '_term' => $_POST[self::POST_TYPE . '_term'],
							self::POST_TYPE . '_content' => $_POST[self::POST_TYPE . '_content']
						]
					];

					if ( @$_POST[self::POST_TYPE . '_shorten'] == 'on' ) {
						$new_post['meta_input'][self::POST_TYPE . '_shorturl'] = $this->generate_short_url( $new_post, $new_post['meta_input'][self::POST_TYPE . '_url'] );
					}

					$result = wp_insert_post( $new_post );
				}, $networks );

				add_action( 'save_post', [&$this, 'save_post'], 10, 1 );
			}

			if ( @$_POST[self::POST_TYPE . '_shorten'] == 'on' ) {
				$_POST[self::POST_TYPE . '_shorturl'] = $this->generate_short_url( $_POST, $_POST[self::POST_TYPE . '_url'] );
			}

			array_map( function($key) {
				$field = self::POST_TYPE . '_' . $key;
				$updated = sanitize_text_field( $_POST[$field] );
				$current = get_post_meta( $_POST['ID'], $field, true );

				if ( '' === $updated ) {
					delete_post_meta( $_POST['ID'], $field, $current );
				}
				else if ( isset($updated) && $updated !== $current ) {
					update_post_meta( $_POST['ID'], $field, $updated );
				}
			}, array_keys($this->link_elements) );
		}

		unset($_POST[self::POST_TYPE . '_batch']);
		unset($_POST[self::POST_TYPE . '_shorten']);
	}

	/**
	 * Update post title and content to include complete link when inserting new link posts
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $data					Array of slashed post data
	 * @param $postarr				Array of sanitized, but otherwise unmodified post data
	 *
	 * @return array				Updated $data array of post data with completed link
	 */
	public function insert_post_data( $data, $postarr ) {
		if ( isset($postarr['post_type']) && $postarr['post_type'] == self::POST_TYPE ) {
			if ( $postarr['post_status'] == 'publish' && isset( $postarr['meta_input'] ) ) {
				$data['post_title'] = $this->validate_url( $postarr['meta_input'][self::POST_TYPE . '_url'] );
				$data['post_content'] = $data['post_title'] . $this->generate_query_string( $postarr['meta_input'], $data['post_title'] );
			}
			else if ( isset( $postarr[self::POST_TYPE . '_url'] ) ) {
				$data['post_title'] = $this->validate_url( $postarr[self::POST_TYPE . '_url'] );
				$data['post_content'] = $data['post_title'] . sanitize_text_field( $this->generate_query_string( $postarr, $data['post_title'] ) );
			}
		}

		return $data;
	}

	/**
	 * Generate link query string by combining separate link utm values. This method will accept either
	 * a post array with individual utm entries or an array with utm entries contained within a meta_input entry
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $data					Array of post data containing associative utm input values, or Array of post data
	 * 								containing meta_input array with associative utm input values
	 * @param $url					String url query string is being prepared for
	 *
	 * @return string				Prepared link query string with configured utm parameters
	 */
	public function generate_query_string( $data, $url ) {
		if ( isset($data['meta_input']) ) {
			$data = $data['meta_input'];
		}

		$params_array = array_map(
			'sanitize_text_field',
			array_filter([
				'utm_source' => $data[self::POST_TYPE . '_source'],
				'utm_medium' => $data[self::POST_TYPE . '_medium'],
				'utm_campaign' => $data[self::POST_TYPE . '_campaign'],
				'utm_term' => $data[self::POST_TYPE . '_term'],
				'utm_content' => $data[self::POST_TYPE . '_content'],
				'utm_gen' => 'utmdc'
			])
		);

		return (strpos($url, '?') ? '&' : '?') . http_build_query($params_array);
	}

	/**
	 * Retrieve short url from Goo.gl API
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $data					Array of post data containing associative utm input values, or Array of post data
	 * 								containing meta_input array with associative utm input values
	 * @param $url					String url query string is being prepared for
	 *
	 * @return string				Shortened url if request successful, empty string if not
	 */
	public function generate_short_url( $data, $url ) {
		$short_url = '';
		$api_key = get_option(self::POST_TYPE . '_apikey');

		if ( isset($data['meta_input']) ) {
			$data = $data['meta_input'];
		}

		if ( $api_key != '' ) {
			$long_url = $this->sanitize_url( $data[self::POST_TYPE . '_url'] . $this->generate_query_string( $data, $url ) );

			$response = wp_remote_post(
				self::API_URL . '?key=' . $api_key,
				[
					'headers' => ['content-type' => 'application/json'],
					'body' => json_encode( ['longUrl' => $long_url] ),
				]
			);

			if ( $response['response']['code'] == 200 ) {
				$body = json_decode( $response['body'] );

				if ( @filter_var($body->id, FILTER_VALIDATE_URL) ) {
					$short_url = $this->sanitize_url( $body->id );
				}
			}
		}

		return $short_url;
	}

	/**
	 * Update links post list to include link element columns, remove title and date columns
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $columns				An array of column name => label
	 *
	 * @return array				Updated column array, with new columns added
	 */
	public function post_list_header( $columns ) {
		unset($columns['title']);
		unset($columns['date']);
		unset($columns['author']);

		return array_merge(
			$columns,
			[
				'utmdc_link' => __( 'Link', UTMDC_TEXT_DOMAIN ),
				'utmdc_source' => __( 'Source', UTMDC_TEXT_DOMAIN ),
				'utmdc_medium' => __( 'Medium', UTMDC_TEXT_DOMAIN ),
				'utmdc_campaign' => __( 'Campaign', UTMDC_TEXT_DOMAIN ),
				'utmdc_term' => __( 'Term', UTMDC_TEXT_DOMAIN ),
				'utmdc_content' => __( 'Content', UTMDC_TEXT_DOMAIN ),
				'copy_utmdc_link' => __( 'Copy Links', UTMDC_TEXT_DOMAIN ),
			]
		);
	}

	/**
	 * Output link element contents within links post list custom columns
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $column_name			Name of column to display
	 * @param $post_id				ID of current post
	 */
	public function post_list_columns( $column_name, $post_id ) {
		if ($column_name == 'utmdc_link') {
			echo sprintf(
				'<a href="%s" target="_blank">%s</a>',
				get_post_meta( $post_id, self::POST_TYPE . '_url', true ),
				strip_tags(get_the_content())
			);
		}
		else if ($column_name == 'utmdc_source') {
			echo get_post_meta( $post_id, self::POST_TYPE . '_source', true );
		}
		else if ($column_name == 'utmdc_medium') {
			echo get_post_meta( $post_id, self::POST_TYPE . '_medium', true );
		}
		else if ($column_name == 'utmdc_campaign') {
			echo get_post_meta( $post_id, self::POST_TYPE . '_campaign', true );
		}
		else if ($column_name == 'utmdc_term') {
			echo get_post_meta( $post_id, self::POST_TYPE . '_term', true );
		}
		else if ($column_name == 'utmdc_content') {
			echo get_post_meta( $post_id, self::POST_TYPE . '_content', true );
		}
		else if ($column_name == 'copy_utmdc_link') {
			echo sprintf(
				'%s <input type="text" value="%s" readonly="readonly" class="utmdclinks-copy">',
				_x( 'Full:', 'Post list copy link input label', UTMDC_TEXT_DOMAIN ),
				strip_tags( get_the_content() )
			);

			echo sprintf(
				'%s <input type="text" value="%s" readonly="readonly" class="utmdclinks-copy">',
				_x( 'Short:', 'Post list copy link input label', UTMDC_TEXT_DOMAIN ),
				get_post_meta( $post_id, self::POST_TYPE . '_shorturl', true )
			);
		}
	}

	/**
	 * Filter post list query based on user filter selection.
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $query				Query to filter
	 *
	 * @return object				Updated query with filtered query vars
	 */
	public function apply_filters( $query ) {
		$filters = array_keys( $this->link_elements );
		unset($filters['url']);

		$meta_query = array_filter(array_map( function($filter) {
			$filter = self::POST_TYPE . '_' . $filter;

			if ( @isset($_GET[$filter]) && $_GET[$filter] != '' ) {
				return [
					'key' => $filter,
					'value' => urldecode( filter_input(INPUT_GET, $filter, FILTER_SANITIZE_STRING) ),
					'compare' => '='
				];
			}
		}, $filters ));

		$query->set( 'meta_query', $meta_query );

		return $query;
	}

	/**
	 * Create UI filters displayed above post list in admin.
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $post_type			Post type slug
	 */
	public function filter_ui($post_type) {
		global $wpdb;

		$filter_options = $this->link_elements;
		unset($filter_options['url']);
		unset($filter_options['shorturl']);

		$markup = array_map( function($key, $filter) use($wpdb) {
			$options = array_map( function ($value) use($key) {
				return sprintf(
					'<option value="%s"%s>%s</option>',
					urlencode($value->meta_value),
					selected(
						$value->meta_value,
						urldecode(@$_GET[self::POST_TYPE . '_' . $key]),
						false
					),
					$value->meta_value
				);
			},	$wpdb->get_results($wpdb->prepare(
					"SELECT DISTINCT(meta_value)
					FROM $wpdb->postmeta
					WHERE meta_key = '%s'
						AND meta_value != ''
						AND post_id IN ( SELECT ID FROM $wpdb->posts WHERE post_type = '%s' AND post_status != 'trash' )
					ORDER BY meta_value",
					self::POST_TYPE . '_' . $key,
					self::POST_TYPE ))
			);

			return sprintf(
				'<select id="filter-by-%1$s" name="%1$s"><option value="">%2$s %3$s</option>%4$s</select>',
				self::POST_TYPE . '_' . $key,
				__( 'Any', UTMDC_TEXT_DOMAIN ),
				$filter['short_label'],
				implode(PHP_EOL, $options)
			);

		}, array_keys($filter_options), $filter_options );

		echo implode(PHP_EOL, $markup);
	}

	/**
	 * Remove Edit from links bulk actions
	 *
	 * @param $bulk_actions			Array of bulk actions
	 *
	 * @return mixed				Array of updated bulk actions
	 */
	public function bulk_actions( $bulk_actions ) {
		unset($bulk_actions['edit']);
		return $bulk_actions;
	}

	/**
	 * Enqueue utm.codes css, uses hashed file contents for version
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function add_css() {
		wp_enqueue_style(
			'font-awesome',
			'https://use.fontawesome.com/releases/v5.0.4/css/all.css',
			[],
			UTMDC_VERSION,
			'all'
		);
		wp_enqueue_style(
			'utm-dot-codes',
			UTMDC_PLUGIN_URL . 'css/utmdotcodes.css',
			['font-awesome'],
			hash_file('sha1', UTMDC_PLUGIN_DIR . 'css/utmdotcodes.css'),
			'all'
		);
	}

	/**
	 * Enqueue utm.codes links javascript, uses hashed file contents for version
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function add_js() {
		wp_enqueue_script(
			'utm-dot-codes',
			UTMDC_PLUGIN_URL . 'js/utmdotcodes.min.js',
			['jquery'],
			hash_file('sha1', UTMDC_PLUGIN_DIR . 'js/utmdotcodes.min.js'),
			'all'
		);
	}

	/**
	 * Generate alternate output for social inputs in link creation form to display when batch creation active
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $key					String name of link element
	 *
	 * @return string				Content to output with form (may include markup)
	 */
	public function batch_alt($key) {
		$alt = '';

		switch ($key) {

			case 'source':
				$networks = get_option(self::POST_TYPE . '_social');
				$network_count = 0;

				if ( is_array($networks) ) {
					$network_count = count($networks);
				}

				$alt = sprintf(
					'%s <a href="%s" target="_blank" tabindex="-1">%s %s %s</a>.',
					_x( 'Individual links will be created with unique source for', 'Batch link creation notice, links to settings page with number of active networks', UTMDC_TEXT_DOMAIN ),
					esc_url( admin_url( 'options-general.php?page=' . self::SETTINGS_PAGE ) ),
					$network_count,
					__( 'active', UTMDC_TEXT_DOMAIN ),
					ngettext(
						__( 'network', UTMDC_TEXT_DOMAIN ),
						__( 'networks', UTMDC_TEXT_DOMAIN ),
						$network_count
					)
				);
				break;

			case 'medium':
				$alt = __( 'Social', UTMDC_TEXT_DOMAIN );
				break;

		}

		return $alt;
	}

	/**
	 * Load utm.codes links language files
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function load_languages() {
		load_theme_textdomain( UTMDC_TEXT_DOMAIN, UTMDC_PLUGIN_DIR . 'languages' );
	}

	/**
	 * Add link count to dashboard glance
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function add_glance( $glances ) {
		$post_count = number_format_i18n( wp_count_posts( self::POST_TYPE )->publish );
		$post_object = get_post_type_object( self::POST_TYPE );

		$glances[] = sprintf(
			'<a href="%s" class="%s">%s %s</a>',
			( current_user_can('edit_posts') ) ? admin_url('edit.php?post_type='.$post_object->name) : 'javascript:;',
			'utmdclink-count',
			$post_count,
			ngettext(
				$post_object->labels->singular_name,
				$post_object->labels->name,
				$post_count
			)
		);

		return $glances;
	}

	/**
	 * Get link elements array with configured values
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @return array				Array of link elements
	 */
	public function get_link_elements() {
		return $this->link_elements;
	}

	/**
	 * Validate URL input, if invalid sustitute with site url and add error notice
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $url					String to validate as URL
	 *
	 * @return string				Validated URL or site URL on error
	 */
	public function validate_url( $url ) {
		$url = $this->sanitize_url( $url );

		if ( $url != filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$url = get_home_url( null, '/' );

			add_filter('redirect_post_location', function( $location ) {
				return add_query_arg( 'utmdc-error', '1', $location );
			});
		}

		return $url;
	}

	/**
	 * Wrapper for WordPress esc_url() to provide better contextual indication of what we're doing to the url
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @param $url					String to validate as URL
	 *
	 * @return string				Cleaned url string
	 */
	public function sanitize_url( $url ) {
		return esc_url( $url );
	}

	/**
	 * Determine if class is running in a test
	 *
	 * @since 1.0
	 * @version 1.0
	 *
	 * @return bool					True if running tests
	 */
	public function is_test() {
		return defined('UTMDC_IS_TEST') && constant('UTMDC_IS_TEST');
	}
}
