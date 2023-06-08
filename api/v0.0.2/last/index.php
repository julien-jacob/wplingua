<?php

define( 'WPLINGUA_API', true );
define( 'API_KEY_LENGTH', 16 );


require_once 'inc/error.php';
require_once 'inc/grade.php';
require_once 'inc/main.php';

require_once 'inc/translate.php'; // TODO : Move !

echo wplngapi_wplingua_api();

// $translation = wplngapi_translate( 'en', 'fr', 'Still Life and the power of painting – Envince' );
// var_dump($translation);

