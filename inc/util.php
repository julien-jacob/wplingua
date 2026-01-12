<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Check if substring is contained in string
 *
 * @param string $haystack String to check
 * @param string $needle Sub-string
 *
 * @return bool
 */
function wplng_str_contains( $haystack, $needle ) {
	return ( strpos( $haystack, $needle ) !== false );
}


/**
 * Check if string starts by sub_string
 *
 * @param string $haystack String to check
 * @param string $needle Sub-string
 *
 * @return bool
 */
function wplng_str_starts_with( $haystack, $needle ) {

	if ( ! is_string( $haystack ) || ! is_string( $needle ) ) {
		return false;
	}

	return substr_compare( $haystack, $needle, 0, strlen( $needle ) ) === 0;
}


/**
 * Check if string ends by sub_string
 *
 * @param string $haystack String to check
 * @param string $needle Sub-string
 *
 * @return bool
 */
function wplng_str_ends_with( $haystack, $needle ) {

	if ( ! is_string( $haystack ) || ! is_string( $needle ) ) {
		return false;
	}

	return substr_compare( $haystack, $needle, -strlen( $needle ) ) === 0;
}

/**
 * Return true is $str is an URL
 *
 * @param string $str
 * @return bool
 */
function wplng_str_is_url( $str ) {

	$parsed = wp_parse_url( $str );
	$is_url = false;

	if ( is_string( $str )
		&& ( '' !== trim( $str ) )
		&& wplng_str_contains( $str, '/' )
		&& ! wplng_str_starts_with( $str, 'wpgb-content-block/' ) // Plugin: WP Grid Builder
		&& ! wplng_str_starts_with( $str, '/wc/store/v1' ) // Plugin: WooCommerce
		&& ! wplng_str_starts_with( $str, 'GlotPress/' ) // Plugin: WooCommerce
	) {
		if ( isset( $parsed['scheme'] )
			&& (
				( 'https' === $parsed['scheme'] )
				|| ( 'http' === $parsed['scheme'] )
			)
		) {
			// URL has http/https/...
			$is_url = ! ( filter_var( $str, FILTER_VALIDATE_URL ) === false );
		} else {
			// PHP filter_var does not support relative urls, so we simulate a full URL
			$is_url = ( filter_var( 'https://website.com/' . ltrim( $str, '/' ), FILTER_VALIDATE_URL ) !== false );
		}
	}

	return $is_url;
}


/**
 * Return true is $str is a translatable text
 * Return false if $str is a number, mail addredd, symbol, ...
 *
 * @param string $text
 * @return bool
 */
function wplng_text_is_translatable( $text ) {

	$text = trim( $text );

	if ( '' === $text ) {
		return false;
	}

	// Check special no translate tag
	if ( wplng_str_contains( $text, '_wplingua_no_translate_' ) ) {
		return false;
	}

	if ( wplng_str_is_malicious( $text ) ) {
		return false;
	}

	// Check for better plugin compatibility
	if ( wplng_str_contains( $text, 'presto_player' )
		|| wplng_str_contains( $text, 'presto-player' )
	) {
		return false;
	}

	// Check if it's a email address
	if ( filter_var( $text, FILTER_VALIDATE_EMAIL ) ) {
		return false;
	}

	// Check bad HTML tags and templating tags
	if ( wplng_str_starts_with( $text, '<' )
		&& wplng_str_ends_with( $text, '>' )
	) {
		return false;
	}

	// Check JS tags
	if ( wplng_str_starts_with( $text, '{{' )
		&& wplng_str_ends_with( $text, '}}' )
	) {
		return false;
	}

	// Get letters only
	$letters = $text;
	$letters = html_entity_decode( $letters );
	$letters = preg_replace( '#[^\p{L}\p{N}]#u', '', $letters );
	$letters = preg_replace( '#[\d\s]#u', '', $letters );

	return ! empty( $letters );
}


