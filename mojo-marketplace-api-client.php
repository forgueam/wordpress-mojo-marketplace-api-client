<?php
/**
 * Plugin Name: Mojo Marketplace API Client
 * Plugin URI: 
 * Description: Processes Mojo Marketplace Purchase Notifications
 * Author: Aaron Forgue
 * Author URI: 
 * Version: 1.0
 * Text Domain: mmac
 */


if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Mojo_Marketplace_Api_Client' ) ) {

class Mojo_Marketplace_Api_Client
{
	private static $instance;

	private $api_username;
	private $api_key;

	public $mojo_api_url = 'http://www.mojomarketplace.com/api/v1/';

	/**
	 * Main Mojo_Marketplace_Api_Client Instance
	 *
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Mojo_Marketplace_Api_Client ) ) {
			self::$instance = new Mojo_Marketplace_Api_Client;
		}
		return self::$instance;
	}

	/**
	 * Initialization
	 *
	 */
	public function init( $api_username, $api_key ) {
		$this->api_username = $api_username;
		$this->api_key = $api_key;
	}

	/**
	 * Create a WordPress user account
	 */
	public function create_user( $email_address, $user_data = array() ) {

		// Check for duplicate email addresses
		$existing_user = get_user_by( 'email', $email_address );
		if ( !empty( $existing_user->ID ) ) {
			return $existing_user->ID;
		}

		// Check for duplicate usernames
		$existing_user = get_user_by( 'login', $email_address );
		if ( !empty( $existing_user->ID ) ) {
			return $existing_user->ID;
		}

		// No existing users. Initialize base details for a new user account
		$new_wp_user = array(
			'user_email' => $email_address,
			'user_nicename' => $email_address,
			'nickname' => $email_address,
			'user_login' => $email_address
		);

		// Set password
		if ( !empty( $user_data['password'] ) ) {
			$password = $user_data['password'];
		} else {
			$password = wp_generate_password();
		}
		
		// Create the WP user
		$new_wp_user_id = wp_create_user( $new_wp_user['user_login'], $password, $new_wp_user['user_email'] );

		if ( is_wp_error( $new_wp_user_id ) ) {
			return false;
		}

		// If user creation was successful, update some other attributes
		$new_wp_user['ID'] = $new_wp_user_id;
		$new_wp_user['first_name'] = !empty( $user_data['first_name'] ) ? $user_data['first_name'] : null;
		$new_wp_user['last_name'] = !empty( $user_data['last_name'] ) ? $user_data['last_name'] : null;
		$new_wp_user['display_name'] = ( empty( $user_data['first_name'] ) && empty( $user_data['last_name'] ) ) ? $email_address : $user_data['first_name'] . ' ' . $user_data['last_name'];

		wp_update_user( $new_wp_user );

		return $new_wp_user_id;
	}

	/**
	 * Verify Mojo Marketplace purchase notification data
	 */
	public function verify_notification_data( $notification_data ) {

		$response = $this->request( 'public_key', array() );

		if ( !$response ) {
			return false;
		}

		$publicKey = trim( $response );

		$pubkeyid = openssl_get_publickey( $publicKey );

		$buyerData = $inMessage['buyer_data'];

		$digitalSignature = hex2bin( $inMessage['signature'] );

		return openssl_verify( $message, $inSignature, $pubkeyid );

	}

	/**
	 * Send request
	 */
	public function request( $action, $post_fields, $request_type = 'post' ) {

		$ch = curl_init( $this->mojo_api_url . $this->api_username . '/' . $this->$api_key . '/' . $action );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );

		if ( $request_type == 'post' ) {
			curl_setopt( $ch, CURLOPT_POST, true );

			if ( !empty( $request_data ) ) {
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields );
			}
		}

		$response = curl_exec( $ch );

		if ( $response === false ) {
			echo 'Curl error: ' . curl_error( $ch );
			return false;
		}

		return $response;
	}
}

} // Class exists check

/**
 * The function responsible for fetching the Mojo_Marketplace_Api_Client Instance
 *
 */
function mojo_marketplace_api_client_instance() {
	return Mojo_Marketplace_Api_Client::instance();
}