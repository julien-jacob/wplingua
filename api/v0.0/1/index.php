<?php

define( 'MACHIAVEL_API', true );
define( 'API_KEY_LENGTH', 16 );


require_once 'inc/error.php';
require_once 'inc/grade.php';
require_once 'inc/main.php';

require_once 'inc/translate.php'; // TODO : Move !

echo mcvapi_machiavel_api();

// $translation = mcvapi_translate( 'en', 'fr', 'Still Life and the power of painting – Envince' );
// var_dump($translation);

