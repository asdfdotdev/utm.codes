<?php
/**
 * Utm.codes plugin class
 *
 * @package UtmDotCodes
 */

/**
 * Class UtmDotCodes
 */
class UtmDotCodes {

	const POST_TYPE        = 'utmdclink';
	const NONCE_LABEL      = 'UTMDC_nonce';
	const REST_NONCE_LABEL = 'UTMDC_REST_nonce';
	const SETTINGS_PAGE    = 'utm-dot-codes';
	const SETTINGS_GROUP   = 'UTMDC_settings_group';

	/**
	 * Collection of elements required to construct a link.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $link_elements;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $pagenow;

		require_once 'shorten/interface.php';

		remove_post_type_support( self::POST_TYPE, 'revisions' );

		add_action( 'plugins_loaded', array( &$this, 'load_languages' ) );
		add_action( 'init', array( &$this, 'create_post_type' ) );
		add_action( 'admin_menu', array( &$this, 'add_settings_page' ) );
		add_action( 'admin_init', array( &$this, 'register_plugin_settings' ) );
		add_action( 'admin_head', array( &$this, 'add_css' ) );
		add_action( 'admin_footer', array( &$this, 'add_js' ) );
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ), 10, 2 );
		add_action( 'add_meta_boxes', array( &$this, 'remove_meta_boxes' ) );
		add_action( 'save_post', array( &$this, 'save_post' ), 10, 1 );
		add_action( 'dashboard_glance_items', array( &$this, 'add_glance' ) );
		add_action( 'wp_ajax_utmdc_check_url_response', array( &$this, 'check_url_response' ) );

		add_filter( 'plugin_action_links_' . UTMDC_PLUGIN_FILE, array( &$this, 'add_links' ), 10, 1 );
		add_filter( 'wp_insert_post_data', array( &$this, 'insert_post_data' ), 10, 2 );
		add_filter( 'gettext', array( &$this, 'change_publish_button' ), 10, 2 );
		add_filter(
			sprintf( 'pre_update_option_%s', self::POST_TYPE . '_rebrandly_domains_update' ),
			array( &$this, 'pre_rebrandly_domains_update' ),
			10,
			3
		);

		$is_post_list  = ( 'edit.php' === $pagenow );
		$is_utmdc_post = ( self::POST_TYPE === filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING ) );

		if ( ( is_admin() && $is_post_list && $is_utmdc_post ) || $this->is_test() ) {
			add_action( 'restrict_manage_posts', array( &$this, 'filter_ui' ), 5, 1 );
			add_action( 'pre_get_posts', array( &$this, 'apply_filters' ), 5, 1 );
			add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( &$this, 'post_list_header' ), 10, 1 );
			add_filter( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( &$this, 'post_list_columns' ), 10, 2 );
			add_filter( 'months_dropdown_results', array( &$this, 'months_dropdown_results' ), 10, 2 );
			add_filter( 'bulk_actions-edit-' . self::POST_TYPE, array( &$this, 'bulk_actions' ) );
			add_filter( 'post_row_actions', array( &$this, 'remove_quick_edit' ), 10, 1 );
		}
	}

	/**
	 * Create utm.codes link post type and, if enabled in settings, labels taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function create_post_type() {
		$this->link_elements = array(
			'url'      => array(
				'label'       => esc_html_x( 'Link URL', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'URL', 'utm-dot-codes' ),
				'type'        => 'url',
				'required'    => true,
				'batch_alt'   => true,
			),
			'source'   => array(
				'label'       => esc_html_x( 'Campaign Source', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Source', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => true,
				'batch_alt'   => true,
			),
			'medium'   => array(
				'label'       => esc_html_x( 'Campaign Medium', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Medium', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => true,
			),
			'campaign' => array(
				'label'       => esc_html_x( 'Campaign Name', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Campaign', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => false,
			),
			'term'     => array(
				'label'       => esc_html_x( 'Campaign Term', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Term', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => false,
			),
			'content'  => array(
				'label'       => esc_html_x( 'Campaign Content', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Content', 'utm-dot-codes' ),
				'type'        => 'text',
				'required'    => false,
				'batch_alt'   => false,
			),
			'shorturl' => array(
				'label'       => esc_html_x( 'Short URL', 'utm-dot-codes' ),
				'short_label' => esc_html_x( 'Short URL', 'utm-dot-codes' ),
				'type'        => 'url',
				'required'    => false,
				'batch_alt'   => false,
			),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'             => array(
					'menu_name'          => _x( 'utm.codes', 'admin menu', 'utm-dot-codes' ),
					'name'               => _x( 'Marketing Links', 'post type general name', 'utm-dot-codes' ),
					'singular_name'      => _x( 'Marketing Link', 'post type singular name', 'utm-dot-codes' ),
					'name_admin_bar'     => _x( 'Marketing Link', 'add new on admin bar', 'utm-dot-codes' ),
					'add_new'            => _x( 'Add New Link', 'marketing link', 'utm-dot-codes' ),
					'add_new_item'       => __( 'Add New Marketing Link', 'utm-dot-codes' ),
					'new_item'           => __( 'New Marketing Link', 'utm-dot-codes' ),
					'edit_item'          => __( 'Edit Marketing Link', 'utm-dot-codes' ),
					'view_item'          => __( 'View Marketing Link', 'utm-dot-codes' ),
					'all_items'          => __( 'All Marketing Links', 'utm-dot-codes' ),
					'search_items'       => __( 'Search Marketing Links', 'utm-dot-codes' ),
					'parent_item_colon'  => __( 'Parent Link:', 'utm-dot-codes' ),
					'not_found'          => __( 'No marketing links found.', 'utm-dot-codes' ),
					'not_found_in_trash' => __( 'No marketing links found in Trash.', 'utm-dot-codes' ),
					'featured_image'     => __( 'Featured Image', 'utm-dot-codes' ),
				),
				'description'        => __( 'utm.codes Marketing Links', 'utm-dot-codes' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => array( 'author' ),
				'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9Ii0yMi4yMjIyMjIyMjIyMjIyMjUgLTIyLjIyMjIyMjIyMjIyMjIyNSAxNDQuNDQ0NDQ0NDQ0NDQ0NDYgMTU1LjU1NTU1NTU1NTU1NTU3IiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIj48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMTYuNjY2NjY2NjY2NjY2NjY0IC0xMS4xMTExMTExMTExMTExMSkgc2NhbGUoNS41NTU1NTU1NTU1NTU1NTUpIj48ZyBmaWxsPSIjMDAwMDAwIj48cGF0aCBkPSJNMTUgMmMtMS42IDAtMy4xLjctNC4yIDEuNy44LjIgMS41LjUgMi4xLjkuNi0uNCAxLjMtLjYgMi4xLS42IDIuMiAwIDQgMS44IDQgNHY1YzAgMi4yLTEuOCA0LTQgNHMtNC0xLjgtNC00VjkuNWMtLjUtLjYtMS4yLTEtMi0xVjEzYzAgMy4zIDIuNyA2IDYgNnM2LTIuNyA2LTZWOGMwLTMuMy0yLjctNi02LTZ6Ij48L3BhdGg+PHBhdGggZD0iTTkgMjJjMS42IDAgMy4xLS43IDQuMi0xLjctLjgtLjItMS41LS41LTIuMS0uOS0uNi40LTEuMy42LTIuMS42LTIuMiAwLTQtMS44LTQtNHYtNWMwLTIuMiAxLjgtNCA0LTRzNCAxLjggNCA0djMuNWMuNS42IDEuMiAxIDIgMVYxMWMwLTMuMy0yLjctNi02LTZzLTYgMi43LTYgNnY1YzAgMy4zIDIuNyA2IDYgNnoiPjwvcGF0aD48L2c+PC9nPjwvc3ZnPg==',
			)
		);

		if ( 'on' === get_option( self::POST_TYPE . '_labels' ) ) {
			register_taxonomy(
				self::POST_TYPE . '-label',
				array( self::POST_TYPE ),
				array(
					'labels'             => array(
						'name'                       => _x( 'Link Labels', 'Taxonomy General Name', 'utm-dot-codes' ),
						'singular_name'              => _x( 'Link Label', 'Taxonomy Singular Name', 'utm-dot-codes' ),
						'menu_name'                  => __( 'Link Labels', 'utm-dot-codes' ),
						'all_items'                  => __( 'All Link Labels', 'utm-dot-codes' ),
						'edit_item'                  => __( 'Edit Link Label', 'utm-dot-codes' ),
						'view_item'                  => __( 'View Link Label', 'utm-dot-codes' ),
						'update_item'                => __( 'Update Link Label', 'utm-dot-codes' ),
						'add_new_item'               => __( 'Add New Link Label', 'utm-dot-codes' ),
						'new_item_name'              => __( 'New Label', 'utm-dot-codes' ),
						'search_items'               => __( 'Search Labels', 'utm-dot-codes' ),
						'separate_items_with_commas' => __( 'Separate labels with commas.', 'utm-dot-codes' ),
						'add_or_remove_items'        => __( 'Add or remove labels', 'utm-dot-codes' ),
						'choose_from_most_used'      => __( 'Select from most popular labels.', 'utm-dot-codes' ),
						'not_found'                  => __( 'Not Found', 'utm-dot-codes' ),
						'no_terms'                   => __( 'No labels', 'utm-dot-codes' ),
						'items_list'                 => __( 'Labels list', 'utm-dot-codes' ),
						'items_list_navigation'      => __( 'Labels list navigation', 'utm-dot-codes' ),
					),
					'hierarchical'       => false,
					'public'             => false,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'show_admin_column'  => true,
					'show_in_nav_menus'  => false,
					'show_in_rest'       => false,
					'show_tagcloud'      => true,
				)
			);
		}
	}

	/**
	 * Add meta box to links post edit for the post meta form.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_box() {
		add_meta_box(
			'utmdc_link_meta_box',
			'utm.codes Editor',
			array( &$this, 'meta_box_contents' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Remove default meta boxes that we don't need.
	 *
	 * @since 1.0.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'slugdiv', self::POST_TYPE, 'normal' );
	}

	/**
	 * Generate and output links form markup, used to update post meta with link contents
	 *
	 * @since 1.0.0
	 */
	public function meta_box_contents() {
		global $post;

		$contents = array();

		if ( isset( $_GET['utmdc-error'] ) ) {
			$error = $this->get_error_message( intval( $_GET['utmdc-error'] ) );

			if ( ! empty( $error['message'] ) ) {
				$contents[] = sprintf(
					'<div class="notice %s"><p>%s</p></div>',
					esc_html( $error['style'] ),
					esc_html( $error['message'] )
				);
			}
		}

		$contents = array_merge(
			$contents,
			array_map(
				function( $key, $entry ) use ( $post ) {
					$value = '';

					if ( 'object' === gettype( $post ) ) {
						$value = get_post_meta( $post->ID, self::POST_TYPE . '_' . $key, true );

						if ( 'url' === $entry['type'] ) {
							$value = esc_url( $value );
						} else {
							$value = esc_attr( $value );
						}
					}

					return sprintf(
						'<p><label for="%1$s_%2$s" class="%1$s_%2$s">%3$s<br><input type="%4$s" name="%1$s_%2$s" id="%1$s_%2$s"%5$s value="%6$s"><span>%7$s</span></label></p>',
						self::POST_TYPE,
						$key,
						esc_html( $entry['label'] ),
						esc_html( $entry['type'] ),
						( $entry['required'] ? ' required="required"' : '' ),
						isset( $value ) ? esc_html( $value ) : '',
						( $entry['batch_alt'] ) ? $this->batch_alt( $key ) : ''
					);
				},
				array_keys( $this->link_elements ),
				$this->link_elements
			)
		);

		$is_default_shortener      = ( 'none' !== get_option( self::POST_TYPE . '_shortener' ) );
		$is_api_key_set            = ( '' !== get_option( self::POST_TYPE . '_apikey' ) );
		$is_using_custom_shortener = ( false !== apply_filters( 'utmdc_shorten_object', false ) );

		if ( $is_using_custom_shortener || ( $is_default_shortener && $is_api_key_set ) ) {
			array_unshift(
				$contents,
				sprintf(
					'<p><label for="%1$s_%2$s" class="selectit"><input type="checkbox" name="%1$s_%2$s" id="%1$s_%2$s">%3$s</label></p>',
					self::POST_TYPE,
					'shorten',
					esc_html__( 'Shorten Completed Link When Saving', 'utm-dot-codes' )
				)
			);
		}

		array_unshift(
			$contents,
			sprintf(
				'<input type="hidden" name="%s" value="%s">',
				self::NONCE_LABEL,
				wp_create_nonce( UTMDC_PLUGIN_FILE )
			)
		);

		if ( ! empty( $post->post_content ) && '' !== $post->post_content ) {
			array_unshift(
				$contents,
				sprintf(
					'<p><b>%1$s</b><br><a href="%2$s" target="_blank">%2$s</a></p>',
					esc_html__( 'Marketing Link', 'utm-dot-codes' ),
					esc_html( $post->post_content )
				)
			);

			$contents[] = sprintf(
				'<p><label for="%1$s_%2$s" class="%1$s_%2$s">%3$s<br><textarea name="%1$s_%2$s" id="%1$s_%2$s">%4$s</textarea></p>',
				self::POST_TYPE,
				'notes',
				esc_html__( 'Notes', 'utm-dot-codes' ),
				esc_html( get_post_meta( $post->ID, self::POST_TYPE . '_notes', true ) )
			);
		} else {
			$social_setting = get_option( self::POST_TYPE . '_social' );
			if ( 'array' === gettype( $social_setting ) && count( $social_setting ) > 0 ) {
				array_unshift(
					$contents,
					sprintf(
						'<p><label for="%1$s_%2$s" class="selectit"><input type="checkbox" name="%1$s_%2$s" id="%1$s_%2$s">%3$s</label></p>',
						self::POST_TYPE,
						'batch',
						esc_html__( 'Create Social Links in Batch', 'utm-dot-codes' )
					)
				);
			}
		}

		if ( $this->is_test() ) {
			return $contents;
		} else {
			echo implode( PHP_EOL, $contents );
		}
	}

