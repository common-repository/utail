<?php

require_once dirname(__FILE__) . '/utail-utils.php';

class UTail {
	private $client_id;
	private $secret;

	function __construct($client_id, $secret) {
		$this->client_id = $client_id;
		$this->secret = $secret;
	}
	
	/* API */

	function get_discount($user_id) {
		return $this->call('get_discount', array( $user_id ));
	}

	function apply_discount($user_id, $price) {
		$ret = $this->call('apply_discount', array( $user_id, $price ));
		return $ret->price;
	}
	
	function track_purchase($user_id, $amount, $close_session = false) {
		$ret = $this->call('track_purchase', array( $user_id, $amount, $close_session ));
		return $ret->success;
	}
	
	/* Utility */

	static function host() {
		return defined('UTAIL_DEV') && UTAIL_DEV ? 'utail.dev' : 'utail.com';
	}
	
	/* Private routine */

	private static function base_url() {
		return 'https://'.self::host().'/server/index.php/api/';
	}

	private function call($method, $params) {
		$query = '/';
		if ( $params && count($params) ) {
			foreach ( $params as $param ) {
				$query .= urlencode($param) . '/';
			}
		}
		// Make a HTTP GET request
		$url = self::base_url() . $method . $query . '?' . http_build_query(array(
			'client' => $this->client_id,
			'secret' => $this->secret
		));
		utail_log_debug('making request to API: ' . $url);
		$json = file_get_contents($url);
		// Convert result
		$result = json_decode($json);
		if ( ! $result ) {
			throw new Exception('[UTail] Invalid JSON');
		} elseif ( $result->error ) {
			throw new Exception("[UTail] {$result->message}");
		}
		// Success
		return $result;
	}

}
