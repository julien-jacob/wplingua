<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get AJAX action to exclude from translation
 *
 * @return array
 */
function wplng_data_excluded_ajax_action() {
	return apply_filters(
		'wplng_excluded_ajax_action',
		array(
			// WordPress
			'heartbeat',

			// Plugin: wpLingua
			'wplng_ajax_heartbeat',
			'wplng_ajax_translation',
			'wplng_ajax_edit_modal',
			'wplng_ajax_save_modal',
			'wplng_ajax_slug',
		)
	);
}
