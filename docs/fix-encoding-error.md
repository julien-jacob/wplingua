# Fix encoding error

## Solition 1

```php
/**
 * wpLingua - Add a filter to modify intercepted HTML content
 */
add_filter(
	'wplng_intercepted_html',
	function ( $content ) {

		// Replace specific characters (en dash and HTML entity) with a hyphen
		$content = str_replace(
			array( '&#8211;', '–' ),
			'-',
			$content
		);

		// Return the modified content
		return $content;
	}
);
```

## Solition 2

```php
/**
 * pLingua - Add a filter to modify intercepted HTML content
 */
add_filter(
	'wplng_intercepted_html',
	function ( $content ) {

		// Complete list of problematic characters to replace
		$replacements = array(
			// Dashes
			'—'            => '-',   // em dash (U+2014)
			'–'            => '-',   // en dash (U+2013)
			'-'            => '-',   // non-breaking hyphen (U+2011)
			'&#8211;'      => '-',  // HTML entity for en dash
			'&#8212;'      => '-',  // HTML entity for em dash

			// Typographic apostrophes
			'’'            => "'",   // closing apostrophe (U+2019)
			'‘'            => "'",   // opening apostrophe (U+2018)
			'&#8216;'      => "'",  // HTML entity for ‘
			'&#8217;'      => "'",  // HTML entity for ’

			// Typographic quotation marks
			'“'            => '"',   // opening double quotation mark (U+201C)
			'”'            => '"',   // closing double quotation mark (U+201D)
			'«'            => '"',   // French opening quotation mark
			'»'            => '"',   // French closing quotation mark
			'&#8220;'      => '"',  // HTML entity for “
			'&#8221;'      => '"',  // HTML entity for ”
			'&#171;'       => '"',  // «
			'&#187;'       => '"',  // »

			// Invisible spaces
			"\xC2\xA0"     => ' ', // non-breaking space (U+00A0)
			"\xE2\x80\xAF" => ' ', // narrow non-breaking space (U+202F)
			"\xE2\x80\x8B" => '',  // zero-width space (U+200B)
			"\xEF\xBB\xBF" => '',  // invisible UTF-8 BOM (U+FEFF)

			// Typographic ellipsis
			'…'            => '...', // ellipsis (U+2026)
			'&#8230;'      => '...', // HTML entity for …

			// Other “fancy” characters
			'©'            => '(c)', // copyright symbol
			'®'            => '(r)', // registered trademark symbol
			'™'            => '(tm)', // trademark symbol
		);

		// Perform the replacements
		$content = strtr( $content, $replacements );

		// Remove any invisible control characters
		$content = preg_replace( '/[\x00-\x1F\x7F]/u', '', $content );

		return $content;
	}
);
```

## Solition 3

```php
/**
 * pLingua - Add a filter to control whether the wptexturize function should run
 */
add_filter(
	'run_wptexturize',
	function ( $run ) {

		// If the wplng_start function does not exist, return the original value
		if ( ! function_exists( 'wplng_start' ) ) {
			return $run;
		}

		// If the current language ID does not match the website language ID, disable wptexturize
		if ( wplng_get_language_website_id() !== wplng_get_language_current_id() ) {
			return false;
		}

		// Otherwise, return the original value
		return $run;
	}
);
```