	/**
	 * Register links plugin settings page.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_options_page(
			esc_html__( 'utm.codes Plugin Settings', 'utm-dot-codes' ),
			esc_html__( 'utm.codes', 'utm-dot-codes' ),
			'manage_options',
			self::SETTINGS_PAGE,
			array( &$this, 'render_settings_options' )
		);
	}

	/**
	 * Generate and output links settings page options.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_options() {
		require_once 'shorten/interface.php';

		$networks      = $this->get_social_networks();
		$lowercase     = ( 'on' === get_option( self::POST_TYPE . '_lowercase' ) );
		$alphanumeric  = ( 'on' === get_option( self::POST_TYPE . '_alphanumeric' ) );
		$nospaces      = ( 'on' === get_option( self::POST_TYPE . '_nospaces' ) );
		$labels        = ( 'on' === get_option( self::POST_TYPE . '_labels' ) );
		$show_notes    = ( 'on' === get_option( self::POST_TYPE . '_notes_show' ) );
		$preview_notes = intval( get_option( self::POST_TYPE . '_notes_preview' ) );
		?>

		<div class="wrap">

			<form method="post" action="options.php">
				<h1>
					<img src="<?php echo esc_url( UTMDC_PLUGIN_URL ); ?>img/utm-dot-codes-logo.png" id="utm_dot_codes_logo" alt="utm.codes Settings" title="Configure your utm.codes plugin here.">
				</h1>
				<h1 class="title">
					<?php esc_html_e( 'Link Format Options', 'utm-dot-codes' ); ?>
				</h1>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Force Lowercase:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<div class="utmdclinks-settings-toggle"><input id="%1$s" name="%1$s" type="checkbox" %2$s><label for="%1$s"><div data-on="%3$s" data-off="%4$s"></div></label></div>',
								esc_html( self::POST_TYPE . '_lowercase' ),
								esc_html( checked( $lowercase, true, false ) ),
								esc_html__( 'On', 'utm-dot-codes' ),
								esc_html__( 'Off', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Alphanumeric Only:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<div class="utmdclinks-settings-toggle"><input id="%1$s" name="%1$s" type="checkbox" %2$s><label for="%1$s"><div data-on="%3$s" data-off="%4$s"></div></label></div>',
								esc_html( self::POST_TYPE . '_alphanumeric' ),
								esc_html( checked( $alphanumeric, true, false ) ),
								esc_html__( 'On', 'utm-dot-codes' ),
								esc_html__( 'Off', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Remove Spaces:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<div class="utmdclinks-settings-toggle"><input id="%1$s" name="%1$s" type="checkbox" %2$s><label for="%1$s"><div data-on="%3$s" data-off="%4$s"></div></label></div>',
								esc_html( self::POST_TYPE . '_nospaces' ),
								esc_html( checked( $nospaces, true, false ) ),
								esc_html__( 'On', 'utm-dot-codes' ),
								esc_html__( 'Off', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
				</table>
				<p>
					<?php
						printf(
							'%s <a href="https://github.com/asdfdotdev/utm.codes/wiki/Link-Formats" target="_blank">%s</a>',
							esc_html__( 'Adding your own custom link formatting is easy with an API filter.', 'utm-dot-codes' ),
							esc_html__( 'Visit our wiki for examples and to find out more.', 'utm-dot-codes' )
						);
					?>
				</p>
				<h1 class="title">
					<?php esc_html_e( 'Advanced Options', 'utm-dot-codes' ); ?>
				</h1>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Link Labels:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<div class="utmdclinks-settings-toggle"><input id="%1$s" name="%1$s" type="checkbox" %2$s><label for="%1$s"><div data-on="%3$s" data-off="%4$s"></div></label></div>',
								esc_html( self::POST_TYPE . '_labels' ),
								esc_html( checked( $labels, true, false ) ),
								esc_html__( 'On', 'utm-dot-codes' ),
								esc_html__( 'Off', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Notes in Link List:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<div class="utmdclinks-settings-toggle"><input id="%1$s" name="%1$s" type="checkbox" %2$s><label for="%1$s"><div data-on="%3$s" data-off="%4$s"></div></label></div>',
								esc_html( self::POST_TYPE . '_notes_show' ),
								esc_html( checked( $show_notes, true, false ) ),
								esc_html__( 'On', 'utm-dot-codes' ),
								esc_html__( 'Off', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
					<tr valign="top" id="utmdclinks_notes_preview_row"<?php echo ( ! $show_notes ) ? 'class="hidden"' : ''; ?>">
						<th scope="row">
							<?php esc_html_e( 'Notes Preview Length:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<div class="utmdclinks-settings-slider"><input id="%1$s" name="%1$s" type="range" min="0" max="50" value="%2$d" step="1"><output></output></div>',
								esc_html( self::POST_TYPE . '_notes_preview' ),
								intval( $preview_notes )
							);
							?>
							<p>
								<br>
								<?php esc_html_e( 'Set to 0 to output complete notes.', 'utm-dot-codes' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<h1 class="title">
					<?php esc_html_e( 'Social Sources', 'utm-dot-codes' ); ?>
				</h1>
				<p>
					<?php esc_html_e( 'Select sites to include when batch creating social links.', 'utm-dot-codes' ); ?>
				</p>
				<div class="utmdclinks-settings-social">
					<?php
					settings_fields( self::SETTINGS_GROUP );

					$active_networks = get_option( self::POST_TYPE . '_social' );

					array_map(
						function( $key, $value ) use ( $active_networks ) {
							printf(
								'<label for="%1$s"><i class="%2$s"></i><input type="checkbox" name="%1$s" id="%1$s" %4$s/>%3$s</label>',
								esc_html( self::POST_TYPE . '_social[' . $key . ']' ),
								esc_html( $value[1] ),
								esc_html( $value[0] ),
								checked( isset( $active_networks[ $key ] ), true, false )
							);
						},
						array_keys( $networks ),
						$networks
					);
					?>
				</div>
				<p>
					<?php
					printf(
						'%s <a href="https://github.com/asdfdotdev/utm.codes/wiki/Social-Networks" target="_blank">%s</a>',
						esc_html__( 'Adding your own custom network options is easy with an API filter.', 'utm-dot-codes' ),
						esc_html__( 'Visit our wiki for examples and to find out more.', 'utm-dot-codes' )
					);
					?>
				</p>
				<h1 class="title">
					<?php esc_html_e( 'URL Shortener', 'utm-dot-codes' ); ?>
				</h1>
				<p>
					<?php esc_html_e( 'Setup api access to enable link shortening.', 'utm-dot-codes' ); ?>
				</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Shortener Service:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							$active_shortener = 'none';

							if ( false === apply_filters( 'utmdc_shorten_object', false ) ) {

								$active_shortener = get_option( self::POST_TYPE . '_shortener' );

								printf(
									'<select id="%1$s" name="%1$s" class="utmdclinks-settings-select">',
									esc_html( self::POST_TYPE . '_shortener' )
								);

								array_map(
									function ( $value ) use ( $active_shortener ) {
										printf(
											'<option value="%s"%s>%s</option>',
											esc_html( strtolower( $value ) ),
											( strtolower( $value ) === $active_shortener ? ' selected="selected"' : '' ),
											esc_html( $value )
										);
									},
									array( 'None', 'Bitly', 'Rebrandly' )
								);

								print( '</select>' );

							} else {

								echo esc_html__( 'Custom shortener in use via API filter. Remove to use default options.', 'utm-dot-codes' );

							}
							?>
						</td>
					</tr>
					<tr valign="top" id="utmdclinks_shortener_api_row" class="<?php echo ( 'none' === $active_shortener ) ? 'hidden' : ''; ?>">
						<th scope="row">
							<?php esc_html_e( 'API Key:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							printf(
								'<input type="text" name="%s" value="%s" size="40">',
								esc_html( self::POST_TYPE . '_apikey' ),
								esc_html( get_option( self::POST_TYPE . '_apikey' ) )
							);

							printf(
								'<br><sup>[ %s <a href="https://github.com/asdfdotdev/utm.codes/wiki/Setup-&-Config" target="_blank">%s</a> ]</sup>',
								esc_html__( 'API Questions?', 'utm-dot-codes' ),
								esc_html__( 'Click here for more additional details.', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
					<tr valign="top" id="utmdclinks_shortener_custom_domain_row" class="<?php echo ( 'rebrandly' !== $active_shortener ) ? 'hidden' : ''; ?>">
						<th scope="row">
							<?php esc_html_e( 'Short Domain:', 'utm-dot-codes' ); ?>
						</th>
						<td>
							<?php
							$rebrandly_domain  = get_option( self::POST_TYPE . '_rebrandly_domains_active' );
							$rebrandly_domains = json_decode( get_option( self::POST_TYPE . '_rebrandly_domains', true ) );
							$domains           = array_merge(
								array(
									(object) array(
										'id'        => '',
										'full_name' => 'rebrand.ly',
									),
								),
								is_array( $rebrandly_domains ) ? $rebrandly_domains : array()
							);

							printf(
								'<input id="%1$s" name="%1$s" type="hidden" value="%2$s">',
								esc_html( self::POST_TYPE . '_rebrandly_domains' ),
								esc_html( wp_json_encode( $rebrandly_domains ) )
							);

							printf(
								'<select id="%1$s" name="%1$s" class="utmdclinks-settings-select">',
								esc_html( self::POST_TYPE . '_rebrandly_domains_active' )
							);

							foreach ( $domains as $domain ) {
								$domain_id = ( ! empty( $domain->id ) ? ' (' . $domain->id . ')' : '' );

								printf(
									'<option value="%s"%s>%s%s</option>',
									esc_html( $domain->id ),
									( $domain->id === $rebrandly_domain ? ' selected="selected"' : '' ),
									esc_html( $domain->full_name ),
									esc_html( $domain_id )
								);
							}

							print( '</select>' );

							printf(
								'<label for="%1$s"><input type="checkbox" name="%1$s" id="%1$s">%2$s</label>',
								esc_html( self::POST_TYPE . '_rebrandly_domains_update' ),
								esc_html__( 'Update Options from Rebrandly.', 'utm-dot-codes' )
							);
							?>
						</td>
					</tr>
				</table>
				<p>
					<?php
					printf(
						'%s <a href="https://github.com/asdfdotdev/utm.codes/wiki/Shortener-Integration" target="_blank">%s</a>',
						esc_html__( 'Adding your own custom link shortener is easy.', 'utm-dot-codes' ),
						esc_html__( 'Visit our wiki for examples and to find out more.', 'utm-dot-codes' )
					);
					?>
				</p>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php

	}

	/**
	 * Register plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_settings() {
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_social' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_apikey' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_lowercase' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_alphanumeric' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_nospaces' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_labels' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_notes_show' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_notes_preview' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_shortener' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_rebrandly_domains' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_rebrandly_domains_active' );
		register_setting( self::SETTINGS_GROUP, self::POST_TYPE . '_rebrandly_domains_update' );
	}

	/**
	 * Add our special links for display beside the utm.codes plugin in the installed plugins list.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Plugin action links.
	 *
	 * @return array Updated array of links.
	 */
	public function add_links( $links ) {
		return array_merge(
			array(
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'options-general.php?page=' . self::SETTINGS_PAGE ) ),
					esc_html__( 'Settings', 'utm-dot-codes' )
				),
				sprintf(
					'<a href="https://github.com/asdfdotdev/utm.codes" target="_blank">%s</a>',
					esc_html__( 'Code', 'utm-dot-codes' )
				),
			),
			$links
		);
	}

	/**
	 * Update link post meta data when saving the link.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The Post ID.
	 *
	 * @return int|void Post ID if changes are made, void if not changes are made.
	 */
	public function save_post( $post_id ) {
		$invalid_nonce  = ( isset( $_POST[ self::NONCE_LABEL ] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_LABEL ] ) ), UTMDC_PLUGIN_FILE ) );
		$doing_autosave = ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE );
		$cannot_edit    = ( ! current_user_can( 'publish_posts', $post_id ) );

		if ( $invalid_nonce || $doing_autosave || $cannot_edit ) {
			return $post_id;
		}

		if ( isset( $_POST['post_type'] ) && self::POST_TYPE === sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) ) {
			$valid_post_id = false;

			if ( isset( $_POST['ID'] ) ) {
				$valid_post_id = ( intval( $_POST['ID'] ) === $post_id );
			}

			if ( ! $valid_post_id ) {
				add_filter(
					'redirect_post_location',
					function( $location ) {
						return add_query_arg( 'utmdc-error', '2', $location );
					}
				);

				return $post_id;
			}

			if ( isset( $_POST[ self::POST_TYPE . '_url' ] ) ) {
				$_POST[ self::POST_TYPE . '_url' ] = $this->validate_url( esc_url_raw( wp_unslash( $_POST[ self::POST_TYPE . '_url' ] ) ) );
			}

			if ( isset( $_POST[ self::POST_TYPE . '_batch' ] ) && 'on' === $_POST[ self::POST_TYPE . '_batch' ] ) {
				$networks = array_keys( get_option( self::POST_TYPE . '_social' ) );

				$_POST[ self::POST_TYPE . '_source' ] = $networks[0];
				$_POST[ self::POST_TYPE . '_medium' ] = esc_html__( 'social', 'utm-dot-codes' );
				unset( $networks[0] );

				$post_template = array();

				array_map(
					function( $key ) use ( &$post_template ) {
						if ( isset( $_POST[ self::POST_TYPE . '_' . $key ] ) ) {
							$post_template[ self::POST_TYPE . '_' . $key ] = sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_' . $key ] ) );
						}
					},
					array_keys( $this->link_elements )
				);

				remove_action( 'save_post', array( &$this, 'save_post' ) );

				array_map(
					function( $network ) {
						$meta_input = array(
							self::POST_TYPE . '_source' => $network,
							self::POST_TYPE . '_medium' => esc_html__( 'social', 'utm-dot-codes' ),
						);

						if ( isset( $_POST[ self::POST_TYPE . '_url' ] ) ) {
							$meta_input[ self::POST_TYPE . '_url' ] = $this->sanitize_url(
								sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_url' ] ) )
							);
						}

						if ( isset( $_POST[ self::POST_TYPE . '_campaign' ] ) ) {
							$meta_input[ self::POST_TYPE . '_campaign' ] = $this->filter_link_element(
								'utm_campaign',
								sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_campaign' ] ) )
							);
						}

						if ( isset( $_POST[ self::POST_TYPE . '_term' ] ) ) {
							$meta_input[ self::POST_TYPE . '_term' ] = $this->filter_link_element(
								'utm_term',
								sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_term' ] ) )
							);
						}

						if ( isset( $_POST[ self::POST_TYPE . '_content' ] ) ) {
							$meta_input[ self::POST_TYPE . '_content' ] = $this->filter_link_element(
								'utm_content',
								sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_content' ] ) )
							);
						}

						$new_post = array(
							'post_title'   => '',
							'post_content' => '',
							'post_type'    => self::POST_TYPE,
							'post_status'  => 'publish',
							'meta_input'   => $meta_input,
						);

						if ( isset( $_POST[ self::POST_TYPE . '_shorten' ] ) ) {
							if ( 'on' === sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_shorten' ] ) ) ) {
								$new_post['meta_input'][ self::POST_TYPE . '_shorturl' ] = $this->generate_short_url( $new_post, $new_post['meta_input'][ self::POST_TYPE . '_url' ] );
							}
						}

						$new_post_id = wp_insert_post( $new_post );

						if ( $new_post_id > 0 && isset( $_POST['tax_input'][ self::POST_TYPE . '-label' ] ) ) {

							if ( is_array( $_POST['tax_input'][ self::POST_TYPE . '-label' ] ) ) {
								$post_labels = array_map(
									'sanitize_text_field',
									wp_unslash( $_POST['tax_input'][ self::POST_TYPE . '-label' ] )
								);
							} else {
								$post_labels = explode(
									',',
									sanitize_text_field( wp_unslash( $_POST['tax_input'][ self::POST_TYPE . '-label' ] ) )
								);
							}

							wp_set_object_terms(
								$new_post_id,
								$post_labels,
								self::POST_TYPE . '-label',
								false
							);
						}

					},
					$networks
				);

				add_action( 'save_post', array( &$this, 'save_post' ), 10, 1 );
			}

			if ( isset( $_POST[ self::POST_TYPE . '_shorten' ] ) && 'on' === $_POST[ self::POST_TYPE . '_shorten' ] ) {
				$_POST[ self::POST_TYPE . '_shorturl' ] = $this->generate_short_url( $_POST, sanitize_text_field( wp_unslash( $_POST[ self::POST_TYPE . '_url' ] ) ) );
			}

			array_map(
				function( $key ) {
					$field         = self::POST_TYPE . '_' . $key;
					$post_id       = absint( $_POST['ID'] );
					$current       = get_post_meta( $post_id, $field, true );
					$updated       = '';
					$do_not_filter = array(
						self::POST_TYPE . '_url',
						self::POST_TYPE . '_shorturl',
						self::POST_TYPE . '_notes',
					);

					if ( isset( $_POST[ $field ] ) ) {
						if ( self::POST_TYPE . '_notes' === $field ) {
							$updated = sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) );
						} else {
							$updated = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
						}
					}

					if ( ! in_array( $field, $do_not_filter, true ) ) {
						$updated = $this->filter_link_element( 'utm_' . $key, $updated );
					}

					if ( '' === $updated ) {
						delete_post_meta( $post_id, $field, $current );
					} elseif ( isset( $updated ) && $updated !== $current ) {
						update_post_meta( $post_id, $field, $updated );
					}
				},
				array_merge(
					array_keys( $this->link_elements ),
					array( 'notes' )
				)
			);

			$this->delete_cache();
		}

		unset( $_POST[ self::POST_TYPE . '_batch' ] );
		unset( $_POST[ self::POST_TYPE . '_shorten' ] );
	}

	/**
	 * Update post title and content to include complete link when inserting new link posts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Array of slashed post data.
	 * @param array $postarr Array of sanitized, but otherwise unmodified post data.
	 *
	 * @return array Updated $data array of post data with completed link.
	 */
	public function insert_post_data( $data, $postarr ) {
		if ( isset( $postarr['post_type'] ) && self::POST_TYPE === $postarr['post_type'] ) {
			if ( 'publish' === $postarr['post_status'] && isset( $postarr['meta_input'] ) ) {
				$data['post_title']   = $this->validate_url( $postarr['meta_input'][ self::POST_TYPE . '_url' ] );
				$data['post_content'] = $data['post_title'] . $this->generate_query_string( $postarr['meta_input'], $data['post_title'] );
			} elseif ( isset( $postarr[ self::POST_TYPE . '_url' ] ) ) {
				if ( isset( $postarr[ self::POST_TYPE . '_batch' ] ) && 'on' === $postarr[ self::POST_TYPE . '_batch' ] ) {
					$networks                               = array_keys( get_option( self::POST_TYPE . '_social' ) );
					$postarr[ self::POST_TYPE . '_source' ] = $networks[0];
					$postarr[ self::POST_TYPE . '_medium' ] = 'social';
				}

				$data['post_title']   = $this->validate_url( $postarr[ self::POST_TYPE . '_url' ] );
				$data['post_content'] = $data['post_title'] . $this->generate_query_string( $postarr, sanitize_text_field( $data['post_title'] ) );
			}
		}

		return $data;
	}

	/**
	 * Generate link query string by combining separate link utm values. This method will accept either a post
	 * array with individual utm entries or an array with utm entries contained within a meta_input entry.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data Array of post data containing associative utm input values, or Array of post data.
	 *  containing meta_input array with associative utm input values.
	 * @param string $url String url for which query string is being prepared, for handling custom injected params.
	 *
	 * @return string Prepared link query string with configured utm parameters.
	 */
	public function generate_query_string( $data, $url ) {
		if ( isset( $data['meta_input'] ) ) {
			$data = $data['meta_input'];
		}

		$params_array = array_filter(
			array(
				'utm_source'   => $this->filter_link_element(
					'utm_source',
					$data[ self::POST_TYPE . '_source' ]
				),
				'utm_medium'   => $this->filter_link_element(
					'utm_medium',
					$data[ self::POST_TYPE . '_medium' ]
				),
				'utm_campaign' => $this->filter_link_element(
					'utm_campaign',
					$data[ self::POST_TYPE . '_campaign' ]
				),
				'utm_term'     => $this->filter_link_element(
					'utm_term',
					$data[ self::POST_TYPE . '_term' ]
				),
				'utm_content'  => $this->filter_link_element(
					'utm_content',
					$data[ self::POST_TYPE . '_content' ]
				),
				'utm_gen'      => 'utmdc',
			)
		);

		return ( strpos( $url, '?' ) ? '&' : '?' ) . http_build_query( $params_array );
	}

	/**
	 * Retrieve short url, if unsuccessful add error code to return location.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data Array of post data containing associative utm input values, or Array of post data
	 * containing meta_input array with associative utm input values.
	 * @param string $url Base Url of link being shortened, used for generating query string.
	 *
	 * @return string Shortened url if request successful, empty string if not.
	 */
	public function generate_short_url( $data, $url ) {
		require_once 'shorten/interface.php';

		$short_url = '';
		$shortener = null;
		$error     = false;

		switch ( get_option( self::POST_TYPE . '_shortener' ) ) {
			case 'bitly':
				require_once 'shorten/class-bitly.php';
				$shortener = new \UtmDotCodes\Bitly(
					get_option( self::POST_TYPE . '_apikey' )
				);
				break;

			case 'rebrandly':
				require_once 'shorten/class-rebrandly.php';
				$shortener = new \UtmDotCodes\Rebrandly(
					get_option( self::POST_TYPE . '_apikey' ),
					get_option( self::POST_TYPE . '_rebrandly_domains_active' )
				);
				break;

			case 'none':
			default:
				break;
		}

		$shortener = apply_filters( 'utmdc_shorten_object', $shortener );

		if ( $shortener instanceof \UtmDotCodes\Shorten ) {
			try {
				$shortener->shorten( $data, $this->generate_query_string( $data, $url ) );

				if ( empty( $shortener->get_error() ) ) {
					$short_url = $shortener->get_response();
				} else {
					add_filter(
						'redirect_post_location',
						function( $location ) use ( $shortener ) {
							return add_query_arg( 'utmdc-error', $shortener->get_error(), $location );
						}
					);
				}
			} catch ( Exception $exception ) {
				$error = true;
			}
		} else {
			$error = true;
		}

		if ( $error ) {
			add_filter(
				'redirect_post_location',
				function( $location ) {
					return add_query_arg( 'utmdc-error', '1000', $location );
				}
			);
		}

		return $short_url;
	}

	/**
	 * Update links post list to include link element columns, remove title and date columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Array of column name => label.
	 *
	 * @return array Updated column array, with new columns added.
	 */
	public function post_list_header( $columns ) {
		unset( $columns['cb'] );
		unset( $columns['title'] );
		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns = array_merge(
			array(
				'cb'              => '<input type="checkbox" />',
				'utmdc_link'      => esc_html__( 'Link', 'utm-dot-codes' ),
				'utmdc_source'    => esc_html__( 'Source', 'utm-dot-codes' ),
				'utmdc_medium'    => esc_html__( 'Medium', 'utm-dot-codes' ),
				'utmdc_campaign'  => esc_html__( 'Campaign', 'utm-dot-codes' ),
				'utmdc_term'      => esc_html__( 'Term', 'utm-dot-codes' ),
				'utmdc_content'   => esc_html__( 'Content', 'utm-dot-codes' ),
				'utmdc_notes'     => esc_html__( 'Notes', 'utm-dot-codes' ),
				'copy_utmdc_link' => esc_html__( 'Copy Links', 'utm-dot-codes' ),
			),
			$columns
		);

		if ( 'on' !== get_option( self::POST_TYPE . '_notes_show' ) ) {
			unset( $columns['utmdc_notes'] );
		}

		return $columns;
	}

	/**
	 * Output link element contents within links post list custom columns.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $column_name Name of column to display.
	 * @param integer $post_id Post ID of current post.
	 */
	public function post_list_columns( $column_name, $post_id ) {
		if ( 'utmdc_link' === $column_name ) {
			printf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( get_post_meta( $post_id, self::POST_TYPE . '_url', true ) ),
				esc_html( wp_strip_all_tags( get_the_content() ) )
			);
		} elseif ( 'utmdc_source' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, self::POST_TYPE . '_source', true ) );
		} elseif ( 'utmdc_medium' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, self::POST_TYPE . '_medium', true ) );
		} elseif ( 'utmdc_campaign' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, self::POST_TYPE . '_campaign', true ) );
		} elseif ( 'utmdc_term' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, self::POST_TYPE . '_term', true ) );
		} elseif ( 'utmdc_content' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, self::POST_TYPE . '_content', true ) );
		} elseif ( 'utmdc_notes' === $column_name ) {
			$notes_length = intval( get_option( self::POST_TYPE . '_notes_preview' ) );
			$notes        = esc_html( get_post_meta( $post_id, self::POST_TYPE . '_notes', true ) );

			if ( 0 < $notes_length ) {
				$notes = wp_trim_words(
					$notes,
					$notes_length
				);
			}

			echo esc_html( $notes );
		} elseif ( 'copy_utmdc_link' === $column_name ) {
			printf(
				'%s <input type="text" value="%s" readonly="readonly" class="utmdclinks-copy">',
				esc_html_x( 'Full:', 'utm-dot-codes' ),
				esc_url_raw( get_the_content() )
			);

			$short_url = get_post_meta( $post_id, self::POST_TYPE . '_shorturl', true );

			if ( $short_url ) {
				printf(
					'%s <input type="text" value="%s" readonly="readonly" class="utmdclinks-copy">',
					esc_html_x( 'Short:', 'utm-dot-codes' ),
					esc_url_raw( $short_url )
				);

				$is_bitly     = 'bit.ly' === wp_parse_url( $short_url, PHP_URL_HOST );
				$is_rebrandly = 'rebrand.ly' === wp_parse_url( $short_url, PHP_URL_HOST );

				if ( $is_bitly || $is_rebrandly ) {
					printf(
						'<a href="%s+" target="_blank"><i class="fas fa-chart-line"></i> %s</a>',
						esc_url_raw( $short_url ),
						esc_html_x( 'View Report', 'utm-dot-codes' )
					);
				}
			}
		}
	}

	/**
	 * Filter post list query based on user filter selection.
	 *
	 * @since 1.0.0
	 *
	 * @param object $query WP_Query object to filter.
	 *
	 * @return object Updated query with filtered query vars.
	 */
	public function apply_filters( $query ) {

		if ( self::POST_TYPE === $query->query['post_type'] ) {

			$filters = array_keys( $this->link_elements );
			unset( $filters['url'] );

			$meta_query = array_filter(
				array_map(
					function( $filter ) {
						$filter_name            = self::POST_TYPE . '_' . $filter;
						$filter_value           = rawurldecode( filter_input( INPUT_GET, $filter_name, FILTER_SANITIZE_STRING ) );
						$sanitized_filter_value = sanitize_text_field( wp_unslash( $filter_value ) );

						if ( ! empty( $sanitized_filter_value ) ) {
							return array(
								'key'     => $filter_name,
								'value'   => $sanitized_filter_value,
								'compare' => '=',
							);
						}
					},
					$filters
				)
			);

			$query->set( 'meta_query', $meta_query );

		}

		return $query;
	}

	/**
	 * Create UI filters displayed above post list in admin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type slug.
	 *
	 * @return array|void
	 */
	public function filter_ui( $post_type ) {
		global $wpdb;

		if ( self::POST_TYPE === $post_type ) {

			$filter_options = $this->link_elements;
			unset( $filter_options['url'] );
			unset( $filter_options['shorturl'] );

			$markup = array_map(
				function( $key, $filter ) use ( $wpdb ) {
					$cached_key    = self::POST_TYPE . '_options_' . $key;
					$cached_values = wp_cache_get( $cached_key );

					if ( false === $cached_values ) {
						$cached_values = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT DISTINCT(meta_value)
						FROM $wpdb->postmeta
						WHERE meta_key = %s
							AND meta_value != ''
							AND post_id IN ( SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status != 'trash' )
						ORDER BY meta_value",
								self::POST_TYPE . '_' . $key,
								self::POST_TYPE
							)
						);

						wp_cache_set( $cached_key, $cached_values );
					}

					$options = array_map(
						function ( $value ) use ( $key ) {
							$key_value        = '';
							$active_key_value = filter_input( INPUT_GET, self::POST_TYPE . '_' . $key, FILTER_SANITIZE_STRING );

							if ( isset( $active_key_value ) ) {
								$key_value = sanitize_text_field( wp_unslash( $active_key_value ) );
							}

							return sprintf(
								'<option value="%s"%s>%s</option>',
								rawurlencode( $value->meta_value ),
								selected(
									$value->meta_value,
									rawurldecode( $key_value ),
									false
								),
								$value->meta_value
							);
						},
						$cached_values
					);

					return sprintf(
						'<select id="filter-by-%1$s" name="%1$s"><option value="">%2$s %3$s</option>%4$s</select>',
						self::POST_TYPE . '_' . $key,
						esc_html__( 'Any', 'utm-dot-codes' ),
						$filter['short_label'],
						implode( PHP_EOL, $options )
					);

				},
				array_keys( $filter_options ),
				$filter_options
			);

			if ( 'on' === get_option( self::POST_TYPE . '_labels' ) ) {

				$terms = get_terms(
					array(
						'taxonomy'   => self::POST_TYPE . '-label',
						'hide_empty' => true,
					)
				);

				$term_options = array_map(
					function( $key, $value ) {
						$label              = '';
						$active_label_value = filter_input( INPUT_GET, self::POST_TYPE . '_' . $label, FILTER_SANITIZE_STRING );

						if ( isset( $active_label_value ) ) {
							$label = sanitize_text_field( wp_unslash( $active_label_value ) );
						}

						return sprintf(
							'<option value="%s"%s>%s (%s)</option>',
							rawurlencode( str_replace( ' ', '-', $value->name ) ),
							selected(
								str_replace( ' ', '-', $value->name ),
								rawurldecode( $label ),
								false
							),
							$value->name,
							$value->count
						);
					},
					array_keys( $terms ),
					$terms
				);

				$markup[] = sprintf(
					'<select id="filter-by-%1$s" name="%1$s"><option value="">%2$s</option>%3$s</select>',
					self::POST_TYPE . '-label',
					esc_html__( 'Any Label', 'utm-dot-codes' ),
					implode( PHP_EOL, $term_options )
				);
			}

			if ( $this->is_test() ) {
				return $markup;
			} else {
				echo implode( PHP_EOL, $markup );
			}
		}

	}

	/**
	 * Remove Edit from links bulk actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $bulk_actions Array of bulk action links.
	 *
	 * @return array Updated bulk action links array.
	 */
	public function bulk_actions( $bulk_actions ) {
		unset( $bulk_actions['edit'] );
		return $bulk_actions;
	}

	/**
	 * Enqueue utm.codes css, uses hashed file contents for version.
	 *
	 * @since 1.0.0
	 */
	public function add_css() {
		wp_enqueue_style(
			'font-awesome',
			'https://use.fontawesome.com/releases/v5.15.0/css/all.css',
			array(),
			UTMDC_VERSION,
			'all'
		);
		wp_enqueue_style(
			'utm-dot-codes',
			UTMDC_PLUGIN_URL . 'css/utmdotcodes.css',
			array( 'font-awesome' ),
			hash_file( 'sha1', UTMDC_PLUGIN_DIR . 'css/utmdotcodes.css' ),
			'all'
		);
	}

	/**
	 * Enqueue utm.codes links javascript using hashed file contents for version, add rest object with localize script.
	 *
	 * @since 1.0.0
	 */
	public function add_js() {
		wp_enqueue_script(
			'utm-dot-codes',
			UTMDC_PLUGIN_URL . 'js/utmdotcodes.js',
			array( 'jquery' ),
			hash_file( 'sha1', UTMDC_PLUGIN_DIR . 'js/utmdotcodes.js' ),
			'all'
		);

		wp_localize_script(
			'utm-dot-codes',
			'utmdcRestApi',
			array(
				'actionKey' => wp_create_nonce( self::REST_NONCE_LABEL ),
			)
		);
	}

	/**
	 * Generate alternate output for social inputs in link creation form to display when batch creation active.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Name of link element.
	 *
	 * @return string Content to output with form (may include markup).
	 */
	public function batch_alt( $key ) {
		$alt = '';

		switch ( $key ) {

			case 'source':
				$networks      = get_option( self::POST_TYPE . '_social' );
				$network_count = 0;

				if ( is_array( $networks ) ) {
					$network_count = count( $networks );
				}

				$alt = sprintf(
					'%s <a href="%s" target="_blank" tabindex="-1">%s %s %s</a>.',
					esc_html_x( 'Individual links will be created with unique source for', 'Batch link creation notice, links to settings page with number of active networks', 'utm-dot-codes' ),
					esc_url( admin_url( 'options-general.php?page=' . self::SETTINGS_PAGE ) ),
					absint( $network_count ),
					esc_html__( 'active', 'utm-dot-codes' ),
					esc_html( _n( 'network', 'networks', $network_count, 'utm-dot-codes' ) )
				);
				break;

			case 'medium':
				$alt = __( 'social', 'utm-dot-codes' );
				break;

			case 'url':
				$alt = sprintf(
					'<i class="fas fa-question-circle" title="%s"></i><i class="fas fa-circle" title="%s"></i><i class="fas fa-times-circle" title="%s"></i><i class="fas fa-check-circle" title="%s"></i>',
					esc_html__( 'Unable to validate url, please check manually.', 'utm-dot-codes' ),
					esc_html__( 'Update Link URL to check status.', 'utm-dot-codes' ),
					esc_html__( 'Link appears invalid, please check before saving.', 'utm-dot-codes' ),
					esc_html__( 'Link appears valid.', 'utm-dot-codes' )
				);
				break;

		}

		return $alt;
	}

	/**
	 * Load utm.codes links language files.
	 *
	 * @since 1.0.0
	 */
	public function load_languages() {
		load_theme_textdomain( 'utm-dot-codes', UTMDC_PLUGIN_DIR . 'languages' );
	}

	/**
	 * Add link count to dashboard glance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $glances Array of extra 'At a Glance' widget items.
	 *
	 * @return array Updated array with additional widget items.
	 */
	public function add_glance( $glances ) {
		if ( current_user_can( 'publish_posts' ) ) {
			$post_count  = number_format_i18n( wp_count_posts( self::POST_TYPE )->publish );
			$post_object = get_post_type_object( self::POST_TYPE );

			$glances[] = sprintf(
				'<a href="%s" class="%s">%s %s</a>',
				admin_url( 'edit.php?post_type=' . $post_object->name ),
				'utmdclink-count',
				$post_count,
				_n( 'Marketing Link', 'Marketing Links', $post_count, 'utm-dot-codes' )
			);
		}

		return $glances;
	}

	/**
	 * Validate URL input, if invalid substitute with site url and add error notice.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url String to validate as URL.
	 *
	 * @return string Validated URL or site URL on error.
	 */
	public function validate_url( $url ) {

		/**
		 * Regex filter taken from: https://mathiasbynens.be/demo/url-regex
		 */
		preg_match( '@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $url, $matches );

		if ( empty( $matches ) ) {
			$url = get_home_url( null, '/' );

			add_filter(
				'redirect_post_location',
				function( $location ) {
					return add_query_arg( 'utmdc-error', '1', $location );
				}
			);
		}

		$url = $this->sanitize_url( wp_unslash( $url ) );

		return $url;
	}

	/**
	 * Wrapper for WordPress esc_url() to provide better contextual indication of what we're doing to the url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url String to validate as URL.
	 *
	 * @return string Sanitized url string.
	 */
	public function sanitize_url( $url ) {
		return esc_url( $url );
	}

	/**
	 * Apply format filters to link element based on current settings, additionally passes formatted string
	 * to sanitize_text_field() to sanitize formatted element value.
	 *
	 * @since 1.1.0
	 *
	 * @param string $element Name of link element being formatted.
	 * @param string $value Value of link element to format.
	 *
	 * @return string Value with formatting applied.
	 */
	public function filter_link_element( $element, $value ) {
		$value = apply_filters( 'utmdc_element_pre_filters', $value, $element );

		if ( 'on' === get_option( self::POST_TYPE . '_alphanumeric' ) ) {
			$value = preg_replace( '/[^A-Za-z0-9\- ]/', '', $value );
		}

		if ( 'on' === get_option( self::POST_TYPE . '_lowercase' ) ) {
			$value = strtolower( $value );
		}

		if ( 'on' === get_option( self::POST_TYPE . '_nospaces' ) ) {
			$value = preg_replace( '/\s+/', '-', $value );
		}

		$value = apply_filters( 'utmdc_element_post_filters', $value, $element );

		return sanitize_text_field( trim( $value ) );
	}

	/**
	 * Check response code for user provided URL and send JSON response back to an ajax request.
	 *
	 * @since 1.2.0
	 */
	public function check_url_response() {
		$response = array(
			'message' => 'Could not process request.',
			'status'  => 500,
		);

		if ( check_ajax_referer( self::REST_NONCE_LABEL, 'key', false ) ) {
			if ( isset( $_REQUEST['action'] ) && 'utmdc_check_url_response' === sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) ) {
				$request_url = '';

				if ( isset( $_REQUEST['url'] ) ) {
					$request_url = sanitize_text_field( wp_unslash( $_REQUEST['url'] ) );
				}

				$is_valid_referer = ( false !== check_ajax_referer( self::REST_NONCE_LABEL, 'key', false ) );
				$is_valid_url     = ( filter_var( $request_url, FILTER_VALIDATE_URL ) === $request_url );

				if ( $is_valid_referer && $is_valid_url ) {
					$args = array();
					if ( $this->is_test() ) {
						$args = array( 'sslverify' => false );
					}

					$url_check = wp_remote_get( $request_url, $args );

					if ( is_wp_error( $url_check ) ) {
						$response['message'] = $url_check->get_error_messages();
					} else {
						$response['status']  = $url_check['response']['code'];
						$response['message'] = $url_check['response']['message'];
					}
				}
			}
		}

		wp_send_json( $response );
	}

	/**
	 * Delete cache entries we create.
	 *
	 * @since 1.4.0
	 */
	private function delete_cache() {
		/**
		 * Delete options cache used for filtering links post list.
		 */
		array_walk(
			$this->link_elements,
			function( $element ) {
				wp_cache_delete( self::POST_TYPE . '_options_' . $element['type'] );
			}
		);
	}

	/**
	 * Remove the months dropdown from our links post list.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $months options for the month dropdown.
	 * @param string $post_type post type value.
	 *
	 * @return array
	 */
	public function months_dropdown_results( $months, $post_type ) {

		if ( self::POST_TYPE === $post_type ) {
			$months = array();
		}

		return $months;
	}

	/**
	 * Retrieve list of filtered social networks for batch link creation.
	 *
	 * @since 1.5.0
	 *
	 * @return mixed|void
	 */
	public function get_social_networks() {
		$networks = apply_filters(
			'utmdc_social_sources',
			array(
				'behance'        => array( 'Behance', 'fab fa-behance' ),
				'blogger'        => array( 'Blogger', 'fab fa-blogger-b' ),
				'digg'           => array( 'Digg', 'fab fa-digg' ),
				'discourse'      => array( 'Discourse', 'fab fa-discourse' ),
				'facebook'       => array( 'Facebook', 'fab fa-facebook-f' ),
				'flickr'         => array( 'Flickr', 'fab fa-flickr' ),
				'github'         => array( 'GitHub', 'fab fa-github' ),
				'goodreads'      => array( 'Goodreads', 'fab fa-goodreads-g' ),
				'hacker-news'    => array( 'Hacker News', 'fab fa-hacker-news' ),
				'instagram'      => array( 'Instagram', 'fab fa-instagram' ),
				'linkedin'       => array( 'LinkedIn', 'fab fa-linkedin-in' ),
				'medium'         => array( 'Medium', 'fab fa-medium-m' ),
				'meetup'         => array( 'Meetup', 'fab fa-meetup' ),
				'mix'            => array( 'Mix', 'fab fa-mix' ),
				'odnoklassniki'  => array( 'Odnoklassniki', 'fab fa-odnoklassniki' ),
				'pinterest'      => array( 'Pinterest', 'fab fa-pinterest-p' ),
				'reddit'         => array( 'Reddit', 'fab fa-reddit-alien' ),
				'slack'          => array( 'Slack', 'fab fa-slack' ),
				'stack-exchange' => array( 'Stack Exchange', 'fab fa-stack-exchange' ),
				'stack-overflow' => array( 'Stack Overflow', 'fab fa-stack-overflow' ),
				'tumblr'         => array( 'Tumblr', 'fab fa-tumblr' ),
				'twitter'        => array( 'Twitter', 'fab fa-twitter' ),
				'vimeo'          => array( 'Vimeo', 'fab fa-vimeo-v' ),
				'vk'             => array( 'VK', 'fab fa-vk' ),
				'weibo'          => array( 'Weibo', 'fab fa-weibo' ),
				'whatsapp'       => array( 'WhatsApp', 'fab fa-whatsapp' ),
				'xing'           => array( 'Xing', 'fab fa-xing' ),
				'yelp'           => array( 'Yelp', 'fab fa-yelp' ),
				'youtube'        => array( 'YouTube', 'fab fa-youtube' ),
			)
		);

		ksort( $networks );

		return $networks;
	}

	/**
	 * Get the current link elements array.
	 *
	 * @since 1.0.0
	 *
	 * @return array of current link elements.
	 */
	public function get_link_elements() {
		return $this->link_elements;
	}

	/**
	 * Determine if class is running in a test.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if running tests false if not.
	 */
	public function is_test() {
		return defined( 'UTMDC_IS_TEST' ) && constant( 'UTMDC_IS_TEST' );
	}

	/**
	 * Remove quick edit from post row action.
	 *
	 * @since 1.5.0
	 *
	 * @param array $actions array of row action links.
	 *
	 * @return mixed
	 */
	public function remove_quick_edit( $actions ) {
		unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}

	/**
	 * Change button text for better clarity.
	 *
	 * @since 1.5.0
	 *
	 * @param string $translation text to translate.
	 * @param string $text text domain.
	 *
	 * @return string changed text.
	 */
	public function change_publish_button( $translation, $text ) {
		global $pagenow;

		$is_new_post_page = ( 'post-new.php' === $pagenow );
		$is_utmdc_post    = ( self::POST_TYPE === filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING ) );

		if ( $is_new_post_page && $is_utmdc_post ) {
			if ( 'Publish' === $text ) {
				$translation = esc_html__( 'Save', 'utm-dot-codes' );
			}
		}

		return $translation;
	}

	/**
	 * Retrieve error message for output based on provided error code.
	 *
	 * @since 1.6.0
	 *
	 * @param integer $error_code numeric value to convert to message.
	 *
	 * @return array error message elements: style & message text.
	 */
	public function get_error_message( $error_code ) {
		$error_message = array(
			'style'   => '',
			'message' => '',
		);

		switch ( $error_code ) {
			/**
			 * Internal Errors
			 */

			// Invalid URL String.
			case 1:
				$error_message = array(
					'style'   => 'notice-warning',
					'message' => esc_html__( 'Invalid URL format. Replaced with site URL. Please update as needed.', 'utm-dot-codes' ),
				);
				break;

			// Invalid Post ID.
			case 2:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Unable to save link. Please try again, your changes were not saved.', 'utm-dot-codes' ),
				);
				break;

			// Shortener Object Error.
			case 1000:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Invalid URL shortener config.', 'utm-dot-codes' ),
				);
				break;

			/**
			 * Bitly
			 */
			case 100:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Unable to connect to Bitly API to shorten url. Please try again later.', 'utm-dot-codes' ),
				);
				break;
			case 4030:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Bitly API responded with unauthorized error. API Key is invalid or rate limit exceeded.', 'utm-dot-codes' ),
				);
				break;
			case 500:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Bitly API experienced an error when shortening the link, please try again later.', 'utm-dot-codes' ),
				);
				break;

			/**
			 * Rebrandly
			 */
			case 401:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Rebrandly API responded with unauthorized error. API Key is invalid or rate limit exceeded.', 'utm-dot-codes' ),
				);
				break;
			case 4031:
				$error_message = array(
					'style'   => 'notice-error',
					'message' => esc_html__( 'Rebrandly API experienced an error when shortening the link, please try again later.', 'utm-dot-codes' ),
				);
				break;
		}

		return apply_filters( 'utmdc_error_message', $error_message, $error_code );
	}

	/**
	 * Retrieve updated list of domain options when requested by the user.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed  $value The new value.
	 * @param mixed  $old_value The old value.
	 * @param string $option The option being updated.
	 *
	 * @return string Empty string to ensure the checkbox remains unchecked.
	 */
	public function pre_rebrandly_domains_update( $value, $old_value, $option ) {
		$update_setting   = ( self::POST_TYPE . '_rebrandly_domains_update' === $option );
		$update_requested = ( 'on' === $value );

		if ( $update_setting && $update_requested ) {
			if ( 'rebrandly' === get_option( self::POST_TYPE . '_shortener' ) ) {
				require_once 'shorten/class-rebrandly.php';

				$rebrandly = new \UtmDotCodes\Rebrandly( get_option( self::POST_TYPE . '_apikey' ) );
				$options   = $rebrandly->get_domains();

				if ( is_array( $options ) && 0 < count( $options ) ) {
					update_option(
						self::POST_TYPE . '_rebrandly_domains',
						wp_json_encode( $options ),
						false
					);
				}
			}
		}

		return '';
	}
}