/**
 * Check if a string contains malicious patterns (SQL injection, XSS, etc.)
 *
 * This function detects common attack patterns used in:
 * - SQL injection (UNION, SELECT, SLEEP, BENCHMARK, etc.)
 * - XSS attacks (script tags, javascript: protocol, event handlers)
 * - Database enumeration (INFORMATION_SCHEMA, system variables)
 * - File operations (LOAD_FILE, INTO OUTFILE)
 *
 * @param string $text The string to check for malicious patterns.
 * @return bool True if the string contains malicious patterns, false otherwise.
 */
function wplng_str_is_malicious( $text ) {

	// Check for SQL injection attempts
	$sql_patterns = array(
		'/[\'\"=\)]\s*--\s*$/i',                           // Matches: ' --, " --, = --, ) --
		'/\bOR\b\s*\d+\s*=\s*\(\s*SELECT\b/i',             // OR-based injection: OR 123=(SELECT ...)
		'/\bAND\b\s*\d+\s*=\s*\(\s*SELECT\b/i',            // AND-based injection: AND 123=(SELECT ...)
		'/\bUNION\b\s+(ALL\s+)?SELECT\b/i',                // UNION-based injection: UNION SELECT or UNION ALL SELECT
		'/PG_SLEEP\s*\(/i',                                // PostgreSQL time-based injection: PG_SLEEP()
		'/\bSLEEP\s*\(\s*\d/i',                            // MySQL time-based injection: SLEEP(15)
		'/BENCHMARK\s*\(/i',                               // MySQL time-based injection: BENCHMARK()
		'/WAITFOR\s+DELAY\s+\'/i',                         // SQL Server time-based injection: WAITFOR DELAY '...'
		'/;\s*DROP\s+TABLE\b/i',                           // Destructive query: ;DROP TABLE
		'/;\s*DELETE\s+FROM\b/i',                          // Destructive query: ;DELETE FROM
		'/;\s*INSERT\s+INTO\b/i',                          // Stacked query: ;INSERT INTO
		'/;\s*UPDATE\s+\w+\s+SET\b/i',                     // Stacked query: ;UPDATE ... SET
		'/\'\s*OR\s*\'[\d\w]+\'\s*=\s*\'[\d\w]+/i',        // Classic bypass: ' OR '1'='1
		'/\'\s*OR\s+\d+\s*=\s*\d+/i',                      // Classic bypass: ' OR 1=1
		'/\)\s*OR\s+\d+\s*=\s*\(\s*SELECT\b/i',            // Subquery injection: ) OR 123=(SELECT ...)
		'/^-?\d+\s+OR\s+[\d+\-*\/]+\s*=\s*[\d+\-*\/]+/i',  // Boolean injection: -1 OR 2+866-866-1=0+0+0+1
		'/\bOR\b\s+[\d+\-*\/]+\s*=\s*[\d+\-*\/]+\s*--/i',  // Boolean injection with comment: OR 1+1=2 --
		'/\bAND\b\s+[\d+\-*\/]+\s*=\s*[\d+\-*\/]+/i',      // Boolean injection: AND 1+1=2
		'/LOAD_FILE\s*\(/i',                               // MySQL file read: LOAD_FILE()
		'/INTO\s+(OUT|DUMP)FILE/i',                        // MySQL file write: INTO OUTFILE / INTO DUMPFILE
		'/\bEXEC\s*\(/i',                                  // SQL Server command execution: EXEC()
		'/\bXP_\w+\s*\(/i',                                // SQL Server extended stored procedures: XP_CMDSHELL(), etc.
		'/@@[a-zA-Z_]\w*/i',                               // MySQL system variables extraction
		'/CHAR\s*\(\s*\d+\s*(,\s*\d+\s*){2,}\)/i',         // String obfuscation: CHAR(65,66,67) with 3+ args
		'/0x[0-9a-f]{16,}/i',                              // Hex-encoded payload: long hexadecimal string (16+ chars)
		'/CONCAT\s*\([^)]*SELECT/i',                       // Obfuscated injection: CONCAT() containing SELECT
		'/ORDER\s+BY\s+\d+\s*--/i',                        // Column enumeration: ORDER BY 1--
		'/INFORMATION_SCHEMA\./i',                         // Database schema enumeration: INFORMATION_SCHEMA.tables, etc.
		'/EXTRACTVALUE\s*\(/i',                            // MySQL XML-based injection: EXTRACTVALUE()
		'/UPDATEXML\s*\(/i',                               // MySQL XML-based injection: UPDATEXML()
	);

	foreach ( $sql_patterns as $pattern ) {
		if ( preg_match( $pattern, $text ) ) {
			return true;
		}
	}

	// Check for XSS attempts
	$xss_patterns = array(
		'/<script\b[^>]*>/i',                                 // Script tag injection: <script> or <script src="...">
		'/javascript\s*:/i',                                  // JavaScript protocol handler: javascript:alert()
		'/\bon(error|load|click|mouseover|focus|blur)\s*=/i', // Event handler injection: onerror=, onclick=, etc.
		'/data\s*:\s*text\/html/i',                           // Data URI XSS: data:text/html,...
		'/vbscript\s*:/i',                                    // VBScript protocol handler: vbscript:msgbox()
	);

	foreach ( $xss_patterns as $pattern ) {
		if ( preg_match( $pattern, $text ) ) {
			return true;
		}
	}

	return false;
}


