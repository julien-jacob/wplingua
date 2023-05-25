<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function mcv_get_url_current() {
	$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}


function mcv_get_url_current_for_language( $language_id ) {

}


function mcv_get_url_for_language( $url, $language_id ) {

}
