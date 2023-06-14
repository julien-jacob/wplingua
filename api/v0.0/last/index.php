<?php

define( 'WPLINGUA_API', true );
define( 'API_KEY_LENGTH', 16 );


require_once 'inc/error.php';
require_once 'inc/grade.php';
require_once 'inc/main.php';

require_once 'inc/translate.php'; // TODO : Move !



// echo var_export( wplngapi_is_translatable_text( '..,$' ), true );

echo wplngapi_wplingua_api();

// $translation = wplngapi_translate( 'en', 'fr', 'Still Life and the power of painting – Envince' );
// var_dump($translation);


// $ch = curl_init();
// curl_setopt( $ch, CURLOPT_URL, 'http://127.0.0.1:5000/translate' );
// curl_setopt( $ch, CURLOPT_POST, 1 );
// curl_setopt(
//     $ch,
//     CURLOPT_POSTFIELDS,
//     http_build_query(
//         [
//             'q'       => 'hello world',
//             'source'  => 'en',
//             'target'  => 'fr',
//             'format'  => 'html',
//             // 'api_key' => '576e1336-c1d7-4dc9-a8a4-05cd75185263',
//         ]
//     )
// );

// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
// var_dump(curl_exec( $ch ));
// $server_output = json_decode( curl_exec( $ch ), true );
// // print_r($server_output);
// curl_close( $ch );

// var_dump($server_output);

