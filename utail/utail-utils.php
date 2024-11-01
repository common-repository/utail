<?php

if ( ! defined('UTAIL_UTILS') ) {
	
	/* Logging */

	function utail_log_error($message, $var = null) {
		error_log('[UTail] ' . $message);
		if ( function_exists('browser') ) {
			browser()->error($var, $message);
		}
	}

	function utail_log_debug($message, $var = null) {
		error_log('[UTail] ' . $message);
		if ( function_exists('browser') ) {
			browser()->log($var, $message);
		}
	}

	function utail_log_dump($var) {
		error_log('[UTail] ' . var_export($var, true));
		if ( function_exists('browser') ) {
			browser()->log($var);
		}
	}

	/* Environment */

	//define('UTAIL_DEV', isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'utail.dev');
	utail_log_debug('dev environment = ' . ( defined('UTAIL_DEV') ? UTAIL_DEV : '???' ));
	
	define('UTAIL_UTILS', true);

}