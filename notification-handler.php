<?php

	require_once( 'config.php' );
	require_once( 'config_check.php' );

	// Purchase notifications use PUT requests to send data
	$purchase_data = file_get_contents( 'php://input' );

	// Check for any received data
	if ( empty( $purchase_data ) ) {
		echo 'No data received.';
		exit;
	}

	// Purchase Notifications are JSON encoded.
	$purchase_data = json_decode( $purchase_data, true );

	// Check for expected data
	if ( empty( $purchase_data['buyer_data']['email'] ) ) {
		echo 'Expected purchase data not present.';
		exit;
	}

	// Load WordPress core
	require_once( MMAC_WP_CORE_PATH . 'wp-load.php' );

	// Check for Client class. Plugin may not be activated.
	if ( ! class_exists( 'Mojo_Marketplace_Api_Client' ) ) {
		echo 'Mojo_Marketplace_Api_Client class does not exist.';
		exit;
	}

	// Start up the Client and pass in the Purchase Notification data for processing
	$mmac = mojo_marketplace_api_client_instance();
	$mmac->init( MOJO_MARKETPLACE_API_USERNAME, MOJO_MARKETPLACE_API_KEY );

	// Verify purchase notification data
	//$mmac->verify_notification_data( $purchase_data );

	// Create user account for order
	$password = wp_generate_password();
	$wp_user_id = $mmac->create_user( 
		$purchase_data['buyer_data']['email'],
		array(
			'password' => $password,
			'first_name' => $purchase_data['buyer_data']['first_name'],
			'last_name' => $purchase_data['buyer_data']['last_name']
		)
	);

	// TODO: Email user their account information
	// wp_mail( $purchase_data['buyer_data']['email'], MMAC_EMAIL_NOTIFICATION_SUBJECT, 'Your temporary password: ' . $password );


/***** Everything below this line is related to Easy Digital Downloads ******/

	// Create Easy Digital Downloads Purchase
	require_once( MMAC_EDD_PATH . 'easy-digital-downloads/includes/payments/functions.php' );

	if ( empty( $purchase_data['buyer_data']['products'] ) ) {
		echo 'Purchase notification contains no prodcuts.';
		exit;
	}

	$cart_details = array();
	$downloads = array();
	$order_total = 0;

	foreach ( $purchase_data['buyer_data']['products'] as $mm_product_id ) {
		if ( !isset( $mmac_product_id_map[$mm_product_id] ) ) {
			continue;
		}

		$edd_post_id = $mmac_product_id_map[$mm_product_id];

		$edd_product = get_post( $edd_post_id );
		if ( empty( $edd_product->post_title ) ) {
			continue;
		}

		$edd_product_price = get_post_meta( $edd_post_id, 'edd_price', true );
		if ( $edd_product_price == '' ) {
			$edd_product_price = 0;
		}
		
		$order_total += $edd_product_price;

		$cart_details[] = array(
			'name' => $edd_product->post_title,
			'id' => $edd_post_id,
			'item_number' => array(
				'id' => $edd_post_id,
				'options' => array(),
				'quantity' => 1
			),
			'item_price' => $edd_product_price,
			'quantity' => 1,
			'discount' => 0,
			'subtotal' => $edd_product_price,
			'tax' => 0,
			'price' => $edd_product_price
		);

		$downloads[] = array(
			'id' => $edd_post_id,
			'options' => array(),
			'quantity' => 1
		);

	}

	$payment_data = array(
		'status' => 'publish',
		'parent' => null,
		'date' => date( 'Y-m-d H:i:s' ),
		'cart_details' => $cart_details,
		'downloads' => $downloads,
		'currency' => 'USD',
		'gateway' => 'Mojo Marketplace',
		'price' => $order_total,
		'purchase_key' => $purchase_data['buyer_data']['order_id'],
		'user_email' => $purchase_data['buyer_data']['email'],
		'user_info' => array(
			'id' => $wp_user_id,
			'email' => $purchase_data['buyer_data']['email'],
			'first_name' => $purchase_data['buyer_data']['first_name'],
			'last_name' => $purchase_data['buyer_data']['last_name'],
			'discount' => 'none'
		)
	);
	
	edd_insert_payment($payment_data);