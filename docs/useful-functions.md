# Useful Functions in wpLingua Plugin

This document provides an overview of some key functions available in the wpLingua plugin. These functions are useful for managing multilingual features in your WordPress site.

---

- [wplng_get_language_website_id()](#wplng_get_language_website_id)
- [wplng_get_language_current_id()](#wplng_get_language_current_id)
- [wplng_get_languages_target_ids()](#wplng_get_languages_target_ids)
- [wplng_get_url_original()](#wplng_get_url_original)
- [wplng_get_url_current()](#wplng_get_url_current)
- [wplng_get_url_current_for_language( $language_id )](#wplng_get_url_current_for_language-language_id)
- [wplng_url_is_translatable( $url )](#wplng_url_is_translatable-url)

---

## `wplng_get_language_website_id()`
**Description:**  
Retrieves the language ID of the website's default/original language.

**Usage:**
```php
<?php

$website_language_id = wplng_get_language_website_id();
echo $website_language_id; // Example output: 'en'
```

---

## `wplng_get_language_current_id()`
**Description:**  
Gets the language ID of the current page being viewed.

**Usage:**
```php
<?php

$current_language_id = wplng_get_language_current_id();
echo $current_language_id; // Example output: 'fr'
```

---

## `wplng_get_languages_target_ids()`
**Description:**  
Retrieves an array of target language IDs that the website supports.

**Usage:**
```php
<?php

$target_language_ids = wplng_get_languages_target_ids();
var_dump( $target_language_ids ); // Example output: ['en', 'fr', 'es']
```

---

## `wplng_get_url_original()`
**Description:**  
Gets the untranslated/original URL of the current page.

**Usage:**
```php
<?php

$original_url = wplng_get_url_original();
echo $original_url; // Example output: 'https://example.com/original-page'
```

---

## `wplng_get_url_current()`
**Description:**  
Retrieves the current URL being viewed.

**Usage:**
```php
<?php

$current_url = wplng_get_url_current();
echo $current_url; // Example output: 'https://example.com/fr/page-actuelle'
```

---

## `wplng_get_url_current_for_language( $language_id )`
**Description:**  
Gets the URL of the current page translated into the specified language.

**Parameters:**
- `$language_id` (string): The target language ID (e.g., `'fr'` for French).

**Usage:**
```php
<?php

$translated_url = wplng_get_url_current_for_language( 'fr' );
echo $translated_url; // Example output: 'https://example.com/fr/page-actuelle'
```

---

## `wplng_url_is_translatable( $url )`
**Description:**  
Checks if the given URL is translatable.

**Parameters:**
- `$url` (string): The URL to check.

**Usage:**
```php
<?php

$is_translatable = wplng_url_is_translatable( wplng_get_url_original() );
var_dump( $is_translatable ); // Example output: true
```

**Returns:**  
`true` if the URL is translatable, `false` otherwise.

---

These functions are essential for managing multilingual content and ensuring proper URL handling in a multilingual WordPress site.