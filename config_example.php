<?php

// Mojo Marketplace API credentials
define( 'MOJO_MARKETPLACE_API_USERNAME', 'xxxxx' );
define( 'MOJO_MARKETPLACE_API_KEY', 'xxxxx' );

// Relative or absolute path to wp-load.php
define( 'MMAC_WP_CORE_PATH', '/path/to/wordpress/' );

// Relative or absolute path to easy-digital-downloads plugin directory
define( 'MMAC_EDD_PATH', '/path/to/wp-content/plugins/easy-digital-downloads/' );

// Email Notification Subject and Content

// Use this setting to route all email notifications to the specified email address
// Comment this setting out to send email notificaitons to real users
//define( 'MMAC_EMAIL_NOTIFICATION_TEST_EMAIL', 'example@example.com' );

define( 'MMAC_EMAIL_NOTIFICATION_SUBJECT_NEW_USER', 'xxxxxx' );
define( 'MMAC_EMAIL_NOTIFICATION_CONTENT_NEW_USER', "
	yyyyyy {EMAIL} yyyyyy {PASSWORD} yyyyyy
" );

define( 'MMAC_EMAIL_NOTIFICATION_SUBJECT_EXISTING_USER', 'xxxxxx' );
define( 'MMAC_EMAIL_NOTIFICATION_CONTENT_EXISTING_USER', "
	yyyyyy {EMAIL} yyyyyy
" );

// Mojo Marketplace / Easy Digital Downloads Product Map
// MM_Product_ID => EDD_Post_ID
$mmac_product_id_map = array(
	'xxxxx' => 12345
	'yyyyy' => 67890
);