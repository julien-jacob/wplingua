<?php

define( 'MACHIAVEL_API', true );
define( 'API_KEY_LENGTH', 16 );


require_once 'inc/error.php';
require_once 'inc/grade.php';
require_once 'inc/main.php';

require_once 'inc/translate.php'; // TODO : Move ! 

echo mcvapi_machiavel_api();


// // die('ok');
// $url = http_build_query(
//     array(
//         'client' => 'gtx',
//         'sl'     => 'fr',
//         'tl'     => 'es',
//         'dt'     => 't',
//         'q'      => urlencode( 'Salut le monde !' ),
//     )
// );

// $url = 'https://translate.googleapis.com/translate_a/single?' . $url;

// $url = urlencode($url);
// $x = wp_remote_get( $url );


//  $handle = curl_init($url);
//  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//  $x = curl_exec($handle);

//  echo '<pre>';
//  var_dump(file_get_contents($url));
//  echo '</pre>';
//  die;