/**
 * Escape texte (used for comparison)
 *
 * @param string $text String to escape
 * @return string Escape texte for comparison
 */
function wplng_text_esc( $text ) {

	$text = html_entity_decode( $text );
	$text = esc_html( $text );
	$text = esc_attr( $text );

	$text = wp_specialchars_decode(
		$text,
		ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401
	);

	$text = str_replace( '\\', '', $text );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = trim( $text );

	return $text;
}


/**
 * Escape texte (used for editor)
 *
 * @param string $text String to escape
 * @return string Escape texte for editor
 */
function wplng_text_esc_displayed( $text ) {

	$search  = array( '<', '&lt;', '>', '&gt;' );
	$replace = array( '[', '[', ']', ']' );

	$text = str_replace(
		$search,
		$replace,
		$text
	);

	return $text;
}


/**
 * Return true if $str is HTML
 *
 * @param string $str String to check
 * @return bool true if $str is HTML
 */
function wplng_str_is_html( $str ) {
	return wplng_str_contains( $str, '<' )
		&& wplng_str_contains( $str, '>' )
		&& ( $str !== wp_strip_all_tags( $str ) );
}


/**
 * Checks whether a string is a valid XML.
 *
 * @param string $str The string to validate.
 * @return bool Returns true if the string is valid XML, false otherwise.
 */
function wplng_str_is_xml( $str ) {
	// Return false if the input is empty or not a string.
	if ( empty( $str ) || ! is_string( $str ) ) {
		return false;
	}

	// Suppress XML parsing errors to avoid warnings/notices.
	libxml_use_internal_errors( true );

	// Try to load the string as XML.
	$xml = simplexml_load_string( $str );

	// Determine if parsing was successful.
	$is_valid_xml = ( $xml !== false );

	// Clear any accumulated libxml errors.
	libxml_clear_errors();
	libxml_use_internal_errors( false );

	return $is_valid_xml;
}


/**
 * Return true if $str is a local ID
 * Ex: fr_FR, fr, FR, ...
 *
 * @param string $str String to check
 * @return bool true if $str is a local ID
 */
function wplng_str_is_locale_id( $str ) {

	$locale  = get_locale();
	$locales = array(
		$locale,                                        // Ex: fr_FR
		strtolower( $locale ),                          // Ex: fr_fr
		str_replace( '_', '-', $locale ),               // Ex: fr-FR
		strtolower( str_replace( '_', '-', $locale ) ), // Ex: fr-fr
		substr( $locale, 0, 2 ),                        // Ex: FR
		strtolower( substr( $locale, 0, 2 ) ),          // Ex: fr
	);

	return in_array( $str, $locales );
}


/**
 * Return true if $str contains sub-strings present in the i18n script
 *
 * @param string $str String to check
 * @return bool String is a i18n script
 */
