# Content by Language

This section explains how to display or manipulate content based on the current language using the wpLingua plugin. It provides tools such as shortcodes and hooks to customize the behavior of your multilingual website. You can use these features to ensure that your content is displayed or processed appropriately for each language.

## Shortcode

The `[wplng_only]` shortcode allows you to display specific content based on the current language. Below is an example of how to use it:

```
[wplng_only lang="en"]My content in English[/wplng_only]
[wplng_only lang="fr"]Mon contenu en français[/wplng_only]
```

### Parameters

- `lang`: Specifies the language(s) for which the content should be displayed. You can use:
  - A single language code (e.g., `en`, `fr`).
  - Multiple language codes separated by commas (e.g., `en,fr`).
  - Special values:
    - `translated`: Displays content for all translated languages.
    - `original`: Displays content for the original language of the website.

### Example

```
[wplng_only lang="translated"]This content is shown in all the translated languages.[/wplng_only]
[wplng_only lang="original"]This content is shown only in the original language.[/wplng_only]
```

## Hook on Untranslated HTML

The `wplng_intercepted_html` filter allows you to modify the HTML content before it is processed by wpLingua. This is useful for intercepting and altering raw content before any translation occurs.

### Example

```php
<?php

add_filter( 'wplng_intercepted_html', function( $content ) {

    // Example: Add a custom message to the content for debugging purposes
    $content .= '<!-- Debug: Content intercepted before translation -->';

    return $content;
} );
```

This hook is particularly useful for debugging or for making changes to the content structure before it is translated.

## Hook on Translated HTML

The `wplng_translated_html` filter allows you to modify the HTML content after it has been translated. This is useful for replacing specific strings or making adjustments based on the current language.

### Example

```php
<?php

add_filter( 'wplng_translated_html', function( $content ) {

    $language_current_id = wplng_get_language_current_id();

    $strings_original = array(
        'hello',
        'world',
    );

    switch ($language_current_id) {
        case 'fr':
            $content = str_replace(
                $strings_original,
                array(
                    'Bonjour',
                    'Monde',
                ),
                $content
            );
            break;

        case 'es':
            $content = str_replace(
                $strings_original,
                array(
                    'Hola',
                    'Mundo',
                ),
                $content
            );
            break;
    }

    return $content;
} );
```

---

By using these tools, you can fully customize the behavior of your multilingual website and ensure that your content is displayed and processed correctly for each language.
```