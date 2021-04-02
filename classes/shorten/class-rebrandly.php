<?php
/**
 * Rebrandly API shortener class.
 *
 * @package UtmDotCodes
 */

namespace UtmDotCodes;

/**
 * Class Rebrandly.
 */
class Rebrandly implements \UtmDotCodes\Shorten {

	const API_URL = 'https://api.rebrandly.com/v1';

	/**
	 * API credentials for Rebrandly API.
	 *
	 * @var string|null The API key for the shortener.
	 */
	private $api_key;

	/**
	 * Response from API.
	 *
	 * @var object|null The response object from the shortener.
	 */
	private $response;

	/**
	 * Error message.
	 *
	 * @var object|null Error object with code and message properties.
	 */
	private $error_code;

	/**
	 * Shorten domain id to use for shortened link.
	 *
	 * @var string|null Rebrandly domain ID for the shortened link.
	 */
	private $short_domain_id;

	/**
	 * Rebrandly constructor.
	 *
	 * @param string $api_key Credentials for API.
	 * @param null   $short_domain_id ID of the custom domain to use when shortening.
	 */
	public function __construct( $api_key, $short_domain_id = null ) {
		$this->api_key         = $api_key;
		$this->short_domain_id = $short_domain_id;
	}

	/**
	 * See interface for docblock.
	 *
	 * @inheritDoc
	 *
	 * @param array  $data See interface.
	 * @param string $query_string See interface.
	 *
	 * @return void
	 */
	public function shorten( $data, $query_string ) {
		if ( isset( $data['meta_input'] ) ) {
			$data = $data['meta_input'];
		}

		if ( '' !== $this->api_key ) {
			$domain_args = array();

			if ( ! empty( $this->short_domain_id ) ) {
				$domain_args = array(
					'id'  => esc_html( $this->short_domain_id ),
					'ref' => printf( '/domains/%s', esc_html( $this->short_domain_id ) ),
				);
			}

			$response = wp_remote_post(
				self::API_URL . '/links',
				// Selective overrides of WP_Http() defaults.
				array(
					'method'      => 'POST',
					'timeout'     => 15,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'apikey'       => $this->api_key,
						'Content-Type' => 'application/json',
					),
					'body'        => wp_json_encode(
						array(
							'destination' => $data['utmdclink_url'] . $query_string,
							'domain'      => $domain_args,
						)
					),
				)
			);

			if ( isset( $response->errors ) ) {
				$this->error_code = 101;
			} else {
				$body = json_decode( $response['body'] );

				if ( 200 === $response['response']['code'] ) {
					$response_url = '';

					if ( isset( $body->shortUrl ) ) {
						$response_url = $body->shortUrl;

						if ( ! preg_match( '/^https?:\/\//', $response_url ) ) {
							$response_url = 'https://' . $response_url;
						}
					}

					if ( filter_var( $response_url, FILTER_VALIDATE_URL ) ) {
						$this->response = esc_url( wp_unslash( $response_url ) );
					}
				} elseif ( 401 === $response['response']['code'] ) {
					$this->error_code = 401;
				} elseif ( 403 === $response['response']['code'] ) {
					$this->error_code = 4031;
				}
			}
		}
	}

	/**
	 * Get available custom domain options.
	 *
	 * @return array array of domain options
	 */
	public function get_domains() {
		$domains = array();

		if ( '' !== $this->api_key ) {

			$response = wp_remote_get(
				self::API_URL . '/domains?' . http_build_query(
					array(
						'orderBy'  => 'fullName',
						'orderDir' => 'desc',
						'limit'    => 50,
						'active'   => 'true',
						'type'     => 'user',
					)
				),
				// Selective overrides of WP_Http() defaults.
				array(
					'timeout'     => 15,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'apikey'       => $this->api_key,
						'Content-Type' => 'application/json',
					),
				)
			);

			if ( ! isset( $response->errors ) ) {
				$domains = array_map(
					function( $domain ) {
						$is_active    = $domain->active;
						$dns_verified = ( 'verified' === $domain->status->dns );

						if ( $is_active && $dns_verified ) {
							return array(
								'id'        => sanitize_text_field( wp_unslash( $domain->id ) ),
								'full_name' => sanitize_text_field( wp_unslash( $domain->fullName ) ),
							);
						}
					},
					json_decode( $response['body'] )
				);
			}
		}

		return $domains;
	}

	/**
	 * Get response from Rebrandly API for the request.
	 *
	 * @inheritDoc
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Get error code/message returned by Rebrandly API for the request.
	 *
	 * @inheritDoc
	 */
	public function get_error() {
		return $this->error_code;
	}
}
