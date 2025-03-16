<?php

// If this file is called directly, abort.

use PHP_CodeSniffer\Standards\MySource\Sniffs\Debug\FirebugConsoleSniff;

if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * HeartBeat: Clear bad translations 
 *
 * @return void
 */
function wplng_ajax_heartbeat() {

	$last_beat = get_option( 'wplng_hb_last_update' );
	$now       = time();
	$counter   = 10;

	if ( ! empty( $last_beat )
		&& ( $last_beat + ( MINUTE_IN_SECONDS * 1 ) ) > $now
	) {
		error_log('HeartBeat disabled');
		wp_send_json_success();
		return;
	}

	error_log('HeartBeat start');

	update_option( 'wplng_hb_last_update', $now );

	$args = array(
		'post_type'              => 'wplng_translation',
		'posts_per_page'         => -1,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'cache_results'          => false,
	);

	$the_query = new WP_Query( $args );

	while ( $the_query->have_posts() ) {

		if ( $counter <= 0 ) {
			break;
		}

		$the_query->the_post();

		$id   = get_the_ID();
		$meta = get_post_meta( $id );

		if ( ! isset( $meta['wplng_translation_original'][0] )
			|| ! is_string( $meta['wplng_translation_original'][0] )
			|| empty( $meta['wplng_translation_translations'][0] )
			|| ! isset($meta['wplng_translation_md5'][0])
		) {
			$counter--;
			wp_delete_post( $id, true );
			error_log( 'wpLingua HeartBeat - Delete post - ID: ' . $id );
		}
	}

	wp_reset_postdata();
	wp_send_json_success();
}
