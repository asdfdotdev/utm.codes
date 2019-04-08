<?php
namespace UtmDotCodes;

/**
 * Bitly shortener class.
 *
 * @package UtmDotCodes
 */
class Bitly implements \UtmDotCodes\Shorten {

	const API_URL = 'https://api-ssl.bitly.com/v3';

	/**
	 * API credentials for Bitly API.
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
	 * Bitly constructor.
	 *
	 * @param string $api_key Credentials for API.
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * See interface for docblock.
	 *
	 * @inheritDoc
	 *
	 * @param array  $data See interface.
	 * @param string $query_string See interface.
	 *
	 * @return mixed|void
	 */
	public function shorten( $data, $query_string ) {
		if ( isset( $data['meta_input'] ) ) {
			$data = $data['meta_input'];
		}

		if ( '' !== $this->api_key ) {
			$response = wp_remote_get(
				self::API_URL . '/shorten?' . http_build_query(
					[
						'access_token' => $this->api_key,
						'longUrl'      => $data['utmdclink_url'] . $query_string,
					]
				)
			);

			if ( isset( $response->errors ) ) {
				$this->error_code = 100;
			} else {
				$body = json_decode( $response['body'] );

				if ( 200 === $body->status_code ) {
					$response_url = '';

					if ( isset( $body->data->url ) ) {
						$response_url = $body->data->url;
					}

					if ( filter_var( $response_url, FILTER_VALIDATE_URL ) ) {
						$this->response = esc_url( wp_unslash( $body->data->url ) );
					}
				} elseif ( 500 === $body->status_code ) {
					$this->error_code = 500;
				} elseif ( 403 === $body->status_code ) {
					$this->error_code = 500;
				}
			}
		}
	}

	/**
	 * Get response from Bitly API for the request.
	 *
	 * @inheritDoc
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Get error code/message returned by Bitly API for the request.
	 *
	 * @inheritDoc
	 */
	public function get_error() {
		return $this->error_code;
	}
}
