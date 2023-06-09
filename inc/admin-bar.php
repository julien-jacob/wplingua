<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




function wplng_admin_bar_menu() {

	if ( ! wplng_url_current_is_translatable()) {
		return;
	}

    global $wp_admin_bar;

    $wp_admin_bar->add_menu( array(
        'id' => 'wplingua-menu',
        'title' => __('wpLingua', 'wplingua'),
        'href' => false
    ) );
 
	
	if ( ! isset( $_GET['wplingua-visual-editor'] )) {
		$url = add_query_arg('wplingua-visual-editor', '1', wplng_get_url_current());
 
		// Sub menu to open facebook (external link) in new window
		$wp_admin_bar->add_menu( array(
			'id' => 'wplangua-visual-editor',
			'parent' => 'wplingua-menu',
			'title' => __('Visual editor', 'wplingua'),
			'href' => esc_url($url),
			// 'meta' => array( 'target'=>'_blank' )
		) );
	} else {
		$url = remove_query_arg('wplingua-visual-editor', wplng_get_url_current());
 
		// Sub menu to open facebook (external link) in new window
		$wp_admin_bar->add_menu( array(
			'id' => 'wplangua-visual-editor-disable',
			'parent' => 'wplingua-menu',
			'title' => __('Disable visual editor', 'wplingua'),
			'href' => esc_url($url),
			// 'meta' => array( 'target'=>'_blank' )
		) );
	}
	
	
}