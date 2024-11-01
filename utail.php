<?php
/**
 * Plugin Name: UTail
 * Plugin URI: http://utail.com/?portfolio=wordpress
 * Description: UTail shortcodes and widgets.
 * Version: 1.3
 * Author: Marc Lesnick
 * Author URI: https://profiles.wordpress.org/marclesnick/
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

require_once dirname(__FILE__) . '/utail/utail-api.php';
require_once dirname(__FILE__) . '/integrations/utail-woocommerce.php';

function utail_ensure_session() 
{
	if ( ! session_id() ) session_start();
}

/* Init API */

function &utail_instance() 
{
	static $utail;
	
	if ( $utail ) return $utail;

	//utail_ensure_session();

	$client_id = get_option('utail_client_id');
	if ( ! $client_id ) {
		utail_log_error('init failed: client_id is empty');
		throw new Exception('[UTail] init failed: client_id is empty');
	}

	$secret = get_option('utail_secret');
	if ( ! $secret ) {
		utail_log_error('init failed: secret is empty');
		throw new Exception('[UTail] init failed: secret is empty');
	}

	// HTTP headers to allow iFrame communication
	//header('X-Frame-Options: ALLOW-FROM ' . UTail::host());
	//header('Content-Security-Policy: frame-ancestors ' . UTail::host());

	$utail = new UTail($client_id, $secret);
	utail_log_debug('init ok');
	return $utail;
}

function utail_get_user_id() {
	utail_ensure_session();
	if ( isset($_SESSION['utail_user_id']) ) {
		return $_SESSION['utail_user_id'];
	}
}

/* Frontend */

function utail_button($atts) {
	if (!is_array($atts)) {
	$atts = array();
	}
	if ( ! isset($atts['code_id']) ) {
		$atts['code_id'] = get_option('utail_widget_code_id');
	}
	// Extract lang parameters 
	foreach ( $atts as $key => $value ) {
		if ( strpos($key, 'lang_') === 0 ) {
			$parts = explode('_', $key);
			if ( count($parts) === 3 ) {
				$atts['lang'][$parts[1]][$parts[2]] = $value;
			}
			unset($atts[$key]);
		}
	}
	$query = http_build_query($atts);
	ob_start();
	require dirname(__FILE__) . '/utail-button.php';
	return ob_get_clean();
}

add_shortcode('utail-button', 'utail_button');

function utail_ajax_set_user_id() {
	$nonce = $_POST['_wpnonce'];
	if ( wp_verify_nonce( $nonce, 'utail_nonce_YB8arV4S' ) ) {
		$user_id = $_POST['user_id'];
		if ( $user_id ) {
			utail_ensure_session();
			$_SESSION['utail_user_id'] = $user_id;
			utail_log_debug("user id is saved: $user_id");
		}
	} else {
		utail_log_error("couldn't verify nonce: $nonce");
	}
}

add_action('wp_ajax_set_user_id', 'utail_ajax_set_user_id');
add_action('wp_ajax_nopriv_set_user_id', 'utail_ajax_set_user_id');

function utail_wp_head() {
	$social_discount_bg = get_option('utail_social_discount_bg');
	if ( $social_discount_bg ) {
		echo "<style>.utail-discount, .utail-discount > td:last-child { background-color: $social_discount_bg; }</style>";
	}
}

add_action('wp_head', 'utail_wp_head');

/* Backend */

function utail_admin_enqueue_scripts() {
	if ( is_admin() ) {
		// Add the color picker css file
		wp_enqueue_style('wp-color-picker');
		// Include our custom jQuery file with WordPress Color Picker dependency
		wp_enqueue_script('custom-script-handle', plugins_url( 'utail-admin-settings.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}
}

add_action('admin_enqueue_scripts', 'utail_admin_enqueue_scripts');

function utail_settings_menu() {
	add_options_page( 'UTail settings', 'UTail', 'manage_options', 'utail.php', 'utail_settings_page' );
}

function utail_settings_page() {
	require dirname(__FILE__) . '/utail-admin.php';
}

add_action('admin_menu', 'utail_settings_menu');

function utail_register_settings() {
	register_setting( 'utail-settings', 'utail_client_id', 'trim' );
	register_setting( 'utail-settings', 'utail_secret', 'trim' );
	register_setting( 'utail-settings', 'utail_widget_code_id', 'trim' );
	register_setting( 'utail-settings', 'utail_social_discount_bg' );
}

add_action('admin_init', 'utail_register_settings');
