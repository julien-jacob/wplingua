<?php
// ob_start();

define( 'WPLINGUA_API', true );
define( 'API_KEY_LENGTH', 32 );

require_once 'inc/api.php';
require_once 'inc/error.php';
require_once 'inc/grade.php';
require_once 'inc/main.php';

require_once 'inc/translate.php'; // TODO : Move !

echo wplngapi_wplingua_api();

// // Check if JSON response is valid
// $response = ob_get_clean();
// json_decode( $response );
// if ( json_last_error() === JSON_ERROR_NONE ) {
// 	wplngapi_error_die( 17 );
// } else {
// 	echo $response;
// }
