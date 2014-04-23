<?php

	// Purchase notifications use PUT requests to send data
	$purchase_data = file_get_contents('php://input');

	// Check for any received data
	if ( empty( $purchase_data ) ) {
		exit;
	}

	// Purchase Notifications are JSON encoded.
	$purchase_data = json_decode( $purchase_data, true );

	// Check for expected data
	if ( empty( $purchase_data['buyer_data']['email'] ) ) {
		exit;
	}

	// Load WordPress core
	// TODO: Make this configurable
	require_once('../../../wp-load.php');

	// Check for expected configuration constants
	if ( !defined( 'MOJO_MARKETPLACE_API_USERNAME' ) || !defined( 'MOJO_MARKETPLACE_API_KEY' ) ) {
		exit;
	}

	// Check for Client class. Plugin may not be activated.
	if ( ! class_exists( 'Mojo_Marketplace_Api_Client' ) ) {
		exit;
	}

	// Start up the Client and pass in the Purchase Notification data for processing
	$mmac = mojo_marketplace_api_client_instance();
	$mmac->init( MOJO_MARKETPLACE_API_USERNAME, MOJO_MARKETPLACE_API_KEY );
	$mmac->purchase_notification( $purchase_data );