function wplng_str_is_script_i18n( $str ) {

	$str = trim( $str );

	return wplng_str_contains( $str, 'wp.i18n.setLocaleData' )
		&& wplng_str_contains( $str, 'translations.locale_data.messages' )
		// Check if $str ends with ");"
		&& wplng_str_ends_with( $str, ');' )
		// Check if $str starts with "( function( domain, translations ) {"
		&& ( preg_match( '#^\(\s*function\s*\(\s*domain\s*,\s*translations\s*\)\s*\{#', $str ) === 1 );
}


/**
 * Return true is $str is a JSON
 *
 * @param string $str String to check
 * @return bool true is $str is a JSON
 */
function wplng_str_is_json( $str ) {
	$decoded = json_decode( $str, true );
	return ( json_last_error() === JSON_ERROR_NONE ) && is_array( $decoded );
}


/**
 * Checks if a JSON element should be excluded based on defined exclusion rules.
 *
 * @param mixed $element The JSON element to check.
 * @param array $parents The parent elements of the JSON element.
 *
 * @return bool True if the element matches any exclusion rule, false otherwise.
 */
function wplng_json_element_is_excluded( $element, $parents ) {

	$rules = wplng_data_json_rules_exclusion();

	foreach ( $rules as $rule ) {
		if ( $rule( $element, $parents ) === true ) {
			return true;
		}
	}

	return false;
}


/**
 * Checks if a JSON element should be included based on defined inclusion rules.
 *
 * @param mixed $element The JSON element to check.
 * @param array $parents The parent elements of the JSON element.
 *
 * @return bool True if the element matches any inclusion rule, false otherwise.
 */
function wplng_json_element_is_included( $element, $parents ) {

	$rules = wplng_data_json_rules_inclusion();

	foreach ( $rules as $rule ) {
		if ( $rule( $element, $parents ) === true ) {
			return true;
		}
	}

	return false;
}


/**
 * Get the context
 *
 * @return string Context
 */
function wplng_get_context() {

	$context = 'UNKNOW';

	if ( defined( 'DOING_AJAX' )
		&& DOING_AJAX
		&& ! empty( $_SERVER['HTTP_REFERER'] )
	) {
		$context = $_SERVER['HTTP_REFERER'];
		$context = sanitize_url( $context );
	} elseif ( isset( $_SERVER['HTTPS'] )
		&& isset( $_SERVER['HTTP_HOST'] )
		&& isset( $_SERVER['REQUEST_URI'] )
	) {
		$context  = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' );
		$context .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$context  = sanitize_url( $context );
	}

	return apply_filters(
		'wplng_api_call_translate_context',
		$context
	);
}


/**
 * Return true is website is in sub folder
 *
 * @return bool
 */
function wplng_website_in_sub_folder() {
	$parsed = wp_parse_url( get_home_url() );
	return ! empty( $parsed['path'] );
}


/**
 * Counts the number of published posts for all public post types.
 *
 * This function retrieves all registered public post types, including custom post types,
 * and counts the total number of published posts for those post types.
 * It explicitly includes 'post', 'page', and 'product' post types to ensure they are counted.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return int The total count of published posts for the specified post types.
 */
function wplng_count_public_content() {
	global $wpdb;

	// Retrieve all public post types
	$post_types = get_post_types( array( 'public' => true ), 'names' );

	// Explicitly add specific post types to ensure they are included
	$post_types = array_merge( $post_types, array( 'post', 'page', 'product' ) );

	// Remove duplicate post types
	$post_types = array_unique( $post_types );

	// Check if any post types were found
	if ( empty( $post_types ) ) {
		return 0; // Return 0 if no post types are found
	}

	// Prepare placeholders for the SQL query
	$placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );

	// Build the SQL query to count published posts for the specified post types
	$sql  = 'SELECT COUNT(*)' . PHP_EOL;
	$sql .= "FROM {$wpdb->posts}" . PHP_EOL;
	$sql .= "WHERE post_status = 'publish'" . PHP_EOL;
	$sql .= "AND post_type IN ($placeholders)" . PHP_EOL;

	// Secure the SQL query using $wpdb->prepare()
	$query = $wpdb->prepare( $sql, $post_types );

	// Execute the query and retrieve the result
	$nombre_de_posts = $wpdb->get_var( $query );

	// Return the total count as an integer
	return intval( $nombre_de_posts );
}
