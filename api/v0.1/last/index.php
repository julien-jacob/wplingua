<?php

define( 'MACHIAVEL_API', true );
define( 'API_KEY_LENGTH', 16 );


require_once('inc/error.php');
require_once('inc/grade.php');
require_once('inc/main.php');

echo mcvapi_machiavel_api();
