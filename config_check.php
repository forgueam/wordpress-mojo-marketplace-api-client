<?php

	// Check for expected configuration constants
	if ( !defined( 'MOJO_MARKETPLACE_API_USERNAME' ) || !defined( 'MOJO_MARKETPLACE_API_KEY' ) ) {
		echo 'Mojo Marketplace API credentials not defined.';
		exit;
	}

	if ( !defined( 'MMAC_WP_CORE_PATH' ) ) {
		echo 'MMAC_WP_CORE_PATH not defined.';
		exit;
	}

	if ( !defined( 'MMAC_EDD_PATH' ) ) {
		echo 'MMAC_EDD_PATH not defined.';
		exit;
	}