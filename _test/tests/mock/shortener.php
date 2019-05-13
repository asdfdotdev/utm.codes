<?php
/**
 * MockShortener shortener class.
 *
 * @package UtmDotCodes
 */
class MockShortener implements \UtmDotCodes\Shorten {

	private $api_key;
	private $response;
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
		$this->response = 'https://short.ly';
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
